<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$page = $page ?? '';
$module = $module ?? '';
?>
<link rel="stylesheet" href="../../css/adminsidebar.css">
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="logo-container">
            <div class="logo-wrapper">
                <img src="../../img/logo.png" alt="Logo" class="logo">
            </div>
            <div class="logo-text">
                <h2 class="app-name">Microfinance</h2>
                <span class="app-tagline">32005</span>
            </div>
        </div>
        <button class="sidebar-toggle" id="sidebarToggle" type="button">
            <i data-lucide="panel-left-close"></i>
        </button>
    </div>

    <nav class="sidebar-nav">

        <div class="nav-section">
            <span class="nav-section-title">ANALYTICS & REPORTING</span>
            <a href="dashboard.php" class="nav-item <?php echo($page === 'dashboard') ? 'active' : ''; ?>">
                <i data-lucide="layout-dashboard"></i>
                <span>HR ANALYTICS</span>
            </a>
        </div>

        <div class="nav-section">
            <span class="nav-section-title">HUMAN RESOURCES I</span>

            <div class="nav-item-group <?php echo($module === 'recruitment') ? 'active' : ''; ?>">
                <button class="nav-item has-submenu" data-module="recruitment" type="button">
                    <div class="nav-item-content">
                        <i data-lucide="layers-plus"></i>
                        <span>Recruitment</span>
                    </div>
                    <i data-lucide="chevron-down" class="submenu-icon"></i>
                </button>
                <div class="submenu" id="submenu-recruitment">
                    <a href="recruitment.php" class="submenu-item <?php echo($page === 'recruitment') ? 'active' : ''; ?>">
                        <i data-lucide="layers-plus"></i>
                        <span>Recruitment</span>
                    </a>
                </div>
            </div>

            <div class="nav-item-group <?php echo($module === 'applicationmgt') ? 'active' : ''; ?>">
                <button class="nav-item has-submenu" data-module="applicationmgt" type="button">
                    <div class="nav-item-content">
                        <i data-lucide="contact-round"></i>
                        <span>Applicant Management</span>
                    </div>
                    <i data-lucide="chevron-down" class="submenu-icon"></i>
                </button>
                <div class="submenu" id="submenu-applicationmgt">
                    <a href="applicationmgt.php" class="submenu-item <?php echo($page === 'applicationmgt') ? 'active' : ''; ?>">
                        <i data-lucide="contact-round"></i>
                        <span>Applicant Management</span>
                    </a>
                </div>
            </div>

            <div class="nav-item-group <?php echo($module === 'newhiredonboard') ? 'active' : ''; ?>">
                <button class="nav-item has-submenu" data-module="newhiredonboard" type="button">
                    <div class="nav-item-content">
                        <i data-lucide="user-plus"></i>
                        <span>New Hired Onboard</span>
                    </div>
                    <i data-lucide="chevron-down" class="submenu-icon"></i>
                </button>
                <div class="submenu" id="submenu-newhiredonboard">
                    <a href="newhiredonboard.php" class="submenu-item <?php echo($page === 'newhiredonboard') ? 'active' : ''; ?>">
                        <i data-lucide="user-plus"></i>
                        <span>New Hired Onboard</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="nav-section">
            <span class="nav-section-title">HUMAN RESOURCES II</span>

            <div class="nav-item-group <?php echo($module === 'accounts') ? 'active' : ''; ?>">
                <button class="nav-item has-submenu" data-module="accounts" type="button">
                    <div class="nav-item-content">
                        <i data-lucide="users"></i>
                        <span>Account Management</span>
                    </div>
                    <i data-lucide="chevron-down" class="submenu-icon"></i>
                </button>
                <div class="submenu" id="submenu-accounts">
                    <a href="useraccount.php" class="submenu-item <?php echo($page === 'useraccount') ? 'active' : ''; ?>">
                        <i data-lucide="user-plus"></i>
                        <span>User Accounts</span>
                    </a>
                    <a href="rolespermission.php" class="submenu-item <?php echo($page === 'rolespermission') ? 'active' : ''; ?>">
                        <i data-lucide="contact-round"></i>
                        <span>Roles & Permissions</span>
                    </a>
                    <a href="securitysetting.php" class="submenu-item <?php echo($page === 'securitysetting') ? 'active' : ''; ?>">
                        <i data-lucide="user-cog"></i>
                        <span>Security Settings</span>
                    </a>
                    <a href="auditlogs.php" class="submenu-item <?php echo($page === 'auditlogs') ? 'active' : ''; ?>">
                        <i data-lucide="book-user"></i>
                        <span>Audit Logs</span>
                    </a>
                </div>
            </div>

            <div class="nav-item-group <?php echo($module === 'competency') ? 'active' : ''; ?>">
                <button class="nav-item has-submenu" data-module="competency" type="button">
                    <div class="nav-item-content">
                        <i data-lucide="pickaxe"></i>
                        <span>Competency Management</span>
                    </div>
                    <i data-lucide="chevron-down" class="submenu-icon"></i>
                </button>
                <div class="submenu" id="submenu-competency">
                    <a href="competencylibrary.php" class="submenu-item <?php echo($page === 'competencylibrary' || $page === 'competency') ? 'active' : ''; ?>">
                        <i data-lucide="book-text"></i>
                        <span>Competency Library</span>
                    </a>
                    <a href="competencycategory.php" class="submenu-item <?php echo($page === 'competencycategory') ? 'active' : ''; ?>">
                        <i data-lucide="chart-bar-stacked"></i>
                        <span>Competency Category</span>
                    </a>
                    <a href="competencylevel.php" class="submenu-item <?php echo($page === 'competencylevel') ? 'active' : ''; ?>">
                        <i data-lucide="circle-gauge"></i>
                        <span>Competency Level</span>
                    </a>
                    <a href="competencyposition.php" class="submenu-item <?php echo($page === 'competencyposition') ? 'active' : ''; ?>">
                        <i data-lucide="briefcase"></i>
                        <span>Competency Position</span>
                    </a>
                </div>
            </div>

            <div class="nav-item-group <?php echo($module === 'training') ? 'active' : ''; ?>">
                <button class="nav-item has-submenu" data-module="training" type="button">
                    <div class="nav-item-content">
                        <i data-lucide="briefcase-business"></i>
                        <span>Training Management</span>
                    </div>
                    <i data-lucide="chevron-down" class="submenu-icon"></i>
                </button>
                <div class="submenu" id="submenu-training">
                    <a href="training.php" class="submenu-item <?php echo($page === 'training') ? 'active' : ''; ?>">
                        <i data-lucide="briefcase-business"></i>
                        <span>Training Management</span>
                    </a>
                </div>
            </div>

            <div class="nav-item-group <?php echo($module === 'succession') ? 'active' : ''; ?>">
                <button class="nav-item has-submenu" data-module="succession" type="button">
                    <div class="nav-item-content">
                        <i data-lucide="notebook-pen"></i>
                        <span>Succession Planning</span>
                    </div>
                    <i data-lucide="chevron-down" class="submenu-icon"></i>
                </button>
                <div class="submenu" id="submenu-succession">
                    <a href="succession.php" class="submenu-item <?php echo($page === 'succession') ? 'active' : ''; ?>">
                        <i data-lucide="notebook-pen"></i>
                        <span>Succession Planning</span>
                    </a>
                </div>
            </div>

            <div class="nav-item-group <?php echo($module === 'learning') ? 'active' : ''; ?>">
                <button class="nav-item has-submenu" data-module="learning" type="button">
                    <div class="nav-item-content">
                        <i data-lucide="notebook-text"></i>
                        <span>Learning Management</span>
                    </div>
                    <i data-lucide="chevron-down" class="submenu-icon"></i>
                </button>
                <div class="submenu" id="submenu-learning">
                    <a href="learning.php" class="submenu-item <?php echo($page === 'learning') ? 'active' : ''; ?>">
                        <i data-lucide="notebook-text"></i>
                        <span>Learning Management</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="nav-section">
            <span class="nav-section-title">HUMAN RESOURCES III</span>

            <div class="nav-item-group <?php echo($module === 'shift') ? 'active' : ''; ?>">
                <button class="nav-item has-submenu" data-module="shift" type="button">
                    <div class="nav-item-content">
                        <i data-lucide="calendar-check"></i>
                        <span>Shift & Scheduling</span>
                    </div>
                    <i data-lucide="chevron-down" class="submenu-icon"></i>
                </button>
                <div class="submenu" id="submenu-shift">
                    <a href="employees.php" class="submenu-item <?php echo($page === 'employees') ? 'active' : ''; ?>">
                        <i data-lucide="inbox"></i>
                        <span>Receive Employees</span>
                    </a>
                    <a href="admin_roster.php" class="submenu-item <?php echo($page === 'admin_roster') ? 'active' : ''; ?>">
                        <i data-lucide="send-to-back"></i>
                        <span>Shift & Scheduling</span>
                    </a>
                </div>
            </div>

            <div class="nav-item-group <?php echo($module === 'claims') ? 'active' : ''; ?>">
                <button class="nav-item has-submenu" data-module="claims" type="button">
                    <div class="nav-item-content">
                        <i data-lucide="receipt-text"></i>
                        <span>Claims & Reimbursements</span>
                    </div>
                    <i data-lucide="chevron-down" class="submenu-icon"></i>
                </button>
                <div class="submenu" id="submenu-claims">
                    <a href="admin_claims_review.php" class="submenu-item <?php echo($page === 'claims') ? 'active' : ''; ?>">
                        <i data-lucide="receipt-text"></i>
                        <span>Claims & Reimbursements</span>
                    </a>
                </div>
            </div>

            <div class="nav-item-group <?php echo($module === 'timesheet') ? 'active' : ''; ?>">
                <button class="nav-item has-submenu" data-module="timesheet" type="button">
                    <div class="nav-item-content">
                        <i data-lucide="calendar-days"></i>
                        <span>Timesheet</span>
                    </div>
                    <i data-lucide="chevron-down" class="submenu-icon"></i>
                </button>
                <div class="submenu" id="submenu-timesheet">
                    <a href="admin_timesheet.php" class="submenu-item <?php echo($page === 'timesheet') ? 'active' : ''; ?>">
                        <i data-lucide="calendar-days"></i>
                        <span>Timesheet</span>
                    </a>
                </div>
            </div>

            <div class="nav-item-group <?php echo($module === 'leave') ? 'active' : ''; ?>">
                <button class="nav-item has-submenu" data-module="leave" type="button">
                    <div class="nav-item-content">
                        <i data-lucide="tickets-plane"></i>
                        <span>Leave Management</span>
                    </div>
                    <i data-lucide="chevron-down" class="submenu-icon"></i>
                </button>
                <div class="submenu" id="submenu-leave">
                    <a href="admin_leave.php" class="submenu-item <?php echo($page === 'leave') ? 'active' : ''; ?>">
                        <i data-lucide="tickets-plane"></i>
                        <span>Leave Management</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="nav-section">
            <span class="nav-section-title">HUMAN RESOURCES IV</span>

            <div class="nav-item-group <?php echo($module === 'corehumancapital') ? 'active' : ''; ?>">
                <button class="nav-item has-submenu" data-module="corehumancapital" type="button">
                    <div class="nav-item-content">
                        <i data-lucide="book-user"></i>
                        <span>Core Human Capital</span>
                    </div>
                    <i data-lucide="chevron-down" class="submenu-icon"></i>
                </button>
                <div class="submenu" id="submenu-corehumancapital">
                    <a href="dispatch.php" class="submenu-item <?php echo($page === 'dispatch') ? 'active' : ''; ?>">
                        <i data-lucide="send"></i>
                        <span>Master Data Dispatch</span>
                    </a>
                    <a href="orgprofile.php" class="submenu-item <?php echo($page === 'orgprofile') ? 'active' : ''; ?>">
                        <i data-lucide="building-2"></i>
                        <span>Organization Profile</span>
                    </a>
                    <a href="positioncatalog.php" class="submenu-item <?php echo($page === 'positioncatalog') ? 'active' : ''; ?>">
                        <i data-lucide="user-star"></i>
                        <span>Position Catalog</span>
                    </a>
                    <a href="employeemaster.php" class="submenu-item <?php echo($page === 'employeemaster') ? 'active' : ''; ?>">
                        <i data-lucide="file-user"></i>
                        <span>Employee Master Files</span>
                    </a>
                    <a href="informationapproval.php" class="submenu-item <?php echo($page === 'informationapproval') ? 'active' : ''; ?>">
                        <i data-lucide="file-check"></i>
                        <span>Information Approval</span>
                    </a>
                    <a href="bankform.php" class="submenu-item <?php echo($page === 'bankform') ? 'active' : ''; ?>">
                        <i data-lucide="file-text"></i>
                        <span>Bank Form Management</span>
                    </a>
                    <a href="auditlogs.php" class="submenu-item <?php echo($page === 'auditlogs') ? 'active' : ''; ?>">
                        <i data-lucide="book-user"></i>
                        <span>Audit Logs</span>
                    </a>
                </div>
            </div>

            <div class="nav-item-group <?php echo($module === 'planning') ? 'active' : ''; ?>">
                <button class="nav-item has-submenu" data-module="planning" type="button">
                    <div class="nav-item-content">
                        <i data-lucide="circle-pile"></i>
                        <span>Compensation Planning</span>
                    </div>
                    <i data-lucide="chevron-down" class="submenu-icon"></i>
                </button>
                <div class="submenu" id="submenu-planning">
                    <a href="salary.php" class="submenu-item <?php echo($page === 'salarymgt') ? 'active' : ''; ?>">
                        <i data-lucide="banknote"></i>
                        <span>Salary & Scales Management</span>
                    </a>
                    <a href="statutory.php" class="submenu-item <?php echo($page === 'statutory') ? 'active' : ''; ?>">
                        <i data-lucide="scale"></i>
                        <span>Statutory Contributions</span>
                    </a>
                    <a href="matrix.php" class="submenu-item <?php echo($page === 'matrix') ? 'active' : ''; ?>">
                        <i data-lucide="scale"></i>
                        <span>Merit Matrix Structure</span>
                    </a>
                    <a href="cycle.php" class="submenu-item <?php echo($page === 'cycle') ? 'active' : ''; ?>">
                        <i data-lucide="notebook-pen"></i>
                        <span>Compensation Structure Management</span>
                    </a>
                </div>
            </div>

            <div class="nav-item-group <?php echo($module === 'payroll') ? 'active' : ''; ?>">
                <button class="nav-item has-submenu" data-module="payroll" type="button">
                    <div class="nav-item-content">
                        <i data-lucide="banknote"></i>
                        <span>Payroll Management</span>
                    </div>
                    <i data-lucide="chevron-down" class="submenu-icon"></i>
                </button>
                <div class="submenu" id="submenu-payroll">
                    <a href="comperules.php" class="submenu-item <?php echo($page === 'comperules') ? 'active' : ''; ?>">
                        <i data-lucide="boxes"></i>
                        <span>Compensation Rules</span>
                    </a>
                    <a href="payroll.php" class="submenu-item <?php echo($page === 'payroll') ? 'active' : ''; ?>">
                        <i data-lucide="play-circle"></i>
                        <span>Payroll Processing</span>
                    </a>
                    <a href="#" class="submenu-item">
                        <i data-lucide="history"></i>
                        <span>Payroll History</span>
                    </a>
                    <a href="#" class="submenu-item">
                        <i data-lucide="file-check"></i>
                        <span>Approvals</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="nav-section">
            <span class="nav-section-title">FINANCE</span>

            <div class="nav-item-group <?php echo($module === 'budget') ? 'active' : ''; ?>">
                <button class="nav-item has-submenu" data-module="budget" type="button">
                    <div class="nav-item-content">
                        <i data-lucide="hand-coins"></i>
                        <span>Budget Management</span>
                    </div>
                    <i data-lucide="chevron-down" class="submenu-icon"></i>
                </button>
                <div class="submenu" id="submenu-budget">
                    <a href="positionrequest.php" class="submenu-item <?php echo($page === 'positionrequest') ? 'active' : ''; ?>">
                        <i data-lucide="badge-dollar-sign"></i>
                        <span>Position Requests</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="nav-section">
            <span class="nav-section-title">SETTINGS</span>

            <a href="#" class="nav-item">
                <i data-lucide="settings"></i>
                <span>Configuration</span>
            </a>

            <a href="#" class="nav-item">
                <i data-lucide="shield"></i>
                <span>Security</span>
            </a>
        </div>
    </nav>

    <div class="sidebar-footer">
        <div class="user-profile">
            <div class="user-avatar">
                <img src="../../img/profile.png" alt="User">
            </div>
            <div class="user-info">
                <span class="user-name"><?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?></span>
                <span class="user-role"><?php echo htmlspecialchars($_SESSION['user_role'] ?? 'Administrator'); ?></span>
            </div>
            <button class="user-menu-btn" id="userMenuBtn" type="button">
                <i data-lucide="more-vertical"></i>
            </button>
            <div class="user-menu-dropdown" id="userMenuDropdown">
                <div class="umd-header">
                    <div class="umd-avatar" id="umdAvatar"></div>
                    <div class="umd-info">
                        <span class="umd-signed">Signed in as</span>
                        <span class="umd-name" id="umdName"></span>
                        <span class="umd-role" id="umdRole"></span>
                    </div>
                </div>
                <div class="umd-divider"></div>
                <a href="profile.php" class="umd-item">
                    <i data-lucide="user-round"></i>
                    <span>Profile</span>
                </a>
                <div class="umd-divider"></div>
                <a href="../../login.php" class="umd-item umd-item-danger umd-sign-out">
                    <i data-lucide="log-out"></i>
                    <span>Sign Out</span>
                </a>
            </div>
        </div>
    </div>
