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
            if (submenu) {
                submenu.classList.toggle("active");
                item.classList.toggle("active");
            }
        });
    });

    // 4. AP Management Logic
    let currentTab = 'pending';
    const apPendingBody = document.getElementById('apPendingBody');
    const apHistoryBody = document.getElementById('apHistoryBody');
    const apSearch = document.getElementById('apSearch');

    const formatCurrency = (amount) => {
        return new Intl.NumberFormat('en-PH', {
            style: 'currency',
            currency: 'PHP'
        }).format(amount);
    };

    const fetchAP = async (status = 'Pending') => {
        const targetBody = status === 'Pending' ? apPendingBody : apHistoryBody;
        if (!targetBody) return;

        try {
            const response = await fetch(`ap_action.php?action=list_ap&status=${status}`);
            const result = await response.json();

            if (result.ok) {
                renderAP(result.data, targetBody, status);
                if (status === 'Pending') {
                    updateStats(result.data);
                }
            } else {
                console.error("Error fetching AP:", result.error);
                targetBody.innerHTML = `<tr><td colspan="7" style="text-align: center; color: #ef4444;">Error: ${result.error}</td></tr>`;
            }
        } catch (error) {
            console.error("Fetch error:", error);
            targetBody.innerHTML = `<tr><td colspan="7" style="text-align: center; color: #ef4444;">Failed to load data.</td></tr>`;
        }
    };

    const renderAP = (data, container, status) => {
        if (data.length === 0) {
            container.innerHTML = `<tr><td colspan="7" style="text-align: center; padding: 40px; color: var(--text-tertiary);">No ${status.toLowerCase()} vouchers found.</td></tr>`;
            return;
        }

        container.innerHTML = data.map(item => `
            <tr>
                <td style="font-weight: 600; color: var(--brand-green);">${item.batch_code}</td>
                <td style="font-weight: 500;">${item.employee_name || '---'}</td>
                <td>
                    <div style="display: flex; flex-direction: column;">
                        <span style="font-weight: 600;">${item.category}</span>
                        <span style="font-size: 11px; color: var(--text-tertiary);">${item.period_start} to ${item.period_end}</span>
                    </div>
                </td>
                <td>${item.payee_name}</td>
                <td style="font-weight: 700; font-family: 'Outfit', sans-serif;">${formatCurrency(item.amount)}</td>
                <td>
                    <span class="badge-premium ${status === 'Pending' ? 'badge-warning' : 'badge-success'}">
                        <i data-lucide="${status === 'Pending' ? 'clock' : 'check-circle'}"></i>
                        ${status}
                    </span>
                </td>
                <td>
                    <div style="display: flex; gap: 8px;">
                        <button class="icon-btn view-btn" data-id="${item.id}" title="View Breakdown" style="color: #3b82f6; border-color: rgba(59, 130, 246, 0.2); width: 32px; height: 32px;">
                            <i data-lucide="eye" style="width: 14px;"></i>
                        </button>
                        ${status === 'Pending' ? `
                            <button class="icon-btn release-btn" data-id="${item.id}" title="Release Payment" style="color: var(--brand-green); border-color: rgba(44, 160, 120, 0.2); width: 32px; height: 32px;">
                                <i data-lucide="send" style="width: 14px;"></i>
                            </button>
                        ` : ''}
                    </div>
                </td>
            </tr>
        `).join('');

        if (window.lucide) window.lucide.createIcons();

        // Attach event listeners
        container.querySelectorAll('.release-btn').forEach(btn => {
            btn.addEventListener('click', () => releasePayment(btn.dataset.id));
        });
        container.querySelectorAll('.view-btn').forEach(btn => {
            btn.addEventListener('click', () => openDetailsModal(btn.dataset.id));
        });
    };

    const openDetailsModal = async (id) => {
        const modal = document.getElementById('apDetailsModal');
        const listBody = document.getElementById('apDetailsBody');
        const title = document.getElementById('modalVoucherTitle');
        const subtitle = document.getElementById('modalVoucherSubtitle');

        if (!modal || !listBody) return;

        listBody.innerHTML = '<tr><td colspan="3" style="text-align: center; padding: 20px;">Loading details...</td></tr>';
        modal.style.display = 'flex';

        try {
            const response = await fetch(`ap_action.php?action=get_voucher_details&id=${id}`);
            const result = await response.json();

            if (result.ok) {
                title.textContent = `${result.category} Distribution`;
                subtitle.textContent = `Employee Breakdown for Batch`;

                if (result.data.length === 0) {
                    listBody.innerHTML = '<tr><td colspan="3" style="text-align: center; padding: 20px;">No breakdown details found.</td></tr>';
                } else {
                    listBody.innerHTML = result.data.map(item => `
                        <tr>
                            <td style="font-weight: 600;">${item.employee_name}</td>
                            <td style="font-weight: 700; font-family: 'Outfit', sans-serif;">${formatCurrency(item.amount)}</td>
                            <td style="color: var(--text-tertiary); font-size: 13px;">${new Date(item.date).toLocaleDateString()}</td>
                        </tr>
                    `).join('');
                }
            } else {
                listBody.innerHTML = `<tr><td colspan="3" style="text-align: center; color: #ef4444;">${result.error}</td></tr>`;
            }
        } catch (error) {
            listBody.innerHTML = '<tr><td colspan="3" style="text-align: center; color: #ef4444;">Failed to load details.</td></tr>';
        }

        if (window.lucide) window.lucide.createIcons();
    };

    window.closeDetailsModal = () => {
        const modal = document.getElementById('apDetailsModal');
        if (modal) modal.style.display = 'none';
    };

    // Close modal on outside click
    window.addEventListener('click', (e) => {
        const modal = document.getElementById('apDetailsModal');
        if (e.target === modal) closeDetailsModal();
    });

    const updateStats = (data) => {
        const totalPayable = data.reduce((sum, item) => sum + parseFloat(item.amount), 0);
        document.getElementById('statPendingVouchers').textContent = data.length;
        document.getElementById('statTotalPayable').textContent = formatCurrency(totalPayable);
    };

    const releasePayment = async (id) => {
        const result = await Swal.fire({
            title: 'Release Payment?',
            text: "This will mark the voucher as Paid.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#2ca078',
            confirmButtonText: 'Yes, Release',
            reverseButtons: true
        });

        if (result.isConfirmed) {
            try {
                const formData = new FormData();
                formData.append('action', 'release_payment');
                formData.append('id', id);

                const response = await fetch('ap_action.php', {
                    method: 'POST',
                    body: formData
                });
                const res = await response.json();

                if (res.ok) {
                    Swal.fire('Success', res.message, 'success');
                    fetchAP('Pending');
                    if (currentTab === 'history') fetchAP('Paid');
                } else {
                    Swal.fire('Error', res.error, 'error');
                }
            } catch (error) {
                Swal.fire('Error', 'Something went wrong', 'error');
            }
        }
    };

    // Tab Switching Logic
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));

            btn.classList.add('active');
            const tabId = btn.dataset.tab;
            currentTab = tabId;
            document.getElementById(tabId).classList.add('active');

            fetchAP(tabId === 'pending' ? 'Pending' : 'Paid');
        });
    });

    // Search Logic
    if (apSearch) {
        apSearch.addEventListener('input', () => {
            const query = apSearch.value.toLowerCase();
            const rows = document.querySelectorAll('.payroll-table tbody tr');
            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        });
    }

    // Initial load
    fetchAP('Pending');

    if (typeof lucide !== "undefined") lucide.createIcons();
});

