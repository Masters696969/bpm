// Session Timeout Manager
class SessionTimeoutManager {
    constructor() {
        this.timeoutDuration = 2 * 60 * 1000; // 2 minutes in milliseconds
        this.warningDuration = 30 * 1000; // 30 seconds warning
        this.timeoutTimer = null;
        this.warningTimer = null;
        this.lastActivity = Date.now();
        this.isWarningShown = false;
        
        this.init();
    }

    init() {
        // Start monitoring
        this.startMonitoring();
        
        // Track user activity
        this.trackActivity();
        
        // Create timer display in header
        this.createTimerDisplay();
        
        // Update timer every second
        setInterval(() => this.updateTimerDisplay(), 1000);
    }

    trackActivity() {
        // Track various user activities
        const events = [
            'mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click'
        ];
        
        events.forEach(event => {
            document.addEventListener(event, () => {
                this.resetTimer();
            }, true);
        });
        
        // Track AJAX calls
        const originalFetch = window.fetch;
        window.fetch = (...args) => {
            this.resetTimer();
            return originalFetch.apply(this, args);
        };
    }

    resetTimer() {
        this.lastActivity = Date.now();
        this.isWarningShown = false;
        
        // Clear existing timers
        if (this.timeoutTimer) clearTimeout(this.timeoutTimer);
        if (this.warningTimer) clearTimeout(this.warningTimer);
        
        // Set new timers
        this.warningTimer = setTimeout(() => {
            this.showWarning();
        }, this.timeoutDuration - this.warningDuration);
        
        this.timeoutTimer = setTimeout(() => {
            this.logout();
        }, this.timeoutDuration);
    }

    startMonitoring() {
        this.resetTimer();
    }

    createTimerDisplay() {
        // Find any h1 header on admin pages
        const header = document.querySelector('h1');
        if (!header) {
            // Fallback to original position if no header found
            this.createOriginalTimer();
            return;
        }
        
        // Create minimalist timer container
        const timerContainer = document.createElement('span');
        timerContainer.id = 'session-timer';
        timerContainer.style.cssText = `
            display: inline-flex;
            align-items: center;
            margin-left: 12px;
            font-size: 14px;
            color: #2ca078;
            font-weight: 400;
            opacity: 0.8;
            transition: all 0.2s ease;
            cursor: default;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        `;
        
        // Add subtle separator
        const separator = document.createElement('span');
        separator.textContent = '•';
        separator.style.cssText = `
            margin: 0 8px;
            color: #2ca078;
        `;
        
        // Add timer text
        const timerText = document.createElement('span');
        timerText.id = 'timer-text';
        timerText.textContent = '2:00';
        timerText.style.cssText = `
            font-weight: 500;
            color: #2ca078;
        `;
        
        timerContainer.appendChild(separator);
        timerContainer.appendChild(timerText);
        
        // Insert after the header text, inline with it
        header.appendChild(timerContainer);
        
        // Add hover effect
        timerContainer.addEventListener('mouseenter', () => {
            timerContainer.style.opacity = '1';
        });
        
        timerContainer.addEventListener('mouseleave', () => {
            timerContainer.style.opacity = '0.8';
        });
    }

