document.addEventListener('DOMContentLoaded', () => {
    fetchPendingRequests();
    if (window.lucide) lucide.createIcons();

    // Modal Elements for Employee Information Requests
    const modal = document.getElementById('requestActionModal');
    const btnClose = document.getElementById('btnCloseActionModal');
    const btnEndorse = document.getElementById('btnEndorse');
    let currentRequestId = null;

    if (btnClose) {
        btnClose.addEventListener('click', () => {
            modal.classList.add('hidden');
        });
    }

    if (btnEndorse) {
        btnEndorse.addEventListener('click', () => processRequest(currentRequestId, 'endorse_request'));
    }

    window.viewRequest = function (requestId, dataStr, proofPath) {
        currentRequestId = requestId;
        const data = JSON.parse(decodeURIComponent(dataStr));
        const container = document.getElementById('requestDetailsBody');

        let html = `
            <div class="rem-section-hdr rem-shdr-blue">
                <i data-lucide="file-diff"></i> Requested Changes
            </div>
            <div class="rem-fields">
        `;

        const entries = Object.entries(data).filter(([k, v]) => v);

        if (entries.length === 0) {
            html += `<div class="rem-field full"><div style="text-align:center; padding: 20px; font-size: 13px; color: var(--text-secondary);">No data fields changed.</div></div>`;
        } else {
            for (let i = 0; i < entries.length; i += 2) {
                html += `<div class="rem-row">`;
                html += `
                    <div class="rem-field ${i === entries.length - 1 ? 'full' : ''}">
                        <label>${formatLabel(entries[i][0])}</label>
                        <div class="rem-input" style="background: var(--surface-hover); min-height: 35px; border-color: transparent;">${entries[i][1]}</div>
                    </div>
                `;
                if (i + 1 < entries.length) {
                    html += `
                        <div class="rem-field">
                            <label>${formatLabel(entries[i + 1][0])}</label>
                            <div class="rem-input" style="background: var(--surface-hover); min-height: 35px; border-color: transparent;">${entries[i + 1][1]}</div>
                        </div>
                    `;
                }
                html += `</div>`;
            }
        }
        html += `</div>`;

        if (proofPath && proofPath !== 'null' && proofPath !== 'undefined') {
            const absoluteProofPath = '../../' + proofPath;
            const isImage = proofPath.match(/\.(jpg|jpeg|png|gif)$/i);

            html += `
                <div class="rem-section" style="margin-top: 30px;">
                    <div class="rem-section-hdr rem-shdr-green">
                        <i data-lucide="file-check-2"></i> Validation Proof
                    </div>
                    <div class="rem-fields">
                        <div class="rem-field full" style="border-bottom: 0;">
            `;

            if (isImage) {
                html += `
                    <div style="border: 1px solid var(--border-color); border-radius: 8px; overflow: hidden; max-height: 400px; display: flex; justify-content: center; background: var(--surface-hover);">
                        <img src="${absoluteProofPath}" alt="Validation Proof" style="max-width: 100%; max-height: 400px; object-fit: contain;">
                    </div>
                 `;
            } else {
                html += `
                     <div style="display: flex; align-items: center; justify-content: space-between; padding: 16px; background: rgba(44, 160, 120, .08); border-radius: 10px; border: 1px solid rgba(44, 160, 120, .2);">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 40px; height: 40px; border-radius: 8px; background: #ef4444; color: white; display: flex; align-items: center; justify-content: center;">
                                <i data-lucide="file-text"></i>
                            </div>
                            <div>
                                <div style="font-size: 14px; font-weight: 600; color: var(--text-primary);">Document Proof</div>
                                <div style="font-size: 12px; color: var(--text-secondary);">PDF File attached</div>
                            </div>
                        </div>
                        <a href="${absoluteProofPath}" target="_blank" style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; background: var(--surface); border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px; font-weight: 600; color: var(--text-primary); text-decoration: none;">
                            <i data-lucide="download" style="width: 14px; height: 14px;"></i> View / Download
                        </a>
                     </div>
                 `;
            }
            html += `</div></div>`;
        }

        container.innerHTML = html;
        modal.classList.remove('hidden');
        if (window.lucide) lucide.createIcons();
    };
});

