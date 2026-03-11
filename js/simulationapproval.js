document.addEventListener("DOMContentLoaded", () => {
    const lucide = window.lucide;
    const financeTableBody = document.getElementById("financeTableBody");
    const approvalModal = document.getElementById("approvalModal");
    const modalTableContainer = document.getElementById("modalTableContainer");
    const pendingCountEl = document.getElementById("pendingFinalCount");
    const approveBtn = document.getElementById("approveBtn");
    const rejectBtn = document.getElementById("rejectBtn");
    const closeModal = document.getElementById("closeModal");

    let currentDraftId = null;

    // Premium Clock Logic
    function initPremiumClock() {
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
    initPremiumClock();

    const loadSimulations = async () => {
        if (!financeTableBody) return;
        try {
            const res = await fetch("be_finance_fetch.php?action=fetch");
            const data = await res.json();
            if (data.success && data.data) {
                if (pendingCountEl) pendingCountEl.innerText = data.data.filter(s => s.Status === 'Reviewed').length;
                if (data.data.length === 0) {
                    financeTableBody.innerHTML = '<tr><td colspan="5" style="text-align:center; padding:40px; color:var(--text-secondary);">No simulations for approval.</td></tr>';
                } else {
                    financeTableBody.innerHTML = data.data.map(s => `
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding:12px;">
                                <div style="font-weight:600; color:var(--text-primary);">${s.CycleName}</div>
                                <div style="font-size:11px; color:var(--text-secondary);">Ref: #${s.DraftID}</div>
                            </td>
                            <td style="padding:12px; color:var(--brand-green); font-weight:700;">\u20B1${parseFloat(s.TotalCost).toLocaleString()}</td>
                            <td style="padding:12px; color:var(--text-secondary);">${s.ProposedBy || 'HR User'}</td>
                            <td style="padding:12px; color:var(--text-secondary);">${new Date(s.CreatedAt).toLocaleDateString()}</td>
                            <td style="padding:12px;">
                                ${s.Status === 'Reviewed' ? `
                                <button class="action-btn btn-approve-row" data-id="${s.DraftID}" style="padding:6px 16px; border-radius:6px; background:var(--brand-blue); color:white; border:none; cursor:pointer; font-weight:600; display:flex; align-items:center; gap:6px;">
                                    <i data-lucide="shield-check" style="width:14px;height:14px;"></i> Final Review
                                </button>` : `<span class="badge approved">Approved</span>`}
                            </td>
                        </tr>
                    `).join("");
                    if (window.lucide) window.lucide.createIcons();
                }
            }
        } catch (e) { console.error("Load Error:", e); }
    };

    document.addEventListener("click", async (e) => {
        const rowBtn = e.target.closest(".btn-approve-row");
        if (rowBtn) {
            const id = rowBtn.getAttribute("data-id");
            currentDraftId = id;
            modalTableContainer.innerHTML = '<div style="padding:40px; text-align:center;">Loading details...</div>';
            approvalModal.style.display = "flex";

            try {
                const res = await fetch(`be_finance_fetch.php?action=details&id=${id}`);
                const data = await res.json();
                if (data.success) {
                    const empData = JSON.parse(data.data.EmployeeData);

                    // Add Hero Stats to Modal
                    const totalImpact = parseFloat(data.data.TotalCost).toLocaleString();
                    const staffCount = empData.length;

                    document.getElementById("modalSubtitle").innerHTML = `
                        <div style="display:flex; gap:16px; margin-top:8px;">
                            <div style="background:rgba(16,185,129,0.1); color:#10b981; padding:4px 12px; border-radius:12px; font-size:12px; font-weight:700;">\u20B1${totalImpact} Final Cost</div>
                            <div style="background:rgba(139,92,246,0.1); color:#8b5cf6; padding:4px 12px; border-radius:12px; font-size:12px; font-weight:700;">${staffCount} Staff Included</div>
                        </div>
                    `;

                    renderFinanceDetails(empData);
                }
            } catch (err) {
                modalTableContainer.innerHTML = '<div style="color:var(--brand-red); padding:20px;">Error loading details.</div>';
            }
        }
    });

    const renderFinanceDetails = (data) => {
        const headers = [
            "EE ID", "Name & Position", "Rating", "Status", "Salary", "Grade Midpoint", "Compa-Ratio",
            "Prop. %", "Prop. Increase (\u20B1)", "Basic (New)", "Gross Salary", "Net Pay", "Increase"
        ];

        let html = `
            <div style="overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse; font-size:12px; min-width:1400px;">
                    <thead>
                        <tr style="background:var(--background-alt); border-bottom:2px solid var(--border-color);">
                            ${headers.map(h => `<th style="padding:12px 8px; text-align:left;">${h}</th>`).join("")}
                        </tr>
                    </thead>
                    <tbody>
                        ${data.map(emp => `
                            <tr style="border-bottom:1px solid var(--border-color);">
                                <td style="padding:8px;">${emp.EmployeeCode || '---'}</td>
                                <td style="padding:8px; white-space:nowrap;">
                                    <div style="font-weight:600;">${emp.Name || 'Unknown'}</div>
                                    <div style="font-size:10px; color:var(--text-secondary);">${emp.Position || '-'}</div>
                                </td>
                                <td style="padding:8px; text-align:center;">${emp.Rating}</td>
                                <td style="padding:8px; text-align:center;"><span class="badge reviewed">Reviewed</span></td>
                                <td style="padding:8px;">\u20B1${parseFloat(emp.Salary).toLocaleString()}</td>
                                <td style="padding:8px;">\u20B1${parseFloat(emp.GradeMidpoint).toLocaleString()}</td>
                                <td style="padding:8px; text-align:center;">${emp.CompaRatio}</td>
                                <td style="padding:8px; text-align:center; font-weight:700;">${emp.PropPct}%</td>
                                <td style="padding:8px; color:var(--brand-green);">+\u20B1${parseFloat(emp.PropInc).toLocaleString()}</td>
                                <td style="padding:8px; font-weight:700;">\u20B1${parseFloat(emp.BasicNew).toLocaleString()}</td>
                                <td style="padding:8px; font-weight:700;">\u20B1${parseFloat(emp.GrossSalary).toLocaleString()}</td>
                                <td style="padding:8px; font-weight:700; color:var(--brand-green);">\u20B1${parseFloat(emp.NetPay).toLocaleString()}</td>
                                <td style="padding:8px; font-weight:700; color:var(--brand-green);">+\u20B1${parseFloat(emp.Increase).toLocaleString()}</td>
                            </tr>
                        `).join("")}
                    </tbody>
                </table>
            </div>
        `;
        modalTableContainer.innerHTML = html;
        if (window.lucide) window.lucide.createIcons();
    };

    if (closeModal) closeModal.addEventListener("click", () => approvalModal.style.display = "none");
    if (document.getElementById("cancelApproval")) document.getElementById("cancelApproval").addEventListener("click", () => approvalModal.style.display = "none");

    if (approveBtn) {
        approveBtn.addEventListener("click", async () => {
            const result = await Swal.fire({
                title: "Confirm Final Approval?",
                text: "This will finalize the compensation cycle and commit the salary changes.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#10b981",
                confirmButtonText: "Yes, Approve & Commit"
            });
            if (!result.isConfirmed) return;

            const res = await fetch("be_finance_approve.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ action: "approve", id: currentDraftId })
            });
            const data = await res.json();
            if (data.success) {
                Swal.fire("Success", "Compensation simulation finalized successfully.", "success").then(() => location.reload());
            }
        });
    }

    if (rejectBtn) {
        rejectBtn.addEventListener("click", async () => {
            const { value: reason } = await Swal.fire({
                title: "Reject Simulation",
                input: "text",
                inputLabel: "Reason for rejection",
                inputPlaceholder: "Enter your reason here...",
                showCancelButton: true
            });
            if (reason) {
                const res = await fetch("be_finance_approve.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ action: "reject", id: currentDraftId, reason: reason })
                });
                const data = await res.json();
                if (data.success) {
                    Swal.fire("Rejected", "Simulation rejected successfully.", "success").then(() => location.reload());
                }
            }
        });
    }

    loadSimulations();

    // Sidebar Active Link Logic
    (function () {
        const path = window.location.pathname;
        const page = path.split('/').pop() || 'dashboard.php';
        const current = page.split('?')[0];
        document.querySelectorAll('.sidebar .nav-item, .sidebar .submenu-item').forEach(el => el.classList.remove('active'));
        const navMatch = document.querySelector(`.sidebar a.nav-item[href$="${current}"]`);
        if (navMatch) navMatch.classList.add('active');
    })();
});
