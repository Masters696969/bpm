// Global function for the onclick in PHP
window.togglePromoteInline = function(el) {
    const row = el.closest('tr');
    const label = el;
    const inline = row.querySelector('.promote-inline');
    if (inline.style.display === 'none') {
        inline.style.display = 'flex';
        label.style.display = 'none';
    } else {
        inline.style.display = 'none';
        label.style.display = 'flex';
    }
};

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

    themeToggle.addEventListener("click", () => {
        body.classList.toggle("dark-mode");
        localStorage.setItem("theme", body.classList.contains("dark-mode") ? "dark" : "light");
    });

    // 2. Sidebar & Mobile Logic
    sidebarToggle.addEventListener("click", () => {
        sidebar.classList.toggle("collapsed");
        localStorage.setItem("sidebarCollapsed", sidebar.classList.contains("collapsed"));
    });

    if (localStorage.getItem("sidebarCollapsed") === "true") sidebar.classList.add("collapsed");

    mobileMenuBtn.addEventListener("click", () => sidebar.classList.toggle("mobile-open"));

    // 3. Submenu Logic
    document.querySelectorAll(".nav-item.has-submenu").forEach((item) => {
        item.addEventListener("click", (e) => {
            const module = item.getAttribute("data-module");
            const submenu = document.getElementById(`submenu-${module}`);
            submenu.classList.toggle("active");
            item.classList.toggle("active");
        });
    });

    // 4. Tab Switching Logic
    const tabButtons = document.querySelectorAll(".tab-btn");
    const tabPanels = document.querySelectorAll(".tab-panel");

    if (tabButtons.length > 0) {
        tabButtons.forEach(btn => {
            btn.addEventListener("click", (e) => {
                const targetTab = btn.getAttribute("data-tab");
                if (!targetTab) return;

                // BPM Enforcement: Prevent manual opening of Simulation/Salary Scale Tabs
                if ((targetTab === "simulation" || targetTab === "salary-scale") && !window.simulationUnlocked) {
                    e.preventDefault();
                    if (window.Swal) Swal.fire('Locked', 'Please click "Start Simulation Cycle" or "Continue" a Draft to access this screen.', 'warning');
                    return;
                }

                switchTab(targetTab);
            });
        });

        // Initialize UI State
        const simBtn = document.querySelector('.tab-btn[data-tab="simulation"]');
        if (simBtn) simBtn.classList.add('locked-tab');
        const scaleBtn = document.querySelector('.tab-btn[data-tab="salary-scale"]');
        if (scaleBtn) scaleBtn.classList.add('locked-tab');
    }

    // 5. Start Simulation Cycle Button
    const startCycleBtn = document.getElementById("startCycleBtn");
    if (startCycleBtn) {
        startCycleBtn.addEventListener("click", () => {
            // bpm auto-save on start (User Request)
            saveSimulationDraft(true);
            
            // Unlock simulation tab and switch to it
            switchTab('simulation');
        });
    }

    // 5.1 Request Finance Budget logic
    const btnRequestBudget = document.getElementById("btnRequestBudget");
    if (btnRequestBudget) {
        btnRequestBudget.addEventListener("click", () => {
            const amount = parseFloat(document.getElementById("budgetAllocation").value) || 0;
            if (amount <= 0) {
                Swal.fire('Error', 'Please enter a valid budget amount.', 'error');
                return;
            }

            Swal.fire({
                title: 'Request Budget from Finance?',
                text: `You are requesting \u20B1${amount.toLocaleString()} for this compensation cycle.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3b82f6',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, Send Request',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    btnRequestBudget.disabled = true;
                    btnRequestBudget.innerHTML = '<i data-lucide="loader" class="animate-spin" style="width:14px; height:14px;"></i><span>Sending...</span>';
                    if (window.lucide) window.lucide.createIcons();

                    const formData = new URLSearchParams();
                    formData.append('amount', amount);
                    formData.append('period_id', 1); // HR Period ID 1

                    fetch('backend/be_request_budget.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Sent!', data.message || 'Request sent to Finance server.', 'success');
                            
                            // Update UI immediately without reload if possible
                            const badge = document.getElementById("budgetBadge");
                            if (badge) {
                                badge.innerText = "PENDING";
                                badge.className = "status-badge status-badge-warning";
                            }
                            btnRequestBudget.disabled = true;
                            btnRequestBudget.innerHTML = 'Request Finance';
                            
                            // Update requested amount text
                            const reqText = document.getElementById("requestedAmountText");
                            if (reqText) {
                                reqText.innerText = amount.toLocaleString(undefined, { minimumFractionDigits: 2 });
                            } else {
                                // If the text div didn't exist, we might need a reload or a smarter UI update
                                setTimeout(() => location.reload(), 2000);
                            }
                        } else {
                            Swal.fire('Request Failed', data.message || 'Error communicating with Finance server.', 'error');
                            btnRequestBudget.disabled = false;
                            btnRequestBudget.innerHTML = 'Request Finance';
                        }
                        if (window.lucide) window.lucide.createIcons();
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire('System Error', 'Could not reach the HR backend.', 'error');
                        btnRequestBudget.disabled = false;
                        btnRequestBudget.innerHTML = 'Request Finance';
                        if (window.lucide) window.lucide.createIcons();
                    });
                }
            });
        });
    }



    function saveSimulationDraft(isAuto = false) {
        const saveBtn = document.getElementById("saveDraftBtn");
        if (saveBtn) {
            saveBtn.disabled = true;
            saveBtn.innerHTML = isAuto ? '<i data-lucide="loader" class="animate-spin"></i><span>Auto-Saving...</span>' : '<i data-lucide="loader" class="animate-spin"></i><span>Saving...</span>';
            if (window.lucide) window.lucide.createIcons();
        }

        const data = prepareDraftData();
        const params = new URLSearchParams();
        for (const key in data) {
            params.append(key, typeof data[key] === 'object' ? JSON.stringify(data[key]) : data[key]);
        }

        return fetch('backend/save_draft.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: params
        })
            .then(res => res.text()) // Get as text first
            .then(text => {
                let data;
                try {
                    data = JSON.parse(text);
                } catch (e) {
                    console.error("Server returned non-JSON:", text);
                    throw new Error("Invalid JSON response from server. Check console for details.");
                }

                if (saveBtn) {
                    saveBtn.innerHTML = '<i data-lucide="save"></i><span>Save Draft</span>';
                    saveBtn.disabled = false;
                    if (window.lucide) window.lucide.createIcons();
                }

                if (data.success) {
                    if (!isAuto) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Draft Saved Successfully',
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true
                        }).then(() => location.reload());
                    } else {
                        console.log("Auto-save successful");
                    }
                } else {
                    Swal.fire('Error', data.message || 'Failed to save draft.', 'error');
                }
                return data;
            })
            .catch(err => {
                if (saveBtn) {
                    saveBtn.innerHTML = '<i data-lucide="save"></i><span>Save Draft</span>';
                    saveBtn.disabled = false;
                    if (window.lucide) window.lucide.createIcons();
                }
                console.error(err);
                Swal.fire('Error', 'Save failure: ' + err.message, 'error');
                throw err;
            });
    }

    function switchTab(tabId) {
        if (tabId === 'simulation' || tabId === 'salary-scale') {
            window.simulationUnlocked = true;
            document.querySelectorAll('.tab-btn[data-tab="simulation"], .tab-btn[data-tab="salary-scale"]').forEach(btn => {
                btn.classList.remove('locked-tab');
            });
        }
        const btn = document.querySelector(`.tab-btn[data-tab="${tabId}"]`);
        const panel = document.getElementById(tabId);

        if (btn && panel) {
            tabButtons.forEach(b => b.classList.remove("active"));
            btn.classList.add("active");

            tabPanels.forEach(p => p.classList.remove("active"));
            panel.classList.add("active");

            if (window.lucide) window.lucide.createIcons();
        }
    }

    // 5. Tracking & View Logic
    document.addEventListener("click", (e) => {
        const trackBtn = e.target.closest(".btn-track-proposal");
        const viewPropBtn = e.target.closest(".btn-view-proposal");
        const viewDraftBtn = e.target.closest(".btn-view-draft");

        if (trackBtn || viewPropBtn) {
            const cycleName = (trackBtn || viewPropBtn).getAttribute("data-cycle-name");
            const modalTitle = trackBtn ? "Proposal Tracking View" : "Proposal Details (Read-Only)";
            fetchTrackingDetails(cycleName, `backend/be_track_proposals.php?action=fetch_simulation_details&cycle_name=${encodeURIComponent(cycleName)}`, modalTitle);
        } else if (viewDraftBtn) {
            const draftId = viewDraftBtn.getAttribute("data-draft-id");
            const cycleName = viewDraftBtn.getAttribute("data-cycle-name");
            fetchTrackingDetails(cycleName, `backend/be_track_proposals.php?action=fetch_draft_details&draft_id=${draftId}`, "Draft Details (Read-Only)");
        }
    });

    function fetchTrackingDetails(cycleName, url, title = "Proposal Details") {
        const modal = document.getElementById("trackingModal");
        const list = document.getElementById("trackItemsList");
        
        const modalHeaderTitle = modal.querySelector("h3");
        if (modalHeaderTitle) modalHeaderTitle.innerText = title;

        document.getElementById("trackCycleName").innerText = "Cycle: " + cycleName;
        modal.style.display = "block";
        list.innerHTML = '<tr><td colspan="5" style="text-align:center; padding:20px;">Loading details...</td></tr>';

        fetch(url)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const p = data.details;
                    const badge = document.getElementById("trackStatusBadge");
                    badge.innerText = p.Status;
                    badge.className = "badge " + p.Status.toLowerCase();
                    
                    const financeRefRow = document.getElementById("trackFinanceRef").closest("div");
                    const lastSyncRow = document.getElementById("trackLastSync").closest("div");

                    if (p.Status === 'Draft') {
                        document.getElementById("trackFinanceRef").innerText = "Not Sent Yet";
                        if (lastSyncRow) lastSyncRow.style.opacity = "0.5";
                    } else {
                        document.getElementById("trackFinanceRef").innerText = p.FinanceRef || "Awaiting Sync...";
                        if (lastSyncRow) lastSyncRow.style.opacity = "1";
                    }

                    document.getElementById("trackLastSync").innerText = p.UpdatedAt ? new Date(p.UpdatedAt).toLocaleString() : new Date(p.CreatedAt).toLocaleString();
                    
                    document.getElementById("trackBudget").innerText = "₱" + parseFloat(p.TotalBudget || 5000000).toLocaleString();
                    document.getElementById("trackImpact").innerText = "₱" + parseFloat(p.TotalImpact || 0).toLocaleString();
                    document.getElementById("trackEECount").innerText = data.items.length + " EE";
                    
                    document.getElementById("trackDept").innerText = p.Department || "All Departments";
                    document.getElementById("trackBy").innerText = p.ProposedByName || "System";
                    document.getElementById("trackPropID").innerText = p.ProposalID;

                    data.items.sort((a, b) => (a.FirstName || "").localeCompare(b.FirstName || ""));

                    let html = "";
                    data.items.forEach(item => {
                        const oldSal = parseFloat(item.OldSalary || 0);
                        const structureAdj = parseFloat(item.MarketAdjustment || 0);
                        const meritPct = parseFloat(item.MeritPct || 0);
                        const meritAmt = parseFloat(item.IncreaseAmount || 0);
                        const newSal = parseFloat(item.NewSalary || (oldSal + structureAdj + meritAmt));

                        html += `
                            <tr>
                                <td>
                                    <div style="font-weight:600;">${item.FirstName} ${item.LastName}</div>
                                    <div style="font-size:10px; color:#6b7280;">${item.EmployeeCode}</div>
                                </td>
                                <td>₱${oldSal.toLocaleString()}</td>
                                <td style="color:#3b82f6; font-weight:600;">+₱${structureAdj.toLocaleString()}</td>
                                <td style="color:#f59e0b; font-weight:600;">${meritPct.toFixed(1)}%</td>
                                <td style="color:#10b981; font-weight:600;">+₱${meritAmt.toLocaleString()}</td>
                                <td style="font-weight:600; border-left: 2px solid #f3f4f6;">₱${newSal.toLocaleString()}</td>
                            </tr>
                        `;
                    });
                    list.innerHTML = html;
                    if (window.lucide) lucide.createIcons();
                } else {
                    if (window.Swal) Swal.fire("Error", data.message || "Could not fetch details", "error");
                    modal.style.display = "none";
                }
            })
            .catch(err => {
                console.error(err);
                if (window.Swal) Swal.fire("Error", "System error occurred", "error");
                modal.style.display = "none";
            });
    }

    // 6. Simulation & Proposal Logic
    const tableInputs = document.querySelectorAll(".table-input");
    const submitProposalBtn = document.getElementById("submitProposalBtn");

    const parseCurrency = (el) => {
        if (!el) return 0;
        const val = parseFloat(el.innerText.replace(/[^0-9.-]/g, ""));
        return isNaN(val) ? 0 : val;
    };

    function calculateDeductions() {
        let totalImpact = 0;
        const totalBudget = parseFloat(document.getElementById("budgetAllocation")?.value) || 5000000;
        
        let visibleCount = 0;
        const simRows = document.querySelectorAll(".simulation-table tbody tr.sim-row");
        simRows.forEach(row => {
            if (row.style.display !== "none") visibleCount++;
        });
        const staffCountEl = document.getElementById("simStaffCount");
        if (staffCountEl) staffCountEl.innerText = `${visibleCount} Active`;

        document.querySelectorAll(".simulation-table tbody tr.sim-row").forEach(row => {
            const originalPay = parseFloat(row.getAttribute("data-original-base")) || 0;
            const midPay = parseFloat(row.getAttribute("data-midpoint")) || 0;
            const minPay = parseFloat(row.getAttribute("data-min-salary")) || 0;
            const maxPay = parseFloat(row.getAttribute("data-max-salary")) || 0;
            const rating = parseFloat(row.getAttribute("data-rating")) || 0;

            // 1. Effective Salary: If below min, "it should be the min" in the display/base
            // But we keep track of the original for impact.
            const baseSalaryForSim = parseFloat(row.getAttribute("data-base-salary")) || originalPay;
            const displayedSalary = Math.max(baseSalaryForSim, minPay);
            
            const meritPctInput = row.querySelector(".prop-increase-input");
            let meritPct = parseFloat(meritPctInput?.value) || 0;

            // 2. Merit Matrix Enforcement
            let rangeMin = 0, rangeMax = 0;
            if (rating >= 5) { rangeMin = 4; rangeMax = 6; }
            else if (rating >= 4) { rangeMin = 3; rangeMax = 4; }
            else if (rating >= 3) { rangeMin = 2; rangeMax = 3; }
            else if (rating >= 2) { rangeMin = 0; rangeMax = 1; }
            
            const hint = row.querySelector(".merit-range-hint");
            if (hint) hint.innerText = rating > 0 ? `Allowed: ${rangeMin}-${rangeMax}%` : "Lock: 0%";

            if (meritPct > rangeMax && rating > 0) {
                meritPct = rangeMax;
                if (meritPctInput) meritPctInput.value = meritPct.toFixed(1);
            }
            if (rating <= 0) {
                meritPct = 0;
                if (meritPctInput) meritPctInput.value = 0;
            }

            // 3. Structure Adjustment: Gap between official current salary and the new floor
            const structureAdj = Math.max(0, minPay - originalPay);
            const adjCell = row.querySelector(".market-adjustment");
            if (adjCell) adjCell.innerText = `\u20B1${structureAdj.toLocaleString()}`;

            // 4. Merit Increase = Old Salary × (Merit % / 100)
            const meritIncrease = Math.round(originalPay * (meritPct / 100));

            // 5. New Salary = Old Salary + Structure Adjustment + Merit Increase
            const newSalary = originalPay + structureAdj + meritIncrease;

            // 6. Compa Ratio = New Salary / Mid (Usually on New Salary or base?)
            const compaRatio = midPay > 0 ? (newSalary / midPay) * 100 : 0;

            // 7. Total Impact = (New Salary - Official Current Salary)
            const rowImpact = Math.max(0, newSalary - originalPay);
            totalImpact += rowImpact;

            // 8. Band Status & Salary Cell Display
            const bandCell = row.querySelector(".band-status-cell");
            const salaryCell = row.querySelector(".current-pay");
            if (bandCell) {
                const badge = bandCell.querySelector(".badge");
                if (displayedSalary < minPay) {
                    badge.innerText = "Below Minimum";
                    badge.style.background = "#ef4444";
                    badge.style.color = "white";
                    if (salaryCell) { salaryCell.style.color = "#ef4444"; salaryCell.style.fontWeight = "bold"; }
                } else if (maxPay > 0 && displayedSalary > maxPay) {
                    badge.innerText = "Above Maximum";
                    badge.style.background = "#f59e0b";
                    badge.style.color = "white";
                    if (salaryCell) { salaryCell.style.color = "#f59e0b"; salaryCell.style.fontWeight = "bold"; }
                } else {
                    badge.innerText = "Within Range";
                    badge.style.background = "#10b981";
                    badge.style.color = "white";
                    if (salaryCell) { salaryCell.style.color = ""; salaryCell.style.fontWeight = ""; }
                    
                    if (structureAdj > 0 || (baseSalaryForSim > originalPay)) {
                        badge.innerText = "Adjusted to Min";
                        badge.style.background = "#3b82f6";
                        if (salaryCell) { salaryCell.style.color = "#3b82f6"; salaryCell.style.fontWeight = "bold"; }
                    }
                }
            }

            // Update row UI
            const compaCell = row.querySelector(".compa-ratio");
            if (compaCell) {
                compaCell.innerText = `${compaRatio.toFixed(1)}%`;
                compaCell.style.color = compaRatio > 110 ? "#ef4444" : (compaRatio < 80 ? "#eab308" : "#10b981");
            }

            const incAmtCell = row.querySelector(".prop-increase-amount");
            if (incAmtCell) incAmtCell.innerText = `\u20B1${meritIncrease.toLocaleString()}`;

            const newSalCell = row.querySelector(".proposed-gross");
            if (newSalCell) newSalCell.innerText = `\u20B1${newSalary.toLocaleString()}`;

            const impactCell = row.querySelector(".row-impact");
            if (impactCell) impactCell.innerText = `\u20B1${rowImpact.toLocaleString()}`;

            if (salaryCell) salaryCell.innerText = `\u20B1${displayedSalary.toLocaleString()}`;
        });

        // 8. Dashboard Summary & Budget Control
        const monthlyImpactEl = document.getElementById("totalMonthlyImpact");
        if (monthlyImpactEl) monthlyImpactEl.innerText = `+\u20B1${totalImpact.toLocaleString()}`;

        const yearlyImpactEl = document.getElementById("totalYearlyImpact");
        if (yearlyImpactEl) yearlyImpactEl.innerText = `\u20B1${(totalImpact * 12).toLocaleString()}`;

        const remainingBudgetEl = document.getElementById("remainingBudget");
        const remainingBudget = totalBudget - (totalImpact * 12);
        if (remainingBudgetEl) {
            remainingBudgetEl.innerText = `\u20B1${remainingBudget.toLocaleString()}`;
            remainingBudgetEl.style.color = remainingBudget < 0 ? "#ef4444" : "#10b981";
        }

        // Block Submission if Budget Exceeded
        const sendBtn = document.getElementById("sendProposalBtn");
        if (sendBtn) {
            if (remainingBudget < 0) {
                sendBtn.disabled = true;
                sendBtn.style.opacity = "0.5";
                sendBtn.title = "Annual budget exceeded. Adjust salary or merit percentages.";
                sendBtn.classList.remove("btn-blue");
                sendBtn.classList.add("btn-secondary");
            } else {
                sendBtn.disabled = false;
                sendBtn.style.opacity = "1";
                sendBtn.title = "";
                sendBtn.classList.add("btn-blue");
                sendBtn.classList.remove("btn-secondary");
            }
        }
    }


    // Auto-Simulation & Filters Logic
    const runAutoSimBtn = document.getElementById("runAutoSim");
    if (runAutoSimBtn) {
        runAutoSimBtn.addEventListener("click", () => {
            runAutoSimulation();
            if (window.Swal) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Auto-Simulation applied based on Merit Matrix rules.',
                    showConfirmButton: false,
                    timer: 3000
                });
            }
        });
    }

    const deptFilterBtn = document.getElementById("deptFilter");

    if (deptFilterBtn) {
        deptFilterBtn.addEventListener("change", (e) => {
            const val = e.target.value.toLowerCase();
            document.querySelectorAll(".simulation-table tbody tr.sim-row").forEach(row => {
                const dept = (row.getAttribute("data-department") || "").toLowerCase();
                if (val === "all" || dept === val) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
            calculateDeductions();
        });
    }

    function runAutoSimulation() {
        document.querySelectorAll(".simulation-table tbody tr.sim-row").forEach(row => {
            const recommendedPct = parseFloat(row.getAttribute("data-recommended-pct")) || 0;
            const inputEl = row.querySelector(".prop-increase-input");
            if (inputEl) {
                inputEl.value = recommendedPct.toFixed(1);
                calculateDeductions();
            }
        });
    }

    // Salary Scale Interaction
    document.querySelectorAll(".sim-min-salary, .sim-max-salary").forEach(input => {
        input.addEventListener("input", (e) => {
            const row = e.target.closest("tr");
            const minInput = row.querySelector(".sim-min-salary");
            const maxInput = row.querySelector(".sim-max-salary");
            const midInput = row.querySelector(".sim-mid-salary");
            const spreadCell = row.querySelector(".sim-spread-cell");

            const min = parseFloat(minInput.value) || 0;
            const max = parseFloat(maxInput.value) || 0;

            // 1. Compute Midpoint
            const midpoint = (min + max) / 2;
            midInput.value = Math.round(midpoint);

            // 2. Compute Spread
            const spread = min > 0 ? ((max - min) / min) * 100 : 0;
            if (spreadCell) spreadCell.innerText = `${spread.toFixed(1)}%`;

            // 3. Propagate to Simulation
            const gradeId = row.getAttribute("data-grade-id");
            document.querySelectorAll(`.simulation-table tr.sim-row[data-grade-id="${gradeId}"]`).forEach(simRow => {
                simRow.setAttribute("data-min-salary", min);
                simRow.setAttribute("data-midpoint", midpoint);
                simRow.setAttribute("data-max-salary", max);

                // Update UI Cells in Simulation
                if (simRow.querySelector(".grade-min")) simRow.querySelector(".grade-min").innerText = `\u20B1${min.toLocaleString()}`;
                if (simRow.querySelector(".grade-midpoint")) simRow.querySelector(".grade-midpoint").innerText = `\u20B1${midpoint.toLocaleString()}`;
                if (simRow.querySelector(".grade-max")) simRow.querySelector(".grade-max").innerText = `\u20B1${max.toLocaleString()}`;

                // Floor Logic: If Salary < Min, Salary = Min
                const currentSalary = parseFloat(simRow.getAttribute("data-base-salary")) || 0;
                if (currentSalary < min) {
                    simRow.setAttribute("data-base-salary", min);
                    const salaryCell = simRow.querySelector(".current-pay");
                    if (salaryCell) {
                        salaryCell.innerText = `\u20B1${min.toLocaleString()}`;
                        salaryCell.style.color = "#2ca078";
                        salaryCell.style.fontWeight = "bold";
                    }
                }
            });

            calculateDeductions();
        });
    });




    if (tableInputs.length > 0) {
        tableInputs.forEach(input => {
            input.addEventListener("input", (e) => {
                calculateDeductions();
            });
        });
    }

    // 6. SimulationSalary Scale Logic
    const simMinInputs = document.querySelectorAll(".sim-min-salary");
    const simMaxInputs = document.querySelectorAll(".sim-max-salary");

    function updateGradeInConfig(gradeId, min, max) {
        if (!window.compConfig || !window.compConfig.salaryGrades) return;
        const grade = window.compConfig.salaryGrades.find(g => g.SalaryGradeID == gradeId);
        if (grade) {
            grade.MinSalary = min;
            grade.MaxSalary = max;
            grade.MidSalary = (min + max) / 2;
        }
    }

    function refreshSimulationTableForGrade(gradeId) {
        document.querySelectorAll(`.simulation-table tbody tr[data-grade-id="${gradeId}"]`).forEach(row => {
            const grade = window.compConfig.salaryGrades.find(g => g.SalaryGradeID == gradeId);
            if (grade) {
                const newMin = parseFloat(grade.MinSalary);
                const newMid = parseFloat(grade.MidSalary);
                const newMax = parseFloat(grade.MaxSalary);

                row.setAttribute("data-min-salary", newMin);
                row.setAttribute("data-midpoint", newMid);
                row.setAttribute("data-max-salary", newMax);
                
                // UI Sync
                const minCell = row.querySelector(".grade-min");
                const midCell = row.querySelector(".grade-midpoint");
                const maxCell = row.querySelector(".grade-max");
                if (minCell) minCell.innerText = `\u20B1${newMin.toLocaleString(undefined, { minimumFractionDigits: 0 })}`;
                if (midCell) midCell.innerText = `\u20B1${newMid.toLocaleString(undefined, { minimumFractionDigits: 0 })}`;
                if (maxCell) maxCell.innerText = `\u20B1${newMax.toLocaleString(undefined, { minimumFractionDigits: 0 })}`;

                // bpm: Salary Floor Logic
                const originalBase = parseFloat(row.getAttribute("data-original-base")) || 0;
                let currentEffectiveBase = originalBase;

                if (originalBase < newMin) {
                    currentEffectiveBase = newMin;
                    row.classList.add("salary-bumped");
                } else {
                    row.classList.remove("salary-bumped");
                }

                row.setAttribute("data-base-salary", currentEffectiveBase);
                const salaryCell = row.querySelector(".current-pay");
                if (salaryCell) {
                    salaryCell.innerText = `\u20B1${currentEffectiveBase.toLocaleString(undefined, { minimumFractionDigits: 0 })}`;
                    if (originalBase < newMin) {
                        salaryCell.style.color = "var(--brand-green)";
                        salaryCell.style.fontWeight = "bold";
                    } else {
                        salaryCell.style.color = "";
                        salaryCell.style.fontWeight = "";
                    }
                }

                // Update Grade Label
                const gradeLabel = row.querySelector(".grade-label");
                if (gradeLabel) gradeLabel.innerText = `SG-${grade.GradeLevel}`;

                // Trigger row recalculation
                const input = row.querySelector(".prop-increase-input");
                if (input) {
                    const evt = new Event('input', { bubbles: true });
                    input.dispatchEvent(evt);
                }
            }
        });
        calculateDeductions();
    }

    document.querySelectorAll(".sim-min-salary, .sim-max-salary").forEach(input => {
        input.addEventListener("input", (e) => {
            const row = e.target.closest("tr");
            const gradeId = row.getAttribute("data-grade-id");
            const min = parseFloat(row.querySelector(".sim-min-salary").value) || 0;
            const max = parseFloat(row.querySelector(".sim-max-salary").value) || 0;
            const mid = (min + max) / 2;
            const spread = min > 0 ? ((max - min) / min) * 100 : 0;

            row.querySelector(".sim-mid-salary").value = mid.toFixed(0);
            row.querySelector(".sim-spread-cell").innerText = `${spread.toFixed(1)}%`;

            updateGradeInConfig(gradeId, min, max);
            refreshSimulationTableForGrade(gradeId);
        });
    });

    // 7. Send Proposal Logic
    const sendProposalBtn = document.getElementById("sendProposalBtn");
    if (sendProposalBtn) {
        sendProposalBtn.addEventListener("click", async () => {
            const totalImpactStr = document.getElementById("totalMonthlyImpact")?.innerText || "0";
            const remainingBudgetStr = document.getElementById("remainingBudget")?.innerText || "0";

            const result = await Swal.fire({
                title: 'Send Proposal to Finance?',
                html: `
                    <div style="text-align: left; font-size: 14px;">
                        <p>You are about to submit the compensation simulation results for review.</p>
                        <div style="background: rgba(59, 130, 246, 0.05); padding: 12px; border-radius: 8px; border: 1px solid rgba(59, 130, 246, 0.1);">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                                <span style="color: var(--text-secondary);">Total Monthly Impact:</span>
                                <span style="font-weight: 600; color: var(--brand-blue);">${totalImpactStr}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: var(--text-secondary);">Remaining Budget (Annual):</span>
                                <span style="font-weight: 600; color: #10b981;">${remainingBudgetStr}</span>
                            </div>
                        </div>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3b82f6',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, Send Proposal',
                reverseButtons: true
            });

            if (result.isConfirmed) {
                sendProposalBtn.disabled = true;
                const originalText = sendProposalBtn.innerHTML;
                sendProposalBtn.innerHTML = '<i data-lucide="loader" class="animate-spin"></i><span>Submitting...</span>';
                if (window.lucide) window.lucide.createIcons();

                const data = prepareDraftData();
                const deptFilter = document.getElementById("deptFilter");
                const deptCode = deptFilter ? (deptFilter.value === 'all' ? 'GLOBAL' : deptFilter.value) : 'GLOBAL';

                const params = new URLSearchParams();
                params.append('cycle_name', data.cycle_name);
                params.append('period_id', data.period_id);
                params.append('dept_code', deptCode);
                params.append('total_budget', data.total_budget);
                params.append('total_impact', data.total_cost); // monthly impact
                params.append('remaining_budget', (data.total_budget - (data.total_cost * 12)));
                params.append('employee_data', JSON.stringify(data.detailed_employee_data));
                params.append('salary_scale_data', JSON.stringify(data.salary_scale_data));

                try {
                    const res = await fetch('backend/be_send_proposal.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: params
                    });
                    const resText = await res.text();
                    let response;
                    try {
                        response = JSON.parse(resText);
                    } catch(e) {
                        console.error("Non-JSON:", resText);
                        throw new Error("Invalid server response");
                    }

                    if (response.success) {
                        Swal.fire({
                            title: 'Proposal Sent',
                            text: response.message,
                            icon: 'success',
                            confirmButtonColor: '#10b981'
                        }).then(() => {
                            // Optionally redirect or refresh
                            location.reload();
                        });
                    } else {
                        Swal.fire('Submission Error', response.message || 'Failed to send proposal.', 'error');
                    }
                } catch (e) {
                    console.error(e);
                    Swal.fire('System Error', 'Submission failed: ' + e.message, 'error');
                } finally {
                    sendProposalBtn.disabled = false;
                    sendProposalBtn.innerHTML = originalText;
                    if (window.lucide) window.lucide.createIcons();
                }
            }
        });
    }

    function prepareDraftData() {
        // Find the cycle name input by placeholder or ID
        const cycleNameInput = document.getElementById("cycleNameInput") || document.querySelector('input[placeholder*="Enter cycle name..."]');
        const cycleName = cycleNameInput ? cycleNameInput.value : "FY2025 Compensation Cycle";

        const employeeData = [];
        const detailedEmployeeData = [];
        
        document.querySelectorAll(".simulation-table tbody tr.sim-row").forEach(row => {
            const eeId = row.getAttribute("data-ee-id") || 0;
            const originalSal = parseFloat(row.getAttribute("data-original-base")) || 0;
            const structureAdjText = row.querySelector(".market-adjustment")?.innerText || "0";
            const structureAdjVal = parseFloat(structureAdjText.replace(/[^\d.-]/g, '')) || 0;
            const meritPct = parseFloat(row.querySelector(".prop-increase-input")?.value) || 0;
            const meritAmtText = row.querySelector(".prop-increase-amount")?.innerText || "0";
            const meritAmt = parseFloat(meritAmtText.replace(/[^\d.-]/g, '')) || 0;
            const newSalText = row.querySelector(".proposed-gross")?.innerText || "0";
            const newSal = parseFloat(newSalText.replace(/[^\d.-]/g, '')) || 0;
            const gradeId = row.getAttribute("data-grade-id") || 0;
            const compaText = row.querySelector(".compa-ratio")?.innerText || "0";
            const compa = parseFloat(compaText.replace(/[^\d.-]/g, '')) || 0;

            // Simple format for draft (now includes everything needed for the View Modal)
            employeeData.push({
                EmployeeID: eeId,
                FirstName: row.querySelector(".u-name-premium")?.innerText.split(' ')[0] || "",
                LastName: row.querySelector(".u-name-premium")?.innerText.split(' ').slice(1).join(' ') || "",
                EmployeeCode: row.querySelector(".u-code")?.innerText || "",
                OldSalary: originalSal,
                MarketAdjustment: structureAdjVal,
                MeritPct: meritPct,
                IncreaseAmount: meritAmt,
                NewSalary: newSal,
                GradeID: gradeId,
                CompaRatio: compa
            });

            // Detailed format for Finance (keep for backward compatibility or explicit Finance payload)
            detailedEmployeeData.push({
                ee_id: eeId,
                original_salary: originalSal,
                market_adjustment: structureAdjVal,
                merit_pct: meritPct,
                merit_amount: meritAmt,
                new_salary: newSal,
                grade_id: gradeId,
                compa_ratio: compa
            });
        });

        const salaryScaleData = [];
        document.querySelectorAll(".sim-grade-row").forEach(row => {
            const gradeId = row.getAttribute("data-grade-id");
            const min = parseFloat(row.querySelector(".sim-min-salary")?.value) || 0;
            const max = parseFloat(row.querySelector(".sim-max-salary")?.value) || 0;
            salaryScaleData.push({
                SalaryGradeID: gradeId,
                MinSalary: min,
                MaxSalary: max
            });
        });

        const totalCostStr = document.getElementById("totalMonthlyImpact")?.innerText || "0";
        const totalCost = parseFloat(totalCostStr.replace(/[^\d.-]/g, '')) || 0;
        const totalBudget = parseFloat(document.getElementById("budgetAllocation")?.value) || 5000000;
        const budgetUsedPct = totalBudget > 0 ? ((totalCost * 12) / totalBudget) * 100 : 0;

        return {
            cycle_name: cycleName,
            period_id: 1, // Default period for now
            budget_used: budgetUsedPct.toFixed(2),
            total_budget: totalBudget,
            total_cost: totalCost, // Monthly impact
            employee_data: employeeData,
            detailed_employee_data: detailedEmployeeData,
            salary_scale_data: salaryScaleData
        };
    }

    // 8. Allowance Saving Logic
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

            fetch('backend/save_allowance.php', {
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

    function updateTotalSimulationCost() {
        // Obsolete - consolidated into calculateDeductions
        calculateDeductions();
    }

    if (submitProposalBtn) {
        // Logic removed for Admin context
        console.log("Submission disabled for Admin");
    }

    // Manual Override Listener for absolute increase
    document.querySelectorAll(".prop-increase-amount").forEach(cell => {
        cell.addEventListener("blur", (e) => {
            const row = cell.closest("tr");
            const cleanVal = cell.innerText.replace(/[^\d.]/g, '');
            const absoluteInc = parseFloat(cleanVal) || 0;
            const currentPay = parseFloat(row.getAttribute("data-base-salary")) || 0;

            // Back-calculate percentage
            const pct = (absoluteInc / currentPay) * 100;
            const input = row.querySelector(".prop-increase-input");
            if (input) {
                input.value = pct.toFixed(1);
            }

            // Directly update proposed Basic to bypass Hard Stop Validation
            const proposedBasic = currentPay + absoluteInc;
            const proposedGrossCell = row.querySelector(".proposed-gross");
            if (proposedGrossCell) {
                proposedGrossCell.innerText = `\u20B1${proposedBasic.toLocaleString(undefined, { minimumFractionDigits: 0 })}`;
            }

            const tooltip = row.querySelector(".input-tooltip");
            if (tooltip) tooltip.classList.remove("visible");

            // Recalculate which will trigger the Orange text if maxPayVal is exceeded
            calculateDeductions();
        });

        cell.addEventListener("keydown", (e) => {
            if (e.key === "Enter") {
                e.preventDefault();
                cell.blur();
            }
        });
    });

    // Inline Promotion Action (Refined Toggle logic)
    document.addEventListener("click", (e) => {
        // Toggle Dropdown when clicking label
        if (e.target.closest(".promote-current-label")) {
            const cell = e.target.closest(".promote-cell");
            const label = cell.querySelector(".promote-current-label");
            const inline = cell.querySelector(".promote-inline");
            if (label && inline) {
                label.style.display = 'none';
                inline.style.display = 'flex';
            }
        }

        // Cancel Dropdown
        if (e.target.closest(".promote-cancel-btn")) {
            const cell = e.target.closest(".promote-cell");
            const label = cell.querySelector(".promote-current-label");
            const inline = cell.querySelector(".promote-inline");
            if (label && inline) {
                label.style.display = 'flex';
                inline.style.display = 'none';
            }
        }

        if (e.target.closest(".promote-cancel-btn")) {
            const row = e.target.closest("tr");
            row.querySelector('.promote-inline').style.display = 'none';
            row.querySelector('.promote-current-label').style.display = 'flex';
        }

        // Apply Promotion Logic
        if (e.target.closest(".promote-inline-btn")) {
            const row = e.target.closest("tr");
            const select = row.querySelector(".promote-grade-select");
            const newGradeId = select.value;

            const gradeData = window.compConfig.salaryGrades.find(g => g.SalaryGradeID == newGradeId);
            if (gradeData) {
                // Read-only values from structure
                row.setAttribute("data-grade-id", newGradeId);
                const minVal = parseFloat(gradeData.MinSalary) || 0;
                const midVal = parseFloat(gradeData.MidSalary) || 0;
                const maxVal = parseFloat(gradeData.MaxSalary) || 0;

                row.setAttribute("data-min-salary", minVal);
                row.setAttribute("data-midpoint", midVal);
                row.setAttribute("data-max-salary", maxVal);
                
                // Promotion Adjustment Rule: max(Grade Min, current salary * 1.05 to 1.10)
                // We standardise to at least 5% increase or the grade minimum.
                const originalBase = parseFloat(row.getAttribute("data-original-base")) || 0;
                const promotionIncreaseVal = originalBase * 1.05; // 5% minimum increase
                const targetBaseSalary = Math.max(minVal, promotionIncreaseVal);
                
                row.setAttribute("data-base-salary", targetBaseSalary);

                // Update UI Min/Mid/Max Cells
                row.querySelector(".grade-min").innerText = `\u20B1${minVal.toLocaleString()}`;
                row.querySelector(".grade-midpoint").innerText = `\u20B1${midVal.toLocaleString()}`;
                row.querySelector(".grade-max").innerText = `\u20B1${maxVal.toLocaleString()}`;

                // Update Label (handle "No Promotion" or Grade Level)
                const isNoPromotion = (newGradeId == row.querySelector(".promote-grade-select").options[0].value);
                const labelText = isNoPromotion ? "No Promotion" : `SG-${gradeData.GradeLevel.replace('SG-', '')}`;
                row.querySelector(".promote-current-label span").innerText = labelText;
                
                row.querySelector(".promote-current-label").style.display = 'flex';
                row.querySelector(".promote-inline").style.display = 'none';

                calculateDeductions();

                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: isNoPromotion ? 'Promotion Reverted' : 'Promotion Applied',
                    text: isNoPromotion ? 'Reverted to current grade standards.' : `Scale boundaries and Compa Ratio refreshed for SG-${gradeData.GradeLevel.replace('SG-', '')}.`,
                    showConfirmButton: false,
                    timer: 2000
                });
            }
        }
    });

    // Save Draft Logic (Refactored to named function)
    const saveDraftBtn = document.getElementById("saveDraftBtn");
    if (saveDraftBtn) {
        saveDraftBtn.addEventListener("click", () => saveSimulationDraft(false));
    }

    // Continue Draft Logic (Updated to use data-ee-id)
    document.querySelectorAll(".btn-continue-draft").forEach(btn => {
        btn.addEventListener("click", () => {
            const draftId = btn.getAttribute("data-draft-id");
            if (!draftId) return;

            btn.disabled = true;
            const originalText = btn.innerText;
            btn.innerText = "Loading...";

            fetch(`backend/load_draft.php?id=${draftId}`)
                .then(res => res.json())
                .then(data => {
                    btn.disabled = false;
                    btn.innerText = originalText;

                    if (data.success && data.data) {
                        const employeeData = data.data;

                        document.querySelectorAll(".simulation-table tbody tr.sim-row").forEach(row => {
                            const eeId = row.getAttribute("data-ee-id");

                            if (eeId) {
                                const savedData = employeeData.find(e => e.EmployeeID == eeId);
                                if (savedData) {
                                    const pctInput = row.querySelector(".prop-increase-input");
                                    const amtElement = row.querySelector(".prop-increase-amount");

                                    if (pctInput) {
                                        pctInput.value = savedData.PropPct;
                                        const changeEvent = new Event('input', { bubbles: true });
                                        pctInput.dispatchEvent(changeEvent);
                                    }

                                    if (amtElement && savedData.PropAmt > 0) {
                                        setTimeout(() => {
                                            amtElement.innerText = `\u20B1${savedData.PropAmt.toLocaleString(undefined, { minimumFractionDigits: 2 })}`;
                                            const blurEvent = new Event('blur', { bubbles: true });
                                            amtElement.dispatchEvent(blurEvent);
                                        }, 50);
                                    }
                                }
                            }
                        });

                        // bpm: Force a refresh of all calculations after data application
                        setTimeout(() => {
                            if (typeof calculateDeductions === 'function') {
                                calculateDeductions();
                            }
                        }, 100);

                        // Restore Salary Scale Data
                        if (data.salary_scale_data && Array.isArray(data.salary_scale_data)) {
                            data.salary_scale_data.forEach(saved => {
                                const row = document.querySelector(`.sim-grade-row[data-grade-id="${saved.SalaryGradeID}"]`);
                                if (row) {
                                    row.querySelector(".sim-min-salary").value = saved.MinSalary;
                                    row.querySelector(".sim-max-salary").value = saved.MaxSalary;
                                    const min = parseFloat(saved.MinSalary);
                                    const max = parseFloat(saved.MaxSalary);
                                    row.querySelector(".sim-mid-salary").value = ((min + max) / 2).toFixed(0);
                                    const spread = min > 0 ? ((max - min) / min) * 100 : 0;
                                    row.querySelector(".sim-spread-cell").innerText = `${spread.toFixed(1)}%`;
                                    
                                    // Update internal config
                                    updateGradeInConfig(saved.SalaryGradeID, min, max);
                                }
                            });
                            // Refresh simulation table for all grades after scale update
                            if (typeof refreshSimulationTableForGrade === 'function') {
                                // Just trigger one or clear and rebuild if needed, 
                                // but calculateDeductions usually handles most of it if IDs match.
                            }
                        }

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Draft Loaded Successfully',
                            text: `Restored ${data.cycle_name}.`,
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true
                        }).then(() => {
                            switchTab('simulation');
                            // One final calc after tab switch to ensure everything is visible
                            setTimeout(() => {
                                if (typeof calculateDeductions === 'function') calculateDeductions();
                            }, 300);
                        });

                    } else {
                        Swal.fire('Error', data.message || 'Failed to load draft.', 'error');
                    }
                })
                .catch(err => {
                    btn.disabled = false;
                    btn.innerText = originalText;
                    console.error('Error loading draft:', err);
                    Swal.fire('Error', 'Network error occurred. Could not load draft.', 'error');
                });
        });
    });

    // Initial calculation (only run if a draft is being loaded or if specifically needed)
    // calculateDeductions(); 
    if (window.lucide) window.lucide.createIcons();

});

