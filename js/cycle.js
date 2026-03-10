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

                // BPM Enforcement: Prevent manual opening of Simulation Tab
                if (targetTab === "simulation" && !window.simulationUnlocked) {
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
    }

    // 5. Start Simulation Cycle Button
    const startCycleBtn = document.getElementById("startCycleBtn");
    if (startCycleBtn) {
        startCycleBtn.addEventListener("click", () => {
            // Unlock simulation tab and switch to it
            switchTab('simulation');

            // Optional: Smooth scroll or feedback
            if (window.Swal) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'info',
                    title: 'Simulation cycle started. Tab unlocked.',
                    showConfirmButton: false,
                    timer: 2000
                });
            }
        });
    }

    function switchTab(tabId) {
        if (tabId === 'simulation') {
            window.simulationUnlocked = true;
            const simBtn = document.querySelector('.tab-btn[data-tab="simulation"]');
            if (simBtn) simBtn.classList.remove('locked-tab');
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

    // 5. Simulation & Proposal Logic
    const tableInputs = document.querySelectorAll(".table-input");
    const submitProposalBtn = document.getElementById("submitProposalBtn");

    const parseCurrency = (el) => {
        if (!el) return 0;
        const val = parseFloat(el.innerText.replace(/[^0-9.-]/g, ""));
        return isNaN(val) ? 0 : val;
    };

    function calculateDeductions() {
        // Build map from Allowance Tab
        const gradeTaxableMap = {};
        document.querySelectorAll(".allowance-val-input").forEach(input => {
            const gid = input.getAttribute("data-grade");
            const tax = input.getAttribute("data-is-taxable") === "1";
            const amt = parseFloat(input.value) || 0;
            if (!gradeTaxableMap[gid]) gradeTaxableMap[gid] = 0;
            if (tax) gradeTaxableMap[gid] += amt;
        });

        document.querySelectorAll(".simulation-table tbody tr").forEach(row => {
            const gradeID = row.getAttribute("data-grade-id");
            const currentPayVal = parseFloat(row.getAttribute("data-base-salary")) || 0;
            const midPayVal = parseFloat(row.getAttribute("data-midpoint")) || 0;
            const maxPayVal = parseFloat(row.getAttribute("data-max-salary")) || 0;

            const proposedBasic = parseCurrency(row.querySelector(".proposed-gross")) || currentPayVal;
            const totalAllowances = parseCurrency(row.querySelector(".total-allowances"));
            const taxableAllowances = gradeTaxableMap[gradeID] || 0;

            const totalGross = proposedBasic + totalAllowances;

            // 1. SSS Calculation (2026 Logic)
            const sssMSCCap = 35000;
            const sssRateEE = 0.05;
            const sssRateER = 0.10;

            const regularBase = Math.min(proposedBasic, sssMSCCap);
            let sssEE = regularBase * sssRateEE;
            let sssER = regularBase * sssRateER;

            // 2. SSS WISP Calculation
            let wispEE = 0;
            if (proposedBasic >= sssMSCCap) {
                wispEE = 900;
            } else if (proposedBasic > 20000) {
                wispEE = Math.max(0, proposedBasic - 20000) * 0.02;
            }
            let wispER = wispEE;

            // 3. PhilHealth Calculation
            const phRateTotal = 0.05;
            const phCapEE = 2500;
            const phEE = Math.min((proposedBasic * phRateTotal) / 2, phCapEE);
            const phER = phEE;

            // 4. Pag-IBIG Calculation
            const piRateEE = 0.02;
            const piCap = 200;
            const piEE = Math.min(proposedBasic * piRateEE, piCap);
            const piER = piEE;

            const totalEERead = sssEE + wispEE + phEE + piEE;
            const employerShare = sssER + wispER + phER + piER;
            const fullLoad = totalGross + employerShare;

            // 5. Withholding Tax (TRAIN Law 2026 Monthly)
            let taxable = (proposedBasic + taxableAllowances) - totalEERead;
            let tax = 0;

            if (taxable > 666667) {
                tax = 183541.67 + (taxable - 666667) * 0.35;
            } else if (taxable > 166667) {
                tax = 33541.67 + (taxable - 166667) * 0.30;
            } else if (taxable > 66667) {
                tax = 8541.80 + (taxable - 66667) * 0.25;
            } else if (taxable > 33333) {
                tax = 1875 + (taxable - 33333) * 0.20;
            } else if (taxable > 20833) {
                tax = (taxable - 20833) * 0.15;
            }

            const compaRatio = midPayVal > 0 ? (proposedBasic / midPayVal) * 100 : 0;
            const netIncrease = proposedBasic - currentPayVal;
            const netPay = totalGross - totalEERead - tax;

            // 6. Pay Rates based on BASIC Salary (User Rule)
            const daily = (proposedBasic > 0) ? proposedBasic / 22 : 0;
            const hourly = (daily > 0) ? daily / 8 : 0;
            const semi = totalGross / 2;

            // Update row UI
            const totalGrossCell = row.querySelector(".total-gross");
            const compaRatioCell = row.querySelector(".compa-ratio");
            const propIncAmtCell = row.querySelector(".prop-increase-amount");

            if (totalGrossCell) totalGrossCell.innerText = `\u20B1${totalGross.toLocaleString(undefined, { minimumFractionDigits: 2 })}`;

            if (compaRatioCell) {
                const isMax = compaRatio >= 100; // Simplified for "Max" logic
                compaRatioCell.innerText = isMax ? "MAX" : `${compaRatio.toFixed(0)}%`;
                compaRatioCell.style.color = "";
                compaRatioCell.style.fontWeight = "600";

                if (isMax) {
                    compaRatioCell.style.color = "#ef4444";
                    compaRatioCell.classList.add("at-max");
                } else if (compaRatio < 90) {
                    compaRatioCell.style.color = "#eab308"; // Yellow
                }
            }

            if (propIncAmtCell) {
                const incValText = `+\u20B1${netIncrease.toLocaleString(undefined, { minimumFractionDigits: 2 })}`;
                propIncAmtCell.innerText = incValText;

                // Zero Rule: neutral gray if 0
                if (netIncrease === 0) {
                    propIncAmtCell.classList.add('text-neutral-gray');
                } else {
                    propIncAmtCell.classList.remove('text-neutral-gray');
                }
            }

            if (row.querySelector(".employer-share")) row.querySelector(".employer-share").innerText = `\u20B1${employerShare.toLocaleString(undefined, { minimumFractionDigits: 2 })}`;
            if (row.querySelector(".full-load")) row.querySelector(".full-load").innerText = `\u20B1${fullLoad.toLocaleString(undefined, { minimumFractionDigits: 2 })}`;

            if (row.querySelector(".rate-semi")) row.querySelector(".rate-semi").innerText = `\u20B1${semi.toLocaleString(undefined, { minimumFractionDigits: 2 })}`;
            if (row.querySelector(".rate-daily")) row.querySelector(".rate-daily").innerText = `\u20B1${daily.toLocaleString(undefined, { minimumFractionDigits: 2 })}`;
            if (row.querySelector(".rate-hourly")) row.querySelector(".rate-hourly").innerText = `\u20B1${hourly.toLocaleString(undefined, { minimumFractionDigits: 2 })}`;

            // Calculate current ER share for comparison
            const currentER = calculateEROnly(currentPayVal);
            const erIncrease = employerShare - currentER;
            row.setAttribute('data-er-increase', erIncrease);

            if (row.querySelector(".deduction-sss")) row.querySelector(".deduction-sss").innerText = `\u20B1${sssEE.toLocaleString(undefined, { minimumFractionDigits: 2 })}`;
            if (row.querySelector(".deduction-wisp")) row.querySelector(".deduction-wisp").innerText = `\u20B1${wispEE.toLocaleString(undefined, { minimumFractionDigits: 2 })}`;
            if (row.querySelector(".deduction-ph")) row.querySelector(".deduction-ph").innerText = `\u20B1${phEE.toLocaleString(undefined, { minimumFractionDigits: 2 })}`;
            if (row.querySelector(".deduction-pi")) row.querySelector(".deduction-pi").innerText = `\u20B1${piEE.toLocaleString(undefined, { minimumFractionDigits: 2 })}`;
            if (row.querySelector(".deduction-tax")) row.querySelector(".deduction-tax").innerText = `\u20B1${tax.toLocaleString(undefined, { minimumFractionDigits: 2 })}`;
            if (row.querySelector(".net-pay-cell")) row.querySelector(".net-pay-cell").innerText = `\u20B1${netPay.toLocaleString(undefined, { minimumFractionDigits: 2 })}`;

            const proposedGrossCell = row.querySelector(".proposed-gross");
            if (proposedGrossCell) {
                proposedGrossCell.innerText = `\u20B1${proposedBasic.toLocaleString(undefined, { minimumFractionDigits: 2 })}`;

                // Manual Override Visual Feedback (Orange text if exceeding grade)
                if (maxPayVal > 0 && proposedBasic > maxPayVal) {
                    proposedGrossCell.style.color = "#f97316"; // Orange
                } else {
                    proposedGrossCell.style.color = "";
                }
            }

            const increaseCell = row.querySelector(".increase-cell");
            if (increaseCell) {
                increaseCell.innerText = `+\u20B1${netIncrease.toLocaleString(undefined, { minimumFractionDigits: 0 })}`;
                if (netIncrease === 0) increaseCell.classList.add('text-neutral-gray');
                else increaseCell.classList.remove('text-neutral-gray');
            }
        });

        updateTotalSimulationCost();
    }

    function calculateEROnly(basic) {
        if (!window.compConfig) return 0;
        let sssER = 0; let wispER = 0;
        const sssTable = window.compConfig.sssTable || [];
        const sssMatch = sssTable.find(r => basic >= r.min_salary && basic <= r.max_salary);
        if (sssMatch) {
            sssER = parseFloat(sssMatch.er_regular);
            wispER = parseFloat(sssMatch.er_wisp);
        }

        const phER = (basic * 0.05) / 2;
        const piER = basic > 1500 ? 100 : basic * 0.02;

        return sssER + wispER + phER + piER;
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
            updateTotalSimulationCost();
        });
    }

    function runAutoSimulation() {
        document.querySelectorAll(".simulation-table tbody tr.sim-row").forEach(row => {
            const recommendedPct = parseFloat(row.getAttribute("data-recommended-pct")) || 0;
            const inputEl = row.querySelector(".prop-increase-input");
            if (inputEl) {
                // Only apply if the input is not disabled (at salary ceiling)
                if (!inputEl.disabled) {
                    inputEl.value = recommendedPct.toFixed(1);
                }
                const evt = new Event('input', { bubbles: true });
                inputEl.dispatchEvent(evt);
            }
        });
    }


    // Auto-run on page load once listeners are bound
    setTimeout(() => {
        runAutoSimulation();
    }, 500);

    if (tableInputs.length > 0) {
        tableInputs.forEach(input => {
            input.addEventListener("input", (e) => {
                const row = e.target.closest("tr");
                if (!row) return;

                const currentPay = parseFloat(row.getAttribute("data-base-salary")) || 0;
                const maxPayVal = parseFloat(row.getAttribute("data-max-salary")) || 0;

                let percentage = parseFloat(e.target.value) || 0;
                if (percentage < 0) percentage = 0;

                let proposedBasic = currentPay + (currentPay * (percentage / 100));
                const tooltip = row.querySelector(".input-tooltip");
                const promoteBtn = row.querySelector(".btn-promote-trigger");

                // Hard Stop Validation & Contextual Promotion
                if (maxPayVal > 0 && proposedBasic >= maxPayVal) {
                    proposedBasic = maxPayVal;
                    percentage = ((maxPayVal - currentPay) / currentPay) * 100;
                    e.target.value = percentage.toFixed(1);
                    e.target.disabled = true; // Disable input at max
                    if (tooltip) tooltip.classList.add("visible");

                    if (promoteBtn) {
                        promoteBtn.style.display = 'flex';
                        const currentLvl = parseInt(row.getAttribute("data-grade-id") || 0);
                        promoteBtn.setAttribute("data-next-suggestion", currentLvl + 1);
                    }
                } else {
                    e.target.disabled = false;
                    if (tooltip) tooltip.classList.remove("visible");
                    if (promoteBtn) promoteBtn.style.display = 'none';
                }

                const netIncrease = proposedBasic - currentPay;

                // Update UI
                const proposedGrossCell = row.querySelector(".proposed-gross");
                const increaseCell = row.querySelector(".increase-cell");
                const propIncAmtCell = row.querySelector(".prop-increase-amount");

                if (proposedGrossCell) proposedGrossCell.innerText = `\u20B1${proposedBasic.toLocaleString(undefined, { minimumFractionDigits: 0 })}`;
                if (increaseCell) increaseCell.innerText = `+\u20B1${netIncrease.toLocaleString(undefined, { minimumFractionDigits: 0 })}`;
                if (propIncAmtCell) propIncAmtCell.innerText = `+\u20B1${netIncrease.toLocaleString(undefined, { minimumFractionDigits: 2 })}`;

                calculateDeductions();
            });
        });
    }

    // 6. Allowance Saving Logic
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

    function updateTotalSimulationCost() {
        let totalIncrease = 0;
        let totalERIncrease = 0;
        let totalCompaRatio = 0;
        let count = 0;

        document.querySelectorAll(".simulation-table tbody tr.sim-row").forEach(row => {
            if (row.style.display === "none") return;

            const incVal = parseCurrency(row.querySelector(".prop-increase-amount")) || 0;
            totalIncrease += incVal;

            const erInc = parseFloat(row.getAttribute('data-er-increase')) || 0;
            totalERIncrease += erInc;

            const ratioText = row.querySelector(".compa-ratio")?.innerText || "0%";
            const ratioVal = parseFloat(ratioText.replace(/[^0-9.]/g, '')) || 0;
            totalCompaRatio += ratioVal;
            count++;
        });

        const monthlyImpact = totalIncrease + totalERIncrease;
        const yearlyImpact = monthlyImpact * 12;
        const avgRatio = count > 0 ? totalCompaRatio / count : 0;

        const staffCountEl = document.getElementById("simStaffCount");
        if (staffCountEl) staffCountEl.innerText = `${count} Active Employees`;

        const impactEl = document.getElementById("totalMonthlyImpact");
        if (impactEl) impactEl.innerText = `+\u20B1${monthlyImpact.toLocaleString(undefined, { minimumFractionDigits: 2 })}`;

        const yearlyEl = document.getElementById("totalYearlyImpact");
        if (yearlyEl) yearlyEl.innerText = `\u20B1${yearlyImpact.toLocaleString(undefined, { minimumFractionDigits: 2 })}`;

        const ratioEl = document.getElementById("avgCompaRatio");
        if (ratioEl) ratioEl.innerText = `${avgRatio.toFixed(0)}%`;
    }

    if (submitProposalBtn) {
        submitProposalBtn.addEventListener("click", () => {
            const totalCostStr = document.getElementById("totalMonthlyImpact")?.innerText || "0";
            const totalCost = parseFloat(totalCostStr.replace(/[^\d.-]/g, '')) || 0;
            const budget = document.getElementById("budgetAllocation")?.value || 0;
            const cycleNameInput = document.querySelector('input[placeholder*="Enter cycle name..."]');
            const cycleName = cycleNameInput ? cycleNameInput.value : "FY2025 Cycle";

            if (window.Swal) {
                Swal.fire({
                    title: 'Submit Compensation Proposal?',
                    text: `Total estimated increase cost is \u20B1${totalCost.toLocaleString()}. This will be sent to the HR Manager for final review.`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#2ca078',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, Submit Proposal',
                    cancelButtonText: 'Review Further'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Gather data for submission
                        const employeeData = [];
                        document.querySelectorAll(".simulation-table tbody tr.sim-row").forEach(row => {
                            const eeId = row.getAttribute("data-ee-id") || row.cells[0]?.innerText.trim() || 0;
                            const propPctInput = row.querySelector(".prop-increase-input") || row.querySelector("input[type='number']");
                            const propPct = propPctInput?.value || "0";
                            const propAmtText = row.querySelector(".prop-increase-amount")?.innerText || "0";
                            const propAmt = parseFloat(propAmtText.replace(/[^\d.-]/g, '')) || 0;
                            const newGrade = row.getAttribute("data-grade-id") || 0;
                            const name = row.querySelector(".u-name-premium")?.innerText || row.querySelector(".user-info span")?.innerText || "Unknown Employee";
                            const dept = row.getAttribute("data-department") || "Unassigned";
                            const currentSal = parseFloat(row.getAttribute("data-base-salary")) || 0;
                            const newSalText = row.querySelector(".proposed-gross")?.innerText || "0";
                            const newSal = parseFloat(newSalText.replace(/[^\d.-]/g, '')) || 0;
                            const gradeLabel = row.querySelector(".promote-current-label span")?.innerText || "";
                            const promoGrade = row.querySelector(".promote-inline .promote-grade-select option:checked")?.text.split(" \u2013 ")[0] || "";

                            employeeData.push({
                                EmployeeID: eeId,
                                name: name,
                                department: dept,
                                grade: gradeLabel,
                                current_salary: currentSal,
                                prop_pct: parseFloat(propPct),
                                prop_inc: propAmt,
                                new_salary: newSal,
                                promotion_grade: (newGrade != row.getAttribute("data-grade-id")) ? promoGrade : "",
                                GradeID: newGrade
                            });
                        });

                        console.log("Submitting Simulation Data:", employeeData);

                        const params = new URLSearchParams();
                        params.append('cycle_name', cycleName);
                        params.append('total_cost', totalCost);
                        params.append('employee_data', JSON.stringify(employeeData));

                        fetch('be_submit_simulation.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: params
                        })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        toast: true,
                                        position: 'top-end',
                                        icon: 'success',
                                        title: 'Proposal Submitted Successfully!',
                                        text: 'The compensation structure has been sent to the HR Manager.',
                                        showConfirmButton: false,
                                        timer: 2000,
                                        timerProgressBar: true
                                    }).then(() => location.reload());
                                } else {
                                    Swal.fire('Error', data.message || 'Failed to submit proposal.', 'error');
                                }
                            })
                            .catch(err => {
                                console.error(err);
                                Swal.fire('Error', 'Network error occurred.', 'error');
                            });
                    }
                });
            }
        });
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

        // Approve Promotion
        if (e.target.closest(".promote-inline-btn")) {
            const btn = e.target.closest(".promote-inline-btn");
            const row = btn.closest("tr");
            const select = row.querySelector(".promote-grade-select");
            const newGradeId = select.value;

            if (!window.compConfig || !window.compConfig.salaryGrades) return;

            const gradeData = window.compConfig.salaryGrades.find(g => g.SalaryGradeID == newGradeId);
            if (gradeData) {
                // Update row attributes
                row.setAttribute("data-grade-id", newGradeId);
                row.setAttribute("data-midpoint", gradeData.MidSalary);
                row.setAttribute("data-max-salary", gradeData.MaxSalary);
                // Update Base Salary to New Grade Min (User Request)
                row.setAttribute("data-base-salary", gradeData.MinSalary);

                // Update UI Salary Cell
                const salaryCell = row.querySelector(".current-pay");
                if (salaryCell) {
                    salaryCell.innerText = `\u20B1${parseFloat(gradeData.MinSalary).toLocaleString(undefined, { minimumFractionDigits: 0 })}`;
                }

                // Update UI Midpoint
                const midCell = row.querySelector(".grade-midpoint");
                if (midCell) midCell.innerText = `\u20B1${parseFloat(gradeData.MidSalary).toLocaleString(undefined, { minimumFractionDigits: 0 })}`;

                // Update Label and hide dropdown
                const cell = row.querySelector(".promote-cell");
                const labelSpan = cell.querySelector(".promote-current-label span");
                const label = cell.querySelector(".promote-current-label");
                const inline = cell.querySelector(".promote-inline");
                if (labelSpan) labelSpan.innerText = `SG-${gradeData.GradeLevel}`;
                if (label) label.style.display = 'flex';
                if (inline) inline.style.display = 'none';

                // Re-enable input and reset merit increase to 0.0
                const inputBtn = row.querySelector(".prop-increase-input");
                if (inputBtn) {
                    inputBtn.disabled = false;
                    inputBtn.value = "0.0";
                    // Manually trigger updates to ensure everything refreshes immediately
                    const evt = new Event('input', { bubbles: true });
                    inputBtn.dispatchEvent(evt);
                    // Force a full recalculation just to be 100% sure the OK worked
                    if (typeof calculateDeductions === 'function') calculateDeductions();
                }

                if (window.Swal) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Draft Promotion Applied',
                        text: `${row.querySelector('.u-name-premium') ? row.querySelector('.u-name-premium').innerText : 'Employee'} moved to ${gradeData.GradeLevel}. Salary adjusted to \u20B1${parseFloat(gradeData.MinSalary).toLocaleString()}`,
                        showConfirmButton: false,
                        timer: 3000
                    });
                }
            }
        }
    });

    // Automatic Promotion on Grade Change (User Request)
    document.addEventListener("change", (e) => {
        if (e.target.closest(".promote-grade-select")) {
            const select = e.target.closest(".promote-grade-select");
            const btn = select.parentNode.querySelector(".promote-inline-btn");
            if (btn) btn.click();
        }
    });

    // Save Draft Logic (Updated to use data-ee-id)
    const saveDraftBtn = document.getElementById("saveDraftBtn");
    if (saveDraftBtn) {
        saveDraftBtn.addEventListener("click", () => {
            saveDraftBtn.disabled = true;
            saveDraftBtn.innerHTML = '<i data-lucide="loader" class="animate-spin"></i><span>Saving...</span>';
            if (window.lucide) window.lucide.createIcons();

            const cycleNameInput = document.querySelector('input[placeholder*="Enter cycle name..."]');
            const cycleName = cycleNameInput ? cycleNameInput.value : "FY2025 Cycle";

            // Collect Row Data
            const employeeData = [];
            document.querySelectorAll(".simulation-table tbody tr.sim-row").forEach(row => {
                const eeId = row.getAttribute("data-ee-id") || 0;
                const propPct = row.querySelector(".prop-increase-input")?.value || "0";
                const propAmtText = row.querySelector(".prop-increase-amount")?.innerText || "0";
                const propAmt = parseFloat(propAmtText.replace(/[^\d.-]/g, '')) || 0;
                const newGrade = row.getAttribute("data-grade-id") || 0;

                employeeData.push({
                    EmployeeID: eeId,
                    PropPct: parseFloat(propPct),
                    PropAmt: propAmt,
                    GradeID: newGrade
                });
            });

            const totalCostStr = document.getElementById("totalMonthlyImpact")?.innerText || "0";
            const totalCost = parseFloat(totalCostStr.replace(/[^\d.-]/g, '')) || 0;
            const totalBudgetStr = document.getElementById("budgetAllocation")?.value || "5000000";
            const totalBudget = parseFloat(totalBudgetStr.replace(/[^\d.-]/g, '')) || 5000000;
            const budgetUsedPct = totalBudget > 0 ? (totalCost / totalBudget) * 100 : 0;

            const params = new URLSearchParams();
            params.append('cycle_name', cycleName);
            params.append('period_id', 1);
            params.append('budget_used', budgetUsedPct.toFixed(2));
            params.append('total_budget', totalBudget);
            params.append('total_cost', totalCost);
            params.append('employee_data', JSON.stringify(employeeData));

            fetch('save_draft.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: params
            })
                .then(res => res.json())
                .then(data => {
                    saveDraftBtn.innerHTML = '<i data-lucide="save"></i><span>Save Draft</span>';
                    saveDraftBtn.disabled = false;
                    if (window.lucide) window.lucide.createIcons();

                    if (data.success) {
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
                        Swal.fire('Error', data.message || 'Failed to save draft.', 'error');
                    }
                })
                .catch(err => {
                    saveDraftBtn.innerHTML = '<i data-lucide="save"></i><span>Save Draft</span>';
                    saveDraftBtn.disabled = false;
                    if (window.lucide) window.lucide.createIcons();
                    console.error(err);
                    Swal.fire('Error', 'Network error occurred while saving draft.', 'error');
                });
        });
    }

    // Continue Draft Logic (Updated to use data-ee-id)
    document.querySelectorAll(".btn-continue-draft").forEach(btn => {
        btn.addEventListener("click", () => {
            const draftId = btn.getAttribute("data-draft-id");
            if (!draftId) return;

            btn.disabled = true;
            const originalText = btn.innerText;
            btn.innerText = "Loading...";

            fetch(`load_draft.php?id=${draftId}`)
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

    // Initial calculation
    calculateDeductions();
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
            const res = await fetch('be_notifications.php?action=fetch&module_target=compensation_cycle');
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
                const res = await fetch('be_notifications.php', {
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
});

