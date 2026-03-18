document.addEventListener("DOMContentLoaded", () => {
    // 1. Tab Switching Logic
    const tabButtons = document.querySelectorAll(".tab-btn");
    const tabPanels = document.querySelectorAll(".tab-panel");

    const apiUrl = 'backend/be_payroll.php';
    const batchesBody = document.getElementById('payrollBatchesBody');
    const employeesBody = document.getElementById('payrollEmployeesBody');
    let selectedBatchId = null;

    const peso = (n) => {
        const num = Number(n || 0);
        return `₱${num.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    };

    const moneySpan = (value, kind) => {
        const v = Number(value || 0);
        const cls = kind === 'deduction' ? 'money-deduction' : (kind === 'earning' ? 'money-earning' : '');
        return `<span class="${cls}">${peso(v)}</span>`;
    };

    const fetchJson = async (url, options = {}) => {
        const res = await fetch(url, options);
        const data = await res.json().catch(() => ({}));
        if (!res.ok || !data.ok) {
            const msg = data.message || data.error || `Request failed (${res.status})`;
            throw new Error(msg);
        }
        return data;
    };

    let totalPayrollAmount = 0;
    let isPayrollVisible = false;

    const renderTotalPayroll = () => {
        const elTotal = document.getElementById('statTotalPayroll');
        if (!elTotal) return;
        if (isPayrollVisible) {
            elTotal.textContent = peso(totalPayrollAmount);
        } else {
            elTotal.innerHTML = '&#8369;**,***.**';
        }
    };

    const loadStats = async () => {
        try {
            const data = await fetchJson(`${apiUrl}?action=stats`);
            const elEmp = document.getElementById('statEmployees');
            const elNext = document.getElementById('statNextRun');
            const elPend = document.getElementById('statPending');

            totalPayrollAmount = Number(data.total_payroll || 0);
            renderTotalPayroll();

            if (elEmp) elEmp.textContent = `${data.employees || 0} Paid`;
            if (elNext) elNext.textContent = data.next_run || '--';
            if (elPend) elPend.textContent = `${data.pending || 0} Batches`;
        } catch (e) {
            console.error('Failed to load stats:', e);
        }
    };

    // Toggle logic for Total Payroll
    const togglePayrollBtn = document.getElementById('togglePayrollBtn');
    if (togglePayrollBtn) {
        togglePayrollBtn.addEventListener('click', () => {
            isPayrollVisible = !isPayrollVisible;
            renderTotalPayroll();
            
            // Swap icon
            togglePayrollBtn.innerHTML = isPayrollVisible 
                ? '<i data-lucide="eye" style="width:16px; height:16px; color:var(--brand-green);"></i>'
                : '<i data-lucide="eye-off" style="width:16px; height:16px;"></i>';
            if (window.lucide) window.lucide.createIcons();
        });
    }

    const statusBadge = (status) => {
        const s = String(status || '').toLowerCase();
        if (s.includes('disbursed') || s.includes('completed')) {
            return `<span class="badge-premium badge-success"><i data-lucide="check-check" style="width:14px;"></i> ${status}</span>`;
        }
        if (s.includes('approved') || s.includes('finalized')) {
            return `<span class="badge-premium badge-info" style="background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.2); color: #3b82f6;"><i data-lucide="star" style="width:14px;"></i> ${status}</span>`;
        }
        if (s.includes('pending')) {
            return `<span class="badge-premium badge-warning"><i data-lucide="clock" style="width:14px;"></i> ${status}</span>`;
        }
        if (s.includes('archived')) {
            return `<span class="badge-premium badge-secondary"><i data-lucide="archive" style="width:14px;"></i> ${status}</span>`;
        }
        // Processing or default
        return `<span class="badge-premium badge-info"><i data-lucide="loader" class="spin" style="width:14px;"></i> ${status || 'Processing'}</span>`;
    };

    const renderBatches = (batches) => {
        if (!batchesBody) return;
        batchesBody.innerHTML = '';

        if (!batches || batches.length === 0) {
            batchesBody.innerHTML = `<tr><td colspan="6" style="padding:16px 24px; color: var(--text-secondary);">No payroll batches yet.</td></tr>`;
            return;
        }

        batches.forEach(b => {
            const tr = document.createElement('tr');
            const period = `${b.period_start} - ${b.period_end}`;
            const total = peso(b.total_distributed);

            const canFinalize = String(b.status).toLowerCase() === 'processing' || b.status === '' || b.status === null;

            tr.innerHTML = `
                <td><strong>${b.batch_code}</strong></td>
                <td>${period}</td>
                <td>${b.pay_type}</td>
                <td>${total}</td>
                <td>${statusBadge(b.status)}</td>
                <td>
                    <button class="btn-premium btn-view-batch" data-batch-id="${b.id}" style="background: var(--surface-hover); padding: 6px 12px; border: 1px solid var(--border-color);">View</button>
                    ${canFinalize ? `<button class="btn-premium btn-finalize-batch" data-batch-id="${b.id}" style="background: var(--brand-green); color: white; padding: 6px 12px; margin-left: 8px;">Finalize</button>` : ''}
                </td>
            `;
            batchesBody.appendChild(tr);
        });

        if (window.lucide) window.lucide.createIcons();
    };

    const renderEmployees = (rows) => {
        if (!employeesBody) return;
        employeesBody.innerHTML = '';

        if (!rows || rows.length === 0) {
            employeesBody.innerHTML = `<tr><td colspan="7" style="padding:16px 24px; color: var(--text-secondary);">Select a batch to view employee payroll.</td></tr>`;
            return;
        }

        rows.forEach(r => {
            const name = `${r.FirstName} ${r.LastName}`;
            const initials = `${(r.FirstName || ' ')[0]}${(r.LastName || ' ')[0]}`.toUpperCase();
            const code = r.EmployeeCode || `EMP-${r.employee_id}`;

            // Net pay match indicator
            let netPayHtml = `<strong>${peso(r.net_pay)}</strong>`;
            if (r.expected_monthly_net !== null && r.expected_monthly_net !== undefined) {
                const expectedSemiMonthly = Number(r.expected_monthly_net) / 2;
                const diff = Math.abs(Number(r.net_pay) - expectedSemiMonthly);
                const isMatch = diff < 1; // within 1 peso tolerance
                const indicator = isMatch
                    ? '<span style="color:var(--brand-green);margin-left:6px;" title="Matches expected semi-monthly net">✓</span>'
                    : `<span style="color:var(--brand-yellow);margin-left:6px;" title="Diff: ${peso(diff.toFixed(2))} vs expected">⚠</span>`;
                netPayHtml = `<strong>${peso(r.net_pay)}</strong>${indicator}`;
            }

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>
                    <div class="user-identity">
                        <div class="avatar-initials">${initials}</div>
                        <div class="user-metadata">
                            <span class="user-main-name">${name}</span>
                            <span class="user-sub-info">${code}</span>
                        </div>
                    </div>
                </td>
                <td>${moneySpan(r.basic_pay, 'earning')}</td>
                <td>${moneySpan(r.allowances_total, 'earning')}</td>
                <td>${moneySpan(r.overtime_pay, 'earning')}</td>
                <td>${moneySpan(r.sss_regular_ee, 'deduction')}</td>
                <td>${moneySpan(r.sss_wisp_ee, 'deduction')}</td>
                <td>${moneySpan(r.philhealth_ee, 'deduction')}</td>
                <td>${moneySpan(r.pagibig_ee, 'deduction')}</td>
                <td>${moneySpan(r.late_undertime, 'deduction')}</td>
                <td>${moneySpan(r.withholding_tax, 'deduction')}</td>
                <td>${moneySpan(r.deductions_total, 'deduction')}</td>
                <td>${netPayHtml}</td>
                <td><span class="badge-premium badge-success font-size-xs">${r.status || 'Computed'}</span></td>
                <td><i class="payroll-payslip" data-item-id="${r.item_id}" data-lucide="file-text" style="color: var(--brand-green); cursor: pointer;"></i></td>
            `;
            employeesBody.appendChild(tr);
        });

        if (window.lucide) window.lucide.createIcons();
    };

    let selectedBatchTotal = 0;

    const updateBudgetCard = (budget) => {
        const card = document.getElementById('payrollBudgetCard');
        const statusEl = document.getElementById('payrollBudgetStatus');
        const valueEl = document.getElementById('statDisbursementBudget');
        const refEl = document.getElementById('payrollBudgetRef');
        const btn = document.getElementById('requestPayrollBudgetBtn');

        if (!card || !selectedBatchId) {
            if (card) card.style.display = 'none';
            return;
        }

        card.style.display = 'block';
        
        if (!budget || !budget.status) {
            statusEl.className = 'sync-status-tag';
            statusEl.innerHTML = '<i data-lucide="minus-circle" style="width:12px;"></i> UNSYNCED';
            valueEl.textContent = peso(selectedBatchTotal);
            refEl.textContent = '';
            btn.innerHTML = '<i data-lucide="send" style="width:14px;"></i> Request Finance';
            btn.disabled = false;
        } else {
            const s = budget.status.toLowerCase();
            if (s === 'pending') {
                statusEl.className = 'sync-status-tag sync-pending';
                statusEl.innerHTML = '<i data-lucide="clock" class="spin" style="width:12px;"></i> PENDING FINANCE';
                btn.innerHTML = '<i data-lucide="edit" style="width:14px;"></i> Update Request';
                btn.disabled = false;
            } else if (s === 'approved' || s === 'final' || s === 'done') {
                statusEl.className = 'sync-status-tag sync-success';
                statusEl.innerHTML = '<i data-lucide="check-circle" style="width:12px;"></i> APPROVED';
                
                // Change button to Disburse if not already disbursed
                btn.innerHTML = '<i data-lucide="banknote" style="width:14px;"></i> Disburse Payroll';
                btn.classList.add('btn-disburse-ready');
                btn.disabled = false;
                btn.style.background = 'var(--brand-green)';
            } else if (s === 'completed') {
                statusEl.className = 'sync-status-tag sync-success';
                statusEl.innerHTML = '<i data-lucide="check-check" style="width:12px;"></i> DISBURSED';
                btn.innerHTML = '<i data-lucide="lock" style="width:14px;"></i> Payout Completed';
                btn.disabled = true;
                btn.style.opacity = '0.6';
            } else if (s === 'rejected' || s === 'denied') {
                statusEl.className = 'sync-status-tag sync-error';
                statusEl.innerHTML = '<i data-lucide="x-circle" style="width:12px;"></i> REJECTED';
                btn.innerHTML = '<i data-lucide="rotate-ccw" style="width:14px;"></i> Re-Request';
                btn.disabled = false;
            }
            valueEl.textContent = peso(budget.approved > 0 ? budget.approved : budget.requested);
            refEl.textContent = budget.ref || '';
        }
        
        if (window.lucide) window.lucide.createIcons();
    };

    const loadBatches = async () => {
        const data = await fetchJson(`${apiUrl}?action=list_batches`);
        renderBatches(data.batches);
        return data.batches;
    };

    const loadEmployees = async (batchId) => {
        const data = await fetchJson(`${apiUrl}?action=list_employees&batch_id=${encodeURIComponent(batchId)}`);
        
        // Calculate total distributed from employees if not already known
        selectedBatchTotal = data.employees.reduce((acc, emp) => acc + Number(emp.net_pay), 0);
        
        renderEmployees(data.employees);
        updateBudgetCard(data.batch_budget);
        return data.employees;
    };

    tabButtons.forEach(btn => {
        btn.addEventListener("click", () => {
            const target = btn.getAttribute("data-tab");

            // Update buttons
            tabButtons.forEach(b => b.classList.remove("active"));
            btn.classList.add("active");

            // Update panels
            tabPanels.forEach(p => p.classList.remove("active"));
            const targetPanel = document.getElementById(target);
            if (targetPanel) targetPanel.classList.add("active");

            // Re-create icons for new content
            if (window.lucide) window.lucide.createIcons();
        });
    });

    // 2. Search Logic (Simple Filter)
    const searchInput = document.querySelector('input[type="search"]');
    if (searchInput) {
        searchInput.addEventListener("input", (e) => {
            const term = e.target.value.toLowerCase();
            const rows = document.querySelectorAll(".payroll-table tbody tr");

            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(term) ? "" : "none";
            });
        });
    }

    // 3. New Payroll Run Modal — Dynamically loads periods from timesheet_period
    const runPayrollBtn = document.getElementById("runPayrollBtn");
    if (runPayrollBtn && window.Swal) {
        runPayrollBtn.addEventListener("click", async () => {
            // First, load available periods from the API
            let periods = [];
            try {
                const periodsData = await fetchJson(`${apiUrl}?action=list_periods`);
                periods = periodsData.periods || [];
            } catch (e) {
                Swal.fire({
                    title: 'Could Not Load Periods',
                    text: 'Unable to fetch timesheet periods. Please try again.',
                    icon: 'error',
                    confirmButtonColor: '#ef4444'
                });
                return;
            }

            if (periods.length === 0) {
                Swal.fire({
                    title: 'No Timesheet Periods Available',
                    html: `<p style="color:var(--bs-body-color,#374151);font-size:14px;line-height:1.7;">
                        There are no APPROVED or FINALIZED timesheet periods with employee data.<br><br>
                        Please go to <strong>Timesheet Data</strong> to review periods and ensure they are approved before running payroll.
                    </p>`,
                    icon: 'warning',
                    confirmButtonColor: '#2ca078',
                    confirmButtonText: 'Go to Timesheet Data',
                    showCancelButton: true,
                    cancelButtonText: 'Cancel'
                }).then(r => {
                    if (r.isConfirmed) window.location.href = 'timesheetdata.php';
                });
                return;
            }

            // Build inputOptions from real periods
            const inputOptions = {};
            periods.forEach(p => {
                const from = new Date(p.StartDate).toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                const to   = new Date(p.EndDate).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                const dept = p.DepartmentName ? ` — ${p.DepartmentName}` : '';
                const emps = `${p.EmpCount} emp${p.EmpCount != 1 ? 's' : ''}`;
                inputOptions[p.PeriodID] = `${from} – ${to}${dept} (${emps}) [${p.Status}]`;
            });

            Swal.fire({
                title: 'Initialize Payroll Processing',
                text: 'Select the timesheet period for this payroll run.',
                input: 'select',
                inputOptions,
                inputPlaceholder: 'Select a period',
                showCancelButton: true,
                confirmButtonColor: '#2ca078',
                confirmButtonText: 'Initialize Processing',
                background: document.body.classList.contains('dark-mode') ? '#1a1a1a' : '#fff',
                color: document.body.classList.contains('dark-mode') ? '#f9fafb' : '#111827',
                inputValidator: (value) => {
                    if (!value) return 'Please select a period.';
                }
            }).then((result) => {
                if (!result.isConfirmed) return;
                const periodId = result.value; // this is the PeriodID from timesheet_period

                Swal.fire({
                    title: 'Processing...',
                    text: 'Creating payroll batch and computing payroll.',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading(),
                    background: document.body.classList.contains('dark-mode') ? '#1a1a1a' : '#fff',
                    color: document.body.classList.contains('dark-mode') ? '#f9fafb' : '#111827'
                });

                fetchJson(apiUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({ action: 'create_batch', period_id: periodId }).toString()
                }).then(async (data) => {
                    await loadBatches();
                    selectedBatchId = data.batch_id;
                    await loadEmployees(selectedBatchId);
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Batch Created!',
                        text: `Payroll batch ${data.batch_code} created.`,
                        showConfirmButton: false,
                        timer: 2500,
                        timerProgressBar: true
                    });

                    const employeesTab = document.querySelector('.tab-btn[data-tab="employees"]');
                    if (employeesTab) employeesTab.click();
                }).catch(err => {
                    Swal.fire({
                        title: 'Failed',
                        text: err.message || 'Unable to create batch.',
                        icon: 'error',
                        confirmButtonColor: '#ef4444'
                    });
                });
            });
        });
    }


    // Batch table actions
    document.addEventListener('click', async (e) => {
        const payslipIcon = e.target.closest('.payroll-payslip');
        if (payslipIcon) {
            const itemId = payslipIcon.getAttribute('data-item-id');
            if (itemId) {
                window.open(`payslip.php?item_id=${encodeURIComponent(itemId)}`, '_blank');
            }
            return;
        }

        const viewBtn = e.target.closest('.btn-view-batch');
        if (viewBtn) {
            const id = viewBtn.getAttribute('data-batch-id');
            selectedBatchId = id;
            try {
                await loadEmployees(id);
                const employeesTab = document.querySelector('.tab-btn[data-tab="employees"]');
                if (employeesTab) employeesTab.click();
            } catch (err) {
                if (window.Swal) {
                    Swal.fire({ title: 'Failed', text: err.message, icon: 'error', confirmButtonColor: '#ef4444' });
                }
            }
            return;
        }

        const finBtn = e.target.closest('.btn-finalize-batch');
        if (finBtn) {
            const id = finBtn.getAttribute('data-batch-id');
            if (!window.Swal) return;
            const confirm = await Swal.fire({
                title: 'Finalize batch?',
                text: 'This will lock the batch as Finalized.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2ca078',
                cancelButtonColor: '#6b7280'
            });
            if (!confirm.isConfirmed) return;
            try {
                await fetchJson(apiUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({ action: 'finalize_batch', batch_id: id }).toString()
                });
                await loadBatches();
                if (selectedBatchId === id) {
                    await loadEmployees(id);
                }
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Batch Finalized',
                    text: 'Batch finalized successfully.',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });
            } catch (err) {
                Swal.fire({ title: 'Failed', text: err.message, icon: 'error', confirmButtonColor: '#ef4444' });
            }
        }
    });

    // 3.5 Payroll Budget Request & Disbursement Logic
    const requestBtn = document.getElementById("requestPayrollBudgetBtn");
    if (requestBtn && window.Swal) {
        requestBtn.addEventListener("click", async () => {
            if (!selectedBatchId) return;

            // BRANCH: If budget is already Approved, perform DISBURSEMENT
            if (requestBtn.classList.contains('btn-disburse-ready')) {
                const confirm = await Swal.fire({
                    title: 'Disburse Payroll?',
                    text: `Are you ready to finalize the payout of ${peso(selectedBatchTotal)} for this batch?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#2ca078',
                    confirmButtonText: 'Yes, Disburse Now',
                    background: document.body.classList.contains('dark-mode') ? '#1a1a1a' : '#fff',
                    color: document.body.classList.contains('dark-mode') ? '#f9fafb' : '#111827'
                });

                if (confirm.isConfirmed) {
                    Swal.fire({ title: 'Disbursing...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                    try {
                        await fetchJson('backend/be_payroll.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: new URLSearchParams({ action: 'disburse_batch', batch_id: selectedBatchId }).toString()
                        });
                        Swal.fire({ icon: 'success', title: 'Payaout Success', text: 'Batch has been marked as Disbursed.', timer: 2000 });
                        await loadBatches();
                        await loadEmployees(selectedBatchId);
                    } catch (err) {
                        Swal.fire({ title: 'Failed', text: err.message, icon: 'error' });
                    }
                }
                return;
            }

            // BRANCH: Initial Budget Request
            const { value: amount } = await Swal.fire({
                title: 'Request Payroll Budget',
                text: 'Enter the total budget amount needed for this disbursement.',
                input: 'number',
                inputValue: selectedBatchTotal,
                inputAttributes: { step: '0.01' },
                showCancelButton: true,
                confirmButtonColor: '#2ca078',
                confirmButtonText: 'Send Request',
                background: document.body.classList.contains('dark-mode') ? '#1a1a1a' : '#fff',
                color: document.body.classList.contains('dark-mode') ? '#f9fafb' : '#111827'
            });

            if (amount) {
                Swal.fire({
                    title: 'Sending Request...',
                    text: 'Connecting to Finance Laptop...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading(),
                    background: document.body.classList.contains('dark-mode') ? '#1a1a1a' : '#fff',
                    color: document.body.classList.contains('dark-mode') ? '#f9fafb' : '#111827'
                });

                try {
                    const res = await fetchJson('backend/be_request_payroll_budget.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: new URLSearchParams({ batch_id: selectedBatchId, amount: amount }).toString()
                    });

                    Swal.fire({
                        icon: 'success',
                        title: 'Request Sent',
                        text: res.message,
                        confirmButtonColor: '#2ca078'
                    });

                    // Refresh budget card
                    await loadEmployees(selectedBatchId);
                } catch (err) {
                    Swal.fire({
                        title: 'Sync Failed',
                        text: err.message,
                        icon: 'error',
                        confirmButtonColor: '#ef4444'
                    });
                }
            }
        });
    }

    // 4. Sidebar Toggle Logic (Core to all dashboards)
    const body = document.body;
    const themeToggle = document.getElementById("themeToggle");
    const sidebarToggle = document.getElementById("sidebarToggle");
    const sidebar = document.getElementById("sidebar");
    const mobileMenuBtn = document.getElementById("mobileMenuBtn");

    if (themeToggle) {
        themeToggle.addEventListener("click", () => {
            body.classList.toggle("dark-mode");
            localStorage.setItem("theme", body.classList.contains("dark-mode") ? "dark" : "light");
        });
    }

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

    // Submenu Logic
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

    if (window.lucide) window.lucide.createIcons();

    // Initial data
    loadStats().catch(() => { });
    if (batchesBody) {
        loadBatches().catch(() => { });
    }
    if (employeesBody) {
        renderEmployees([]);
    }
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

// User Menu Dropdown Logic (Merged)
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
            const result = await Swal.fire({
                title: 'Sign Out?',
                text: 'You are about to sign out of your account.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '<i class="swal-icon-logout"></i> Yes, Sign Out',
                cancelButtonText: 'Stay',
                reverseButtons: true,
                customClass: {
                    popup: 'swal-signout-popup',
                    title: 'swal-signout-title',
                }
            });
            if (result.isConfirmed) {
                await Swal.fire({
                    icon: 'success',
                    title: 'Signed Out',
                    text: 'You have been signed out successfully.',
                    timer: 1500,
                    showConfirmButton: false,
                });
                window.location.href = dest;
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
