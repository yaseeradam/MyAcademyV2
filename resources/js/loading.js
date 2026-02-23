// Global loading indicator for Livewire 3
// Livewire 3 request hook API: { uri, options, payload, respond, succeed, fail }
document.addEventListener('livewire:init', () => {
    let loadingTimer = null;
    let safetyTimer = null;
    let activeUserRequests = 0;

    /**
     * Detect whether a Livewire request is a background polling request.
     * Polling requests should NOT trigger the global loader.
     */
    function isPollingRequest({ options, payload }) {
        try {
            // Livewire 3 sends an X-Livewire header and the payload contains
            // "components" with "calls". Polling calls use the method name
            // that matches the wire:poll directive (e.g. '$refresh', 'tick',
            // 'refreshCount'). But the simplest reliable signal is the
            // 'X-Livewire-Polling' header that Livewire adds automatically.
            const headers = options?.headers || {};
            if (headers['X-Livewire-Is-Polling'] === true || headers['X-Livewire-Is-Polling'] === 'true') {
                return true;
            }

            // Fallback: inspect the payload for polling fingerprints
            // Livewire 3 sets a "polling" flag on the component memo
            if (payload) {
                const raw = typeof payload === 'string' ? JSON.parse(payload) : payload;
                const components = raw?.components || [];
                // If ALL components in this request are polling, skip loader
                if (components.length > 0 && components.every(c => {
                    const calls = c?.calls || [];
                    // $refresh with no other calls = polling
                    if (calls.length === 1 && calls[0]?.method === '$refresh') return true;
                    // Known polling methods
                    const pollingMethods = ['$refresh', 'tick', 'refreshCount'];
                    return calls.length > 0 && calls.every(call => pollingMethods.includes(call?.method));
                })) {
                    return true;
                }
            }
        } catch (e) {
            // If we can't parse, assume it's NOT polling (safe default)
        }
        return false;
    }

    // Show loading indicator on user-initiated Livewire requests
    Livewire.hook('request', (requestData) => {
        const { succeed, fail } = requestData;

        // Skip the global loader for background polling requests
        if (isPollingRequest(requestData)) {
            // Still wire up succeed/fail to avoid breaking the hook chain
            succeed(() => { });
            fail(() => { });
            return;
        }

        activeUserRequests++;

        // Debounce: only show loader if request takes > 300ms
        // This prevents flashing on quick interactions
        if (!loadingTimer && activeUserRequests === 1) {
            loadingTimer = setTimeout(() => {
                if (activeUserRequests > 0) {
                    showGlobalLoading();

                    // Safety net: auto-hide after 15 seconds to prevent infinite loaders
                    clearTimeout(safetyTimer);
                    safetyTimer = setTimeout(() => {
                        activeUserRequests = 0;
                        clearTimeout(loadingTimer);
                        loadingTimer = null;
                        hideGlobalLoading();
                    }, 15000);
                }
            }, 300);
        }

        succeed(() => {
            activeUserRequests = Math.max(0, activeUserRequests - 1);
            if (activeUserRequests === 0) {
                clearTimeout(loadingTimer);
                loadingTimer = null;
                clearTimeout(safetyTimer);
                safetyTimer = null;
                hideGlobalLoading();
            }
        });

        fail(({ status }) => {
            activeUserRequests = Math.max(0, activeUserRequests - 1);
            if (activeUserRequests === 0) {
                clearTimeout(loadingTimer);
                loadingTimer = null;
                clearTimeout(safetyTimer);
                safetyTimer = null;
                hideGlobalLoading();
            }

            // Handle different error types
            if (status === 419) {
                window.showError?.('Your session has expired. Please refresh the page.');
            } else if (status === 403) {
                window.showError?.('You do not have permission to perform this action.');
            } else if (status === 500) {
                window.showError?.('A server error occurred. Please try again or contact support.');
            }
        });
    });

    // Handle alert events from Livewire
    Livewire.on('alert', (event) => {
        const data = event[0] || event;
        const message = data?.message || 'Action completed';
        const type = data?.type || 'info';

        if (window.showNotification) {
            window.showNotification(message, type);
        }
    });

    // Safety net: hide loader on unhandled JS errors
    window.addEventListener('error', () => {
        activeUserRequests = 0;
        clearTimeout(loadingTimer);
        loadingTimer = null;
        clearTimeout(safetyTimer);
        safetyTimer = null;
        hideGlobalLoading();
    });
});

// Global loading indicator element management
function showGlobalLoading() {
    let loader = document.getElementById('global-loader');
    if (!loader) {
        loader = document.createElement('div');
        loader.id = 'global-loader';
        loader.className = 'fixed top-24 right-8 z-[100] rounded-2xl bg-white/90 backdrop-blur-md shadow-2xl border border-slate-200/50 p-4 transition-all duration-300';
        loader.innerHTML = `
            <div class="flex items-center gap-4">
                <div class="relative h-7 w-7">
                    <div class="absolute inset-0 rounded-full border-4 border-amber-100"></div>
                    <div class="absolute inset-0 rounded-full border-4 border-amber-500 border-t-transparent animate-spin"></div>
                </div>
                <div class="flex flex-col">
                    <span class="text-sm font-bold text-slate-800">Processing</span>
                    <span class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Please wait...</span>
                </div>
            </div>
        `;
        document.body.appendChild(loader);
    }
    loader.style.display = 'block';
    // Trigger reflow before transition
    loader.offsetHeight;
    loader.style.opacity = '1';
    loader.style.transform = 'translateX(0)';
}

function hideGlobalLoading() {
    const loader = document.getElementById('global-loader');
    if (loader) {
        loader.style.opacity = '0';
        loader.style.transform = 'translateX(20px)';
        setTimeout(() => {
            if (loader.style.opacity === '0') {
                loader.style.display = 'none';
            }
        }, 300);
    }
}

// Global error/success helpers that use the enhanced notification system
window.showError = function (message) {
    if (window.showNotification) {
        window.showNotification(message, 'error');
    } else {
        alert(message);
    }
};

window.showSuccess = function (message) {
    if (window.showNotification) {
        window.showNotification(message, 'success');
    } else {
        console.log('Success:', message);
    }
};

// Ensure animation styles are present
if (!document.getElementById('loading-animations')) {
    const style = document.createElement('style');
    style.id = 'loading-animations';
    style.textContent = `
        @keyframes slide-in-right {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        .animate-slide-in-right {
            animation: slide-in-right 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
    `;
    document.head.appendChild(style);
}

