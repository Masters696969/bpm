<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../../css/admin_payroll.css?v=1.3">
  <script src="https://unpkg.com/lucide@latest"></script>
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
        <a href="dashboard.php" class="nav-item active">
          <i data-lucide="layout-dashboard"></i>
          <span>HR ANALYTICS</span>
        </a>
      <div class="nav-section">
        <span class="nav-section-title">HUMAN RESOURCES I</span>
        <div class="nav-item-group active">
          <button class="nav-item has-submenu" data-module="recruitment">
            <div class="nav-item-content">
              <i data-lucide="layers-plus"></i>
              <span>Recruitment</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-recruitment">
           <a href="recruitment.php" class="nav-item <?php echo($page === 'recruitment') ? 'active' : ''; ?>">
              <i data-lucide="layers-plus"></i>
              <span>Recruitment</span>
            </a>
          </div>
          <div class="nav-item-group active">
          <button class="nav-item has-submenu" data-module="applicationmgt">
            <div class="nav-item-content">
              <i data-lucide="contact-round"></i>
              <span>Applicant Management</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-applicationmgt">
            <a href="applicationmgt.php" class="nav-item <?php echo($page === 'applicationmgt') ? 'active' : ''; ?>">
              <i data-lucide="contact-round"></i>
              <span>Applicant Management</span>
            </a>
          </div>
          <div class="nav-item-group active">
          <button class="nav-item has-submenu" data-module="newhiredonboard">
            <div class="nav-item-content">
              <i data-lucide="user-plus"></i>
              <span>New Hired Onboard</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-newhiredonboard">
            <a href="newhiredonboard.php" class="nav-item <?php echo($page === 'newhiredonboard') ? 'active' : ''; ?>">
              <i data-lucide="user-plus"></i>
              <span>New Hired Onboard</span>
            </a>
          </div>
        </div>
        <div class="nav-section">
        <span class="nav-section-title">HUMAN RESOURCES II</span>
        <div class="nav-item-group active">
          <button class="nav-item has-submenu" data-module="accounts">
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
           <div class="nav-item-group <?php echo($module === 'competency') ? 'active' : ''; ?>">
            <button class="nav-item has-submenu" data-module="competency">
              <div class="nav-item-content">
                <i data-lucide="pickaxe"></i>
                <span>Competency Management</span>
              </div>
              <i data-lucide="chevron-down" class="submenu-icon"></i>
            </button>
            <div class="submenu" id="submenu-competency">
              <a href="competencylibrary.php" class="submenu-item <?php echo($page === 'competency') ? 'active' : ''; ?>">
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
              <a href="competencyemployee.php" class="submenu-item <?php echo($page === 'competencyemployee') ? 'active' : ''; ?>">
                <i data-lucide="square-user"></i>
                <span>Competency Employee</span>
              </a>
                <a href="bankquestion.php" class="submenu-item <?php echo($page === 'bankquestion') ? 'active' : ''; ?>">
                <i data-lucide="book-open-check"></i>
                <span>Bank Question</span>
              </a>
            </div>
        </div>
         <div class="nav-item-group <?php echo($module === 'training') ? 'active' : ''; ?>">
            <button class="nav-item has-submenu" data-module="training">
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
            <button class="nav-item has-submenu" data-module="succession">
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
            <button class="nav-item has-submenu" data-module="learning">
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
          <div class="nav-section">   
            <span class="nav-section-title">HUMAN RESOURCES III</span>
          <div class="nav-item-group <?php echo($module === 'shift') ? 'active' : ''; ?>">
            <button class="nav-item has-submenu" data-module="shift">
              <div class="nav-item-content">
                <i data-lucide="calendar-check"></i>
                <span>Shift & Scheduling</span>
              </div>
              <i data-lucide="chevron-down" class="submenu-icon"></i>
            </button>
            <div class="submenu" id="submenu-shift">
              <a href="admin_roster.php" class="submenu-item <?php echo($page === 'shift') ? 'active' : ''; ?>">
                <i data-lucide="send-to-back"></i>
                <span>Shift & Scheduling</span>
              </a>
            </div>
            <div class="nav-item-group <?php echo($module === 'claims') ? 'active' : ''; ?>">
            <button class="nav-item has-submenu" data-module="claims">
              <div class="nav-item-content">
                <i data-lucide="receipt-text"></i>
                <span>Claims & Reimbursements</span>
              </div>
              <i data-lucide="chevron-down" class="submenu-icon"></i>
            </button>
            <div class="submenu" id="submenu-claims">
              <a href="claims.php" class="submenu-item <?php echo($page === 'claims') ? 'active' : ''; ?>">
                <i data-lucide="receipt-text"></i>
                <span>Claims & Reimbursements</span>
              </a>
            </div>
            <div class="nav-item-group <?php echo($module === 'time') ? 'active' : ''; ?>">
            <button class="nav-item has-submenu" data-module="time">
              <div class="nav-item-content">
                <i data-lucide="clock"></i>
                <span>Time & Attendance</span>
              </div>
              <i data-lucide="chevron-down" class="submenu-icon"></i>
            </button>
            <div class="submenu" id="submenu-time">
              <a href="time.php" class="submenu-item <?php echo($page === 'time') ? 'active' : ''; ?>">
                <i data-lucide="clock"></i>
                <span>Time & Attendance</span>
              </a>
            </div>
            <div class="nav-item-group <?php echo($module === 'timesheet') ? 'active' : ''; ?>">
            <button class="nav-item has-submenu" data-module="timesheet">
              <div class="nav-item-content">
                <i data-lucide="calendar-days"></i>
                <span>Timesheet</span>
              </div>
              <i data-lucide="chevron-down" class="submenu-icon"></i>
            </button>
            <div class="submenu" id="submenu-timesheet">
              <a href="timesheet.php" class="submenu-item <?php echo($page === 'timesheet') ? 'active' : ''; ?>">
                <i data-lucide="calendar-days"></i>
                <span>Timesheet</span>
              </a>
            </div>
             <div class="nav-item-group <?php echo($module === 'leave') ? 'active' : ''; ?>">
            <button class="nav-item has-submenu" data-module="leave">
              <div class="nav-item-content">
                <i data-lucide="tickets-plane"></i>
                <span>Leave Management</span>
              </div>
              <i data-lucide="chevron-down" class="submenu-icon"></i>
            </button>
            <div class="submenu" id="submenu-leave">
              <a href="leave.php" class="submenu-item <?php echo($page === 'leave') ? 'active' : ''; ?>">
                <i data-lucide="tickets-plane"></i>
                <span>Leave Management</span>
              </a>
            </div>
       <div class="nav-section">
        <span class="nav-section-title">HUMAN RESOURCES IV</span>
          <div class="nav-item-group <?php echo($module === 'corehumancapital') ? 'active' : ''; ?>">
          <button class="nav-item has-submenu" data-module="corehumancapital">
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
          <button class="nav-item has-submenu" data-module="planning">
            <div class="nav-item-content">
              <i data-lucide="circle-pile"></i>
              <span>Compensation Planning</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-planning">
           <a href="comintake.php" class="submenu-item <?php echo($page === 'intake') ? 'active' : ''; ?>">
              <i data-lucide="layout-dashboard"></i>
              <span>Master Data Intake</span>
            </a>
            <a href="salary.php" class="submenu-item <?php echo($page === 'salarymgt') ? 'active' : ''; ?>">
              <i data-lucide="banknote"></i>
              <span>Salary & Scales Management</span>
            </a>
            <a href="statutory.php" class="submenu-item <?php echo($page === 'statutory') ? 'active' : ''; ?>">
              <i data-lucide="scale"></i>
              <span>Statutory Contributions</span>
            </a>
            <a href="matrix.php" class="submenu-item <?php echo($page === 'matrix') ? 'active' : ''; ?>">
              <i data-lucide="percent"></i>
              <span>Merit Matrix Structure</span>
            </a>
            <a href="allowance.php" class="submenu-item <?php echo($page === 'allowance') ? 'active' : ''; ?>">
              <i data-lucide="gift"></i>
              <span>Allowance Structure</span>
            </a>
            <a href="cycle.php" class="submenu-item <?php echo($page === 'cycle') ? 'active' : ''; ?>">
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
        
        <div class="nav-item-group <?php echo($module === 'budget') ? 'active' : ''; ?>">
          <button class="nav-item has-submenu" data-module="budget">
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
          <h1>Payroll Processing</h1>
          <p>Initialize and manage semi-monthly payroll cycles.</p>
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
      <!-- Stats Grid -->
      <div class="stats-grid">
        <div class="stat-card-premium">
          <div class="stat-icon-wrapper" style="background: rgba(44, 160, 120, 0.1); color: var(--brand-green);">
            <i data-lucide="credit-card"></i>
          </div>
          <div class="stat-info">
            <span class="stat-label" style="display:flex; justify-content:space-between; align-items:center;">
              Total Payroll
              <button id="togglePayrollBtn" style="background:transparent; border:none; color:var(--text-tertiary); cursor:pointer; padding:0; height:16px;">
                <i data-lucide="eye-off" style="width:16px; height:16px;"></i>
              </button>
            </span>
            <h3 class="stat-value" id="statTotalPayroll">&#8369;**,***.**</h3>
          </div>
        </div>

        <div class="stat-card-premium">
          <div class="stat-icon-wrapper" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">
            <i data-lucide="users"></i>
          </div>
          <div class="stat-info">
            <span class="stat-label">Employees</span>
            <h3 class="stat-value" id="statEmployees">0 Paid</h3>
          </div>
        </div>

        <div class="stat-card-premium">
          <div class="stat-icon-wrapper" style="background: rgba(255, 193, 7, 0.1); color: var(--brand-yellow);">
            <i data-lucide="clock"></i>
          </div>
          <div class="stat-info">
            <span class="stat-label">Next Run</span>
            <h3 class="stat-value" id="statNextRun">--</h3>
          </div>
        </div>

        <div class="stat-card-premium">
          <div class="stat-icon-wrapper" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
            <i data-lucide="alert-circle"></i>
          </div>
          <div class="stat-info">
            <span class="stat-label">Pending</span>
            <h3 class="stat-value" id="statPending">0 Batches</h3>
          </div>
        </div>

        <div class="stat-card-premium finance-sync-card" id="payrollBudgetCard" style="grid-column: span 1; display: none;">
          <div class="stat-info">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
              <span class="stat-label">Disbursement Budget</span>
              <div id="payrollBudgetStatus" class="sync-status-tag">
                <i data-lucide="refresh-cw" class="spin"></i> Syncing
              </div>
            </div>
            <div style="display: flex; align-items: baseline; gap: 8px;">
              <h3 class="stat-value" id="statDisbursementBudget">&#8369;0.00</h3>
              <span id="payrollBudgetRef" style="font-size: 10px; opacity: 0.6;"></span>
            </div>
            <button class="btn-premium" id="requestPayrollBudgetBtn" style="width: 100%; margin-top: 12px; height: 32px; font-size: 12px; background: var(--brand-green); color: white; display: flex; align-items: center; justify-content: center; gap: 6px;">
              <i data-lucide="send" style="width: 14px;"></i> Request Finance
            </button>
          </div>
        </div>
      </div>

      <!-- Control Header -->
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div class="premium-tabs">
          <button class="tab-btn active" data-tab="batches">
            <i data-lucide="layer-group" style="width: 18px;"></i>
            Payroll Batches
          </button>
          <button class="tab-btn" data-tab="employees">
            <i data-lucide="user-check" style="width: 18px;"></i>
            Employee Payroll
          </button>
          <button class="tab-btn" data-tab="settings">
            <i data-lucide="settings-2" style="width: 18px;"></i>
            Statutory Settings
          </button>
        </div>

        <button class="btn-premium btn-primary-premium" id="runPayrollBtn">
          <i data-lucide="play-circle"></i>
          Initialize New Batch
        </button>
      </div>

      <!-- Tab Content -->
      <div class="tab-panel active" id="batches">
        <div class="payroll-table-container">
          <table class="payroll-table">
            <thead>
              <tr>
                <th>Batch ID</th>
                <th>Period</th>
                <th>Type</th>
                <th>Total Distributed</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody id="payrollBatchesBody"></tbody>
          </table>
        </div>
      </div>

      <div class="tab-panel" id="employees">
        <div class="payroll-table-container">
          <table class="payroll-table">
            <thead>
              <tr>
                <th>Employee</th>
                <th>Basic Pay</th>
                <th>Allowances</th>
                <th>OT Pay</th>
                <th>SSS Regular</th>
                <th>SSS WISP</th>
                <th>PhilHealth</th>
                <th>Pag-IBIG</th>
                <th>Late/UT</th>
                <th>W.Tax</th>
                <th>Deductions</th>
                <th>Net Pay</th>
                <th>Status</th>
                <th>Payslip</th>
              </tr>
            </thead>
            <tbody id="payrollEmployeesBody"></tbody>
          </table>
        </div>
      </div>

      <div class="tab-panel" id="settings">
        <!-- Statutory Settings Master Grid -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px;">
           <div class="content-card" style="padding: 24px; background: var(--surface); border: 1px solid var(--border-color); border-radius: 20px; transition: var(--transition);">
              <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                 <div class="stat-icon-wrapper" style="background: rgba(44, 160, 120, 0.1); color: var(--brand-green); width: 48px; height: 48px;">
                    <i data-lucide="landmark"></i>
                 </div>
                 <span class="badge-premium badge-success" style="font-size: 10px;">Updated</span>
              </div>
              <h4 style="margin-bottom: 8px;">SSS Contributions</h4>
              <p style="font-size: 13px; color: var(--text-secondary); margin-bottom: 20px; min-height: 40px;">Manage MSC capping, ER/EE split rates, and WISP+ voluntary plans.</p>
              <div style="display: flex; gap: 8px;">
                 <button class="btn-premium" style="background: var(--surface-hover); border: 1px solid var(--border-color); flex: 1; font-size: 12px;">Rates</button>
                 <button class="btn-premium" style="background: var(--brand-green); color: white; flex: 1; font-size: 12px;">Table</button>
              </div>
           </div>

           <div class="content-card" style="padding: 24px; background: var(--surface); border: 1px solid var(--border-color); border-radius: 20px;">
              <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                 <div class="stat-icon-wrapper" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6; width: 48px; height: 48px;">
                    <i data-lucide="heart-pulse"></i>
                 </div>
                 <span class="badge-premium badge-success" style="font-size: 10px;">Fixed</span>
              </div>
              <h4 style="margin-bottom: 8px;">PhilHealth (NHIP)</h4>
              <p style="font-size: 13px; color: var(--text-secondary); margin-bottom: 20px; min-height: 40px;">Standard 5% premium rate configuration with &#8369;10k-&#8369;100k salary floor/ceiling.</p>
              <button class="btn-premium" style="background: var(--surface-hover); border: 1px solid var(--border-color); width: 100%; font-size: 12px;">Configure Premiums</button>
           </div>

           <div class="content-card" style="padding: 24px; background: var(--surface); border: 1px solid var(--border-color); border-radius: 20px;">
              <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                 <div class="stat-icon-wrapper" style="background: rgba(255, 193, 7, 0.1); color: var(--brand-yellow); width: 48px; height: 48px;">
                    <i data-lucide="home"></i>
                 </div>
              </div>
              <h4 style="margin-bottom: 8px;">Pag-IBIG (HDMF)</h4>
              <p style="font-size: 13px; color: var(--text-secondary); margin-bottom: 20px; min-height: 40px;">2% EE/ER contribution rates with &#8369;100.00 capping for mandatory savings.</p>
              <button class="btn-premium" style="background: var(--surface-hover); border: 1px solid var(--border-color); width: 100%; font-size: 12px;">Manage Savings</button>
           </div>

           <div class="content-card" style="padding: 24px; background: var(--surface); border: 1px solid var(--border-color); border-radius: 20px;">
              <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                 <div class="stat-icon-wrapper" style="background: rgba(239, 68, 68, 0.1); color: #ef4444; width: 48px; height: 48px;">
                    <i data-lucide="file-key"></i>
                 </div>
                 <span class="badge-premium badge-warning" style="font-size: 10px;">Review</span>
              </div>
              <h4 style="margin-bottom: 8px;">Withholding Tax</h4>
              <p style="font-size: 13px; color: var(--text-secondary); margin-bottom: 20px; min-height: 40px;">BIR TRAIN Law 2026 tax table integration for automated net pay calculation.</p>
              <button class="btn-premium" style="background: var(--surface-hover); border: 1px solid var(--border-color); width: 100%; font-size: 12px;">Tax Schedule</button>
           </div>
        </div>
      </div>
  </main>
  <script src="../../js/admin_payroll.js"></script>
  <script>
    lucide.createIcons();
  </script>
</body>
</html>







