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

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initClock);
    initClock();
}

const addDeptBtn = document.getElementById('addDeptBtn');
if (addDeptBtn) {
    addDeptBtn.addEventListener('click', () => {
        Swal.fire({
            title: 'Add New Department',
            input: 'text',
            inputLabel: 'Department Name',
            inputPlaceholder: 'Enter department name...',
            showCancelButton: true,
            confirmButtonText: 'Add Department',
            confirmButtonColor: '#2ca078',
            cancelButtonColor: '#6b7280',
            background: 'var(--surface)',
            color: 'var(--text-primary)',
            customClass: {
                popup: 'premium-swal-popup',
                title: 'premium-swal-title',
                confirmButton: 'premium-swal-confirm'
            },
            preConfirm: (value) => {
                if (!value) {
                    Swal.showValidationMessage('Department name is required');
                }
                return value;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const deptInput = document.getElementById('deptInput');
                const addDeptForm = document.getElementById('addDeptForm');
                if (deptInput && addDeptForm) {
                    deptInput.value = result.value;
                    addDeptForm.submit();
                }
            }
        });
    });
}

const deleteDeptBtn = document.getElementById('deleteDeptBtn');
if (deleteDeptBtn) {
    deleteDeptBtn.addEventListener('click', () => {
        if (typeof deletableDepts === 'undefined' || deletableDepts.length === 0) {
            Swal.fire({
                icon: 'info',
                title: 'No deletable departments',
                text: 'Only newly added departments can be removed. Core departments are protected.',
                confirmButtonColor: '#2ca078'
            });
            return;
        }

        const inputOptions = {};
        deletableDepts.forEach(dept => {
            inputOptions[dept.id] = dept.name;
        });

        Swal.fire({
            title: 'Select Department to Remove',
            input: 'select',
            inputOptions: inputOptions,
            inputPlaceholder: 'Choose a department...',
            showCancelButton: true,
            confirmButtonText: 'Remove',
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            background: 'var(--surface)',
            color: 'var(--text-primary)',
        }).then((result) => {
            if (result.isConfirmed) {
                const selectedId = result.value;
                const deptName = inputOptions[selectedId];

                Swal.fire({
                    title: 'Are you sure?',
                    text: `You are about to remove the "${deptName}" department. This action cannot be undone.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, remove it',
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280'
                }).then((confirmResult) => {
                    if (confirmResult.isConfirmed) {
                        const deleteDeptIdInput = document.getElementById('deleteDeptIdInput');
                        const deleteDeptForm = document.getElementById('deleteDeptForm');
                        if (deleteDeptIdInput && deleteDeptForm) {
                            deleteDeptIdInput.value = selectedId;
                            deleteDeptForm.submit();
                        }
                    }
                });
            }
        });
    });
}

// Show Department Details Modal
function showDeptDetails(dept) {
    if (!dept || !dept.positions) return;

    let positionsHtml = `
        <div class="dept-details-container">
            <style>
                .details-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 15px;
                    background: var(--surface);
                    border-radius: 8px;
                    overflow: hidden;
                }
                .details-table th {
                    background: rgba(44, 160, 120, 0.1);
                    color: var(--brand-green);
                    padding: 12px;
                    text-align: left;
                    font-size: 13px;
                }
                .details-table td {
                    padding: 12px;
                    border-bottom: 1px solid var(--border-color);
                    color: var(--text-primary);
                    font-size: 14px;
                }
                .details-table tr:last-child td {
                    border-bottom: none;
                }
                .headcount-badge {
                    background: rgba(44, 160, 120, 0.15);
                    color: var(--brand-green);
                    padding: 4px 10px;
                    border-radius: 12px;
                    font-weight: 600;
                    font-size: 12px;
                    display: inline-block;
                }
                .headcount-badge.vacancy {
                    background: rgba(239, 68, 68, 0.1);
                    color: #ef4444;
                }
                .modal-title-wrapper {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    gap: 12px;
                    width: 100%;
                }
                .modal-title-icon {
                    color: var(--brand-green);
                    display: flex;
                    align-items: center;
                }
            </style>
            <table class="details-table">
                <thead>
                    <tr>
                        <th>Position Name</th>
                        <th style="text-align: center;">Headcount (Current/Auth)</th>
                    </tr>
                </thead>
                <tbody>
    `;

    if (dept.positions.length > 0) {
        dept.positions.forEach(pos => {
            const isVacancy = pos.current < pos.authorized;
            const badgeClass = isVacancy ? 'headcount-badge vacancy' : 'headcount-badge';
            const headcountDisplay = `${pos.current} / ${pos.authorized}`;

            positionsHtml += `
                <tr>
                    <td>${pos.name}</td>
                    <td style="text-align: center;">
                        <span class="${badgeClass}">${headcountDisplay}</span>
                    </td>
                </tr>
            `;
        });
    } else {
        positionsHtml += `<tr><td colspan="2" style="text-align: center; padding: 20px;">No positions documented for this department.</td></tr>`;
    }

    positionsHtml += `
                </tbody>
            </table>
        </div>
    `;

    Swal.fire({
        title: dept.name,
        html: positionsHtml,
        iconHtml: `<i data-lucide="${dept.icon || 'building-2'}" style="width: 80px; height: 80px; stroke-width: 1.5;"></i>`,
        customClass: {
            icon: 'no-border'
        },
        confirmButtonText: 'Got it',
        confirmButtonColor: '#2ca078',
        width: '550px',
        didOpen: () => {
            if (window.lucide) {
                lucide.createIcons();
            }
        }
    });
}
