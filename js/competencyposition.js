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
            submenu.classList.toggle("active");
            item.classList.toggle("active");
        });
    });

    // 4. Grouped Management Logic
    const manageModal = document.getElementById('manageCompetenciesModal');
    const inlineModal = document.getElementById('inlineActionModal');
    const manageTableBody = document.getElementById('manageTableBody');
    const batchTableBody = document.getElementById('batchTableBody');
    const inlineForm = document.getElementById('inlineActionForm');
    let currentPositionId = null;
    let competencyLevels = []; 

    // Pagination State
    let manageData = [];
    let managePage = 1;
    let availableCompetencies = [];
    let batchPage = 1;
    const itemsPerPage = 7;
    let tempBatchAssignments = {}; // { compId: levelId }

    const toggleModal = (m, show) => {
        if (!m) return;
        m.classList.toggle("active", show);
        document.body.style.overflow = show ? "hidden" : "";
    };

    window.openManageModal = function(posId) {
        currentPositionId = posId;
        const formData = new FormData();
        formData.append('action', 'get_position_competencies');
        formData.append('position_id', posId);

        fetch('backend/position_action.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById('manageTitle').textContent = data.position_name;
                document.getElementById('manageSubTitle').textContent = data.department_name;
                
                manageData = data.data;
                managePage = 1;

                availableCompetencies = data.available_competencies;
                batchPage = 1;
                window._firstLoadBatch = true; // Mark that we just loaded new data
                
                // Initialize tempBatchAssignments from current mappings
                tempBatchAssignments = {};
                data.data.forEach(m => tempBatchAssignments[m.competency_id] = m.level_id);
                
                if (competencyLevels.length === 0) {
                    competencyLevels = [
                        {id: "1", name: "Basic", rank: "1"},
                        {id: "2", name: "Intermediate", rank: "2"},
                        {id: "3", name: "Advanced", rank: "3"},
                        {id: "4", name: "Expert", rank: "4"}
                    ];
                }

                renderManageTable();
                renderBatchTable();
                toggleModal(manageModal, true);
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        });
    };

    function updateMainTableRow(posId, newCount) {
        const row = document.getElementById(`row-pos-${posId}`);
        if (!row) return;

        const badge = row.querySelector('.count-badge-pos');
        if (!badge) return;

        const countLabel = newCount > 0 ? `${newCount} Competencies` : "No Mappings";
        const countColor = newCount > 0 ? 'var(--brand-green)' : '#94a3b8';
        const lucideIcon = newCount > 0 ? 'check-circle' : 'alert-circle';

        badge.style.background = `${countColor}10`;
        badge.style.color = countColor;
        badge.style.border = `1px solid ${countColor}20`;
        
        badge.innerHTML = `
            <i data-lucide="${lucideIcon}" style="width: 14px; height: 14px; margin-right: 6px;"></i>
            ${countLabel}
        `;
        
        if (window.lucide) window.lucide.createIcons();
    }

    function renderManageTable() {
        const start = (managePage - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const pageData = manageData.slice(start, end);

        manageTableBody.innerHTML = '';
        if (manageData.length === 0) {
            manageTableBody.innerHTML = '<tr><td colspan="3" style="text-align: center; color: var(--text-tertiary); padding: 30px;">No competencies assigned.</td></tr>';
            document.getElementById('managePagination').style.display = 'none';
            return;
        }

        document.getElementById('managePagination').style.display = manageData.length > itemsPerPage ? 'flex' : 'none';
        document.getElementById('managePageInfo').textContent = `Page ${managePage} of ${Math.ceil(manageData.length / itemsPerPage)}`;
        document.getElementById('prevManagePage').disabled = managePage === 1;
        document.getElementById('nextManagePage').disabled = managePage === Math.ceil(manageData.length / itemsPerPage);

        pageData.forEach(m => {
            const rank = parseInt(m.rank_level);
            let color = '#6b7280';
            if (rank === 1) color = '#94a3b8';
            else if (rank === 2) color = '#3498db';
            else if (rank === 3) color = '#f1c40f';
            else if (rank >= 4) color = '#e74c3c';

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><strong>${m.competency_name}</strong></td>
                <td style="text-align: center;">
                    <span class="rank-badge-pos" style="background: ${color}20; color: ${color};">
                        L${rank} - ${m.level_name}
                    </span>
                </td>
                <td style="text-align: center;">
                    <div class="pos-manage-actions">
                        <button class="action-btn-pos delete-btn-inline" title="Remove">
                            <i data-lucide="trash-2"></i>
                        </button>
                    </div>
                </td>
            `;
            tr.querySelector('.delete-btn-inline').addEventListener('click', () => deleteMapping(m.id));
            manageTableBody.appendChild(tr);
        });
        lucide.createIcons();
    }

    function renderBatchTable() {
        const start = (batchPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const pageData = availableCompetencies.slice(start, end);

        batchTableBody.innerHTML = '';
        if (availableCompetencies.length === 0) {
            batchTableBody.innerHTML = '<tr><td colspan="3" style="text-align: center; color: var(--text-tertiary); padding: 30px;">No available competencies found.</td></tr>';
            document.getElementById('batchPagination').style.display = 'none';
            return;
        }

        document.getElementById('batchPagination').style.display = availableCompetencies.length > itemsPerPage ? 'flex' : 'none';
        document.getElementById('batchPageInfo').textContent = `Page ${batchPage} of ${Math.ceil(availableCompetencies.length / itemsPerPage)}`;
        document.getElementById('prevBatchPage').disabled = batchPage === 1;
        document.getElementById('nextBatchPage').disabled = batchPage === Math.ceil(availableCompetencies.length / itemsPerPage);

        // Update Select All Checkbox based on ALL data
        const selectAll = document.getElementById('selectAllBatch');
        if (selectAll) {
            const allAssigned = availableCompetencies.every(c => !!tempBatchAssignments[c.id]);
            const someAssigned = availableCompetencies.some(c => !!tempBatchAssignments[c.id]);
            selectAll.checked = allAssigned;
            selectAll.indeterminate = someAssigned && !allAssigned;
        }

        pageData.forEach(c => {
            const isAssigned = !!tempBatchAssignments[c.id];
            const currentLevel = tempBatchAssignments[c.id] || "";
            const isDeptComp = c.comp_dept_id && c.comp_dept_id != 0;
            
            // Auto-check department competencies if they are not already in tempBatchAssignments
            // Only do this when first loading availableCompetencies
            if (isDeptComp && tempBatchAssignments[c.id] === undefined && window._firstLoadBatch) {
                tempBatchAssignments[c.id] = "1"; // Default to Basic
            }

            const tr = document.createElement('tr');
            if (isDeptComp) tr.classList.add('dept-specific-row');
            
            tr.innerHTML = `
                <td style="text-align: center;">
                    <input type="checkbox" class="batch-check row-check" data-comp-id="${c.id}" ${tempBatchAssignments[c.id] !== undefined ? 'checked' : ''}>
                </td>
                <td>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        ${c.name}
                        ${isDeptComp ? '<span style="font-size: 10px; background: rgba(52, 152, 219, 0.1); color: #3498db; padding: 2px 6px; border-radius: 4px; font-weight: 700;">DEPT</span>' : ''}
                    </div>
                </td>
                <td>
                    <select class="batch-level-select" data-comp-id="${c.id}" ${tempBatchAssignments[c.id] === undefined ? 'disabled' : ''}>
                        <option value="" disabled ${tempBatchAssignments[c.id] === undefined ? 'selected' : ''}>Choose Level...</option>
                        ${competencyLevels.map(l => `
                            <option value="${l.id}" ${tempBatchAssignments[c.id] == l.id ? 'selected' : ''}>L${l.rank} - ${l.name}</option>
                        `).join('')}
                    </select>
                </td>
            `;
            
            const check = tr.querySelector('.row-check');
            const select = tr.querySelector('.batch-level-select');
            
            check.addEventListener('change', () => {
                if (check.checked) {
                    tempBatchAssignments[c.id] = select.value || ""; 
                } else {
                    delete tempBatchAssignments[c.id];
                }
                select.disabled = !check.checked;
                updateSelectAllState();
            });

            select.addEventListener('change', () => {
                if (tempBatchAssignments[c.id] !== undefined) {
                    tempBatchAssignments[c.id] = select.value;
                }
            });
            
            batchTableBody.appendChild(tr);
        });
        window._firstLoadBatch = false; // Reset after first render
    }

    // Pagination Listeners
    document.getElementById('prevManagePage').addEventListener('click', () => { if (managePage > 1) { managePage--; renderManageTable(); } });
    document.getElementById('nextManagePage').addEventListener('click', () => { if (managePage < Math.ceil(manageData.length / itemsPerPage)) { managePage++; renderManageTable(); } });
    
    document.getElementById('prevBatchPage').addEventListener('click', () => { if (batchPage > 1) { batchPage--; renderBatchTable(); } });
    document.getElementById('nextBatchPage').addEventListener('click', () => { if (batchPage < Math.ceil(availableCompetencies.length / itemsPerPage)) { batchPage++; renderBatchTable(); } });

    // Handle Select All logic
    const selectAll = document.getElementById('selectAllBatch');
    if (selectAll) {
        selectAll.onclick = function() {
            availableCompetencies.forEach(c => {
                if (selectAll.checked) {
                    if (!tempBatchAssignments[c.id]) tempBatchAssignments[c.id] = "1"; // Default to Basic if none selected
                } else {
                    delete tempBatchAssignments[c.id];
                }
            });
            renderBatchTable();
        };
    }

    function updateSelectAllState() {
        const selectAll = document.getElementById('selectAllBatch');
        if (!selectAll) return;
        const allChecked = availableCompetencies.every(c => !!tempBatchAssignments[c.id]);
        const someChecked = availableCompetencies.some(c => !!tempBatchAssignments[c.id]);
        selectAll.checked = allChecked;
        selectAll.indeterminate = someChecked && !allChecked;
    }

    // Batch Assignment Logic
    const openBatchBtn = document.getElementById('openAddInlineBtn');
    if (openBatchBtn) {
        openBatchBtn.addEventListener('click', () => {
            document.getElementById('inline_pos_id').value = currentPositionId;
            toggleModal(inlineModal, true);
        });
    }

    if (inlineForm) {
        inlineForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const assignments = [];
            let valid = true;

            // Use tempBatchAssignments which tracks all selected items across pages
            for (const [compId, levelId] of Object.entries(tempBatchAssignments)) {
                if (!levelId) {
                    valid = false;
                    Swal.fire('Required', 'Please select a level for all checked competencies.', 'warning');
                    break;
                }
                assignments.push({
                    competency_id: compId,
                    level_id: levelId
                });
            }

            if (!valid) return;

            const formData = new FormData();
            formData.append('action', 'save_batch_assignments');
            formData.append('position_id', currentPositionId);
            assignments.forEach((a, index) => {
                formData.append(`assignments[${index}][competency_id]`, a.competency_id);
                formData.append(`assignments[${index}][level_id]`, a.level_id);
            });

            try {
                const res = await fetch('backend/position_action.php', { method: 'POST', body: formData });
                const data = await res.json();
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false,
                        target: 'body',
                        customClass: { container: 'swal-on-top' }
                    });
                    toggleModal(inlineModal, false);
                    openManageModal(currentPositionId);
                    if (data.new_count !== undefined) {
                        updateMainTableRow(currentPositionId, data.new_count);
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message,
                        target: 'body',
                        customClass: { container: 'swal-on-top' }
                    });
                }
            } catch (err) {
                console.error(err);
            }
        });
    }

    window.deleteMapping = function(id) {
        Swal.fire({
            title: 'Remove Mapping?',
            text: "This position will no longer require this competency.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74c3c',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, remove it',
            target: 'body',
            customClass: { container: 'swal-on-top' }
        }).then(async (result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('id', id);

                const res = await fetch('backend/position_action.php', { method: 'POST', body: formData });
                const data = await res.json();
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Removed!',
                        text: data.message,
                        timer: 1000,
                        showConfirmButton: false,
                        target: 'body',
                        customClass: { container: 'swal-on-top' }
                    });
                    openManageModal(currentPositionId);
                    if (data.new_count !== undefined) {
                        updateMainTableRow(currentPositionId, data.new_count);
                    }
                }
            }
        });
    };

    // Modal Closing
    document.getElementById('closeManageModal')?.addEventListener('click', () => toggleModal(manageModal, false));
    document.getElementById('closeInlineModal')?.addEventListener('click', () => toggleModal(inlineModal, false));
    document.getElementById('cancelInline')?.addEventListener('click', () => toggleModal(inlineModal, false));

    window.addEventListener('click', (e) => {
        if (e.target.classList.contains("modal-overlay-pos")) {
            toggleModal(e.target, false);
        }
    });

    if (typeof lucide !== 'undefined') lucide.createIcons();

    // Main Table Filtering Logic
    const posSearch = document.getElementById('positionSearch');
    const deptFilter = document.getElementById('deptFilter');
    const mainTableBody = document.querySelector('#mainMappingTable tbody');

    if (posSearch || deptFilter) {
        const handleFilter = () => {
            const searchTerm = posSearch?.value.toLowerCase() || "";
            const filterDept = deptFilter?.value || "";
            const rows = mainTableBody.querySelectorAll('tr[data-pos-name]');

            rows.forEach(row => {
                const posName = row.dataset.posName || "";
                const deptName = row.dataset.deptName || "";
                
                const matchesSearch = posName.includes(searchTerm);
                const matchesDept = !filterDept || deptName === filterDept;

                row.style.display = (matchesSearch && matchesDept) ? "" : "none";
            });
        };

        posSearch?.addEventListener('input', handleFilter);
        deptFilter?.addEventListener('change', handleFilter);
    }

    // 5. Automatic Modal Opening if target_pos_id is set
    if (window.target_pos_id) {
        window.openManageModal(window.target_pos_id);
    }
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
            if (submenu) submenu.classList.add('active');
            if (submenu) submenu.style.maxHeight = '500px';
            const btn = parentGroup.querySelector('.nav-item.has-submenu');
            if (btn) btn.classList.add('active');
        }
    } else {
        const navMatch = document.querySelector(`.sidebar a.nav-item[href$="${current}"]`);
        if (navMatch) navMatch.classList.add('active');
    }
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
            if (dd && !dd.contains(e.target) && e.target !== btn) {
                dd.classList.remove('umd-open');
            }
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
            if (result.isConfirmed) window.location.href = dest;
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
        const clockStr = `${days[now.getDay()]}, ${months[now.getMonth()]} ${now.getDate()}, ${now.getFullYear()}, ${now.getHours().toString().padStart(2, '0')}:${now.getMinutes().toString().padStart(2, '0')}:${now.getSeconds().toString().padStart(2, '0')}`;
        clockEl.textContent = clockStr;
    };

    setInterval(updateClock, 1000);
    updateClock();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initClock);
} else {
    initClock();
}
