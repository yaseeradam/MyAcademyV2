<!-- Notification Container -->
<div id="notificationContainer" class="fixed top-20 right-6 z-50 space-y-2"></div>

<script>
// Enhanced notification system
window.showNotification = function(message, type = 'info', options = {}) {
    const container = document.getElementById('notificationContainer');
    if (!container) return;
    
    const notification = document.createElement('div');
    const id = 'notification-' + Date.now();
    notification.id = id;
    
    const icons = {
        success: '<path d="M20 6 9 17l-5-5" />',
        error: '<path d="M6 18L18 6M6 6l12 12" />',
        warning: '<path d="M12 9v2m0 4h.01" />',
        info: '<path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z" />'
    };
    
    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        warning: 'bg-orange-500',
        info: 'bg-brand-500'
    };
    
    notification.className = `p-4 rounded-lg shadow-lg text-white ${colors[type]} animate-slide-up max-w-sm`;
    notification.innerHTML = `
        <div class="flex items-start gap-3">
            <svg class="h-5 w-5 flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                ${icons[type]}
            </svg>
            <div class="flex-1">
                <p class="text-sm font-medium">${message}</p>
                ${options.action ? `<button class="mt-2 text-xs underline hover:no-underline" onclick="${options.action}">${options.actionText || 'View'}</button>` : ''}
            </div>
            <button onclick="closeNotification('${id}')" class="flex-shrink-0 ml-2">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    `;
    
    container.appendChild(notification);
    
    // Auto-remove after duration
    const duration = options.duration || (type === 'error' ? 5000 : 3000);
    setTimeout(() => {
        closeNotification(id);
    }, duration);
    
    return id;
};

window.closeNotification = function(id) {
    const notification = document.getElementById(id);
    if (notification) {
        notification.style.opacity = '0';
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => notification.remove(), 300);
    }
};

// Show progress notification
window.showProgressNotification = function(message, progress = 0) {
    const id = showNotification(message, 'info', { duration: 0 });
    const notification = document.getElementById(id);
    
    if (notification) {
        // Add progress bar
        const progressBar = document.createElement('div');
        progressBar.className = 'mt-2 bg-white/20 rounded-full h-1';
        progressBar.innerHTML = `<div class="bg-white rounded-full h-1 transition-all duration-300" style="width: ${progress}%"></div>`;
        notification.querySelector('.flex-1').appendChild(progressBar);
    }
    
    return {
        id,
        updateProgress: (newProgress) => {
            const bar = notification?.querySelector('.bg-white');
            if (bar) bar.style.width = `${newProgress}%`;
            if (newProgress >= 100) {
                setTimeout(() => closeNotification(id), 1000);
            }
        }
    };
};

// Show success notification with action
window.showSuccessNotification = function(message, action = null, actionText = null) {
    return showNotification(message, 'success', {
        action,
        actionText,
        duration: 4000
    });
};

// Show error notification
window.showErrorNotification = function(message) {
    return showNotification(message, 'error', {
        duration: 5000
    });
};

// Show warning notification
window.showWarningNotification = function(message) {
    return showNotification(message, 'warning', {
        duration: 4000
    });
};
</script>