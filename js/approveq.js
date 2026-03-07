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

// ---------------------------------------------------------
// Universal Proposals Fetching Logic
// ---------------------------------------------------------

async function fetchUniversalProposals() {
    const tableBody = document.getElementById('universalProposalsBody');
    if (!tableBody) return;

    tableBody.innerHTML = `<tr><td colspan="6"><div class="empty-state"><i data-lucide="loader-2" class="spin"></i><p>Loading proposals…</p></div></td></tr>`;
    if (window.lucide) lucide.createIcons();

    try {
        const [salaryRes, statRes, meritRes, allowanceRes, simRes] = await Promise.all([
            fetch('../manager/be_salary_proposal.php?action=fetch_manager_approved').then(r => r.json()).catch(() => ({ success: false, data: [] })),
            fetch('../manager/be_statutorymgt.php?action=fetch_manager_approved').then(r => r.json()).catch(() => ({ success: false, data: [] })),
            fetch('../manager/be_meritmatrixmgt.php?action=fetch_manager_approved').then(r => r.json()).catch(() => ({ success: false, data: [] })),
            fetch('../manager/be_allowancemgt.php?action=fetch_manager_approved').then(r => r.json()).catch(() => ({ success: false, data: [] })),
            fetch('../manager/be_fetch_simulations.php?action=fetch').then(r => r.json()).catch(() => ({ success: false, data: [] }))
        ]);

        let proposals = [];

        if (salaryRes.success && salaryRes.data) {
            salaryRes.data.forEach(p => { p.type = 'salary'; proposals.push(p); });
        }
        if (statRes.success && statRes.data) {
            statRes.data.forEach(p => { p.type = 'statutory'; proposals.push(p); });
        }
        if (meritRes.success && meritRes.data) {
            meritRes.data.forEach(p => { p.type = 'merit'; proposals.push(p); });
        }
        if (allowanceRes.success && allowanceRes.data) {
            allowanceRes.data.forEach(p => { p.type = 'allowance'; proposals.push(p); });
        }
        if (simRes && simRes.success && simRes.data) {
            simRes.data.filter(s => s.Status === 'Approved').forEach(p => {
                p.type = 'simulation';
                proposals.push(p);
            });
        }

        // Sort by CreatedAt descending
        proposals.sort((a, b) => new Date(b.CreatedAt) - new Date(a.CreatedAt));

        if (proposals.length === 0) {
            tableBody.innerHTML = `<tr><td colspan="6">
                <div class="empty-state">
                    <i data-lucide="check-circle"></i>
                    <p>No proposals found</p>
                    <span>Everything is up to date.</span>
                </div></td></tr>`;
            if (window.lucide) lucide.createIcons();
            return;
        }

        tableBody.innerHTML = proposals.map(req => {
            const date = new Date(req.CreatedAt).toLocaleDateString('en-PH', {
                year: 'numeric', month: 'short', day: 'numeric'
            });
            const proposer = req.ProposedByName || 'Analyst';

            let typePill, details, reviewAction;

            if (req.type === 'salary') {
                typePill = `<span class="type-pill" style="background:rgba(16,185,129,0.1); color:var(--brand-green);"><i data-lucide="git-pull-request"></i> Salary Scale</span>`;
                details = `<strong>${req.TotalChanges} Grade(s)</strong> modified`;
                reviewAction = `<button class="btn-review" onclick="viewEndorsedBatch('${req.BatchReference}', '${encodeURIComponent(req.Reason)}')"><i data-lucide="eye"></i> Review</button>`;
            } else if (req.type === 'statutory') {
                typePill = `<span class="type-pill" style="background:rgba(59, 130, 246, 0.1); color:var(--brand-blue);"><i data-lucide="landmark"></i> Statutory</span>`;
                details = `<span style="max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: inline-block;">${req.Reason || 'Statutory updates'}</span>`;
                reviewAction = `<button class="btn-review" onclick="viewStatutoryBatch('${req.BatchReference}', '${encodeURIComponent(req.Reason)}', '${req.ProofPath}')"><i data-lucide="eye"></i> Review</button>`;
            } else if (req.type === 'merit') {
                typePill = `<span class="type-pill" style="background:rgba(139, 92, 246, 0.1); color:var(--brand-purple);"><i data-lucide="trending-up"></i> Merit Matrix</span>`;
                details = `<strong>${req.TotalChanges} Rate(s)</strong> modified`;
                reviewAction = `<button class="btn-review" onclick="viewMeritBatch('${req.BatchReference}', '${encodeURIComponent(req.Reason)}')"><i data-lucide="eye"></i> Review</button>`;
            } else if (req.type === 'allowance') {
                typePill = `<span class="type-pill" style="background:rgba(245, 158, 11, 0.1); color:var(--brand-orange);"><i data-lucide="gift"></i> Allowance</span>`;
                details = `<strong>${req.TotalChanges} Grade(s)</strong> modified`;
                reviewAction = `<button class="btn-review" onclick="viewAllowanceBatch('${req.BatchReference}', '${encodeURIComponent(req.Reason)}')"><i data-lucide="eye"></i> Review</button>`;
            } else if (req.type === 'simulation') {
                typePill = `<span class="type-pill" style="background:rgba(37,99,235,0.1); color:#2563eb;"><i data-lucide="calculator"></i> Comp. Simulation</span>`;
                details = `<strong>${req.CycleName}</strong> cycle`;
                reviewAction = `<button class="btn-review" onclick="viewSimulationBatch('${req.DraftID}')"><i data-lucide="eye"></i> Review</button>`;
            }

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
                <td>${typePill}</td>
                <td>${details}</td>
                <td style="color: var(--text-secondary); font-size:13px;">${date}</td>
                <td><span class="badge badge-success">Mgr. Approved</span></td>
                <td>${reviewAction}</td>
            </tr>`;
        }).join('');

        if (window.lucide) lucide.createIcons();
    } catch (error) {
        console.error('Error fetching universal proposals:', error);
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

    // Reset Lock State for Approval Button
    const btnEndorse = document.getElementById('btnEndorseProposal');
    if (btnEndorse) {
        btnEndorse.disabled = true;
        btnEndorse.style.cursor = 'not-allowed';
        btnEndorse.style.opacity = '0.6';
        btnEndorse.title = 'Please view the Summary Total first';
    }

    try {
        const response = await fetch(`../manager/be_salary_proposal.php?action=fetch_manager_approved_details&batch_reference=${batchRef}`);
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

document.addEventListener("DOMContentLoaded", () => {
    fetchUniversalProposals();

    const proposalModal = document.getElementById('proposalActionModal');
    const btnCloseProposalModal = document.getElementById('btnCloseProposalModal');
    const btnRejectProposal = document.getElementById('btnRejectProposal');
    const btnEndorseProposal = document.getElementById('btnEndorseProposal'); // The "Apply" button

    // Financial Summary Elements
    const btnViewSummary = document.getElementById('btnViewSummary');
    const financialSummaryModal = document.getElementById('financialSummaryModal');
    const btnCloseSummaryModal = document.getElementById('btnCloseSummaryModal');
    const btnAcknowledgeSummary = document.getElementById('btnAcknowledgeSummary');
    const summaryLoadingState = document.getElementById('summaryLoadingState');
    const summaryLoadedState = document.getElementById('summaryLoadedState');

    if (btnCloseSummaryModal) {
        btnCloseSummaryModal.addEventListener('click', () => {
            financialSummaryModal.classList.add('hidden');
        });
    }

    if (btnAcknowledgeSummary) {
        btnAcknowledgeSummary.addEventListener('click', () => {
            financialSummaryModal.classList.add('hidden');
            // Unlock the main approve button
            if (btnEndorseProposal) {
                btnEndorseProposal.disabled = false;
                btnEndorseProposal.style.cursor = 'pointer';
                btnEndorseProposal.style.opacity = '1';
                btnEndorseProposal.title = '';

                // Optional: Flash it to draw attention
                btnEndorseProposal.style.transition = 'transform 0.2s';
                btnEndorseProposal.style.transform = 'scale(1.05)';
                setTimeout(() => { btnEndorseProposal.style.transform = 'scale(1)'; }, 200);
            }
        });
    }

    if (btnViewSummary) {
        btnViewSummary.addEventListener('click', async () => {
            if (!currentBatchRef) return;
            financialSummaryModal.classList.remove('hidden');
            summaryLoadingState.classList.remove('hidden');
            summaryLoadedState.classList.add('hidden');

            try {
                const response = await fetch(`../manager/be_salary_proposal.php?action=fetch_financial_impact&batch_reference=${currentBatchRef}`);
                const result = await response.json();

                if (result.success) {
                    const data = result.data;
                    document.getElementById('statImpactedCount').innerText = data.impactedHeadcount || 0;
                    document.getElementById('statMonthlyIncrease').innerHTML = `&#8369;${parseFloat(data.monthlyIncrease || 0).toLocaleString('en-PH', { minimumFractionDigits: 2 })}`;
                    document.getElementById('statAnnualRequirement').innerHTML = `&#8369;${parseFloat(data.annualRequirement || 0).toLocaleString('en-PH', { minimumFractionDigits: 2 })}`;

                    const detailsBody = document.getElementById('summaryDetailsBody');
                    if (data.gradesImpact.length > 0) {
                        detailsBody.innerHTML = data.gradesImpact.map(g => {
                            return `<tr>
                                <td><span style="font-weight:600;">${g.GradeLevel}</span> <span style="font-size:12px; color:var(--text-secondary);">(${g.GradeName})</span></td>
                                <td><span style="font-weight:600;">${g.TotalHeadcount}</span> <span style="color:var(--brand-red); font-size:12px;">(${g.ImpactedHeadcount} impacted)</span></td>
                                <td style="color:var(--text-secondary);">&#8369;${parseFloat(g.CurrentGrossMonthly).toLocaleString('en-PH', { minimumFractionDigits: 2 })}</td>
                                <td style="font-weight:600;">&#8369;${parseFloat(g.NewGrossMonthly).toLocaleString('en-PH', { minimumFractionDigits: 2 })}</td>
                                <td style="color:var(--brand-green); font-weight:600;">+&#8369;${parseFloat(g.MonthlyIncrease).toLocaleString('en-PH', { minimumFractionDigits: 2 })}</td>
                            </tr>`;
                        }).join('');
                    } else {
                        detailsBody.innerHTML = `<tr><td colspan="5" style="text-align:center; color:var(--text-secondary); padding:20px;">No employees currently found in these grades.</td></tr>`;
                    }

                    summaryLoadingState.classList.add('hidden');
                    summaryLoadedState.classList.remove('hidden');
                } else {
                    summaryLoadingState.innerHTML = `<p style="color:var(--brand-red);">Failed to load financial impact: ${result.message}</p>`;
                }
            } catch (err) {
                console.error('Error fetching financial impact:', err);
                summaryLoadingState.innerHTML = `<p style="color:var(--brand-red);">Failed to calculate financial impact due to network error.</p>`;
            }
        });
    }

    if (btnCloseProposalModal) {
        btnCloseProposalModal.addEventListener('click', () => {
            proposalModal.classList.add('hidden');
        });
    }

    if (btnEndorseProposal) {
        btnEndorseProposal.addEventListener('click', async () => {
            if (!currentBatchRef) return;

            const result = await Swal.fire({
                title: 'Apply Salary Scale Changes?',
                text: "Are you sure you want to approve and apply these changes? This will officially overwrite the current salary scale.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#2563eb', // blue
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, Apply Now'
            });

            if (!result.isConfirmed) return;

            try {
                const response = await fetch('../manager/be_salary_proposal.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'apply_batch', batch_reference: currentBatchRef })
                });
                const res = await response.json();

                if (res.success) {
                    await Swal.fire('Applied!', res.message, 'success');
                    proposalModal.classList.add('hidden');
                    fetchUniversalProposals(); // Reload table
                } else {
                    Swal.fire('Error!', res.message, 'error');
                }
            } catch (error) {
                console.error('Error applying batch:', error);
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
                    fetchUniversalProposals();
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

// ---------------------------------------------------------
// Statutory Proposals Display and Applying Logic
// ---------------------------------------------------------

let currentStatutoryBatchRef = null;

window.viewStatutoryBatch = async function (batchRef, reasonStr, proofPath) {
    currentStatutoryBatchRef = batchRef;
    const reason = decodeURIComponent(reasonStr);
    document.getElementById('statutoryReasonText').innerText = reason || 'No reason provided.';

    const proofLink = document.getElementById('statutoryProofLink');
    if (proofLink) proofLink.href = '../../' + proofPath;

    const tbody = document.getElementById('statutoryDetailsBody');
    tbody.innerHTML = `<tr><td colspan="4" style="text-align:center;">Loading details...</td></tr>`;

    document.getElementById('statutoryActionModal').classList.remove('hidden');

    const btnApprove = document.getElementById('btnApproveStatutory');
    if (btnApprove) {
        btnApprove.disabled = true;
        btnApprove.style.cursor = 'not-allowed';
        btnApprove.style.opacity = '0.6';
        btnApprove.title = 'Please view the Financial Impact Summary first';
    }

    try {
        const response = await fetch(`../manager/be_statutorymgt.php?action=fetch_manager_approved_details&batch_reference=${batchRef}`);
        const result = await response.json();

        if (result.success && result.data.length > 0) {
            tbody.innerHTML = result.data.map(req => {
                const isPercent = req.FieldName.includes('pct') || req.FieldName.includes('rate');
                const symbol = isPercent ? '' : '₱';
                const suffix = isPercent ? '%' : '';
                return `
                <tr>
                    <td><strong>${req.Category}</strong></td>
                    <td><span style="font-size:13px; color:var(--text-secondary);">${req.FieldName.replace(/_/g, ' ')}</span></td>
                    <td style="color:var(--text-secondary); text-decoration:line-through;">${symbol}${parseFloat(req.OldValue).toLocaleString('en-PH', { minimumFractionDigits: isPercent ? 1 : 2 })}${suffix}</td>
                    <td style="color:#3b82f6; font-weight:600;">${symbol}${parseFloat(req.ProposedValue).toLocaleString('en-PH', { minimumFractionDigits: isPercent ? 1 : 2 })}${suffix}</td>
                </tr>`;
            }).join('');
        } else {
            tbody.innerHTML = `<tr><td colspan="4" style="text-align:center; color:var(--text-secondary); padding:20px;">No details found.</td></tr>`;
        }
    } catch (e) {
        tbody.innerHTML = `<tr><td colspan="4" style="text-align:center; color:var(--text-secondary); padding:20px;">Failed to load details.</td></tr>`;
    }
};

document.addEventListener("DOMContentLoaded", () => {
    const statutoryActionModal = document.getElementById('statutoryActionModal');
    const btnCloseStatutoryModal = document.getElementById('btnCloseStatutoryModal');
    const btnRejectStatutory = document.getElementById('btnRejectStatutory');
    const btnApproveStatutory = document.getElementById('btnApproveStatutory');

    // Summary
    const statutorySummaryModal = document.getElementById('statutorySummaryModal');
    const btnCloseStatutorySummaryModal = document.getElementById('btnCloseStatutorySummaryModal');
    const btnViewStatutorySummary = document.getElementById('btnViewStatutorySummary');
    const btnAcknowledgeStatutorySummary = document.getElementById('btnAcknowledgeStatutorySummary');
    const statSummaryLoadingState = document.getElementById('statSummaryLoadingState');
    const statSummaryLoadedState = document.getElementById('statSummaryLoadedState');

    if (btnCloseStatutoryModal) btnCloseStatutoryModal.addEventListener('click', () => statutoryActionModal.classList.add('hidden'));
    if (btnCloseStatutorySummaryModal) btnCloseStatutorySummaryModal.addEventListener('click', () => statutorySummaryModal.classList.add('hidden'));

    if (btnAcknowledgeStatutorySummary) {
        btnAcknowledgeStatutorySummary.addEventListener('click', () => {
            statutorySummaryModal.classList.add('hidden');
            if (btnApproveStatutory) {
                btnApproveStatutory.disabled = false;
                btnApproveStatutory.style.cursor = 'pointer';
                btnApproveStatutory.style.opacity = '1';
                btnApproveStatutory.title = '';
                btnApproveStatutory.style.transition = 'transform 0.2s';
                btnApproveStatutory.style.transform = 'scale(1.05)';
                setTimeout(() => { btnApproveStatutory.style.transform = 'scale(1)'; }, 200);
            }
        });
    }

    if (btnViewStatutorySummary) {
        btnViewStatutorySummary.addEventListener('click', async () => {
            if (!currentStatutoryBatchRef) return;
            statutorySummaryModal.classList.remove('hidden');
            statSummaryLoadingState.classList.remove('hidden');
            statSummaryLoadedState.classList.add('hidden');

            try {
                const response = await fetch(`../manager/be_statutorymgt.php?action=fetch_financial_impact&batch_reference=${currentStatutoryBatchRef}`);
                const result = await response.json();

                if (result.success) {
                    const data = result.data;
                    document.getElementById('statAffectedCount').innerText = data.impactedHeadcount || 0;
                    const mIncER = parseFloat(data.monthlyIncreaseER || 0);
                    const aReqER = parseFloat(data.annualRequirementER || 0);
                    const mIncEE = parseFloat(data.monthlyIncreaseEE || 0);
                    const aReqEE = parseFloat(data.annualRequirementEE || 0);

                    document.getElementById('statMonthlyIncreaseTotalER').innerHTML = (mIncER < 0 ? '-' : '') + `&#8369;${Math.abs(mIncER).toLocaleString('en-PH', { minimumFractionDigits: 2 })}`;
                    document.getElementById('statAnnualRequirementTotalER').innerHTML = (aReqER < 0 ? '-' : '') + `&#8369;${Math.abs(aReqER).toLocaleString('en-PH', { minimumFractionDigits: 2 })}`;

                    document.getElementById('statMonthlyIncreaseTotalEE').innerHTML = (mIncEE < 0 ? '-' : '') + `&#8369;${Math.abs(mIncEE).toLocaleString('en-PH', { minimumFractionDigits: 2 })}`;
                    document.getElementById('statAnnualRequirementTotalEE').innerHTML = (aReqEE < 0 ? '-' : '') + `&#8369;${Math.abs(aReqEE).toLocaleString('en-PH', { minimumFractionDigits: 2 })}`;

                    const detailsBody = document.getElementById('statSummaryDetailsBody');
                    if (data.statutoryImpacts && data.statutoryImpacts.length > 0) {
                        detailsBody.innerHTML = data.statutoryImpacts.map(i => {
                            let html = '';
                            // ER Row
                            html += `<tr>
                                <td><span style="font-weight:600;">${i.Category}</span> <span style="font-size:12px; color:var(--text-secondary);">(Company Cost)</span></td>
                                <td style="color:var(--text-secondary);">${i.ER.OldRate}</td>
                                <td>${i.ER.NewRate}</td>
                                <td style="color:${i.ER.MonthlyIncrease < 0 ? 'var(--brand-green)' : 'var(--brand-red)'}; font-weight:600;">${i.ER.MonthlyIncrease <= 0 ? '' : '+'}&#8369;${i.ER.MonthlyIncrease.toLocaleString('en-PH', { minimumFractionDigits: 2 })}</td>
                            </tr>`;
                            // EE Row
                            html += `<tr style="background: rgba(0,0,0,0.015);">
                                <td><span style="font-weight:600;">${i.Category}</span> <span style="font-size:12px; color:var(--text-secondary);">(Employee Deduction)</span></td>
                                <td style="color:var(--text-secondary);">${i.EE.OldRate}</td>
                                <td>${i.EE.NewRate}</td>
                                <td style="color:${i.EE.MonthlyIncrease < 0 ? 'var(--brand-green)' : 'var(--brand-red)'}; font-weight:600;">${i.EE.MonthlyIncrease <= 0 ? '' : '+'}&#8369;${i.EE.MonthlyIncrease.toLocaleString('en-PH', { minimumFractionDigits: 2 })}</td>
                            </tr>`;
                            return html;
                        }).join('');
                    } else {
                        detailsBody.innerHTML = `<tr><td colspan="4" style="text-align:center; color:var(--text-secondary); padding:20px;">No calculated cost increases.</td></tr>`;
                    }

                    statSummaryLoadingState.classList.add('hidden');
                    statSummaryLoadedState.classList.remove('hidden');
                } else {
                    statSummaryLoadingState.innerHTML = `<p style="color:var(--brand-red);">Failed to load impact: ${result.message}</p>`;
                }
            } catch (err) {
                console.error('Error fetching statutory impact:', err);
                statSummaryLoadingState.innerHTML = `<p style="color:var(--brand-red);">Failed to calculate impact due to network error.</p>`;
            }
        });
    }

    if (btnApproveStatutory) {
        btnApproveStatutory.addEventListener('click', async () => {
            if (!currentStatutoryBatchRef) return;

            const result = await Swal.fire({
                title: 'Apply Statutory Changes?',
                text: "Are you sure you want to approve and apply these changes? This will officially overwrite the current statutory settings.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#2563eb',
                confirmButtonText: 'Yes, Apply Now'
            });

            if (!result.isConfirmed) return;

            try {
                const response = await fetch('../manager/be_statutorymgt.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'apply_batch', batch_reference: currentStatutoryBatchRef })
                });
                const res = await response.json();

                if (res.success) {
                    await Swal.fire('Applied!', res.message, 'success');
                    statutoryActionModal.classList.add('hidden');
                    fetchUniversalProposals();
                } else {
                    Swal.fire('Error!', res.message, 'error');
                }
            } catch (error) {
                Swal.fire('Error!', 'An error occurred.', 'error');
            }
        });
    }

    if (btnRejectStatutory) {
        btnRejectStatutory.addEventListener('click', async () => {
            if (!currentStatutoryBatchRef) return;

            const result = await Swal.fire({
                title: 'Reject Proposals?',
                text: "The requested statutory changes will be discarded.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Yes, Reject'
            });

            if (!result.isConfirmed) return;

            try {
                const response = await fetch('../manager/be_statutorymgt.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'reject_batch', batch_reference: currentStatutoryBatchRef })
                });
                const res = await response.json();

                if (res.success) {
                    Swal.fire('Rejected!', res.message, 'success');
                    statutoryActionModal.classList.add('hidden');
                    fetchUniversalProposals();
                } else {
                    Swal.fire('Error!', res.message, 'error');
                }
            } catch (error) {
                Swal.fire('Error!', 'An error occurred.', 'error');
            }
        });
    }
});

// ---------------------------------------------------------
// Merit Matrix Proposals Display and Applying Logic
// ---------------------------------------------------------

let currentMeritBatchRef = null;

window.viewMeritBatch = async function (batchRef, reasonStr) {
    currentMeritBatchRef = batchRef;
    const reason = decodeURIComponent(reasonStr);
    document.getElementById('meritReasonText').innerText = reason || 'No reason provided.';

    const tbody = document.getElementById('meritDetailsBody');
    tbody.innerHTML = `<tr><td colspan="4" style="text-align:center;">Loading details...</td></tr>`;

    document.getElementById('meritActionModal').classList.remove('hidden');

    const btnApprove = document.getElementById('btnApproveMerit');
    if (btnApprove) {
        btnApprove.disabled = true;
        btnApprove.style.cursor = 'not-allowed';
        btnApprove.style.opacity = '0.6';
        btnApprove.title = 'Please view the Financial Impact Summary first';
    }

    try {
        const response = await fetch(`../manager/be_meritmatrixmgt.php?action=fetch_manager_approved_details&batch_reference=${batchRef}`);
        const result = await response.json();

        if (result.success && result.data.length > 0) {
            tbody.innerHTML = result.data.map(req => {
                return `
                <tr>
                    <td><strong>${req.performance_rating}</strong></td>
                    <td><span style="color:var(--text-secondary);">${req.compa_ratio_range}</span></td>
                    <td style="color:var(--text-secondary); text-decoration:line-through;">${parseFloat(req.OldMaxIncrease || 5.0).toFixed(1)}%</td>
                    <td style="color:#8b5cf6; font-weight:600;">${parseFloat(req.ProposedMaxIncrease).toFixed(1)}%</td>
                </tr>`;
            }).join('');
        } else {
            tbody.innerHTML = `<tr><td colspan="4" style="text-align:center; color:var(--text-secondary); padding:20px;">No details found.</td></tr>`;
        }
    } catch (e) {
        tbody.innerHTML = `<tr><td colspan="4" style="text-align:center; color:var(--text-secondary); padding:20px;">Failed to load details.</td></tr>`;
    }
};

document.addEventListener("DOMContentLoaded", () => {
    const meritActionModal = document.getElementById('meritActionModal');
    const btnCloseMeritModal = document.getElementById('btnCloseMeritModal');
    const btnRejectMerit = document.getElementById('btnRejectMerit');
    const btnApproveMerit = document.getElementById('btnApproveMerit');

    // Summary
    const meritSummaryModal = document.getElementById('meritSummaryModal');
    const btnCloseMeritSummaryModal = document.getElementById('btnCloseMeritSummaryModal');
    const btnViewMeritSummary = document.getElementById('btnViewMeritSummary');
    const btnAcknowledgeMeritSummary = document.getElementById('btnAcknowledgeMeritSummary');
    const meritSummaryLoadingState = document.getElementById('meritSummaryLoadingState');
    const meritSummaryLoadedState = document.getElementById('meritSummaryLoadedState');

    if (btnCloseMeritModal) btnCloseMeritModal.addEventListener('click', () => meritActionModal.classList.add('hidden'));
    if (btnCloseMeritSummaryModal) btnCloseMeritSummaryModal.addEventListener('click', () => meritSummaryModal.classList.add('hidden'));

    if (btnAcknowledgeMeritSummary) {
        btnAcknowledgeMeritSummary.addEventListener('click', () => {
            meritSummaryModal.classList.add('hidden');
            if (btnApproveMerit) {
                btnApproveMerit.disabled = false;
                btnApproveMerit.style.cursor = 'pointer';
                btnApproveMerit.style.opacity = '1';
                btnApproveMerit.title = '';
                btnApproveMerit.style.transition = 'transform 0.2s';
                btnApproveMerit.style.transform = 'scale(1.05)';
                setTimeout(() => { btnApproveMerit.style.transform = 'scale(1)'; }, 200);
            }
        });
    }

    if (btnViewMeritSummary) {
        btnViewMeritSummary.addEventListener('click', async () => {
            if (!currentMeritBatchRef) return;
            meritSummaryModal.classList.remove('hidden');
            meritSummaryLoadingState.classList.remove('hidden');
            meritSummaryLoadedState.classList.add('hidden');

            try {
                const response = await fetch(`../manager/be_meritmatrixmgt.php?action=fetch_financial_impact&batch_reference=${currentMeritBatchRef}`);
                const result = await response.json();

                if (result.success) {
                    const data = result.data;
                    document.getElementById('statMeritHeadcount').innerText = data.eligibleHeadcount || 0;
                    document.getElementById('statMeritMaxExposure').innerHTML = `&#8369;${parseFloat(data.maxBudgetExposure || 0).toLocaleString('en-PH', { minimumFractionDigits: 2 })}`;
                    document.getElementById('statMeritProjCost') ? (document.getElementById('statMeritProjCost').innerHTML = `&#8369;${parseFloat(data.projectedPerformanceCost || 0).toLocaleString('en-PH', { minimumFractionDigits: 2 })}`) : null;
                    document.getElementById('statMeritAnnual') ? (document.getElementById('statMeritAnnual').innerHTML = `&#8369;${parseFloat(data.annualizedOutcome || 0).toLocaleString('en-PH', { minimumFractionDigits: 2 })}`) : null;

                    const detailsBody = document.getElementById('meritSummaryDetailsBody');
                    if (data.gradesImpact && data.gradesImpact.length > 0) {
                        detailsBody.innerHTML = data.gradesImpact.map(i => {
                            const v = parseFloat(i.variance);
                            return `<tr>
                                <td><span style="font-weight:600;">${i.performance_rating}</span></td>
                                <td style="color:var(--text-secondary);">${i.compa_ratio_range}</td>
                                <td style="color:var(--text-secondary);">${i.old_max}</td>
                                <td style="font-weight:600;">${i.new_max}</td>
                                <td style="color:${v < 0 ? 'var(--brand-green)' : (v > 0 ? 'var(--brand-red)' : 'var(--text-secondary)')}; font-weight:600;">${v > 0 ? '+' : ''}${i.variance}</td>
                            </tr>`;
                        }).join('');
                    } else {
                        detailsBody.innerHTML = `<tr><td colspan="5" style="text-align:center; color:var(--text-secondary); padding:20px;">No details available.</td></tr>`;
                    }

                    meritSummaryLoadingState.classList.add('hidden');
                    meritSummaryLoadedState.classList.remove('hidden');
                } else {
                    meritSummaryLoadingState.innerHTML = `<p style="color:var(--brand-red);">Failed to load impact: ${result.message}</p>`;
                }
            } catch (err) {
                console.error('Error fetching merit impact:', err);
                meritSummaryLoadingState.innerHTML = `<p style="color:var(--brand-red);">Failed to calculate impact due to network error.</p>`;
            }
        });
    }

    if (btnApproveMerit) {
        btnApproveMerit.addEventListener('click', async () => {
            if (!currentMeritBatchRef) return;

            const result = await Swal.fire({
                title: 'Apply Merit Matrix Changes?',
                text: "Are you sure you want to approve and apply these changes? This will officially overwrite the current merit matrix.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#8b5cf6',
                confirmButtonText: 'Yes, Apply Now'
            });

            if (!result.isConfirmed) return;

            try {
                const response = await fetch('../manager/be_meritmatrixmgt.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'apply_batch', batch_reference: currentMeritBatchRef })
                });
                const res = await response.json();

                if (res.success) {
                    await Swal.fire('Applied!', res.message, 'success');
                    meritActionModal.classList.add('hidden');
                    fetchUniversalProposals();
                } else {
                    Swal.fire('Error!', res.message, 'error');
                }
            } catch (error) {
                Swal.fire('Error!', 'An error occurred.', 'error');
            }
        });
    }

    if (btnRejectMerit) {
        btnRejectMerit.addEventListener('click', async () => {
            if (!currentMeritBatchRef) return;

            const result = await Swal.fire({
                title: 'Reject Proposals?',
                text: "The requested merit matrix changes will be discarded.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Yes, Reject'
            });

            if (!result.isConfirmed) return;

            try {
                const response = await fetch('../manager/be_meritmatrixmgt.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'reject_batch', batch_reference: currentMeritBatchRef })
                });
                const res = await response.json();

                if (res.success) {
                    Swal.fire('Rejected!', res.message, 'success');
                    meritActionModal.classList.add('hidden');
                    fetchUniversalProposals();
                } else {
                    Swal.fire('Error!', res.message, 'error');
                }
            } catch (error) {
                Swal.fire('Error!', 'An error occurred.', 'error');
            }
        });
    }
});

