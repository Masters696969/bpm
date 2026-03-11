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

    // 4. Data Loading & Review Logic
    const simSubmissionsBody = document.getElementById("simSubmissionsBody");
    const reviewModal = document.getElementById("reviewModal");
    const modalDetails = document.getElementById("modalDetails");
    const approveBtn = document.getElementById("approveBtn");
    const rejectBtn = document.getElementById("rejectBtn");
    const closeModal = document.getElementById("closeModal");
    const saveProgressBtn = document.getElementById("saveProgressBtn");

    let currentReviewId = "";
    let currentSimData = null;

    const loadSimSubmissions = async () => {
        if (!simSubmissionsBody) return;
        try {
            const res = await fetch("be_fetch_simulations.php?action=fetch");
            const data = await res.json();
            if (data.success && data.data) {
                const pending = data.data.filter(s => s.Status === 'Verified');
                const approved = data.data.filter(s => s.Status === 'Reviewed');

                if (document.getElementById("submittedSimsCount"))
                    document.getElementById("submittedSimsCount").innerText = pending.length;
                if (document.getElementById("approvedSimsCount"))
                    document.getElementById("approvedSimsCount").innerText = approved.length;

                if (pending.length === 0) {
                    simSubmissionsBody.innerHTML = '<tr><td colspan="5" style="text-align:center; padding:20px;">No pending simulations.</td></tr>';
                } else {
                    simSubmissionsBody.innerHTML = pending.map(s => `
                        <tr>
                            <td>
                                <div style="font-weight:700; color:var(--text-primary);">${s.CycleName}</div>
                                <div style="font-size:11px; color:var(--text-secondary);">Ref: #${s.DraftID}</div>
                            </td>
                            <td style="color:var(--brand-green); font-weight:700; font-size:15px;">\u20B1${parseFloat(s.TotalCost).toLocaleString()}</td>
                            <td><span class="badge verified">${s.Status}</span></td>
                            <td>${new Date(s.CreatedAt).toLocaleDateString()}</td>
                            <td>
                                <button class="action-btn btn-review review-sim-btn" data-id="${s.DraftID}">
                                    <i data-lucide="calculator"></i> Review
                                </button>
                            </td>
                        </tr>
                    `).join("");
                    if (window.lucide) window.lucide.createIcons();
                }
            }
        } catch (e) { console.error("Load Submissions Error:", e); }
    };

    document.addEventListener("click", async (e) => {
        const btn = e.target.closest(".review-sim-btn");
        if (btn) {
            const id = btn.getAttribute("data-id");
            currentReviewId = id;

            document.getElementById("modalTitle").innerText = "Review & Edit Compensation Simulation";
            if (saveProgressBtn) saveProgressBtn.style.display = "block";
            modalDetails.innerHTML = "Loading simulation details...";
            reviewModal.querySelector(".modal-content").style.maxWidth = "1600px";
            reviewModal.style.display = "flex";

            try {
                const res = await fetch(`be_fetch_simulations.php?action=details&id=${id}`);
                const data = await res.json();
                if (data.success) {
                    currentSimData = JSON.parse(data.data.EmployeeData);
                    const hero = document.getElementById("modalHero");
                    if (hero) {
                        hero.innerHTML = `
                            <div class="rem-hero-item">
                                <span class="rem-hero-label">Cycle Name</span>
                                <span class="rem-hero-val">${data.data.CycleName}</span>
                            </div>
                            <div class="rem-hero-item">
                                <span class="rem-hero-label">Staff Count</span>
                                <span class="rem-hero-val">${currentSimData.length} Active</span>
                            </div>
                            <div class="rem-hero-item" id="remHeroImpact">
                                <span class="rem-hero-label">Total Impact</span>
                                <span class="rem-hero-val" style="color:var(--brand-green);">\u20B1${parseFloat(data.data.TotalCost).toLocaleString()}</span>
                            </div>
                        `;
                    }
                    renderSimulationTable();
                } else {
                    throw new Error(data.message || "Failed to fetch details.");
                }
            } catch (err) {
                console.error("Fetch Details Error:", err);
                modalDetails.innerHTML = `<div style="padding:40px; text-align:center; color:var(--brand-red); font-weight:600;">Error: ${err.message}</div>`;
            }
        }
    });

    const renderSimulationTable = () => {
        if (!currentSimData || !Array.isArray(currentSimData)) {
            modalDetails.innerHTML = "No data available.";
            return;
        }

        const headers = [
            "EE ID", "Name & Position", "Rating", "Status", "Salary", "Grade Midpoint", "Compa-Ratio",
            "Promote", "Prop. %", "Prop. Increase (\u20B1)", "Basic (New)", "Total Allowances",
            "Gross Salary", "Semi-Monthly", "Daily", "Hourly", "Employer Share", "Full Load",
            "SSS Regular", "SSS WISP", "PhilHealth", "Pag-IBIG", "W. Tax", "Net Pay", "Increase"
        ];

        const html = `
            <div style="overflow-x:auto;">
                <table class="role-table" style="width:100%; font-size:11px; min-width:2800px;">
                    <thead>
                        <tr>
                            ${headers.map(h => `<th style="padding:12px 8px; text-align:left;">${h}</th>`).join("")}
                        </tr>
                    </thead>
                    <tbody>
                        ${currentSimData.map((e, idx) => `
                            <tr data-idx="${idx}">
                                <td style="padding:8px; font-family:monospace;">${e.EmployeeCode || '---'}</td>
                                <td style="padding:8px; white-space:nowrap;">
                                    <div style="font-weight:600; color:var(--text-primary); text-transform: capitalize;">${e.Name || 'Unknown'}</div>
                                    <div style="font-size:10px; color:var(--text-secondary);">${e.Position || '-'}</div>
                                </td>
                                <td style="padding:8px; text-align:center;">${e.Rating}</td>
                                <td style="padding:8px; text-align:center;"><span class="badge verified">${e.Status}</span></td>
                                <td style="padding:8px;">\u20B1${parseFloat(e.Salary || 0).toLocaleString()}</td>
                                <td style="padding:8px;">\u20B1${parseFloat(e.GradeMidpoint || 0).toLocaleString()}</td>
                                <td style="padding:8px; text-align:center;">${e.CompaRatio}</td>
                                <td style="padding:8px; color:var(--brand-purple); font-weight:600;">${e.Promote || '-'}</td>
                                <td style="padding:8px; text-align:center;">
                                    <input type="number" step="0.1" class="edit-prop-pct" data-idx="${idx}" value="${e.PropPct || 0}" 
                                           style="width:65px; padding:4px 6px; text-align:center; border:1px solid var(--border-color); border-radius:6px; background:var(--background); color:var(--text-primary); font-weight:700;">
                                </td>
                                <td style="padding:8px; color:var(--brand-green); font-weight:600;">
                                    <input type="number" step="1" class="edit-prop-inc" data-idx="${idx}" value="${e.PropInc || 0}" 
                                           style="width:100px; padding:4px 6px; text-align:right; border:1px solid var(--border-color); border-radius:6px; background:var(--background); color:var(--brand-green); font-weight:700;">
                                </td>
                                <td style="padding:8px; font-weight:700;">\u20B1${parseFloat(e.BasicNew).toLocaleString()}</td>
                                <td style="padding:8px;">\u20B1${parseFloat(e.TotalAllowances).toLocaleString()}</td>
                                <td style="padding:8px; font-weight:700; color:var(--brand-blue);">\u20B1${parseFloat(e.GrossSalary).toLocaleString()}</td>
                                <td style="padding:8px;">\u20B1${parseFloat(e.SemiMonthly).toLocaleString()}</td>
                                <td style="padding:8px;">\u20B1${parseFloat(e.Daily).toLocaleString()}</td>
                                <td style="padding:8px;">\u20B1${parseFloat(e.Hourly).toLocaleString()}</td>
                                <td style="padding:8px;">\u20B1${parseFloat(e.EmployerShare).toLocaleString()}</td>
                                <td style="padding:8px;">\u20B1${parseFloat(e.FullLoad).toLocaleString()}</td>
                                <td style="padding:8px; color:var(--brand-red);">\u20B1${parseFloat(e.SSSRegular).toLocaleString()}</td>
                                <td style="padding:8px; color:var(--brand-red);">\u20B1${parseFloat(e.SSSWISP).toLocaleString()}</td>
                                <td style="padding:8px; color:var(--brand-red);">\u20B1${parseFloat(e.PhilHealth).toLocaleString()}</td>
                                <td style="padding:8px; color:var(--brand-red);">\u20B1${parseFloat(e.PagIBIG).toLocaleString()}</td>
                                <td style="padding:8px; color:var(--brand-red);">\u20B1${parseFloat(e.WTax).toLocaleString()}</td>
                                <td style="padding:8px; font-weight:700; color:var(--brand-green);">\u20B1${parseFloat(e.NetPay).toLocaleString()}</td>
                                <td style="padding:8px; font-weight:700; color:var(--brand-green);">+\u20B1${parseFloat(e.Increase).toLocaleString()}</td>
                            </tr>
                        `).join("")}
                    </tbody>
                </table>
            </div>
        `;
        modalDetails.innerHTML = html;

        // Add Listeners
        document.querySelectorAll(".edit-prop-pct").forEach(input => {
            input.addEventListener("input", (ev) => {
                const idx = ev.target.getAttribute("data-idx");
                const pct = parseFloat(ev.target.value) || 0;
                updateCalculations(idx, 'pct', pct);
            });
        });

        document.querySelectorAll(".edit-prop-inc").forEach(input => {
            input.addEventListener("input", (ev) => {
                const idx = ev.target.getAttribute("data-idx");
                const val = parseFloat(ev.target.value) || 0;
                updateCalculations(idx, 'amt', val);
            });
        });
    };

    const updateCalculations = (idx, type, value) => {
        const emp = currentSimData[idx];
        const salary = parseFloat(emp.Salary || 0);

        if (type === 'pct') {
            emp.PropPct = value;
            emp.PropInc = (salary * (value / 100)).toFixed(2);
        } else {
            emp.PropInc = value;
            emp.PropPct = ((value / salary) * 100).toFixed(1);
        }

        // Logic for re-calculating net pay, etc. would normally be here.
        // For now, we update the primary fields and visible new basic.
        emp.BasicNew = salary + parseFloat(emp.PropInc);
        emp.Increase = emp.PropInc; // Simplified for this view
        emp.NetPay = parseFloat(emp.NetPay || 0) + (parseFloat(emp.PropInc) - (parseFloat(emp.PropInc) * 0.1)); // Placeholder tax logic

        const row = document.querySelector(`#modalDetails tbody tr[data-idx="${idx}"]`);
        if (row) {
            row.querySelector(".edit-prop-pct").value = emp.PropPct;
            row.querySelector(".edit-prop-inc").value = emp.PropInc;
            row.cells[10].innerText = `\u20B1${parseFloat(emp.BasicNew).toLocaleString()}`;
            row.cells[23].innerText = `\u20B1${parseFloat(emp.NetPay).toLocaleString()}`;
            row.cells[24].innerText = `+\u20B1${parseFloat(emp.Increase).toLocaleString()}`;
        }

        // Update Total Impact in Hero
        let total = 0;
        currentSimData.forEach(e => total += parseFloat(e.PropInc || 0));
        const impactVal = document.querySelector("#remHeroImpact .rem-hero-val");
        if (impactVal) impactVal.innerHTML = `\u20B1${total.toLocaleString()}`;
    };

    if (approveBtn) {
        approveBtn.addEventListener("click", async () => {
            const result = await Swal.fire({
                title: "Forward to Finance?",
                text: "This will endorse the reviewed simulation for final Finance approval.",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#10b981",
                confirmButtonText: "Yes, Endorse"
            });
            if (!result.isConfirmed) return;

            // Save edits first
            let total = 0;
            currentSimData.forEach(e => total += parseFloat(e.PropInc || 0));

            await fetch("be_fetch_simulations.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ action: "save", id: currentReviewId, employee_data: JSON.stringify(currentSimData), total_cost: total })
            });

            const res = await fetch("be_fetch_simulations.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ action: "approve", id: currentReviewId })
            });
            const data = await res.json();
            if (data.success) {
                Swal.fire("Verified", "Simulation forwarded to Finance successfully.", "success").then(() => location.reload());
            }
        });
    }

    if (saveProgressBtn) {
        saveProgressBtn.addEventListener("click", async () => {
            let total = 0;
            currentSimData.forEach(e => total += parseFloat(e.PropInc || 0));

            const res = await fetch("be_fetch_simulations.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ action: "save", id: currentReviewId, employee_data: JSON.stringify(currentSimData), total_cost: total })
            });
            const data = await res.json();
            if (data.success) {
                Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Progress Saved', showConfirmButton: false, timer: 2000 });
            }
        });
    }
    // Premium Clock Functionality
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
    loadSimSubmissions();
});
