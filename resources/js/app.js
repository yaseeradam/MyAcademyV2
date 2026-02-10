import './bootstrap';

// Mobile Sidebar
const mobileSidebar = document.getElementById('mobileSidebar');
const mobileSidebarOverlay = document.getElementById('mobileSidebarOverlay');
const openMobileSidebar = document.getElementById('openMobileSidebar');
const closeMobileSidebar = document.getElementById('closeMobileSidebar');

function openSidebar() {
    mobileSidebar.classList.remove('-translate-x-full');
    mobileSidebarOverlay.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeSidebar() {
    mobileSidebar.classList.add('-translate-x-full');
    mobileSidebarOverlay.classList.add('hidden');
    document.body.style.overflow = '';
}

if (openMobileSidebar) {
    openMobileSidebar.addEventListener('click', openSidebar);
}

if (closeMobileSidebar) {
    closeMobileSidebar.addEventListener('click', closeSidebar);
}

if (mobileSidebarOverlay) {
    mobileSidebarOverlay.addEventListener('click', closeSidebar);
}

// Dark Mode
const darkModeToggle = document.getElementById('darkModeToggle');
const html = document.documentElement;

// Check for saved theme preference or default to light
const currentTheme = localStorage.getItem('theme') || 'light';
if (currentTheme === 'dark') {
    html.classList.add('dark');
}

if (darkModeToggle) {
    darkModeToggle.addEventListener('click', () => {
        html.classList.toggle('dark');
        const theme = html.classList.contains('dark') ? 'dark' : 'light';
        localStorage.setItem('theme', theme);
        
        // Add animation class
        darkModeToggle.classList.add('animate-pulse-soft');
        setTimeout(() => {
            darkModeToggle.classList.remove('animate-pulse-soft');
        }, 500);
    });
}

// Add animations to elements when they come into view
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('animate-fade-in');
            observer.unobserve(entry.target);
        }
    });
}, observerOptions);

// Observe all cards and stat cards
document.addEventListener('DOMContentLoaded', () => {
    const cards = document.querySelectorAll('.card, .stat-card, [class*="card"]');
    cards.forEach(card => observer.observe(card));
});

// Enhanced form interactions
document.querySelectorAll('input, select, textarea').forEach(element => {
    element.addEventListener('focus', function() {
        this.parentElement.classList.add('animate-scale-hover');
    });
    
    element.addEventListener('blur', function() {
        this.parentElement.classList.remove('animate-scale-hover');
    });
});

// Button click animations
document.querySelectorAll('button, .btn-primary, .btn-outline, .btn-ghost').forEach(button => {
    button.addEventListener('click', function(e) {
        // Create ripple effect
        const ripple = document.createElement('span');
        const rect = this.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = e.clientX - rect.left - size / 2;
        const y = e.clientY - rect.top - size / 2;
        
        ripple.style.width = ripple.style.height = size + 'px';
        ripple.style.left = x + 'px';
        ripple.style.top = y + 'px';
        ripple.classList.add('ripple');
        
        this.style.position = 'relative';
        this.style.overflow = 'hidden';
        this.appendChild(ripple);
        
        setTimeout(() => {
            ripple.remove();
        }, 600);
    });
});

// Search functionality
const searchInputs = document.querySelectorAll('.input-search');
searchInputs.forEach(input => {
    let searchTimeout;
    
    input.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();
        
        if (query.length > 2) {
            searchTimeout = setTimeout(() => {
                // Add loading state
                this.classList.add('animate-pulse-soft');
                
                // Perform search (placeholder)
                console.log('Searching for:', query);
                
                setTimeout(() => {
                    this.classList.remove('animate-pulse-soft');
                }, 500);
            }, 300);
        }
    });
});

// Notification system
window.showNotification = function(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-20 right-6 z-50 p-4 rounded-lg shadow-lg animate-slide-up ${
        type === 'success' ? 'bg-green-500 text-white' :
        type === 'error' ? 'bg-red-500 text-white' :
        type === 'warning' ? 'bg-orange-500 text-white' :
        'bg-brand-500 text-white'
    }`;
    notification.innerHTML = `
        <div class="flex items-center gap-3">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                ${type === 'success' ? '<path d="M20 6 9 17l-5-5" />' :
                  type === 'error' ? '<path d="M6 18L18 6M6 6l12 12" />' :
                  type === 'warning' ? '<path d="M12 9v2m0 4h.01" />' :
                  '<path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z" />'}
            </svg>
            <span class="text-sm font-medium">${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
};

// Modal alert (center popup)
function ensureAlertModal() {
    let overlay = document.getElementById('alertModalOverlay');
    if (overlay) return overlay;

    overlay = document.createElement('div');
    overlay.id = 'alertModalOverlay';
    overlay.className = 'fixed inset-0 z-[100] hidden items-center justify-center bg-black/50 p-4';
    overlay.innerHTML = `
        <div id="alertModalPanel" class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl ring-1 ring-inset ring-gray-200">
            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <div id="alertModalTitle" class="text-sm font-semibold text-gray-900">Alert</div>
                    <div id="alertModalMessage" class="mt-2 text-sm text-gray-700"></div>
                </div>
                <button id="alertModalClose" type="button" class="rounded-lg p-2 text-gray-400 hover:bg-gray-100" aria-label="Close">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="mt-5 flex justify-end gap-2">
                <button id="alertModalOk" type="button" class="btn-primary">OK</button>
            </div>
        </div>
    `;

    document.body.appendChild(overlay);

    const close = () => {
        overlay.classList.add('hidden');
        overlay.classList.remove('flex');
    };

    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) close();
    });

    overlay.querySelector('#alertModalClose')?.addEventListener('click', close);
    overlay.querySelector('#alertModalOk')?.addEventListener('click', close);

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') close();
    });

    return overlay;
}