// ---------------------------------------------------------
// Allowance Proposals Display and Applying Logic
// ---------------------------------------------------------

let currentAllowanceBatchRef = null;

window.viewAllowanceBatch = async function (batchRef, reasonStr) {
    currentAllowanceBatchRef = batchRef;
    const reason = decodeURIComponent(reasonStr);
    document.getElementById('allowanceReasonText').innerText = reason || 'No reason provided.';

    const tbody = document.getElementById('allowanceDetailsBody');
    tbody.innerHTML = `<tr><td colspan="4" style="text-align:center;">Loading details...</td></tr>`;

    document.getElementById('allowanceActionModal').classList.remove('hidden');

    const btnApprove = document.getElementById('btnApproveAllowance');
    if (btnApprove) {
        btnApprove.disabled = true;
        btnApprove.style.cursor = 'not-allowed';
        btnApprove.style.opacity = '0.6';
        btnApprove.title = 'Please view the Financial Impact Summary first';
    }

    try {
        const response = await fetch(`../manager/be_allowancemgt.php?action=fetch_manager_approved_details&batch_reference=${batchRef}`);
        const result = await response.json();

        if (result.success && result.data.length > 0) {
            tbody.innerHTML = result.data.map(req => {
                return `
                <tr>
                    <td><strong>${req.GradeLevel}</strong> <span style="font-size:12px; color:var(--text-secondary);">(${req.GradeName})</span></td>
                    <td><span style="color:var(--text-secondary);">${req.AllowanceName}</span></td>
                    <td style="color:var(--text-secondary); text-decoration:line-through;">&#8369;${parseFloat(req.OldAmount || 0).toLocaleString('en-PH', { minimumFractionDigits: 2 })}</td>
                    <td style="color:#f59e0b; font-weight:600;">&#8369;${parseFloat(req.ProposedAmount).toLocaleString('en-PH', { minimumFractionDigits: 2 })}</td>
                </tr>`;
            }).join('');
        } else {
            tbody.innerHTML = `<tr><td colspan="4" style="text-align:center; color:var(--text-secondary); padding:20px;">No details found.</td></tr>`;
        }
    } catch (e) {
        tbody.innerHTML = `<tr><td colspan="4" style="text-align:center; color:var(--text-secondary); padding:20px;">Failed to load details.</td></tr>`;
    }
};

