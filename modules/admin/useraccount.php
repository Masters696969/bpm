<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['user_role']) !== 'administrator') {
    header('Location: ../../login.php');
    exit;
}

require_once '../../config/config.php';

$page = 'useraccount';
$module = 'accounts';

// Fetch all users with their creation date (based on earliest role assignment)
$usersSql = "SELECT ua.AccountID, ua.Username, ua.Email, ua.AccountStatus, ua.IsVerified,
             MIN(uar.AssignedAt) as CreatedAt, e.EmployeeCode
             FROM useraccounts ua
             LEFT JOIN useraccountroles uar ON ua.AccountID = uar.AccountID
             LEFT JOIN employee e ON ua.EmployeeID = e.EmployeeID
             GROUP BY ua.AccountID
             ORDER BY ua.AccountID DESC";
$usersResult = $conn->query($usersSql);
$users = [];
if ($usersResult) {
    while ($row = $usersResult->fetch_assoc()) {
        $users[] = $row;
    }
}

// Fetch all roles for dropdown
$rolesSql = "SELECT RoleID, RoleName FROM roles ORDER BY RoleName ASC";
$rolesResult = $conn->query($rolesSql);
$roles = [];
if ($rolesResult) {
    while ($row = $rolesResult->fetch_assoc()) {
        $roles[] = $row;
    }
}

// Fetch employees who DON'T have a user account yet
$unlinkedEmployeesSql = "SELECT e.EmployeeID, e.EmployeeCode, e.FirstName, e.LastName 
                         FROM employee e 
                         LEFT JOIN useraccounts ua ON e.EmployeeID = ua.EmployeeID 
                         WHERE ua.AccountID IS NULL 
                         ORDER BY e.LastName ASC";
