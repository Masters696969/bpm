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

    // 4. Table Selection & Search Filter
    const selectAll = document.getElementById("selectAll");
    const rowCheckboxes = document.querySelectorAll(".row-checkbox");
    const searchInput = document.getElementById("roleSearch");
    const tableRows = document.querySelectorAll(".role-row-item");

    if (selectAll) {
        selectAll.addEventListener("change", () => {
            rowCheckboxes.forEach(cb => {
                if (cb.closest('tr').style.display !== 'none') {
                    cb.checked = selectAll.checked;
                }
            });
        });
    }

    if (searchInput) {
        searchInput.addEventListener("keyup", () => {
            const query = searchInput.value.toLowerCase();
            tableRows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(query) ? "" : "none";
            });
        });
    }

    // 5. Modal Logic
    const modal = document.getElementById("marketModal");
    const marketBtns = document.querySelectorAll(".market-salary-btn");
    const closeModal = document.getElementById("closeModal");
    const confirmSync = document.getElementById("confirmSync");
    let currentRole = "";

    marketBtns.forEach(btn => {
        btn.addEventListener("click", (e) => {
            const row = e.target.closest("tr");
            currentRole = row.querySelector(".client-name").innerText;
            document.getElementById("modalTitle").innerText = `Sync ${currentRole}`;
            modal.style.display = "flex";
        });
    });

    if (closeModal) closeModal.addEventListener("click", () => modal.style.display = "none");
    if (confirmSync) {
        confirmSync.addEventListener("click", () => {
            alert(`Success: ${currentRole} queued for analysis.`);
            modal.style.display = "none";
        });
    }

    if (typeof lucide !== "undefined") lucide.createIcons();
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