</aside>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const lucide = window.lucide;
    const body = document.body;
    const themeToggle = document.getElementById("themeToggle");
    const sidebarToggle = document.getElementById("sidebarToggle");
    const sidebar = document.getElementById("sidebar");
    const mobileMenuBtn = document.getElementById("mobileMenuBtn");

    function openSubmenu(parentGroup) {
        if (!parentGroup) return;
        const submenu = parentGroup.querySelector(".submenu");
        const btn = parentGroup.querySelector(".nav-item.has-submenu");

        parentGroup.classList.add("open");
        if (btn) btn.classList.add("open");

        if (submenu) {
            submenu.classList.add("open");
            submenu.style.maxHeight = submenu.scrollHeight + "px";
        }
    }

    function closeSubmenu(parentGroup) {
        if (!parentGroup) return;
        const submenu = parentGroup.querySelector(".submenu");
        const btn = parentGroup.querySelector(".nav-item.has-submenu");

        parentGroup.classList.remove("open");
        if (btn) btn.classList.remove("open");

        if (submenu) {
            submenu.classList.remove("open");
            submenu.style.maxHeight = "0px";
        }
    }

    function toggleSubmenu(parentGroup) {
        if (!parentGroup) return;
        const isOpen = parentGroup.classList.contains("open");
        if (isOpen) {
            closeSubmenu(parentGroup);
        } else {
            openSubmenu(parentGroup);
        }
    }

    // Theme Logic
    const savedTheme = localStorage.getItem("theme");
    if (savedTheme === "dark") {
        body.classList.add("dark-mode");
    }

    if (themeToggle) {
        themeToggle.addEventListener("click", function () {
            body.classList.toggle("dark-mode");
            localStorage.setItem(
                "theme",
                body.classList.contains("dark-mode") ? "dark" : "light"
            );
        });
    }

    // Sidebar Collapse Logic
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener("click", function () {
            sidebar.classList.toggle("collapsed");
            localStorage.setItem(
                "sidebarCollapsed",
                sidebar.classList.contains("collapsed")
            );
        });

        if (localStorage.getItem("sidebarCollapsed") === "true") {
            sidebar.classList.add("collapsed");
        }
    }

    // Mobile Menu Logic
    if (mobileMenuBtn && sidebar) {
        mobileMenuBtn.addEventListener("click", function () {
            sidebar.classList.toggle("mobile-open");
        });
    }

    // Submenu Logic
    document.querySelectorAll(".nav-item.has-submenu").forEach(function (item) {
        item.addEventListener("click", function () {
            const parentGroup = item.closest(".nav-item-group");
            toggleSubmenu(parentGroup);
        });
    });

    // Active Link Logic
    (function () {
        const path = window.location.pathname;
        const pageName = path.split("/").pop() || "dashboard.php";
        const current = pageName.split("?")[0];

        document.querySelectorAll(".sidebar .nav-item, .sidebar .submenu-item").forEach(function (el) {
            el.classList.remove("active");
        });

        document.querySelectorAll(".sidebar .nav-item-group").forEach(function (group) {
            group.classList.remove("active");
            closeSubmenu(group);
        });

        const submenuMatch = document.querySelector('.sidebar a.submenu-item[href$="' + current + '"]');

        if (submenuMatch) {
            submenuMatch.classList.add("active");

            const parentGroup = submenuMatch.closest(".nav-item-group");
            if (parentGroup) {
                parentGroup.classList.add("active");

                const btn = parentGroup.querySelector(".nav-item.has-submenu");
                if (btn) {
                    btn.classList.add("active");
                }

                openSubmenu(parentGroup);
            }
            return;
        }

        const navMatch = document.querySelector('.sidebar a.nav-item[href$="' + current + '"]');
        if (navMatch) {
            navMatch.classList.add("active");
        }
    })();

    // User Menu Logic
    const nameEl = document.querySelector(".sidebar-footer .user-name");
    const roleEl = document.querySelector(".sidebar-footer .user-role");
    const umdName = document.getElementById("umdName");
    const umdRole = document.getElementById("umdRole");
    const umdAvatar = document.getElementById("umdAvatar");

    if (nameEl && umdName) {
        const name = nameEl.textContent.trim();
        umdName.textContent = name;

        if (umdAvatar) {
            umdAvatar.textContent = name.charAt(0).toUpperCase();
        }
    }

    if (roleEl && umdRole) {
        umdRole.textContent = roleEl.textContent.trim();
    }

    const btn = document.getElementById("userMenuBtn");
    const dd = document.getElementById("userMenuDropdown");

    if (btn && dd) {
        btn.addEventListener("click", function (e) {
            e.stopPropagation();
            dd.classList.toggle("umd-open");
        });

        document.addEventListener("click", function (e) {
            if (!dd.contains(e.target) && e.target !== btn) {
                dd.classList.remove("umd-open");
            }
        });

        document.addEventListener("keydown", function (e) {
            if (e.key === "Escape") {
                dd.classList.remove("umd-open");
            }
        });
    }

    // Sign Out Logic
    const signOutLinks = document.querySelectorAll(".umd-sign-out");
    signOutLinks.forEach(function (link) {
        link.addEventListener("click", async function (e) {
            e.preventDefault();
            const dest = link.getAttribute("href");

            const result = await Swal.fire({
                title: "Sign Out?",
                text: "You are about to sign out of your account.",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#ef4444",
                cancelButtonColor: "#6b7280",
                confirmButtonText: '<i class="swal-icon-logout"></i> Yes, Sign Out',
                cancelButtonText: "Stay",
                reverseButtons: true,
                customClass: {
                    popup: "swal-signout-popup",
                    title: "swal-signout-title"
                }
            });

            if (result.isConfirmed) {
                await Swal.fire({
                    icon: "success",
                    title: "Signed Out",
                    text: "You have been signed out successfully.",
                    timer: 1500,
                    showConfirmButton: false
                });

                window.location.href = dest;
            }
        });
    });

    if (typeof lucide !== "undefined") {
        lucide.createIcons();
    }
});
</script>
<script src="https://unpkg.com/lucide@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>