async function fetchPendingRequests() {
    const tableBody = document.getElementById('requestsTableBody');
    if (!tableBody) return;

    try {
        const [infoRes, propRes, statRes, meritRes, allowanceRes, simRes] = await Promise.all([
            fetch('be_pending.php?action=fetch_pending').then(r => r.json()).catch(() => ({ success: false, data: [] })),
            fetch('be_pending.php?action=fetch_proposals').then(r => r.json()).catch(() => ({ success: false, data: [] })),
            fetch('be_pending.php?action=fetch_statutory_proposals').then(r => r.json()).catch(() => ({ success: false, data: [] })),
            fetch('be_pending.php?action=fetch_merit_proposals').then(r => r.json()).catch(() => ({ success: false, data: [] })),
            fetch('be_pending.php?action=fetch_allowance_proposals').then(r => r.json()).catch(() => ({ success: false, data: [] })),
            fetch('be_pending.php?action=fetch_simulations').then(r => r.json()).catch(() => ({ success: false, data: [] }))
        ]);

        let allRequests = [];

        if (infoRes.success) {
            allRequests = allRequests.concat(infoRes.data.map(req => ({
                id: req.RequestID,
                type: 'info_update',
                requestType: req.RequestType,
                date: new Date(req.RequestDate),
                status: req.Status,
                firstName: req.FirstName,
                lastName: req.LastName,
                department: req.DepartmentName,
                dataStr: encodeURIComponent(req.RequestData),
                proofPath: req.ProofPath
            })));
        }

        if (propRes.success) {
            allRequests = allRequests.concat(propRes.data.map(req => {
                const parts = (req.ProposedByName || 'Analyst').split(' ');
                return {
                    id: req.BatchReference,
                    type: 'salary_proposal',
                    requestType: 'Salary Scale',
                    date: new Date(req.CreatedAt),
                    status: req.Status,
                    firstName: parts[0],
                    lastName: parts.slice(1).join(' '),
                    department: 'Compensation',
                    totalChanges: req.TotalChanges || 0,
                    reason: req.Reason
                };
            }));
        }

        if (statRes.success) {
            allRequests = allRequests.concat(statRes.data.map(req => {
                const parts = (req.ProposedByName || 'Analyst').split(' ');
                return {
                    id: req.BatchReference,
                    type: 'statutory_proposal',
                    requestType: 'Statutory Adjust',
                    date: new Date(req.CreatedAt),
                    status: req.Status,
                    firstName: parts[0],
                    lastName: parts.slice(1).join(' '),
                    department: 'Compliance',
                    totalChanges: req.TotalChanges || 0,
                    reason: req.Reason,
                    proofPath: req.ProofPath
                };
            }));
        }

        if (meritRes.success) {
            allRequests = allRequests.concat(meritRes.data.map(req => {
                const parts = (req.ProposedByName || 'Analyst').split(' ');
                return {
                    id: req.BatchReference,
                    type: 'merit_proposal',
                    requestType: 'Merit Matrix',
                    date: new Date(req.CreatedAt),
                    status: req.Status,
                    firstName: parts[0],
                    lastName: parts.slice(1).join(' '),
                    department: 'Compensation',
                    totalChanges: req.TotalChanges || 0,
                    reason: req.Reason
                };
            }));
        }

        if (allowanceRes.success) {
            allRequests = allRequests.concat(allowanceRes.data.map(req => {
                const parts = (req.ProposedByName || 'Analyst').split(' ');
                return {
                    id: req.BatchReference,
                    type: 'allowance_proposal',
                    requestType: 'Allowance Adjust',
                    date: new Date(req.CreatedAt),
                    status: req.Status,
                    firstName: parts[0],
                    lastName: parts.slice(1).join(' '),
                    department: 'Compensation',
                    totalChanges: req.TotalChanges || 0,
                    reason: req.Reason
                };
            }));
        }

        if (simRes && simRes.success) {
            allRequests = allRequests.concat(simRes.data.map(req => {
                return {
                    id: req.BatchReference,
                    type: 'simulation_proposal',
                    requestType: 'Comp. Simulation',
                    date: new Date(req.CreatedAt),
                    status: 'Pending Review',
                    firstName: req.ProposedByName || 'Analyst',
                    lastName: '',
                    department: req.Department,
                    totalCost: req.TotalCost,
                    cycleName: req.CycleName
                };
            }));
        }

        allRequests.sort((a, b) => b.date - a.date);

        if (allRequests.length === 0) {
            tableBody.innerHTML = `<tr><td colspan="5"><div class="empty-state"><i data-lucide="inbox"></i><p>No pending requests found</p></div></td></tr>`;
            if (window.lucide) lucide.createIcons();
            return;
        }

        tableBody.innerHTML = allRequests.map(req => {
            const initials = ((req.firstName ? req.firstName[0] : '') + (req.lastName ? req.lastName[0] : '')).toUpperCase() || 'AN';
            const name = (req.firstName + ' ' + req.lastName).trim() || 'Analyst';
            const dateStr = req.date.toLocaleDateString('en-PH', { year: 'numeric', month: 'short', day: 'numeric' });

            let actionBtn = '';
            let requestTypePill = '';

            if (req.type === 'info_update') {
                requestTypePill = `<span class="type-pill"><i data-lucide="file-pen-line"></i> ${req.requestType}</span>`;
                actionBtn = `<button class="btn-review" onclick="viewRequest(${req.id}, '${req.dataStr}', '${req.proofPath}')"><i data-lucide="eye"></i> View & Endorse</button>`;
            } else if (req.type === 'salary_proposal') {
                requestTypePill = `<span class="type-pill" style="background:rgba(16,185,129,0.1); color:var(--brand-green);"><i data-lucide="git-pull-request"></i> ${req.requestType}</span>`;
                actionBtn = `<button class="btn-review" style="background:var(--brand-green); border-color:var(--brand-green); color:white;" onclick="viewProposalBatch('${req.id}', '${encodeURIComponent(req.reason)}')"><i data-lucide="eye"></i> View & Endorse</button>`;
            } else if (req.type === 'statutory_proposal') {
                requestTypePill = `<span class="type-pill" style="background:rgba(59,130,246,0.1); color:#3b82f6;"><i data-lucide="landmark"></i> ${req.requestType}</span>`;
                actionBtn = `<button class="btn-review" style="background:#3b82f6; border-color:#3b82f6; color:white;" onclick="viewStatutoryProposal('${req.id}', '${encodeURIComponent(req.reason)}', '${req.proofPath}')"><i data-lucide="eye"></i> View & Endorse</button>`;
            } else if (req.type === 'merit_proposal') {
                requestTypePill = `<span class="type-pill" style="background:rgba(139,92,246,0.1); color:#8b5cf6;"><i data-lucide="trending-up"></i> ${req.requestType}</span>`;
                actionBtn = `<button class="btn-review" style="background:#8b5cf6; border-color:#8b5cf6; color:white;" onclick="viewMeritProposal('${req.id}', '${encodeURIComponent(req.reason)}')"><i data-lucide="eye"></i> View & Endorse</button>`;
            } else if (req.type === 'simulation_proposal') {
                requestTypePill = `<span class="type-pill" style="background:rgba(37,99,235,0.1); color:#2563eb;"><i data-lucide="calculator"></i> ${req.requestType}</span>`;
                actionBtn = `<button class="btn-review" style="background:#2563eb; border-color:#2563eb; color:white;" onclick="viewSimulation('${req.id}')"><i data-lucide="eye"></i> View & Endorse</button>`;
            } else {
                requestTypePill = `<span class="type-pill" style="background:rgba(245,158,11,0.1); color:#f59e0b;"><i data-lucide="gift"></i> ${req.requestType}</span>`;
                actionBtn = `<button class="btn-review" style="background:#f59e0b; border-color:#f59e0b; color:white;" onclick="viewAllowanceProposal('${req.id}', '${encodeURIComponent(req.reason)}')"><i data-lucide="eye"></i> View & Endorse</button>`;
            }

            return `
            <tr class="req-row">
                <td><div class="emp-cell"><div class="emp-avatar">${initials}</div><div><div class="emp-name">${name}</div><div class="emp-dept">${req.department}</div></div></div></td>
                <td>${requestTypePill}</td>
                <td style="color: var(--text-secondary); font-size:13px;">${dateStr}</td>
                <td><span class="badge badge-warning">Pending Review</span></td>
                <td>${actionBtn}</td>
            </tr>`;
        }).join('');

        if (window.lucide) lucide.createIcons();
    } catch (error) {
        console.error('Error fetching requests:', error);
        tableBody.innerHTML = `<tr><td colspan="5" style="text-align:center;padding:32px;">Failed to load requests.</td></tr>`;
    }
}

