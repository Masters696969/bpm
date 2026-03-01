document.addEventListener('DOMContentLoaded', () => {
    fetchPendingRequests();
    lucide.createIcons();

    // Modal Elements
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

        // Changed Data Section
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
            // Group into rows of 2 for grid layout
            for (let i = 0; i < entries.length; i += 2) {
                html += `<div class="rem-row">`;

                // First column
                html += `
                    <div class="rem-field ${i === entries.length - 1 ? 'full' : ''}">
                        <label>${formatLabel(entries[i][0])}</label>
                        <div class="rem-input" style="background: var(--surface-hover); min-height: 35px; border-color: transparent;">${entries[i][1]}</div>
                    </div>
                `;

                // Second column if exists
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
        html += `</div>`; // Close rem-fields

        // Proof Section
        if (proofPath && proofPath !== 'null' && proofPath !== 'undefined') {
            const absoluteProofPath = '../../' + proofPath;
            const isImage = proofPath.match(/\.(jpg|jpeg|png|gif)$/i);

            html += `
                </div> <!-- Close previous rem-section -->
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
                    <div style="text-align: center; margin-top: 8px;">
                         <a href="${absoluteProofPath}" target="_blank" style="font-size: 12px; color: var(--brand-green); text-decoration: none; font-weight: 500;"><i data-lucide="external-link" style="width:12px; height:12px; vertical-align:middle; margin-right:3px;"></i>Open full size</a>
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
                        <a href="${absoluteProofPath}" target="_blank" style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; background: var(--surface); border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px; font-weight: 600; color: var(--text-primary); text-decoration: none; transition: var(--transition);">
                            <i data-lucide="download" style="width: 14px; height: 14px;"></i> View / Download
                        </a>
                     </div>
                 `;
            }

            html += `
                        </div>
                    </div>
            `;
        }

        container.innerHTML = html;
        modal.classList.remove('hidden');
        lucide.createIcons();
    };
});

async function fetchPendingRequests() {
    const tableBody = document.getElementById('requestsTableBody');
    if (!tableBody) return;

    try {
        // Fetch both endpoints simultaneously
        const [infoRes, propRes] = await Promise.all([
            fetch('be_pending.php?action=fetch_pending').then(r => r.json()).catch(() => ({ success: false, data: [] })),
            fetch('be_pending.php?action=fetch_proposals').then(r => r.json()).catch(() => ({ success: false, data: [] }))
        ]);

        let allRequests = [];

        // Normalize Information Requests
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

        // Normalize Salary Proposals
        if (propRes.success) {
            allRequests = allRequests.concat(propRes.data.map(req => {
                const parts = (req.ProposedByName || 'Analyst').split(' ');
                const fName = parts[0];
                const lName = parts.length > 1 ? parts.slice(1).join(' ') : '';
                return {
                    id: req.BatchReference,
                    type: 'salary_proposal',
                    requestType: 'Salary Scale',
                    date: new Date(req.CreatedAt),
                    status: req.Status,
                    firstName: fName,
                    lastName: lName,
                    department: 'Compensation',
                    totalChanges: req.TotalChanges || 0,
                    reason: req.Reason
                };
            }));
        }

        // Sort by date descending
        allRequests.sort((a, b) => b.date - a.date);

        if (allRequests.length === 0) {
            tableBody.innerHTML = `<tr><td colspan="5">
                <div class="empty-state">
                    <i data-lucide="inbox"></i>
                    <p>No pending requests found</p>
                    <span>All clear for now!</span>
                </div></td></tr>`;
            lucide.createIcons();
            return;
        }

        tableBody.innerHTML = allRequests.map(req => {
            const fInitial = req.firstName ? req.firstName[0] : '';
            const lInitial = req.lastName ? req.lastName[0] : '';
            const initials = (fInitial + lInitial).toUpperCase() || 'AN';
            const name = (req.firstName + ' ' + req.lastName).trim() || 'Analyst';

            const dateStr = req.date.toLocaleDateString('en-PH', {
                year: 'numeric', month: 'short', day: 'numeric'
            });

            let actionBtn = '';
            let requestTypePill = '';

            if (req.type === 'info_update') {
                requestTypePill = `
                    <span class="type-pill">
                        <i data-lucide="file-pen-line"></i>
                        ${req.requestType}
                    </span>`;
                actionBtn = `
                    <button class="btn-review" onclick="viewRequest(${req.id}, '${req.dataStr}', '${req.proofPath}')">
                        <i data-lucide="eye"></i> View & Endorse
                    </button>`;
            } else {
                requestTypePill = `
                    <span class="type-pill" style="background:rgba(16,185,129,0.1); color:var(--brand-green);">
                        <i data-lucide="git-pull-request"></i>
                        ${req.requestType}
                    </span>
                    <div style="font-size:11px; color:var(--text-secondary); margin-top:4px;">${req.totalChanges} Grade(s) modified</div>`;
                actionBtn = `
                    <button class="btn-review" style="background:var(--brand-green); border-color:var(--brand-green); color:white;" onclick="viewProposalBatch('${req.id}', '${encodeURIComponent(req.reason)}')">
                        <i data-lucide="eye"></i> View & Endorse
                    </button>`;
            }

            return `
            <tr class="req-row">
                <td>
                    <div class="emp-cell">
                        <div class="emp-avatar" style="${req.type === 'salary_proposal' ? 'background:var(--brand-green); color:white;' : ''}">${initials}</div>
                        <div>
                            <div class="emp-name">${name}</div>
                            <div class="emp-dept">${req.department || '-'}</div>
                        </div>
                    </div>
                </td>
                <td>
                    ${requestTypePill}
                </td>
                <td style="color: var(--text-secondary); font-size:13px;">${dateStr}</td>
                <td><span class="badge badge-warning">Pending Review</span></td>
                <td>
                    ${actionBtn}
                </td>
            </tr>`;
        }).join('');

        lucide.createIcons();
    } catch (error) {
        console.error('Error fetching unified requests:', error);
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
    } catch (error) {
        console.error('Error processing request:', error);
        Swal.fire('Error!', 'An error occurred.', 'error');
    }
}

