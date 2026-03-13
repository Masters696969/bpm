// Filter by Department
function filterByDept(deptId) {
    if (deptId === 'all') {
        window.location.href = 'positioncatalog.php';
    } else {
        window.location.href = 'positioncatalog.php?dept=' + deptId;
    }
}

// Toggle Department Accordion
function toggleAccordion(deptName) {
    const selector = 'accordion-' + deptName.replace(/ /g, '-');
    const accordion = document.getElementById(selector);
    if (accordion) {
        accordion.classList.toggle('active');
    }
}

// Send Requisition Trigger
function sendRequisition(posId, posName) {
    Swal.fire({
        title: 'Send Hiring Requisition?',
        text: `You are about to initiate a formal hiring request for "${posName}". This will be managed in the Recruitment module.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Send Request',
        confirmButtonColor: '#2ca078',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('reqPosId').value = posId;
            document.getElementById('requisitionForm').submit();
        }
    });
}

// Cancel Requisition Trigger
function cancelRequisition(posId, posName) {
    Swal.fire({
        title: 'Cancel Hiring Requisition?',
        text: `Are you sure you want to stop the recruitment process for "${posName}"? This will move the requisition to "Cancelled" status.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, Cancel it',
        confirmButtonColor: '#ef4444',
        cancelButtonText: 'Keep it',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('cancelReqPosId').value = posId;
            document.getElementById('cancelRequisitionForm').submit();
        }
    });
}

// Manage Position Modal (Full Edit)
function launchManagePositionModal(pos) {
    const departments = JSON.parse(document.getElementById('dept-data').textContent);
    const grades = JSON.parse(document.getElementById('grade-data').textContent);

    let deptOptions = departments.map(d => `<option value="${d.DepartmentID}" ${d.DepartmentID == pos.deptId ? 'selected' : ''}>${d.DepartmentName}</option>`).join('');
    let gradeOptions = grades.map(g => `<option value="${g.SalaryGradeID}" ${g.SalaryGradeID == pos.gradeId ? 'selected' : ''}>${g.GradeName}</option>`).join('');

    Swal.fire({
        title: 'Manage Position',
        html: `
            <div style="text-align: left;">
                <div class="swal-field">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px; color: var(--text-secondary);">Position Name</label>
                    <input id="swalManageName" class="swal2-input" value="${pos.name}" style="margin: 0; width: 100%;">
                </div>
                <div class="swal-field" style="margin-top: 15px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px; color: var(--text-secondary);">Position Code</label>
                    <input id="swalManageCode" class="swal2-input" value="${pos.code}" style="margin: 0; width: 100%;">
                </div>
                <div class="swal-field" style="margin-top: 15px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px; color: var(--text-secondary);">Department</label>
                    <select id="swalManageDept" style="margin: 0; width: 100%; display: block; padding: 12px; border: 1px solid var(--border-color); border-radius: 8px; background: var(--surface); color: var(--text-primary); cursor: pointer;">
                        ${deptOptions}
                    </select>
                </div>
                <div class="swal-field" style="margin-top: 15px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px; color: var(--text-secondary);">Salary Grade</label>
                    <select id="swalManageGrade" style="margin: 0; width: 100%; display: block; padding: 12px; border: 1px solid var(--border-color); border-radius: 8px; background: var(--surface); color: var(--text-primary); cursor: pointer;">
                        ${gradeOptions}
                    </select>
                </div>
                <div class="swal-field" style="margin-top: 15px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px; color: var(--text-secondary);">Authorized Headcount</label>
                    <input id="swalManageAuth" type="number" class="swal2-input" value="${pos.auth}" min="1" style="margin: 0; width: 100%;">
                </div>
                <div style="margin-top: 25px; padding-top: 20px; border-top: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 12px; color: var(--text-tertiary);">Dangerous Action:</span>
                    <button type="button" onclick="confirmDeletePosition(${pos.id}, '${pos.name.replace(/'/g, "\\'")}')" 
                        style="padding: 8px 16px; background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                        <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                        Delete Position
                    </button>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Submit Change Request',
        confirmButtonColor: '#2ca078',
        cancelButtonText: 'Cancel',
        didOpen: () => {
            if (window.lucide) window.lucide.createIcons();
        },
        preConfirm: () => {
            const name = document.getElementById('swalManageName').value;
            const code = document.getElementById('swalManageCode').value;
            const deptId = document.getElementById('swalManageDept').value;
            const gradeId = document.getElementById('swalManageGrade').value;
            const auth = document.getElementById('swalManageAuth').value;

            if (!name) {
                Swal.showValidationMessage('Please enter a position name');
                return false;
            }
            return { name, code, deptId, gradeId, auth };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Updating Position...',
                text: 'Please wait while we submit the change request.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            document.getElementById('updatePosId').value = pos.id;
            document.getElementById('updatePosName').value = result.value.name;
            document.getElementById('updatePosCode').value = result.value.code;
            document.getElementById('updateDeptId').value = result.value.deptId;
            document.getElementById('updateGradeId').value = result.value.gradeId;
            document.getElementById('updateAuthHeadcount').value = result.value.auth;
            document.getElementById('updatePositionForm').submit();
        }
    });
}

// Separate function for Delete Confirmation
function confirmDeletePosition(id, name) {
    Swal.fire({
        title: 'Delete Position?',
        text: `Are you sure you want to delete "${name}"? This action cannot be undone.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, Delete Position',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Requesting Deletion...',
                text: 'Please wait while we submit your request.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            document.getElementById('deletePosId').value = id;
            document.getElementById('deletePositionForm').submit();
        }
    });
}

