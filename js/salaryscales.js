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

    // Salary Grade Spread Calculation Logic
    function initScaleCalculations(row) {
        const minInput = row.querySelector(".min-salary-input");
        const maxInput = row.querySelector(".max-salary-input");
        const midInput = row.querySelector(".mid-salary-input");
        const spreadCell = row.querySelector(".spread-cell");

        [minInput, maxInput].forEach(input => {
            if (!input) return;
            input.addEventListener("input", () => {
                const min = parseFloat(minInput.value) || 0;
                const max = parseFloat(maxInput.value) || 0;

                // Update Midpoint
                if (midInput && min >= 0 && max >= 0) {
                    midInput.value = Math.round((min + max) / 2);
                }

                // Update Spread
                if (spreadCell && min > 0) {
                    const spread = ((max - min) / min) * 100;
                    spreadCell.innerText = `${spread.toFixed(1)}%`;
                }
            });
        });
    }

    // Initialize existing rows
    document.querySelectorAll("#salaryGradeTable tbody tr").forEach(row => initScaleCalculations(row));

    // Save Scales Logic
    const saveScalesBtn = document.getElementById("saveScalesBtn");
    if (saveScalesBtn) {
        saveScalesBtn.addEventListener("click", async () => {
            const result = await Swal.fire({
                title: 'Save Changes?',
                text: "Are you want to save this? This can affect the current salary scale.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#2ca078',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, Save Changes',
                reverseButtons: true
            });

            if (result.isConfirmed) {
                const grades = [];
                document.querySelectorAll("#salaryGradeTable tbody tr").forEach(row => {
                    grades.push({
                        id: row.getAttribute("data-id"),
                        min: row.querySelector(".min-salary-input").value,
                        max: row.querySelector(".max-salary-input").value
                    });
                });

                try {
                    const response = await fetch('save_salary_grade.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ grades: grades })
                    });
                    const data = await response.json();

                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Saved!',
                            text: 'Salary scales updated successfully.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        throw new Error(data.error || 'Failed to save');
                    }
                } catch (error) {
                    Swal.fire('Error', error.message, 'error');
                }
            }
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

// ---------------------------------------------------------
// Endorsed Salary Proposals Fetching and Applying Logic
// ---------------------------------------------------------
async function fetchEndorsedProposals() {
    const tableBody = document.getElementById('endorsedProposalsBody');
    if (!tableBody) return;

    try {
        const response = await fetch('../manager/be_salary_proposal.php?action=fetch_endorsed');
        const result = await response.json();

        if (result.success) {
            const requests = result.data;

            if (requests.length === 0) {
                tableBody.innerHTML = `<tr><td colspan="6">
                    <div class="empty-state">
                        <i data-lucide="check-circle"></i>
                        <p>No endorsed proposals found</p>
                        <span>Everything is up to date.</span>
                    </div></td></tr>`;
                if (window.lucide) lucide.createIcons();
                return;
            }

            tableBody.innerHTML = requests.map(req => {
                const date = new Date(req.CreatedAt).toLocaleDateString('en-PH', {
                    year: 'numeric', month: 'short', day: 'numeric'
                });
                const proposer = req.ProposedByName || 'Analyst';

                return `
                <tr class="req-row">
                    <td>
                        <div class="emp-cell">
                            <div class="emp-avatar" style="background:var(--brand-blue); color:white;">${proposer.substring(0, 2).toUpperCase()}</div>
                            <div>
                                <div class="emp-name">${proposer}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="type-pill" style="background:rgba(16,185,129,0.1); color:var(--brand-green);">
                            <i data-lucide="git-pull-request"></i>
                            Salary Scale
                        </span>
                    </td>
                    <td><strong>${req.TotalChanges} Grade(s)</strong> modified</td>
                    <td style="color: var(--text-secondary); font-size:13px;">${date}</td>
                    <td><span class="badge badge-warning">Endorsed</span></td>
                    <td>
                        <button class="btn-review" onclick="viewEndorsedBatch('${req.BatchReference}', '${encodeURIComponent(req.Reason)}')">
                            <i data-lucide="eye"></i> Review
                        </button>
                    </td>
                </tr>`;
            }).join('');

            if (window.lucide) lucide.createIcons();
        } else {
            tableBody.innerHTML = `<tr><td colspan="6" style="text-align:center;color:var(--text-secondary);padding:32px;">Error: ${result.message}</td></tr>`;
        }
    } catch (error) {
        console.error('Error fetching proposals:', error);
        tableBody.innerHTML = `<tr><td colspan="6" style="text-align:center;padding:32px;">Failed to load requests.</td></tr>`;
    }
}

let currentBatchRef = null;

window.viewEndorsedBatch = async function (batchRef, reasonStr) {
    currentBatchRef = batchRef;
    const reason = decodeURIComponent(reasonStr);
    document.getElementById('proposalReasonText').innerText = reason || 'No reason provided.';

    const tbody = document.getElementById('proposalDetailsBody');
    tbody.innerHTML = `<tr><td colspan="6" style="text-align:center;">Loading details...</td></tr>`;

    document.getElementById('proposalActionModal').classList.remove('hidden');

    try {
        const response = await fetch(`../manager/be_salary_proposal.php?action=fetch_proposal_details&batch_reference=${batchRef}`);
        const result = await response.json();

        if (result.success && result.data.length > 0) {
            tbody.innerHTML = result.data.map(item => {
                const oldMinStr = parseFloat(item.OldMin).toLocaleString('en-PH', { minimumFractionDigits: 2 });
                const oldMaxStr = parseFloat(item.OldMax).toLocaleString('en-PH', { minimumFractionDigits: 2 });
                const newMinStr = parseFloat(item.ProposedMinSalary).toLocaleString('en-PH', { minimumFractionDigits: 2 });
                const newMaxStr = parseFloat(item.ProposedMaxSalary).toLocaleString('en-PH', { minimumFractionDigits: 2 });

                // compute diff styles
                const isMinChanged = parseFloat(item.ProposedMinSalary) !== parseFloat(item.OldMin);
                const isMaxChanged = parseFloat(item.ProposedMaxSalary) !== parseFloat(item.OldMax);

                return `
                <tr>
                    <td><strong>${item.GradeLevel}</strong></td>
                    <td><span style="font-size:13px; color:var(--text-secondary);">${item.GradeName}</span></td>
                    <td class="text-muted" style="text-decoration: ${isMinChanged ? 'line-through' : 'none'};">&#8369;${oldMinStr}</td>
                    <td style="${isMinChanged ? 'color:var(--brand-green); font-weight:600;' : 'color:var(--text-secondary);'}">&#8369;${newMinStr}</td>
                    <td class="text-muted" style="text-decoration: ${isMaxChanged ? 'line-through' : 'none'};">&#8369;${oldMaxStr}</td>
                    <td style="${isMaxChanged ? 'color:var(--brand-green); font-weight:600;' : 'color:var(--text-secondary);'}">&#8369;${newMaxStr}</td>
                </tr>`;
            }).join('');
        } else {
            tbody.innerHTML = `<tr><td colspan="6" style="text-align:center; color:var(--text-secondary); padding:20px;">No detail records found for this batch.</td></tr>`;
        }
    } catch (e) {
        tbody.innerHTML = `<tr><td colspan="6" style="text-align:center; color:var(--text-secondary); padding:20px;">Failed to load details.</td></tr>`;
    }
};

// Modal buttons logic
document.addEventListener("DOMContentLoaded", () => {
    fetchEndorsedProposals();

    const proposalModal = document.getElementById('proposalActionModal');
    const btnCloseProposalModal = document.getElementById('btnCloseProposalModal');
    const btnRejectProposal = document.getElementById('btnRejectProposal');
    const btnEndorseProposal = document.getElementById('btnEndorseProposal');

    if (btnCloseProposalModal) {
        btnCloseProposalModal.addEventListener('click', () => {
            proposalModal.classList.add('hidden');
        });
    }

    if (btnEndorseProposal) {
        btnEndorseProposal.addEventListener('click', async () => {
            if (!currentBatchRef) return;

            const result = await Swal.fire({
                title: 'Approve & Send to Finance?',
                text: "Are you sure you want to approve this proposal? It will be forwarded to Finance for final application.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2563eb', // blue
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, Send to Finance'
            });

            if (!result.isConfirmed) return;

            try {
                const response = await fetch('../manager/be_salary_proposal.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'manager_approve_batch', batch_reference: currentBatchRef })
                });
                const res = await response.json();

                if (res.success) {
                    await Swal.fire('Approved!', res.message, 'success');
                    proposalModal.classList.add('hidden');
                    fetchEndorsedProposals(); // Reload table
                } else {
                    Swal.fire('Error!', res.message, 'error');
                }
            } catch (error) {
                console.error('Error approving batch:', error);
                Swal.fire('Error!', 'An error occurred.', 'error');
            }
        });
    }

    if (btnRejectProposal) {
        btnRejectProposal.addEventListener('click', async () => {
            if (!currentBatchRef) return;

            const result = await Swal.fire({
                title: 'Reject Proposals?',
                text: "The requested salary changes will be discarded.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, Reject'
            });

            if (!result.isConfirmed) return;

            try {
                const response = await fetch('../manager/be_salary_proposal.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'reject_batch', batch_reference: currentBatchRef })
                });
                const res = await response.json();

                if (res.success) {
                    Swal.fire('Rejected!', res.message, 'success');
                    proposalModal.classList.add('hidden');
                    fetchEndorsedProposals();
                } else {
                    Swal.fire('Error!', res.message, 'error');
                }
            } catch (error) {
                console.error('Error rejecting batch:', error);
                Swal.fire('Error!', 'An error occurred.', 'error');
            }
        });
    }
});