// Sidebar Active Link Logic
(function () {
    const path = window.location.pathname;
    const page = path.split('/').pop() || 'dashboard.php';
    const current = page.split('?')[0];

    document.querySelectorAll('.sidebar .nav-item, .sidebar .submenu-item').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.sidebar .nav-item-group').forEach(group => group.classList.remove('active'));

    const navMatch = document.querySelector(`.sidebar a.nav-item[href$="${current}"]`);
    if (navMatch) {
        navMatch.classList.add('active');
        // If it's in a submenu, expand it
        const parentGroup = navMatch.closest('.nav-item-group');
        if (parentGroup) {
            parentGroup.classList.add('active');
            const submenu = parentGroup.querySelector('.submenu');
            if (submenu) submenu.classList.add('active');
        }
    }
})();

// User Menu Dropdown Logic
document.addEventListener('DOMContentLoaded', () => {
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
    }

    const signOutLinks = document.querySelectorAll('.umd-sign-out');
    signOutLinks.forEach(link => {
        link.addEventListener('click', async e => {
            e.preventDefault();
            const dest = link.getAttribute('href');
            const result = await Swal.fire({
                title: 'Sign Out?',
                text: 'You are about to sign out.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Yes, Sign Out'
            });
            if (result.isConfirmed) {
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
        const now = new Date();
        clockEl.textContent = now.toLocaleDateString('en-US', {
            weekday: 'short',
            month: 'short',
            day: 'numeric',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
    };

    setInterval(updateClock, 1000);
    updateClock();
}

initClock();
