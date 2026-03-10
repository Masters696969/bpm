document.addEventListener('DOMContentLoaded', () => {
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

    // 2. Sidebar & Mobile Logic
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener("click", () => {
            sidebar.classList.toggle("collapsed");
            localStorage.setItem("sidebarCollapsed", sidebar.classList.contains("collapsed"));
        });
        if (localStorage.getItem("sidebarCollapsed") === "true") sidebar.classList.add("collapsed");
    }

    if (mobileMenuBtn && sidebar) {
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

    // 4. Sidebar Active Link Logic
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
            if (submenu) {
                submenu.classList.add('active');
                submenu.style.maxHeight = '500px';
            }
            const btn = parentGroup.querySelector('.nav-item.has-submenu');
            if (btn) btn.classList.add('active');
        }
    } else {
        const navMatch = document.querySelector(`.sidebar a.nav-item[href$="${current}"]`);
        if (navMatch) navMatch.classList.add('active');
    }

    // 5. User Menu Dropdown Logic
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

    const userMenuBtn = document.getElementById('userMenuBtn');
    const userMenuDropdown = document.getElementById('userMenuDropdown');
    if (userMenuBtn && userMenuDropdown) {
        userMenuBtn.addEventListener('click', e => {
            e.stopPropagation();
            userMenuDropdown.classList.toggle('umd-open');
        });
        document.addEventListener('click', e => {
            if (!userMenuDropdown.contains(e.target) && e.target !== userMenuBtn) {
                userMenuDropdown.classList.remove('umd-open');
            }
        });
    }

    // 6. Real-time Clock Functionality
    const clockEl = document.getElementById('realTimeClock');
    if (clockEl) {
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

    // 7. Dispatch Logic
    const tableBody = document.getElementById('dispatchTableBody');
    const viewModal = document.getElementById('viewEmployeeModal');
    const modalEmployeeList = document.getElementById('modalEmployeeList');
    let currentPendingEmployees = [];

    async function initDispatchView() {
        if (!tableBody) return;
        try {
            const summaryRes = await fetch('be_dispatch.php?action=fetch_dispatcher_summary');
            const summary = await summaryRes.json();
            const employeeRes = await fetch('be_dispatch.php?action=fetch_new_hires');
            const employeeData = await employeeRes.json();
            if (summary.success && employeeData.success) {
                currentPendingEmployees = employeeData.data;
                renderDispatcherRow(summary.dispatcher);
            }
        } catch (error) { console.error('Initialization Error:', error); }
    }

    function renderDispatcherRow(dispatcher) {
        tableBody.innerHTML = '';
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
            <td>${new Date(dispatcher.date).toLocaleDateString()}</td>
            <td>${dispatcher.time}</td>
            <td>
                <span class="status-badge" style="background: rgba(44, 160, 120, 0.1); color: var(--brand-green);">
                    ${dispatcher.pending_count > 0 ? 'Pending Batch' : 'All Synced'}
                </span>
            </td>
            <td>
                <div style="display: flex; gap: 8px;">
                    <button class="btn-dispatch-single" onclick="openViewModal()">
                        <i data-lucide="eye" style="width: 14px;"></i> View
                    </button>
                    <button class="btn-dispatch-single" style="background: var(--brand-green); color: white; border: none;" onclick="dispatchAll()">
                        <i data-lucide="send" style="width: 14px;"></i> Dispatch
                    </button>
                </div>
            </td>
        `;
        tableBody.appendChild(tr);
        if (lucide) lucide.createIcons();
    }

    window.openViewModal = () => {
        modalEmployeeList.innerHTML = '';
        if (currentPendingEmployees.length === 0) {
            modalEmployeeList.innerHTML = '<tr><td colspan="4" style="text-align:center; padding: 20px;">No employees pending dispatch.</td></tr>';
        } else {
            currentPendingEmployees.forEach(emp => {
                const tr = document.createElement('tr');
                tr.innerHTML = `<td>${emp.FirstName} ${emp.LastName}</td><td>${emp.EmployeeCode || 'NEW'}</td><td>${emp.DepartmentName || 'N/A'}</td><td>${emp.PositionName || 'N/A'}</td>`;
                modalEmployeeList.appendChild(tr);
            });
        }
        viewModal.style.display = 'flex';
        if (lucide) lucide.createIcons();
    };

    window.closeViewModal = () => viewModal.style.display = 'none';

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
                const response = await fetch('be_dispatch.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'dispatch_all' })
                });
                const res = await response.json();
                if (res.success) {
                    await Swal.fire({ icon: 'success', title: 'Success', text: res.message, timer: 2000, showConfirmButton: false });
                    closeViewModal();
                    initDispatchView();
                } else { Swal.fire('Error', res.message, 'error'); }
            } catch (error) { Swal.fire('Error', 'Batch dispatch failed.', 'error'); }
        }
    };

    initDispatchView();
    if (lucide) lucide.createIcons();
});