// Sidebar Active Link Logic (Merged)
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

// User Menu Dropdown Logic (Merged)
document.addEventListener('DOMContentLoaded', () => {
    const nameEl = document.querySelector('.sidebar-footer .user-name');
    const roleEl = document.querySelector('.sidebar-footer .user-role');
    const umdName = document.getElementById('umdName');
    const umdRole = document.getElementById('umdRole');
    const umdAvatar = document.getElementById('umdAvatar');
    const sidebarAvatar = document.getElementById('sidebarAvatar');

    if (nameEl) {
        const name = nameEl.textContent.trim();
        const initials = name.split(' ')
            .map(n => n[0])
            .join('')
            .toUpperCase()
            .substring(0, 2);

        if (umdName) umdName.textContent = name;
        if (umdAvatar) umdAvatar.textContent = initials;
        if (sidebarAvatar) sidebarAvatar.textContent = initials;
    }
    if (roleEl && umdRole) umdRole.textContent = roleEl.textContent.trim();

    const btn = document.getElementById('userMenuBtn');
    const dd = document.getElementById('userMenuDropdown');
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
                confirmButtonText: '<i class="swal-icon-logout"></i> Yes, Sign Out',
                cancelButtonText: 'Stay',
                reverseButtons: true,
                customClass: {
                    popup: 'swal-signout-popup',
                    title: 'swal-signout-title',
                }
            });
            if (result.isConfirmed) {
                await Swal.fire({
                    icon: 'success',
                    title: 'Signed Out',
                    text: 'You have been signed out successfully.',
                    timer: 1500,
                    showConfirmButton: false,
                });
                window.location.href = dest;
            }
        });
    });
});