function formatLabel(key) {
    return key.replace(/([A-Z])/g, ' $1').trim();
}

// ===================================================
// Sidebar & Theme Logic (Synced from core modules)
// ===================================
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

    if (themeToggle) {
        themeToggle.addEventListener("click", () => {
            body.classList.toggle("dark-mode");
            localStorage.setItem("theme", body.classList.contains("dark-mode") ? "dark" : "light");
        });
    }

    // 2. Sidebar Logic
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener("click", () => {
            sidebar.classList.toggle("collapsed");
            localStorage.setItem("sidebarCollapsed", sidebar.classList.contains("collapsed"));
        });
        if (localStorage.getItem("sidebarCollapsed") === "true") sidebar.classList.add("collapsed");
    }

    if (mobileMenuBtn && sidebar) {
        mobileMenuBtn.addEventListener("click", () => sidebar.classList.toggle("mobile-open"));
    }

    // 3. Submenu Logic
    document.querySelectorAll(".nav-item.has-submenu").forEach((item) => {
        item.addEventListener("click", (e) => {
            const module = item.getAttribute("data-module");
            const submenu = document.getElementById(`submenu-${module}`);
            if (submenu) {
                submenu.classList.toggle("active");
                item.classList.toggle("active");
            }
        });
    });

    // 4. User Menu Dropdown Logic
    const dd = document.getElementById('userMenuDropdown');
    const btn = document.getElementById('userMenuBtn');
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
    }

    if (typeof lucide !== "undefined") lucide.createIcons();
});

// Sidebar Active Link Logic
(function () {
    const path = window.location.pathname;
    const page = path.split('/').pop() || 'pending.php';
    const current = page.split('?')[0];

    document.querySelectorAll('.sidebar .nav-item, .sidebar .submenu-item').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.sidebar .nav-item-group').forEach(group => group.classList.remove('active'));

    const navMatch = document.querySelector(`.sidebar a.nav-item[href$="${current}"]`);
    if (navMatch) navMatch.classList.add('active');
})();

// Real-time Clock Logic
function initClock() {
    const clockEl = document.getElementById('realTimeClock');
    if (!clockEl) return;
    const updateClock = () => {
        const days = ['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'];
        const months = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];
        const now = new Date();
        const clockStr = `${days[now.getDay()]}, ${months[now.getMonth()]} ${now.getDate()}, ${now.getFullYear()}, ${now.getHours() % 12 || 12}:${now.getMinutes().toString().padStart(2, '0')}:${now.getSeconds().toString().padStart(2, '0')} ${now.getHours() >= 12 ? 'PM' : 'AM'}`;
        clockEl.textContent = clockStr;
    };
    setInterval(updateClock, 1000);
    updateClock();
}
initClock();


let currentBatchRef = null;

