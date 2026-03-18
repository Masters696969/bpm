<?php
session_start();
if (!isset($_SESSION['username'])) {
  header("Location: ../../login.php");
  exit();
}
require_once '../../config/config.php';

// ── Live HR Analytics Queries ────────────────────────────────────────────────
// 1. Total employees
$r = $conn->query("SELECT COUNT(*) as c FROM employee");
$total_employees = (int)$r->fetch_assoc()['c'];

// 2. Employment status breakdown
$r = $conn->query("SELECT EmploymentStatus, COUNT(*) as c FROM employmentinformation GROUP BY EmploymentStatus");
$emp_status = [];
while ($row = $r->fetch_assoc()) $emp_status[] = $row;

// 3. Gender breakdown
$r = $conn->query("SELECT COALESCE(Gender,'Unknown') as Gender, COUNT(*) as c FROM employee GROUP BY Gender");
$gender_data = [];
while ($row = $r->fetch_assoc()) $gender_data[] = $row;

// 4. Department headcount
$r = $conn->query("SELECT d.DepartmentName, COUNT(ei.EmployeeID) as headcount FROM department d LEFT JOIN employmentinformation ei ON d.DepartmentID=ei.DepartmentID GROUP BY d.DepartmentID ORDER BY headcount DESC LIMIT 6");
$dept_data = [];
while ($row = $r->fetch_assoc()) $dept_data[] = $row;

// 5. Applicant pipeline
$r = $conn->query("SELECT COUNT(*) as c FROM applicants WHERE Status NOT IN ('Rejected','Withdrawn')");
$pending_applicants = (int)$r->fetch_assoc()['c'];

$r = $conn->query("SELECT COUNT(*) as c FROM applicants WHERE Status='Accepted' AND ApprovalStatus='Approved'");
$to_onboard = (int)$r->fetch_assoc()['c'];

$r = $conn->query("SELECT COUNT(*) as c FROM applicants WHERE ApprovalStatus='Hired'");
$total_hired = (int)$r->fetch_assoc()['c'];

$r = $conn->query("SELECT COUNT(*) as c FROM applicants WHERE Status='Rejected' OR Status='Withdrawn'");
$rejected_apps = (int)$r->fetch_assoc()['c'];

// 6. Recent hires
$r = $conn->query("SELECT e.FirstName, e.LastName, e.EmployeeCode, p.PositionName, d.DepartmentName, ei.HiringDate, ei.EmploymentStatus FROM employee e LEFT JOIN employmentinformation ei ON e.EmployeeID=ei.EmployeeID LEFT JOIN positions p ON ei.PositionID=p.PositionID LEFT JOIN department d ON ei.DepartmentID=d.DepartmentID ORDER BY e.EmployeeID DESC LIMIT 6");
$recent_hires = [];
while ($row = $r->fetch_assoc()) $recent_hires[] = $row;

// 7. Authorized vs actual headcount
$r = $conn->query("SELECT SUM(AuthorizedHeadcount) as auth FROM positions");
$authorized_headcount = (int)($r->fetch_assoc()['auth'] ?? 0);
$headcount_gap = max(0, $authorized_headcount - $total_employees);

