// Global loading indicator
document.addEventListener('livewire:init', () => {
    // Show loading indicator on Livewire requests
    Livewire.hook('request', ({ fail }) => {
        // Show global loading
        showGlobalLoading();
        
        fail(({ status, content }) => {
            hideGlobalLoading();
            
            // Handle different error types
            if (status === 419) {
                showError('Your session has expired. Please refresh the page.');
            } else if (status === 403) {
                showError('You do not have permission to perform this action.');
            } else if (status === 404) {
                showError('The requested resource was not found.');
            } else if (status === 500) {
                showError('A server error occurred. Please try again or contact support.');
            } else if (status === 422) {
                // Validation errors are handled by Livewire
                return;
            } else {
                showError('An unexpected error occurred. Please try again.');
            }
        });
    });
    
    Livewire.hook('commit', () => {
        hideGlobalLoading();
    });
    
    // Handle alert events
    Livewire.on('alert', (event) => {
        const data = event[0] || event;
        const message = data?.message || 'Action completed';
        const type = data?.type || 'info';
        
        showNotification(message, type);
    });
});

// Global loading indicator
function showGlobalLoading() {
    let loader = document.getElementById('global-loader');
    if (!loader) {
        loader = document.createElement('div');
        loader.id = 'global-loader';
        loader.className = 'fixed top-20 right-6 z-50 rounded-xl bg-white shadow-lg border border-slate-200 p-4';
        loader.innerHTML = `
            <div class="flex items-center gap-3">
                <div class="relative h-6 w-6">
                    <div class="absolute inset-0 rounded-full border-4 border-amber-200"></div>
                    <div class="absolute inset-0 rounded-full border-4 border-amber-500 border-t-transparent animate-spin"></div>
                </div>
                <span class="text-sm font-medium text-slate-700">Loading...</span>
            </div>
        `;
        document.body.appendChild(loader);
    }
    loader.style.display = 'block';
}

function hideGlobalLoading() {
    const loader = document.getElementById('global-loader');
    if (loader) {
        loader.style.display = 'none';
    }
}

// Notification system
function showNotification(message, type = 'info') {
    const colors = {
        success: 'bg-green-50 border-green-200 text-green-800',
        error: 'bg-red-50 border-red-200 text-red-800',
        warning: 'bg-yellow-50 border-yellow-200 text-yellow-800',
        info: 'bg-blue-50 border-blue-200 text-blue-800',
    };
    
    const icons = {
        success: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />',
        error: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />',
        warning: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />',
        info: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
    };
    
    const notification = document.createElement('div');
    notification.className = `fixed top-20 right-6 z-50 rounded-xl border-2 ${colors[type] || colors.info} p-4 shadow-lg animate-slide-in-right`;
    notification.innerHTML = `
        <div class="flex items-start gap-3">
            <svg class="h-5 w-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                ${icons[type] || icons.info}
            </svg>
            <p class="text-sm font-semibold leading-relaxed flex-1">${message}</p>
            <button onclick="this.closest('div').remove()" class="flex-shrink-0 rounded-lg p-1 hover:bg-black/5 transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => notification.remove(), 300);
    }, 5000);
}

function showError(message) {
    showNotification(message, 'error');
}

function showSuccess(message) {
    showNotification(message, 'success');
}

// Add animation styles
const style = document.createElement('style');
style.textContent = `
    @keyframes slide-in-right {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    .animate-slide-in-right {
        animation: slide-in-right 0.3s ease-out;
    }
`;
document.head.appendChild(style);

// Export for use in other scripts
window.showNotification = showNotification;
window.showError = showError;
window.showSuccess = showSuccess;