$unlinkedResult = $conn->query($unlinkedEmployeesSql);
$unlinkedEmployees = [];
if ($unlinkedResult) {
    while ($row = $unlinkedResult->fetch_assoc()) {
        $unlinkedEmployees[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Account Management - Microfinance</title>
  <link rel="stylesheet" href="../../css/useraccount.css?v=1.4">
  <script src="https://unpkg.com/lucide@0.474.0/dist/umd/lucide.js" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="icon" type="image/png" href="../../img/logo.png">
</head>
<body>

  <!-- Sidebar -->
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
      <button class="sidebar-toggle" id="sidebarToggle">
        <i data-lucide="panel-left-close"></i>
      </button>
    </div>

   <nav class="sidebar-nav">
      <div class="nav-section">
        <span class="nav-section-title">ANALYTICS & REPORTING</span>
        <a href="dashboard.php" class="nav-item <?php echo ($page === 'dashboard') ? 'active' : ''; ?>">
          <i data-lucide="layout-dashboard"></i>
          <span>HR ANALYTICS</span>
        </a>
      <div class="nav-section">
        <span class="nav-section-title">HUMAN RESOURCES I</span>
        <div class="nav-item-group <?php echo ($module === 'recruitment') ? 'active' : ''; ?>">
          <button class="nav-item has-submenu <?php echo ($module === 'recruitment') ? 'active' : ''; ?>" data-module="recruitment">
            <div class="nav-item-content">
              <i data-lucide="users"></i>
              <span>Recruitment</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-recruitment">
           <a href="recruitment.php" class="nav-item <?php echo ($page === 'recruitment') ? 'active' : ''; ?>">
              <i data-lucide="users"></i>
              <span>Recruitment</span>
            </a>
          </div>
          <div class="nav-item-group <?php echo ($module === 'applicationmgt') ? 'active' : ''; ?>">
          <button class="nav-item has-submenu <?php echo ($module === 'applicationmgt') ? 'active' : ''; ?>" data-module="applicationmgt">
            <div class="nav-item-content">
              <i data-lucide="contact-round"></i>
              <span>Applicant Management</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-applicationmgt">
            <a href="applicationmgt.php" class="nav-item <?php echo ($page === 'applicationmgt') ? 'active' : ''; ?>">
              <i data-lucide="contact-round"></i>
              <span>Applicant Management</span>
            </a>
          </div>
          <div class="nav-item-group <?php echo ($module === 'newhiredonboard') ? 'active' : ''; ?>">
          <button class="nav-item has-submenu <?php echo ($module === 'newhiredonboard') ? 'active' : ''; ?>" data-module="newhiredonboard">
            <div class="nav-item-content">
              <i data-lucide="user-plus"></i>
              <span>New Hired Onboard</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-newhiredonboard">
            <a href="newhiredonboard.php" class="nav-item <?php echo ($page === 'newhiredonboard') ? 'active' : ''; ?>">
              <i data-lucide="user-plus"></i>
              <span>New Hired Onboard</span>
            </a>
          </div>
        </div>
        <div class="nav-section">
        <span class="nav-section-title">HUMAN RESOURCES II</span>
        <div class="nav-item-group <?php echo ($module === 'accounts') ? 'active' : ''; ?>">
          <button class="nav-item has-submenu <?php echo ($module === 'accounts') ? 'active' : ''; ?>" data-module="accounts">
            <div class="nav-item-content">
              <i data-lucide="users"></i>
              <span>Account Management</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-accounts">
            <a href="useraccount.php" class="submenu-item active">
              <i data-lucide="user-plus"></i>
              <span>User Accounts</span>
            </a>
            <a href="rolespermission.php" class="submenu-item">
              <i data-lucide="contact-round"></i>
              <span>Roles & Permissions</span>
            </a>
            <a href="securitysetting.php" class="submenu-item">
              <i data-lucide="user-cog"></i>
              <span>Security Settings</span>
            </a>
            <a href="auditlogs.php" class="submenu-item">
              <i data-lucide="book-user"></i>
              <span>Audit Logs</span>
            </a>
          </div>
           <div class="nav-item-group <?php echo ($module === 'competency') ? 'active' : ''; ?>">
            <button class="nav-item has-submenu" data-module="competency">
              <div class="nav-item-content">
                <i data-lucide="pickaxe"></i>
                <span>Competency Management</span>
              </div>
              <i data-lucide="chevron-down" class="submenu-icon"></i>
            </button>
            <div class="submenu" id="submenu-competency">
              <a href="competencylibrary.php" class="submenu-item <?php echo ($page === 'competency') ? 'active' : ''; ?>">
                <i data-lucide="book-text"></i>
                <span>Competency Library</span>
              </a>
              <a href="competencycategory.php" class="submenu-item <?php echo ($page === 'competencycategory') ? 'active' : ''; ?>">
                <i data-lucide="chart-bar-stacked"></i>
                <span>Competency Category</span>
              </a>
               <a href="competencylevel.php" class="submenu-item <?php echo ($page === 'competencylevel') ? 'active' : ''; ?>">
                <i data-lucide="circle-gauge"></i>
                <span>Competency Level</span>
              </a>
              <a href="competencyposition.php" class="submenu-item <?php echo ($page === 'competencyposition') ? 'active' : ''; ?>">
                <i data-lucide="briefcase"></i>
                <span>Competency Position</span>
              </a>
              <a href="competencyemployee.php" class="submenu-item <?php echo ($page === 'competencyemployee') ? 'active' : ''; ?>">
                <i data-lucide="square-user"></i>
                <span>Competency Employee</span>
              </a>
                <a href="bankquestion.php" class="submenu-item <?php echo ($page === 'bankquestion') ? 'active' : ''; ?>">
                <i data-lucide="book-open-check"></i>
                <span>Bank Question</span>
              </a>
            </div>
        </div>
         <div class="nav-item-group <?php echo ($module === 'training') ? 'active' : ''; ?>">
            <button class="nav-item has-submenu" data-module="training">
              <div class="nav-item-content">
                <i data-lucide="briefcase-business"></i>
                <span>Training Management</span>
              </div>
              <i data-lucide="chevron-down" class="submenu-icon"></i>
            </button>
            <div class="submenu" id="submenu-training">
              <a href="training.php" class="submenu-item <?php echo ($page === 'training') ? 'active' : ''; ?>">
                <i data-lucide="briefcase-business"></i>
                <span>Training Management</span>
              </a>
            </div>
        </div>
         <div class="nav-item-group <?php echo ($module === 'succession') ? 'active' : ''; ?>">
            <button class="nav-item has-submenu" data-module="succession">
              <div class="nav-item-content">
                <i data-lucide="notebook-pen"></i>
                <span>Succession Planning</span>
              </div>
              <i data-lucide="chevron-down" class="submenu-icon"></i>
            </button>
            <div class="submenu" id="submenu-succession">
              <a href="succession.php" class="submenu-item <?php echo ($page === 'succession') ? 'active' : ''; ?>">
                <i data-lucide="notebook-pen"></i>
                <span>Succession Planning</span>
              </a>
            </div>
        </div>
         <div class="nav-item-group <?php echo ($module === 'learning') ? 'active' : ''; ?>">
            <button class="nav-item has-submenu <?php echo ($module === 'learning') ? 'active' : ''; ?>" data-module="learning">
              <div class="nav-item-content">
                <i data-lucide="notebook-text"></i>
                <span>Learning Management</span>
              </div>
              <i data-lucide="chevron-down" class="submenu-icon"></i>
            </button>
            <div class="submenu" id="submenu-learning">
              <a href="learning.php" class="submenu-item <?php echo ($page === 'learning') ? 'active' : ''; ?>">
                <i data-lucide="notebook-text"></i>
                <span>Learning Management</span>
              </a>
            </div>
        </div>
          <div class="nav-section">   
            <span class="nav-section-title">HUMAN RESOURCES III</span>
          <div class="nav-item-group <?php echo ($module === 'shift') ? 'active' : ''; ?>">
            <button class="nav-item has-submenu <?php echo ($module === 'shift') ? 'active' : ''; ?>" data-module="shift">
              <div class="nav-item-content">
                <i data-lucide="calendar-check"></i>
                <span>Shift & Scheduling</span>
              </div>
              <i data-lucide="chevron-down" class="submenu-icon"></i>
            </button>
            <div class="submenu" id="submenu-shift">
              <a href="#" class="submenu-item <?php echo ($page === 'shift') ? 'active' : ''; ?>">
                <i data-lucide="send-to-back"></i>
                <span>Shift & Scheduling</span>
              </a>
            </div>
            <div class="nav-item-group <?php echo ($module === 'claims') ? 'active' : ''; ?>">
            <button class="nav-item has-submenu <?php echo ($module === 'claims') ? 'active' : ''; ?>" data-module="claims">
              <div class="nav-item-content">
                <i data-lucide="receipt-text"></i>
                <span>Claims & Reimbursements</span>
              </div>
              <i data-lucide="chevron-down" class="submenu-icon"></i>
            </button>
            <div class="submenu" id="submenu-claims">
              <a href="claims.php" class="submenu-item <?php echo ($page === 'claims') ? 'active' : ''; ?>">
                <i data-lucide="receipt-text"></i>
                <span>Claims & Reimbursements</span>
              </a>
            </div>
            <div class="nav-item-group <?php echo ($module === 'time') ? 'active' : ''; ?>">
            <button class="nav-item has-submenu <?php echo ($module === 'time') ? 'active' : ''; ?>" data-module="time">
              <div class="nav-item-content">
                <i data-lucide="clock"></i>
                <span>Time & Attendance</span>
              </div>
              <i data-lucide="chevron-down" class="submenu-icon"></i>
            </button>
            <div class="submenu" id="submenu-time">
              <a href="time.php" class="submenu-item <?php echo ($page === 'time') ? 'active' : ''; ?>">
                <i data-lucide="clock"></i>
                <span>Time & Attendance</span>
              </a>
            </div>
            <div class="nav-item-group <?php echo ($module === 'timesheet') ? 'active' : ''; ?>">
            <button class="nav-item has-submenu <?php echo ($module === 'timesheet') ? 'active' : ''; ?>" data-module="timesheet">
              <div class="nav-item-content">
                <i data-lucide="calendar-days"></i>
                <span>Timesheet</span>
              </div>
              <i data-lucide="chevron-down" class="submenu-icon"></i>
            </button>
            <div class="submenu" id="submenu-timesheet">
              <a href="timesheet.php" class="submenu-item <?php echo ($page === 'timesheet') ? 'active' : ''; ?>">
                <i data-lucide="calendar-days"></i>
                <span>Timesheet</span>
              </a>
            </div>
             <div class="nav-item-group <?php echo ($module === 'leave') ? 'active' : ''; ?>">
            <button class="nav-item has-submenu <?php echo ($module === 'leave') ? 'active' : ''; ?>" data-module="leave">
              <div class="nav-item-content">
                <i data-lucide="tickets-plane"></i>
                <span>Leave Management</span>
              </div>
              <i data-lucide="chevron-down" class="submenu-icon"></i>
            </button>
            <div class="submenu" id="submenu-leave">
              <a href="leave.php" class="submenu-item <?php echo ($page === 'leave') ? 'active' : ''; ?>">
                <i data-lucide="tickets-plane"></i>
                <span>Leave Management</span>
              </a>
            </div>
       <div class="nav-section">
        <span class="nav-section-title">HUMAN RESOURCES IV</span>
          <div class="nav-item-group <?php echo ($module === 'corehumancapital') ? 'active' : ''; ?>">
          <button class="nav-item has-submenu <?php echo ($module === 'corehumancapital') ? 'active' : ''; ?>" data-module="corehumancapital">
            <div class="nav-item-content">
              <i data-lucide="book-user"></i>
              <span>Core Human Capital</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-corehumancapital">
            <a href="dispatch.php" class="submenu-item <?php echo ($page === 'dispatch') ? 'active' : ''; ?>">
              <i data-lucide="send"></i>
              <span>Master Data Dispatch</span>
            </a>
             <a href="orgprofile.php" class="submenu-item <?php echo ($page === 'orgprofile') ? 'active' : ''; ?>">
              <i data-lucide="building-2"></i>
              <span>Organization Profile</span>
            </a>
            <a href="positioncatalog.php" class="submenu-item <?php echo ($page === 'positioncatalog') ? 'active' : ''; ?>">
              <i data-lucide="user-star"></i>
              <span>Position Catalog</span>
            </a>
            <a href="employeemaster.php" class="submenu-item <?php echo ($page === 'employeemaster') ? 'active' : ''; ?>">
              <i data-lucide="file-user"></i>
              <span>Employee Master Files</span>
            </a>
            <a href="informationapproval.php" class="submenu-item <?php echo ($page === 'informationapproval') ? 'active' : ''; ?>">
              <i data-lucide="file-check"></i>
              <span>Information Approval</span>
            </a>
            <a href="bankform.php" class="submenu-item <?php echo ($page === 'bankform') ? 'active' : ''; ?>">
              <i data-lucide="file-text"></i>
              <span>Bank Form Management</span>
            </a>
            <a href="auditlogs.php" class="submenu-item <?php echo ($page === 'auditlogs') ? 'active' : ''; ?>">
              <i data-lucide="book-user"></i>
              <span>Audit Logs</span>
            </a>
          </div>
        </div>
          <div class="nav-item-group <?php echo ($module === 'planning') ? 'active' : ''; ?>">
          <button class="nav-item has-submenu <?php echo ($module === 'planning') ? 'active' : ''; ?>" data-module="planning">
            <div class="nav-item-content">
              <i data-lucide="circle-pile"></i>
              <span>Compensation Planning</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-planning">
           <a href="comintake.php" class="submenu-item <?php echo ($page === 'intake') ? 'active' : ''; ?>">
              <i data-lucide="layout-dashboard"></i>
              <span>Master Data Intake</span>
            </a>
            <a href="salary.php" class="submenu-item <?php echo ($page === 'salarymgt') ? 'active' : ''; ?>">
              <i data-lucide="banknote"></i>
              <span>Salary & Scales Management</span>
            </a>
            <a href="statutory.php" class="submenu-item <?php echo ($page === 'statutory') ? 'active' : ''; ?>">
              <i data-lucide="scale"></i>
              <span>Statutory Contributions</span>
            </a>
            <a href="matrix.php" class="submenu-item <?php echo ($page === 'matrix') ? 'active' : ''; ?>">
              <i data-lucide="percent"></i>
              <span>Merit Matrix Structure</span>
            </a>
            <a href="allowance.php" class="submenu-item <?php echo ($page === 'allowance') ? 'active' : ''; ?>">
              <i data-lucide="gift"></i>
              <span>Allowance Structure</span>
            </a>
            <a href="cycle.php" class="submenu-item <?php echo ($page === 'cycle') ? 'active' : ''; ?>">
              <i data-lucide="notebook-pen"></i>
              <span>Compensation Structure Management</span>
            </a>
          </div>
        </div>
        <div class="nav-item-group <?php echo ($module === 'payroll') ? 'active' : ''; ?>">
          <button class="nav-item has-submenu <?php echo ($module === 'payroll') ? 'active' : ''; ?>" data-module="payroll">
            <div class="nav-item-content">
              <i data-lucide="banknote"></i>
              <span>Payroll Management</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-payroll">
            <a href="comperules.php" class="submenu-item">
              <i data-lucide="boxes"></i>
              <span>Compensation Rules</span>
            </a>
            <a href="payroll.php" class="submenu-item <?php echo ($page === 'payroll') ? 'active' : ''; ?>">
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
        
        <div class="nav-item-group <?php echo ($module === 'budget') ? 'active' : ''; ?>">
          <button class="nav-item has-submenu <?php echo ($module === 'budget') ? 'active' : ''; ?>" data-module="budget">
            <div class="nav-item-content">
              <i data-lucide="hand-coins"></i>
              <span>Budget Management</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-budget">
            <a href="positionrequest.php" class="submenu-item <?php echo ($page === 'positionrequest') ? 'active' : ''; ?>">
              <i data-lucide="badge-dollar-sign"></i>
              <span>Position Requests</span>
            </a>
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
          <span class="user-name"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
          <span class="user-role"><?php echo htmlspecialchars($_SESSION['user_role']); ?></span>
        </div>
        <button class="user-menu-btn" id="userMenuBtn">
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
          <a href="profile.php" class="umd-item"><i data-lucide="user-round"></i><span>Profile</span></a>
          <div class="umd-divider"></div>
          <a href="../../login.php" class="umd-item umd-item-danger umd-sign-out"><i data-lucide="log-out"></i><span>Sign Out</span></a>
        </div>
      </div>
    </div>
  </aside>

  <!-- Main Content -->
  <main class="main-content">
    <header class="page-header">
      <div class="header-left">
        <button class="mobile-menu-btn" id="mobileMenuBtn">
          <i data-lucide="menu"></i>
        </button>
        <div class="header-title">
          <h1>Account Management</h1>
          <p>Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>! Manage user accounts and permissions.</p>
        </div>
      </div>
      <div class="header-right">
                        <div class="header-clock">
          <span id="realTimeClock"></span>
        </div>
        <button class="theme-toggle" id="themeToggle" aria-label="Toggle theme">
          <i data-lucide="sun" class="sun-icon"></i>
          <i data-lucide="moon" class="moon-icon"></i>
        </button>
        <button class="icon-btn">
          <i data-lucide="bell"></i>
        </button>
      </div>
    </header>

    <div class="content-wrapper">

      <!-- Stats Bar -->
      <?php
        $totalUsers    = count($users);
        $activeUsers   = count(array_filter($users, fn($u) => $u['AccountStatus'] === 'Active'));
        $inactiveUsers = $totalUsers - $activeUsers;
        $unverified    = count(array_filter($users, fn($u) => !$u['IsVerified']));
      ?>
      <div class="ua-stats">
        <div class="ua-stat-card">
          <div class="ua-stat-icon blue"><i data-lucide="users"></i></div>
          <div class="ua-stat-info">
            <span class="ua-stat-value"><?php echo $totalUsers; ?></span>
            <span class="ua-stat-label">Total Accounts</span>
          </div>
        </div>
        <div class="ua-stat-card">
          <div class="ua-stat-icon green"><i data-lucide="user-check"></i></div>
          <div class="ua-stat-info">
            <span class="ua-stat-value"><?php echo $activeUsers; ?></span>
            <span class="ua-stat-label">Active</span>
          </div>
        </div>
        <div class="ua-stat-card">
          <div class="ua-stat-icon amber"><i data-lucide="user-minus"></i></div>
          <div class="ua-stat-info">
            <span class="ua-stat-value"><?php echo $inactiveUsers; ?></span>
            <span class="ua-stat-label">Inactive</span>
          </div>
        </div>
        <div class="ua-stat-card">
          <div class="ua-stat-icon red"><i data-lucide="shield-off"></i></div>
          <div class="ua-stat-info">
            <span class="ua-stat-value"><?php echo $unverified; ?></span>
            <span class="ua-stat-label">Unverified</span>
          </div>
        </div>
      </div>

      <!-- Table Card -->
      <section class="users-panel">
        <div class="panel-header">
          <div class="panel-header-left">
            <div class="panel-header-icon"><i data-lucide="user-cog"></i></div>
            <div class="panel-header-titles">
              <h2>User Accounts</h2>
              <div class="panel-header-sub"><?php echo $totalUsers; ?> accounts registered</div>
            </div>
          </div>
          <div class="panel-actions">
            <div class="panel-search">
              <i data-lucide="search"></i>
              <input type="search" id="tableSearch" placeholder="Search accounts…">
            </div>
            <select id="statusFilter" class="ua-filter-select">
              <option value="">All Status</option>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
            <select id="verifiedFilter" class="ua-filter-select">
              <option value="">All Verified</option>
              <option value="verified">Verified</option>
              <option value="unverified">Unverified</option>
            </select>
            <button id="addUserBtn" class="btn btn-primary">
              <i data-lucide="user-plus"></i> Add Account
            </button>
          </div>
        </div>

        <div class="panel-body">
          <div class="table-responsive">
            <table id="usersTable" class="users-table">
              <thead>
                <tr>
                  <th>User</th>
                  <th>Email</th>
                  <th>Status</th>
                  <th>Verified</th>
                  <th>Created</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($users as $user):
                  $initials = strtoupper(substr($user['Username'], 0, 1)) . (strlen($user['Username']) > 1 ? strtoupper(substr($user['Username'], 1, 1)) : '');
                ?>
                <tr>
                  <td>
                    <div class="ua-user-cell">
                      <div class="ua-user-avatar"><?php echo htmlspecialchars($initials); ?></div>
                      <div>
                        <div class="ua-user-name"><?php echo htmlspecialchars($user['Username']); ?></div>
                        <?php if (!empty($user['EmployeeCode'])): ?>
                        <div class="ua-user-id"><?php echo htmlspecialchars($user['EmployeeCode']); ?></div>
                        <?php endif; ?>
                      </div>
                    </div>
                  </td>
                  <td><?php echo htmlspecialchars($user['Email']); ?></td>
                  <td>
                    <span class="badge badge-<?php echo strtolower($user['AccountStatus']); ?>">
                      <?php echo htmlspecialchars($user['AccountStatus']); ?>
                    </span>
                  </td>
                  <td>
                    <span class="badge badge-<?php echo $user['IsVerified'] ? 'verified' : 'unverified'; ?>">
                      <?php echo $user['IsVerified'] ? 'Verified' : 'Unverified'; ?>
                    </span>
                  </td>
                  <td><?php echo date('M d, Y', strtotime($user['CreatedAt'] ?? 'now')); ?></td>
                  <td>
                    <div class="action-buttons">
                      <button class="btn btn-sm btn-edit" data-account-id="<?php echo $user['AccountID']; ?>" data-username="<?php echo htmlspecialchars($user['Username']); ?>">
                        <i data-lucide="edit-2"></i> Edit
                      </button>
                      <button class="btn btn-sm btn-delete" data-account-id="<?php echo $user['AccountID']; ?>" data-username="<?php echo htmlspecialchars($user['Username']); ?>">
                        <i data-lucide="trash-2"></i> Delete
                      </button>
                    </div>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
        <!-- Pagination -->
        <div class="ua-pagination" id="uaPagination"></div>
      </section>

      <!-- Add Account Modal -->
      <div id="addUserModal" class="modal" aria-hidden="true">
        <div class="modal-dialog">

          <!-- Gradient hero header -->
          <div class="modal-hero">
            <div class="modal-hero-inner">
              <div class="modal-hero-icon"><i data-lucide="user-plus"></i></div>
              <div class="modal-hero-text">
                <h3 id="modalTitle">Add New Account</h3>
                <p>Fill in the details below to create a user account.</p>
              </div>
              <button class="close-modal" id="closeModalBtn" title="Close">&times;</button>
            </div>
          </div>

          <div class="modal-body">
            <form id="createUserForm">
              <input type="hidden" id="accountId" name="account_id" value="">

              <div class="form-row" id="employeeLinkRow">
                <label for="employeeId">Link to Employee <span class="hint">(Optional)</span></label>
                <select id="employeeId" name="employee_id">
                  <option value="">-- No Employee Link --</option>
                  <?php foreach ($unlinkedEmployees as $emp): ?>
                    <option value="<?php echo $emp['EmployeeID']; ?>">
                      <?php echo htmlspecialchars($emp['LastName'] . ', ' . $emp['FirstName'] . ' (' . $emp['EmployeeCode'] . ')'); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <small class="hint">Select an employee to link this account to.</small>
              </div>

              <div class="form-row">
                <label for="username">Username <span class="required">*</span></label>
                <input id="username" name="username" type="text" placeholder="Enter username" required />
              </div>

              <div class="form-row">
                <label for="email">Email Address <span class="required">*</span></label>
                <input id="email" name="email" type="email" placeholder="Enter email address" required />
              </div>

              <div class="form-row">
                <label for="password">Password <span class="required">*</span></label>
                <div class="password-wrapper">
                  <input id="password" name="password" type="password" placeholder="Enter password" required />
                  <button type="button" class="btn-toggle-pwd" onclick="togglePassword('password')">
                    <i data-lucide="eye" class="eye-icon"></i>
                  </button>
                </div>
              </div>

              <div class="form-row">
                <label for="confirmPassword">Confirm Password <span class="required">*</span></label>
                <div class="password-wrapper">
                  <input id="confirmPassword" name="confirm_password" type="password" placeholder="Confirm password" required />
                  <button type="button" class="btn-toggle-pwd" onclick="togglePassword('confirmPassword')">
                    <i data-lucide="eye" class="eye-icon"></i>
                  </button>
                </div>
              </div>

              <div class="form-row">
                <label for="roles">Assign Roles <span class="required">*</span></label>
                <select id="roles" name="roles[]" multiple size="4" required>
                  <?php foreach ($roles as $role): ?>
                    <option value="<?php echo $role['RoleID']; ?>">
                      <?php echo htmlspecialchars($role['RoleName']); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <small class="hint">Hold Ctrl/Cmd to select multiple roles.</small>
              </div>

              <div class="form-row">
                <label for="accountStatus">Account Status <span class="required">*</span></label>
                <select id="accountStatus" name="account_status" required>
                  <option value="Active">Active</option>
                  <option value="Inactive">Inactive</option>
                </select>
              </div>
            </form>
          </div>

          <!-- Sticky footer -->
          <div class="form-actions">
            <button type="button" id="cancelCreate" class="btn-modal-cancel">Cancel</button>
            <button type="submit" form="createUserForm" class="btn-modal-submit">
              <i data-lucide="save"></i> <span id="submitBtnLabel">Create Account</span>
            </button>
          </div>

        </div>
      </div>

    </div>
  </main>
  <script src="../../js/admin_common.js"></script>
  <script src="../../js/useraccount.js"></script>
  <script>
    // Initialize icons safely
    if (window.lucide) {
      window.lucide.createIcons();
    }

    // Robust Fallback: Verify if openAddAccountModal exists, if not, define it
    if (typeof window.openAddAccountModal !== 'function') {
      window.openAddAccountModal = function() {
        console.log("Fallback modal opener triggered");
        const modal = document.getElementById("addUserModal");
        const form = document.getElementById("createUserForm");
        
        if (modal) {
          modal.style.display = "flex";
          modal.classList.add("show");
          modal.setAttribute("aria-hidden", "false");
          
          if (form) {
            form.reset();
            document.getElementById("accountId").value = "";
          }
          
          // Reset header and button text for 'Add' mode
          const title = document.getElementById("modalTitle");
          const lbl   = document.getElementById("submitBtnLabel");
          if (title) title.textContent = "Add New Account";
          if (lbl)   lbl.textContent   = "Create Account";
        } else {
          alert("Error: Modal element not found!");
        }
      };
    }

    // Attach explicit click listener to button as backup
    document.addEventListener('DOMContentLoaded', function() {
      const btn = document.getElementById("addUserBtn");
      if (btn) {
        btn.onclick = function(e) {
          e.preventDefault();
          if (window.openAddAccountModal) {
            window.openAddAccountModal();
          } else {
            console.error("openAddAccountModal is still not defined");
          }
        };
      }
    });

    // Table search + filter + pagination
    document.addEventListener('DOMContentLoaded', function() {
      const searchInput   = document.getElementById('tableSearch');
      const statusFilter  = document.getElementById('statusFilter');
      const verifiedFilter = document.getElementById('verifiedFilter');
      const tbody = document.querySelector('#usersTable tbody');
      const paginationEl = document.getElementById('uaPagination');
      const perPage = 10;
      let currentPage = 1;

      function getAllRows() {
        return Array.from(tbody.querySelectorAll('tr'));
      }

      function filterRows() {
        const q = (searchInput.value || '').toLowerCase();
        const st = (statusFilter.value || '').toLowerCase();
        const vf = (verifiedFilter.value || '').toLowerCase();

        return getAllRows().filter(row => {
          const text = row.textContent.toLowerCase();
          const matchQ  = !q  || text.includes(q);
          const matchSt = !st || text.includes(st);
          const matchVf = !vf || text.includes(vf);
          return matchQ && matchSt && matchVf;
        });
      }

      function renderPage() {
        const rows = filterRows();
        const totalPages = Math.max(1, Math.ceil(rows.length / perPage));
        if (currentPage > totalPages) currentPage = totalPages;

        getAllRows().forEach(r => r.style.display = 'none');
        rows.slice((currentPage - 1) * perPage, currentPage * perPage).forEach(r => r.style.display = '');

        // Pagination controls
        let html = `<span class="ua-page-info">${rows.length} result${rows.length !== 1 ? 's' : ''} &bull; Page ${currentPage} of ${totalPages}</span><div class="ua-page-btns">`;
        html += `<button class="ua-page-btn" onclick="changePage(-1)" ${currentPage === 1 ? 'disabled' : ''}><i data-lucide="chevron-left"></i></button>`;
        for (let i = 1; i <= totalPages; i++) {
          html += `<button class="ua-page-btn ${i === currentPage ? 'active' : ''}" onclick="goPage(${i})">${i}</button>`;
        }
        html += `<button class="ua-page-btn" onclick="changePage(1)" ${currentPage === totalPages ? 'disabled' : ''}><i data-lucide="chevron-right"></i></button></div>`;
        paginationEl.innerHTML = html;
        if (window.lucide) window.lucide.createIcons();
      }

      window.changePage = (dir) => { currentPage += dir; renderPage(); };
      window.goPage = (p) => { currentPage = p; renderPage(); };

      [searchInput, statusFilter, verifiedFilter].forEach(el => {
        if (el) el.addEventListener('input', () => { currentPage = 1; renderPage(); });
      });

      renderPage();
    });

    // Handle close buttons for fallback
    document.addEventListener('click', function(e) {
      if (e.target && (e.target.id === 'closeModalBtn' || e.target.id === 'cancelCreate' || e.target.classList.contains('close-modal'))) {
        const modal = document.getElementById("addUserModal");
        if (modal) {
          modal.style.display = "none";
          modal.classList.remove("show");
        }
      }
      // Click outside
      if (e.target && e.target.id === 'addUserModal') {
        e.target.style.display = "none";
        e.target.classList.remove("show");
      }
    });

    // Theme Toggle JavaScript
    const themeToggle = document.getElementById('themeToggle');
    const body = document.body;
    
    // Check for saved theme preference or default to light mode
    const currentTheme = localStorage.getItem('theme') || 'light';
    if (currentTheme === 'dark') {
      body.classList.add('dark-mode');
    }
    
    if (themeToggle) {
      themeToggle.addEventListener('click', () => {
        body.classList.toggle('dark-mode');
        const theme = body.classList.contains('dark-mode') ? 'dark' : 'light';
        localStorage.setItem('theme', theme);
      });
    }

    // Initialize Lucide icons
    if (typeof lucide !== 'undefined') {
      lucide.createIcons();
    }

    // Real-time Clock
    function updateClock() {
      const now = new Date();
      const options = { 
        weekday: 'short',
        year: 'numeric', 
        month: 'short', 
        day: 'numeric',
        hour: '2-digit', 
        minute: '2-digit',
        second: '2-digit',
        hour12: true 
      };
      const timeString = now.toLocaleString('en-US', options);
      const clockElement = document.getElementById('realTimeClock');
      if (clockElement) {
        clockElement.textContent = timeString;
      }
    }
    
    // Update clock immediately and then every second
    updateClock();
    setInterval(updateClock, 1000);

    // Sidebar Navigation Click Functionality
    document.addEventListener('DOMContentLoaded', function() {
      // Handle submenu toggles
      const submenuButtons = document.querySelectorAll('.nav-item.has-submenu');
      
      submenuButtons.forEach(button => {
        button.addEventListener('click', function(e) {
          e.preventDefault();
          e.stopPropagation();
          
          const module = this.getAttribute('data-module');
          const submenu = document.getElementById('submenu-' + module);
          const navItemGroup = this.closest('.nav-item-group');
          
          if (submenu) {
            // Toggle current submenu
            submenu.classList.toggle('active');
            navItemGroup.classList.toggle('active');
            
            // Close other submenus in the same section
            const parentSection = this.closest('.nav-section');
            if (parentSection) {
              const otherSubmenus = parentSection.querySelectorAll('.submenu.active');
              const otherNavGroups = parentSection.querySelectorAll('.nav-item-group.active');
              
              otherSubmenus.forEach(otherSubmenu => {
                if (otherSubmenu !== submenu) {
                  otherSubmenu.classList.remove('active');
                }
              });
              
              otherNavGroups.forEach(otherGroup => {
                if (otherGroup !== navItemGroup) {
                  otherGroup.classList.remove('active');
                }
              });
            }
          }
          
          // Reinitialize icons after DOM changes
          if (typeof lucide !== 'undefined') {
            setTimeout(() => lucide.createIcons(), 100);
          }
        });
      });
      
      // Handle sidebar toggle
      const sidebarToggle = document.getElementById('sidebarToggle');
      const sidebar = document.getElementById('sidebar');
      
      if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
          sidebar.classList.toggle('collapsed');
          
          // Reinitialize icons after toggle
          if (typeof lucide !== 'undefined') {
            setTimeout(() => lucide.createIcons(), 100);
          }
        });
      }
    });
  </script>
</body>
</html>