    createOriginalTimer() {
        // Minimalist fallback timer for pages without h1
        const timerContainer = document.createElement('div');
        timerContainer.id = 'session-timer';
        timerContainer.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: rgba(44, 160, 120, 0.1);
            color: #2ca078;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            border: 1px solid rgba(44, 160, 120, 0.3);
            box-shadow: 0 2px 4px rgba(44, 160, 120, 0.1);
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            backdrop-filter: blur(10px);
        `;
        
        // Add timer text
        const timerText = document.createElement('span');
        timerText.id = 'timer-text';
        timerText.textContent = '2:00';
        
        timerContainer.appendChild(timerText);
        
        // Add to page
        document.body.appendChild(timerContainer);
    }

    updateTimerDisplay() {
        const timerText = document.getElementById('timer-text');
        if (!timerText) return;
        
        const elapsed = Date.now() - this.lastActivity;
        const remaining = Math.max(0, this.timeoutDuration - elapsed);
        
        const minutes = Math.floor(remaining / 60000);
        const seconds = Math.floor((remaining % 60000) / 1000);
        
        const timeString = `${minutes}:${seconds.toString().padStart(2, '0')}`;
        timerText.textContent = timeString;
        
        // Change color based on remaining time
        const timerContainer = document.getElementById('session-timer');
        if (timerContainer) {
            if (remaining <= 30000) { // Last 30 seconds
                timerContainer.style.color = '#e53e3e';
                timerContainer.style.opacity = '1';
                
                const timerText = document.getElementById('timer-text');
                if (timerText) {
                    timerText.style.color = '#e53e3e';
                    timerText.style.fontWeight = '600';
                }
                
                const separator = timerContainer.querySelector('span:first-child');
                if (separator) {
                    separator.style.color = '#feb2b2';
                }
            } else if (remaining <= 60000) { // Last minute
                timerContainer.style.color = '#dd6b20';
                timerContainer.style.opacity = '0.9';
                
                const timerText = document.getElementById('timer-text');
                if (timerText) {
                    timerText.style.color = '#dd6b20';
                    timerText.style.fontWeight = '500';
                }
                
                const separator = timerContainer.querySelector('span:first-child');
                if (separator) {
                    separator.style.color = '#fbd38d';
                }
            } else {
                // Normal state - green theme
                timerContainer.style.color = '#2ca078';
                timerContainer.style.opacity = '0.8';
                
                const timerText = document.getElementById('timer-text');
                if (timerText) {
                    timerText.style.color = '#2ca078';
                    timerText.style.fontWeight = '500';
                }
                
                const separator = timerContainer.querySelector('span:first-child');
                if (separator) {
                    separator.style.color = '#2ca078';
                }
                
                // For fallback timer (fixed position)
                if (timerContainer.style.position === 'fixed') {
                    timerContainer.style.background = 'rgba(44, 160, 120, 0.1)';
                    timerContainer.style.borderColor = 'rgba(44, 160, 120, 0.3)';
                    timerContainer.style.boxShadow = '0 2px 4px rgba(44, 160, 120, 0.1)';
                }
            }
        }
    }

    showWarning() {
        if (this.isWarningShown) return;
        this.isWarningShown = true;
        
        Swal.fire({
            icon: 'warning',
            title: 'Session Expiring Soon!',
            html: `
                <div style="text-align: center;">
                    <p>Your session will expire in <strong>30 seconds</strong> due to inactivity.</p>
                    <p>Do you want to extend your session?</p>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Extend Session',
            cancelButtonText: 'Logout',
            confirmButtonColor: '#2ca078',
            cancelButtonColor: '#e53e3e',
            timer: 30000,
            timerProgressBar: true,
            didOpen: () => {
                // Add countdown to warning
                const timer = Swal.getHtmlContainer().querySelector('.swal2-timer-progress-bar-container');
                if (timer) {
                    timer.style.marginTop = '15px';
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                this.extendSession();
            } else if (result.isDismissed || result.isDenied) {
                this.logout();
            }
        });
    }

    extendSession() {
        // Reset timer
        this.resetTimer();
        
        // Show success message
        Swal.fire({
            icon: 'success',
            title: 'Session Extended',
            text: 'Your session has been extended for another 2 minutes.',
            timer: 2000,
            showConfirmButton: false,
            position: 'top-end',
            toast: true
        });
    }

    logout() {
        // Clear all timers
        if (this.timeoutTimer) clearTimeout(this.timeoutTimer);
        if (this.warningTimer) clearTimeout(this.warningTimer);
        
        // Show logout message
        Swal.fire({
            icon: 'info',
            title: 'Session Expired',
            text: 'You have been logged out due to inactivity.',
            timer: 3000,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        }).then(() => {
            // Redirect to login with full URL
            window.location.href = 'http://localhost/bpm/login.php';
        });
        
        // Call server-side logout
        fetch('logout.php', { method: 'POST' }).catch(() => {});
    }
}

// Add pulse animation
const style = document.createElement('style');
style.textContent = `
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
`;
document.head.appendChild(style);

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    // Only initialize on pages that require authentication
    if (window.location.pathname.includes('modules/') || 
        window.location.pathname.includes('dashboard')) {
        new SessionTimeoutManager();
    }
});
