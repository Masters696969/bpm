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

    if (themeToggle) {
        themeToggle.addEventListener("click", () => {
            body.classList.toggle("dark-mode");
            localStorage.setItem("theme", body.classList.contains("dark-mode") ? "dark" : "light");
        });
    }

    // 2. Sidebar Logic
    if (sidebarToggle) {
        sidebarToggle.addEventListener("click", () => {
            sidebar.classList.toggle("collapsed");
            localStorage.setItem("sidebarCollapsed", sidebar.classList.contains("collapsed"));
        });
    }

    if (localStorage.getItem("sidebarCollapsed") === "true") sidebar.classList.add("collapsed");

    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener("click", () => sidebar.classList.toggle("mobile-open"));
    }

    // 3. Submenu Logic
    document.querySelectorAll(".nav-item.has-submenu").forEach((item) => {
        item.addEventListener("click", (e) => {
            const module = item.getAttribute("data-module");
            const submenu = document.getElementById(`submenu-${module}`);
            if (submenu) {
                submenu.classList.toggle("active");
                item.classList.toggle("active");
            }
        });
    });

    // 4. Employee Competency Mapping Logic
    const employeeSearch = document.getElementById("employeeSearch");
    const deptFilter = document.getElementById("deptFilter");
    const employeeRows = document.querySelectorAll("#mainEmployeeTable tbody tr");

    const filterEmployees = () => {
        const query = employeeSearch ? employeeSearch.value.toLowerCase() : "";
        const dept = deptFilter ? deptFilter.value : "";
        employeeRows.forEach(row => {
            const name = row.getAttribute("data-emp-name") || "";
            const rowDept = row.getAttribute("data-dept-name") || "";
            const matchesSearch = name.includes(query);
            const matchesDept = dept === "" || rowDept === dept;
            row.style.display = (matchesSearch && matchesDept) ? "" : "none";
        });
    };

    if (employeeSearch) employeeSearch.addEventListener("keyup", filterEmployees);
    if (deptFilter) deptFilter.addEventListener("change", filterEmployees);

    // 5. Modal Logic
    const manageModal = document.getElementById("manageCompetenciesModal");
    const closeManageModal = document.getElementById("closeManageModal");
    const manageTableBody = document.getElementById("manageTableBody");
    const manageTitle = document.getElementById("manageTitle");
    const manageSubTitle = document.getElementById("manageSubTitle");

    const inlineModal = document.getElementById("inlineActionModal");
    const closeInlineModal = document.getElementById("closeInlineModal");
    const cancelInline = document.getElementById("cancelInline");
    const inlineForm = document.getElementById("inlineActionForm");
    const openAddInlineBtn = document.getElementById("openAddInlineBtn");

    let currentEmployeeId = null;
    let currentDeptId = null;
    let availableItems = [];

    window.openManageModal = (id, name, dept, deptId) => {
        currentEmployeeId = id;
        currentDeptId = deptId;
        if (manageTitle) manageTitle.innerText = name;
        if (manageSubTitle) manageSubTitle.innerText = dept;
        fetchAssignedCompetencies(id);
        if (manageModal) manageModal.classList.add("active");
    };

    const fetchAvailableCompetencies = (deptId) => {
        const tbody = document.getElementById("checklistTableBody");
        if (!tbody) return;
        
        tbody.innerHTML = `<tr><td colspan="3" style="text-align: center; padding: 20px;"><i data-lucide="loader-2" class="spin"></i> Loading...</td></tr>`;
        if (window.lucide) window.lucide.createIcons();

        fetch(`backend/employee_competency_action.php?action=get_available&dept_id=${deptId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    availableItems = data.data;
                    renderChecklistTable(availableItems);
                } else {
                    tbody.innerHTML = `<tr><td colspan="3" style="color: #ef4444; text-align: center;">Error loading list: ${data.message}</td></tr>`;
                }
            })
            .catch(err => {
                tbody.innerHTML = `<tr><td colspan="3" style="color: #ef4444; text-align: center;">Connection error</td></tr>`;
            });
    };

    const renderChecklistTable = (items) => {
        const tbody = document.getElementById("checklistTableBody");
        if (!tbody) return;

        if (!items.length) {
            tbody.innerHTML = `<tr><td colspan="3" style="text-align: center; color: var(--text-tertiary); padding: 10px;">No competencies found for this department.</td></tr>`;
            return;
        }

        const levelsOptions = (window.proficiencyLevels || []).map(l => `<option value="${l.id}">${l.name}</option>`).join('');

        tbody.innerHTML = items.map(item => `
            <tr>
                <td style="text-align: center;">
                    <div class="custom-checkbox-emp">
                        <input type="checkbox" name="comp_select" value="${item.id}" onchange="updateRowState(this)">
                    </div>
                </td>
                <td>
                    <div class="comp-info-mini">
                        <strong>${item.name}</strong>
                    </div>
                </td>
                <td>
                    <select class="form-select-mini" id="level_for_${item.id}" disabled>
                        <option value="">Choose Level...</option>
                        ${levelsOptions}
                    </select>
                </td>
            </tr>
        `).join('');
        
        if (window.lucide) window.lucide.createIcons();
    };

    window.updateRowState = (checkbox) => {
        const levelSelect = document.getElementById(`level_for_${checkbox.value}`);
        if (levelSelect) {
            levelSelect.disabled = !checkbox.checked;
            if (checkbox.checked) {
                levelSelect.focus();
            } else {
                levelSelect.value = "";
            }
        }
    };

    window.toggleSelectAllCompetencies = () => {
        const checkboxes = document.querySelectorAll('input[name="comp_select"]');
        const icon = document.getElementById("selectAllIcon");
        const allChecked = Array.from(checkboxes).every(c => c.checked);
        
        checkboxes.forEach(c => {
            c.checked = !allChecked;
            updateRowState(c);
        });

        if (icon) {
            icon.setAttribute("data-lucide", !allChecked ? "check-square" : "minus-square");
            if (window.lucide) window.lucide.createIcons();
        }
    };

    const fetchAssignedCompetencies = (id) => {
        fetch(`backend/employee_competency_action.php?action=get_assigned&employee_id=${id}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    renderAssignedTable(data.data);
                    // Update main table badge
                    updateMainTableBadge(id, data.data.length);
                    // Refresh overall stats
                    refreshOverallStats();
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(err => Swal.fire('Error', 'Failed to fetch competencies', 'error'));
    };

    const updateMainTableBadge = (id, count) => {
        const badge = document.getElementById(`status-badge-${id}`);
        if (!badge) return;

        const label = count > 0 ? `${count} Competencies` : 'No Assessments';
        const color = count > 0 ? 'var(--brand-green)' : '#94a3b8';
        const icon = count > 0 ? 'check-circle' : 'alert-circle';

        badge.style.background = `${color}10`;
        badge.style.color = color;
        badge.style.borderColor = `${color}20`;
        
        const badgeText = badge.querySelector('.badge-text');
        if (badgeText) badgeText.innerText = label;
        
        const iconEl = badge.querySelector('i');
        if (iconEl) {
            iconEl.setAttribute('data-lucide', icon);
            if (window.lucide) window.lucide.createIcons();
        }
    };

    const refreshOverallStats = () => {
        fetch(`backend/employee_competency_action.php?action=get_stats`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const activeAssessments = document.getElementById('stat-active-assessments');
                    if (activeAssessments) {
                        activeAssessments.innerText = data.data.active_assessments.toLocaleString();
                    }
                }
            });
    };

    const renderAssignedTable = (items) => {
        if (!manageTableBody) return;
        manageTableBody.innerHTML = items.length ? items.map(item => `
            <tr>
                <td><strong>${item.competency_name}</strong></td>
                <td style="text-align: center;">
                    <span class="proficiency-pill" style="background: rgba(44, 160, 120, 0.1); color: var(--brand-green);">
                        ${item.level_name}
                    </span>
                </td>
                <td style="text-align: center; color: var(--text-tertiary); font-size: 12px;">
                    ${new Date(item.assessed_at).toLocaleDateString()}
                </td>
                <td style="text-align: center;">
                    <div style="display: flex; gap: 8px; justify-content: center;">
                        <button class="action-btn-emp delete-btn" onclick="deleteAssignment(${item.id})">
                            <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('') : '<tr><td colspan="4" style="text-align: center; padding: 20px; color: var(--text-tertiary);">No competencies assigned yet.</td></tr>';
        if (window.lucide) window.lucide.createIcons();
    };

    if (closeManageModal) closeManageModal.addEventListener("click", () => manageModal.classList.remove("active"));

    if (openAddInlineBtn) {
        openAddInlineBtn.addEventListener("click", () => {
            const idField = document.getElementById("inline_emp_id");
            if (idField) idField.value = currentEmployeeId;
            if (inlineForm) inlineForm.reset();
            fetchAvailableCompetencies(currentDeptId);
            if (inlineModal) inlineModal.classList.add("active");
        });
    }

    if (closeInlineModal) closeInlineModal.addEventListener("click", () => inlineModal.classList.remove("active"));
    if (cancelInline) cancelInline.addEventListener("click", () => inlineModal.classList.remove("active"));

    if (inlineForm) {
        inlineForm.addEventListener("submit", (e) => {
            e.preventDefault();
            
            const selected = [];
            document.querySelectorAll('input[name="comp_select"]:checked').forEach(chk => {
                const levelId = document.getElementById(`level_for_${chk.value}`).value;
                if (levelId) {
                    selected.push({
                        competency_id: chk.value,
                        level_id: levelId
                    });
                }
            });

            if (selected.length === 0) {
                Swal.fire('Selection Required', 'Please select at least one competency and choose its level.', 'warning');
                return;
            }

            const formData = new FormData();
            formData.append("action", "save_competency");
            formData.append("employee_id", currentEmployeeId);
            formData.append("assignments", JSON.stringify(selected));

            fetch('backend/employee_competency_action.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Success', data.message, 'success');
                    if (inlineModal) inlineModal.classList.remove("active");
                    fetchAssignedCompetencies(currentEmployeeId);
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(err => Swal.fire('Error', 'Failed to save competency', 'error'));
        });
    }

    window.deleteAssignment = (id) => {
        Swal.fire({
            title: 'Are you sure?',
            text: "This competency assessment will be removed.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append("action", "delete_competency");
                formData.append("id", id);

                fetch('backend/employee_competency_action.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Deleted!', data.message, 'success');
                        fetchAssignedCompetencies(currentEmployeeId);
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                })
                .catch(err => Swal.fire('Error', 'Failed to delete assignment', 'error'));
            }
        });
    };

    if (typeof lucide !== "undefined") lucide.createIcons();
});

// Sidebar Active Link Logic
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
document.addEventListener('DOMContentLoaded', () => {
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
            if (typeof Swal !== 'undefined') {
                const result = await Swal.fire({
                    title: 'Sign Out?',
                    text: 'You are about to sign out of your account.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    confirmButtonText: 'Yes, Sign Out',
                    cancelButtonText: 'Stay',
                    reverseButtons: true
                });
                if (result.isConfirmed) {
                    window.location.href = dest;
                }
            } else {
                if (confirm('Sign Out?')) window.location.href = dest;
            }
        });
    });
});

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

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initClock);
} else {
    initClock();
}
