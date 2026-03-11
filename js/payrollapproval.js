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

// Payroll Approval Logic
(function () {
    const apiUrl = '../../modules/payroll/payroll_action.php';
    const pendingBatchesBody = document.getElementById('pendingBatchesBody');
    const statPendingCount = document.getElementById('statPendingCount');
    const statApprovedCount = document.getElementById('statApprovedCount');
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

    const renderBatches = (batches) => {
        if (!pendingBatchesBody) return;
        pendingBatchesBody.innerHTML = '';

        if (!batches || batches.length === 0) {
            pendingBatchesBody.innerHTML = `
                <div style="padding: 48px 24px; text-align: center;">
                    <div style="width: 64px; height: 64px; border-radius: 50%; background: var(--background); margin: 0 auto 16px; display: flex; align-items: center; justify-content: center;">
                        <i data-lucide="inbox" style="width: 28px; height: 28px; color: var(--text-tertiary);"></i>
                    </div>
                    <p style="font-size: 16px; font-weight: 500; color: var(--text-primary); margin-bottom: 4px;">No pending batches</p>
                    <p style="font-size: 14px; color: var(--text-tertiary);">All payroll batches have been processed</p>
                </div>
            `;
            if (window.lucide) window.lucide.createIcons();
            return;
        }

        batches.forEach(b => {
            const card = document.createElement('div');
            card.className = 'approval-batch-card';
            card.innerHTML = `
                <div class="abc-info">
                    <div class="abc-icon">
                        <i data-lucide="file-text"></i>
                    </div>
                    <div class="abc-details">
                        <div class="abc-code">${b.batch_code}</div>
                        <div class="abc-meta">
                            <span><i data-lucide="calendar"></i>${b.period_start} - ${b.period_end}</span>
                            <span class="abc-type-badge">${b.pay_type}</span>
                        </div>
                    </div>
                </div>
                <div class="abc-actions-wrapper">
                    <div class="abc-stats">
                        <div class="abc-stat-label">${b.employee_count} employees</div>
                        <div class="abc-stat-value">${peso(b.total_distributed)}</div>
                    </div>
                    <div class="abc-buttons">
                        <button class="btn-approve" data-batch-id="${b.id}">
                            <i data-lucide="check"></i> Approve
                        </button>
                        <button class="btn-reject" data-batch-id="${b.id}">
                            <i data-lucide="x"></i> Reject
                        </button>
                    </div>
                </div>
            `;
            pendingBatchesBody.appendChild(card);
        });

        if (window.lucide) window.lucide.createIcons();
        attachActionHandlers();
    };

    const attachActionHandlers = () => {
        document.querySelectorAll('.btn-approve').forEach(btn => {
            btn.addEventListener('click', async (e) => {
                const batchId = btn.getAttribute('data-batch-id');
                const result = await Swal.fire({
                    title: 'Approve Payroll Batch?',
                    text: 'This will finalize the payroll and make it visible to employees.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: 'var(--brand-green)',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, Approve'
                });
                if (result.isConfirmed) {
                    try {
                        await fetchJson(apiUrl, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: new URLSearchParams({ action: 'approve_batch', batch_id: batchId }).toString()
                        });
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Approved',
                            text: 'Payroll batch has been approved and sent to Disbursement.',
                            showConfirmButton: false,
                            timer: 2500,
                            timerProgressBar: true
                        });
                        loadPendingBatches();
                    } catch (err) {
                        Swal.fire({ icon: 'error', title: 'Error', text: err.message });
                    }
                }
            });
        });

        document.querySelectorAll('.btn-reject').forEach(btn => {
            btn.addEventListener('click', async (e) => {
                const batchId = btn.getAttribute('data-batch-id');
                const result = await Swal.fire({
                    title: 'Reject Payroll Batch?',
                    text: 'This will mark the batch as rejected and return it for revision.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, Reject'
                });
                if (result.isConfirmed) {
                    try {
                        await fetchJson(apiUrl, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: new URLSearchParams({ action: 'reject_batch', batch_id: batchId }).toString()
                        });
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Rejected',
                            text: 'Payroll batch has been rejected.',
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true
                        });
                        loadPendingBatches();
                    } catch (err) {
                        Swal.fire({ icon: 'error', title: 'Error', text: err.message });
                    }
                }
            });
        });
    };

    const loadPendingBatches = async () => {
        try {
            const data = await fetchJson(`${apiUrl}?action=list_pending_approvals`);
            console.log('Pending batches response:', data);
            if (data.debug_all_batches) {
                console.log('All batch statuses:', data.debug_all_batches);
            }
            renderBatches(data.batches);
            if (statPendingCount) statPendingCount.textContent = data.batches?.length || 0;
        } catch (err) {
            console.error('Failed to load pending batches:', err);
            if (pendingBatchesBody) {
                pendingBatchesBody.innerHTML = `
                    <div style="padding: 48px 24px; text-align: center;">
                        <div style="width: 64px; height: 64px; border-radius: 50%; background: rgba(239, 68, 68, 0.1); margin: 0 auto 16px; display: flex; align-items: center; justify-content: center;">
                            <i data-lucide="alert-circle" style="width: 28px; height: 28px; color: #ef4444;"></i>
                        </div>
                        <p style="font-size: 16px; font-weight: 500; color: var(--text-primary); margin-bottom: 4px;">Failed to load batches</p>
                        <p style="font-size: 14px; color: var(--text-tertiary);">${err.message}</p>
                    </div>
                `;
                if (window.lucide) window.lucide.createIcons();
            }
        }
    };

    if (btnRefresh) {
        btnRefresh.addEventListener('click', loadPendingBatches);
    }

    // Initial load
    if (pendingBatchesBody) {
        loadPendingBatches();
    }
})();
