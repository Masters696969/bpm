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

    // 5. Salary Grade Spread Calculation Logic
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

                if (midInput && min >= 0 && max >= 0) {
                    midInput.value = Math.round((min + max) / 2);
                }

                if (spreadCell && min > 0) {
                    const spread = ((max - min) / min) * 100;
                    spreadCell.innerText = `${spread.toFixed(1)}%`;
                }
            });
        });
    }

    document.querySelectorAll("#salaryGradeTable tbody tr").forEach(row => initScaleCalculations(row));

    // Propose Change Modal Logic
    const btnProposeChange = document.getElementById("btnProposeChange");
    const proposeChangeModal = document.getElementById("proposeChangeModal");
    const closeProposeModalBtn = document.getElementById("closeProposeModalBtn");
    const cancelProposeBtn = document.getElementById("cancelProposeBtn");
    const submitProposalScaleBtn = document.getElementById("submitProposalScaleBtn");

    if (btnProposeChange && proposeChangeModal) {
        btnProposeChange.addEventListener("click", () => {
            proposeChangeModal.style.display = "flex";
            if (window.lucide) window.lucide.createIcons();
        });
    }

    const closeProposeModal = () => {
        if (proposeChangeModal) proposeChangeModal.style.display = "none";
    };

    if (closeProposeModalBtn) closeProposeModalBtn.addEventListener("click", closeProposeModal);
    if (cancelProposeBtn) cancelProposeBtn.addEventListener("click", closeProposeModal);

    if (submitProposalScaleBtn) {
        submitProposalScaleBtn.addEventListener("click", async () => {
            const reason = document.getElementById("proposalReason")?.value.trim() ?? "";
            if (!reason) {
                Swal.fire('Error', 'Please provide a reason for the proposal.', 'error');
                return;
            }

            const proposals = [];
            document.querySelectorAll("#proposeScaleTable tbody tr").forEach(row => {
                const gradeId = row.getAttribute("data-id");
                const minInput = row.querySelector(".prop-min-input");
                const maxInput = row.querySelector(".prop-max-input");

                const propMin = parseFloat(minInput.value) || 0;
                const propMax = parseFloat(maxInput.value) || 0;
                const origMin = parseFloat(minInput.getAttribute("data-original")) || 0;
                const origMax = parseFloat(maxInput.getAttribute("data-original")) || 0;

                if (propMin !== origMin || propMax !== origMax) {
                    proposals.push({ SalaryGradeID: gradeId, ProposedMin: propMin, ProposedMax: propMax });
                }
            });

            if (proposals.length === 0) {
                Swal.fire('No Changes', 'Please modify at least one salary grade before submitting a proposal.', 'warning');
                return;
            }

            submitProposalScaleBtn.innerText = "Submitting...";
            submitProposalScaleBtn.disabled = true;

            try {
                // Use FormData so we can attach the proof file
                const fd = new FormData();
                fd.append('reason', reason);
                fd.append('proposals', JSON.stringify(proposals));

                const proofInput = document.getElementById('proofFileInput');
                if (proofInput && proofInput.files[0]) {
                    fd.append('proof_file', proofInput.files[0]);
                }

                const response = await fetch('backend/be_salary.php', {
                    method: 'POST',
                    body: fd
                });
                const data = await response.json();

                if (data.success) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Salary scale proposal submitted to Supervisor for endorsement.',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                    closeProposeModal();
                    if (document.getElementById("proposalReason")) document.getElementById("proposalReason").value = '';
                    if (proofInput) { proofInput.value = ''; document.getElementById('proofFileBadge')?.classList.remove('visible'); }
                } else {
                    throw new Error(data.message || 'Error saving proposal');
                }
            } catch (error) {
                Swal.fire('Error', error.message, 'error');
            } finally {
                submitProposalScaleBtn.innerText = "Submit Proposal";
                submitProposalScaleBtn.disabled = false;
            }
        });
    }

    // Proof file upload — name display + drag-over
    const proofInput = document.getElementById('proofFileInput');
    const proofBadge = document.getElementById('proofFileBadge');
    const proofBadgeName = document.getElementById('proofBadgeName');
    const proofUploadArea = document.getElementById('proofUploadArea');

    if (proofInput && proofBadge) {
        proofInput.addEventListener('change', () => {
            const file = proofInput.files[0];
            if (file) {
                proofBadgeName.textContent = file.name;
                proofBadge.classList.add('visible');
            } else {
                proofBadge.classList.remove('visible');
            }
            if (window.lucide) window.lucide.createIcons();
        });
    }

    if (proofUploadArea) {
        proofUploadArea.addEventListener('dragover', e => { e.preventDefault(); proofUploadArea.classList.add('drag-over'); });
        proofUploadArea.addEventListener('dragleave', () => proofUploadArea.classList.remove('drag-over'));
        proofUploadArea.addEventListener('drop', () => proofUploadArea.classList.remove('drag-over'));
    }


    // Track Status Logic
    const btnTrackStatus = document.getElementById('btnTrackStatus');
    const trackStatusModal = document.getElementById('trackStatusModal');
    const closeTrackModalBtn = document.getElementById('closeTrackModalBtn');

    const stageAList = document.getElementById('stageAList');
    const stageBDetails = document.getElementById('stageBDetails');
    const trackingBatchesBody = document.getElementById('trackingBatchesBody');
    const btnBackToTrackList = document.getElementById('btnBackToTrackList');

    if (!btnTrackStatus || !trackStatusModal) return;

    // Pagination State
    let salaryTrackingPage = 1;
    const itemsPerPage = 5;
    let allSalaryBatches = [];

    btnTrackStatus.addEventListener('click', () => {
        trackStatusModal.style.display = 'flex';
        stageAList.style.display = 'block';
        stageBDetails.style.display = 'none';
        salaryTrackingPage = 1;
        loadTrackingBatches();
    });

    function renderSalaryTrackingPage() {
        const start = (salaryTrackingPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const pageItems = allSalaryBatches.slice(start, end);

        document.getElementById('salaryTotalEntries').textContent = allSalaryBatches.length;
        document.getElementById('salaryPageRange').textContent = `${allSalaryBatches.length > 0 ? start + 1 : 0}-${Math.min(end, allSalaryBatches.length)}`;
        document.getElementById('prevSalaryPage').disabled = salaryTrackingPage === 1;
        document.getElementById('nextSalaryPage').disabled = end >= allSalaryBatches.length;

        trackingBatchesBody.innerHTML = pageItems.map(b => {
            const date = new Date(b.SubmittedDate).toLocaleDateString();
            const badgeStyle = getBadgeForStatus(b.Status);
            return `
                <tr style="background: var(--surface); transition: var(--transition); border: 1px solid var(--border-color); border-radius: 12px; margin-bottom: 8px; box-shadow: var(--shadow-sm);">
                    <td style="padding: 16px; border-radius: 12px 0 0 12px;">
                        <div style="font-family: monospace; font-size: 11px; color: var(--text-tertiary); margin-bottom: 4px;">REFERENCE</div>
                        <span style="font-weight: 700; color: var(--text-primary); font-size: 13px;">${b.BatchReference.split('_')[1].substring(0, 12)}...</span>
                    </td>
                    <td style="padding: 16px;">
                        <div style="font-size: 12px; font-weight: 600; color: var(--text-primary);">${date}</div>
                    </td>
                    <td style="padding: 16px;">
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <i data-lucide="layers" style="width: 14px; height: 14px; color: var(--text-tertiary);"></i>
                            <span style="font-size: 13px; font-weight: 500;">${b.TotalChanges} Grades</span>
                        </div>
                    </td>
                    <td style="padding: 16px;">
                        <span class="badge ${badgeStyle}" style="padding: 4px 10px; border-radius: 20px; font-size: 11px; text-transform: uppercase; font-weight: 700; letter-spacing: 0.03em;">${b.Status}</span>
                    </td>
                    <td style="padding: 16px; border-radius: 0 12px 12px 0; text-align: right;">
                        <button class="btnViewTrackDetails" data-ref="${b.BatchReference}" style="background: var(--brand-green); color: white; border: none; padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 700; cursor: pointer; transition: var(--transition); display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 4px 10px rgba(44, 160, 120, 0.2);">
                            <i data-lucide="external-link" style="width: 14px; height: 14px;"></i> Details
                        </button>
                    </td>
                </tr>`;
        }).join('');

        document.querySelectorAll('.btnViewTrackDetails').forEach(btn => {
            btn.addEventListener('click', (e) => viewTrackDetails(e.currentTarget.getAttribute('data-ref')));
        });
        if (window.lucide) window.lucide.createIcons();
    }

    document.getElementById('prevSalaryPage').onclick = () => { if (salaryTrackingPage > 1) { salaryTrackingPage--; renderSalaryTrackingPage(); } };
    document.getElementById('nextSalaryPage').onclick = () => { if (salaryTrackingPage * itemsPerPage < allSalaryBatches.length) { salaryTrackingPage++; renderSalaryTrackingPage(); } };

    function loadTrackingBatches() {
        trackingBatchesBody.innerHTML = '<tr><td colspan="5" style="text-align:center; padding: 40px;">Loading proposals...</td></tr>';
        fetch('backend/be_track_proposals.php?action=fetch_all')
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    allSalaryBatches = data.batches;
                    renderSalaryTrackingPage();
                }
            });
    }

    closeTrackModalBtn.addEventListener('click', () => {
        trackStatusModal.style.display = 'none';
    });

    btnBackToTrackList.addEventListener('click', () => {
        stageAList.style.display = 'block';
        stageBDetails.style.display = 'none';
    });

    function viewTrackDetails(batchRef) {
        stageAList.style.display = 'none';
        stageBDetails.style.display = 'block';

        document.getElementById('stepperBatchTitle').textContent = `Batch: ${batchRef}`;
        document.getElementById('stepperBatchReason').textContent = 'Loading details...';

        const steps = ['step1', 'step2', 'step3', 'step4'];
        steps.forEach(s => {
            const el = document.getElementById(s);
            el.className = 'stepper-step step-pending';
            document.getElementById(s + 'Desc').textContent = 'Pending';
        });
        document.getElementById('stepperLineFill').style.width = '0%';
        document.getElementById('stepperLineFill').style.backgroundColor = '#10b981';
        document.getElementById('step4Title').textContent = 'Approved';

        fetch(`backend/be_track_proposals.php?action=fetch_details&batch_reference=${batchRef}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const status = data.details.Status;
                    const proposer = data.details.ProposedByName || 'You';

                    document.getElementById('stepperBatchReason').textContent = data.details.Reason;

                    const el1 = document.getElementById('step1');
                    const el2 = document.getElementById('step2');
                    const el3 = document.getElementById('step3');
                    const el4 = document.getElementById('step4');

                    if (status === 'Pending') {
                        el1.className = 'stepper-step step-completed';
                        document.getElementById('step1Desc').textContent = proposer;

                        el2.className = 'stepper-step step-current';
                        document.getElementById('step2Desc').textContent = 'In Progress';
                        document.getElementById('stepperLineFill').style.width = '33%';

                    } else if (status === 'Endorsed') {
                        el1.className = 'stepper-step step-completed';
                        document.getElementById('step1Desc').textContent = proposer;

                        el2.className = 'stepper-step step-completed';
                        document.getElementById('step2Desc').textContent = 'Reviewed';

                        el3.className = 'stepper-step step-current';
                        document.getElementById('step3Desc').textContent = 'Manager Action';
                        document.getElementById('stepperLineFill').style.width = '66%';

                    } else if (status === 'Applied') {
                        el1.className = 'stepper-step step-completed';
                        document.getElementById('step1Desc').textContent = proposer;
                        el2.className = 'stepper-step step-completed';
                        document.getElementById('step2Desc').textContent = 'Reviewed';
                        el3.className = 'stepper-step step-completed';
                        document.getElementById('step3Desc').textContent = 'Endorsed';

                        el4.className = 'stepper-step step-completed';
                        document.getElementById('step4Title').textContent = 'Approved';
                        document.getElementById('step4Desc').textContent = 'Finalized';
                        document.getElementById('stepperLineFill').style.width = '100%';

                    } else if (status === 'Rejected') {
                        el1.className = 'stepper-step step-completed';
                        document.getElementById('step1Desc').textContent = proposer;
                        el2.className = 'stepper-step step-completed';
                        document.getElementById('step2Desc').textContent = 'Reviewed';
                        el3.className = 'stepper-step step-completed';
                        document.getElementById('step3Desc').textContent = 'Endorsed';

                        el4.className = 'stepper-step step-rejected';
                        document.getElementById('step4Title').textContent = 'Rejected';
                        document.getElementById('step4Desc').textContent = 'Finalized';
                        document.getElementById('stepperLineFill').style.width = '100%';
                        document.getElementById('stepperLineFill').style.backgroundColor = '#ef4444';
                    }
                }
            });
    }

    function getBadgeForStatus(status) {
        if (status === 'Pending') return 'badge-warning';
        if (status === 'Endorsed') return 'badge-info';
        if (status === 'Applied') return 'badge-success';
        if (status === 'Rejected') return 'badge-danger';
        return 'badge-secondary';
    }

    document.querySelectorAll('.rp-close-modal').forEach(btn => {
        btn.addEventListener('click', () => {
            const modal = btn.closest('.modal');
            if (modal) modal.style.display = 'none';
        });
    });


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
