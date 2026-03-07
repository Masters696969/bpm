document.addEventListener("DOMContentLoaded", () => {
    // Basic setup
    const lucide = window.lucide;
    if (lucide) lucide.createIcons();

    // ==========================================
    // --- Allowance Saving Logic (Optional based on design) ---
    // ==========================================
    document.querySelectorAll(".allowance-val-input").forEach(input => {
        input.addEventListener("change", () => {
            const gradeId = input.getAttribute("data-grade");
            const typeId = input.getAttribute("data-type");
            const amount = input.value;

            input.style.opacity = '0.5';

            const params = new URLSearchParams();
            params.append('grade_id', gradeId);
            params.append('type_id', typeId);
            params.append('amount', amount);

            fetch('save_allowance.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: params
            })
                .then(res => res.json())
                .then(data => {
                    input.style.opacity = '1';
                    if (data.success) {
                        console.log('Allowance saved successfully');
                    } else {
                        console.error('Save failed:', data.error);
                    }
                })
                .catch(err => {
                    input.style.opacity = '1';
                    console.error('Fetch error:', err);
                });
        });
    });

    // ==========================================
    // --- Allowance Propose Change Logic ---
    // ==========================================
    const btnProposeAllowanceChange = document.getElementById("btnProposeAllowanceChange");
    const proposeAllowanceModal = document.getElementById("proposeAllowanceModal");
    const closeProposeAllowanceModalBtn = document.getElementById("closeProposeAllowanceModalBtn");
    const cancelAllowanceProposeBtn = document.getElementById("cancelAllowanceProposeBtn");
    const allowanceProposalForm = document.getElementById("allowanceProposalForm");

    if (btnProposeAllowanceChange && proposeAllowanceModal) {
        btnProposeAllowanceChange.addEventListener("click", () => {
            proposeAllowanceModal.style.display = "flex";
            if (window.lucide) window.lucide.createIcons();
        });
    }
    const closeAllowanceModal = () => {
        if (proposeAllowanceModal) {
            proposeAllowanceModal.style.display = "none";
            if (allowanceProposalForm) allowanceProposalForm.reset();
        }
    }
    if (closeProposeAllowanceModalBtn) closeProposeAllowanceModalBtn.addEventListener("click", closeAllowanceModal);
    if (cancelAllowanceProposeBtn) cancelAllowanceProposeBtn.addEventListener("click", closeAllowanceModal);

    if (allowanceProposalForm) {
        allowanceProposalForm.addEventListener("submit", async (e) => {
            e.preventDefault();
            const submitBtn = allowanceProposalForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;

            const reason = document.getElementById("allowanceProposalReason").value.trim();
            if (!reason) { Swal.fire('Error', 'Provide a reason.', 'error'); return; }

            const changes = [];
            const inputs = document.querySelectorAll('.allowance-prop-input');
            inputs.forEach(input => {
                const gradeId = input.getAttribute('data-grade');
                const typeId = input.getAttribute('data-type');
                const originalVal = parseFloat(input.getAttribute('data-original')) || 0;
                const propVal = parseFloat(input.value) || 0;

                if (propVal !== originalVal) {
                    changes.push({
                        grade_id: gradeId,
                        type_id: typeId,
                        amount: propVal
                    });
                }
            });

            if (changes.length === 0) {
                Swal.fire('Error', 'No changes detected to propose.', 'error');
                return;
            }

            submitBtn.innerHTML = 'Submitting...';
            submitBtn.disabled = true;

            try {
                const fd = new FormData();
                fd.append('reason', reason);
                fd.append('changes', JSON.stringify(changes));
                const res = await fetch('be_allowance_proposal.php', { method: 'POST', body: fd });
                const data = await res.json();

                if (data.success) {
                    Swal.fire({ icon: 'success', title: 'Success', text: 'Allowance proposal submitted.', confirmButtonColor: '#2ca078' }).then(() => {
                        window.location.reload();
                    });
                    closeAllowanceModal();
                } else {
                    throw new Error(data.message);
                }
            } catch (e) {
                Swal.fire('Error', e.message, 'error');
            } finally {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        });
    }

    // ==========================================
    // --- Allowance Tracking Logic ---
    // ==========================================
    const btnTrackAllowanceStatus = document.getElementById('btnTrackAllowanceStatus');
    const trackAllowanceModal = document.getElementById('trackAllowanceModal');
    const closeTrackAllowanceBtn = document.getElementById('closeTrackAllowanceBtn');
    const allowanceStageAList = document.getElementById('allowanceStageAList');
    const allowanceStageBDetails = document.getElementById('allowanceStageBDetails');
    const allowanceTrackingBody = document.getElementById('allowanceTrackingBody');
    const btnBackToAllowanceList = document.getElementById('btnBackToAllowanceList');

    let allowanceTrackingPage = 1;
    let allAllowanceBatches = [];
    const itemsPerPage = 5;

    function getBadgeForStatus(status) {
        if (status === 'Applied' || status === 'Approved') return 'badge-approved';
        if (status === 'Rejected') return 'badge-rejected';
        if (status === 'Pending') return 'badge-pending';
        return 'badge-active';
    }

    function renderAllowanceTrackingPage() {
        if (!allowanceTrackingBody) return;
        const start = (allowanceTrackingPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const pageItems = allAllowanceBatches.slice(start, end);

        document.getElementById('allowanceTotalEntries').textContent = allAllowanceBatches.length;
        document.getElementById('allowancePageRange').textContent = `${allAllowanceBatches.length > 0 ? start + 1 : 0}-${Math.min(end, allAllowanceBatches.length)}`;
        document.getElementById('prevAllowancePage').disabled = allowanceTrackingPage === 1;
        document.getElementById('nextAllowancePage').disabled = end >= allAllowanceBatches.length;

        allowanceTrackingBody.innerHTML = pageItems.map(b => {
            const date = new Date(b.SubmittedDate).toLocaleDateString();
            const badgeStyle = getBadgeForStatus(b.Status);
            return `
                <tr style="background: var(--surface); transition: var(--transition); border: 1px solid var(--border-color); border-radius: 12px; margin-bottom: 8px; box-shadow: var(--shadow-sm);">
                    <td style="padding: 16px; border-radius: 12px 0 0 12px;">
                        <div style="font-family: monospace; font-size: 11px; color: var(--text-tertiary); margin-bottom: 4px;">REFERENCE</div>
                        <span style="font-weight: 700; color: var(--text-primary); font-size: 13px;">${b.BatchReference.split('_')[1].substring(0, 12)}...</span>
                    </td>
                    <td style="padding: 16px;">
                        <span class="type-pill" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b; border: none; font-size: 11px; font-weight: 700;">Allowance</span>
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
                        <button class="btnViewAllowanceTrack" data-ref="${b.BatchReference}" style="background: #f59e0b; color: white; border: none; padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 700; cursor: pointer; transition: var(--transition); display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 4px 10px rgba(245, 158, 11, 0.2);">
                            <i data-lucide="external-link" style="width: 14px; height: 14px;"></i> Details
                        </button>
                    </td>
                </tr>`;
        }).join('');

        document.querySelectorAll('.btnViewAllowanceTrack').forEach(btn => {
            btn.addEventListener('click', (e) => viewAllowanceTrackDetails(e.currentTarget.getAttribute('data-ref')));
        });
        if (window.lucide) window.lucide.createIcons();
    }

    if (document.getElementById('prevAllowancePage')) {
        document.getElementById('prevAllowancePage').onclick = () => { if (allowanceTrackingPage > 1) { allowanceTrackingPage--; renderAllowanceTrackingPage(); } };
        document.getElementById('nextAllowancePage').onclick = () => { if (allowanceTrackingPage * itemsPerPage < allAllowanceBatches.length) { allowanceTrackingPage++; renderAllowanceTrackingPage(); } };
    }

    if (btnTrackAllowanceStatus && trackAllowanceModal) {
        btnTrackAllowanceStatus.addEventListener('click', () => {
            trackAllowanceModal.style.display = 'flex';
            allowanceStageAList.style.display = 'block';
            allowanceStageBDetails.style.display = 'none';
            allowanceTrackingPage = 1;
            loadAllowanceTrackingBatches();
        });
    }

    function loadAllowanceTrackingBatches() {
        if (!allowanceTrackingBody) return;
        allowanceTrackingBody.innerHTML = '<tr><td colspan="6" style="text-align:center; padding: 60px;">Loading proposals...</td></tr>';

        fetch('be_track_proposals.php?action=fetch_all_allowance')
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    allAllowanceBatches = data.batches;
                    renderAllowanceTrackingPage();
                } else {
                    allowanceTrackingBody.innerHTML = '<tr><td colspan="6" style="text-align:center;">Failed to load data</td></tr>';
                }
            });
    }

    if (closeTrackAllowanceBtn) closeTrackAllowanceBtn.addEventListener('click', () => trackAllowanceModal.style.display = 'none');
    if (btnBackToAllowanceList) {
        btnBackToAllowanceList.addEventListener('click', () => {
            allowanceStageAList.style.display = 'block';
            allowanceStageBDetails.style.display = 'none';
        });
    }

    function viewAllowanceTrackDetails(batchRef) {
        allowanceStageAList.style.display = 'none';
        allowanceStageBDetails.style.display = 'block';

        document.getElementById('allowanceStepperBatchTitle').textContent = `Batch: ${batchRef}`;
        document.getElementById('allowanceStepperBatchReason').textContent = 'Loading details...';

        const steps = ['allowanceStep1', 'allowanceStep2', 'allowanceStep3', 'allowanceStep4'];
        steps.forEach(s => {
            const el = document.getElementById(s);
            if (el) {
                el.className = 'stepper-step step-pending';
                const descEl = document.getElementById(s + 'Desc');
                if (descEl) descEl.textContent = 'Pending';
            }
        });
        const lineFill = document.getElementById('allowanceStepperLineFill');
        if (lineFill) {
            lineFill.style.width = '0%';
            lineFill.style.backgroundColor = '#f59e0b';
        }
        document.getElementById('allowanceStep4Title').textContent = 'Finalized';

        fetch(`be_track_proposals.php?action=fetch_allowance_details&batch_reference=${batchRef}`)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.data.length > 0) {
                    const first = data.data[0];
                    const status = first.Status;
                    const proposer = first.ProposedByName || 'Analyst';
                    document.getElementById('allowanceStepperBatchReason').textContent = first.Reason;

                    const el1 = document.getElementById('allowanceStep1');
                    const el2 = document.getElementById('allowanceStep2');
                    const el3 = document.getElementById('allowanceStep3');
                    const el4 = document.getElementById('allowanceStep4');
                    const step4Title = document.getElementById('allowanceStep4Title');

                    // Stepper Logic: Pending -> Reviewed -> Manager Approved -> Applied/Rejected
                    if (status === 'Pending') {
                        if (el1) el1.className = 'stepper-step step-completed';
                        document.getElementById('allowanceStep1Desc').textContent = proposer;
                        if (el2) el2.className = 'stepper-step step-current';
                        document.getElementById('allowanceStep2Desc').textContent = 'In Progress';
                        if (lineFill) lineFill.style.width = '33%';
                    } else if (status === 'Reviewed') {
                        if (el1) el1.className = 'stepper-step step-completed';
                        if (el2) el2.className = 'stepper-step step-completed';
                        document.getElementById('allowanceStep2Desc').textContent = 'Reviewed';
                        if (el3) el3.className = 'stepper-step step-current';
                        document.getElementById('allowanceStep3Desc').textContent = 'Manager Action';
                        if (lineFill) lineFill.style.width = '66%';
                    } else if (status === 'Manager Approved') {
                        if (el1) el1.className = 'stepper-step step-completed';
                        if (el2) el2.className = 'stepper-step step-completed';
                        if (el3) el3.className = 'stepper-step step-completed';
                        document.getElementById('allowanceStep3Desc').textContent = 'Approved';
                        if (el4) el4.className = 'stepper-step step-current';
                        if (step4Title) step4Title.textContent = 'Finance Auth';
                        document.getElementById('allowanceStep4Desc').textContent = 'In Progress';
                        if (lineFill) lineFill.style.width = '100%';
                    } else if (status === 'Applied') {
                        [el1, el2, el3, el4].forEach(e => { if (e) e.className = 'stepper-step step-completed'; });
                        if (step4Title) step4Title.textContent = 'Applied';
                        document.getElementById('allowanceStep4Desc').textContent = 'Finalized';
                        if (lineFill) lineFill.style.width = '100%';
                    } else if (status === 'Rejected') {
                        [el1, el2, el3].forEach(e => { if (e) e.className = 'stepper-step step-completed'; });
                        if (el4) el4.className = 'stepper-step step-rejected';
                        if (step4Title) step4Title.textContent = 'Rejected';
                        document.getElementById('allowanceStep4Desc').textContent = 'Finalized';
                        if (lineFill) {
                            lineFill.style.width = '100%';
                            lineFill.style.backgroundColor = '#ef4444';
                        }
                    } else {
                        // Default fallback (e.g. Endorsed or older flows)
                        if (el1) el1.className = 'stepper-step step-completed';
                        if (el2) el2.className = 'stepper-step step-completed';
                        document.getElementById('allowanceStep2Desc').textContent = 'Reviewed';
                        if (el3) el3.className = 'stepper-step step-current';
                        document.getElementById('allowanceStep3Desc').textContent = 'Manager Action';
                        if (lineFill) lineFill.style.width = '66%';
                    }

                    const tbody = document.getElementById('allowanceTrackDetailsTableBody');
                    if (tbody) {
                        tbody.innerHTML = data.data.map(req => {
                            return `
                                <tr>
                                    <td style="padding: 12px;">
                                        <div style="font-weight: 600; color: var(--text-primary);">${req.GradeLevel} / ${req.GradeName}</div>
                                        <div style="font-size: 11px; color: var(--text-tertiary);">${req.AllowanceName}</div>
                                    </td>
                                    <td style="padding: 12px; color: var(--text-secondary); text-decoration: line-through;">
                                        ₱${parseFloat(req.OldAmount).toLocaleString()}
                                    </td>
                                    <td style="padding: 12px; color: var(--brand-green); font-weight: 700; font-size: 15px;">
                                        ₱${parseFloat(req.ProposedAmount).toLocaleString()}
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