window.showAlertModal = function(message, type = 'info', options = {}) {
    const overlay = ensureAlertModal();
    const titleEl = overlay.querySelector('#alertModalTitle');
    const msgEl = overlay.querySelector('#alertModalMessage');
    const panel = overlay.querySelector('#alertModalPanel');

    const titles = {
        success: 'Success',
        error: 'Error',
        warning: 'Warning',
        info: 'Alert',
    };

    const rings = {
        success: 'ring-green-200',
        error: 'ring-red-200',
        warning: 'ring-orange-200',
        info: 'ring-brand-200',
    };

    const safeType = titles[type] ? type : 'info';

    if (titleEl) titleEl.textContent = options.title || titles[safeType];
    if (msgEl) msgEl.textContent = message || 'Done.';

    if (panel) {
        panel.classList.remove('ring-green-200', 'ring-red-200', 'ring-orange-200', 'ring-brand-200');
        panel.classList.add(rings[safeType]);
    }

    overlay.classList.remove('hidden');
    overlay.classList.add('flex');
};

// Livewire modal bridge (server-side -> centered modal alert)
document.addEventListener('livewire:init', () => {
    if (typeof window.Livewire === 'undefined') return;

    window.Livewire.on('alert', (payload = {}) => {
        const message = typeof payload === 'string' ? payload : (payload.message || 'Done.');
        const type = typeof payload === 'object' && payload.type ? payload.type : 'info';
        const title = typeof payload === 'object' && payload.title ? payload.title : undefined;

        window.showAlertModal(message, type, { title });
    });

    // Message unread sound (plays only when server emits "messages-unread")
    let audioContext;
    let audioUnlocked = false;

    const unlockAudio = () => {
        audioUnlocked = true;
        document.removeEventListener('pointerdown', unlockAudio);
        document.removeEventListener('keydown', unlockAudio);
    };

    document.addEventListener('pointerdown', unlockAudio, { once: true });
    document.addEventListener('keydown', unlockAudio, { once: true });

    const playBeep = () => {
        if (!audioUnlocked) return;

        try {
            audioContext = audioContext || new (window.AudioContext || window.webkitAudioContext)();
            const ctx = audioContext;

            const oscillator = ctx.createOscillator();
            const gain = ctx.createGain();

            oscillator.type = 'sine';
            oscillator.frequency.value = 880;

            const now = ctx.currentTime;
            gain.gain.setValueAtTime(0.0001, now);
            gain.gain.exponentialRampToValueAtTime(0.12, now + 0.01);
            gain.gain.exponentialRampToValueAtTime(0.0001, now + 0.18);

            oscillator.connect(gain);
            gain.connect(ctx.destination);

            oscillator.start(now);
            oscillator.stop(now + 0.2);
        } catch (e) {
            // ignore
        }
    };

    window.Livewire.on('messages-unread', () => {
        playBeep();
    });
});

// Bulk operations
window.selectAll = function(checkbox) {
    const checkboxes = document.querySelectorAll('.checkbox-custom');
    checkboxes.forEach(cb => cb.checked = checkbox.checked);
};

window.getSelectedIds = function() {
    const checkboxes = document.querySelectorAll('.checkbox-custom:checked');
    return Array.from(checkboxes).map(cb => cb.value);
};

// Export functionality
window.exportData = function(type, format = 'csv') {
    showNotification(`Exporting ${type} as ${format.toUpperCase()}...`, 'info');
    
    // Simulate export
    setTimeout(() => {
        showNotification(`${type} exported successfully!`, 'success');
    }, 1500);
};

// Initialize tooltips
document.querySelectorAll('[title]').forEach(element => {
    element.addEventListener('mouseenter', function() {
        const tooltip = document.createElement('div');
        tooltip.className = 'absolute z-50 px-2 py-1 text-xs text-white bg-gray-900 rounded shadow-lg animate-fade-in';
        tooltip.textContent = this.getAttribute('title');
        tooltip.style.bottom = '100%';
        tooltip.style.left = '50%';
        tooltip.style.transform = 'translateX(-50%) translateY(-4px)';
        
        this.style.position = 'relative';
        this.appendChild(tooltip);
        
        this.addEventListener('mouseleave', function() {
            tooltip.remove();
        }, { once: true });
    });
});
