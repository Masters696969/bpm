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
            card.style.cssText = 'background: var(--background); border-radius: 16px; padding: 20px; margin-bottom: 12px; border: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; transition: all 0.2s ease;';
            card.innerHTML = `
                <div style="display: flex; align-items: center; gap: 16px;">
                    <div style="width: 48px; height: 48px; border-radius: 12px; background: linear-gradient(135deg, rgba(44, 160, 120, 0.15), rgba(59, 130, 246, 0.1)); display: flex; align-items: center; justify-content: center;">
                        <i data-lucide="file-text" style="width: 22px; height: 22px; color: var(--brand-green);"></i>
                    </div>
                    <div>
                        <div style="font-size: 16px; font-weight: 600; color: var(--text-primary); margin-bottom: 4px;">${b.batch_code}</div>
                        <div style="display: flex; align-items: center; gap: 12px; font-size: 13px; color: var(--text-secondary);">
                            <span><i data-lucide="calendar" style="width: 14px; height: 14px; margin-right: 4px;"></i>${b.period_start} - ${b.period_end}</span>
                            <span style="padding: 2px 8px; border-radius: 6px; background: rgba(255, 193, 7, 0.1); color: var(--brand-yellow); font-size: 11px; font-weight: 600;">${b.pay_type}</span>
                        </div>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 24px;">
                    <div style="text-align: right;">
                        <div style="font-size: 12px; color: var(--text-tertiary); margin-bottom: 2px;">${b.employee_count} employees</div>
                        <div style="font-size: 18px; font-weight: 700; color: var(--brand-green);">${peso(b.total_distributed)}</div>
                    </div>
                    <div style="display: flex; gap: 8px;">
                        <button class="btn-approve" data-batch-id="${b.id}" style="padding: 10px 20px; border-radius: 10px; border: none; background: var(--brand-green); color: #fff; cursor: pointer; font-weight: 600; font-size: 14px; display: flex; align-items: center; gap: 6px; transition: all 0.2s ease;">
                            <i data-lucide="check" style="width: 16px; height: 16px;"></i> Approve
                        </button>
                        <button class="btn-reject" data-batch-id="${b.id}" style="padding: 10px 20px; border-radius: 10px; border: none; background: transparent; color: #ef4444; cursor: pointer; font-weight: 600; font-size: 14px; border: 1px solid #ef4444; display: flex; align-items: center; gap: 6px; transition: all 0.2s ease;">
                            <i data-lucide="x" style="width: 16px; height: 16px;"></i> Reject
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
                            text: 'Payroll batch has been approved.',
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