document.addEventListener("DOMContentLoaded", () => {
    const allowanceActionModal = document.getElementById('allowanceActionModal');
    const btnCloseAllowanceModal = document.getElementById('btnCloseAllowanceModal');
    const btnRejectAllowance = document.getElementById('btnRejectAllowance');
    const btnApproveAllowance = document.getElementById('btnApproveAllowance');

    // Summary
    const allowanceSummaryModal = document.getElementById('allowanceSummaryModal');
    const btnCloseAllowanceSummaryModal = document.getElementById('btnCloseAllowanceSummaryModal');
    const btnViewAllowanceSummary = document.getElementById('btnViewAllowanceSummary');
    const btnAcknowledgeAllowanceSummary = document.getElementById('btnAcknowledgeAllowanceSummary');
    const allowanceSummaryLoadingState = document.getElementById('allowanceSummaryLoadingState');
    const allowanceSummaryLoadedState = document.getElementById('allowanceSummaryLoadedState');

    if (btnCloseAllowanceModal) btnCloseAllowanceModal.addEventListener('click', () => allowanceActionModal.classList.add('hidden'));
    if (btnCloseAllowanceSummaryModal) btnCloseAllowanceSummaryModal.addEventListener('click', () => allowanceSummaryModal.classList.add('hidden'));

    if (btnAcknowledgeAllowanceSummary) {
        btnAcknowledgeAllowanceSummary.addEventListener('click', () => {
            allowanceSummaryModal.classList.add('hidden');
            if (btnApproveAllowance) {
                btnApproveAllowance.disabled = false;
                btnApproveAllowance.style.cursor = 'pointer';
                btnApproveAllowance.style.opacity = '1';
                btnApproveAllowance.title = '';
                btnApproveAllowance.style.transition = 'transform 0.2s';
                btnApproveAllowance.style.transform = 'scale(1.05)';
                setTimeout(() => { btnApproveAllowance.style.transform = 'scale(1)'; }, 200);
            }
        });
    }

    if (btnViewAllowanceSummary) {
        btnViewAllowanceSummary.addEventListener('click', async () => {
            if (!currentAllowanceBatchRef) return;
            allowanceSummaryModal.classList.remove('hidden');
            allowanceSummaryLoadingState.classList.remove('hidden');
            allowanceSummaryLoadedState.classList.add('hidden');

            try {
                const response = await fetch(`../manager/be_allowancemgt.php?action=fetch_financial_impact&batch_reference=${currentAllowanceBatchRef}`);
                const result = await response.json();

                if (result.success) {
                    const data = result.data;
                    document.getElementById('statAllwTotalLiab').innerHTML = `&#8369;${parseFloat(data.totalMonthlyLiability || 0).toLocaleString('en-PH', { minimumFractionDigits: 2 })}`;
                    document.getElementById('statAllwMonthlyChange').innerHTML = `+&#8369;${parseFloat(data.monthlyBudgetChange || 0).toLocaleString('en-PH', { minimumFractionDigits: 2 })}`;
                    document.getElementById('statAllwAnnual').innerHTML = `&#8369;${parseFloat(data.annualizedFunding || 0).toLocaleString('en-PH', { minimumFractionDigits: 2 })}`;

                    const detailsBody = document.getElementById('allowanceSummaryDetailsBody');
                    if (data.gradesImpact && data.gradesImpact.length > 0) {
                        detailsBody.innerHTML = data.gradesImpact.map(i => {
                            return `<tr>
                                <td><span style="font-weight:600;">${i.GradeLevel}</span></td>
                                <td style="color:var(--text-secondary);">&#8369;${parseFloat(i.OldTotalPkg).toLocaleString('en-PH', { minimumFractionDigits: 2 })}</td>
                                <td style="font-weight:600;">&#8369;${parseFloat(i.ProposedTotalPkg).toLocaleString('en-PH', { minimumFractionDigits: 2 })}</td>
                                <td style="color:var(--brand-green);">&#8369;${parseFloat(i.DeMinimis).toLocaleString('en-PH', { minimumFractionDigits: 2 })}</td>
                                <td style="color:var(--brand-red);">&#8369;${parseFloat(i.Taxable).toLocaleString('en-PH', { minimumFractionDigits: 2 })}</td>
                            </tr>`;
                        }).join('');
                    } else {
                        detailsBody.innerHTML = `<tr><td colspan="5" style="text-align:center; color:var(--text-secondary); padding:20px;">No details available.</td></tr>`;
                    }

                    allowanceSummaryLoadingState.classList.add('hidden');
                    allowanceSummaryLoadedState.classList.remove('hidden');
                } else {
                    allowanceSummaryLoadingState.innerHTML = `<p style="color:var(--brand-red);">Failed to load impact: ${result.message}</p>`;
                }
            } catch (err) {
                console.error('Error fetching allowance impact:', err);
                allowanceSummaryLoadingState.innerHTML = `<p style="color:var(--brand-red);">Failed to calculate impact due to network error.</p>`;
            }
        });
    }

    if (btnApproveAllowance) {
        btnApproveAllowance.addEventListener('click', async () => {
            if (!currentAllowanceBatchRef) return;

            const result = await Swal.fire({
                title: 'Apply Allowance Changes?',
                text: "Are you sure you want to approve and apply these changes? This will officially overwrite the current allowances.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f59e0b',
                confirmButtonText: 'Yes, Apply Now'
            });

            if (!result.isConfirmed) return;

            try {
                const response = await fetch('../manager/be_allowancemgt.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'apply_batch', batch_reference: currentAllowanceBatchRef })
                });
                const res = await response.json();

                if (res.success) {
                    await Swal.fire('Applied!', res.message, 'success');
                    allowanceActionModal.classList.add('hidden');
                    fetchUniversalProposals();
                } else {
                    Swal.fire('Error!', res.message, 'error');
                }
            } catch (error) {
                Swal.fire('Error!', 'An error occurred.', 'error');
            }
        });
    }

    if (btnRejectAllowance) {
        btnRejectAllowance.addEventListener('click', async () => {
            if (!currentAllowanceBatchRef) return;

            const result = await Swal.fire({
                title: 'Reject Proposals?',
                text: "The requested allowance changes will be discarded.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Yes, Reject'
            });

            if (!result.isConfirmed) return;

            try {
                const response = await fetch('../manager/be_allowancemgt.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'reject_batch', batch_reference: currentAllowanceBatchRef })
                });
                const res = await response.json();

                if (res.success) {
                    Swal.fire('Rejected!', res.message, 'success');
                    allowanceActionModal.classList.add('hidden');
                    fetchUniversalProposals();
                } else {
                    Swal.fire('Error!', res.message, 'error');
                }
            } catch (error) {
                Swal.fire('Error!', 'An error occurred.', 'error');
            }
        });
    }

    // --- Simulation Handlers ---
    const simulationActionModal = document.getElementById('simulationActionModal');
    const simulationSummaryModal = document.getElementById('simulationSummaryModal');
    const btnCloseSimulationModal = document.getElementById('btnCloseSimulationModal');
    const btnViewSimulationSummary = document.getElementById('btnViewSimulationSummary');
    const btnCloseSimSummary = document.getElementById('btnCloseSimSummary');
    const btnAcknowledgeSimSummary = document.getElementById('btnAcknowledgeSimSummary');
    const btnFinalApproveSimulation = document.getElementById('btnFinalApproveSimulation');
    const btnRejectSimulation = document.getElementById('btnRejectSimulation');

    window.viewSimulationBatch = async function (batchId) {
        currentSimBatchRef = batchId;
        const tbody = document.getElementById('simulationDetailsBody');
        tbody.innerHTML = `<tr><td colspan="6" style="text-align:center;"><i data-lucide="loader-2" class="spin"></i> Loading...</td></tr>`;
        if (window.lucide) lucide.createIcons();

        simulationActionModal.classList.remove('hidden');

        if (btnFinalApproveSimulation) {
            btnFinalApproveSimulation.disabled = true;
            btnFinalApproveSimulation.style.opacity = '0.6';
            btnFinalApproveSimulation.style.cursor = 'not-allowed';
        }

        try {
            const res = await fetch(`../manager/be_fetch_simulations.php?action=details&id=${batchId}`);
            const data = await res.json();
            if (data.success) {
                const empData = JSON.parse(data.data.EmployeeData);
                tbody.innerHTML = empData.map(e => `
                    <tr>
                        <td>
                            <div style="font-weight:600;">${e.name}</div>
                            <div style="font-size:11px; color:var(--text-secondary);">${e.department}</div>
                        </td>
                        <td>${e.grade}</td>
                        <td style="text-align:right;">\u20B1${parseFloat(e.current_salary).toLocaleString()}</td>
                        <td style="text-align:center; color:var(--brand-green); font-weight:600;">${e.prop_pct}%</td>
                        <td style="text-align:right;">\u20B1${parseFloat(e.prop_inc).toLocaleString()}</td>
                        <td style="text-align:right; font-weight:700; color:var(--brand-blue);">\u20B1${parseFloat(e.new_salary).toLocaleString()}</td>
                    </tr>
                `).join('');
            }
        } catch (err) { tbody.innerHTML = `<tr><td colspan="6" style="text-align:center;">Error loading details.</td></tr>`; }
    };

    if (btnCloseSimulationModal) btnCloseSimulationModal.addEventListener('click', () => simulationActionModal.classList.add('hidden'));
    if (btnCloseSimSummary) btnCloseSimSummary.addEventListener('click', () => simulationSummaryModal.classList.add('hidden'));

    if (btnViewSimulationSummary) {
        btnViewSimulationSummary.addEventListener('click', async () => {
            document.getElementById('simSummaryLoading').classList.remove('hidden');
            document.getElementById('simSummaryLoaded').classList.add('hidden');
            simulationSummaryModal.classList.remove('hidden');

            try {
                const res = await fetch(`../manager/be_fetch_simulations.php?action=details&id=${currentSimBatchRef}`);
                const data = await res.json();
                if (data.success) {
                    const empData = JSON.parse(data.data.EmployeeData);
                    let impacted = 0, monthly = 0;
                    empData.forEach(e => { if (parseFloat(e.prop_inc) > 0) impacted++; monthly += parseFloat(e.prop_inc); });

                    document.getElementById('simImpactCount').innerText = impacted;
                    document.getElementById('simMonthlyIncrease').innerText = `\u20B1${monthly.toLocaleString()}`;
                    document.getElementById('simAnnualRequirement').innerText = `\u20B1${(monthly * 12).toLocaleString()}`;
                    if (document.getElementById('simSummaryTitle')) document.getElementById('simSummaryTitle').innerText = data.data.CycleName;

                    document.getElementById('simSummaryLoading').classList.add('hidden');
                    document.getElementById('simSummaryLoaded').classList.remove('hidden');
                }
            } catch (err) { console.error(err); }
        });
    }

    if (btnAcknowledgeSimSummary) {
        btnAcknowledgeSimSummary.addEventListener('click', () => {
            simulationSummaryModal.classList.add('hidden');
            if (btnFinalApproveSimulation) {
                btnFinalApproveSimulation.disabled = false;
                btnFinalApproveSimulation.style.opacity = '1';
                btnFinalApproveSimulation.style.cursor = 'pointer';
            }
        });
    }

    if (btnRejectSimulation) {
        btnRejectSimulation.addEventListener('click', async () => {
            const res = await Swal.fire({
                title: 'Reject Simulation?',
                text: "This will send the proposal back for revision.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Yes, Reject'
            });
            if (res.isConfirmed) {
                const r = await fetch(`../manager/be_fetch_simulations.php?action=reject&id=${currentSimBatchRef}`);
                const data = await r.json();
                if (data.success) {
                    Swal.fire('Rejected!', '', 'success').then(() => location.reload());
                }
            }
        });
    }

    if (btnFinalApproveSimulation) {
        btnFinalApproveSimulation.addEventListener('click', async () => {
            const res = await Swal.fire({
                title: 'Final Apply Salaries?',
                text: "This will officially update employee records. This action is irreversible.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#2563eb',
                confirmButtonText: 'Yes, Apply Now'
            });
            if (res.isConfirmed) {
                try {
                    const r = await fetch('be_finalize_simulation.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ batch_id: currentSimBatchRef })
                    });
                    const data = await r.json();
                    if (data.success) {
                        Swal.fire('Finalized!', data.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Error!', data.message, 'error');
                    }
                } catch (e) { Swal.fire('Error!', 'Network Error', 'error'); }
            }
        });
    }
});
