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

    // 3. Submenu Logic
    document.querySelectorAll(".nav-item.has-submenu").forEach((item) => {
        item.addEventListener("click", (e) => {
            const module = item.getAttribute("data-module");
            const submenu = document.getElementById(`submenu-${module}`);
            if(submenu) {
                submenu.classList.toggle("active");
                item.classList.toggle("active");
            }
        });
    });

    // Sidebar Active Link Logic (Merged)
    (function () {
        const path = window.location.pathname;
        const page = path.split('/').pop() || 'dashboard.php';
        const current = page.split('?')[0];

        document.querySelectorAll('.sidebar .nav-item, .sidebar .submenu-item').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.sidebar .nav-item-group').forEach(group => group.classList.remove('active'));

        const submenuMatch = document.querySelector(`.sidebar a.submenu-item[href$="${current}"]`);
        if (submenuMatch) {
            submenuMatch.classList.add('active');
            const parentGroup = submenuMatch.closest('.nav-item-group');
            if (parentGroup) {
                parentGroup.classList.add('active');
                const submenu = parentGroup.querySelector('.submenu');
                if (submenu) submenu.style.maxHeight = '500px';
                const btn = parentGroup.querySelector('.nav-item.has-submenu');
                if (btn) btn.classList.add('active');
            }
            return;
        }

        const navMatch = document.querySelector(`.sidebar a.nav-item[href$="${current}"]`);
        if (navMatch) navMatch.classList.add('active');
    })();

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

    // --- TRAINING MODULES FETCH LOGIC ---

    const modulesGrid = document.getElementById('trainingModulesGrid');
    const btnRefresh = document.getElementById('btnRefresh');

    const fetchTrainingPrograms = async () => {
        modulesGrid.innerHTML = `
           <div class="empty-state" style="grid-column: 1 / -1; padding: 48px; background: var(--surface); border: 1px solid var(--border-color); border-radius: 12px; text-align: center;">
                <i data-lucide="loader-2" class="spin" style="color: var(--brand-green); width: 32px; height: 32px; margin-bottom: 16px;"></i>
                <h3 style="color: var(--text-primary);">Loading training programs...</h3>
           </div>
        `;
        if (typeof lucide !== 'undefined') lucide.createIcons();

        try {
            // Re-using the fetch_modules logic from backend
            const response = await fetch('backend/be_trainingmgt.php?action=fetch_all_modules');
            if (!response.ok) throw new Error('Network response was not ok');
            const result = await response.json();
            
            if (result.success) {
                renderPrograms(result.data);
            } else {
                Swal.fire('Error', result.message || 'Failed to load programs.', 'error');
                modulesGrid.innerHTML = `<div style="grid-column: 1 / -1; color: #ef4444; text-align: center;">Failed to load data.</div>`;
            }
        } catch (error) {
            console.error('Fetch error:', error);
            Swal.fire('Error', 'A network error occurred based on backend query. Please try again.', 'error');
            modulesGrid.innerHTML = `<div style="grid-column: 1 / -1; color: #ef4444; text-align: center;">Connection error.</div>`;
        }
    };

    const renderPrograms = (programs) => {
        if (!programs || programs.length === 0) {
            modulesGrid.innerHTML = `
                <div class="empty-state" style="grid-column: 1 / -1; padding: 48px; background: var(--surface); border: 1px solid var(--border-color); border-radius: 12px; text-align: center;">
                    <i data-lucide="book-x" style="color: var(--text-secondary); width: 32px; height: 32px; margin-bottom: 16px;"></i>
                    <h3 style="color: var(--text-primary);">No training programs found</h3>
                </div>
            `;
            if (typeof lucide !== 'undefined') lucide.createIcons();
            return;
        }

        modulesGrid.innerHTML = programs.map(prog => {
            const safeTitle = prog.ModuleName.replace(/'/g, "\\'");
            const safeDesc = (prog.Description || '').replace(/'/g, "\\'");
            return `
                <div class="program-card">
                    <div class="program-header" style="justify-content: space-between; align-items: flex-start;">
                        <div style="display:flex; align-items:center; gap:16px;">
                            <div class="program-icon">
                                <i data-lucide="book-open"></i>
                            </div>
                            <h3 class="program-title">${prog.ModuleName}</h3>
                        </div>
                        <button class="icon-btn" onclick="openEditModal(${prog.ModuleID}, '${safeTitle}', '${safeDesc}')" title="Edit Program" style="color:var(--text-secondary); background:var(--background); border:1px solid var(--border-color); width:32px; height:32px; display:flex; align-items:center; justify-content:center; border-radius:8px; cursor:pointer;">
                            <i data-lucide="edit-3" style="width:16px; height:16px;"></i>
                        </button>
                    </div>
                    <p class="program-desc">${prog.Description || 'No description provided.'}</p>
                    
                    <div class="program-footer">
                        <a href="training_detail.php?id=${prog.ModuleID}" class="btn-view">
                            View & Assign <i data-lucide="arrow-right" style="width:14px; height:14px;"></i>
                        </a>
                    </div>
                </div>
            `;
        }).join('');
        
        if (typeof lucide !== 'undefined') lucide.createIcons();
    };

    if (btnRefresh) {
        btnRefresh.addEventListener('click', fetchTrainingPrograms);
    }

    // --- ADD PROGRAM MODAL LOGIC ---
    const btnAddProgram = document.getElementById('btnAddProgram');
    const addProgramModal = document.getElementById('addProgramModal');
    const closeAddProgramModal = document.getElementById('closeAddProgramModal');
    const cancelAddProgramModal = document.getElementById('cancelAddProgramModal');
    const addProgramForm = document.getElementById('addProgramForm');
    const btnSaveProgram = document.getElementById('btnSaveProgram');

    const closeAddModal = () => {
        addProgramModal.classList.remove('active');
        addProgramForm.reset();
    };

    if (btnAddProgram) btnAddProgram.addEventListener('click', () => addProgramModal.classList.add('active'));
    if (closeAddProgramModal) closeAddProgramModal.addEventListener('click', closeAddModal);
    if (cancelAddProgramModal) cancelAddProgramModal.addEventListener('click', closeAddModal);

    if (addProgramForm) {
        addProgramForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const originalBtnText = btnSaveProgram.innerHTML;
            btnSaveProgram.innerHTML = `<i data-lucide="loader-2" class="spin"></i> Uploading...`;
            btnSaveProgram.disabled = true;
            if (typeof lucide !== 'undefined') lucide.createIcons();

            try {
                const formData = new FormData(addProgramForm);
                const response = await fetch('backend/be_trainingmgt.php?action=create_module', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.success) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Training module added successfully!',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                    closeAddModal();
                    fetchTrainingPrograms(); // Refresh grid
                } else {
                    Swal.fire('Error', result.message || 'Failed to add module', 'error');
                }
            } catch (error) {
                console.error('Upload error:', error);
                Swal.fire('Error', 'A network error occurred. Please try again.', 'error');
            } finally {
                btnSaveProgram.innerHTML = originalBtnText;
                btnSaveProgram.disabled = false;
                if (typeof lucide !== 'undefined') lucide.createIcons();
            }
        });
    }

    // --- EDIT PROGRAM MODAL LOGIC ---
    const editProgramModal = document.getElementById('editProgramModal');
    const closeEditProgramModal = document.getElementById('closeEditProgramModal');
    const cancelEditProgramModal = document.getElementById('cancelEditProgramModal');
    const editProgramForm = document.getElementById('editProgramForm');
    const btnUpdateProgram = document.getElementById('btnUpdateProgram');
    const editModuleID = document.getElementById('editModuleID');
    const editModuleName = document.getElementById('editModuleName');
    const editModuleDesc = document.getElementById('editModuleDesc');

    window.openEditModal = (id, title, desc) => {
        editModuleID.value = id;
        editModuleName.value = title;
        editModuleDesc.value = desc;
        editProgramModal.classList.add('active');
    };

    const closeEditModal = () => {
        editProgramModal.classList.remove('active');
        editProgramForm.reset();
    };

    if (closeEditProgramModal) closeEditProgramModal.addEventListener('click', closeEditModal);
    if (cancelEditProgramModal) cancelEditProgramModal.addEventListener('click', closeEditModal);

    if (editProgramForm) {
        editProgramForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const originalBtnText = btnUpdateProgram.innerHTML;
            btnUpdateProgram.innerHTML = `<i data-lucide="loader-2" class="spin"></i> Updating...`;
            btnUpdateProgram.disabled = true;
            if (typeof lucide !== 'undefined') lucide.createIcons();

            try {
                const formData = new FormData(editProgramForm);
                const response = await fetch('backend/be_trainingmgt.php?action=edit_module', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.success) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Training module updated successfully!',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                    closeEditModal();
                    fetchTrainingPrograms(); // Refresh grid
                } else {
                    Swal.fire('Error', result.message || 'Failed to update module', 'error');
                }
            } catch (error) {
                console.error('Update error:', error);
                Swal.fire('Error', 'A network error occurred. Please try again.', 'error');
            } finally {
                btnUpdateProgram.innerHTML = originalBtnText;
                btnUpdateProgram.disabled = false;
                if (typeof lucide !== 'undefined') lucide.createIcons();
            }
        });
    }

    // Init
    if (typeof lucide !== "undefined") lucide.createIcons();
    fetchTrainingPrograms();
});
