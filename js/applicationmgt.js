document.addEventListener("DOMContentLoaded", () => {
    console.log("Recruitment JS Initializing...");
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
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener("click", () => {
            sidebar.classList.toggle("collapsed");
            localStorage.setItem("sidebarCollapsed", sidebar.classList.contains("collapsed"));
        });
    }

    if (localStorage.getItem("sidebarCollapsed") === "true" && sidebar) sidebar.classList.add("collapsed");
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
                if (submenu.classList.contains("active")) {
                    submenu.style.maxHeight = submenu.scrollHeight + "px";
                } else {
                    submenu.style.maxHeight = "0";
                }
            }
        });
    });

    // 4. Table Filtering
    const searchInput = document.getElementById("applicantSearch");
    const statusFilter = document.getElementById("statusFilter");
    const tableRows = document.querySelectorAll(".applicant-row");

    const filterTable = () => {
        const query = searchInput ? searchInput.value.toLowerCase() : "";
        const status = statusFilter ? statusFilter.value : "all";
        tableRows.forEach(row => {
            const text = row.innerText.toLowerCase();
            const rowStatus = row.getAttribute("data-status");
            const matchesSearch = text.includes(query);
            const matchesStatus = status === "all" || rowStatus === status;
            row.style.display = matchesSearch && matchesStatus ? "" : "none";
        });
    };

    if (searchInput) searchInput.addEventListener("input", filterTable);
    if (statusFilter) statusFilter.addEventListener("change", filterTable);

    // 5. Modal Logic (View Details)
    const modal = document.getElementById("applicantModal");
    const closeModal = document.getElementById("closeModal");
    let activeApplicantId = null;

    document.querySelectorAll(".view-details").forEach(btn => {
        btn.addEventListener("click", async () => {
            const id = btn.getAttribute("data-id");
            activeApplicantId = id;
            try {
                const response = await fetch(`backend/applicant_action.php?action=get_details&id=${id}`);
                const result = await response.json();
                if (result.success) {
                    const data = result.data;
                    const firstName = data.FirstName || "";
                    const lastName = data.LastName || "";
                    const initials = (firstName.charAt(0) + lastName.charAt(0)).toUpperCase() || "??";
                    document.getElementById("modalAvatar").innerText = initials;
                    document.getElementById("modalName").innerText = `${data.FirstName} ${data.MiddleName ? data.MiddleName + ' ' : ''}${data.LastName}`;
                    document.getElementById("modalJob").innerHTML = `<i data-lucide="briefcase"></i> ${data.JobTitle}`;
                    document.getElementById("modalApplied").innerHTML = `<i data-lucide="calendar"></i> Applied ${new Date(data.AppliedAt).toLocaleDateString()}`;
                    document.getElementById("modalEmail").innerText = data.Email;
                    document.getElementById("modalPhone").innerText = data.Phone;
                    document.getElementById("modalGenderBirth").innerText = `${data.Gender || 'N/A'} | Born ${data.DateOfBirth || 'N/A'}`;
                    document.getElementById("modalAddress").innerText = data.PermanentAddress || 'N/A';
                    document.getElementById("modalEmergency").innerText = `${data.EmergencyContactName || 'N/A'} (${data.EmergencyRelationship || 'N/A'}) - ${data.EmergencyPhone || 'N/A'}`;

                    const docsContainer = document.getElementById("modalDocs");
                    docsContainer.innerHTML = '';
                    const docs = [
                        { label: 'Resume/CV', path: data.ResumePath, icon: 'file-text' },
                        { label: 'Gov ID', path: data.GovIDPath, icon: 'id-card' },
                        { label: 'Clearance', path: data.ClearancePath, icon: 'shield-check' },
                        { label: 'TOR', path: data.TORPath, icon: 'graduation-cap' },
                        { label: 'ID Picture', path: data.IDPicturePath, icon: 'image' }
                    ];

                    docs.forEach(doc => {
                        if (doc.path) {
                            const link = document.createElement('a');
                            link.href = `../../${doc.path}`;
                            link.target = '_blank';
                            link.className = "doc-link-custom"; // Style via CSS or inline
                            link.style.cssText = "display:flex; align-items:center; justify-content:space-between; padding:14px 18px; background:var(--background); border-radius:12px; border:1px solid var(--border-color); text-decoration:none; transition:all 0.2s ease; cursor:pointer;";
                            link.innerHTML = `
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div style="background: rgba(59, 130, 246, 0.1); color: #3b82f6; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                        <i data-lucide="${doc.icon}" style="width: 16px;"></i>
                                    </div>
                                    <span style="font-size: 14px; font-weight: 600; color: var(--text-primary);">${doc.label}</span>
                                </div>
                                <i data-lucide="external-link" style="width: 14px; color: var(--text-tertiary);"></i>
                            `;
                            docsContainer.appendChild(link);
                        }
                    });

                    // Assessment Info
                    const resumeInput = document.getElementById("resumeScoreInput");
                    if (resumeInput) resumeInput.value = data.ResumeScore || 0;

                    const ratingText = document.getElementById("modalRatingText");
                    if (ratingText) {
                        const score = data.ExamScore || 0; // Temporarily using ExamScore if rating not available
                        // We actually have e.AverageRating joined in some queries but maybe not here
                        // Let's assume we fetch evaluation avg if present
                        ratingText.innerText = data.AverageRating ? `${Number(data.AverageRating).toFixed(1)} / 5.0` : "No Rating";
                    }

                    const examText = document.getElementById("modalExamText");
                    if (examText) examText.innerText = data.ExamScore ? `${data.ExamScore} / 15` : "Not Taken";

                    const schedBtn = document.getElementById("scheduleInterviewBtn");
                    const evalBtn = document.getElementById("evaluateBtn");

                    // Button visibility logic
                    if (data.Status === 'Interview') {
                        if (schedBtn) schedBtn.classList.add("hidden");
                        if (evalBtn) evalBtn.classList.remove("hidden");
                    } else if (data.Status === 'Accepted' || data.Status === 'Rejected') {
                        // Hide both if already decided
                        if (schedBtn) schedBtn.classList.add("hidden");
                        if (evalBtn) evalBtn.classList.add("hidden");
                    } else {
                        // Default for New/Reviewed/Shortlisted
                        if (schedBtn) schedBtn.classList.remove("hidden");
                        if (evalBtn) evalBtn.classList.add("hidden");
                    }
                    modal.style.display = "flex";
                    if (window.lucide) window.lucide.createIcons();
                }
            } catch (error) { console.error("Error fetching details:", error); }
        });
    });

    // Handle Resume Score Save
    const saveResumeBtn = document.getElementById("saveResumeScoreBtn");
    if (saveResumeBtn) {
        saveResumeBtn.addEventListener("click", async () => {
            const score = document.getElementById("resumeScoreInput").value;
            if (score === "" || score < 0 || score > 100) {
                Swal.fire("Invalid Score", "Please enter a value between 0 and 100.", "warning");
                return;
            }

            saveResumeBtn.disabled = true;
            saveResumeBtn.innerText = "Saving...";

            const formData = new FormData();
            formData.append("action", "update_resume_score");
            formData.append("id", activeApplicantId);
            formData.append("score", score);

            try {
                const response = await fetch("backend/applicant_action.php", { method: "POST", body: formData });
                const result = await response.json();
                if (result.success) {
                    Swal.fire("Saved", "Resume score updated successfully. Refreshing selection rankings...", "success").then(() => {
                        location.reload(); // Refresh to update the selection ranking tab
                    });
                } else {
                    Swal.fire("Error", result.message, "error");
                }
            } catch (error) {
                console.error("Error saving resume score:", error);
                Swal.fire("Error", "Failed to save score.", "error");
            } finally {
                saveResumeBtn.disabled = false;
                saveResumeBtn.innerText = "Save";
            }
        });
    }

    if (closeModal) closeModal.addEventListener("click", () => modal.style.display = "none");
    window.addEventListener("click", (e) => { if (e.target === modal) modal.style.display = "none"; });

    // 6. Tab Switching Logic
    const tabBtns = document.querySelectorAll(".tab-btn");
    const tabContents = document.querySelectorAll(".tab-content");
    tabBtns.forEach(btn => {
        btn.addEventListener("click", () => {
            const tabId = btn.getAttribute("data-tab");
            tabBtns.forEach(b => b.classList.remove("active"));
            tabContents.forEach(c => c.classList.remove("active"));
            btn.classList.add("active");
            const activeTab = document.getElementById(`${tabId}Tab`);
            if (activeTab) activeTab.classList.add("active");
        });
    });

    // 7. Scheduling Modal
    const scheduleModal = document.getElementById("scheduleModal");
    const scheduleForm = document.getElementById("scheduleForm");
    const schedTrigger = document.getElementById("scheduleInterviewBtn");
    if (schedTrigger) {
        schedTrigger.addEventListener("click", () => {
            document.getElementById("schedApplicantId").value = activeApplicantId;

            // Auto-scheduling: Set date to tomorrow, time to 9:00 AM
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            const dateStr = tomorrow.toISOString().split('T')[0];

            const dateInput = document.getElementById("schedDate");
            const timeInput = document.getElementById("schedTime");

            if (dateInput) dateInput.value = dateStr;
            if (timeInput) timeInput.value = "09:00";

            modal.style.display = "none";
            scheduleModal.classList.add("active");
        });
    }

    const interviewMode = document.getElementById("interviewMode");
    if (interviewMode) {
        interviewMode.addEventListener("change", () => {
            const locLabel = document.getElementById("locationLabel");
            const locInput = document.getElementById("locationLink");
            if (locLabel && locInput) {
                if (interviewMode.value === "Online") {
                    locLabel.innerText = "Meeting Link (Zoom/Google Meet)";
                    locInput.placeholder = "https://meet.google.com/xxx-xxxx-xxx";
                } else {
                    locLabel.innerText = "Office Location";
                    locInput.placeholder = "Room 302, 3rd Floor";
                }
            }
        });
    }

    if (scheduleForm) {
        scheduleForm.addEventListener("submit", async (e) => {
            e.preventDefault();
            const submitBtn = document.getElementById("submitScheduleBtn");

            // Spam Protection: Disable button
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="spin-icon"></i> Processing...';
            }

            const formData = new FormData(scheduleForm);
            try {
                const response = await fetch('backend/interview_action.php', { method: 'POST', body: formData });
                const result = await response.json();

                if (result.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: result.message,
                        icon: 'success',
                        target: 'body', // Ensure it's above everything
                        customClass: { container: 'swal-z-index' }
                    }).then(() => location.reload());
                } else {
                    Swal.fire('Error', result.error, 'error');
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i data-lucide="calendar-check-2"></i> Finalize Invitation';
                        if (window.lucide) lucide.createIcons();
                    }
                }
            } catch (err) {
                console.error(err);
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Retry Invitation';
                }
            }
        });
    }

    // 8. Evaluation Logic
    const evaluationModal = document.getElementById("evaluationModal");
    const evaluationForm = document.getElementById("evaluationForm");
    const openEvaluation = (id, name) => {
        if (evaluationForm) {
            evaluationForm.reset();
            evaluationForm.querySelectorAll(".star-btn").forEach(btn => btn.classList.remove("active"));
            evaluationForm.querySelectorAll("input[type='hidden'][id^='rating_']").forEach(input => input.value = "0");
            document.getElementById("avgScoreDisplay").innerText = "0.0";
        }
        document.getElementById("evalApplicantId").value = id;
        document.getElementById("evalCandidateName").innerText = `Evaluate: ${name}`;
        evaluationModal.classList.add("active");
    };

    const evalTrigger = document.getElementById("evaluateBtn");
    if (evalTrigger) {
        evalTrigger.addEventListener("click", () => {
            const name = document.getElementById("modalName").innerText;
            modal.style.display = "none";
            openEvaluation(activeApplicantId, name);
        });
    }

    document.querySelectorAll(".evaluate-candidate").forEach(btn => {
        btn.addEventListener("click", () => {
            openEvaluation(btn.getAttribute("data-id"), btn.getAttribute("data-name"));
        });
    });

    const updateAverage = () => {
        const categories = ['technical', 'communication', 'financial', 'reliability'];
        let total = 0, count = 0;
        categories.forEach(cat => {
            const el = document.getElementById(`rating_${cat}`);
            if (el) {
                const val = parseInt(el.value) || 0;
                if (val > 0) { total += val; count++; }
            }
        });
        const display = document.getElementById("avgScoreDisplay");
        if (display) display.innerText = count > 0 ? (total / count).toFixed(1) : "0.0";
    };

    document.addEventListener("click", (e) => {
        const btn = e.target.closest(".star-btn");
        if (!btn) return;

        const container = btn.closest(".rating-stars");
        if (!container) return;

        const category = container.getAttribute("data-category");
        const val = btn.getAttribute("data-value");
        const hiddenInput = document.getElementById(`rating_${category}`);

        if (hiddenInput) {
            hiddenInput.value = val;
            container.querySelectorAll(".star-btn").forEach(b => {
                const bVal = b.getAttribute("data-value");
                b.classList.toggle("active", parseInt(bVal) <= parseInt(val));
            });
            updateAverage();
        }
    });

    if (evaluationForm) {
        evaluationForm.addEventListener("submit", async (e) => {
            e.preventDefault();
            const formData = new FormData(evaluationForm);
            const response = await fetch('backend/evaluation_action.php', { method: 'POST', body: formData });
            const result = await response.json();
            if (result.success) {
                Swal.fire('Evaluation Submitted', result.message, 'success').then(() => location.reload());
            } else { Swal.fire('Error', result.error, 'error'); }
        });
    }

    // 9. Hiring Approval Actions
    document.addEventListener('click', function (e) {
        const viewBtn = e.target.closest('.view-eval');
        if (viewBtn) {
            const evalId = viewBtn.getAttribute('data-id');
            fetch(`backend/applicant_action.php?action=get_evaluation&id=${evalId}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const ev = data.data;
                        document.getElementById('viewEvalTitle').textContent = `Evaluation: ${ev.FirstName} ${ev.LastName}`;
                        document.getElementById('displayAvg').textContent = Number(ev.AverageRating).toFixed(1);
                        const decisionEl = document.getElementById('displayDecision');
                        decisionEl.textContent = ev.Decision;
                        decisionEl.style.color = ev.Decision === 'Strong Hire' ? '#10b981' : (ev.Decision === 'Do Not Hire' ? '#ef4444' : '#f59e0b');
                        document.getElementById('displayComments').textContent = ev.Comments || "No additional comments provided.";
                        document.getElementById('val_tech').textContent = `${ev.TechnicalRating} / 5`;
                        document.getElementById('val_comm').textContent = `${ev.CommunicationRating} / 5`;
                        document.getElementById('val_fin').textContent = `${ev.FinancialRating} / 5`;
                        document.getElementById('val_rel').textContent = `${ev.ReliabilityRating} / 5`;
                        document.getElementById('viewEvalModal').classList.add('active');
                    }
                });
        }

        const approveBtn = e.target.closest('.approve-hire');
        if (approveBtn) {
            const appId = approveBtn.getAttribute('data-id');
            Swal.fire({
                title: 'Confirm Hiring Decision?',
                text: "This candidate will be approved for onboarding.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2ca078',
                confirmButtonText: 'Yes, Approve Hire'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('id', appId);
                    formData.append('action', 'approve_hire');
                    fetch('backend/applicant_action.php', { method: 'POST', body: formData })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) { Swal.fire('Hired!', data.message, 'success').then(() => location.reload()); }
                            else { Swal.fire('Error', data.message || 'Action failed', 'error'); }
                        });
                }
            });
        }
    });

    // 10. User Menu & Logout
    const userBtn = document.getElementById('userMenuBtn');
    const userDropdown = document.getElementById('userMenuDropdown');
    if (userBtn && userDropdown) {
        userBtn.addEventListener('click', e => { e.stopPropagation(); userDropdown.classList.toggle('umd-open'); });
        document.addEventListener('click', e => { if (!userDropdown.contains(e.target) && e.target !== userBtn) userDropdown.classList.remove('umd-open'); });
    }

    document.querySelectorAll('.umd-sign-out').forEach(link => {
        link.addEventListener('click', async e => {
            e.preventDefault();
            const result = await Swal.fire({
                title: 'Sign Out?',
                text: 'You are about to sign out of your account.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Yes, Sign Out'
            });
            if (result.isConfirmed) window.location.href = link.getAttribute('href');
        });
    });

    // 11. Global Clicks (Modal Close)
    window.addEventListener("click", (e) => {
        if (e.target === scheduleModal) scheduleModal.classList.remove("active");
        if (e.target === evaluationModal) evaluationModal.classList.remove("active");
        if (e.target === document.getElementById('viewEvalModal')) document.getElementById('viewEvalModal').classList.remove('active');
    });

    // 12. Real-time Clock
    const clockEl = document.getElementById('realTimeClock');
    if (clockEl) {
        const updateClock = () => {
            clockEl.textContent = new Date().toLocaleString('en-US', {
                weekday: 'short', month: 'short', day: 'numeric', year: 'numeric',
                hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true
            });
        };
        setInterval(updateClock, 1000);
        updateClock();
    }

    if (window.lucide) window.lucide.createIcons();
});
