document.addEventListener("DOMContentLoaded", () => {
    const lucide = window.lucide;
    const body = document.body;
    const themeToggle = document.getElementById("themeToggle");
    const sidebarToggle = document.getElementById("sidebarToggle");
    const sidebar = document.getElementById("sidebar");
    const mobileMenuBtn = document.getElementById("mobileMenuBtn");

    // 1. Theme Logic
    const savedTheme = localStorage.getItem("theme");
    if (savedTheme === "dark") body.classList.add("dark-mode");

    themeToggle.addEventListener("click", () => {
        body.classList.toggle("dark-mode");
        localStorage.setItem("theme", body.classList.contains("dark-mode") ? "dark" : "light");
    });

    // 2. Sidebar & Mobile Logic
    sidebarToggle.addEventListener("click", () => {
        sidebar.classList.toggle("collapsed");
        localStorage.setItem("sidebarCollapsed", sidebar.classList.contains("collapsed"));
    });

    if (localStorage.getItem("sidebarCollapsed") === "true") sidebar.classList.add("collapsed");

    mobileMenuBtn.addEventListener("click", () => sidebar.classList.toggle("mobile-open"));

    // User Menu Dropdown Logic
    const initializeUserMenu = () => {
        const nameEl = document.querySelector('.sidebar-footer .user-name');
        const roleEl = document.querySelector('.sidebar-footer .user-role');
        const umdName = document.getElementById('umdName');
        const umdRole = document.getElementById('umdRole');
        const umdAvatar = document.getElementById('umdAvatar');
        
        if (nameEl && umdName) {
            const name = nameEl.textContent.trim();
            umdName.textContent = name;
            if (umdAvatar) umdAvatar.textContent = name.charAt(0).toUpperCase();
        }
        if (roleEl && umdRole) umdRole.textContent = roleEl.textContent.trim();

        const btn = document.getElementById('userMenuBtn');
        const dd = document.getElementById('userMenuDropdown');
        if (btn && dd) {
            btn.addEventListener('click', e => {
                e.stopPropagation();
                dd.classList.toggle('umd-open');
            });
            document.addEventListener('click', e => {
                if (!dd.contains(e.target) && e.target !== btn) {
                    dd.classList.remove('umd-open');
                }
            });
            document.addEventListener('keydown', e => {
                if (e.key === 'Escape') dd.classList.remove('umd-open');
            });
        }

        const signOutLinks = document.querySelectorAll('.umd-sign-out');
        signOutLinks.forEach(link => {
            link.addEventListener('click', async e => {
                e.preventDefault();
                const dest = link.getAttribute('href');
                const result = await Swal.fire({
                    title: 'Sign Out?',
                    text: 'You are about to sign out of your account.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, Sign Out',
                    cancelButtonText: 'Stay',
                    reverseButtons: true
                });
                if (result.isConfirmed) {
                    window.location.href = dest;
                }
            });
        });
    };
    initializeUserMenu();

    // Real-time Clock Functionality
    function initClock() {
        const clockEl = document.getElementById('realTimeClock');
        if (!clockEl) return;

        const updateClock = () => {
            const days = ['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'];
            const months = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];
            const now = new Date();
            const dayName = days[now.getDay()];
            const monthName = months[now.getMonth()];
            const date = now.getDate();
            const year = now.getFullYear();
            let hours = now.getHours();
            const minutes = now.getMinutes().toString().padStart(2, '0');
            const seconds = now.getSeconds().toString().padStart(2, '0');
            const ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12;
            hours = hours ? hours : 12;
            const formattedHours = hours.toString().padStart(2, '0');

            clockEl.textContent = `${dayName}, ${monthName} ${date}, ${year}, ${formattedHours}:${minutes}:${seconds} ${ampm}`;
        };

        setInterval(updateClock, 1000);
        updateClock();
    }
    initClock();

    // --- LEARNING MODULES LOGIC ---
    
    const modulesGrid = document.getElementById('learningModulesGrid');
    const btnRefresh = document.getElementById('btnRefresh');

    const fetchMyModules = async () => {
        modulesGrid.innerHTML = `
           <div class="empty-state" style="grid-column: 1 / -1; padding: 48px; background: var(--surface); border: 1px solid var(--border-color); border-radius: 16px; text-align: center;">
                <i data-lucide="loader-2" class="spin" style="color: var(--brand-green); width: 32px; height: 32px; margin-bottom: 16px;"></i>
                <h3 style="color: var(--text-primary); margin-bottom: 8px;">Loading your modules...</h3>
           </div>
        `;
        if (typeof lucide !== 'undefined') lucide.createIcons();

        try {
            const response = await fetch('backend/be_learningmgt.php?action=fetch_my_modules');
            if (!response.ok) throw new Error('Network response was not ok');
            const result = await response.json();
            
            if (result.success) {
                renderModules(result.data);
            } else {
                Swal.fire('Error', result.message || 'Failed to load learning modules.', 'error');
                modulesGrid.innerHTML = `<div style="grid-column: 1 / -1; color: #ef4444; text-align: center;">Failed to load data.</div>`;
            }
        } catch (error) {
            console.error('Fetch error:', error);
            Swal.fire('Error', 'A network error occurred. Please try again.', 'error');
            modulesGrid.innerHTML = `<div style="grid-column: 1 / -1; color: #ef4444; text-align: center;">Connection error.</div>`;
        }
    };

    const getStatusStyles = (status) => {
        switch(status) {
            case 'Completed': return { icon: 'check-circle', class: 'completed', text: 'Completed', btnText: 'View', btnClass: 'secondary' };
            case 'In Progress': return { icon: 'play-circle', class: 'in-progress', text: 'In Progress', btnText: 'Continue', btnClass: '' };
            default: return { icon: 'clock', class: 'pending', text: 'Pending', btnText: 'Start Training', btnClass: '' }; // Pending
        }
    };

    const renderModules = (modules) => {
        if (!modules || modules.length === 0) {
            modulesGrid.innerHTML = `
                <div class="empty-state" style="grid-column: 1 / -1; padding: 48px; background: var(--surface); border: 1px solid var(--border-color); border-radius: 16px; text-align: center;">
                    <div style="background: rgba(44, 160, 120, 0.1); width: 64px; height: 64px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 16px;">
                        <i data-lucide="award" style="color: var(--brand-green); width: 32px; height: 32px;"></i>
                    </div>
                    <h3 style="color: var(--text-primary); margin-bottom: 8px;">You're all caught up!</h3>
                    <p style="color: var(--text-secondary); max-width: 400px; margin: 0 auto;">There are no pending training modules assigned to you at the moment.</p>
               </div>
            `;
            if (typeof lucide !== 'undefined') lucide.createIcons();
            return;
        }

        modulesGrid.innerHTML = modules.map(mod => {
            const styles = getStatusStyles(mod.Status);
            return `
                <div class="module-card">
                    <div class="module-header">
                        <div class="module-icon-container ${styles.class}">
                            <i data-lucide="book-open"></i>
                        </div>
                        <span class="module-badge ${styles.class}">${styles.text}</span>
                    </div>
                    <h3 class="module-title">${mod.ModuleName}</h3>
                    <p class="module-desc">${mod.Description || 'No description provided for this module.'}</p>
                    
                    <div class="module-footer">
                        <span style="font-size: 11px; color: var(--text-tertiary);">Assigned: ${mod.AssignedDate ? mod.AssignedDate.split(' ')[0] : 'N/A'}</span>
                        <a href="training_view.php?assignment_id=${mod.AssignmentID}" class="btn-action ${styles.btnClass}">
                            ${styles.btnText} <i data-lucide="arrow-right" style="width:14px; height:14px;"></i>
                        </a>
                    </div>
                </div>
            `;
        }).join('');
        
        if (typeof lucide !== 'undefined') lucide.createIcons();
    };

    btnRefresh.addEventListener('click', fetchMyModules);

    // Initial Load
    if (typeof lucide !== 'undefined') lucide.createIcons();
    fetchMyModules();
});