// 8. Pending leave requests
$r = $conn->query("SHOW TABLES LIKE 'leave_requests'");
$pending_leave = 0;
if ($r->num_rows > 0) {
    $r = $conn->query("SELECT COUNT(*) as c FROM leave_requests WHERE Status='Pending'");
    $pending_leave = (int)$r->fetch_assoc()['c'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HR Analytics Dashboard</title>
  <link rel="stylesheet" href="../../css/admindashboard.css?v=1.2">
  <script src="https://unpkg.com/lucide@latest"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            <a href="timesheetdata.php" class="submenu-item <?php echo($page === 'timesheetdata') ? 'active' : ''; ?>">
              <i data-lucide="layout-dashboard"></i>
              <span>Timesheet Data</span>
            </a>
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
        <button class="icon-btn" onclick="window.print()" title="Print Dashboard">
          <i data-lucide="printer"></i>
        </button>
        <button class="icon-btn">
          <i data-lucide="bell"></i>
        </button>
      </div>
    </header>

    <div class="content-wrapper">

      <?php
        // KPI color helpers
        $statusColors = ['Probationary'=>'#f59e0b','Regular'=>'#2ca078','Contractual'=>'#3b82f6'];
        $total_probationary = 0; $total_regular = 0; $total_contractual = 0;
        foreach($emp_status as $s) {
          if($s['EmploymentStatus']==='Probationary') $total_probationary=(int)$s['c'];
          if($s['EmploymentStatus']==='Regular')  $total_regular=(int)$s['c'];
          if($s['EmploymentStatus']==='Contractual') $total_contractual=(int)$s['c'];
        }
      ?>

      <!-- ── KPI Cards ── -->
      <div class="stats-grid" style="grid-template-columns:repeat(auto-fit,minmax(210px,1fr));gap:18px;margin-bottom:28px;">

        <div class="stat-card" style="position:relative;overflow:hidden;">
          <div class="stat-icon" style="background:rgba(44,160,120,.12);color:#2ca078;"><i data-lucide="users"></i></div>
          <div class="stat-content">
            <span class="stat-label">Total Employees</span>
            <h3 class="stat-value" style="font-size:2rem;"><?= $total_employees ?></h3>
            <div class="stat-trend positive"><i data-lucide="trending-up"></i><span>Active workforce</span></div>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon" style="background:rgba(245,158,11,.12);color:#f59e0b;"><i data-lucide="contact-round"></i></div>
          <div class="stat-content">
            <span class="stat-label">Pending Applicants</span>
            <h3 class="stat-value" style="font-size:2rem;"><?= $pending_applicants ?></h3>
            <div class="stat-trend" style="color:var(--text-secondary);"><i data-lucide="clock"></i><span>In pipeline</span></div>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon" style="background:rgba(59,130,246,.12);color:#3b82f6;"><i data-lucide="user-plus"></i></div>
          <div class="stat-content">
            <span class="stat-label">Ready to Onboard</span>
            <h3 class="stat-value" style="font-size:2rem;"><?= $to_onboard ?></h3>
            <?php if($to_onboard>0): ?>
            <div class="stat-trend positive"><i data-lucide="alert-circle"></i><a href="newhiredonboard.php" style="color:#2ca078;text-decoration:none;">Finalize now →</a></div>
            <?php else: ?>
            <div class="stat-trend" style="color:var(--text-secondary);"><i data-lucide="check"></i><span>None pending</span></div>
            <?php endif; ?>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon" style="background:rgba(139,92,246,.12);color:#8b5cf6;"><i data-lucide="briefcase"></i></div>
          <div class="stat-content">
            <span class="stat-label">Headcount Gap</span>
            <h3 class="stat-value" style="font-size:2rem;"><?= $headcount_gap ?></h3>
            <div class="stat-trend" style="color:var(--text-secondary);"><i data-lucide="target"></i><span><?= $total_employees ?> / <?= $authorized_headcount ?> authorized</span></div>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon" style="background:rgba(239,68,68,.12);color:#ef4444;"><i data-lucide="tickets-plane"></i></div>
          <div class="stat-content">
            <span class="stat-label">Leave Requests</span>
            <h3 class="stat-value" style="font-size:2rem;"><?= $pending_leave ?></h3>
            <div class="stat-trend <?= $pending_leave>0?'negative':'' ?>"><i data-lucide="<?= $pending_leave>0?'alert-circle':'check-circle' ?>"></i><span><?= $pending_leave>0?'Awaiting approval':'All cleared' ?></span></div>
          </div>
        </div>

      </div>

      <!-- ── Charts Row ── -->
      <div class="content-grid" style="grid-template-columns:1fr 1fr 1fr;gap:20px;margin-bottom:24px;">

        <!-- Department Headcount (Bar) -->
        <div class="content-card" style="grid-column:span 2;">
          <div class="card-header">
            <div><h3 class="card-title">Department Headcount</h3><p class="card-subtitle">Employee distribution across departments</p></div>
          </div>
          <div class="card-body" style="padding:16px;">
            <canvas id="deptChart" height="120"></canvas>
          </div>
        </div>

        <!-- Employment Status (Doughnut) -->
        <div class="content-card">
          <div class="card-header">
            <div><h3 class="card-title">Employment Type</h3><p class="card-subtitle">Status distribution</p></div>
          </div>
          <div class="card-body" style="padding:16px;display:flex;align-items:center;justify-content:center;">
            <canvas id="statusChart" width="200" height="200"></canvas>
          </div>
        </div>

      </div>

      <!-- ── Second Row ── -->
      <div class="bottom-grid" style="grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;">

        <!-- Gender Breakdown (Doughnut) -->
        <div class="content-card">
          <div class="card-header">
            <div><h3 class="card-title">Gender Diversity</h3><p class="card-subtitle">Workforce composition</p></div>
          </div>
          <div class="card-body" style="padding:16px;display:flex;flex-direction:column;align-items:center;">
            <canvas id="genderChart" width="180" height="180"></canvas>
            <div style="display:flex;gap:20px;margin-top:14px;flex-wrap:wrap;justify-content:center;">
              <?php
                $gColors=['Male'=>'#3b82f6','Female'=>'#ec4899','Unknown'=>'#9ca3af'];
                foreach($gender_data as $g):
                  $col = $gColors[$g['Gender']] ?? '#9ca3af';
              ?>
              <div style="display:flex;align-items:center;gap:6px;font-size:12px;">
                <span style="width:10px;height:10px;border-radius:50%;background:<?=$col?>;display:inline-block;"></span>
                <span><?=htmlspecialchars($g['Gender'])?> — <strong><?=$g['c']?></strong></span>
              </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>

        <!-- Applicant Pipeline -->
        <div class="content-card">
          <div class="card-header">
            <div><h3 class="card-title">Recruitment Pipeline</h3><p class="card-subtitle">Applicant stages overview</p></div>
            <a href="applicationmgt.php" class="btn-text">Manage →</a>
          </div>
          <div class="card-body" style="padding:20px;">
            <?php
              $pipelineStages = [
                ['label'=>'New / Pending','count'=>0,'color'=>'#f59e0b','icon'=>'file-text'],
                ['label'=>'In Interview','count'=>0,'color'=>'#3b82f6','icon'=>'mic'],
                ['label'=>'Hired','count'=>$total_hired,'color'=>'#2ca078','icon'=>'user-check'],
                ['label'=>'Ready to Onboard','count'=>$to_onboard,'color'=>'#8b5cf6','icon'=>'user-plus'],
                ['label'=>'Rejected','count'=>$rejected_apps,'color'=>'#ef4444','icon'=>'x-circle'],
              ];
              // count new and interview from pipeline data
              foreach($pipelineStages as &$ps) {
                if($ps['label']==='New / Pending') { $ps['count'] = array_sum(array_column(array_filter($pipelineStages, fn($p)=>false), 'count')); }
              }
              $totalApplicantsAll = $pending_applicants + $total_hired + $rejected_apps;
            ?>
            <?php
              $stagesDisplay = [
                ['label'=>'Pending Review','count'=>$pending_applicants,'color'=>'#f59e0b','icon'=>'file-text'],
                ['label'=>'Hired (All Time)','count'=>$total_hired,'color'=>'#2ca078','icon'=>'user-check'],
                ['label'=>'Ready to Onboard','count'=>$to_onboard,'color'=>'#8b5cf6','icon'=>'user-plus'],
                ['label'=>'Rejected / Withdrawn','count'=>$rejected_apps,'color'=>'#ef4444','icon'=>'x-circle'],
              ];
              $totalAll = $pending_applicants + $total_hired + $rejected_apps;
            ?>
            <div style="display:flex;flex-direction:column;gap:14px;">
              <?php foreach($stagesDisplay as $s): ?>
              <div style="display:flex;align-items:center;gap:12px;">
                <div style="width:36px;height:36px;border-radius:8px;background:<?=$s['color']?>22;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                  <i data-lucide="<?=$s['icon']?>" style="width:16px;color:<?=$s['color']?>;"></i>
                </div>
                <div style="flex:1;">
                  <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
                    <span style="font-size:12px;color:var(--text-secondary);"><?=htmlspecialchars($s['label'])?></span>
                    <span style="font-size:12px;font-weight:700;color:var(--text-primary);"><?=$s['count']?></span>
                  </div>
                  <div style="height:5px;background:var(--border-color);border-radius:3px;">
                    <?php $pct = $totalAll>0?round($s['count']/$totalAll*100):0; ?>
                    <div style="height:5px;background:<?=$s['color']?>;border-radius:3px;width:<?=$pct?>%;"></div>
                  </div>
                </div>
              </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>

      </div>

      <!-- ── Recent Hires Table ── -->
      <div class="content-card" style="margin-bottom:24px;">
        <div class="card-header">
          <div><h3 class="card-title">Recent Hires</h3><p class="card-subtitle">Latest employees added to the system</p></div>
          <a href="employeemaster.php" class="btn-text">View All →</a>
        </div>
        <div class="card-body" style="padding:0;">
          <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead>
              <tr style="border-bottom:1px solid var(--border-color);">
                <th style="padding:12px 20px;text-align:left;font-size:11px;text-transform:uppercase;letter-spacing:.06em;color:var(--text-tertiary);font-weight:600;">Employee</th>
                <th style="padding:12px 20px;text-align:left;font-size:11px;text-transform:uppercase;letter-spacing:.06em;color:var(--text-tertiary);font-weight:600;">Department</th>
                <th style="padding:12px 20px;text-align:left;font-size:11px;text-transform:uppercase;letter-spacing:.06em;color:var(--text-tertiary);font-weight:600;">Position</th>
                <th style="padding:12px 20px;text-align:left;font-size:11px;text-transform:uppercase;letter-spacing:.06em;color:var(--text-tertiary);font-weight:600;">Hired Date</th>
                <th style="padding:12px 20px;text-align:left;font-size:11px;text-transform:uppercase;letter-spacing:.06em;color:var(--text-tertiary);font-weight:600;">Status</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($recent_hires as $hire):
                $initials = strtoupper(substr($hire['FirstName'],0,1).substr($hire['LastName'],0,1));
                $avatarColors = ['#2ca078','#3b82f6','#f59e0b','#8b5cf6','#ef4444','#ec4899'];
                $avatarColor = $avatarColors[crc32($hire['EmployeeCode'])%count($avatarColors)];
                $statusColor = $hire['EmploymentStatus']==='Regular' ? '#2ca078' : ($hire['EmploymentStatus']==='Contractual'?'#3b82f6':'#f59e0b');
              ?>
              <tr style="border-bottom:1px solid var(--border-color);transition:background .15s;" onmouseover="this.style.background='var(--surface)'" onmouseout="this.style.background=''">
                <td style="padding:14px 20px;">
                  <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:34px;height:34px;border-radius:8px;background:<?=$avatarColor?>;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:#fff;flex-shrink:0;"><?=$initials?></div>
                    <div>
                      <div style="font-weight:600;color:var(--text-primary);"><?=htmlspecialchars($hire['FirstName'].' '.$hire['LastName'])?></div>
                      <div style="font-size:11px;color:var(--text-tertiary);"><?=htmlspecialchars($hire['EmployeeCode'])?></div>
                    </div>
                  </div>
                </td>
                <td style="padding:14px 20px;color:var(--text-secondary);"><?=htmlspecialchars($hire['DepartmentName']??'—')?></td>
                <td style="padding:14px 20px;color:var(--text-secondary);"><?=htmlspecialchars($hire['PositionName']??'—')?></td>
                <td style="padding:14px 20px;color:var(--text-secondary);"><?=htmlspecialchars(date('M d, Y',strtotime($hire['HiringDate']??'now')))?></td>
                <td style="padding:14px 20px;">
                  <span style="background:<?=$statusColor?>22;color:<?=$statusColor?>;border:1px solid <?=$statusColor?>44;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;"><?=htmlspecialchars($hire['EmploymentStatus']??'—')?></span>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

    </div>

    <!-- ── Chart.js Initialization ── -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
      const isDark = document.body.classList.contains('dark-mode');
      const gridColor = 'rgba(128,128,128,0.12)';
      const textColor = getComputedStyle(document.documentElement).getPropertyValue('--text-secondary').trim() || '#6b7280';

      // Department Bar Chart
      const deptCtx = document.getElementById('deptChart');
      if (deptCtx) {
        new Chart(deptCtx, {
          type: 'bar',
          data: {
            labels: <?= json_encode(array_column($dept_data,'DepartmentName')) ?>,
            datasets: [{
              label: 'Employees',
              data: <?= json_encode(array_map(fn($d)=>(int)$d['headcount'], $dept_data)) ?>,
              backgroundColor: ['#2ca078cc','#3b82f6cc','#f59e0bcc','#8b5cf6cc','#ef4444cc','#ec4899cc'],
              borderRadius: 8,
              borderSkipped: false,
            }]
          },
          options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
              y: { beginAtZero: true, grid: { color: gridColor }, ticks: { precision: 0 } },
              x: { grid: { display: false } }
            }
          }
        });
      }

      // Employment Status Doughnut
      const statusCtx = document.getElementById('statusChart');
      if (statusCtx) {
        new Chart(statusCtx, {
          type: 'doughnut',
          data: {
            labels: <?= json_encode(array_column($emp_status,'EmploymentStatus')) ?>,
            datasets: [{
              data: <?= json_encode(array_map(fn($s)=>(int)$s['c'], $emp_status)) ?>,
              backgroundColor: ['#f59e0bcc', '#2ca078cc', '#3b82f6cc'],
              borderWidth: 0,
              hoverOffset: 6
            }]
          },
          options: {
            responsive: false,
            cutout: '68%',
            plugins: {
              legend: { position: 'bottom', labels: { padding: 12, font: { size: 11 } } }
            }
          }
        });
      }

      // Gender Doughnut
      const genderCtx = document.getElementById('genderChart');
      if (genderCtx) {
        const gColors = <?= json_encode(array_map(fn($g)=> $g['Gender']==='Male'?'#3b82f6cc':($g['Gender']==='Female'?'#ec4899cc':'#9ca3afcc'), $gender_data)) ?>;
        new Chart(genderCtx, {
          type: 'doughnut',
          data: {
            labels: <?= json_encode(array_column($gender_data,'Gender')) ?>,
            datasets: [{
              data: <?= json_encode(array_map(fn($g)=>(int)$g['c'], $gender_data)) ?>,
              backgroundColor: gColors,
              borderWidth: 0,
              hoverOffset: 6
            }]
          },
          options: {
            responsive: false,
            cutout: '65%',
            plugins: { legend: { display: false } }
          }
        });
      }
    });
    </script>
  </main>
  <script src="../../js/admindashboard.js"></script>
  <script>
    lucide.createIcons();
  </script>
</body>
</html>






