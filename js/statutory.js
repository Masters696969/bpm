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


document.addEventListener('DOMContentLoaded', () => {
    // --- Statutory Propose Change Modal Logic ---
    const btnProposeStatutoryChange = document.getElementById("btnProposeStatutoryChange");
    const proposeStatutoryModal = document.getElementById("proposeStatutoryModal");
    const closeStatutoryModalBtn = document.getElementById("closeStatutoryModalBtn");
    const cancelStatutoryBtn = document.getElementById("cancelStatutoryBtn");
    const statutoryProposalForm = document.getElementById("statutoryProposalForm");
    const govProofFileInput = document.getElementById("govProofFile");
    const fileNameLabel = document.getElementById("fileNameLabel");

    if (btnProposeStatutoryChange && proposeStatutoryModal) {
        btnProposeStatutoryChange.addEventListener("click", () => {
            proposeStatutoryModal.style.display = "flex";
            if (window.lucide) window.lucide.createIcons();
        });
    }

    const closeStatutoryModal = () => {
        if (proposeStatutoryModal) {
            proposeStatutoryModal.style.display = "none";
            statutoryProposalForm.reset();
            if (fileNameLabel) fileNameLabel.textContent = "Select proof file...";
        }
    };

    if (closeStatutoryModalBtn) closeStatutoryModalBtn.addEventListener("click", closeStatutoryModal);
    if (cancelStatutoryBtn) cancelStatutoryBtn.addEventListener("click", closeStatutoryModal);

    if (govProofFileInput && fileNameLabel) {
        govProofFileInput.addEventListener("change", (e) => {
            const file = e.target.files[0];
            if (file) {
                fileNameLabel.textContent = file.name;
                fileNameLabel.style.color = "var(--brand-green)";
            } else {
                fileNameLabel.textContent = "Select proof file...";
                fileNameLabel.style.color = "var(--text-secondary)";
            }
        });
    }

    if (statutoryProposalForm) {
        statutoryProposalForm.addEventListener("submit", async (e) => {
            e.preventDefault();

            const submitBtn = statutoryProposalForm.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;

            submitBtn.innerHTML = '<i data-lucide="loader-2" class="spin"></i> Submitting...';
            submitBtn.disabled = true;
            if (window.lucide) window.lucide.createIcons();

            const formData = new FormData(statutoryProposalForm);

            try {
                const response = await fetch('be_statutory_proposal.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Proposal Submitted!',
                        text: 'Your statutory change proposal has been sent to the Supervisor for endorsement.',
                        confirmButtonColor: '#2ca078'
                    });
                    closeStatutoryModal();
                } else {
                    throw new Error(data.message || 'Failed to submit proposal');
                }
            } catch (error) {
                Swal.fire('Error', error.message, 'error');
            } finally {
                submitBtn.innerHTML = originalBtnText;
                submitBtn.disabled = false;
                if (window.lucide) window.lucide.createIcons();
            }
        });
    }

    // --- Statutory Tracking Logic ---
    const btnTrackStatutoryStatus = document.getElementById('btnTrackStatutoryStatus');
    const statutoryTrackingModal = document.getElementById('statutoryTrackingModal');
    const closeStatutoryTrackModalBtn = document.getElementById('closeStatutoryTrackModalBtn');
    const statutoryStageAList = document.getElementById('statutoryStageAList');
    const statutoryStageBDetails = document.getElementById('statutoryStageBDetails');
    const statutoryTrackingBody = document.getElementById('statutoryTrackingBody');
    const btnBackToStatutoryList = document.getElementById('btnBackToStatutoryList');

    let allStatutoryBatches = [];
    let statTrackingPage = 1;
    const statItemsPerPage = 5;

    function getBadgeForStatus(status) {
        if (status === 'Pending') return 'status-pending';
        if (status === 'Approved' || status === 'Applied') return 'status-active';
        if (status === 'Rejected') return 'status-inactive';
        return 'status-pending';
    }

    function renderStatutoryTrackingPage() {
        const start = (statTrackingPage - 1) * statItemsPerPage;
        const end = start + statItemsPerPage;
        const pageItems = allStatutoryBatches.slice(start, end);

        document.getElementById('statTotalEntries').textContent = allStatutoryBatches.length;
        document.getElementById('statPageRange').textContent = `${allStatutoryBatches.length > 0 ? start + 1 : 0}-${Math.min(end, allStatutoryBatches.length)}`;
        document.getElementById('prevStatPage').disabled = statTrackingPage === 1;
        document.getElementById('nextStatPage').disabled = end >= allStatutoryBatches.length;

        if (pageItems.length === 0) {
            statutoryTrackingBody.innerHTML = `<tr><td colspan="6" style="text-align:center; padding:60px; color: var(--text-tertiary);">
            <i data-lucide="inbox" style="width: 48px; height: 48px; opacity: 0.2; margin-bottom: 12px;"></i>
            <p>No tracking history available</p>
        </td></tr>`;
            if (window.lucide) window.lucide.createIcons();
            return;
        }

        statutoryTrackingBody.innerHTML = pageItems.map(b => {
            const date = new Date(b.SubmittedDate).toLocaleDateString();
            const badgeStyle = getBadgeForStatus(b.Status);
            return `
            <tr style="background: var(--surface); transition: var(--transition); border: 1px solid var(--border-color); border-radius: 12px; margin-bottom: 8px; box-shadow: var(--shadow-sm);">
                <td style="padding: 16px; border-radius: 12px 0 0 12px;">
                    <div style="font-family: monospace; font-size: 11px; color: var(--text-tertiary); margin-bottom: 4px;">REFERENCE</div>
                    <span style="font-weight: 700; color: var(--text-primary); font-size: 13px;">${b.BatchReference.split('_')[1].substring(0, 12)}...</span>
                </td>
                <td style="padding: 16px;">
                    <span class="type-pill" style="background: rgba(44, 160, 120, 0.1); color: var(--brand-green); border: none; font-size: 11px; font-weight: 700;">${b.Category}</span>
                </td>
                <td style="padding: 16px;">
                    <div style="font-size: 12px; font-weight: 600; color: var(--text-primary);">${date}</div>
                </td>
                <td style="padding: 16px;">
                    <div style="display: flex; align-items: center; gap: 6px;">
                        <i data-lucide="layers" style="width: 14px; height: 14px; color: var(--text-tertiary);"></i>
                        <span style="font-size: 13px; font-weight: 500;">${b.TotalChanges} Adjustments</span>
                    </div>
                </td>
                <td style="padding: 16px;">
                    <span class="badge ${badgeStyle}" style="padding: 4px 10px; border-radius: 20px; font-size: 11px; text-transform: uppercase; font-weight: 700; letter-spacing: 0.03em;">${b.Status}</span>
                </td>
                <td style="padding: 16px; border-radius: 0 12px 12px 0; text-align: right;">
                    <button class="btnViewStatTrack" data-ref="${b.BatchReference}" style="background: var(--brand-green); color: white; border: none; padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 700; cursor: pointer; transition: var(--transition); display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 4px 10px rgba(44, 160, 120, 0.2);">
                        <i data-lucide="external-link" style="width: 14px; height: 14px;"></i> Details
                    </button>
                </td>
            </tr > `;
        }).join('');

        document.querySelectorAll('.btnViewStatTrack').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const ref = e.currentTarget.getAttribute('data-ref');
                viewStatutoryTrackDetails(ref);
            });
        });
        if (window.lucide) window.lucide.createIcons();
    }

    document.getElementById('prevStatPage').onclick = () => { if (statTrackingPage > 1) { statTrackingPage--; renderStatutoryTrackingPage(); } };
    document.getElementById('nextStatPage').onclick = () => { if (statTrackingPage * statItemsPerPage < allStatutoryBatches.length) { statTrackingPage++; renderStatutoryTrackingPage(); } };

    btnTrackStatutoryStatus.addEventListener('click', () => {
        statutoryTrackingModal.style.display = 'flex';
        statutoryStageAList.style.display = 'block';
        statutoryStageBDetails.style.display = 'none';
        statTrackingPage = 1;
        loadStatutoryTrackingBatches();
    });

    function loadStatutoryTrackingBatches() {
        if (!statutoryTrackingBody) return;
        statutoryTrackingBody.innerHTML = '<tr><td colspan="6" style="text-align:center; padding: 60px;">Loading proposals...</td></tr>';

        fetch('be_track_proposals.php?action=fetch_all_statutory')
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    allStatutoryBatches = data.batches;
                    renderStatutoryTrackingPage();
                }
            });
    }

    if (closeStatutoryTrackModalBtn) {
        closeStatutoryTrackModalBtn.addEventListener('click', () => {
            statutoryTrackingModal.style.display = 'none';
        });
    }

    if (btnBackToStatutoryList) {
        btnBackToStatutoryList.addEventListener('click', () => {
            statutoryStageAList.style.display = 'block';
            statutoryStageBDetails.style.display = 'none';
        });
    }

    function viewStatutoryTrackDetails(batchRef) {
        statutoryStageAList.style.display = 'none';
        statutoryStageBDetails.style.display = 'block';

        const titleEl = document.getElementById('statutoryStepperBatchTitle');
        const reasonEl = document.getElementById('statutoryStepperBatchReason');
        if (titleEl) titleEl.textContent = `Batch: ${batchRef} `;
        if (reasonEl) reasonEl.textContent = 'Loading details...';

        // Reset Stepper
        const steps = ['statStep1', 'statStep2', 'statStep3', 'statStep4'];
        steps.forEach(s => {
            const el = document.getElementById(s);
            if (el) {
                el.className = 'stepper-step step-pending';
                const descEl = document.getElementById(s + 'Desc');
                if (descEl) descEl.textContent = 'Pending';
            }
        });
        const lineFill = document.getElementById('statutoryStepperLineFill');
        if (lineFill) {
            lineFill.style.width = '0%';
            lineFill.style.backgroundColor = '#10b981';
        }
        const step4Title = document.getElementById('statStep4Title');
        if (step4Title) step4Title.textContent = 'Approved';

        fetch(`be_track_proposals.php?action=fetch_statutory_details&batch_reference=${batchRef}`)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.data && data.data.length > 0) {
                    const first = data.data[0];
                    const status = first.Status;
                    if (reasonEl) reasonEl.textContent = first.Reason;

                    const el1 = document.getElementById('statStep1');
                    const el2 = document.getElementById('statStep2');
                    const el3 = document.getElementById('statStep3');
                    const el4 = document.getElementById('statStep4');

                    if (status === 'Pending') {
                        if (el1) el1.className = 'stepper-step step-completed';
                        const d1 = document.getElementById('statStep1Desc');
                        if (d1) d1.textContent = 'Analyst';
                        if (el2) el2.className = 'stepper-step step-current';
                        const d2 = document.getElementById('statStep2Desc');
                        if (d2) d2.textContent = 'In Progress';
                        if (lineFill) lineFill.style.width = '33%';
                    } else if (status === 'Endorsed') {
                        if (el1) el1.className = 'stepper-step step-completed';
                        if (el2) el2.className = 'stepper-step step-completed';
                        const d2 = document.getElementById('statStep2Desc');
                        if (d2) d2.textContent = 'Reviewed';
                        if (el3) el3.className = 'stepper-step step-current';
                        const d3 = document.getElementById('statStep3Desc');
                        if (d3) d3.textContent = 'Manager Action';
                        if (lineFill) lineFill.style.width = '66%';
                    } else if (status === 'Applied') {
                        if (el1) el1.className = 'stepper-step step-completed';
                        if (el2) el2.className = 'stepper-step step-completed';
                        if (el3) el3.className = 'stepper-step step-completed';
                        if (el4) el4.className = 'stepper-step step-completed';
                        if (step4Title) step4Title.textContent = 'Approved';
                        const d4 = document.getElementById('statStep4Desc');
                        if (d4) d4.textContent = 'Finalized';
                        if (lineFill) lineFill.style.width = '100%';
                    } else if (status === 'Rejected') {
                        if (el1) el1.className = 'stepper-step step-completed';
                        if (el2) el2.className = 'stepper-step step-completed';
                        if (el3) el3.className = 'stepper-step step-completed';
                        if (el4) el4.className = 'stepper-step step-rejected';
                        if (step4Title) step4Title.textContent = 'Rejected';
                        const d4 = document.getElementById('statStep4Desc');
                        if (d4) d4.textContent = 'Finalized';
                        if (lineFill) {
                            lineFill.style.width = '100%';
                            lineFill.style.backgroundColor = '#ef4444';
                        }
                    }

                    const tbody = document.getElementById('statutoryTrackDetailsTableBody');
                    if (tbody) {
                        tbody.innerHTML = data.data.map(req => {
                            const fieldLower = req.FieldName.toLowerCase();
                            const isPercent = fieldLower.includes('pct') || fieldLower.includes('rate');
                            const symbol = isPercent ? '' : '₱';
                            const suffix = isPercent ? '%' : '';

                            const oldValue = parseFloat(req.OldValue);
                            const newValue = parseFloat(req.ProposedValue);

                            return `
                                <tr>
                                    <td style="padding: 12px;">
                                        <div style="font-weight: 600; color: var(--text-primary);">${req.FieldName.replace(/_/g, ' ').toUpperCase()}</div>
                                        <div style="font-size: 11px; color: var(--text-tertiary);">${req.Category}</div>
                                    </td>
                                    <td style="padding: 12px; color: var(--text-secondary); text-decoration: line-through;">
                                        ${symbol}${oldValue.toLocaleString(undefined, { minimumFractionDigits: isPercent ? 2 : 0 })}${suffix}
                                    </td>
                                    <td style="padding: 12px; color: var(--brand-green); font-weight: 700; font-size: 15px;">
                                        ${symbol}${newValue.toLocaleString(undefined, { minimumFractionDigits: isPercent ? 2 : 0 })}${suffix}
                                    </td>
                                </tr>`;
                        }).join('');
                    }
                }
            });
    }


});
