/* Shared Notification Logic - Premium Polling System */
function initGlobalNotifications(moduleTarget = 'compensation_cycle') {
    const notifBtn = document.getElementById('notifBtn');
    const notifDropdown = document.getElementById('notifDropdown');
    const notifBadge = document.getElementById('notifBadge');
    const notifList = document.getElementById('notifList');
    const markReadAll = document.getElementById('markReadAll');

    if (!notifBtn || !notifDropdown) return;

    // Toggle dropdown
    notifBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        notifDropdown.classList.toggle('hidden');
    });

    document.addEventListener('click', (e) => {
        if (!notifDropdown.contains(e.target) && e.target !== notifBtn) {
            notifDropdown.classList.add('hidden');
        }
    });

    const fetchNotifs = async () => {
        try {
            // Determine path based on module location
            const isSubmodule = window.location.pathname.includes('/modules/');
            const apiPath = isSubmodule
                ? `../compensation/be_notifications.php?action=fetch&module_target=${moduleTarget}`
                : `modules/compensation/be_notifications.php?action=fetch&module_target=${moduleTarget}`;

            const res = await fetch(apiPath);
            const data = await res.json();

            if (data.success) {
                renderNotifs(data.notifications);
                if (data.unread_count > 0) {
                    notifBadge.textContent = data.unread_count;
                    notifBadge.classList.remove('hidden');
                } else {
                    notifBadge.classList.add('hidden');
                }
            }
        } catch (e) {
            console.error('Notification fetch error:', e);
        }
    };

    const renderNotifs = (notifs) => {
        if (!notifList) return;

        if (notifs.length === 0) {
            notifList.innerHTML = '<div class="notif-empty">No new notifications</div>';
            notifBadge.classList.add('hidden'); // Hide badge if list is empty
            return;
        }

        notifList.innerHTML = notifs.map(n => {
            const date = new Date(n.created_at).toLocaleString('en-PH', {
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
            const unreadClass = parseInt(n.is_read) === 0 ? 'unread' : '';

            return `
                <div class="notif-item ${unreadClass}">
                    <div class="notif-item-icon">
                        <i data-lucide="bell"></i>
                    </div>
                    <div class="notif-item-content">
                        <div class="notif-message">${n.message}</div>
                        <div class="notif-time">${date}</div>
                    </div>
                </div>
            `;
        }).join('');

        if (window.lucide) window.lucide.createIcons();
    };

    if (markReadAll) {
        markReadAll.addEventListener('click', async (e) => {
            e.stopPropagation();
            try {
                const isSubmodule = window.location.pathname.includes('/modules/');
                const apiPath = isSubmodule
                    ? '../compensation/be_notifications.php'
                    : 'modules/compensation/be_notifications.php';

                const res = await fetch(apiPath, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `action=mark_read&module_target=${moduleTarget}`
                });
                const data = await res.json();
                if (data.success) {
                    fetchNotifs();
                }
            } catch (e) {
                console.error('Failed to mark as read:', e);
            }
        });
    }

    // Initial fetch and polling every 30 seconds
    fetchNotifs();
    setInterval(fetchNotifs, 30000);
}

// Don't auto-init here if we want to pass parameters, or use a default
// document.addEventListener('DOMContentLoaded', () => initGlobalNotifications('compensation_cycle'));

