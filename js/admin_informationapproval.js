document.addEventListener('DOMContentLoaded', () => {
    fetchEndorsedRequests();
    lucide.createIcons();

    // Modal Elements
    const modal = document.getElementById('requestActionModal');
    const btnClose = document.getElementById('btnCloseActionModal');
    const btnApprove = document.getElementById('btnApprove');
    const btnReject = document.getElementById('btnReject');
    let currentRequestId = null;

    if (btnClose) {
        btnClose.addEventListener('click', () => {
            modal.classList.add('hidden');
        });
    }

    if (btnApprove) {
        btnApprove.addEventListener('click', () => processRequest(currentRequestId, 'approve_request'));
    }

    if (btnReject) {
        btnReject.addEventListener('click', () => processRequest(currentRequestId, 'reject_request'));
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

async function fetchEndorsedRequests() {
    const tableBody = document.getElementById('requestsTableBody');
    if (!tableBody) return;

    try {
        const response = await fetch('be_informationapproval.php?action=fetch_endorsed');
        const result = await response.json();

        if (result.success) {
            const requests = result.data;

            if (requests.length === 0) {
                tableBody.innerHTML = `<tr><td colspan="5">
                    <div class="empty-state">
                        <i data-lucide="inbox"></i>
                        <p>No endorsed requests found</p>
                        <span>Requests will appear here after Supervisor endorsement.</span>
                    </div></td></tr>`;
                lucide.createIcons();
                return;
            }

            tableBody.innerHTML = requests.map(req => {
                const initials = (req.FirstName[0] + req.LastName[0]).toUpperCase();
                const date = new Date(req.SupervisorDate).toLocaleDateString('en-PH', {
                    year: 'numeric', month: 'short', day: 'numeric'
                });

                return `
                <tr class="req-row">
                    <td>
                        <div class="emp-cell">
                            <div class="emp-avatar">${initials}</div>
                            <div>
                                <div class="emp-name">${req.FirstName} ${req.LastName}</div>
                                <div class="emp-dept">${req.DepartmentName || '-'}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="type-pill">
                            <i data-lucide="file-pen-line"></i>
                            ${req.RequestType}
                        </span>
                    </td>
                    <td style="color: var(--text-secondary); font-size:13px;">${date}</td>
                    <td>
                        <div>
                            <span class="badge badge-success">Endorsed</span>
                            <div style="font-size:10px; color:var(--text-tertiary);">By: ${req.SupFirstName} ${req.SupLastName}</div>
                        </div>
                    </td>
                    <td>
                        <button class="btn-review" onclick="viewRequest(${req.RequestID}, '${encodeURIComponent(req.RequestData)}', '${req.ProofPath}')">
                            <i data-lucide="eye"></i> Review & Approve
                        </button>
                    </td>
                </tr>`;
            }).join('');

            lucide.createIcons();
        } else {
            tableBody.innerHTML = `<tr><td colspan="5" style="text-align:center;color:var(--text-secondary);padding:32px;">Error: ${result.message}</td></tr>`;
        }
    } catch (error) {
        console.error('Error fetching requests:', error);
        tableBody.innerHTML = `<tr><td colspan="5" style="text-align:center;padding:32px;">Failed to load requests.</td></tr>`;
    }
}

async function processRequest(requestId, action) {
    if (!requestId) return;

    const actionText = action === 'approve_request' ? 'Approve' : 'Reject';
    const confirmColor = action === 'approve_request' ? '#10b981' : '#ef4444';

    const result = await Swal.fire({
        title: `Confirm ${actionText}?`,
        text: `Are you sure you want to ${actionText.toLowerCase()} this request?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: confirmColor,
        cancelButtonColor: '#6b7280',
        confirmButtonText: `Yes, ${actionText} it!`
    });

    if (!result.isConfirmed) return;

    try {
        const response = await fetch('be_informationapproval.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: action, request_id: requestId })
        });
        const res = await response.json();

        if (res.success) {
            Swal.fire('Success!', res.message, 'success');
            document.getElementById('requestActionModal').classList.add('hidden');
            fetchEndorsedRequests();
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
                confirmButtonText: 'Yes, Sign Out',
                cancelButtonText: 'Stay',
                reverseButtons: true
            });
            if (result.isConfirmed) {
                window.location.href = dest;
            }
        });
    });

    if (typeof lucide !== "undefined") lucide.createIcons();
});

// Sidebar Active Link Logic
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