window.viewProposalBatch = async function (batchRef, reasonStr) {
    currentBatchRef = batchRef;
    const reasonText = decodeURIComponent(reasonStr);

    document.getElementById('proposalReasonText').textContent = reasonText;
    const modal = document.getElementById('proposalActionModal');
    const tbody = document.getElementById('proposalDetailsBody');
    tbody.innerHTML = `<tr><td colspan="6" style="text-align:center;"><i data-lucide="loader-2" class="spin"></i> Loading...</td></tr>`;

    modal.classList.remove('hidden');
    if (window.lucide) lucide.createIcons();

    try {
        const response = await fetch(`be_pending.php?action=fetch_proposal_details&batch_reference=${batchRef}`);
        const result = await response.json();
        if (result.success && result.data.length > 0) {
            tbody.innerHTML = result.data.map(req => {
                return `
                <tr>
                    <td><strong>${req.GradeLevel}</strong></td>
                    <td><span style="font-size:13px; color:var(--text-secondary);">${req.GradeName}</span></td>
                    <td style="color:var(--text-secondary); text-decoration:line-through;">\u20B1${parseFloat(req.OldMin).toLocaleString('en-PH', { minimumFractionDigits: 2 })}</td>
                    <td style="color:var(--brand-green); font-weight:600;">\u20B1${parseFloat(req.ProposedMinSalary).toLocaleString('en-PH', { minimumFractionDigits: 2 })}</td>
                    <td style="color:var(--text-secondary); text-decoration:line-through;">\u20B1${parseFloat(req.OldMax).toLocaleString('en-PH', { minimumFractionDigits: 2 })}</td>
                    <td style="color:var(--brand-green); font-weight:600;">\u20B1${parseFloat(req.ProposedMaxSalary).toLocaleString('en-PH', { minimumFractionDigits: 2 })}</td>
                </tr>
                `;
            }).join('');
        } else {
            tbody.innerHTML = `<tr><td colspan="6" style="text-align:center; color:var(--text-secondary);">No precise details found.</td></tr>`;
        }
    } catch (err) {
        tbody.innerHTML = `<tr><td colspan="6" style="text-align:center; color:var(--brand-red);">Failed to load details.</td></tr>`;
    }
}

// Modal buttons wiring
document.addEventListener('DOMContentLoaded', () => {
    const btnCloseProposalModal = document.getElementById('btnCloseProposalModal');
    const btnRejectProposal = document.getElementById('btnRejectProposal');
    const btnEndorseProposal = document.getElementById('btnEndorseProposal');
    const proposalModal = document.getElementById('proposalActionModal');

    if (btnCloseProposalModal) {
        btnCloseProposalModal.addEventListener('click', () => {
            proposalModal.classList.add('hidden');
        });
    }

    if (btnEndorseProposal) {
        btnEndorseProposal.addEventListener('click', async () => {
            if (!currentBatchRef) return;
            const result = await Swal.fire({
                title: `Endorse Salary Scale Proposal?`,
                text: `This will endorse the proposed scales to the Manager for final application.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: `Yes, Endorse Now`
            });
            if (result.isConfirmed) {
                try {
                    const response = await fetch('be_pending.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ action: 'endorse_batch', batch_reference: currentBatchRef })
                    });
                    const res = await response.json();
                    if (res.success) {
                        Swal.fire('Endorsed!', res.message, 'success');
                        proposalModal.classList.add('hidden');
                        fetchPendingRequests();
                    } else {
                        Swal.fire('Error!', res.message, 'error');
                    }
                } catch (e) {
                    Swal.fire('Error!', 'An error occurred.', 'error');
                }
            }
        });
    }

    if (btnRejectProposal) {
        btnRejectProposal.addEventListener('click', async () => {
            if (!currentBatchRef) return;
            const result = await Swal.fire({
                title: `Reject Proposal?`,
                text: `This will permanently reject these salary changes.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: `Yes, Reject`
            });
            if (result.isConfirmed) {
                try {
                    const response = await fetch('be_pending.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ action: 'reject_batch', batch_reference: currentBatchRef })
                    });
                    const res = await response.json();
                    if (res.success) {
                        Swal.fire('Rejected!', res.message, 'success');
                        proposalModal.classList.add('hidden');
                        fetchPendingRequests();
                    } else {
                        Swal.fire('Error!', res.message, 'error');
                    }
                } catch (e) {
                    Swal.fire('Error!', 'An error occurred.', 'error');
                }
            }
        });
    }
});
