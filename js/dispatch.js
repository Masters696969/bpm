document.addEventListener('DOMContentLoaded', () => {
    const tableBody = document.getElementById('dispatchTableBody');
    const viewModal = document.getElementById('viewEmployeeModal');
    const modalEmployeeList = document.getElementById('modalEmployeeList');
    const body = document.body;
    const themeToggle = document.getElementById("themeToggle");
    const sidebarToggle = document.getElementById("sidebarToggle");
    const sidebar = document.getElementById("sidebar");
    const mobileMenuBtn = document.getElementById("mobileMenuBtn");

    let currentPendingEmployees = [];

    // Theme Logic
    const savedTheme = localStorage.getItem("theme");
    if (savedTheme === "dark") body.classList.add("dark-mode");

    themeToggle.addEventListener("click", () => {
        body.classList.toggle("dark-mode");
        localStorage.setItem("theme", body.classList.contains("dark-mode") ? "dark" : "light");
    });

    // Sidebar & Mobile Logic
    sidebarToggle.addEventListener("click", () => {
        sidebar.classList.toggle("collapsed");
        localStorage.setItem("sidebarCollapsed", sidebar.classList.contains("collapsed"));
    });

    if (localStorage.getItem("sidebarCollapsed") === "true") sidebar.classList.add("collapsed");

    mobileMenuBtn.addEventListener("click", () => sidebar.classList.toggle("mobile-open"));

    // Submenu Logic
    document.querySelectorAll(".nav-item.has-submenu").forEach((item) => {
        item.addEventListener("click", (e) => {
            const module = item.getAttribute("data-module");
            const submenu = document.getElementById(`submenu-${module}`);
            submenu.classList.toggle("active");
            item.classList.toggle("active");
        });
    });

    // Active Page Highlighting
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

    // 1. Fetch Dispatcher Summary and Pending Employees
    async function initDispatchView() {
        if (!tableBody) return;

        try {
            // First fetch the dispatcher info
            const summaryRes = await fetch('backend/be_dispatch.php?action=fetch_dispatcher_summary');
            const summary = await summaryRes.json();

            // Then fetch the actual employee list
            const employeeRes = await fetch('backend/be_dispatch.php?action=fetch_new_hires');
            const employeeData = await employeeRes.json();

            if (summary.success && employeeData.success) {
                currentPendingEmployees = employeeData.data;
                renderDispatcherRow(summary.dispatcher);
            } else {
                Swal.fire('Error', summary.message || employeeData.message || 'Failed to load dispatch data', 'error');
            }
        } catch (error) {
            console.error('Initialization Error:', error);
            Swal.fire('Error', 'Connection failed while loading dispatch data', 'error');
        }
    }

    // 2. Render Single Dispatcher Row
    function renderDispatcherRow(dispatcher) {
        tableBody.innerHTML = '';
        const date = new Date(dispatcher.date).toLocaleDateString();
        const time = dispatcher.time;

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>
                <div class="emp-info">
                    <div class="emp-avatar" style="background: rgba(44, 160, 120, 0.1); color: var(--brand-green);">
                        ${dispatcher.name.charAt(0).toUpperCase()}
                    </div>
                    <div class="emp-details">
                        <div style="font-weight: 600; color: var(--text-primary);">${dispatcher.name}</div>
                        <div style="font-size: 12px; color: var(--text-secondary);">Current Dispatcher</div>
                    </div>
                </div>
            </td>
            <td>${dispatcher.position}</td>
            <td>${date}</td>
            <td>${time}</td>
            <td>
                <span class="status-badge" style="background: rgba(44, 160, 120, 0.1); color: var(--brand-green);">
                    ${dispatcher.pending_count > 0 ? 'Pending Batch' : 'All Synced'}
                </span>
            </td>
            <td>
                <div style="display: flex; gap: 8px;">
                    <button class="btn-dispatch-single" onclick="openViewModal()">
                        <i data-lucide="eye" style="width: 14px;"></i>
                        View
                    </button>
                    <button class="btn-dispatch-single" style="background: var(--brand-green); color: white; border: none;" onclick="dispatchAll()">
                        <i data-lucide="send" style="width: 14px;"></i>
                        Dispatch
                    </button>
                </div>
            </td>
        `;
        tableBody.appendChild(tr);
        if (window.lucide) window.lucide.createIcons();
    }

    // 3. Modal Actions
    window.openViewModal = () => {
        modalEmployeeList.innerHTML = '';

        if (currentPendingEmployees.length === 0) {
            modalEmployeeList.innerHTML = '<tr><td colspan="4" style="text-align:center; padding: 20px;">No employees pending dispatch.</td></tr>';
        } else {
            currentPendingEmployees.forEach(emp => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${emp.FirstName} ${emp.LastName}</td>
                    <td>${emp.EmployeeCode || 'NEW'}</td>
                    <td>${emp.DepartmentName || 'N/A'}</td>
                    <td>${emp.PositionName || 'N/A'}</td>
                `;
                modalEmployeeList.appendChild(tr);
            });
        }

        viewModal.style.display = 'flex';
        if (window.lucide) window.lucide.createIcons();
    };

    window.closeViewModal = () => {
        viewModal.style.display = 'none';
    };

    // 4. Batch Dispatch Action
    window.dispatchAll = async () => {
        if (currentPendingEmployees.length === 0) {
            Swal.fire('Notice', 'No employees to dispatch.', 'info');
            return;
        }

        const confirm = await Swal.fire({
            title: 'Confirm Batch Dispatch',
            text: `Send ${currentPendingEmployees.length} employee records to Intake?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#2ca078',
            confirmButtonText: 'Yes, Dispatch All'
        });

        if (confirm.isConfirmed) {
            Swal.fire({ title: 'Dispatching...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });

            try {
                const response = await fetch('backend/be_dispatch.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'dispatch_all' })
                });
                const res = await response.json();

                if (res.success) {
                    await Swal.fire({ icon: 'success', title: 'Success', text: res.message, timer: 2000, showConfirmButton: false });
                    closeViewModal();
                    initDispatchView(); // Refresh table
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            } catch (error) {
                console.error('Dispatch All Error:', error);
                Swal.fire('Error', 'Batch dispatch failed.', 'error');
            }
        }
    };

    // Initial load
    initDispatchView();
});
// Redundant UI logic removed (none was found, but ensuring cleanliness)