// Real-time Clock Functionality
function initClock() {
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

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initClock);
} else {
    initClock();
}

// Notifications Logic (Merged)
document.addEventListener('DOMContentLoaded', () => {
    const bellBtn = document.getElementById('bellIconBtn');
    const notifDropdown = document.getElementById('notifDropdown');
    const notifBadge = document.getElementById('notifBadge');
    const notifList = document.getElementById('notifList');
    const markReadBtn = document.getElementById('markReadBtn');

    if (!bellBtn || !notifDropdown) return;

    bellBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        const isVisible = notifDropdown.style.display === 'block';
        notifDropdown.style.display = isVisible ? 'none' : 'block';
    });

    document.addEventListener('click', (e) => {
        if (!notifDropdown.contains(e.target) && e.target !== bellBtn) {
            notifDropdown.style.display = 'none';
        }
    });

    const loadNotifications = async () => {
        try {
            const res = await fetch('backend/be_notifications.php?action=fetch&module_target=compensation_cycle');
            const data = await res.json();
            if (data.success) {
                // Update badge
                if (data.unread_count > 0) {
                    notifBadge.style.display = 'inline-block';
                    notifBadge.textContent = data.unread_count;
                } else {
                    notifBadge.style.display = 'none';
                }

                // Update list
                if (data.notifications.length === 0) {
                    notifList.innerHTML = '<div style="padding: 16px; text-align: center; color: var(--text-secondary, #6b7280); font-size: 14px;">No notifications yet.</div>';
                } else {
                    notifList.innerHTML = data.notifications.map(n => {
                        const date = new Date(n.created_at).toLocaleString('en-PH', { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
                        const bg = parseInt(n.is_read) === 1 ? 'transparent' : 'rgba(44, 160, 120, 0.05)';
                        const border = parseInt(n.is_read) === 1 ? 'none' : '3px solid #2ca078';
                        return `
                            <div style="padding: 12px 16px; border-bottom: 1px solid var(--border-color, #e5e7eb); background: ${bg}; border-left: ${border};">
                                <div style="font-size: 13px; color: var(--text-primary, #111827); line-height: 1.4;">${n.message}</div>
                                <div style="font-size: 11px; color: var(--text-secondary, #6b7280); margin-top: 4px;">${date}</div>
                            </div>
                        `;
                    }).join('');
                }
            }
        } catch (e) {
            console.error('Failed to load notifications:', e);
        }
    };

    if (markReadBtn) {
        markReadBtn.addEventListener('click', async (e) => {
            e.stopPropagation();
            try {
                const res = await fetch('backend/be_notifications.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'action=mark_read&module_target=compensation_cycle'
                });
                const data = await res.json();
                if (data.success) {
                    loadNotifications();
                }
            } catch (e) {
                console.error('Failed to mark as read:', e);
            }
        });
    }

    // Load initial and poll every 10 seconds for faster updates during testing
    loadNotifications();
    setInterval(loadNotifications, 10000);

    // Simulation Real-time Listeners
    document.querySelectorAll(".prop-increase-input").forEach(input => {
        input.addEventListener("input", calculateDeductions);
    });

    // Multi-select/other triggers if they affect calculation
    const deptFilter = document.getElementById("deptFilter");
    if (deptFilter) {
        deptFilter.addEventListener("change", calculateDeductions);
    }

    // Initial Trigger for calculations and band validation
    calculateDeductions();
});

