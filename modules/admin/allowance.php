<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../../login.php");
    exit();
}

require_once '../../config/config.php';

// Fetch Active Compensation Period (Assume ID 1 for now)
$period_id = 1;
$period_query = $conn->query("SELECT * FROM compensation_period WHERE period_id = $period_id");
$period_data = $period_query->fetch_assoc();

// Fetch Salary Grades
$grades_query = $conn->query("
    SELECT sg.SalaryGradeID, sg.GradeLevel, sg.GradeName, sg.MinSalary, sg.MaxSalary, sg.MidSalary, sg.Description
    FROM salary_grades sg 
    WHERE sg.period_id = $period_id 
    ORDER BY sg.SalaryGradeID ASC
");
$salary_grades = [];
while ($row = $grades_query->fetch_assoc()) {
    $salary_grades[] = $row;
}

// Fetch Allowance Types
$allowance_types_query = $conn->query("SELECT * FROM allowance_types ORDER BY AllowanceTypeID ASC");
$allowance_types = [];
$allowance_taxable_map = [];
while ($row = $allowance_types_query->fetch_assoc()) {
    $allowance_types[] = $row;
    $allowance_taxable_map[$row['AllowanceTypeID']] = $row['IsTaxable'];
}

// Fetch Grade Allowances
$grade_allowances_query = $conn->query("SELECT * FROM grade_allowances");
$grade_allowance_map = [];
while ($row = $grade_allowances_query->fetch_assoc()) {
    $grade_allowance_map[$row['SalaryGradeID']][$row['AllowanceTypeID']] = $row['Amount'];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link rel="stylesheet" href="../../css/cycle.css?v=1.2">
  <link rel="stylesheet" href="../../css/admin_allowance.css?v=1.2">
  <script src="https://unpkg.com/lucide@latest"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="icon" type="image/png" href="../../img/logo.png">
</head>
<body>

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
        <a href="dashboard.php" class="nav-item">
          <i data-lucide="layout-dashboard"></i>
          <span>HR ANALYTICS</span>
        </a>
      </div>
      <div class="nav-section">
        <span class="nav-section-title">HUMAN RESOURCES I</span>
        <div class="nav-item-group">
          <button class="nav-item has-submenu" data-module="recruitment">
            <div class="nav-item-content">
              <i data-lucide="layers-plus"></i>
              <span>Recruitment</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-recruitment">
            <a href="recruitment.php" class="submenu-item">
              <i data-lucide="layers-plus"></i>
              <span>Recruitment</span>
            </a>
          </div>
        </div>
        <div class="nav-item-group">
          <button class="nav-item has-submenu" data-module="applicationmgt">
            <div class="nav-item-content">
              <i data-lucide="contact-round"></i>
              <span>Applicant Management</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-applicationmgt">
            <a href="applicationmgt.php" class="submenu-item">
              <i data-lucide="contact-round"></i>
              <span>Applicant Management</span>
            </a>
          </div>
        </div>
        <div class="nav-item-group">
          <button class="nav-item has-submenu" data-module="newhiredonboard">
            <div class="nav-item-content">
              <i data-lucide="user-plus"></i>
              <span>New Hired Onboard</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-newhiredonboard">
            <a href="newhiredonboard.php" class="submenu-item">
              <i data-lucide="user-plus"></i>
              <span>New Hired Onboard</span>
            </a>
          </div>
        </div>
      </div>
      <div class="nav-section">
        <span class="nav-section-title">HUMAN RESOURCES II</span>
        <div class="nav-item-group">
          <button class="nav-item has-submenu" data-module="accounts">
            <div class="nav-item-content">
              <i data-lucide="users"></i>
              <span>Account Management</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-accounts">
            <a href="useraccount.php" class="submenu-item">
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
        </div>
        <div class="nav-item-group">
          <button class="nav-item has-submenu" data-module="competency">
            <div class="nav-item-content">
              <i data-lucide="pickaxe"></i>
              <span>Competency Management</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-competency">
            <a href="competencylibrary.php" class="submenu-item">
              <i data-lucide="book-text"></i>
              <span>Competency Library</span>
            </a>
            <a href="competencycategory.php" class="submenu-item">
              <i data-lucide="chart-bar-stacked"></i>
              <span>Competency Category</span>
            </a>
            <a href="competencylevel.php" class="submenu-item">
              <i data-lucide="circle-gauge"></i>
              <span>Competency Level</span>
            </a>
            <a href="competencyposition.php" class="submenu-item">
              <i data-lucide="briefcase"></i>
              <span>Competency Position</span>
            </a>
            <a href="competencyemployee.php" class="submenu-item">
              <i data-lucide="square-user"></i>
              <span>Competency Employee</span>
            </a>
            <a href="bankquestion.php" class="submenu-item">
              <i data-lucide="book-open-check"></i>
              <span>Bank Question</span>
            </a>
          </div>
        </div>
        <div class="nav-item-group">
          <button class="nav-item has-submenu" data-module="training">
            <div class="nav-item-content">
              <i data-lucide="briefcase-business"></i>
              <span>Training Management</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-training">
            <a href="training.php" class="submenu-item">
              <i data-lucide="briefcase-business"></i>
              <span>Training Management</span>
            </a>
          </div>
        </div>
        <div class="nav-item-group">
          <button class="nav-item has-submenu" data-module="succession">
            <div class="nav-item-content">
              <i data-lucide="notebook-pen"></i>
              <span>Succession Planning</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-succession">
            <a href="succession.php" class="submenu-item">
              <i data-lucide="notebook-pen"></i>
              <span>Succession Planning</span>
            </a>
          </div>
        </div>
        <div class="nav-item-group">
          <button class="nav-item has-submenu" data-module="learning">
            <div class="nav-item-content">
              <i data-lucide="notebook-text"></i>
              <span>Learning Management</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-learning">
            <a href="learning.php" class="submenu-item">
              <i data-lucide="notebook-text"></i>
              <span>Learning Management</span>
            </a>
          </div>
        </div>
      </div>
      <div class="nav-section">
        <span class="nav-section-title">HUMAN RESOURCES III</span>
        <div class="nav-item-group">
          <button class="nav-item has-submenu" data-module="shift">
            <div class="nav-item-content">
              <i data-lucide="calendar-check"></i>
              <span>Shift & Scheduling</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-shift">
            <a href="#" class="submenu-item">
              <i data-lucide="send-to-back"></i>
              <span>Shift & Scheduling</span>
            </a>
          </div>
        </div>
        <div class="nav-item-group">
          <button class="nav-item has-submenu" data-module="claims">
            <div class="nav-item-content">
              <i data-lucide="receipt-text"></i>
              <span>Claims & Reimbursements</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-claims">
            <a href="claims.php" class="submenu-item">
              <i data-lucide="receipt-text"></i>
              <span>Claims & Reimbursements</span>
            </a>
          </div>
        </div>
        <div class="nav-item-group">
          <button class="nav-item has-submenu" data-module="time">
            <div class="nav-item-content">
              <i data-lucide="clock"></i>
              <span>Time & Attendance</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-time">
            <a href="time.php" class="submenu-item">
              <i data-lucide="clock"></i>
              <span>Time & Attendance</span>
            </a>
          </div>
        </div>
        <div class="nav-item-group">
          <button class="nav-item has-submenu" data-module="timesheet">
            <div class="nav-item-content">
              <i data-lucide="calendar-days"></i>
              <span>Timesheet</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-timesheet">
            <a href="timesheet.php" class="submenu-item">
              <i data-lucide="calendar-days"></i>
              <span>Timesheet</span>
            </a>
          </div>
        </div>
        <div class="nav-item-group">
          <button class="nav-item has-submenu" data-module="leave">
            <div class="nav-item-content">
              <i data-lucide="tickets-plane"></i>
              <span>Leave Management</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-leave">
            <a href="leave.php" class="submenu-item">
              <i data-lucide="tickets-plane"></i>
              <span>Leave Management</span>
            </a>
          </div>
        </div>
      </div>
      <div class="nav-section">
        <span class="nav-section-title">HUMAN RESOURCES IV</span>
        <div class="nav-item-group">
          <button class="nav-item has-submenu" data-module="corehumancapital">
            <div class="nav-item-content">
              <i data-lucide="book-user"></i>
              <span>Core Human Capital</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-corehumancapital" style="max-height: 500px;">
            <a href="dispatch.php" class="submenu-item">
              <i data-lucide="send"></i>
              <span>Master Data Dispatch</span>
            </a>
            <a href="orgprofile.php" class="submenu-item">
              <i data-lucide="building-2"></i>
              <span>Organization Profile</span>
            </a>
            <a href="positioncatalog.php" class="submenu-item">
              <i data-lucide="user-star"></i>
              <span>Position Catalog</span>
            </a>
            <a href="employeemaster.php" class="submenu-item">
              <i data-lucide="file-user"></i>
              <span>Employee Master Files</span>
            </a>
            <a href="informationapproval.php" class="submenu-item">
              <i data-lucide="file-check"></i>
              <span>Information Approval</span>
            </a>
            <a href="bankform.php" class="submenu-item">
              <i data-lucide="file-text"></i>
              <span>Bank Form Management</span>
            </a>
            <a href="auditlogs.php" class="submenu-item">
              <i data-lucide="book-user"></i>
              <span>Audit Logs</span>
            </a>
          </div>
        </div>
        <div class="nav-item-group active">
          <button class="nav-item has-submenu" data-module="planning">
            <div class="nav-item-content">
              <i data-lucide="circle-pile"></i>
              <span>Compensation Planning</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-planning" style="max-height: 500px;">
            <a href="comintake.php" class="submenu-item">
              <i data-lucide="layout-dashboard"></i>
              <span>Master Data Intake</span>
            </a>
            <a href="salary.php" class="submenu-item">
              <i data-lucide="banknote"></i>
              <span>Salary & Scales Management</span>
            </a>
            <a href="statutory.php" class="submenu-item">
              <i data-lucide="scale"></i>
              <span>Statutory Contributions</span>
            </a>
            <a href="matrix.php" class="submenu-item">
              <i data-lucide="percent"></i>
              <span>Merit Matrix Structure</span>
            </a>
            <a href="allowance.php" class="submenu-item active">
              <i data-lucide="gift"></i>
              <span>Allowance Structure</span>
            </a>
            <a href="cycle.php" class="submenu-item">
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
            <a href="timesheet.php" class="submenu-item">
              <i data-lucide="calendar-check"></i>
              <span>Timesheet Management</span>
            </a>
            <a href="comperules.php" class="submenu-item">
              <i data-lucide="boxes"></i>
              <span>Compensation Rules</span>
            </a>
            <a href="payroll.php" class="submenu-item">
              <i data-lucide="play-circle"></i>
              <span>Payroll Processing</span>
            </a>
          </div>
        </div>
      </div>
      <div class="nav-section">
        <span class="nav-section-title">FINANCE</span>
        <div class="nav-item-group">
          <button class="nav-item has-submenu" data-module="budget">
            <div class="nav-item-content">
              <i data-lucide="hand-coins"></i>
              <span>Budget Management</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-budget">
            <a href="positionrequest.php" class="submenu-item">
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
          <!-- Allowances Tab -->
          <div class="tab-panel active" id="allowances">
            <div class="section-header" style="margin-bottom: 24px; display: flex; justify-content: space-between; align-items: flex-start; padding-bottom: 16px; border-bottom: 1px solid var(--border-color);">
              <div class="sh-info">
                <h3 style="font-size: 20px; font-weight: 700; color: var(--text-primary); margin-bottom: 8px;">Allowance & Benefit Structures</h3>
                <p style="color: var(--text-secondary); font-size: 14px;">Define standard non-taxable (De Minimis) and taxable allowances by employee grade.</p>
              </div>
              <div class="comp-panel-actions" style="display: flex; gap: 12px;">
              </div>
            </div>
            <div class="table-container">
              <table class="comp-table editable-table">
                <thead>
                  <tr>
                    <th>Grade Level</th>
                    <th>Grade Name</th>
                    <?php foreach ($allowance_types as $type): ?>
                    <th>
                      <div class="allowance-header">
                        <span><?php echo htmlspecialchars($type['AllowanceName']); ?></span>
                        <span class="tax-badge <?php echo $type['IsTaxable'] ? 'taxable' : 'non-taxable'; ?>">
                          <?php echo $type['IsTaxable'] ? 'Taxable' : 'De Minimis'; ?>
                        </span>
                        <small><?php echo ($type['Frequency'] == 'Annual') ? 'Annual' : 'Monthly'; ?></small>
                      </div>
                    </th>
                    <?php endforeach; ?>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($salary_grades as $grade): ?>
                  <tr>
                    <td><strong><?php echo htmlspecialchars($grade['GradeLevel']); ?></strong></td>
                    <td><?php echo htmlspecialchars($grade['GradeName']); ?></td>
                    <?php foreach ($allowance_types as $type): ?>
                    <?php 
                      $amount = $grade_allowance_map[$grade['SalaryGradeID']][$type['AllowanceTypeID']] ?? 0;
                    ?>
                    <td>
                      <div class="input-with-symbol">
                        <span>&#8369;</span>
                        <input type="number" 
                               value="<?php echo (int)$amount; ?>" 
                               class="table-input-premium allowance-val-input"
                               data-grade="<?php echo $grade['SalaryGradeID']; ?>"
                               data-type="<?php echo $type['AllowanceTypeID']; ?>"
                               data-is-taxable="<?php echo $type['IsTaxable']; ?>">
                      </div>
                    </td>
                    <?php endforeach; ?>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
    </div>
    

          </div>
        </div>
      </div>
  </main>
  <script src="../../js/compensationdashboard.js?v=1.3"></script>
  <script src="../../js/admin_allowance.js"></script>
  <script>
    lucide.createIcons();
  </script>
</body>
</html>