// ===================================================
// Question Bank Module Logic (Content Wrapper Only)
// ===================================================
(function() {
    const initQuestionBank = () => {
        const questionForm = document.getElementById('questionForm');
        const questionsGrid = document.getElementById('questionsGrid');
        const addBtn = document.getElementById('addQuestionBtn');
        const modal = document.getElementById('questionModal');
        const closeModal = document.getElementById('closeModal');
        const cancelBtn = document.getElementById('cancelBtn');
        const categoryFilter = document.getElementById('categoryFilter');
        const searchInput = document.getElementById('questionSearch');

        if (!questionsGrid) return; // Not on the right page

        let allQuestions = [];

        const fetchCategories = () => {
            fetch('backend/bankquestion_action.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=fetch_categories'
            })
            .then(res => res.json())
            .then(data => {
                if (data.success && categoryFilter) {
                    categoryFilter.innerHTML = '<option value="">All Categories</option>';
                    data.data.forEach(cat => {
                        let displayName = cat.name;
                        // Match user's specific naming if possible, otherwise use DB name
                        if (!displayName.toLowerCase().includes('competencies') && !displayName.toLowerCase().includes('transaction')) {
                            displayName += ' Competencies';
                        }
                        categoryFilter.innerHTML += `<option value="${cat.id}">${displayName}</option>`;
                    });
                }
            });
        };

        const fetchCompetencies = () => {
            fetch('backend/bankquestion_action.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=fetch_competencies'
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const compSelect = document.getElementById('competency_id');
                    
                    if (compSelect) {
                        // Group by category for the modal dropdown
                        const grouped = data.data.reduce((acc, curr) => {
                            const cat = curr.category_name || 'Uncategorized';
                            if (!acc[cat]) acc[cat] = [];
                            acc[cat].push(curr);
                            return acc;
                        }, {});

                        let optionsHtml = '';
                        for (const cat in grouped) {
                            optionsHtml += `<optgroup label="${cat}">`;
                            grouped[cat].forEach(comp => {
                                optionsHtml += `<option value="${comp.id}">${comp.name}</option>`;
                            });
                            optionsHtml += `</optgroup>`;
                        }
                        compSelect.innerHTML = '<option value="" disabled selected>Select Competency</option>' + optionsHtml;
                    }
                }
            });
        };

        const fetchQuestions = () => {
            fetch('backend/bankquestion_action.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=fetch_questions'
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    allQuestions = data.data;
                    renderQuestions(allQuestions);
                }
            });
        };

        const renderQuestions = (questions) => {
            questionsGrid.innerHTML = '';
            questions.forEach(q => {
                const card = document.createElement('div');
                card.className = 'question-card';
                
                const optionsHtml = `
                    <div class="qc-options">
                        <div class="option-item ${q.correct_answer === 'A' ? 'correct' : ''}">A. ${q.option_a}</div>
                        <div class="option-item ${q.correct_answer === 'B' ? 'correct' : ''}">B. ${q.option_b}</div>
                        <div class="option-item ${q.correct_answer === 'C' ? 'correct' : ''}">C. ${q.option_c}</div>
                        <div class="option-item ${q.correct_answer === 'D' ? 'correct' : ''}">D. ${q.option_d}</div>
                    </div>
                `;

                card.innerHTML = `
                    <div class="qc-header">
                        <span class="qc-type">Multiple Choice</span>
                        <div class="table-actions">
                            <button class="action-btn edit-btn edit" data-id="${q.id}">
                                <i data-lucide="edit-3"></i>
                            </button>
                            <button class="action-btn delete-btn delete" data-id="${q.id}">
                                <i data-lucide="trash-2"></i>
                            </button>
                        </div>
                    </div>
                    <div class="qc-text">${q.question_text}</div>
                    ${optionsHtml}
                    <div class="qc-footer">
                        <span class="qc-competency">
                            <i data-lucide="tag" style="width:12px;height:12px;display:inline;margin-right:4px;"></i>${q.category_name} 
                            <span style="opacity:0.6; margin:0 4px;">&bull;</span>
                            <i data-lucide="award" style="width:12px;height:12px;display:inline;margin-right:4px;"></i>${q.competency_name}
                        </span>
                    </div>
                `;
                questionsGrid.appendChild(card);
            });
            if (window.lucide) window.lucide.createIcons();
            attachCardEvents();
        };

        const attachCardEvents = () => {
            document.querySelectorAll('.edit').forEach(btn => {
                btn.onclick = () => {
                    const id = btn.dataset.id;
                    const q = allQuestions.find(x => x.id == id);
                    if (q) fillModal(q);
                };
            });

            document.querySelectorAll('.delete').forEach(btn => {
                btn.onclick = () => {
                    const id = btn.dataset.id;
                    if (window.Swal) {
                        Swal.fire({
                            title: 'Are you sure?',
                            text: "This question will be deleted permanently.",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#ef4444',
                            cancelButtonColor: '#111827',
                            confirmButtonText: 'Yes, delete it!'
                        }).then((result) => {
                            if (result.isConfirmed) deleteQuestion(id);
                        });
                    }
                };
            });
        };

        const fillModal = (q) => {
            document.getElementById('question_id').value = q.id;
            document.getElementById('competency_id').value = q.competency_id;
            document.getElementById('question_text').value = q.question_text;
            document.getElementById('modalTitle').innerText = 'Edit Question';
            
            document.getElementById('opt_a').value = q.option_a;
            document.getElementById('opt_b').value = q.option_b;
            document.getElementById('opt_c').value = q.option_c;
            document.getElementById('opt_d').value = q.option_d;
            document.getElementById('correct_mc').value = q.correct_answer;

            modal.style.display = 'flex';
        };

        const deleteQuestion = (id) => {
            fetch('backend/bankquestion_action.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=delete&id=${id}`
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (window.Swal) Swal.fire('Deleted!', 'Question removed.', 'success');
                    fetchQuestions();
                }
            });
        };

        const filterQuestions = () => {
            const term = searchInput.value.toLowerCase();
            const catId = categoryFilter.value;

            const filtered = allQuestions.filter(q => {
                const matchTerm = q.question_text.toLowerCase().includes(term);
                const matchCat = catId === '' || q.category_id == catId;
                return matchTerm && matchCat;
            });
            renderQuestions(filtered);
        };

        if (addBtn) {
            addBtn.addEventListener('click', () => {
                questionForm.reset();
                document.getElementById('question_id').value = '';
                document.getElementById('modalTitle').innerText = 'Add New Question';
                modal.style.display = 'flex';
            });
        }

        if (closeModal) closeModal.addEventListener('click', () => modal.style.display = 'none');
        if (cancelBtn) cancelBtn.addEventListener('click', () => modal.style.display = 'none');
        if (searchInput) searchInput.oninput = filterQuestions;
        if (categoryFilter) categoryFilter.onchange = filterQuestions;

        if (questionForm) {
            questionForm.onsubmit = (e) => {
                e.preventDefault();
                const formData = new FormData(questionForm);
                const id = document.getElementById('question_id').value;
                formData.append('action', id ? 'update' : 'add');
                
                // Assuming only multiple_choice, so hardcode question_type and correct_answer source
                formData.set('question_type', 'multiple_choice');
                formData.set('correct_answer', document.getElementById('correct_mc').value);

                fetch('backend/bankquestion_action.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        modal.style.display = 'none';
                        if (window.Swal) Swal.fire('Saved!', data.message, 'success');
                        fetchQuestions();
                    } else {
                        if (window.Swal) Swal.fire('Error', data.message, 'error');
                    }
                });
            };
        }

        fetchCategories();
        fetchCompetencies();
        fetchQuestions();
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof initClock === 'function') initClock();
            initQuestionBank();
        });
    } else {
        if (typeof initClock === 'function') initClock();
        initQuestionBank();
    }
})();
