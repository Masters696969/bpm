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
            btn.addEventListener("click", () => {
                const targetTab = btn.getAttribute("data-tab");
                if (!targetTab) return;
                switchTab(targetTab);
            });
        });
    }

    function switchTab(tabId) {
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

    // Start Cycle Logic
    const startCycleBtn = document.getElementById("startCycleBtn");
    if (startCycleBtn) {
        startCycleBtn.addEventListener("click", () => {
            const cycleNameInput = document.querySelector('input[value*="FY2025"]');
            const cycleName = cycleNameInput ? cycleNameInput.value : "FY2025";
            if (!cycleName) {
                if (window.Swal) Swal.fire('Error', 'Please enter a cycle name.', 'error');
                else alert('Please enter a cycle name.');
                return;
            }
            switchTab('salary');
        });
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
            const currentPayVal = parseCurrency(row.querySelector(".current-pay"));
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

            const netPay = totalGross - totalEERead - tax;

            // 6. Pay Rates based on BASIC Salary (User Rule)
            const daily = (proposedBasic > 0) ? proposedBasic / 22 : 0;
            const hourly = (daily > 0) ? daily / 8 : 0;
            const semi = totalGross / 2;

            // Update row UI
            const totalGrossCell = row.querySelector(".total-gross");
            if (totalGrossCell) totalGrossCell.innerText = `\u20B1${totalGross.toLocaleString(undefined, { minimumFractionDigits: 2 })}`;
            if (row.querySelector(".employer-share")) row.querySelector(".employer-share").innerText = `\u20B1${employerShare.toLocaleString(undefined, { minimumFractionDigits: 2 })}`;
            if (row.querySelector(".full-load")) row.querySelector(".full-load").innerText = `\u20B1${fullLoad.toLocaleString(undefined, { minimumFractionDigits: 2 })}`;

            if (row.querySelector(".rate-semi")) row.querySelector(".rate-semi").innerText = `\u20B1${semi.toLocaleString(undefined, { minimumFractionDigits: 2 })}`;
            if (row.querySelector(".rate-daily")) row.querySelector(".rate-daily").innerText = `\u20B1${daily.toLocaleString(undefined, { minimumFractionDigits: 2 })}`;
            if (row.querySelector(".rate-hourly")) row.querySelector(".rate-hourly").innerText = `\u20B1${hourly.toLocaleString(undefined, { minimumFractionDigits: 2 })}`;

            if (row.querySelector(".deduction-sss")) row.querySelector(".deduction-sss").innerText = `\u20B1${sssEE.toLocaleString(undefined, { minimumFractionDigits: 2 })}`;
            if (row.querySelector(".deduction-wisp")) row.querySelector(".deduction-wisp").innerText = `\u20B1${wispEE.toLocaleString(undefined, { minimumFractionDigits: 2 })}`;
            if (row.querySelector(".deduction-ph")) row.querySelector(".deduction-ph").innerText = `\u20B1${phEE.toLocaleString(undefined, { minimumFractionDigits: 2 })}`;
            if (row.querySelector(".deduction-pi")) row.querySelector(".deduction-pi").innerText = `\u20B1${piEE.toLocaleString(undefined, { minimumFractionDigits: 2 })}`;
            if (row.querySelector(".deduction-tax")) row.querySelector(".deduction-tax").innerText = `\u20B1${tax.toLocaleString(undefined, { minimumFractionDigits: 2 })}`;
            if (row.querySelector(".net-pay-cell")) row.querySelector(".net-pay-cell").innerText = `\u20B1${netPay.toLocaleString(undefined, { minimumFractionDigits: 2 })}`;
        });

        updateTotalSimulationCost();
    }

    if (tableInputs.length > 0) {
        tableInputs.forEach(input => {
            input.addEventListener("input", (e) => {
                const row = e.target.closest("tr");
                if (!row) return;

                const currentPay = parseCurrency(row.querySelector(".current-pay"));

                // Enforce 5% Cap
                let percentage = parseFloat(e.target.value) || 0;
                if (percentage > 5.0) {
                    percentage = 5.0;
                    e.target.value = 5.0;
                }

                const netIncrease = (currentPay * percentage) / 100;
                const newGross = currentPay + netIncrease;

                // Update UI
                const proposedGrossCell = row.querySelector(".proposed-gross");
                const increaseCell = row.querySelector(".increase-cell");

                if (proposedGrossCell) proposedGrossCell.innerText = `\u20B1${newGross.toLocaleString(undefined, { minimumFractionDigits: 0 })}`;
                if (increaseCell) increaseCell.innerText = `+\u20B1${netIncrease.toLocaleString(undefined, { minimumFractionDigits: 0 })}`;

                calculateDeductions();
            });
        });
    }

    // Salary Grade Spread Calculation Logic
    function initScaleCalculations(row) {
        const minInput = row.querySelector(".min-salary-input");
        const maxInput = row.querySelector(".max-salary-input");
        const midInput = row.querySelector(".mid-salary-input");
        const spreadCell = row.querySelector(".spread-cell");

        [minInput, maxInput].forEach(input => {
            if (!input) return;
            input.addEventListener("input", () => {
                const min = parseFloat(minInput.value) || 0;
                const max = parseFloat(maxInput.value) || 0;

                // Update Midpoint
                if (midInput && min >= 0 && max >= 0) {
                    midInput.value = Math.round((min + max) / 2);
                }

                // Update Spread
                if (spreadCell && min > 0) {
                    const spread = ((max - min) / min) * 100;
                    spreadCell.innerText = `${spread.toFixed(1)}%`;
                }
            });
        });
    }

    // Initialize existing rows
    document.querySelectorAll("#salaryGradeTable tbody tr").forEach(row => initScaleCalculations(row));

    // Propose Change Modal Logic
    const btnProposeChange = document.getElementById("btnProposeChange");
    const proposeChangeModal = document.getElementById("proposeChangeModal");
    const closeProposeModalBtn = document.getElementById("closeProposeModalBtn");
    const cancelProposeBtn = document.getElementById("cancelProposeBtn");
    const submitProposalScaleBtn = document.getElementById("submitProposalScaleBtn");

    if (btnProposeChange && proposeChangeModal) {
        btnProposeChange.addEventListener("click", () => {
            proposeChangeModal.style.display = "flex";
            if (window.lucide) window.lucide.createIcons();
        });
    }

    const closeProposeModal = () => {
        if (proposeChangeModal) proposeChangeModal.style.display = "none";
    };

    if (closeProposeModalBtn) closeProposeModalBtn.addEventListener("click", closeProposeModal);
    if (cancelProposeBtn) cancelProposeBtn.addEventListener("click", closeProposeModal);

    if (submitProposalScaleBtn) {
        submitProposalScaleBtn.addEventListener("click", async () => {
            const reason = document.getElementById("proposalReason").value.trim();
            if (!reason) {
                Swal.fire('Error', 'Please provide a reason for the proposal.', 'error');
                return;
            }

            const proposals = [];
            document.querySelectorAll("#proposeScaleTable tbody tr").forEach(row => {
                const gradeId = row.getAttribute("data-id");
                const minInput = row.querySelector(".prop-min-input");
                const maxInput = row.querySelector(".prop-max-input");

                const propMin = parseFloat(minInput.value) || 0;
                const propMax = parseFloat(maxInput.value) || 0;
                const origMin = parseFloat(minInput.getAttribute("data-original")) || 0;
                const origMax = parseFloat(maxInput.getAttribute("data-original")) || 0;

                if (propMin !== origMin || propMax !== origMax) {
                    proposals.push({ SalaryGradeID: gradeId, ProposedMin: propMin, ProposedMax: propMax });
                }
            });

            if (proposals.length === 0) {
                Swal.fire('No Changes', 'Please modify at least one salary grade before submitting a proposal.', 'warning');
                return;
            }

            submitProposalScaleBtn.innerText = "Submitting...";
            submitProposalScaleBtn.disabled = true;

            try {
                const response = await fetch('be_cycle_proposal.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ reason: reason, proposals: proposals })
                });
                const data = await response.json();

                if (data.success) {
                    Swal.fire('Success', 'Salary scale proposal submitted to Supervisor for endorsement.', 'success');
                    closeProposeModal();
                    document.getElementById("proposalReason").value = '';
                } else {
                    throw new Error(data.message || 'Error saving proposal');
                }
            } catch (error) {
                Swal.fire('Error', error.message, 'error');
            } finally {
                submitProposalScaleBtn.innerText = "Submit Proposal";
                submitProposalScaleBtn.disabled = false;
            }
        });
    }

    // Save Scales Logic
    const saveScalesBtnCycle = document.getElementById("saveScalesBtnCycle");
    if (saveScalesBtnCycle) {
        saveScalesBtnCycle.addEventListener("click", async () => {
            const result = await Swal.fire({
                title: 'Save Changes?',
                text: "Are you want to save this? This can affect the current salary scale.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#2ca078',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, Save Changes',
                reverseButtons: true
            });

            if (result.isConfirmed) {
                const grades = [];
                document.querySelectorAll("#salaryGradeTable tbody tr").forEach(row => {
                    grades.push({
                        id: row.getAttribute("data-id"),
                        min: row.querySelector(".min-salary-input").value,
                        max: row.querySelector(".max-salary-input").value
                    });
                });

                try {
                    const response = await fetch('save_salary_grade.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ grades: grades })
                    });
                    const data = await response.json();

                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Saved!',
                            text: 'Salary scales updated successfully.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        throw new Error(data.error || 'Failed to save');
                    }
                } catch (error) {
                    Swal.fire('Error', error.message, 'error');
                }
            }
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
        let totalFullLoad = 0;

        document.querySelectorAll(".increase-cell").forEach(cell => {
            const val = parseFloat(cell.innerText.replace(/[^0-9.-]/g, "")) || 0;
            totalIncrease += val;
        });

        document.querySelectorAll(".full-load").forEach(cell => {
            const val = parseFloat(cell.innerText.replace(/[^0-9.-]/g, "")) || 0;
            totalFullLoad += val;
        });

        const totalCostDisplay = document.getElementById("totalSimulationCost");
        if (totalCostDisplay) {
            totalCostDisplay.innerText = `\u20B1${totalIncrease.toLocaleString(undefined, { minimumFractionDigits: 2 })}`;
        }

        const totalExpenditureDisplay = document.getElementById("totalExpenditure");
        if (totalExpenditureDisplay) {
            totalExpenditureDisplay.innerText = `\u20B1${totalFullLoad.toLocaleString(undefined, { minimumFractionDigits: 2 })}`;
        }
    }

    if (submitProposalBtn) {
        submitProposalBtn.addEventListener("click", () => {
            const totalCost = document.getElementById("totalSimulationCost")?.innerText || "â‚±0.00";
            const budget = document.getElementById("budgetAllocation")?.value || 0;

            if (window.Swal) {
                Swal.fire({
                    title: 'Submit Compensation Proposal?',
                    text: `Total estimated increase cost is ${totalCost}. Initial budget: â‚±${parseFloat(budget).toLocaleString()}. This will be sent to the HR Manager for initial review before reaching Finance.`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#2ca078',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, Submit Proposal',
                    cancelButtonText: 'Review Further'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire(
                            'Proposal Submitted!',
                            'The compensation structure has been sent to the Manager for approval.',
                            'success'
                        );
                    }
                });
            } else {
                if (confirm(`Submit proposal with total cost of ${totalCost}?`)) {
                    alert("Proposal submitted successfully!");
                }
            }
        });
    }

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
    if (nameEl && umdName) {
        const name = nameEl.textContent.trim();
        umdName.textContent = name;
        if (umdAvatar) umdAvatar.textContent = name.charAt(0).toUpperCase();
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
