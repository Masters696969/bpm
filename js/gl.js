document.addEventListener("DOMContentLoaded", () => {
    const apiUrl = 'gl_action.php';
    const tableBody = document.getElementById('glTableBody');
    const tabButtons = document.querySelectorAll('.tab-btn');
    const searchInput = document.getElementById('glSearchInput');

    let currentFilter = 'all';
    let allEntries = [];

    const peso = (n) => {
        const num = Number(n || 0);
        return `₱${num.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    };

    const statusBadge = (status) => {
        const s = String(status || '').toLowerCase();
        if (s === 'posted') {
            return `<span class="badge-premium badge-success"><i data-lucide="check-circle-2"></i> ${status}</span>`;
        }
        if (s === 'voided') {
            return `<span class="badge-premium badge-secondary"><i data-lucide="x-circle"></i> ${status}</span>`;
        }
        return `<span class="badge-premium badge-warning"><i data-lucide="clock"></i> ${status || 'Pending'}</span>`;
    };

    const fetchJson = async (url) => {
        const res = await fetch(url);
        const data = await res.json().catch(() => ({}));
        if (!res.ok || !data.ok) throw new Error(data.error || 'Request failed');
        return data;
    };

    const loadStats = async () => {
        try {
            const data = await fetchJson(`${apiUrl}?action=stats`);
            document.getElementById('statTotalDebit').textContent = peso(data.total_debit);
            document.getElementById('statTotalCredit').textContent = peso(data.total_credit);
            document.getElementById('statTransactionCount').textContent = data.transaction_count;
        } catch (e) {
            console.error('Failed to load stats:', e);
        }
    };

    const renderTable = (entries) => {
        if (!tableBody) return;
        tableBody.innerHTML = '';

        if (!entries || entries.length === 0) {
            tableBody.innerHTML = `<tr><td colspan="7" style="text-align:center; padding: 24px; color: var(--text-tertiary);">No general ledger entries found.</td></tr>`;
            return;
        }

        entries.forEach(item => {
            const debitStr = Number(item.debit) > 0 ? peso(item.debit) : '-';
            const creditStr = Number(item.credit) > 0 ? peso(item.credit) : '-';

            // Format nice date
            const dateObj = new Date(item.transaction_date);
            const dateStr = dateObj.toLocaleDateString(undefined, { month: 'short', day: 'numeric', year: 'numeric' });
            const timeStr = dateObj.toLocaleTimeString(undefined, { hour: '2-digit', minute: '2-digit' });

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>
                    <div style="font-weight: 500; color: var(--text-primary);">${dateStr}</div>
                    <div style="font-size: 12px; color: var(--text-secondary);">${timeStr}</div>
                </td>
                <td style="font-family: monospace; color: var(--brand-green); font-weight: 600;">${item.reference_id}</td>
                <td style="font-weight: 500;">${item.account_name}</td>
                <td style="color: var(--text-secondary); max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="${item.description}">${item.description}</td>
                <td style="text-align: right; color: var(--text-primary); font-weight: 600;">${debitStr}</td>
                <td style="text-align: right; color: var(--text-primary); font-weight: 600;">${creditStr}</td>
                <td>${statusBadge(item.status)}</td>
            `;
            tableBody.appendChild(tr);
        });

        if (window.lucide) window.lucide.createIcons();
    };

    const loadEntries = async (filter) => {
        try {
            const data = await fetchJson(`${apiUrl}?action=list_gl&filter=${filter}`);
            allEntries = data.entries || [];
            if (searchInput && searchInput.value) {
                applySearch(searchInput.value);
            } else {
                renderTable(allEntries);
            }
        } catch (e) {
            console.error('Failed to load entries:', e);
        }
    };

    const applySearch = (term) => {
        const lowered = term.toLowerCase();
        const filtered = allEntries.filter(e =>
            (e.reference_id || '').toLowerCase().includes(lowered) ||
            (e.account_name || '').toLowerCase().includes(lowered) ||
            (e.description || '').toLowerCase().includes(lowered)
        );
        renderTable(filtered);
    };

    // Tab Logic
    tabButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            tabButtons.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            currentFilter = btn.getAttribute('data-tab');
            loadEntries(currentFilter);
        });
    });

    // Search input
    if (searchInput) {
        searchInput.addEventListener('input', (e) => applySearch(e.target.value));
    }

    // Sidebar & UI Logic
    const body = document.body;
    const themeToggle = document.getElementById("themeToggle");
    const sidebarToggle = document.getElementById("sidebarToggle");
    const sidebar = document.getElementById("sidebar");

    if (themeToggle) {
        themeToggle.addEventListener("click", () => {
            body.classList.toggle("dark-mode");
            localStorage.setItem("theme", body.classList.contains("dark-mode") ? "dark" : "light");
        });
        if (localStorage.getItem("theme") === "dark") body.classList.add("dark-mode");
    }

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener("click", () => {
            sidebar.classList.toggle("collapsed");
            localStorage.setItem("sidebarCollapsed", sidebar.classList.contains("collapsed"));
        });
        if (localStorage.getItem("sidebarCollapsed") === "true") sidebar.classList.add("collapsed");
    }

    // Real-time Clock
    function initClock() {
        const clockEl = document.getElementById('realTimeClock');
        if (!clockEl) return;
        const updateClock = () => {
            const now = new Date();
            const days = ['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'];
            const months = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];
            let hours = now.getHours();
            const ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12 || 12;
            const h = hours.toString().padStart(2, '0');
            const m = now.getMinutes().toString().padStart(2, '0');
            const s = now.getSeconds().toString().padStart(2, '0');
            clockEl.textContent = `${days[now.getDay()]}, ${months[now.getMonth()]} ${now.getDate()}, ${now.getFullYear()}, ${h}:${m}:${s} ${ampm}`;
        };
        setInterval(updateClock, 1000);
        updateClock();
    }
    initClock();

    // User Menu Dropdown
    const userMenuBtn = document.getElementById('userMenuBtn');
    const userMenuDropdown = document.getElementById('userMenuDropdown');
    const nameEl = document.querySelector('.sidebar-footer .user-name');
    const roleEl = document.querySelector('.sidebar-footer .user-role');
    const umdName = document.getElementById('umdName');
    const umdRole = document.getElementById('umdRole');
    const umdAvatar = document.getElementById('umdAvatar');

    if (nameEl && umdName) {
        umdName.textContent = nameEl.textContent;
        if (umdAvatar) umdAvatar.textContent = nameEl.textContent.charAt(0).toUpperCase();
    }
    if (roleEl && umdRole) umdRole.textContent = roleEl.textContent;

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

    // Init data load
    loadStats();
    loadEntries(currentFilter);
});
