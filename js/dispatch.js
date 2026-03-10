document.addEventListener('DOMContentLoaded', () => {
    const tableBody = document.getElementById('dispatchTableBody');
    const viewModal = document.getElementById('viewEmployeeModal');
    const modalEmployeeList = document.getElementById('modalEmployeeList');

    let currentPendingEmployees = [];

    // 1. Fetch Dispatcher Summary and Pending Employees
    async function initDispatchView() {
        if (!tableBody) return;

        try {
            // First fetch the dispatcher info
            const summaryRes = await fetch('be_dispatch.php?action=fetch_dispatcher_summary');
            const summary = await summaryRes.json();

            // Then fetch the actual employee list
            const employeeRes = await fetch('be_dispatch.php?action=fetch_new_hires');
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
                const response = await fetch('be_dispatch.php', {
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
