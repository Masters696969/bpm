<?php
require_once '../../config/config.php';
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../../login.php");
    exit();
}

$page = 'newhiredonboard';
$module = 'newhiredonboard';

// Fetch Approved Applicants waiting for Onboarding
$sql = "SELECT a.* 
        FROM applicants a 
        WHERE a.Status = 'Accepted' AND a.ApprovalStatus IN ('Approved', 'Hired')
        ORDER BY a.AppliedAt DESC";

// Debug: Show the actual SQL query
$debug_sql_query = $sql;
$applicants = $conn->query($sql);

// Check for query error
if (!$applicants) {
    $debug_error = "Query Error: " . $conn->error;
} else {
    $debug_error = "Query Success";
}

// DEBUG: Check what applicants exist
$debug_sql = "SELECT Status, ApprovalStatus, COUNT(*) as count FROM applicants GROUP BY Status, ApprovalStatus";
$debug_result = $conn->query($debug_sql);
$debug_info = [];
if ($debug_result) {
    while($row = $debug_result->fetch_assoc()) {
        $debug_info[] = $row;
    }
}

// Also check for any applicants with different statuses that might be ready for onboarding
$backup_sql = "SELECT a.* 
        FROM applicants a 
        WHERE a.Status IN ('Accepted', 'Shortlisted', 'Interview') 
        ORDER BY a.AppliedAt DESC
        LIMIT 10";

$debug_backup_query = $backup_sql;
$backup_applicants = $conn->query($backup_sql);

// Check for backup query error
if (!$backup_applicants) {
    $debug_backup_error = "Backup Query Error: " . $conn->error;
} else {
    $debug_backup_error = "Backup Query Success";
}

// Test simple query to make sure connection works
$test_sql = "SELECT * FROM applicants WHERE Status = 'Accepted' LIMIT 5";
$test_result = $conn->query($test_sql);
$test_count = ($test_result) ? $test_result->num_rows : 0;

// Fetch Positions for Dropdown
$positions = $conn->query("SELECT PositionID, PositionName, SalaryGradeID FROM positions ORDER BY PositionName ASC");
$posArray = [];
while($p = $positions->fetch_assoc()) $posArray[] = $p;

// Fetch Salary Grades
$grades = $conn->query("SELECT SalaryGradeID, GradeName, MinSalary, MaxSalary FROM salary_grades ORDER BY GradeLevel ASC");
$gradeArray = [];
while($g = $grades->fetch_assoc()) $gradeArray[] = $g;

