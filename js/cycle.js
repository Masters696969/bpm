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
            const cycleName = document.querySelector('input[value="FY2025 Annual Merit Review"]').value;
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
            const currentPayVal = parseFloat(row.querySelector(".current-pay")?.innerText.replace(/[₱,]/g, "") || 0);
            const proposedBasic = parseFloat(row.querySelector(".proposed-gross")?.innerText.replace(/[₱,]/g, "") || currentPayVal);
            const totalAllowances = parseFloat(row.querySelector(".total-allowances")?.innerText.replace(/[₱,]/g, "") || 0);
            const taxableAllowances = gradeTaxableMap[gradeID] || 0;

            const totalGross = proposedBasic + totalAllowances;

            // 1. SSS Calculation (2026 Logic)
            // Regular SSS: 5% EE | Cap at ₱35,000 MSC (Max ₱1,750)
            const sssMSCCap = 35000;
            const sssRateEE = 0.05;
            const sssRateER = 0.10;

            const regularBase = Math.min(proposedBasic, sssMSCCap);
            let sssEE = regularBase * sssRateEE;
            let sssER = regularBase * sssRateER;

            // 2. SSS WISP Calculation (User Rule: ₱900 for high earners like 80k)
            // Total EE Share capped at ₱1,750 regular + ₱900 WISP = ₱2,650 for max MSC
            let wispEE = 0;
            if (proposedBasic >= sssMSCCap) {
                wispEE = 900;
            } else if (proposedBasic > 20000) {
                // Pro-rated WISP if needed, but user confirmed 900 for 80k
                wispEE = Math.max(0, proposedBasic - 20000) * 0.02;
            }
            let wispER = wispEE; // Assuming equal split

            // 3. PhilHealth Calculation (2.5% EE | Cap ₱2,500)
            const phRateTotal = 0.05;
            const phCapEE = 2500;
            const phEE = Math.min((proposedBasic * phRateTotal) / 2, phCapEE);
            const phER = phEE;

            // 4. Pag-IBIG Calculation (Max ₱200)
            const piRateEE = 0.02;
            const piCap = 200;
            const piEE = Math.min(proposedBasic * piRateEE, piCap);
            const piER = piEE;

            const totalEERead = sssEE + wispEE + phEE + piEE;
            const employerShare = sssER + wispER + phER + piER;
            const fullLoad = totalGross + employerShare;

            // 5. Withholding Tax (TRAIN Law 2026 Monthly)
            // Taxable Income = (Basic + Taxable Allowances) - (Total EE Contribs)
            // Note: This subtracts Non-Taxable Allowances (like Rice and Laundry) from the tax base.
            let taxable = (proposedBasic + taxableAllowances) - totalEERead;
            let tax = 0;

            if (taxable > 666667) {
                tax = 183541.67 + (taxable - 666667) * 0.35;
            } else if (taxable > 166667) {
                tax = 33541.67 + (taxable - 166667) * 0.30;
            } else if (taxable > 66667) {
                // Correct Bracket 4: 8,541.67 + 25% of excess over 66,667
                // User provided 8,541.80 as base
                tax = 8541.80 + (taxable - 66667) * 0.25;
            } else if (taxable > 33333) {
                tax = 1875 + (taxable - 33333) * 0.20;
            } else if (taxable > 20833) {
                tax = (taxable - 20833) * 0.15;
            }

            const netPay = totalGross - totalEERead - tax;

            // 6. Pay Rates based on BASIC Salary (User Rule)
            const daily = proposedBasic / 22;
            const hourly = daily / 8;
            const semi = totalGross / 2; // Semi-monthly usually half of total gross take-home basis

            // Update row UI
            const totalGrossCell = row.querySelector(".total-gross");
            if (totalGrossCell) totalGrossCell.innerText = `₱${totalGross.toLocaleString(undefined, { minimumFractionDigits: 2 })}`;
            if (row.querySelector(".employer-share")) row.querySelector(".employer-share").innerText = `₱${employerShare.toLocaleString(undefined, { minimumFractionDigits: 2 })}`;
            if (row.querySelector(".full-load")) row.querySelector(".full-load").innerText = `₱${fullLoad.toLocaleString(undefined, { minimumFractionDigits: 2 })}`;

            if (row.querySelector(".rate-semi")) row.querySelector(".rate-semi").innerText = `₱${semi.toLocaleString(undefined, { minimumFractionDigits: 2 })}`;
            if (row.querySelector(".rate-daily")) row.querySelector(".rate-daily").innerText = `₱${daily.toLocaleString(undefined, { minimumFractionDigits: 2 })}`;
            if (row.querySelector(".rate-hourly")) row.querySelector(".rate-hourly").innerText = `₱${hourly.toLocaleString(undefined, { minimumFractionDigits: 2 })}`;

            if (row.querySelector(".deduction-sss")) row.querySelector(".deduction-sss").innerText = `₱${sssEE.toLocaleString(undefined, { minimumFractionDigits: 2 })}`;
            if (row.querySelector(".deduction-wisp")) row.querySelector(".deduction-wisp").innerText = `₱${wispEE.toLocaleString(undefined, { minimumFractionDigits: 2 })}`;
            if (row.querySelector(".deduction-ph")) row.querySelector(".deduction-ph").innerText = `₱${phEE.toLocaleString(undefined, { minimumFractionDigits: 2 })}`;
            if (row.querySelector(".deduction-pi")) row.querySelector(".deduction-pi").innerText = `₱${piEE.toLocaleString(undefined, { minimumFractionDigits: 2 })}`;
            if (row.querySelector(".deduction-tax")) row.querySelector(".deduction-tax").innerText = `₱${tax.toLocaleString(undefined, { minimumFractionDigits: 2 })}`;
            if (row.querySelector(".net-pay-cell")) row.querySelector(".net-pay-cell").innerText = `₱${netPay.toLocaleString(undefined, { minimumFractionDigits: 2 })}`;
        });

        updateTotalSimulationCost();
    }

    if (tableInputs.length > 0) {
        tableInputs.forEach(input => {
            input.addEventListener("input", (e) => {
                const row = e.target.closest("tr");
                if (!row) return;

                const currentPayText = row.querySelector(".current-pay")?.innerText.replace(/[₱,]/g, "") || "0";
                const currentPay = parseFloat(currentPayText);

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
                const allowanceCell = row.querySelector(".total-allowances");
                const totalGrossCell = row.querySelector(".total-gross");
                const increaseCell = row.querySelector(".increase-cell");
                const semiCell = row.querySelector(".rate-semi");
                const dailyCell = row.querySelector(".rate-daily");
                const hourlyCell = row.querySelector(".rate-hourly");

                if (proposedGrossCell) proposedGrossCell.innerText = `₱${newGross.toLocaleString(undefined, { minimumFractionDigits: 0 })}`;
                if (increaseCell) increaseCell.innerText = `+₱${netIncrease.toLocaleString(undefined, { minimumFractionDigits: 0 })}`;

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

        // Archive Row logic
        const archiveBtn = row.querySelector(".archive-grade-btn");
        if (archiveBtn) {
            archiveBtn.addEventListener("click", () => {
                if (window.Swal) {
                    Swal.fire({
                        title: 'Archive this grade?',
                        text: "This grade will be hidden from current planning.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'Yes, Archive'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            row.style.opacity = '0.5';
                            row.style.pointerEvents = 'none';
                            row.classList.add('archived-row');
                            setTimeout(() => row.remove(), 500);
                        }
                    });
                } else if (confirm("Archive this grade?")) {
                    row.remove();
                }
            });
        }
    }

    // Initialize existing rows
    document.querySelectorAll(".comp-table.editable-table tbody tr").forEach(row => initScaleCalculations(row));

    // Add Grade Button
    // Modal Elements for Add Grade
    const gradeModal = document.getElementById("gradeModal");
    const addGradeBtn = document.getElementById("addGradeBtn");
    const closeGradeModalBtn = document.getElementById("closeGradeModalBtn");
    const cancelGradeBtn = document.getElementById("cancelGrade");
    const gradeForm = document.getElementById("gradeForm");

    if (addGradeBtn && gradeModal) {
        addGradeBtn.addEventListener("click", () => {
            gradeModal.classList.add("active");
            gradeModal.setAttribute("aria-hidden", "false");
        });
    }

    const closeModal = () => {
        if (gradeModal) {
            gradeModal.classList.remove("active");
            gradeModal.setAttribute("aria-hidden", "true");
            gradeForm?.reset();
        }
    };

    [closeGradeModalBtn, cancelGradeBtn].forEach(btn => {
        btn?.addEventListener("click", closeModal);
    });

    // Modal Midpoint Dynamic Calculation
    const modalMin = document.getElementById("modal_min_salary");
    const modalMax = document.getElementById("modal_max_salary");
    const modalMid = document.getElementById("modal_mid_salary");

    if (modalMin && modalMax && modalMid) {
        [modalMin, modalMax].forEach(input => {
            input.addEventListener("input", () => {
                const min = parseFloat(modalMin.value) || 0;
                const max = parseFloat(modalMax.value) || 0;
                modalMid.value = Math.round((min + max) / 2);
            });
        });
    }

    if (gradeForm) {
        gradeForm.addEventListener("submit", (e) => {
            e.preventDefault();
            const formData = new FormData(gradeForm);
            const tbody = document.querySelector("#salaryGradeTable tbody");
            if (!tbody) return;

            const min = parseFloat(formData.get("min_salary")) || 0;
            const max = parseFloat(formData.get("max_salary")) || 0;
            const mid = Math.round((min + max) / 2);
            const spread = min > 0 ? (((max - min) / min) * 100).toFixed(1) : "0.0";

            const newRow = document.createElement("tr");
            newRow.innerHTML = `
                <td><input type="text" value="${formData.get("grade_level")}" class="table-input-premium grade-level-input"></td>
                <td><input type="text" value="${formData.get("grade_name")}" class="table-input-premium grade-name-input"></td>
                <td><input type="text" value="${formData.get("description")}" class="table-input-premium description-input" placeholder="Role details..."></td>
                <td><div class="input-with-symbol"><span>₱</span><input type="number" value="${min}" class="table-input-premium min-salary-input"></div></td>
                <td><div class="input-with-symbol"><span>₱</span><input type="number" value="${mid}" class="table-input-premium mid-salary-input" readonly></div></td>
                <td><div class="input-with-symbol"><span>₱</span><input type="number" value="${max}" class="table-input-premium max-salary-input"></div></td>
                <td class="spread-cell">${spread}%</td>
                <td>
                  <button class="btn-icon archive-grade-btn" title="Archive Grade">
                    <i data-lucide="archive"></i>
                  </button>
                </td>
            `;
            tbody.appendChild(newRow);
            if (window.lucide) window.lucide.createIcons();
            initScaleCalculations(newRow);
            closeModal();

            if (window.Swal) {
                Swal.fire({
                    title: 'Grade Added!',
                    text: 'The new salary grade has been staged temporarily.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
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
                        // Optional: show a mini toast or just console log
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
            const val = parseFloat(cell.innerText.replace(/[+₱,]/g, "")) || 0;
            totalIncrease += val;
        });

        document.querySelectorAll(".full-load").forEach(cell => {
            const val = parseFloat(cell.innerText.replace(/[₱,]/g, "")) || 0;
            totalFullLoad += val;
        });

        const totalCostDisplay = document.getElementById("totalSimulationCost");
        if (totalCostDisplay) {
            totalCostDisplay.innerText = `₱${totalIncrease.toLocaleString(undefined, { minimumFractionDigits: 2 })}`;
        }

        const totalExpenditureDisplay = document.getElementById("totalExpenditure");
        if (totalExpenditureDisplay) {
            totalExpenditureDisplay.innerText = `₱${totalFullLoad.toLocaleString(undefined, { minimumFractionDigits: 2 })}`;
        }
    }

    if (submitProposalBtn) {
        submitProposalBtn.addEventListener("click", () => {
            const totalCost = document.getElementById("totalSimulationCost")?.innerText || "₱0.00";
            const budget = document.getElementById("budgetAllocation")?.value || 0;

            // Premium SweetAlert verification
            if (window.Swal) {
                Swal.fire({
                    title: 'Submit Compensation Proposal?',
                    text: `Total estimated increase cost is ${totalCost}. Initial budget: ₱${parseFloat(budget).toLocaleString()}. This will be sent to the HR Manager for initial review before reaching Finance.`,
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
