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

    // --- ASSIGN TRAINING TO EMPLOYEES LOGIC ---

    const btnOpenAssignModal = document.getElementById('btnOpenAssignModal');
    const assignEmployeesModal = document.getElementById('assignEmployeesModal');
    const closeAssignModal = document.getElementById('closeAssignModal');
    const cancelAssignModal = document.getElementById('cancelAssignModal');
    const btnSaveAssignments = document.getElementById('btnSaveAssignments');
    const employeesList = document.getElementById('employeesList');
    const assignModuleId = document.getElementById('assignModuleId').value;
    const employeeSearch = document.getElementById('employeeSearch');

    let allEmployees = [];

    const fetchEmployeesForModule = async () => {
        employeesList.innerHTML = `<div class="empty-state" style="text-align:center;"><i data-lucide="loader-2" class="spin"></i><p>Loading employees...</p></div>`;
        if (typeof lucide !== 'undefined') lucide.createIcons();

        try {
            const formData = new FormData();
            formData.append('module_id', assignModuleId);
            
            const response = await fetch('backend/be_trainingmgt.php?action=fetch_employees_for_module', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            
            if (result.success) {
                allEmployees = result.data;
                renderEmployees(allEmployees);
            } else {
                employeesList.innerHTML = `<p style="color:#ef4444; font-size:13px;">${result.message || 'Failed to load employees'}</p>`;
            }
        } catch (error) {
            console.error('Fetch error:', error);
            employeesList.innerHTML = `<p style="color:#ef4444; font-size:13px;">Network error while loading employees.</p>`;
        }
    };

    const renderEmployees = (employees) => {
        if (!employees || employees.length === 0) {
            employeesList.innerHTML = `<p style="color:var(--text-secondary); font-size:13px;">No matching employees found.</p>`;
            return;
        }

        employeesList.innerHTML = employees.map(emp => {
            const isChecked = emp.IsAssigned ? 'checked' : '';
            const selectedClass = emp.IsAssigned ? 'selected' : '';
            const disabled = emp.Status === 'Completed' ? 'disabled' : '';
            const badge = emp.IsAssigned ? `<span class="module-badge ${emp.Status === 'Completed' ? 'completed' : 'pending'}" style="margin-left:auto; font-size:11px; padding:2px 6px; border-radius:10px; font-weight:600;">${emp.Status}</span>` : '';
            const name = `${emp.FirstName} ${emp.LastName}`;

            return `
                <label class="module-checkbox-container ${selectedClass}" ${disabled ? 'style="opacity:0.6; cursor:not-allowed;"' : 'style="padding:10px; border:1px solid var(--border-color); border-radius:8px; display:flex; align-items:center; gap:12px; cursor:pointer;"'}>
                    <input type="checkbox" class="employee-checkbox" name="employees[]" value="${emp.EmployeeID}" ${isChecked} ${disabled} style="width:18px; height:18px; accent-color:var(--brand-green);">
                    <div class="module-info" style="display:flex; flex-direction:column;">
                        <span class="module-title" style="font-size:14px; font-weight:600;">${name} <span style="font-weight:400; color:var(--text-secondary); font-size:12px;">(${emp.EmployeeCode})</span></span>
                        <span class="module-desc" style="font-size:12px; color:var(--text-secondary);">${emp.PositionName || 'No Position'} • ${emp.DepartmentName || 'No Dept'}</span>
                    </div>
                    ${badge}
                </label>
            `;
        }).join('');

        // Visual toggle effect
        document.querySelectorAll('.employee-checkbox').forEach(cb => {
            if (!cb.disabled) {
                cb.addEventListener('change', (e) => {
                    const container = e.target.closest('.module-checkbox-container');
                    if (e.target.checked) container.style.borderColor = 'var(--brand-green)';
                    else container.style.borderColor = 'var(--border-color)';
                });
            }
        });
    };

    // Sub-search feature inside modal
    if (employeeSearch) {
        employeeSearch.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase();
            const filtered = allEmployees.filter(emp => 
                (emp.FirstName + ' ' + emp.LastName).toLowerCase().includes(query) ||
                (emp.EmployeeCode && emp.EmployeeCode.toLowerCase().includes(query)) ||
                (emp.PositionName && emp.PositionName.toLowerCase().includes(query))
            );
            renderEmployees(filtered);
        });
    }

    const openModal = () => {
        assignEmployeesModal.classList.add('active');
        fetchEmployeesForModule();
    };

    const closeModal = () => assignEmployeesModal.classList.remove('active');

    if (btnOpenAssignModal) btnOpenAssignModal.addEventListener('click', openModal);
    if (closeAssignModal) closeAssignModal.addEventListener('click', closeModal);
    if (cancelAssignModal) cancelAssignModal.addEventListener('click', closeModal);

    // Save Assignments
    if (btnSaveAssignments) {
        btnSaveAssignments.addEventListener('click', async () => {
            const checkboxes = document.querySelectorAll('.employee-checkbox');
            const assignedEmployees = [];
            
            checkboxes.forEach(cb => {
                if (cb.checked) {
                    assignedEmployees.push(cb.value);
                }
            });

            const originalBtnText = btnSaveAssignments.innerHTML;
            btnSaveAssignments.innerHTML = `<i data-lucide="loader-2" class="spin"></i> Saving...`;
            btnSaveAssignments.disabled = true;
            if (typeof lucide !== 'undefined') lucide.createIcons();

            try {
                const formData = new FormData();
                formData.append('module_id', assignModuleId);
                formData.append('employee_ids', JSON.stringify(assignedEmployees));

                const response = await fetch('backend/be_trainingmgt.php?action=save_module_assignments', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.success) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Employees assigned successfully!',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                    closeModal();
                } else {
                    Swal.fire('Error', result.message || 'Failed to save assignments', 'error');
                }
            } catch (error) {
                console.error('Save error:', error);
                Swal.fire('Error', 'A network error occurred. Please try again.', 'error');
            } finally {
                btnSaveAssignments.innerHTML = originalBtnText;
                btnSaveAssignments.disabled = false;
                if (typeof lucide !== 'undefined') lucide.createIcons();
            }
        });
    }

    // Init
    if (typeof lucide !== "undefined") lucide.createIcons();
});
