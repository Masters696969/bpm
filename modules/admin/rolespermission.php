<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['user_role']) !== 'administrator') {
    header('Location: ../../login.php');
    exit;
}

require_once '../../config/config.php';

$page = 'rolespermission';
$module = 'accounts';

// Fetch all roles from database
$rolesSql = "SELECT * FROM roles ORDER BY RoleID ASC";
$rolesResult = $conn->query($rolesSql);
$roles = [];
if ($rolesResult) {
    while ($row = $rolesResult->fetch_assoc()) {
        $roles[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Roles & Permissions - Microfinance</title>
  <!-- Base styles from useraccount.css for layout consistency -->
  <link rel="stylesheet" href="../../css/useraccount.css?v=1.4"> 
  <!-- Specific styles for this page -->
  <link rel="stylesheet" href="../../css/rolespermission.css?v=1.0">
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
            <a href="useraccount.php" class="submenu-item <?php echo ($page === 'useraccount') ? 'active' : ''; ?>">
              <i data-lucide="user-plus"></i>
              <span>User Accounts</span>
            </a>
            <a href="rolespermission.php" class="submenu-item <?php echo ($page === 'rolespermission') ? 'active' : ''; ?>">
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
            <button class="nav-item has-submenu <?php echo ($module === 'competency') ? 'active' : ''; ?>" data-module="competency">
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
            <button class="nav-item has-submenu <?php echo ($module === 'training') ? 'active' : ''; ?>" data-module="training">
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
            <button class="nav-item has-submenu <?php echo ($module === 'succession') ? 'active' : ''; ?>" data-module="succession">
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
            <button class="nav-item has-submenu" data-module="shift">
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
            <button class="nav-item has-submenu" data-module="claims">
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
            <button class="nav-item has-submenu" data-module="time">
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
            <button class="nav-item has-submenu" data-module="timesheet">
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
            <button class="nav-item has-submenu" data-module="leave">
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
          <button class="nav-item has-submenu" data-module="corehumancapital">
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
          <button class="nav-item has-submenu" data-module="planning">
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
        <div class="nav-item-group">
          <button class="nav-item has-submenu" data-module="payroll">
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
            <a href="payroll.php" class="submenu-item active">
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
          <button class="nav-item has-submenu" data-module="budget">
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
          <h1>Roles & Permissions</h1>
          <p>Manage user roles and access rights.</p>
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
      <?php $totalRoles = count($roles); ?>
      <div class="rp-stats">
        <div class="rp-stat-card">
          <div class="rp-stat-icon indigo"><i data-lucide="shield"></i></div>
          <div class="rp-stat-info">
            <span class="rp-stat-value"><?php echo $totalRoles; ?></span>
            <span class="rp-stat-label">Total Roles</span>
          </div>
        </div>
        <div class="rp-stat-card">
          <div class="rp-stat-icon violet"><i data-lucide="shield-check"></i></div>
          <div class="rp-stat-info">
            <span class="rp-stat-value"><?php echo $totalRoles; ?></span>
            <span class="rp-stat-label">Active Roles</span>
          </div>
        </div>
        <div class="rp-stat-card">
          <div class="rp-stat-icon green"><i data-lucide="key-round"></i></div>
          <div class="rp-stat-info">
            <span class="rp-stat-value">—</span>
            <span class="rp-stat-label">Permissions</span>
          </div>
        </div>
      </div>

      <!-- Roles Table Card -->
      <section class="rp-panel">
        <div class="rp-panel-header">
          <div class="rp-panel-left">
            <div class="rp-panel-icon"><i data-lucide="contact-round"></i></div>
            <div class="rp-panel-titles">
              <h2>Defined Roles</h2>
              <div class="rp-panel-sub"><?php echo $totalRoles; ?> role<?php echo $totalRoles !== 1 ? 's' : ''; ?> configured</div>
            </div>
          </div>
          <div class="rp-panel-actions">
            <div class="rp-panel-search">
              <i data-lucide="search"></i>
              <input type="search" id="roleSearch" placeholder="Search roles…">
            </div>
            <select id="roleTypeFilter" class="ua-filter-select">
              <option value="">All Roles</option>
              <?php foreach ($roles as $r): ?>
                <option value="<?php echo strtolower(htmlspecialchars($r['RoleName'])); ?>"><?php echo htmlspecialchars($r['RoleName']); ?></option>
              <?php endforeach; ?>
            </select>
            <button id="addRoleBtn" class="btn btn-primary">
              <i data-lucide="plus"></i> Add Role
            </button>
          </div>
        </div>

        <div class="panel-body">
          <div class="table-responsive">
            <table id="rolesTable" class="users-table">
              <thead>
                <tr>
                  <th>Role</th>
                  <th>Description</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($roles)): ?>
                <tr><td colspan="3" style="text-align:center;padding:32px;color:var(--text-tertiary);">No roles found.</td></tr>
                <?php else: ?>
                  <?php foreach ($roles as $role):
                    $initials = strtoupper(substr($role['RoleName'], 0, 2));
                  ?>
                  <tr>
                    <td>
                      <div class="rp-role-cell">
                        <div class="rp-role-avatar"><?php echo htmlspecialchars($initials); ?></div>
                        <div>
                          <div class="rp-role-name"><?php echo htmlspecialchars($role['RoleName']); ?></div>
                          <div class="rp-role-id">#<?php echo $role['RoleID']; ?></div>
                        </div>
                      </div>
                    </td>
                    <td><span class="rp-desc"><?php echo htmlspecialchars($role['Description'] ?? 'No description'); ?></span></td>
                    <td>
                      <div class="action-buttons">
                        <button class="btn btn-sm btn-edit" data-role-id="<?php echo $role['RoleID']; ?>" onclick="editRole(<?php echo $role['RoleID']; ?>, '<?php echo htmlspecialchars($role['RoleName']); ?>')">
                          <i data-lucide="edit-2"></i> Edit
                        </button>
                        <button class="btn btn-sm btn-delete" data-role-id="<?php echo $role['RoleID']; ?>" onclick="archiveRole(<?php echo $role['RoleID']; ?>)">
                          <i data-lucide="archive"></i> Archive
                        </button>
                        <button class="btn-permission">
                          <i data-lucide="shield-check"></i> Permission
                        </button>
                      </div>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
        <!-- Pagination -->
        <div class="ua-pagination" id="rpPagination"></div>
      </section>

      <!-- Add / Edit Role Modal -->
      <div id="roleModal" class="modal" aria-hidden="true">
        <div class="modal-dialog">

          <!-- Gradient hero -->
          <div class="rp-modal-hero">
            <div class="rp-modal-hero-inner">
              <div class="rp-modal-hero-icon"><i data-lucide="shield-plus"></i></div>
              <div class="rp-modal-hero-text">
                <h3 id="modalTitle">Add New Role</h3>
                <p>Define a role and its description below.</p>
              </div>
              <button class="rp-close-modal" id="closeModalBtn" title="Close">&times;</button>
            </div>
          </div>

          <div class="modal-body">
            <form id="roleForm">
              <input type="hidden" id="roleId" name="role_id" value="">

              <div class="form-row">
                <label for="roleName">Role Name <span class="required">*</span></label>
                <input id="roleName" name="role_name" type="text" placeholder="e.g. HR Manager" required />
              </div>

              <div class="form-row">
                <label for="roleDescription">Description</label>
                <textarea id="roleDescription" name="description" rows="3" placeholder="Describe what this role can do…"></textarea>
              </div>
            </form>
          </div>

          <!-- Sticky footer -->
          <div class="form-actions">
            <button type="button" id="cancelRole" class="btn-modal-cancel">Cancel</button>
            <button type="submit" form="roleForm" class="btn-rp-submit" id="modalSubmitBtn">
              <i data-lucide="save"></i> Save Role
            </button>
          </div>

        </div>
      </div>

    </div>
  </main>
  <script src="../../js/rolespermission.js?v=<?php echo time(); ?>"></script>
  <script>
    if (window.lucide) window.lucide.createIcons();

    // Inline search + filter + pagination
    document.addEventListener('DOMContentLoaded', function() {
      const searchInput  = document.getElementById('roleSearch');
      const typeFilter   = document.getElementById('roleTypeFilter');
      const tbody = document.querySelector('#rolesTable tbody');
      const paginationEl = document.getElementById('rpPagination');
      const perPage = 10;
      let currentPage = 1;

      function getAllRows() {
        return Array.from(tbody.querySelectorAll('tr'));
      }

      function filterRows() {
        const q  = (searchInput.value || '').toLowerCase();
        const tf = (typeFilter.value || '').toLowerCase();
        return getAllRows().filter(row => {
          const text = row.textContent.toLowerCase();
          return (!q || text.includes(q)) && (!tf || text.includes(tf));
        });
      }

      function renderPage() {
        const rows = filterRows();
        const totalPages = Math.max(1, Math.ceil(rows.length / perPage));
        if (currentPage > totalPages) currentPage = totalPages;

        getAllRows().forEach(r => r.style.display = 'none');
        rows.slice((currentPage - 1) * perPage, currentPage * perPage).forEach(r => r.style.display = '');

        let html = `<span class="ua-page-info">${rows.length} result${rows.length !== 1 ? 's' : ''} &bull; Page ${currentPage} of ${totalPages}</span><div class="ua-page-btns">`;
        html += `<button class="ua-page-btn" onclick="rpChangePage(-1)" ${currentPage === 1 ? 'disabled' : ''}><i data-lucide="chevron-left"></i></button>`;
        for (let i = 1; i <= totalPages; i++) {
          html += `<button class="ua-page-btn ${i === currentPage ? 'active' : ''}" onclick="rpGoPage(${i})">${i}</button>`;
        }
        html += `<button class="ua-page-btn" onclick="rpChangePage(1)" ${currentPage === totalPages ? 'disabled' : ''}><i data-lucide="chevron-right"></i></button></div>`;
        paginationEl.innerHTML = html;
        if (window.lucide) window.lucide.createIcons();
      }

      window.rpChangePage = (dir) => { currentPage += dir; renderPage(); };
      window.rpGoPage = (p) => { currentPage = p; renderPage(); };

      [searchInput, typeFilter].forEach(el => {
        if (el) el.addEventListener('input', () => { currentPage = 1; renderPage(); });
      });

      renderPage();
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

    // Initialize Lucide icons
    if (typeof lucide !== 'undefined') {
      lucide.createIcons();
    }
  </script>
</body>
</html>