async function processRequest(requestId, action) {
    if (!requestId) return;
    const result = await Swal.fire({
        title: `Endorse Request?`,
        text: `This will move the request to the Manager for final approval.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#6b7280',
        confirmButtonText: `Yes, Endorse Now`
    });
    if (!result.isConfirmed) return;
    try {
        const response = await fetch('be_pending.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: action, request_id: requestId })
        });
        const res = await response.json();
        if (res.success) {
            Swal.fire('Endorsed!', res.message, 'success');
            document.getElementById('requestActionModal').classList.add('hidden');
            fetchPendingRequests();
        } else {
            Swal.fire('Error!', res.message, 'error');
        }
    } catch (error) { console.error(error); }
}

function formatLabel(key) { return key.replace(/([A-Z])/g, ' $1').trim(); }

// --- Global Scoped Logic ---
let currentBatchRef = null;
window.viewProposalBatch = async function (batchRef, reasonStr) {
    currentBatchRef = batchRef;
    document.getElementById('proposalReasonText').textContent = decodeURIComponent(reasonStr);
    const modal = document.getElementById('proposalActionModal');
    const tbody = document.getElementById('proposalDetailsBody');
    tbody.innerHTML = `<tr><td colspan="6" style="text-align:center;"><i data-lucide="loader-2" class="spin"></i> Loading...</td></tr>`;
    modal.classList.remove('hidden');
    if (window.lucide) lucide.createIcons();
    try {
        const r = await fetch(`be_pending.php?action=fetch_proposal_details&batch_reference=${batchRef}`);
        const res = await r.json();
        if (res.success) {
            tbody.innerHTML = res.data.map(req => `
                <tr>
                    <td><strong>${req.GradeLevel}</strong></td>
                    <td><span style="font-size:13px; color:var(--text-secondary);">${req.GradeName}</span></td>
                    <td style="color:var(--text-secondary); text-decoration:line-through;">\u20B1${parseFloat(req.OldMin).toLocaleString()}</td>
                    <td style="color:var(--brand-green); font-weight:600;">\u20B1${parseFloat(req.ProposedMinSalary).toLocaleString()}</td>
                    <td style="color:var(--text-secondary); text-decoration:line-through;">\u20B1${parseFloat(req.OldMax).toLocaleString()}</td>
                    <td style="color:var(--brand-green); font-weight:600;">\u20B1${parseFloat(req.ProposedMaxSalary).toLocaleString()}</td>
                </tr>`).join('');
        }
    } catch (e) { tbody.innerHTML = '<tr><td colspan="6">Error loading details.</td></tr>'; }
};

let currentMeritBatchRef = null;
window.viewMeritProposal = async function (batchRef, reasonStr) {
    currentMeritBatchRef = batchRef;
    document.getElementById('meritReasonText').textContent = decodeURIComponent(reasonStr);
    const modal = document.getElementById('meritActionModal');
    const tbody = document.getElementById('meritDetailsBody');
    tbody.innerHTML = `<tr><td colspan="4" style="text-align:center;"><i data-lucide="loader-2" class="spin"></i> Loading...</td></tr>`;
    modal.classList.remove('hidden');
    if (window.lucide) lucide.createIcons();
    try {
        const r = await fetch(`be_pending.php?action=fetch_merit_details&batch_reference=${batchRef}`);
        const res = await r.json();
        if (res.success) {
            tbody.innerHTML = res.data.map(req => `
                <tr>
                    <td style="font-weight:600;">${req.performance_rating}</td>
                    <td>${req.compa_ratio_range}</td>
                    <td style="color:var(--brand-green); font-weight:600;">${parseFloat(req.ProposedMinIncrease).toFixed(1)}%</td>
                    <td style="color:var(--brand-green); font-weight:600;">${parseFloat(req.ProposedMaxIncrease).toFixed(1)}%</td>
                </tr>`).join('');
        }
    } catch (e) { tbody.innerHTML = '<tr><td colspan="4">Error loading details.</td></tr>'; }
};

let currentAllowanceBatchRef = null;
window.viewAllowanceProposal = async function (batchRef, reasonStr) {
    currentAllowanceBatchRef = batchRef;
    document.getElementById('allowanceReasonText').textContent = decodeURIComponent(reasonStr);
    const modal = document.getElementById('allowanceActionModal');
    const tbody = document.getElementById('allowanceDetailsBody');
    tbody.innerHTML = `<tr><td colspan="4" style="text-align:center;"><i data-lucide="loader-2" class="spin"></i> Loading...</td></tr>`;
    modal.classList.remove('hidden');
    if (window.lucide) lucide.createIcons();
    try {
        const r = await fetch(`be_pending.php?action=fetch_allowance_details&batch_reference=${batchRef}`);
        const res = await r.json();
        if (res.success) {
            tbody.innerHTML = res.data.map(req => `
                <tr>
                    <td style="font-weight:600;">${req.GradeLevel} / ${req.GradeName}</td>
                    <td>${req.AllowanceName}</td>
                    <td style="color:var(--text-secondary); text-decoration:line-through;">\u20B1${parseFloat(req.OldAmount).toLocaleString()}</td>
                    <td style="color:var(--brand-green); font-weight:600;">\u20B1${parseFloat(req.ProposedAmount).toLocaleString()}</td>
                </tr>`).join('');
        }
    } catch (e) { tbody.innerHTML = '<tr><td colspan="4">Error loading details.</td></tr>'; }
};

let currentStatutoryBatchRef = null;
window.viewStatutoryProposal = async function (batchRef, reasonStr, proofPath) {
    currentStatutoryBatchRef = batchRef;
    document.getElementById('statutoryReasonText').textContent = decodeURIComponent(reasonStr);
    const modal = document.getElementById('statutoryActionModal');
    const tbody = document.getElementById('statutoryDetailsBody');
    const proofLink = document.getElementById('statutoryProofLink');
    if (proofLink) proofLink.href = '../../' + proofPath;
    tbody.innerHTML = `<tr><td colspan="4" style="text-align:center;"><i data-lucide="loader-2" class="spin"></i> Loading...</td></tr>`;
    modal.classList.remove('hidden');
    if (window.lucide) lucide.createIcons();
    try {
        const r = await fetch(`be_pending.php?action=fetch_statutory_details&batch_reference=${batchRef}`);
        const res = await r.json();
        if (res.success) {
            tbody.innerHTML = res.data.map(req => {
                const isPercent = req.FieldName.toLowerCase().includes('pct') || req.FieldName.toLowerCase().includes('rate');
                const symbol = isPercent ? '' : '₱';
                const suffix = isPercent ? '%' : '';
                return `
                <tr>
                    <td><strong>${req.Category}</strong></td>
                    <td><span style="font-size:13px; color:var(--text-secondary);">${req.FieldName.replace(/_/g, ' ')}</span></td>
                    <td style="color:var(--text-secondary); text-decoration:line-through;">${symbol}${parseFloat(req.OldValue).toLocaleString()}${suffix}</td>
                    <td style="color:#3b82f6; font-weight:600;">${symbol}${parseFloat(req.ProposedValue).toLocaleString()}${suffix}</td>
                </tr>`;
            }).join('');
        }
    } catch (e) { tbody.innerHTML = '<tr><td colspan="4">Error loading details.</td></tr>'; }
};

let currentSimulationBatchRef = null;
window.viewSimulation = async function (batchRef) {
    currentSimulationBatchRef = batchRef;
    const modal = document.getElementById('simulationActionModal');
    const tbody = document.getElementById('simulationDetailsBody');
    tbody.innerHTML = `<tr><td colspan="7" style="text-align:center;"><i data-lucide="loader-2" class="spin"></i> Loading simulation data...</td></tr>`;
    modal.classList.remove('hidden');
    if (window.lucide) lucide.createIcons();

    try {
        const r = await fetch(`be_pending.php?action=fetch_simulation_details&batch_reference=${batchRef}`);
        const res = await r.json();
        tbody.innerHTML = res.data.map(emp => {
            const curSal = parseFloat(emp.current_salary) || 0;
            const propPct = parseFloat(emp.prop_pct) || 0;
            const propInc = parseFloat(emp.prop_inc) || 0;
            const newSal = parseFloat(emp.new_salary) || 0;
            return `
                <tr>
                    <td>
                        <div style="font-weight:600;">${emp.name || 'Unknown Employee'}</div>
                        <div style="font-size:11px; color:var(--text-secondary);">${emp.department || '-'}</div>
                    </td>
                    <td>${emp.grade || '-'}</td>
                    <td>\u20B1${curSal.toLocaleString()}</td>
                    <td style="color:${propPct > 0 ? 'var(--brand-green)' : 'var(--text-secondary)'}; font-weight:600;">${propPct.toFixed(1)}%</td>
                    <td>\u20B1${propInc.toLocaleString()}</td>
                    <td style="font-weight:700; color:var(--brand-blue);">\u20B1${newSal.toLocaleString()}</td>
                    <td><span class="badge ${emp.promotion_grade ? 'badge-info' : 'hidden'}">${emp.promotion_grade || ''}</span></td>
                </tr>`;
        }).join('');
    } catch (e) { tbody.innerHTML = '<tr><td colspan="7">Error loading simulation.</td></tr>'; }
};

document.addEventListener('DOMContentLoaded', () => {
    // Shared UI logic
    if (window.lucide) lucide.createIcons();
    const body = document.body;
    const themeToggle = document.getElementById("themeToggle");
    const sidebarToggle = document.getElementById("sidebarToggle");
    const sidebar = document.getElementById("sidebar");

    if (themeToggle) themeToggle.addEventListener("click", () => {
        body.classList.toggle("dark-mode");
        localStorage.setItem("theme", body.classList.contains("dark-mode") ? "dark" : "light");
    });
    if (sidebarToggle && sidebar) sidebarToggle.addEventListener("click", () => {
        sidebar.classList.toggle("collapsed");
        localStorage.setItem("sidebarCollapsed", sidebar.classList.contains("collapsed"));
    });

    // Info Modal handling
    document.getElementById('btnCloseActionModal')?.addEventListener('click', () => document.getElementById('requestActionModal').classList.add('hidden'));

    // Salary Modal handling
    document.getElementById('btnCloseProposalModal')?.addEventListener('click', () => document.getElementById('proposalActionModal').classList.add('hidden'));
    document.getElementById('btnEndorseProposal')?.addEventListener('click', async () => {
        if (!currentBatchRef) return;
        const res = await Swal.fire({
            title: 'Endorse Salary Scale Proposal?',
            text: 'Are you sure you want to endorse these changes? This will move the proposal to the Manager for final approval.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Endorse Now',
            cancelButtonText: 'Cancel'
        });
        if (res.isConfirmed) {
            const r = await fetch('be_pending.php', { method: 'POST', body: JSON.stringify({ action: 'endorse_batch', batch_reference: currentBatchRef }) });
            const data = await r.json();
            if (data.success) {
                Swal.fire('Endorsed!', 'The salary scale proposal has been successfully endorsed.', 'success');
                document.getElementById('proposalActionModal').classList.add('hidden');
                fetchPendingRequests();
            } else {
                Swal.fire('Error!', data.message || 'Failed to endorse.', 'error');
            }
        }
    });

    // Merit Modal handling
    document.getElementById('btnCloseMeritModal')?.addEventListener('click', () => document.getElementById('meritActionModal').classList.add('hidden'));
    document.getElementById('btnEndorseMerit')?.addEventListener('click', async () => {
        if (!currentMeritBatchRef) return;
        const res = await Swal.fire({
            title: 'Endorse Merit Matrix Proposal?',
            text: 'Are you sure you want to endorse these changes? This will move the proposal to the Manager for final approval.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#8b5cf6',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Endorse Now'
        });
        if (res.isConfirmed) {
            const r = await fetch('be_pending.php', { method: 'POST', body: JSON.stringify({ action: 'endorse_merit_batch', batch_reference: currentMeritBatchRef }) });
            if ((await r.json()).success) {
                Swal.fire('Endorsed!', 'Merit Matrix adjustments endorsed successfully.', 'success');
                document.getElementById('meritActionModal').classList.add('hidden');
                fetchPendingRequests();
            }
        }
    });
    document.getElementById('btnRejectMerit')?.addEventListener('click', async () => {
        if (!currentMeritBatchRef) return;
        const res = await Swal.fire({
            title: 'Reject Merit Proposal?',
            text: 'This action will discard the proposed adjustments. Are you sure you want to reject?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Reject It'
        });
        if (res.isConfirmed) {
            const r = await fetch('be_pending.php', { method: 'POST', body: JSON.stringify({ action: 'reject_merit_batch', batch_reference: currentMeritBatchRef }) });
            if ((await r.json()).success) {
                Swal.fire('Rejected!', 'The Merit Matrix proposal has been rejected.', 'success');
                document.getElementById('meritActionModal').classList.add('hidden');
                fetchPendingRequests();
            }
        }
    });

    // Allowance Modal handling
    document.getElementById('btnCloseAllowanceModal')?.addEventListener('click', () => document.getElementById('allowanceActionModal').classList.add('hidden'));
    document.getElementById('btnEndorseAllowance')?.addEventListener('click', async () => {
        if (!currentAllowanceBatchRef) return;
        const res = await Swal.fire({
            title: 'Endorse Allowance Proposal?',
            text: 'Are you sure you want to endorse these changes? This will move the proposal to the Manager for final approval.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#f59e0b',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Endorse Now'
        });
        if (res.isConfirmed) {
            const r = await fetch('be_pending.php', { method: 'POST', body: JSON.stringify({ action: 'endorse_allowance_batch', batch_reference: currentAllowanceBatchRef }) });
            if ((await r.json()).success) {
                Swal.fire('Endorsed!', 'Allowance adjustments endorsed successfully.', 'success');
                document.getElementById('allowanceActionModal').classList.add('hidden');
                fetchPendingRequests();
            }
        }
    });
    document.getElementById('btnRejectAllowance')?.addEventListener('click', async () => {
        if (!currentAllowanceBatchRef) return;
        const res = await Swal.fire({
            title: 'Reject Allowance Proposal?',
            text: 'This action will discard the proposed adjustments. Are you sure you want to reject?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Reject It'
        });
        if (res.isConfirmed) {
            const r = await fetch('be_pending.php', { method: 'POST', body: JSON.stringify({ action: 'reject_allowance_batch', batch_reference: currentAllowanceBatchRef }) });
            if ((await r.json()).success) {
                Swal.fire('Rejected!', 'The Allowance proposal has been rejected.', 'success');
                document.getElementById('allowanceActionModal').classList.add('hidden');
                fetchPendingRequests();
            }
        }
    });

    // Statutory Modal handling
    document.getElementById('btnCloseStatutoryModal')?.addEventListener('click', () => document.getElementById('statutoryActionModal').classList.add('hidden'));
    document.getElementById('btnEndorseStatutory')?.addEventListener('click', async () => {
        if (!currentStatutoryBatchRef) return;
        const res = await Swal.fire({
            title: 'Endorse Statutory Adjustments?',
            text: 'Are you sure you want to endorse these changes? They will be sent to the Manager for final review and application.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3b82f6',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Endorse Now'
        });
        if (res.isConfirmed) {
            const r = await fetch('be_pending.php', { method: 'POST', body: JSON.stringify({ action: 'endorse_statutory_batch', batch_reference: currentStatutoryBatchRef }) });
            if ((await r.json()).success) { Swal.fire('Endorsed!', 'The statutory adjustments have been successfully endorsed.', 'success'); document.getElementById('statutoryActionModal').classList.add('hidden'); fetchPendingRequests(); }
        }
    });
    document.getElementById('btnRejectStatutory')?.addEventListener('click', async () => {
        if (!currentStatutoryBatchRef) return;
        const res = await Swal.fire({
            title: 'Reject Statutory Proposal?',
            text: 'This action will discard the proposed adjustments. Are you sure?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Reject It'
        });
        if (res.isConfirmed) {
            const r = await fetch('be_pending.php', { method: 'POST', body: JSON.stringify({ action: 'reject_statutory_batch', batch_reference: currentStatutoryBatchRef }) });
            if ((await r.json()).success) { Swal.fire('Rejected!', 'The proposal has been rejected and discarded.', 'success'); document.getElementById('statutoryActionModal').classList.add('hidden'); fetchPendingRequests(); }
        }
    });

    // Simulation Modal handling
    document.getElementById('btnCloseSimulationModal')?.addEventListener('click', () => document.getElementById('simulationActionModal').classList.add('hidden'));
    document.getElementById('btnEndorseSimulation')?.addEventListener('click', async () => {
        if (!currentSimulationBatchRef) return;
        const res = await Swal.fire({
            title: 'Endorse Compensation Simulation?',
            text: 'This will move the simulation to the Manager for budget approval and review.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#2563eb',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Endorse Now'
        });
        if (res.isConfirmed) {
            const r = await fetch('be_pending.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'endorse_simulation', batch_reference: currentSimulationBatchRef })
            });
            const data = await r.json();
            if (data.success) {
                Swal.fire('Endorsed!', 'Simulation endorsed successfully.', 'success');
                document.getElementById('simulationActionModal').classList.add('hidden');
                fetchPendingRequests();
            } else {
                Swal.fire('Error!', data.message || 'Failed to endorse.', 'error');
            }
        }
    });

    document.getElementById('btnRejectSimulation')?.addEventListener('click', async () => {
        if (!currentSimulationBatchRef) return;
        const res = await Swal.fire({
            title: 'Reject Compensation Simulation?',
            text: 'This will discard the proposed adjustments. Are you sure you want to reject?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Reject It'
        });
        if (res.isConfirmed) {
            const r = await fetch('be_pending.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'reject_simulation', batch_reference: currentSimulationBatchRef })
            });
            const data = await r.json();
            if (data.success) {
                Swal.fire('Rejected!', 'The simulation has been rejected.', 'success');
                document.getElementById('simulationActionModal').classList.add('hidden');
                fetchPendingRequests();
            } else {
                Swal.fire('Error!', data.message || 'Failed to reject.', 'error');
            }
        }
    });
});

// --- End of Script ---