// Calculate Next Employee ID for UI display
$resCount = $conn->query("SELECT COUNT(EmployeeID) as emp_count FROM employee");
$countRow = $resCount->fetch_assoc();
$nextId = ($countRow['emp_count'] ?? 0) + 1;
$nextEmployeeCode = "ADM" . date('Y') . str_pad($nextId, 4, '0', STR_PAD_LEFT);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link rel="stylesheet" href="../../css/newhiredonboard.css?v=1.2">
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
          <div class="nav-item-group <?php echo ($module === 'newhiredonboard') ? 'active' : ''; ?>">
          <button class="nav-item has-submenu <?php echo ($module === 'newhiredonboard') ? 'active' : ''; ?>" data-module="newhiredonboard">
            <div class="nav-item-content">
              <i data-lucide="user-plus"></i>
              <span>New Hired Onboard</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-newhiredonboard">
            <a href="newhiredonboard.php" class="submenu-item <?php echo ($page === 'newhiredonboard') ? 'active' : ''; ?>">
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
          <h1>Approved for Onboarding</h1>
          <p>Candidates who have passed evaluation and are ready for official employment.</p>
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
      <!-- Premium Cards Section -->
      <div class="premium-cards-section" style="margin-bottom: 32px;">
        <div class="premium-cards-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
          <!-- Ready to Finalize Card -->
          <div class="premium-card" style="background: linear-gradient(135deg, rgba(44, 160, 120, 0.1), rgba(44, 160, 120, 0.05)); border: 1px solid rgba(44, 160, 120, 0.2); border-radius: 16px; padding: 24px; position: relative; overflow: hidden;">
            <div class="premium-card-header" style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
              <div class="premium-icon" style="width: 48px; height: 48px; background: var(--brand-green); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i data-lucide="user-check" style="width: 24px; height: 24px; color: white;"></i>
              </div>
              <div>
                <h3 style="font-size: 16px; font-weight: 600; color: var(--text-primary); margin: 0;">Ready to Finalize</h3>
                <p style="font-size: 12px; color: var(--text-secondary); margin: 0;">Candidates approved for onboarding</p>
              </div>
            </div>
            <div class="premium-stats" style="display: flex; align-items: center; justify-content: space-between;">
              <div>
                <div class="premium-number" id="readyCount" style="font-size: 32px; font-weight: 700; color: var(--brand-green);">0</div>
                <div class="premium-label" style="font-size: 12px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.05em;">Candidates</div>
              </div>
              <div class="premium-badge" style="background: var(--brand-green); color: white; padding: 6px 12px; border-radius: 20px; font-size: 11px; font-weight: 600;">
                <i data-lucide="clock" style="width: 12px; margin-right: 4px;"></i>
                PENDING
              </div>
            </div>
          </div>

          <!-- Already in Master Data Card -->
          <div class="premium-card" style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(59, 130, 246, 0.05)); border: 1px solid rgba(59, 130, 246, 0.2); border-radius: 16px; padding: 24px; position: relative; overflow: hidden;">
            <div class="premium-card-header" style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
              <div class="premium-icon" style="width: 48px; height: 48px; background: #3b82f6; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i data-lucide="database" style="width: 24px; height: 24px; color: white;"></i>
              </div>
              <div>
                <h3 style="font-size: 16px; font-weight: 600; color: var(--text-primary); margin: 0;">Employee Master Data</h3>
                <p style="font-size: 12px; color: var(--text-secondary); margin: 0;">Already processed employees</p>
              </div>
            </div>
            <div class="premium-stats" style="display: flex; align-items: center; justify-content: space-between;">
              <div>
                <div class="premium-number" id="masterDataCount" style="font-size: 32px; font-weight: 700; color: #3b82f6;">0</div>
                <div class="premium-label" style="font-size: 12px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.05em;">Employees</div>
              </div>
              <div class="premium-badge" style="background: #3b82f6; color: white; padding: 6px 12px; border-radius: 20px; font-size: 11px; font-weight: 600;">
                <i data-lucide="check-circle" style="width: 12px; margin-right: 4px;"></i>
                COMPLETED
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="onboard-container">

        <div class="onboard-card">
          <div class="card-header-table" style="display:flex; justify-content:space-between; align-items:center;">
            <div>
              <h3 class="card-title">Pending Onboarding</h3>
              <p class="card-subtitle">Candidates waiting for final profile setup and employee ID generation.</p>
            </div>
            <div class="header-search-container" style="position:relative; width:280px;">
              <i data-lucide="search" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); width:16px; color:var(--text-tertiary);"></i>
              <input type="text" id="onboardSearch" placeholder="Search candidate..." style="width:100%; padding:10px 12px 10px 38px; border-radius:10px; border:1px solid var(--border-color); background:var(--background); color:var(--text-primary); font-size:13px; outline:none; transition:all 0.2s ease;">
            </div>
          </div>
          <div class="card-body-table" style="padding:0;">
            <div class="table-responsive">
              <table class="onboard-table">
                <thead>
                  <tr>
                    <th>Candidate</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Position</th>
                    <th>Premium Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                  // Use backup data if main query returns no results
                  $dataToShow = ($applicants && $applicants->num_rows > 0) ? $applicants : $backup_applicants;
                  
                  if (isset($dataToShow) && $dataToShow && $dataToShow->num_rows > 0): ?>
                    <?php while($row = $dataToShow->fetch_assoc()): 
                        $initials = strtoupper(substr($row['FirstName'], 0, 1) . substr($row['LastName'], 0, 1));
                    ?>
                      <tr class="onboard-row" data-name="<?php echo htmlspecialchars($row['FirstName'] . ' ' . $row['LastName']); ?>" data-email="<?php echo htmlspecialchars($row['Email']); ?>" data-pos="<?php echo htmlspecialchars($row['PositionName'] ?? 'Not Assigned'); ?>">
                        <td>
                          <div style="display:flex; align-items:center; gap:12px;">
                            <div class="oc-avatar" style="width:32px; height:32px; font-size:11px;"><?php echo $initials; ?></div>
                            <div>
                                <div style="font-weight:600; color:var(--text-primary);"><?php echo htmlspecialchars($row['FirstName'] . ' ' . $row['LastName']); ?></div>
                                <div style="font-size:11px; color:var(--text-tertiary);">
                                  Status: <?php echo htmlspecialchars($row['Status'] . '/' . $row['ApprovalStatus']); ?>
                                </div>
                            </div>
                          </div>
                        </td>
                        <td style="font-size:13px; color:var(--text-secondary);"><?php echo htmlspecialchars($row['Email']); ?></td>
                        <td style="font-size:13px; color:var(--text-secondary);"><?php echo htmlspecialchars($row['Phone']); ?></td>
                        <td>
                          <div class="oc-badge" style="display:inline-flex; border-radius:6px; padding:4px 8px; font-size:11px;">
                            <i data-lucide="briefcase" style="width:12px; margin-right:4px;"></i> 
                            <?php echo htmlspecialchars($row['PositionName'] ?? 'Position Not Assigned'); ?>
                          </div>
                        </td>
                        <td>
                          <?php if($row['ApprovalStatus'] === 'Hired'): ?>
                            <div style="display:inline-flex; align-items:center; gap:6px; color:#3b82f6; background:rgba(59, 130, 246, 0.1); padding:6px 14px; border-radius:10px; font-size:11px; font-weight:700; border:1px solid rgba(59, 130, 246, 0.2);">
                              <i data-lucide="database" style="width:14px;"></i>
                              <span>In Master Data</span>
                            </div>
                          <?php elseif($row['Status'] === 'Accepted' && in_array($row['ApprovalStatus'], ['Approved', 'Hired'])): ?>
                            <div style="display:inline-flex; align-items:center; gap:6px; color:var(--brand-green); background:rgba(44, 160, 120, 0.1); padding:6px 14px; border-radius:10px; font-size:11px; font-weight:700; border:1px solid rgba(44, 160, 120, 0.2);">
                              <i data-lucide="star" style="width:14px;"></i>
                              <span>Premium Ready</span>
                            </div>
                          <?php else: ?>
                            <div style="display:inline-flex; align-items:center; gap:6px; color:var(--text-tertiary); background:rgba(156, 163, 175, 0.1); padding:6px 14px; border-radius:10px; font-size:11px; font-weight:700; border:1px solid rgba(156, 163, 175, 0.2);">
                              <i data-lucide="clock" style="width:14px;"></i>
                              <span>Not Ready</span>
                            </div>
                          <?php endif; ?>
                        </td>
                        <td>
                          <?php if($row['ApprovalStatus'] === 'Hired'): ?>
                            <div style="display:inline-flex; align-items:center; gap:6px; color:var(--brand-green); background:rgba(44, 160, 120, 0.05); padding:6px 14px; border-radius:10px; font-size:11px; font-weight:700; border:1px solid rgba(44, 160, 120, 0.1);">
                                <i data-lucide="check-circle" style="width:14px;"></i>
                                <span>Hired</span>
                            </div>
                          <?php elseif($row['Status'] === 'Accepted' && $row['ApprovalStatus'] === 'Approved'): ?>
                            <button class="btn-finalize action-btn-table" data-id="<?php echo $row['ApplicantID']; ?>" 
                                    data-first="<?php echo htmlspecialchars($row['FirstName']); ?>" 
                                    data-last="<?php echo htmlspecialchars($row['LastName']); ?>"
                                    data-email="<?php echo htmlspecialchars($row['Email']); ?>"
                                    data-phone="<?php echo htmlspecialchars($row['Phone']); ?>"
                                    data-address="<?php echo htmlspecialchars($row['PermanentAddress']); ?>"
                                    data-emergency_name="<?php echo htmlspecialchars($row['EmergencyContactName']); ?>"
                                    data-emergency_phone="<?php echo htmlspecialchars($row['EmergencyPhone']); ?>"
                                    data-emergency_rel="<?php echo htmlspecialchars($row['EmergencyRelationship']); ?>"
                                    data-pos_id="<?php echo $row['PositionID'] ?? ''; ?>"
                                    data-pos_code="<?php echo htmlspecialchars($row['PositionCode'] ?? ''); ?>"
                                    data-grade_id="<?php echo $row['DefaultGradeID'] ?? ''; ?>"
                                    data-salary_type="<?php echo htmlspecialchars($row['SalaryType'] ?? 'Monthly'); ?>"
                                    style="background:var(--brand-green); color:white; border:none; padding:6px 14px; border-radius:8px; font-size:11px; font-weight:700; cursor:pointer; display:flex; align-items:center; gap:6px; transition:all 0.2s ease;">
                                <i data-lucide="user-plus" style="width:14px;"></i>
                                <span>Finalize Profile</span>
                            </button>
                          <?php else: ?>
                            <div style="display:inline-flex; align-items:center; gap:6px; color:var(--text-tertiary); background:rgba(156, 163, 175, 0.05); padding:6px 14px; border-radius:10px; font-size:11px; font-weight:700; border:1px solid rgba(156, 163, 175, 0.1);">
                                <i data-lucide="lock" style="width:14px;"></i>
                                <span>Not Eligible</span>
                            </div>
                          <?php endif; ?>
                        </td>
                      </tr>
                    <?php endwhile; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="6" style="text-align:center; padding:60px;">
                        <div class="empty-state">
                            <div class="empty-icon"><i data-lucide="users"></i></div>
                            <h3>No candidates found</h3>
                            <p>No applicants found in the system. Please check the application process.</p>
                        </div>
                      </td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Onboarding Modal -->
      <div id="onboardModal" class="modal-overlay">
          <div class="modal-content onboard-modal-content">
              <div class="modal-header">
                  <div class="header-left">
                      <h2><i data-lucide="user-check"></i> Finalize Employment</h2>
                      <p>Complete the employee profile to trigger onboarding.</p>
                  </div>
                  <div class="header-right">
                      <div class="secure-badge">
                          <i data-lucide="shield-check"></i>
                          <span>SESSION SECURE</span>
                      </div>
                      <button class="close-modal-btn" aria-label="Close modal"><i data-lucide="x"></i></button>
                  </div>
              </div>
              <form id="onboardForm">
                  <input type="hidden" name="applicant_id" id="onboardAppId">
                  <div class="modal-body onboarding-landscape-body">
                      <!-- Left Column: Profile Sidebar -->
                      <div class="onboard-sidebar">
                          <div class="sidebar-section">
                              <label class="section-label">Identity & Access</label>
                              <div class="employee-id-box">
                                  <div class="id-icon"><i data-lucide="fingerprint"></i></div>
                                  <div class="id-details">
                                      <input type="text" id="displayEmployeeCode" value="PENDING..." disabled>
                                  </div>
                              </div>
                          </div>

                          <div class="sidebar-section">
                              <label class="section-label">Candidate Details</label>
                              <div class="profile-preview-card">
                                  <div class="pp-avatar-wrapper">
                                      <div class="pp-avatar" id="ppAvatar"></div>
                                      <div class="pp-status-dot"></div>
                                  </div>
                                  <div class="pp-info">
                                      <h3 id="ppName">Candidate Name</h3>
                                      <p id="ppEmail"></p>
                                  </div>
                              </div>
                          </div>

                          <div class="sidebar-section verification-section">
                              <label class="section-label">Compliance Check</label>
                              <div class="verification-list">
                                  <div class="ver-item">
                                      <i data-lucide="map-pin"></i>
                                      <div>
                                          <label>Address</label>
                                          <p id="ppAddress">--</p>
                                      </div>
                                  </div>
                                  <div class="ver-item">
                                      <i data-lucide="phone"></i>
                                      <div>
                                          <label>Emergency</label>
                                          <p id="ppEmergency">--</p>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </div>

                      <!-- Right Column: Form Main -->
                      <div class="onboard-main">
                          <div class="main-header-row">
                              <h4 class="form-section-title"><i data-lucide="settings-2"></i> Employment Configuration</h4>
                              <div class="status-indicator">ONBOARDING PREPARATION</div>
                          </div>

                          <div class="onboard-form-grid">
                              <div class="form-group">
                                  <label><i data-lucide="award"></i> Position & Role</label>
                                  <select name="position_id" id="onboardPosition" required>
                                      <option value="">Select Position</option>
                                      <?php foreach($posArray as $p): ?>
                                          <option value="<?php echo $p['PositionID']; ?>" data-grade="<?php echo $p['SalaryGradeID']; ?>">
                                              <?php echo htmlspecialchars($p['PositionName']); ?>
                                          </option>
                                      <?php endforeach; ?>
                                  </select>
                              </div>
                              <div class="form-group">
                                  <label><i data-lucide="coins"></i> Salary Grade</label>
                                  <select name="salary_grade_id" id="onboardSalaryGrade" required>
                                      <option value="">Select Grade</option>
                                      <?php foreach($gradeArray as $g): ?>
                                          <option value="<?php echo $g['SalaryGradeID']; ?>">
                                              <?php echo htmlspecialchars($g['GradeName']); ?> (<?php echo number_format($g['MinSalary'], 2); ?> - <?php echo number_format($g['MaxSalary'], 2); ?>)
                                          </option>
                                      <?php endforeach; ?>
                                  </select>
                              </div>
                              <div class="form-group">
                                  <label><i data-lucide="calendar"></i> Effective Hiring Date</label>
                                  <input type="date" name="hiring_date" value="<?php echo date('Y-m-d'); ?>" required>
                              </div>
                              <div class="form-group">
                                  <label><i data-lucide="briefcase"></i> Employment Type</label>
                                  <select name="employment_status" required>
                                      <option value="Probationary">Probationary</option>
                                      <option value="Regular">Regular</option>
                                      <option value="Contractual">Contractual</option>
                                  </select>
                              </div>
                              <div class="form-group">
                                  <label><i data-lucide="wallet"></i> Payroll Cycle (Salary Type)</label>
                                  <select name="salary_type" required>
                                      <option value="Monthly" selected>Monthly</option>
                                      <option value="Daily">Daily</option>
                                      <option value="Hourly">Hourly</option>
                                  </select>
                              </div>
                          </div>
                      </div>
                  </div>
                  <div class="modal-footer">
                      <button type="button" class="btn-secondary close-modal">Cancel</button>
                      <button type="submit" class="btn-primary">Confirm Onboarding</button>
                  </div>
              </form>
          </div>
      </div>
  </main>
  <script src="../../js/newhiredonboard.js"></script>
  <script>
    // Update Premium Card Counts
    document.addEventListener('DOMContentLoaded', function() {
      const rows = document.querySelectorAll('.onboard-row');
      let readyCount = 0;
      let masterDataCount = 0;
      
      rows.forEach(row => {
        const statusCell = row.querySelector('td:nth-child(5)');
        if (statusCell) {
          const text = statusCell.textContent.trim();
          if (text.includes('Premium Ready')) {
            readyCount++;
          } else if (text.includes('In Master Data')) {
            masterDataCount++;
          }
        }
      });
      
      const readyCountEl = document.getElementById('readyCount');
      const masterDataCountEl = document.getElementById('masterDataCount');
      
      if (readyCountEl) readyCountEl.textContent = readyCount;
      if (masterDataCountEl) masterDataCountEl.textContent = masterDataCount;
    });
    
    if (typeof lucide !== 'undefined') lucide.createIcons();
  </script>
</body>
</html>
