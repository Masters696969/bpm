document.addEventListener("DOMContentLoaded", () => {
    const lucide = window.lucide;
    const tabButtons = document.querySelectorAll(".tab-btn");
    const tabPanels = document.querySelectorAll(".tab-panel");
    const apiUrl = 'disbursement_action.php';
    const batchesBody = document.getElementById('disbursementBatchesBody');
    const historyBody = document.getElementById('disbursementHistoryBody');
    let currentFilter = 'ready';

    // 1. Theme Logic
    const savedTheme = localStorage.getItem("theme");
    if (savedTheme === "dark") document.body.classList.add("dark-mode");

    const themeToggle = document.getElementById("themeToggle");
    if (themeToggle) {
        themeToggle.addEventListener("click", () => {
            document.body.classList.toggle("dark-mode");
            localStorage.setItem("theme", document.body.classList.contains("dark-mode") ? "dark" : "light");
        });
    }

    // 2. Real-time Clock
    const initClock = () => {
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
    };
    initClock();

    const peso = (n) => {
        const num = Number(n || 0);
        return `₱${num.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    };

    const fetchJson = async (url, options = {}) => {
        const res = await fetch(url, options);
        const data = await res.json().catch(() => ({}));
        if (!res.ok || !data.ok) {
            throw new Error(data.error || `Request failed (${res.status})`);
        }
        return data;
    };

    const loadStats = async () => {
        try {
            const data = await fetchJson(`${apiUrl}?action=stats`);
            if (document.getElementById('statPendingDisbursement')) {
                document.getElementById('statPendingDisbursement').textContent = peso(data.pending_disbursement);
            }
            if (document.getElementById('statTotalDisbursed')) {
                document.getElementById('statTotalDisbursed').textContent = peso(data.total_disbursed);
            }
            if (document.getElementById('statPendingCount')) {
                document.getElementById('statPendingCount').textContent = `${data.pending_count || 0} Batches`;
            }
        } catch (e) {
            console.error('Failed to load stats:', e);
        }
    };

    const statusBadge = (status) => {
        const s = String(status || '').toLowerCase();
        if (s === 'disbursed') {
            return `<span class="badge-premium badge-success"><i data-lucide="check-check"></i> Disbursed</span>`;
        }
        if (s === 'archived') {
            return `<span class="badge-premium badge-secondary" style="background: rgba(107, 114, 128, 0.1); color: #6b7280;"><i data-lucide="archive"></i> Archived</span>`;
        }
        if (s === 'approved') {
            return `<span class="badge-premium badge-warning" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;"><i data-lucide="clock"></i> Approved</span>`;
        }
        return `<span class="badge-premium badge-secondary">${status}</span>`;
    };

    const renderBatches = (batches, targetBody) => {
        if (!targetBody) return;
        targetBody.innerHTML = '';

        if (!batches || batches.length === 0) {
            targetBody.innerHTML = `<tr><td colspan="6" style="padding:16px 24px; color: var(--text-secondary);">No batches found.</td></tr>`;
            return;
        }

        batches.forEach(b => {
            const tr = document.createElement('tr');
            const period = `${b.period_start} - ${b.period_end}`;
            const total = peso(b.total_distributed);

            const isReady = b.status.toLowerCase() === 'approved';
            const isHistory = ['disbursed', 'archived'].includes(b.status.toLowerCase());

            tr.innerHTML = `
                <td><strong>${b.batch_code}</strong></td>
                <td>${period}</td>
                <td>${b.pay_type}</td>
                <td>${total}</td>
                <td>${statusBadge(b.status)}</td>
                <td>
                    <button class="btn-premium btn-view-batch" data-batch-id="${b.id}" style="background: var(--surface-hover); padding: 6px 12px; border: 1px solid var(--border-color);">View</button>
                    ${isReady ? `
                        <button class="btn-premium btn-approve-disbursement" data-batch-id="${b.id}" style="background: var(--brand-green); color: white; padding: 6px 12px; margin-left: 8px;">Approve</button>
                        <button class="btn-premium btn-archive-batch" data-batch-id="${b.id}" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6; padding: 6px 12px; border: 1px solid #3b82f6; margin-left: 8px;">Archive</button>
                    ` : ''}
                    ${isHistory && b.status.toLowerCase() === 'disbursed' ? `<button class="btn-premium btn-view-proof" data-batch-id="${b.id}" data-batch-code="${b.batch_code}" data-amount="${total}" style="background: rgba(44, 160, 120, 0.1); color: var(--brand-green); padding: 6px 12px; border: 1px solid var(--brand-green); margin-left: 8px;">Proof</button>` : ''}
                </td>
            `;
            targetBody.appendChild(tr);
        });

        if (window.lucide) window.lucide.createIcons();
    };

    const loadBatches = async (filter = 'ready') => {
        try {
            const data = await fetchJson(`${apiUrl}?action=list_batches&filter=${filter}`);
            const body = (filter === 'ready') ? batchesBody : historyBody;
            renderBatches(data.batches, body);
        } catch (err) {
            console.error('Failed to load batches:', err);
        }
    };

    tabButtons.forEach(btn => {
        btn.addEventListener("click", () => {
            const target = btn.getAttribute("data-tab");
            currentFilter = (target === 'ready') ? 'ready' : 'history';

            tabButtons.forEach(b => b.classList.remove("active"));
            btn.classList.add("active");

            tabPanels.forEach(p => p.classList.remove("active"));
            const targetPanel = document.getElementById(target);
            if (targetPanel) targetPanel.classList.add("active");

            loadBatches(currentFilter);
        });
    });

    document.addEventListener('click', async (e) => {
        const approveBtn = e.target.closest('.btn-approve-disbursement');
        if (approveBtn) {
            const batchId = approveBtn.getAttribute('data-batch-id');
            const result = await Swal.fire({
                title: 'Confirm Disbursement?',
                text: "This will officially release the funds to the payroll account.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#2ca078',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Disburse Now'
            });

            if (result.isConfirmed) {
                try {
                    const data = await fetchJson(apiUrl, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: new URLSearchParams({ action: 'disburse_batch', batch_id: batchId }).toString()
                    });

                    Swal.fire({
                        title: 'Disbursed!',
                        text: data.message,
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });

                    loadStats();
                    loadBatches(currentFilter);
                } catch (err) {
                    Swal.fire('Error', err.message, 'error');
                }
            }
        }

        const archiveBtn = e.target.closest('.btn-archive-batch');
        if (archiveBtn) {
            const batchId = archiveBtn.getAttribute('data-batch-id');
            const result = await Swal.fire({
                title: 'Archive Batch?',
                text: "This will move the batch to history without processing disbursement.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3b82f6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Archive'
            });

            if (result.isConfirmed) {
                try {
                    const data = await fetchJson(apiUrl, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: new URLSearchParams({ action: 'archive_batch', batch_id: batchId }).toString()
                    });

                    Swal.fire({
                        title: 'Archived!',
                        text: data.message,
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });

                    loadStats();
                    loadBatches(currentFilter);
                } catch (err) {
                    Swal.fire('Error', err.message, 'error');
                }
            }
        }

        const viewBtn = e.target.closest('.btn-view-batch');
        if (viewBtn) {
            const batchId = viewBtn.getAttribute('data-batch-id');
            try {
                const data = await fetchJson(`${apiUrl}?action=view_items&batch_id=${batchId}`);
                let listHtml = `<div class="payroll-table-container" style="max-height: 500px; overflow-y: auto; overflow-x: auto; background: var(--surface); border-radius: 12px; border: 1px solid var(--border-color);">
                    <table class="payroll-table" style="min-width: 1200px; width: 100%; text-align: left; border-collapse: collapse; font-size: 13px;">
                        <thead style="position: sticky; top: 0; background: var(--surface-hover); z-index: 10;">
                            <tr>
                                <th style="padding: 12px 16px; border-bottom: 1px solid var(--border-color);">Employee</th>
                                <th style="padding: 12px 16px; border-bottom: 1px solid var(--border-color); text-align: right;">Basic Pay</th>
                                <th style="padding: 12px 16px; border-bottom: 1px solid var(--border-color); text-align: right;">Allowances</th>
                                <th style="padding: 12px 16px; border-bottom: 1px solid var(--border-color); text-align: right;">OT Pay</th>
                                <th style="padding: 12px 16px; border-bottom: 1px solid var(--border-color); text-align: right;">SSS Reg</th>
                                <th style="padding: 12px 16px; border-bottom: 1px solid var(--border-color); text-align: right;">SSS WISP</th>
                                <th style="padding: 12px 16px; border-bottom: 1px solid var(--border-color); text-align: right;">PhilHealth</th>
                                <th style="padding: 12px 16px; border-bottom: 1px solid var(--border-color); text-align: right;">Pag-IBIG</th>
                                <th style="padding: 12px 16px; border-bottom: 1px solid var(--border-color); text-align: right;">Late/UT</th>
                                <th style="padding: 12px 16px; border-bottom: 1px solid var(--border-color); text-align: right;">W.Tax</th>
                                <th style="padding: 12px 16px; border-bottom: 1px solid var(--border-color); text-align: right;">Deductions</th>
                                <th style="padding: 12px 16px; border-bottom: 1px solid var(--border-color); text-align: right; color: var(--brand-green);">Net Pay</th>
                            </tr>
                        </thead>
                        <tbody>`;

                if (!data.items || data.items.length === 0) {
                    listHtml += `<tr><td colspan="12" style="padding: 24px; text-align: center; color: var(--text-tertiary);">No breakdown details found for this batch.</td></tr>`;
                } else {
                    let totalNetPay = 0;
                    data.items.forEach(item => {
                        totalNetPay += parseFloat(item.net_pay || 0);
                        listHtml += `
                            <tr style="border-bottom: 1px solid var(--border-color); transition: background 0.2s;">
                                <td style="padding: 12px 16px; font-weight: 500; color: var(--text-primary);">${item.LastName}, ${item.FirstName}</td>
                                <td style="padding: 12px 16px; text-align: right; color: var(--text-primary);">${peso(item.basic_pay)}</td>
                                <td style="padding: 12px 16px; text-align: right; color: var(--text-primary);">${peso(item.allowances_total)}</td>
                                <td style="padding: 12px 16px; text-align: right; color: var(--text-primary);">${peso(item.overtime_pay)}</td>
                                <td style="padding: 12px 16px; text-align: right; color: var(--text-secondary);">${peso(item.sss_regular_ee)}</td>
                                <td style="padding: 12px 16px; text-align: right; color: var(--text-secondary);">${peso(item.sss_wisp_ee)}</td>
                                <td style="padding: 12px 16px; text-align: right; color: var(--text-secondary);">${peso(item.philhealth_ee)}</td>
                                <td style="padding: 12px 16px; text-align: right; color: var(--text-secondary);">${peso(item.pagibig_ee)}</td>
                                <td style="padding: 12px 16px; text-align: right; color: #ef4444;">${peso(item.late_undertime)}</td>
                                <td style="padding: 12px 16px; text-align: right; color: #ef4444;">${peso(item.withholding_tax)}</td>
                                <td style="padding: 12px 16px; text-align: right; color: #ef4444; font-weight: 500;">${peso(item.deductions_total)}</td>
                                <td style="padding: 12px 16px; text-align: right; color: var(--brand-green); font-weight: 700;">${peso(item.net_pay)}</td>
                            </tr>`;
                    });

                    // Add Total Row
                    listHtml += `
                        <tr style="background: var(--surface-hover); border-top: 2px solid var(--border-color);">
                            <td colspan="11" style="padding: 16px; text-align: right; font-weight: 700; color: var(--text-primary); font-size: 14px;">Total Disbursement:</td>
                            <td style="padding: 16px; text-align: right; color: var(--brand-green); font-weight: 800; font-size: 15px;">${peso(totalNetPay)}</td>
                        </tr>`;
                }

                listHtml += `</tbody></table></div>`;

                Swal.fire({
                    title: `Batch Breakdown Details`,
                    html: listHtml,
                    width: '1400px',
                    confirmButtonColor: '#2ca078',
                    confirmButtonText: 'Close'
                });
            } catch (err) {
                Swal.fire('Error', err.message, 'error');
            }
        }
        const proofBtn = e.target.closest('.btn-view-proof');
        if (proofBtn) {
            const code = proofBtn.getAttribute('data-batch-code');
            const amount = proofBtn.getAttribute('data-amount');
            const nowDay = new Date().toLocaleString('en-US', { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' });
            const nowTime = new Date().toLocaleString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true });

            Swal.fire({
                title: 'Disbursement Proof',
                html: `
                    <div style="text-align: left; padding: 20px; background: #f9fafb; border-radius: 12px; border: 1px dashed var(--brand-green);">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                            <span style="color: var(--text-secondary);">Batch Reference:</span>
                            <strong style="color: var(--text-primary);">${code}</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                            <span style="color: var(--text-secondary);">Disbursement Date:</span>
                            <strong style="color: var(--text-primary);">${nowDay}, ${nowTime}</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 20px; padding-top: 12px; border-top: 1px solid #eee;">
                            <span style="color: var(--text-secondary); font-weight: 600;">Total Released:</span>
                            <strong style="color: var(--brand-green); font-size: 20px;">${amount}</strong>
                        </div>
                        <p style="font-size: 12px; color: var(--text-tertiary); text-align: center;">This serves as official proof that the disbursement for ${code} was processed and released to the payroll department.</p>
                    </div>
                `,
                confirmButtonText: 'Print / Download',
                confirmButtonColor: '#2ca078',
                showCancelButton: true,
                cancelButtonText: 'Close'
            });
        }
    });

    // Sidebar Toggles
    const sidebar = document.getElementById("sidebar");
    const sidebarToggle = document.getElementById("sidebarToggle");
    const mobileMenuBtn = document.getElementById("mobileMenuBtn");

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
                item.parentElement.classList.toggle("active");
            }
        });
    });

    // User Menu Dropdown Logic
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

    // Initial load
    if (lucide) lucide.createIcons();
    loadStats();
    loadBatches('ready');
});
