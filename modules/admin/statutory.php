<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../../login.php");
    exit();
}

require_once '../../config/config.php';

// Fetch Active Compensation Period (Assume ID 1 for now)
$period_id = 1;

// Fetch Statutory Settings
$sss_query = $conn->query("SELECT * FROM sss_settings WHERE period_id = $period_id");
$sss_data = $sss_query->fetch_assoc();

$ph_query = $conn->query("SELECT * FROM philhealth_settings WHERE period_id = $period_id");
$ph_data = $ph_query->fetch_assoc();

$pi_query = $conn->query("SELECT * FROM pagibig_settings WHERE period_id = $period_id");
$pi_data = $pi_query->fetch_assoc();

$bir_query = $conn->query("SELECT * FROM bir_tax_settings WHERE period_id = $period_id");
$bir_data = $bir_query->fetch_assoc();

$module = 'planning';
$page = 'statutory';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Statutory Contributions</title>
  <link rel="stylesheet" href="../../css/admin_statutory.css?v=1.4">
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
           <a href="recruitment.php" class="nav-item <?php echo ($page === 'recruitment') ? 'active' : ''; ?>">
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
            <a href="applicationmgt.php" class="nav-item <?php echo ($page === 'applicationmgt') ? 'active' : ''; ?>">
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
            <a href="newhiredonboard.php" class="nav-item <?php echo ($page === 'newhiredonboard') ? 'active' : ''; ?>">
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
            <button class="nav-item has-submenu" data-module="learning">
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
          <h1>Dashboard Overview</h1>
          <p>Welcome back, <?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?>! Here's what's happening today.</p>
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
      <div class="section-header" style="margin-bottom: 24px;">
        <div class="sh-info">
          <h3>Statutory Compliance Settings</h3>
          <p>Configure government-mandated contribution rates and tax thresholds for the current period.</p>
        </div>
        <div class="comp-panel-actions" style="display: flex; gap: 12px;">
        </div>
      </div>
      <div class="comp-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px;">
        <!-- SSS Configuration -->
        <div class="stat-group-card" style="background: var(--surface); border: 1px solid var(--border-color); border-radius: 12px; padding: 20px;">
          <div class="sg-header" style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 16px;">
            <i data-lucide="shield-check" style="color:#2ca078"></i>
            <h4 style="margin: 0; font-size: 16px; color: var(--text-primary);">SSS Contribution</h4>
          </div>
          <div class="sg-body">
            <p class="sg-desc" style="color: var(--text-secondary); font-size: 13px; margin-bottom: 16px;">Manage Social Security rates and WISP mandatory provident fund thresholds.</p>
            <div class="editable-form" style="display: flex; flex-direction: column; gap: 12px;">
              <div class="form-group-inline" style="display: flex; justify-content: space-between; align-items: center;">
                <label style="font-size: 13px; color: var(--text-secondary);">Employee Share (%)</label>
                <input type="number" step="0.1" value="<?php echo number_format($sss_data['employee_share_pct'] ?? 5.0, 1); ?>" class="stat-input" style="width: 120px; padding: 6px 10px; border: 1px solid var(--border-color); border-radius: 6px; text-align: right; background: var(--background); color: var(--text-primary);">
              </div>
              <div class="form-group-inline" style="display: flex; justify-content: space-between; align-items: center;">
                <label style="font-size: 13px; color: var(--text-secondary);">Employer Share (%)</label>
                <input type="number" step="0.1" value="<?php echo number_format($sss_data['employer_share_pct'] ?? 10.0, 1); ?>" class="stat-input" style="width: 120px; padding: 6px 10px; border: 1px solid var(--border-color); border-radius: 6px; text-align: right; background: var(--background); color: var(--text-primary);">
              </div>
              <div class="form-group-inline" style="display: flex; justify-content: space-between; align-items: center;">
                <label style="font-size: 13px; color: var(--text-secondary);">Max MSC (Monthly)</label>
                <div class="inline-input-symbol" style="position: relative; width: 120px;">
                  <span style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--text-tertiary); font-size: 13px;">&#8369;</span>
                  <input type="number" value="<?php echo (int)($sss_data['max_msc_monthly'] ?? 30000); ?>" class="stat-input" style="width: 100%; padding: 6px 10px 6px 24px; border: 1px solid var(--border-color); border-radius: 6px; text-align: right; background: var(--background); color: var(--text-primary);">
                </div>
              </div>
              <div class="form-group-inline" style="display: flex; justify-content: space-between; align-items: center;">
                <label style="font-size: 13px; color: var(--text-secondary);">WISP Threshold</label>
                <div class="inline-input-symbol" style="position: relative; width: 120px;">
                  <span style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--text-tertiary); font-size: 13px;">&#8369;</span>
                  <input type="number" value="<?php echo (int)($sss_data['wisp_threshold'] ?? 20000); ?>" class="stat-input" style="width: 100%; padding: 6px 10px 6px 24px; border: 1px solid var(--border-color); border-radius: 6px; text-align: right; background: var(--background); color: var(--text-primary);">
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- PhilHealth Configuration -->
        <div class="stat-group-card" style="background: var(--surface); border: 1px solid var(--border-color); border-radius: 12px; padding: 20px;">
          <div class="sg-header" style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 16px;">
            <i data-lucide="heart" style="color:#ef4444"></i>
            <h4 style="margin: 0; font-size: 16px; color: var(--text-primary);">PhilHealth Premium</h4>
          </div>
          <div class="sg-body">
            <p class="sg-desc" style="color: var(--text-secondary); font-size: 13px; margin-bottom: 16px;">Current premium rate is 5.0% split equally between EE and ER.</p>
            <div class="editable-form" style="display: flex; flex-direction: column; gap: 12px;">
              <div class="form-group-inline" style="display: flex; justify-content: space-between; align-items: center;">
                <label style="font-size: 13px; color: var(--text-secondary);">Employee Share (%)</label>
                <input type="number" step="0.01" value="<?php echo number_format($ph_data['employee_share_pct'] ?? 2.50, 2); ?>" class="stat-input" style="width: 120px; padding: 6px 10px; border: 1px solid var(--border-color); border-radius: 6px; text-align: right; background: var(--background); color: var(--text-primary);">
              </div>
              <div class="form-group-inline" style="display: flex; justify-content: space-between; align-items: center;">
                <label style="font-size: 13px; color: var(--text-secondary);">Employer Share (%)</label>
                <input type="number" step="0.01" value="<?php echo number_format($ph_data['employer_share_pct'] ?? 2.50, 2); ?>" class="stat-input" style="width: 120px; padding: 6px 10px; border: 1px solid var(--border-color); border-radius: 6px; text-align: right; background: var(--background); color: var(--text-primary);">
              </div>
              <div class="form-group-inline" style="display: flex; justify-content: space-between; align-items: center;">
                <label style="font-size: 13px; color: var(--text-secondary);">Salary Ceiling</label>
                <div class="inline-input-symbol" style="position: relative; width: 120px;">
                  <span style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--text-tertiary); font-size: 13px;">&#8369;</span>
                  <input type="number" value="<?php echo (int)($ph_data['salary_ceiling'] ?? 100000); ?>" class="stat-input" style="width: 100%; padding: 6px 10px 6px 24px; border: 1px solid var(--border-color); border-radius: 6px; text-align: right; background: var(--background); color: var(--text-primary);">
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Pag-IBIG Configuration -->
        <div class="stat-group-card" style="background: var(--surface); border: 1px solid var(--border-color); border-radius: 12px; padding: 20px;">
          <div class="sg-header" style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 16px;">
            <i data-lucide="home" style="color:#ffc107"></i>
            <h4 style="margin: 0; font-size: 16px; color: var(--text-primary);">Pag-IBIG (HDMF)</h4>
          </div>
          <div class="sg-body">
            <p class="sg-desc" style="color: var(--text-secondary); font-size: 13px; margin-bottom: 16px;">Contribution based on percentage or fixed amount caps.</p>
            <div class="editable-form" style="display: flex; flex-direction: column; gap: 12px;">
              <div class="form-group-inline" style="display: flex; justify-content: space-between; align-items: center;">
                <label style="font-size: 13px; color: var(--text-secondary);">Employee Rate (%)</label>
                <input type="number" step="0.1" value="<?php echo number_format($pi_data['employee_rate_pct'] ?? 2.0, 1); ?>" class="stat-input" style="width: 120px; padding: 6px 10px; border: 1px solid var(--border-color); border-radius: 6px; text-align: right; background: var(--background); color: var(--text-primary);">
              </div>
              <div class="form-group-inline" style="display: flex; justify-content: space-between; align-items: center;">
                <label style="font-size: 13px; color: var(--text-secondary);">Monthly Cap (EE)</label>
                <div class="inline-input-symbol" style="position: relative; width: 120px;">
                  <span style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--text-tertiary); font-size: 13px;">&#8369;</span>
                  <input type="number" value="<?php echo (int)($pi_data['monthly_cap_ee'] ?? 200); ?>" class="stat-input" style="width: 100%; padding: 6px 10px 6px 24px; border: 1px solid var(--border-color); border-radius: 6px; text-align: right; background: var(--background); color: var(--text-primary);">
                </div>
              </div>
              <div class="form-group-inline" style="display: flex; justify-content: space-between; align-items: center;">
                <label style="font-size: 13px; color: var(--text-secondary);">Monthly Cap (ER)</label>
                <div class="inline-input-symbol" style="position: relative; width: 120px;">
                  <span style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--text-tertiary); font-size: 13px;">&#8369;</span>
                  <input type="number" value="<?php echo (int)($pi_data['monthly_cap_er'] ?? 200); ?>" class="stat-input" style="width: 100%; padding: 6px 10px 6px 24px; border: 1px solid var(--border-color); border-radius: 6px; text-align: right; background: var(--background); color: var(--text-primary);">
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- BIR Tax Configuration -->
        <div class="stat-group-card" style="background: var(--surface); border: 1px solid var(--border-color); border-radius: 12px; padding: 20px;">
          <div class="sg-header" style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 16px;">
            <i data-lucide="file-text" style="color:#3b82f6"></i>
            <h4 style="margin: 0; font-size: 16px; color: var(--text-primary);">BIR Tax (TRAIN)</h4>
          </div>
          <div class="sg-body">
            <p class="sg-desc" style="color: var(--text-secondary); font-size: 13px; margin-bottom: 16px;">Withholding tax settings and tax-exempt benefit caps.</p>
            <div class="editable-form" style="display: flex; flex-direction: column; gap: 12px;">
              <div class="form-group-inline" style="display: flex; justify-content: space-between; align-items: center;">
                <label style="font-size: 13px; color: var(--text-secondary);">Tax Exempt Limit</label>
                <div class="inline-input-symbol" style="position: relative; width: 120px;">
                  <span style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--text-tertiary); font-size: 13px;">&#8369;</span>
                  <input type="number" value="<?php echo (int)($bir_data['tax_exempt_limit'] ?? 250000); ?>" class="stat-input" style="width: 100%; padding: 6px 10px 6px 24px; border: 1px solid var(--border-color); border-radius: 6px; text-align: right; background: var(--background); color: var(--text-primary);">
                </div>
              </div>
              <div class="form-group-inline" style="display: flex; justify-content: space-between; align-items: center;">
                <label style="font-size: 13px; color: var(--text-secondary);">De Minimis Cap</label>
                <div class="inline-input-symbol" style="position: relative; width: 120px;">
                  <span style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--text-tertiary); font-size: 13px;">&#8369;</span>
                  <input type="number" value="<?php echo (int)($bir_data['de_minimis_cap'] ?? 90000); ?>" class="stat-input" style="width: 100%; padding: 6px 10px 6px 24px; border: 1px solid var(--border-color); border-radius: 6px; text-align: right; background: var(--background); color: var(--text-primary);">
                </div>
              </div>
              <div class="form-group-inline" style="display: flex; justify-content: space-between; align-items: center;">
                <label style="font-size: 13px; color: var(--text-secondary);">13th Month Cap</label>
                <div class="inline-input-symbol" style="position: relative; width: 120px;">
                  <span style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--text-tertiary); font-size: 13px;">&#8369;</span>
                  <input type="number" value="<?php echo (int)($bir_data['thirteenth_month_cap'] ?? 90000); ?>" class="stat-input" style="width: 100%; padding: 6px 10px 6px 24px; border: 1px solid var(--border-color); border-radius: 6px; text-align: right; background: var(--background); color: var(--text-primary);">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </main>
  <script src="../../js/salary.js"></script>
  <script src="../../js/admin_statutory.js"></script>
  <script>
    lucide.createIcons();
  </script>
</body>
</html>