// Launch Add Position Modal
function launchAddPositionModal() {
    const departments = JSON.parse(document.getElementById('dept-data').textContent);
    const grades = JSON.parse(document.getElementById('grade-data').textContent);

    let deptOptions = departments.map(d => `<option value="${d.DepartmentID}">${d.DepartmentName}</option>`).join('');
    let gradeOptions = grades.map(g => `<option value="${g.SalaryGradeID}">${g.GradeName}</option>`).join('');

    Swal.fire({
        title: 'Add New Position',
        html: `
            <div style="text-align: left;">
                <div class="swal-field">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px; color: var(--text-secondary);">Position Name</label>
                    <input id="swalPosName" class="swal2-input" placeholder="e.g. Senior Loan Officer" style="margin: 0; width: 100%;">
                </div>
                <div class="swal-field" style="margin-top: 15px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px; color: var(--text-secondary);">Position Code</label>
                    <input id="swalPosCode" class="swal2-input" placeholder="e.g. FIN-SLO" style="margin: 0; width: 100%;">
                </div>
                <div class="swal-field" style="margin-top: 15px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px; color: var(--text-secondary);">Department</label>
                    <select id="swalDeptId" style="margin: 0; width: 100%; display: block; padding: 12px; border: 1px solid var(--border-color); border-radius: 8px; background: var(--surface); color: var(--text-primary); cursor: pointer;">
                        ${deptOptions}
                    </select>
                </div>
                <div class="swal-field" style="margin-top: 15px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px; color: var(--text-secondary);">Salary Grade</label>
                    <select id="swalGradeId" style="margin: 0; width: 100%; display: block; padding: 12px; border: 1px solid var(--border-color); border-radius: 8px; background: var(--surface); color: var(--text-primary); cursor: pointer;">
                        ${gradeOptions}
                    </select>
                </div>
                <div class="swal-field" style="margin-top: 15px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600; font-size: 14px; color: var(--text-secondary);">Authorized Headcount</label>
                    <input id="swalAuthHeadcount" type="number" class="swal2-input" value="1" min="1" style="margin: 0; width: 100%;">
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Add Position',
        confirmButtonColor: '#2ca078',
        cancelButtonText: 'Cancel',
        preConfirm: () => {
            const name = document.getElementById('swalPosName').value;
            const code = document.getElementById('swalPosCode').value;
            const deptId = document.getElementById('swalDeptId').value;
            const gradeId = document.getElementById('swalGradeId').value;
            const auth = document.getElementById('swalAuthHeadcount').value;

            if (!name) {
                Swal.showValidationMessage('Please enter a position name');
                return false;
            }
            return { name, code, deptId, gradeId, auth };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Adding Position...',
                text: 'Please wait while we update the catalog.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            document.getElementById('formPosName').value = result.value.name;
            document.getElementById('formPosCode').value = result.value.code;
            document.getElementById('formDeptId').value = result.value.deptId;
            document.getElementById('formGradeId').value = result.value.gradeId;
            document.getElementById('formAuthHeadcount').value = result.value.auth;
            document.getElementById('addPositionForm').submit();
        }
    });
}

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

    if (typeof lucide !== "undefined") lucide.createIcons();
});

// Sidebar Active Link Logic
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

// User Menu Dropdown Logic
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
