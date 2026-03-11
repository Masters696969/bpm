document.addEventListener("DOMContentLoaded", () => {
    const lucide = window.lucide;
    
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

    if (lucide) lucide.createIcons();

    // -----------------------------------------------------
    // Tab System Logic
    // -----------------------------------------------------
    const tabLinks = document.querySelectorAll('.tab-link');
    const tabPanes = document.querySelectorAll('.tab-pane');

    tabLinks.forEach(link => {
        link.addEventListener('click', () => {
            const targetTab = link.getAttribute('data-tab');
            
            // Update active link
            tabLinks.forEach(l => l.classList.remove('active'));
            link.classList.add('active');
            
            // Update active pane
            tabPanes.forEach(pane => {
                pane.classList.remove('active');
                if (pane.id === `${targetTab}Tab`) {
                    pane.classList.add('active');
                }
            });
        });
    });

    // -----------------------------------------------------
    // Disbursement Variables & Logic
    // -----------------------------------------------------
    const apiUrl = 'disbursement_action.php';
    let currentBatchId = null;

    const peso = (n) => {
        const num = Number(n || 0);
        return `₱${num.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    };

    const fetchJson = async (url, options = {}) => {
        const res = await fetch(url, options);
        const data = await res.json().catch(() => ({}));
        if (!res.ok || !data.ok) throw new Error(data.error || `Request failed (${res.status})`);
        return data;
    };

    const loadStats = async () => {
        try {
            const data = await fetchJson(`${apiUrl}?action=stats`);
            document.getElementById('statPendingPayout').textContent = peso(data.pending_payout);
            document.getElementById('statTotalPaid').textContent = peso(data.total_paid);
            
            // Fetch history count as well
            const hData = await fetchJson(`${apiUrl}?action=list_history`);
            if (hData.history) {
                document.getElementById('statRecentCount').textContent = hData.history.length;
            }
        } catch (e) { console.error('Stats error:', e); }
    };


    const loadBatches = async () => {
        try {
            const data = await fetchJson(`${apiUrl}?action=list_batches`);
            const body = document.getElementById('disbursementBatchesBody');
            body.innerHTML = '';
            
            if (!data.batches || data.batches.length === 0) {
                body.innerHTML = `<tr><td colspan="6" style="padding: 24px; text-align: center; color: var(--text-secondary);">No batches waiting for payout.</td></tr>`;
                document.getElementById('batchDetailPanel').style.display = 'none';
                return;
            }

            data.batches.forEach(b => {
                const tr = document.createElement('tr');
                tr.className = 'row-fade-in';
                
                const showStatus = b.status === 'Archived' ? '<span class="badge-premium badge-secondary"><i data-lucide="archive"></i> Archived</span>' : '<span class="badge-premium badge-info" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;"><i data-lucide="clock"></i> Pending Transfer</span>';

                tr.innerHTML = `
                    <td><strong>${b.batch_code}</strong></td>
                    <td>${b.period_start} to ${b.period_end}</td>
                    <td>${b.pay_type}</td>
                    <td><span class="badge-premium badge-warning">${b.employees_remaining} Unpaid</span></td>
                    <td>${showStatus}</td>
                    <td>
                        <button class="btn-premium btn-view-batch" data-batch-id="${b.id}" data-batch-code="${b.batch_code}" style="background: var(--brand-green); color: white; padding: 10px 20px; border-radius:10px; font-weight:600;">Process</button>
                    </td>
                `;
                body.appendChild(tr);
            });
            if (lucide) lucide.createIcons();
        } catch (e) { console.error('Batch error:', e); }
    };

    const loadHistory = async () => {
        try {
            const data = await fetchJson(`${apiUrl}?action=list_batches&filter=history`);
            renderHistory(data.batches || []);
        } catch (e) { console.error('History error:', e); }
    };

    const renderHistory = (batches) => {
        const body = document.getElementById('payoutHistoryBody');
        body.innerHTML = '';

        if (!batches || batches.length === 0) {
            body.innerHTML = '<tr><td colspan="6" style="padding: 24px; text-align: center; color: var(--text-secondary);">No history found.</td></tr>';
            return;
        }

        batches.forEach(b => {
            const tr = document.createElement('tr');
            tr.className = 'row-fade-in';
            const period = `${b.period_start} to ${b.period_end}`;
            
            tr.innerHTML = `
                <td><strong>${b.batch_code}</strong></td>
                <td>${period}</td>
                <td>${b.pay_type}</td>
                <td><span class="badge-premium badge-success">${b.paid_count} Paid</span></td>
                <td><span class="badge-premium badge-info" style="background: rgba(44, 160, 120, 0.1); color: var(--brand-green);"><i data-lucide="check-check"></i> ${b.status}</span></td>
                <td>
                    <button class="btn-premium btn-view-history-batch" data-batch-id="${b.id}" data-batch-code="${b.batch_code}" style="background: var(--surface-hover); color: var(--text-primary); padding: 8px 16px; border-radius:10px; font-weight:600; border: 1px solid var(--border-color);">View</button>
                </td>
            `;
            body.appendChild(tr);
        });
        if (lucide) lucide.createIcons();
    };

    const loadEmployees = async (batchId, batchCode, isHistory = false) => {
        try {
            const label = document.getElementById('currentBatchCodeLabel');
            label.textContent = `${batchCode} ${isHistory ? '(History View)' : ''}`;
            
            const modal = document.getElementById('batchDetailModal');
            modal.style.display = 'flex';
            setTimeout(() => modal.classList.add('open'), 10);

            // Hide "Pay All" button if viewing history
            const btnPayAll = document.getElementById('btnPayAll');
            if (btnPayAll) btnPayAll.style.display = isHistory ? 'none' : 'flex';

            const data = await fetchJson(`${apiUrl}?action=list_employees&batch_id=${batchId}${isHistory ? '&all=1' : ''}`);
            const body = document.getElementById('batchEmployeesBody');
            body.innerHTML = '';

            if (!data.employees || data.employees.length === 0) {
                body.innerHTML = `<tr><td colspan="6" style="padding: 40px; text-align: center; color: var(--text-secondary);">No employee data available.</td></tr>`;
                return;
            }

            data.employees.forEach(e => {
                const tr = document.createElement('tr');
                tr.className = 'row-fade-in';
                const hasBank = e.account_number && e.account_number !== 'Not Set';
                const warningHTML = !hasBank ? `<span style="color:#ef4444; font-size:12px; font-weight:600;"><i data-lucide="triangle-alert" style="width:12px;"></i> Missing</span>` : '';
                
                const isPaid = e.item_status === 'Paid';

                tr.innerHTML = `
                    <td><strong>${e.LastName}, ${e.FirstName}</strong></td>
                    <td><span class="institution-badge">${e.bank_name}</span></td>
                    <td>${hasBank ? `<code>${e.account_number}</code>` : warningHTML}</td>
                    <td style="color: var(--brand-green); font-weight: 700; font-size: 15px;">${peso(e.net_pay)}</td>
                    <td>
                        <span class="badge-premium ${isPaid ? 'badge-success' : 'badge-warning'}">
                            ${isPaid ? '<i data-lucide="check" style="width:12px;"></i> PAID' : 'PENDING'}
                        </span>
                    </td>
                    <td>
                        ${!isPaid && !isHistory ? `
                            <button class="btn-premium btn-pay-employee" data-item-id="${e.item_id}" ${!hasBank ? 'disabled title="Missing Bank Detail"' : ''} style="background: #2ca078; color: white; padding: 10px 20px; border-radius:10px; opacity: ${hasBank ? '1' : '0.5'};"><i data-lucide="send" style="width:14px; margin-right:6px;"></i> Pay</button>
                        ` : (isPaid ? '<div class="processed-indicator"><i data-lucide="shield-check" style="width:14px; margin-right:4px;"></i> Processed</div>' : '<span style="color:var(--text-tertiary); font-size:12px;">Read Only</span>')}
                    </td>
                `;
                body.appendChild(tr);
            });

            if (lucide) lucide.createIcons();
        } catch (e) {
            Swal.fire('Error', e.message, 'error');
        }
    };

    const closeBatchModal = () => {
        const modal = document.getElementById('batchDetailModal');
        modal.classList.remove('open');
        setTimeout(() => modal.style.display = 'none', 300);
    };

    document.getElementById('closeBatchModal')?.addEventListener('click', closeBatchModal);
    document.getElementById('batchDetailModal')?.addEventListener('click', (e) => {
        if (e.target.id === 'batchDetailModal') closeBatchModal();
    });

    document.addEventListener('click', async (e) => {
        const viewHistBtn = e.target.closest('.btn-view-history-batch');
        if (viewHistBtn) {
            const batchId = viewHistBtn.getAttribute('data-batch-id');
            const batchCode = viewHistBtn.getAttribute('data-batch-code');
            loadEmployees(batchId, batchCode, true);
        }

        const viewBtn = e.target.closest('.btn-view-batch');
        if (viewBtn) {
            const batchId = viewBtn.getAttribute('data-batch-id');
            const batchCode = viewBtn.getAttribute('data-batch-code');
            loadEmployees(batchId, batchCode, false);
        }

        const payBtn = e.target.closest('.btn-pay-employee');
        if (payBtn && !payBtn.disabled) {
            const itemId = payBtn.getAttribute('data-item-id');
        
            Swal.fire({
                title: 'Process Payout?',
                text: "This will transmit the net pay to the employee's bank account via Xendit V2 API.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2ca078',
                cancelButtonColor: '#aaa',
                confirmButtonText: 'Yes, Transmit'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    Swal.fire({ title: 'Transmitting...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                    try {
                        const data = await fetchJson(apiUrl, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: new URLSearchParams({ action: 'pay_employee', item_id: itemId }).toString()
                        });
                        Swal.fire('Paid!', data.message, 'success');
                        loadStats();
                        loadHistory();
                        loadEmployees(currentBatchId, document.getElementById('currentBatchCodeLabel').textContent.split(' - ')[1].replace(' (History View)', ''), document.getElementById('currentBatchCodeLabel').textContent.includes('(History View)'));
                    } catch (err) {
                        Swal.fire({
                            title: 'Transmission Failed', 
                            html: `<div style="text-align:left; font-size:14px;">${err.message}</div>`, 
                            icon: 'error'
                        });
                    }
                }
            });
        }
    });

    const btnPayAll = document.getElementById('btnPayAll');
    if (btnPayAll) {
        btnPayAll.addEventListener('click', async () => {
             const payBtns = Array.from(document.querySelectorAll('.btn-pay-employee')).filter(b => !b.disabled);
             if (payBtns.length === 0) {
                 return Swal.fire('No valid items', 'There are no employees with valid bank accounts ready to pay.', 'info');
             }

             Swal.fire({
                title: `Pay ${payBtns.length} Employees?`,
                text: "This will sequentially transmit payouts via Xendit V2. Please do not close this window.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#2ca078',
                confirmButtonText: 'Yes, Pay All'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    let successCount = 0;
                    let failureCount = 0;
                    Swal.fire({ title: 'Processing Payouts...', html: `Completed: 0 / ${payBtns.length}`, allowOutsideClick: false, didOpen: () => Swal.showLoading() });

                    for (const btn of payBtns) {
                        const itemId = btn.getAttribute('data-item-id');
                        try {
                            await fetchJson(apiUrl, {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                                body: new URLSearchParams({ action: 'pay_employee', item_id: itemId }).toString()
                            });
                            successCount++;
                        } catch (e) {
                            failureCount++;
                        }
                        Swal.update({ html: `Success: ${successCount} | Failed: ${failureCount} <br> Total: ${payBtns.length} `});
                    }
                    Swal.fire('Bulk Payout Finished', `Successfully paid ${successCount}. Failed ${failureCount}.`, (failureCount === 0 ? 'success' : 'warning'));
                    loadStats();
                    loadHistory();
                    loadEmployees(currentBatchId, document.getElementById('currentBatchCodeLabel').textContent.split(' - ')[1].replace(' (History View)', ''), document.getElementById('currentBatchCodeLabel').textContent.includes('(History View)'));
                }
            });
        });
    }

    // Search History Logic
    const searchPayouts = document.getElementById('searchPayouts');
    if (searchPayouts) {
        searchPayouts.addEventListener('input', (e) => {
            const term = e.target.value.toLowerCase().trim();
            const rows = document.querySelectorAll('#payoutHistoryBody tr');
            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(term) ? '' : 'none';
            });
        });
    }

    loadStats();
    loadBatches();
    loadHistory();
});
