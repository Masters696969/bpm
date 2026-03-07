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

    let currentReviewId = "";
    let currentSimData = null;

    const loadSimSubmissions = async () => {
        if (!simSubmissionsBody) return;
        try {
            const res = await fetch("be_fetch_simulations.php?action=fetch");
            const data = await res.json();
            if (data.success && data.data) {
                const pending = data.data.filter(s => s.Status === 'Endorsed');
                const approved = data.data.filter(s => s.Status === 'Approved');

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
                                <div style="font-size:11px; color:var(--text-secondary);">Compensation Cycle</div>
                            </td>
                            <td style="color:var(--brand-green); font-weight:700; font-size:15px;">\u20B1${parseFloat(s.TotalCost).toLocaleString()}</td>
                            <td><span class="badge ${s.Status.toLowerCase()}">${s.Status}</span></td>
                            <td>${new Date(s.CreatedAt).toLocaleDateString(undefined, { month: 'short', day: 'numeric', year: 'numeric' })}</td>
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
        if (e.target.closest(".review-sim-btn")) {
            const id = e.target.closest(".review-sim-btn").getAttribute("data-id");
            currentReviewId = id;

            document.getElementById("modalTitle").innerText = "Review & Edit Merit Simulation";
            document.getElementById("saveProgressBtn").style.display = "block";
            modalDetails.innerHTML = "Loading simulation details...";
            reviewModal.querySelector(".modal-content").style.maxWidth = "1100px";
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
                                <span class="rem-hero-label">Total Monthly Impact</span>
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

        const html = `
            <table class="role-table" style="width:100%;">
                <thead>
                    <tr>
                        <th style="padding:12px 24px;">Employee</th>
                        <th style="text-align:center; padding:12px;">Current Sal.</th>
                        <th style="text-align:center; padding:12px;">Prop. %</th>
                        <th style="text-align:right; padding:12px;">Monthly Inc.</th>
                        <th style="text-align:right; padding:12px 24px;">New Salary</th>
                    </tr>
                </thead>
                <tbody>
                    ${currentSimData.map((e, idx) => `
                        <tr>
                            <td style="padding:14px 24px;">
                                <div style="font-weight:600; color:var(--text-primary); text-transform: capitalize;">${e.name || 'Unknown'}</div>
                                <div style="font-size:11px; color:var(--text-secondary);">${e.department || '-'} | ${e.grade || '-'}</div>
                            </td>
                            <td style="text-align:center;">\u20B1${parseFloat(e.current_salary || 0).toLocaleString()}</td>
                            <td style="text-align:center;">
                                <input type="number" step="0.1" class="edit-prop-pct" data-idx="${idx}" value="${e.prop_pct || 0}" 
                                       style="width:75px; padding:6px 10px; text-align:center; border:1px solid var(--border-color); border-radius:8px; background:var(--background); color:var(--text-primary); font-weight:600;">
                            </td>
                            <td style="text-align:right; font-weight:600; color:var(--brand-green);">+\u20B1${parseFloat(e.prop_inc || 0).toLocaleString()}</td>
                            <td style="text-align:right; padding:14px 24px; font-weight:700; color:var(--brand-blue);">\u20B1${parseFloat(e.new_salary || 0).toLocaleString()}</td>
                        </tr>
                    `).join("")}
                </tbody>
            </table>
        `;
        modalDetails.innerHTML = html;

        // Listen for changes
        document.querySelectorAll(".edit-prop-pct").forEach(input => {
            input.addEventListener("input", (ev) => {
                const idx = ev.target.getAttribute("data-idx");
                const newPct = parseFloat(ev.target.value) || 0;
                updateEmployeeRow(idx, newPct);
            });
        });
    };

    const updateEmployeeRow = (idx, newPct) => {
        const emp = currentSimData[idx];
        emp.prop_pct = newPct;
        const currentSal = parseFloat(emp.current_salary || 0);
        emp.prop_inc = (currentSal * (newPct / 100)).toFixed(2);
        emp.new_salary = (currentSal + parseFloat(emp.prop_inc)).toFixed(2);

        const row = document.querySelectorAll("#modalDetails tbody tr")[idx];
        if (row) {
            row.cells[3].innerHTML = `+\u20B1${parseFloat(emp.prop_inc).toLocaleString()}`;
            row.cells[4].innerHTML = `\u20B1${parseFloat(emp.new_salary).toLocaleString()}`;
        }

        // Update Hero Total
        let total = 0;
        currentSimData.forEach(e => total += parseFloat(e.prop_inc || 0));
        const impactVal = document.querySelector("#remHeroImpact .rem-hero-val");
        if (impactVal) impactVal.innerHTML = `\u20B1${total.toLocaleString()}`;
    };

    // Modal Actions
    if (closeModal) {
        closeModal.addEventListener("click", () => {
            reviewModal.style.display = "none";
            document.getElementById("modalHero").innerHTML = "";
            document.getElementById("saveProgressBtn").style.display = "none";
        });
    }

    const cancelModal = document.getElementById("cancelModal");
    if (cancelModal) {
        cancelModal.addEventListener("click", () => {
            reviewModal.style.display = "none";
            document.getElementById("modalHero").innerHTML = "";
            document.getElementById("saveProgressBtn").style.display = "none";
        });
    }

    if (approveBtn) {
        approveBtn.addEventListener("click", async () => {
            const result = await Swal.fire({
                title: "Approve Simulation?",
                text: "This will forward the data to Finance for final application.",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#10b981",
                confirmButtonText: "Yes, Approve"
            });
            if (!result.isConfirmed) return;

            try {
                // Save current edits first
                let total = 0;
                currentSimData.forEach(e => total += parseFloat(e.prop_inc || 0));

                await fetch("be_fetch_simulations.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({
                        action: "save",
                        id: currentReviewId,
                        employee_data: JSON.stringify(currentSimData),
                        total_cost: total
                    })
                });

                const res = await fetch("be_fetch_simulations.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ action: "approve", id: currentReviewId })
                });
                const data = await res.json();
                if (data.success) {
                    Swal.fire("Approved", "Forwarded to Finance successfully.", "success").then(() => location.reload());
                } else {
                    Swal.fire("Error", data.message, "error");
                }
            } catch (err) { Swal.fire("Error", "Network error.", "error"); }
        });
    }

    if (rejectBtn) {
        rejectBtn.addEventListener("click", async () => {
            const result = await Swal.fire({
                title: "Reject Simulation?",
                text: "Are you sure you want to reject this batch?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#ef4444",
                confirmButtonText: "Yes, Reject"
            });
            if (!result.isConfirmed) return;

            try {
                const res = await fetch("be_fetch_simulations.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ action: "reject", id: currentReviewId })
                });
                const data = await res.json();
                if (data.success) {
                    Swal.fire("Rejected", "Simulation marked as rejected.", "success").then(() => location.reload());
                } else {
                    Swal.fire("Error", data.message, "error");
                }
            } catch (err) { Swal.fire("Error", "Network error.", "error"); }
        });
    }

    const saveProgressBtn = document.getElementById("saveProgressBtn");
    if (saveProgressBtn) {
        saveProgressBtn.addEventListener("click", async () => {
            if (!currentSimData) return;
            let total = 0;
            currentSimData.forEach(e => total += parseFloat(e.prop_inc || 0));
            try {
                const res = await fetch("be_fetch_simulations.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({
                        action: "save",
                        id: currentReviewId,
                        employee_data: JSON.stringify(currentSimData),
                        total_cost: total
                    })
                });
                const data = await res.json();
                if (data.success) {
                    Swal.fire("Saved", "Changes saved successfully.", "success");
                } else {
                    Swal.fire("Error", data.message, "error");
                }
            } catch (err) { Swal.fire("Error", "Network error.", "error"); }
        });
    }

    loadSimSubmissions();
    if (window.lucide) window.lucide.createIcons();
    initClock();
});

function initClock() {
    const clockEl = document.getElementById('realTimeClock');
    if (!clockEl) return;
    const update = () => {
        const days = ['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'];
        const months = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];
        const now = new Date();
        const hrs = now.getHours();
        const ampm = hrs >= 12 ? 'PM' : 'AM';
        const h = (hrs % 12 || 12).toString().padStart(2, '0');
        const m = now.getMinutes().toString().padStart(2, '0');
        const s = now.getSeconds().toString().padStart(2, '0');
        clockEl.textContent = `${days[now.getDay()]}, ${months[now.getMonth()]} ${now.getDate()}, ${now.getFullYear()}, ${h}:${m}:${s} AM`;
    };
    setInterval(update, 1000);
    update();
}
