document.addEventListener("DOMContentLoaded", () => {
    // Basic navigation/sidebar setup (Assuming shared UI scripts handle standard things)
    const lucide = window.lucide;
    if (lucide) lucide.createIcons();

    // Render Matrix Table UI
    const meritMatrixTbody = document.getElementById("meritMatrixTbody");
    const proposeMeritTbody = document.getElementById("proposeMeritTbody");

    function renderMatrixTables() {
        if (!window.compConfig || !window.compConfig.meritMatrix) return;

        const matrixData = window.compConfig.meritMatrix;
        let htmlView = "";
        let htmlProp = "";

        // Standard 5 to 1 Rating layout
        const ratings = ["5.0", "4.0", "3.0", "2.0", "1.0"];
        const ranges = ["Low", "Mid", "High"];

        ratings.forEach(rating => {
            htmlView += `<tr><td style="padding:16px; border-bottom:1px solid var(--border-color); border-right:1px solid var(--border-color);"><div style="display:flex; align-items:center; gap:12px;"><div style="width:36px; height:36px; border-radius:8px; background:var(--surface-hover); display:flex; align-items:center; justify-content:center; font-weight:700; color:var(--text-primary); border:1px solid var(--border-color);">${rating}</div><div><div style="font-weight:700; color:var(--text-primary); font-size:14px; margin-bottom:2px;">Rating ${rating}</div><div style="font-size:12px; color:var(--text-secondary);">Performance</div></div></div></td>`;

            htmlProp += `<tr><td style="padding:16px; border-bottom:1px solid var(--border-color); border-right:1px solid var(--border-color);"><strong>${rating} Rating</strong></td>`;

            ranges.forEach(range => {
                const node = (matrixData[rating] && matrixData[rating][range]) ? matrixData[rating][range] : { min_increase_pct: 0, max_increase_pct: 0 };

                // View HTML
                htmlView += `
                <td style="padding:16px; border-bottom:1px solid var(--border-color); ${range !== 'High' ? 'border-right:1px solid var(--border-color);' : ''}">
                    <div style="display:flex; flex-direction:column; gap:8px;">
                        <div style="display:flex; justify-content:space-between; align-items:center; background:var(--surface); padding:8px 12px; border-radius:6px; border:1px solid var(--border-color);">
                            <span style="font-size:11px; text-transform:uppercase; color:var(--text-tertiary); font-weight:600;">Min</span>
                            <span style="font-weight:700; color:var(--text-primary); font-family:monospace; font-size:13px;">${parseFloat(node.min_increase_pct).toFixed(1)}%</span>
                        </div>
                        <div style="display:flex; justify-content:space-between; align-items:center; background:rgba(44,160,120,0.05); padding:8px 12px; border-radius:6px; border:1px solid rgba(44,160,120,0.2);">
                            <span style="font-size:11px; text-transform:uppercase; color:var(--brand-green); font-weight:800;">Max</span>
                            <span style="font-weight:800; color:var(--brand-green); font-family:monospace; font-size:13px;">${parseFloat(node.max_increase_pct).toFixed(1)}%</span>
                        </div>
                    </div>
                </td>`;

                // Propose HTML
                htmlProp += `
                <td style="padding:12px; border-bottom:1px solid var(--border-color); ${range !== 'High' ? 'border-right:1px solid var(--border-color);' : ''}">
                    <div style="display:flex; flex-direction:column; gap:6px;">
                        <div class="input-with-symbol" style="position:relative;">
                            <span style="position:absolute; right:10px; top:50%; transform:translateY(-50%); font-size:12px; color:var(--text-tertiary); font-weight:600;">% Min</span>
                            <input type="number" step="0.1" class="table-input-premium merit-min-prop" data-rating="${rating}" data-range="${range}" data-old-val="${parseFloat(node.min_increase_pct)}" value="${parseFloat(node.min_increase_pct).toFixed(1)}" style="padding-right:45px; width:100%; box-sizing:border-box;">
                        </div>
                        <div class="input-with-symbol" style="position:relative;">
                            <span style="position:absolute; right:10px; top:50%; transform:translateY(-50%); font-size:12px; color:var(--brand-green); font-weight:700;">% Max</span>
                            <input type="number" step="0.1" class="table-input-premium merit-max-prop" style="border-color:rgba(44,160,120,0.3); background:rgba(44,160,120,0.02); color:var(--brand-green); font-weight:700; padding-right:45px; width:100%; box-sizing:border-box;" data-rating="${rating}" data-range="${range}" data-old-val="${parseFloat(node.max_increase_pct)}" value="${parseFloat(node.max_increase_pct).toFixed(1)}">
                        </div>
                    </div>
                </td>`;
            });
            htmlView += `</tr>`;
            htmlProp += `</tr>`;
        });

        if (meritMatrixTbody) meritMatrixTbody.innerHTML = htmlView;
        if (proposeMeritTbody) proposeMeritTbody.innerHTML = htmlProp;
    }

    // Initial Render
    renderMatrixTables();

    // ==========================================
    // --- Merit Matrix Propose Change Logic ---
    // ==========================================
    const btnProposeMeritChange = document.getElementById("btnProposeMeritChange");
    const proposeMeritModal = document.getElementById("proposeMeritModal");
    const closeProposeMeritModalBtn = document.getElementById("closeProposeMeritModalBtn");
    const cancelMeritProposeBtn = document.getElementById("cancelMeritProposeBtn");
    const meritProposalForm = document.getElementById("meritProposalForm");

    if (btnProposeMeritChange && proposeMeritModal) {
        btnProposeMeritChange.addEventListener("click", () => {
            proposeMeritModal.style.display = "flex";
            if (window.lucide) window.lucide.createIcons();
        });
    }
    const closeMeritModal = () => {
        if (proposeMeritModal) {
            proposeMeritModal.style.display = "none";
            if (meritProposalForm) meritProposalForm.reset();
        }
    };
    if (closeProposeMeritModalBtn) closeProposeMeritModalBtn.addEventListener("click", closeMeritModal);
    if (cancelMeritProposeBtn) cancelMeritProposeBtn.addEventListener("click", closeMeritModal);

    if (meritProposalForm) {
        meritProposalForm.addEventListener("submit", async (e) => {
            e.preventDefault();
            const submitBtn = meritProposalForm.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;

            const reason = document.getElementById("meritProposalReason").value.trim();
            if (!reason) {
                Swal.fire('Error', 'Please provide a reason.', 'error');
                return;
            }

            const changes = [];
            const rows = document.querySelectorAll("#proposeMeritTable tbody tr");
            rows.forEach(row => {
                const ratingItem = row.querySelector("td:first-child strong");
                if (ratingItem) {
                    const rating = ratingItem.innerText.split(' ')[0]; // e.g. "5.0"
                    const mins = row.querySelectorAll(".merit-min-prop");
                    const maxs = row.querySelectorAll(".merit-max-prop");
                    mins.forEach((minInput, index) => {
                        const maxInput = maxs[index];
                        const range = minInput.getAttribute('data-range');
                        const minVal = parseFloat(minInput.value) || 0;
                        const maxVal = parseFloat(maxInput.value) || 0;

                        const oldMin = parseFloat(minInput.dataset.oldVal) || 0;
                        const oldMax = parseFloat(maxInput.dataset.oldVal) || 0;

                        // Only capture if values actually changed from the original values
                        if (minVal !== oldMin || maxVal !== oldMax) {
                            changes.push({
                                rating: rating,
                                range: range,
                                min: minVal,
                                max: maxVal
                            });
                        }
                    });
                }
            });

            if (changes.length === 0) {
                Swal.fire('Error', 'No matrix changes detected.', 'error');
                return;
            }

            submitBtn.innerHTML = 'Submitting...';
            submitBtn.disabled = true;

            try {
                const formData = new FormData();
                formData.append('reason', reason);
                formData.append('changes', JSON.stringify(changes));

                const response = await fetch('be_merit_proposal.php', { method: 'POST', body: formData });
                const data = await response.json();

                if (data.success) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Merit proposal submitted successfully!',
                        showConfirmButton: false,
                        timer: 1500,
                        timerProgressBar: true
                    }).then(() => {
                        window.location.reload();
                    });
                    closeMeritModal();
                } else {
                    throw new Error(data.message || 'Submission failed');
                }
            } catch (error) {
                Swal.fire('Error', error.message, 'error');
            } finally {
                submitBtn.innerHTML = originalBtnText;
                submitBtn.disabled = false;
            }
        });
    }

    // ==========================================
    // --- Merit Matrix Tracking Logic ---
    // ==========================================
    const btnTrackMeritStatus = document.getElementById('btnTrackMeritStatus');
    const trackMeritModal = document.getElementById('trackMeritModal');
    const closeTrackMeritBtn = document.getElementById('closeTrackMeritBtn');
    const meritStageAList = document.getElementById('meritStageAList');
    const meritStageBDetails = document.getElementById('meritStageBDetails');
    const meritTrackingBody = document.getElementById('meritTrackingBody');
    const btnBackToMeritList = document.getElementById('btnBackToMeritList');

    let meritTrackingPage = 1;
    let allMeritBatches = [];
    const itemsPerPage = 5;

    function getBadgeForStatus(status) {
        if (status === 'Applied' || status === 'Approved') return 'badge-approved';
        if (status === 'Rejected') return 'badge-rejected';
        if (status === 'Pending') return 'badge-pending';
        return 'badge-active'; // Endorsed or logic check
    }

    function renderMeritTrackingPage() {
        if (!meritTrackingBody) return;
        const start = (meritTrackingPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const pageItems = allMeritBatches.slice(start, end);

        document.getElementById('meritTotalEntries').textContent = allMeritBatches.length;
        document.getElementById('meritPageRange').textContent = `${allMeritBatches.length > 0 ? start + 1 : 0}-${Math.min(end, allMeritBatches.length)}`;
        document.getElementById('prevMeritPage').disabled = meritTrackingPage === 1;
        document.getElementById('nextMeritPage').disabled = end >= allMeritBatches.length;

        meritTrackingBody.innerHTML = pageItems.map(b => {
            const date = new Date(b.SubmittedDate).toLocaleDateString();
            const badgeStyle = getBadgeForStatus(b.Status);
            return `
                <tr style="background: var(--surface); transition: var(--transition); border: 1px solid var(--border-color); border-radius: 12px; margin-bottom: 8px; box-shadow: var(--shadow-sm);">
                    <td style="padding: 16px; border-radius: 12px 0 0 12px;">
                        <div style="font-family: monospace; font-size: 11px; color: var(--text-tertiary); margin-bottom: 4px;">REFERENCE</div>
                        <span style="font-weight: 700; color: var(--text-primary); font-size: 13px;">${b.BatchReference.split('_')[1].substring(0, 12)}...</span>
                    </td>
                    <td style="padding: 16px;">
                        <span class="type-pill" style="background: rgba(139, 92, 246, 0.1); color: #8b5cf6; border: none; font-size: 11px; font-weight: 700;">Merit Matrix</span>
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
                        <button class="btnViewMeritTrack" data-ref="${b.BatchReference}" style="background: #8b5cf6; color: white; border: none; padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 700; cursor: pointer; transition: var(--transition); display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 4px 10px rgba(139, 92, 246, 0.2);">
                            <i data-lucide="external-link" style="width: 14px; height: 14px;"></i> Details
                        </button>
                    </td>
                </tr>`;
        }).join('');

        document.querySelectorAll('.btnViewMeritTrack').forEach(btn => {
            btn.addEventListener('click', (e) => viewMeritTrackDetails(e.currentTarget.getAttribute('data-ref')));
        });
        if (window.lucide) window.lucide.createIcons();
    }

    if (document.getElementById('prevMeritPage')) {
        document.getElementById('prevMeritPage').onclick = () => { if (meritTrackingPage > 1) { meritTrackingPage--; renderMeritTrackingPage(); } };
        document.getElementById('nextMeritPage').onclick = () => { if (meritTrackingPage * itemsPerPage < allMeritBatches.length) { meritTrackingPage++; renderMeritTrackingPage(); } };
    }

    if (btnTrackMeritStatus && trackMeritModal) {
        btnTrackMeritStatus.addEventListener('click', () => {
            trackMeritModal.style.display = 'flex';
            meritStageAList.style.display = 'block';
            meritStageBDetails.style.display = 'none';
            meritTrackingPage = 1;
            loadMeritTrackingBatches();
        });
    }

    function loadMeritTrackingBatches() {
        if (!meritTrackingBody) return;
        meritTrackingBody.innerHTML = '<tr><td colspan="6" style="text-align:center; padding: 60px;">Loading proposals...</td></tr>';

        fetch('be_track_proposals.php?action=fetch_all_merit')
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    allMeritBatches = data.batches;
                    renderMeritTrackingPage();
                } else {
                    meritTrackingBody.innerHTML = '<tr><td colspan="6" style="text-align:center;">Failed to load data</td></tr>';
                }
            });
    }

    if (closeTrackMeritBtn) closeTrackMeritBtn.addEventListener('click', () => trackMeritModal.style.display = 'none');
    if (btnBackToMeritList) {
        btnBackToMeritList.addEventListener('click', () => {
            meritStageAList.style.display = 'block';
            meritStageBDetails.style.display = 'none';
        });
    }

    function viewMeritTrackDetails(batchRef) {
        meritStageAList.style.display = 'none';
        meritStageBDetails.style.display = 'block';

        document.getElementById('meritStepperBatchTitle').textContent = `Batch: ${batchRef}`;
        document.getElementById('meritStepperBatchReason').textContent = 'Loading details...';

        const steps = ['meritStep1', 'meritStep2', 'meritStep3', 'meritStep4'];
        steps.forEach(s => {
            const el = document.getElementById(s);
            if (el) {
                el.className = 'stepper-step step-pending';
                const descEl = document.getElementById(s + 'Desc');
                if (descEl) descEl.textContent = 'Pending';
            }
        });
        const lineFill = document.getElementById('meritStepperLineFill');
        if (lineFill) {
            lineFill.style.width = '0%';
            lineFill.style.backgroundColor = '#8b5cf6';
        }
        document.getElementById('meritStep4Title').textContent = 'Finalized';

        fetch(`be_track_proposals.php?action=fetch_merit_details&batch_reference=${batchRef}`)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.data.length > 0) {
                    const first = data.data[0];
                    const status = first.Status;
                    const proposer = first.ProposedByName || 'Analyst';
                    document.getElementById('meritStepperBatchReason').textContent = first.Reason;

                    const el1 = document.getElementById('meritStep1');
                    const el2 = document.getElementById('meritStep2');
                    const el3 = document.getElementById('meritStep3');
                    const el4 = document.getElementById('meritStep4');
                    const step4Title = document.getElementById('meritStep4Title');

                    // Stepper Logic: Pending -> Reviewed -> Manager Approved -> Applied/Rejected
                    if (status === 'Pending') {
                        if (el1) el1.className = 'stepper-step step-completed';
                        document.getElementById('meritStep1Desc').textContent = proposer;
                        if (el2) el2.className = 'stepper-step step-current';
                        document.getElementById('meritStep2Desc').textContent = 'In Progress';
                        if (lineFill) lineFill.style.width = '33%';
                    } else if (status === 'Reviewed') {
                        if (el1) el1.className = 'stepper-step step-completed';
                        if (el2) el2.className = 'stepper-step step-completed';
                        document.getElementById('meritStep2Desc').textContent = 'Reviewed';
                        if (el3) el3.className = 'stepper-step step-current';
                        document.getElementById('meritStep3Desc').textContent = 'Manager Action';
                        if (lineFill) lineFill.style.width = '66%';
                    } else if (status === 'Manager Approved') {
                        if (el1) el1.className = 'stepper-step step-completed';
                        if (el2) el2.className = 'stepper-step step-completed';
                        if (el3) el3.className = 'stepper-step step-completed';
                        document.getElementById('meritStep3Desc').textContent = 'Approved';
                        if (el4) el4.className = 'stepper-step step-current';
                        if (step4Title) step4Title.textContent = 'Finance Auth';
                        document.getElementById('meritStep4Desc').textContent = 'In Progress';
                        if (lineFill) lineFill.style.width = '100%';
                    } else if (status === 'Applied') {
                        [el1, el2, el3, el4].forEach(e => { if (e) e.className = 'stepper-step step-completed'; });
                        if (step4Title) step4Title.textContent = 'Applied';
                        document.getElementById('meritStep4Desc').textContent = 'Finalized';
                        if (lineFill) lineFill.style.width = '100%';
                    } else if (status === 'Rejected') {
                        [el1, el2, el3].forEach(e => { if (e) e.className = 'stepper-step step-completed'; });
                        if (el4) el4.className = 'stepper-step step-rejected';
                        if (step4Title) step4Title.textContent = 'Rejected';
                        document.getElementById('meritStep4Desc').textContent = 'Finalized';
                        if (lineFill) {
                            lineFill.style.width = '100%';
                            lineFill.style.backgroundColor = '#ef4444';
                        }
                    } else {
                        // Default fallback (e.g. Endorsed or older flows)
                        if (el1) el1.className = 'stepper-step step-completed';
                        if (el2) el2.className = 'stepper-step step-completed';
                        document.getElementById('meritStep2Desc').textContent = 'Reviewed';
                        if (el3) el3.className = 'stepper-step step-current';
                        document.getElementById('meritStep3Desc').textContent = 'Manager Action';
                        if (lineFill) lineFill.style.width = '66%';
                    }

                    const tbody = document.getElementById('meritTrackDetailsTableBody');
                    if (tbody) {
                        tbody.innerHTML = data.data.map(req => {
                            const rangeLbl = (req.compa_ratio_range === 'Low') ? '< 90%' : (req.compa_ratio_range === 'Mid' ? '90-110%' : '> 110%');
                            return `
                                <tr>
                                    <td style="padding: 12px;">
                                        <div style="font-weight: 600; color: var(--text-primary);">${req.performance_rating} Rating</div>
                                        <div style="font-size: 11px; color: var(--text-tertiary);">Ratio: ${rangeLbl}</div>
                                    </td>
                                    <td style="padding: 12px; color: var(--text-secondary); text-decoration: line-through;">
                                        ${parseFloat(req.OldMinIncreasePct).toFixed(1)}%
                                    </td>
                                    <td style="padding: 12px; color: var(--text-secondary); text-decoration: line-through;">
                                        ${parseFloat(req.OldMaxIncreasePct).toFixed(1)}%
                                    </td>
                                    <td style="padding: 12px; color: var(--brand-green); font-weight: 700;">
                                        ${parseFloat(req.ProposedMinIncrease).toFixed(1)}%
                                    </td>
                                    <td style="padding: 12px; color: var(--brand-green); font-weight: 700;">
                                        ${parseFloat(req.ProposedMaxIncrease).toFixed(1)}%
                                    </td>
                                </tr>`;
                        }).join('');
                    }
                }
            });
    }

    // Modal Global Close
    document.addEventListener('click', (e) => {
        if (e.target.closest('.rp-close-modal')) {
            const modal = e.target.closest('.modal');
            if (modal) modal.style.display = 'none';
        }
        if (e.target.classList.contains('modal')) {
            e.target.style.display = 'none';
        }
    });

});
