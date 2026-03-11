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

    // 2. Sidebar & Mobile Logic
    if (sidebarToggle) {
        sidebarToggle.addEventListener("click", () => {
            sidebar.classList.toggle("collapsed");
            localStorage.setItem("sidebarCollapsed", sidebar.classList.contains("collapsed"));
        });
    }
    if (localStorage.getItem("sidebarCollapsed") === "true" && sidebar) sidebar.classList.add("collapsed");
    if (mobileMenuBtn) mobileMenuBtn.addEventListener("click", () => sidebar.classList.toggle("mobile-open"));

    // 3. Submenu Logic
    document.querySelectorAll(".nav-item.has-submenu").forEach((item) => {
        item.addEventListener("click", () => {
            const module = item.getAttribute("data-module");
            const submenu = document.getElementById(`submenu-${module}`);
            if (submenu) submenu.classList.toggle("active");
            item.classList.toggle("active");
        });
    });

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

    // 4. Simulation Review Logic
    const verifyTableBody = document.getElementById("verifyTableBody");
    const reviewModal = document.getElementById("reviewModal");
    const modalTableContainer = document.getElementById("modalTableContainer");
    const pendingCountEl = document.getElementById("pendingVerifyCount");
    const approveBtn = document.getElementById("approveBtn");
    const rejectBtn = document.getElementById("rejectBtn");
    const closeModal = document.getElementById("closeModal");
    const cancelReview = document.getElementById("cancelReview");
    const rejectionReason = document.getElementById("rejectionReason");
    const rejectReasonInput = document.getElementById("rejectReasonInput");

    let currentDraftId = null;

    const loadSimulations = async () => {
        if (!verifyTableBody) return;
        try {
            const res = await fetch("be_verify_fetch.php?action=fetch");
            const data = await res.json();
            if (data.success && data.data) {
                if (pendingCountEl) pendingCountEl.innerText = data.data.length;
                if (data.data.length === 0) {
                    verifyTableBody.innerHTML = '<tr><td colspan="5" style="text-align:center; padding:40px; color:var(--text-secondary);">No simulations pending verification.</td></tr>';
                } else {
                    verifyTableBody.innerHTML = data.data.map(s => `
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding:12px;">
                                <div style="font-weight:600; color:var(--text-primary);">${s.CycleName}</div>
                                <div style="font-size:11px; color:var(--text-secondary);">Ref: #${s.DraftID}</div>
                            </td>
                            <td style="padding:12px; color:var(--brand-green); font-weight:700;">\u20B1${parseFloat(s.TotalCost).toLocaleString()}</td>
                            <td style="padding:12px; color:var(--text-secondary);">${s.ProposedBy || 'HR User'}</td>
                            <td style="padding:12px; color:var(--text-secondary);">${new Date(s.CreatedAt).toLocaleDateString()}</td>
                            <td style="padding:12px;">
                                <button class="action-btn btn-verify-row" data-id="${s.DraftID}" style="padding:6px 16px; border-radius:6px; background:var(--brand-green); color:white; border:none; cursor:pointer; font-weight:600; display:flex; align-items:center; gap:6px;">
                                    <i data-lucide="shield-check" style="width:14px;height:14px;"></i> Verify
                                </button>
                            </td>
                        </tr>
                    `).join("");
                    if (window.lucide) window.lucide.createIcons();
                }
            }
        } catch (e) { console.error("Load Error:", e); }
    };

    document.addEventListener("click", async (e) => {
        const verifyBtn = e.target.closest(".btn-verify-row");
        if (verifyBtn) {
            const id = verifyBtn.getAttribute("data-id");
            currentDraftId = id;

            modalTableContainer.innerHTML = '<div style="padding:40px; text-align:center;"><i data-lucide="loader" class="animate-spin"></i> Loading details...</div>';
            if (window.lucide) window.lucide.createIcons();
            reviewModal.style.maxHeight = '95vh';
            reviewModal.style.display = "flex";
            if (rejectionReason) rejectionReason.style.display = "none";
            if (rejectReasonInput) rejectReasonInput.value = "";

            try {
                const res = await fetch(`be_verify_fetch.php?action=details&id=${id}`);
                const data = await res.json();
                if (data.success) {
                    const empData = JSON.parse(data.data.EmployeeData);

                    // Add Hero Stats to Modal
                    const totalImpact = parseFloat(data.data.TotalCost).toLocaleString();
                    const staffCount = empData.length;

                    document.getElementById("modalSubtitle").innerHTML = `
                        <div style="display:flex; gap:16px; margin-top:8px;">
                            <div style="background:rgba(16,185,129,0.1); color:#10b981; padding:4px 12px; border-radius:12px; font-size:12px; font-weight:700;">\u20B1${totalImpact} Total Impact</div>
                            <div style="background:rgba(139,92,246,0.1); color:#8b5cf6; padding:4px 12px; border-radius:12px; font-size:12px; font-weight:700;">${staffCount} Employees Affected</div>
                        </div>
                    `;

                    renderDetailsTable(empData);
                }
            } catch (err) {
                modalTableContainer.innerHTML = '<div style="color:var(--brand-red);">Failed to load data.</div>';
            }
        }
    });

    const renderDetailsTable = (data) => {
        const headers = [
            "EE ID", "Name & Position", "Rating", "Status", "Salary", "Grade Midpoint", "Compa-Ratio",
            "Promote", "Prop. %", "Prop. Increase (\u20B1)", "Basic (New)", "Total Allowances",
            "Gross Salary", "Semi-Monthly", "Daily", "Hourly", "Employer Share", "Full Load",
            "SSS Regular", "SSS WISP", "PhilHealth", "Pag-IBIG", "W. Tax", "Net Pay", "Increase"
        ];

        let html = `
            <div style="overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse; font-size:12px; min-width:2600px;">
                    <thead>
                        <tr style="background:var(--background-alt); border-bottom:2px solid var(--border-color);">
                            ${headers.map(h => `<th style="padding:12px 8px; text-align:left; font-weight:700; color:var(--text-primary); white-space:nowrap;">${h}</th>`).join("")}
                        </tr>
                    </thead>
                    <tbody>
        `;

        data.forEach(emp => {
            html += `
                <tr style="border-bottom:1px solid var(--border-color);">
                    <td style="padding:8px; color:var(--text-secondary); font-family:monospace;">${emp.EmployeeCode || '---'}</td>
                    <td style="padding:8px; white-space:nowrap;">
                        <div style="font-weight:600; color:var(--text-primary); text-transform: capitalize;">${emp.Name || 'Unknown'}</div>
                        <div style="font-size:10px; color:var(--text-secondary);">${emp.Position || '-'}</div>
                    </td>
                    <td style="padding:8px; text-align:center;"><span style="padding:2px 6px; background:rgba(59,130,246,0.1); color:#3b82f6; border-radius:4px; font-weight:700;">${emp.Rating}</span></td>
                    <td style="padding:8px; text-align:center;"><span class="badge submitted" style="font-size:10px; padding:2px 6px;">${emp.Status}</span></td>
                    <td style="padding:8px;">\u20B1${parseFloat(emp.Salary).toLocaleString()}</td>
                    <td style="padding:8px;">\u20B1${parseFloat(emp.GradeMidpoint).toLocaleString()}</td>
                    <td style="padding:8px; text-align:center;">${emp.CompaRatio}</td>
                    <td style="padding:8px; color:var(--brand-purple); font-weight:600;">${emp.Promote || '-'}</td>
                    <td style="padding:8px; text-align:center; font-weight:700;">${emp.PropPct}%</td>
                    <td style="padding:8px; color:var(--brand-green); font-weight:600;">+\u20B1${parseFloat(emp.PropInc).toLocaleString()}</td>
                    <td style="padding:8px; font-weight:700;">\u20B1${parseFloat(emp.BasicNew).toLocaleString()}</td>
                    <td style="padding:8px;">\u20B1${parseFloat(emp.TotalAllowances).toLocaleString()}</td>
                    <td style="padding:8px; font-weight:700; color:var(--brand-blue);">\u20B1${parseFloat(emp.GrossSalary).toLocaleString()}</td>
                    <td style="padding:8px;">\u20B1${parseFloat(emp.SemiMonthly).toLocaleString()}</td>
                    <td style="padding:8px;">\u20B1${parseFloat(emp.Daily).toLocaleString()}</td>
                    <td style="padding:8px;">\u20B1${parseFloat(emp.Hourly).toLocaleString()}</td>
                    <td style="padding:8px;">\u20B1${parseFloat(emp.EmployerShare).toLocaleString()}</td>
                    <td style="padding:8px;">\u20B1${parseFloat(emp.FullLoad).toLocaleString()}</td>
                    <td style="padding:8px; color:var(--brand-red);">\u20B1${parseFloat(emp.SSSRegular).toLocaleString()}</td>
                    <td style="padding:8px; color:var(--brand-red);">\u20B1${parseFloat(emp.SSSWISP).toLocaleString()}</td>
                    <td style="padding:8px; color:var(--brand-red);">\u20B1${parseFloat(emp.PhilHealth).toLocaleString()}</td>
                    <td style="padding:8px; color:var(--brand-red);">\u20B1${parseFloat(emp.PagIBIG).toLocaleString()}</td>
                    <td style="padding:8px; color:var(--brand-red);">\u20B1${parseFloat(emp.WTax).toLocaleString()}</td>
                    <td style="padding:8px; font-weight:700; color:var(--brand-green);">\u20B1${parseFloat(emp.NetPay).toLocaleString()}</td>
                    <td style="padding:8px; font-weight:700; color:var(--brand-green);">+\u20B1${parseFloat(emp.Increase).toLocaleString()}</td>
                </tr>
            `;
        });

        html += '</tbody></table></div>';
        modalTableContainer.innerHTML = html;
        if (window.lucide) window.lucide.createIcons();
    };

    if (closeModal) closeModal.addEventListener("click", () => reviewModal.style.display = "none");
    if (cancelReview) cancelReview.addEventListener("click", () => reviewModal.style.display = "none");

    if (rejectBtn) {
        rejectBtn.addEventListener("click", async () => {
            if (rejectionReason.style.display === "none") {
                rejectionReason.style.display = "block";
                rejectReasonInput.focus();
                return;
            }
            const reason = rejectReasonInput.value.trim();
            if (!reason) {
                Swal.fire("Error", "Please provide a reason for rejection.", "error");
                return;
            }

            const res = await fetch("be_verify_action.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ action: "reject", id: currentDraftId, reason: reason })
            });
            const data = await res.json();
            if (data.success) {
                Swal.fire("Rejected", "Simulation has been rejected.", "success").then(() => location.reload());
            }
        });
    }

    if (approveBtn) {
        approveBtn.addEventListener("click", async () => {
            const result = await Swal.fire({
                title: "Verify Simulation?",
                text: "This will endorse the simulation to the Manager for final review.",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#10b981",
                confirmButtonText: "Yes, Verify"
            });
            if (!result.isConfirmed) return;

            const res = await fetch("be_verify_action.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ action: "approve", id: currentDraftId })
            });
            const data = await res.json();
            if (data.success) {
                Swal.fire("Verified", "Simulation has been verified and forwarded to Manager.", "success").then(() => location.reload());
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
