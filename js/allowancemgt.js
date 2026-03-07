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

// -------------------------------------------------------------
// Allowance Endorsed Proposals Logic
// -------------------------------------------------------------
async function fetchEndorsedProposals() {
    const tbody = document.getElementById('endorsedProposalsBody');
    if (!tbody) return;

    try {
        const response = await fetch('be_allowancemgt.php?action=fetch_endorsed_proposals');
        const res = await response.json();

        if (res.success) {
            if (res.data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="6"><div class="empty-state"><p>No pending allowance proposals to review.</p></div></td></tr>`;
                return;
            }

            tbody.innerHTML = res.data.map(req => {
                const date = new Date(req.UpdatedAt);
                return `
                <tr>
                    <td>
                        <div style="display:flex; align-items:center; gap:12px;">
                            <div style="width:32px; height:32px; border-radius:50%; background:var(--brand-green-light); color:var(--brand-green); display:flex; align-items:center; justify-content:center; font-weight:600; font-size:12px;">
                                ${req.ProposedByName.charAt(0).toUpperCase()}
                            </div>
                            <div>
                                <div style="font-weight:500; color:var(--text-primary);">${req.ProposedByName}</div>
                                <div style="font-size:12px; color:var(--text-secondary);">Compensation Analyst</div>
                            </div>
                        </div>
                    </td>
                    <td><span class="type-pill" style="background:rgba(245,158,11,0.1); color:#f59e0b;"><i data-lucide="gift"></i> Allowance</span></td>
                    <td><strong>${req.TotalChanges}</strong> changes</td>
                    <td>
                        <div style="font-weight:500;">${date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</div>
                        <div style="font-size:12px; color:var(--text-secondary);">${date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</div>
                    </td>
                    <td><span class="badge badge-warning">Endorsed</span></td>
                    <td>
                        <button class="btn-review" onclick="viewAllowanceProposal('${req.BatchReference}', '${encodeURIComponent(req.Reason)}')">
                            <i data-lucide="eye"></i> Review & Approve
                        </button>
                    </td>
                </tr>`;
            }).join('');
        } else {
            tbody.innerHTML = `<tr><td colspan="6">Error: ${res.message}</td></tr>`;
        }
        if (window.lucide) window.lucide.createIcons();
    } catch (e) {
        tbody.innerHTML = `<tr><td colspan="6">Failed to load endorsed proposals.</td></tr>`;
    }
}

let activeBatchRef = null;

window.viewAllowanceProposal = async function (batchRef, reasonStr) {
    activeBatchRef = batchRef;
    document.getElementById('proposalReasonText').textContent = decodeURIComponent(reasonStr);

    const tbody = document.getElementById('proposalDetailsBody');
    tbody.innerHTML = `<tr><td colspan="4" style="text-align:center;"><i data-lucide="loader-2" class="spin"></i> Loading...</td></tr>`;

    document.getElementById('proposalActionModal').classList.remove('hidden');
    if (window.lucide) lucide.createIcons();

    try {
        const response = await fetch(`be_allowancemgt.php?action=fetch_proposal_details&batch_reference=${batchRef}`);
        const res = await response.json();
        if (res.success) {
            tbody.innerHTML = res.data.map(req => `
                <tr>
                    <td style="font-weight:600;">${req.GradeLevel} / <span style="font-size:12px; color:var(--text-secondary); font-weight:normal;">${req.GradeName}</span></td>
                    <td>${req.AllowanceName}</td>
                    <td style="color:var(--text-secondary); text-decoration:line-through;">\u20B1${parseFloat(req.OldAmount).toLocaleString()}</td>
                    <td style="color:var(--brand-green); font-weight:600;">\u20B1${parseFloat(req.ProposedAmount).toLocaleString()}</td>
                </tr>`).join('');
        } else {
            tbody.innerHTML = `<tr><td colspan="4">Error: ${res.message}</td></tr>`;
        }
    } catch (e) {
        tbody.innerHTML = `<tr><td colspan="4">Failed to load details.</td></tr>`;
    }
};

document.addEventListener('DOMContentLoaded', () => {
    fetchEndorsedProposals();

    document.getElementById('btnCloseProposalModal')?.addEventListener('click', () => {
        document.getElementById('proposalActionModal').classList.add('hidden');
    });

    document.getElementById('btnEndorseProposal')?.addEventListener('click', async () => {
        if (!activeBatchRef) return;
        const result = await Swal.fire({
            title: 'Approve & Send to Finance?',
            text: 'Are you sure you want to approve this proposal? It will be forwarded to Finance for final review and application.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#2563eb',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Approve & Forward'
        });

        if (result.isConfirmed) {
            try {
                const response = await fetch('be_allowancemgt.php', {
                    method: 'POST',
                    body: JSON.stringify({ action: 'approve_proposal', batch_reference: activeBatchRef })
                });
                const res = await response.json();
                if (res.success) {
                    Swal.fire('Approved!', res.message, 'success').then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            } catch (e) {
                Swal.fire('Error', 'Failed to process request.', 'error');
            }
        }
    });

    document.getElementById('btnRejectProposal')?.addEventListener('click', async () => {
        if (!activeBatchRef) return;
        const result = await Swal.fire({
            title: 'Reject Allowance Proposal?',
            text: 'This will discard the proposal completely. Proceed?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Reject'
        });

        if (result.isConfirmed) {
            try {
                const response = await fetch('be_allowancemgt.php', {
                    method: 'POST',
                    body: JSON.stringify({ action: 'reject_proposal', batch_reference: activeBatchRef })
                });
                const res = await response.json();
                if (res.success) {
                    Swal.fire('Rejected!', res.message, 'success');
                    document.getElementById('proposalActionModal').classList.add('hidden');
                    fetchEndorsedProposals();
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            } catch (e) {
                Swal.fire('Error', 'Failed to process request.', 'error');
            }
        }
    });
});
