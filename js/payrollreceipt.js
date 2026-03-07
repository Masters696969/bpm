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

    // 4. Table Selection & Search Filter
    const selectAll = document.getElementById("selectAll");
    const rowCheckboxes = document.querySelectorAll(".row-checkbox");
    const searchInput = document.getElementById("roleSearch");
    const tableRows = document.querySelectorAll(".role-row-item");

    if (selectAll) {
        selectAll.addEventListener("change", () => {
            rowCheckboxes.forEach(cb => {
                if (cb.closest('tr').style.display !== 'none') {
                    cb.checked = selectAll.checked;
                }
            });
        });
    }

    if (searchInput) {
        searchInput.addEventListener("keyup", () => {
            const query = searchInput.value.toLowerCase();
            tableRows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(query) ? "" : "none";
            });
        });
    }

    // 5. Modal Logic
    const modal = document.getElementById("marketModal");
    const marketBtns = document.querySelectorAll(".market-salary-btn");
    const closeModal = document.getElementById("closeModal");
    const confirmSync = document.getElementById("confirmSync");
    let currentRole = "";

    marketBtns.forEach(btn => {
        btn.addEventListener("click", (e) => {
            const row = e.target.closest("tr");
            currentRole = row.querySelector(".client-name").innerText;
            document.getElementById("modalTitle").innerText = `Sync ${currentRole}`;
            modal.style.display = "flex";
        });
    });

    if (closeModal) closeModal.addEventListener("click", () => modal.style.display = "none");
    if (confirmSync) {
        confirmSync.addEventListener("click", () => {
            alert(`Success: ${currentRole} queued for analysis.`);
            modal.style.display = "none";
        });
    }

    if (typeof lucide !== "undefined") lucide.createIcons();
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

// Employee Payslip Logic
(function() {
    const apiUrl = '../../modules/payroll/payroll_action.php';
    const payslipsBody = document.getElementById('payslipsBody');
    const statTotalNet = document.getElementById('statTotalNet');
    const statPayslipCount = document.getElementById('statPayslipCount');
    const btnRefresh = document.getElementById('btnRefresh');

    const peso = (n) => {
        const num = Number(n || 0);
        return `₱${num.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    };

    const fetchJson = async (url, options = {}) => {
        const res = await fetch(url, options);
        const data = await res.json().catch(() => ({}));
        if (!res.ok || !data.ok) {
            const msg = data.error || `Request failed (${res.status})`;
            throw new Error(msg);
        }
        return data;
    };

    const renderPayslips = (payslips) => {
        if (!payslipsBody) return;
        payslipsBody.innerHTML = '';

        if (!payslips || payslips.length === 0) {
            payslipsBody.innerHTML = `<tr><td colspan="7" style="padding: 24px; text-align: center; color: var(--text-secondary);">No approved payslips found.</td></tr>`;
            return;
        }

        let totalNet = 0;
        payslips.forEach(p => {
            totalNet += Number(p.net_pay || 0);
            const tr = document.createElement('tr');
            tr.style.borderBottom = '1px solid var(--border-color)';
            tr.innerHTML = `
                <td style="padding: 14px 16px; font-weight: 600; color: var(--text-primary);">${p.batch_code}</td>
                <td style="padding: 14px 16px; color: var(--text-secondary);">${p.period_start} - ${p.period_end}</td>
                <td style="padding: 14px 16px; color: var(--text-secondary);">${p.pay_type}</td>
                <td style="padding: 14px 16px; text-align: right; color: var(--text-primary);">${peso(p.basic_pay)}</td>
                <td style="padding: 14px 16px; text-align: right; color: #ef4444;">${peso(p.deductions_total)}</td>
                <td style="padding: 14px 16px; text-align: right; font-weight: 600; color: var(--brand-green);">${peso(p.net_pay)}</td>
                <td style="padding: 14px 16px; text-align: center;">
                    <button class="btn-view-payslip" data-item-id="${p.item_id}" style="padding: 6px 12px; border-radius: 6px; border: none; background: var(--brand-green); color: #fff; cursor: pointer; font-weight: 500;">
                        <i data-lucide="file-text" style="width: 14px; height: 14px;"></i> View
                    </button>
                </td>
            `;
            payslipsBody.appendChild(tr);
        });

        const avgNet = payslips.length > 0 ? totalNet / payslips.length : 0;
        if (statTotalNet) statTotalNet.textContent = peso(totalNet);
        if (statPayslipCount) statPayslipCount.textContent = payslips.length;
        const statAvgNet = document.getElementById('statAvgNet');
        if (statAvgNet) statAvgNet.textContent = peso(avgNet);

        if (window.lucide) window.lucide.createIcons();
        attachViewHandlers();
    };

    const attachViewHandlers = () => {
        document.querySelectorAll('.btn-view-payslip').forEach(btn => {
            btn.addEventListener('click', () => {
                const itemId = btn.getAttribute('data-item-id');
                window.open(`../../modules/payroll/payslip.php?item_id=${itemId}`, '_blank', 'width=900,height=700');
            });
        });
    };

    const loadPayslips = async () => {
        try {
            const data = await fetchJson(`${apiUrl}?action=employee_payslips`);
            console.log('Payslips response:', data);
            
            // Show debug info if no payslips found
            if ((!data.payslips || data.payslips.length === 0) && data.debug) {
                console.log('Debug info:', data.debug);
                console.log('Employee ID:', data.employee_id);
                
                // Build batch status list
                let batchListHtml = '';
                if (data.debug.all_batch_statuses && data.debug.all_batch_statuses.length > 0) {
                    batchListHtml = '<div style="margin-top: 8px;"><strong>Batch Statuses:</strong><ul style="margin: 4px 0; padding-left: 20px;">';
                    data.debug.all_batch_statuses.forEach(b => {
                        const statusColor = b.status === 'Approved' ? 'var(--brand-green)' : (b.status === 'Rejected' ? '#ef4444' : 'var(--brand-yellow)');
                        batchListHtml += `<li>${b.batch_code}: <span style="color:${statusColor};font-weight:600;">${b.status}</span></li>`;
                    });
                    batchListHtml += '</ul></div>';
                }
                
                if (payslipsBody) {
                    payslipsBody.innerHTML = `
                        <tr><td colspan="7" style="padding: 24px; text-align: center; color: var(--text-secondary);">
                            <strong>No approved payslips found.</strong><br><br>
                            <div style="text-align: left; background: var(--surface); padding: 12px; border-radius: 8px; font-size: 12px;">
                                <div><strong>Employee ID:</strong> ${data.employee_id || 'Not found'}</div>
                                <div><strong>Approved Batches:</strong> ${data.debug.approved_batches || 0}</div>
                                <div><strong>Your Payroll Items:</strong> ${data.debug.employee_payroll_items || 0}</div>
                                ${batchListHtml}
                            </div>
                        </td></tr>
                    `;
                }
                return;
            }
            
            renderPayslips(data.payslips);
        } catch (err) {
            console.error('Failed to load payslips:', err);
            if (payslipsBody) {
                payslipsBody.innerHTML = `
                    <tr><td colspan="7" style="padding: 24px; text-align: center; color: #ef4444;">
                        <strong>Error:</strong> ${err.message}<br>
                        <small style="color: var(--text-tertiary);">Check browser console (F12) for more details.</small>
                    </td></tr>
                `;
            }
        }
    };

    if (btnRefresh) {
        btnRefresh.addEventListener('click', loadPayslips);
    }

    // Initial load
    if (payslipsBody) {
        loadPayslips();
    }
})();
