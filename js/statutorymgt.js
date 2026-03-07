document.addEventListener('DOMContentLoaded', () => {
    fetchEndorsedStatutory();
    if (window.lucide) lucide.createIcons();

    // -- Sidebar & Theme Shared Logic --
    const body = document.body;
    const themeToggle = document.getElementById("themeToggle");
    const sidebarToggle = document.getElementById("sidebarToggle");
    const sidebar = document.getElementById("sidebar");
    const mobileMenuBtn = document.getElementById("mobileMenuBtn");

    if (themeToggle) themeToggle.addEventListener("click", () => {
        body.classList.toggle("dark-mode");
        localStorage.setItem("theme", body.classList.contains("dark-mode") ? "dark" : "light");
    });
    if (sidebarToggle && sidebar) sidebarToggle.addEventListener("click", () => {
        sidebar.classList.toggle("collapsed");
        localStorage.setItem("sidebarCollapsed", sidebar.classList.contains("collapsed"));
    });
    if (mobileMenuBtn && sidebar) mobileMenuBtn.addEventListener("click", () => {
        sidebar.classList.toggle("mobile-open");
    });

    // Clock
    function updateClock() {
        const el = document.getElementById('realTimeClock');
        if (!el) return;
        const now = new Date();
        el.textContent = now.toLocaleDateString('en-PH', { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' }) + ', ' +
            now.toLocaleTimeString('en-PH', { hour12: true });
    }
    setInterval(updateClock, 1000);
    updateClock();

    // User Menu
    const umBtn = document.getElementById('userMenuBtn');
    const umDropdown = document.getElementById('userMenuDropdown');
    if (umBtn && umDropdown) {
        umBtn.addEventListener('click', e => { e.stopPropagation(); umDropdown.classList.toggle('umd-open'); });
        document.addEventListener('click', () => umDropdown.classList.remove('umd-open'));
    }
});

async function fetchEndorsedStatutory() {
    const tableBody = document.getElementById('endorsedStatutoryBody');
    if (!tableBody) return;

    try {
        const response = await fetch('be_statutorymgt.php?action=fetch_endorsed');
        const result = await response.json();

        if (result.success && result.data.length > 0) {
            tableBody.innerHTML = result.data.map(req => {
                const dateStr = new Date(req.CreatedAt).toLocaleDateString('en-PH', { month: 'short', day: 'numeric', year: 'numeric' });
                return `
                <tr class="req-row">
                    <td>
                        <div class="emp-cell">
                            <div class="emp-avatar" style="background:#3b82f6; color:white;">${req.ProposedByName ? req.ProposedByName[0].toUpperCase() : 'A'}</div>
                            <div>
                                <div class="emp-name">${req.ProposedByName || 'Analyst'}</div>
                                <div class="emp-dept">Compensation & Compliance</div>
                            </div>
                        </div>
                    </td>
                    <td><span class="type-pill"><i data-lucide="landmark"></i> Statutory</span></td>
                    <td style="max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-size: 13px; color: var(--text-secondary);">
                        ${req.Reason}
                    </td>
                    <td style="color: var(--text-secondary); font-size:13px;">${dateStr}</td>
                    <td><span class="badge badge-info" style="background:rgba(59,130,246,0.1); color:#3b82f6; border:1px solid rgba(59,130,246,0.2);">Endorsed</span></td>
                    <td>
                        <button class="btn-review" 
                                onclick="viewStatutoryBatch('${req.BatchReference}', '${encodeURIComponent(req.Reason)}', '${req.ProofPath}')">
                            <i data-lucide="eye"></i> Review & Approve
                        </button>
                    </td>
                </tr>`;
            }).join('');
        } else {
            tableBody.innerHTML = `<tr><td colspan="6"><div class="empty-state"><i data-lucide="inbox"></i><p>No endorsed proposals found</p><span>All statutory adjustments are up-to-date.</span></div></td></tr>`;
        }
        if (window.lucide) lucide.createIcons();
    } catch (e) {
        tableBody.innerHTML = `<tr><td colspan="6" style="text-align:center; padding:32px; color:var(--brand-red);">Failed to load proposals.</td></tr>`;
    }
}

let currentStatutoryBatchRef = null;

window.viewStatutoryBatch = async function (batchRef, reasonStr, proofPath) {
    currentStatutoryBatchRef = batchRef;
    document.getElementById('statutoryReasonText').textContent = decodeURIComponent(reasonStr);
    const modal = document.getElementById('statutoryActionModal');
    const tbody = document.getElementById('statutoryDetailsBody');
    const proofLink = document.getElementById('statutoryProofLink');
    if (proofLink) proofLink.href = '../../' + proofPath;

    tbody.innerHTML = `<tr><td colspan="4" style="text-align:center; padding:20px;"><i data-lucide="loader-2" class="spin"></i> Loading...</td></tr>`;
    modal.classList.remove('hidden');
    if (window.lucide) lucide.createIcons();

    try {
        const response = await fetch(`be_statutorymgt.php?action=fetch_proposal_details&batch_reference=${batchRef}`);
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
                    <td style="color:var(--text-secondary); text-decoration:line-through;">${symbol}${parseFloat(req.OldValue).toLocaleString()}${suffix}</td>
                    <td style="color:#3b82f6; font-weight:600;">${symbol}${parseFloat(req.ProposedValue).toLocaleString()}${suffix}</td>
                </tr>`;
            }).join('');
        }
    } catch (e) {
        tbody.innerHTML = `<tr><td colspan="4" style="text-align:center; color:var(--brand-red);">Error loading details.</td></tr>`;
    }
};

// Modal Button Actions
document.addEventListener('DOMContentLoaded', () => {
    const btnClose = document.getElementById('btnCloseStatutoryModal');
    const btnApprove = document.getElementById('btnApproveStatutory');
    const btnReject = document.getElementById('btnRejectStatutory');
    const modal = document.getElementById('statutoryActionModal');

    if (btnClose) btnClose.addEventListener('click', () => modal.classList.add('hidden'));

    if (btnApprove) {
        btnApprove.addEventListener('click', async () => {
            if (!currentStatutoryBatchRef) return;
            const result = await Swal.fire({
                title: 'Approve & Send to Finance?',
                text: 'This will forward the endorsed adjustments to Finance for final approval.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3b82f6',
                confirmButtonText: 'Yes, Send to Finance'
            });
            if (result.isConfirmed) {
                try {
                    const r = await fetch('be_statutorymgt.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ action: 'manager_approve_batch', batch_reference: currentStatutoryBatchRef })
                    });
                    const res = await r.json();
                    if (res.success) {
                        Swal.fire('Success!', res.message, 'success');
                        modal.classList.add('hidden');
                        location.reload(); // Reload to show updated current settings
                    } else {
                        Swal.fire('Error!', res.message, 'error');
                    }
                } catch (e) { Swal.fire('Error!', 'An error occurred.', 'error'); }
            }
        });
    }

    if (btnReject) {
        btnReject.addEventListener('click', async () => {
            if (!currentStatutoryBatchRef) return;
            const result = await Swal.fire({
                title: 'Reject Statutory Proposal?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Yes, Reject'
            });
            if (result.isConfirmed) {
                try {
                    const r = await fetch('be_statutorymgt.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ action: 'reject_batch', batch_reference: currentStatutoryBatchRef })
                    });
                    const res = await r.json();
                    if (res.success) {
                        Swal.fire('Rejected!', res.message, 'success');
                        modal.classList.add('hidden');
                        fetchEndorsedStatutory();
                    } else {
                        Swal.fire('Error!', res.message, 'error');
                    }
                } catch (e) { Swal.fire('Error!', 'An error occurred.', 'error'); }
            }
        });
    }
});
