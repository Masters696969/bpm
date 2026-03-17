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
$period_data = ($period_query) ? $period_query->fetch_assoc() : [];

// Fetch Merit Matrix (for Simulation)
$merit_query = $conn->query("
    SELECT performance_rating AS PerformanceRating, compa_ratio_range AS CompaRatioRange, min_increase_pct AS RecommendedIncrease
    FROM merit_matrix_settings 
    WHERE period_id = $period_id 
    ORDER BY performance_rating DESC, compa_ratio_range ASC
");
$merit_matrix = [];
if ($merit_query) {
    while ($row = $merit_query->fetch_assoc()) {
        $rating = number_format($row['PerformanceRating'], 1);
        $range = $row['CompaRatioRange'];
        if (!isset($merit_matrix[$rating])) $merit_matrix[$rating] = [];
        $merit_matrix[$rating][$range] = [
            'min_increase_pct' => $row['RecommendedIncrease']
        ];
    }
}

// Fetch Salary Grades
$grades_query = $conn->query("
    SELECT sg.SalaryGradeID, sg.GradeLevel, sg.GradeName, sg.MinSalary, sg.MaxSalary, sg.MidSalary, sg.Description
    FROM salary_grades sg 
    WHERE sg.period_id = $period_id 
    ORDER BY sg.SalaryGradeID ASC
");
$salary_grades = [];
if ($grades_query) {
    while ($row = $grades_query->fetch_assoc()) {
        $salary_grades[] = $row;
    }
}

// Fetch Statutory Settings
$sss_query = $conn->query("SELECT * FROM sss_settings WHERE period_id = $period_id");
$sss_data = ($sss_query) ? $sss_query->fetch_assoc() : [];

$ph_query = $conn->query("SELECT * FROM philhealth_settings WHERE period_id = $period_id");
$ph_data = ($ph_query) ? $ph_query->fetch_assoc() : [];

$pi_query = $conn->query("SELECT * FROM pagibig_settings WHERE period_id = $period_id");
$pi_data = ($pi_query) ? $pi_query->fetch_assoc() : [];

// Fetch BIR Tax Settings
$bir_query = $conn->query("SELECT * FROM bir_tax_settings WHERE period_id = $period_id");
$bir_data = ($bir_query) ? $bir_query->fetch_assoc() : [];

// Fetch Saved Drafts
$drafts_query = $conn->query("SELECT DraftID, CycleName, DateStarted, LastSaved, BudgetUsedPct, Status FROM simulation_drafts ORDER BY LastSaved DESC");
$saved_drafts = [];
if ($drafts_query) {
    while ($row = $drafts_query->fetch_assoc()) {
        $saved_drafts[] = $row;
    }
}

// Fetch Live Stats
$target_employees_query = $conn->query("SELECT COUNT(*) as total FROM employmentinformation WHERE EmploymentStatus IN ('Regular', 'Probationary')");
$target_employees = ($target_employees_query) ? $target_employees_query->fetch_assoc()['total'] : 0;

$max_increase_query = $conn->query("SELECT MAX(max_increase_pct) as max_target FROM merit_matrix_settings WHERE period_id = $period_id");
$max_increase = ($max_increase_query && $max_increase_query->num_rows > 0) ? number_format($max_increase_query->fetch_assoc()['max_target'], 1) : "0.0";

$baseline_payroll_query = $conn->query("SELECT SUM(BaseSalary) as base_total FROM employmentinformation WHERE EmploymentStatus IN ('Regular', 'Probationary')");
$baseline_payroll = ($baseline_payroll_query) ? $baseline_payroll_query->fetch_assoc()['base_total'] : 0.00;

function getGradeAllowances($conn) {
    $map = [];
    $res = $conn->query("SELECT * FROM grade_allowances");
    if ($res) {
        while($row = $res->fetch_assoc()) {
            $map[$row['SalaryGradeID']][$row['AllowanceTypeID']] = $row['Amount'];
        }
    }
    return $map;
}
function getAllowanceTaxableMap($conn) {
    $map = [];
    $res = $conn->query("SELECT AllowanceTypeID, IsTaxable FROM allowance_types");
    if ($res) {
        while($row = $res->fetch_assoc()) {
            $map[$row['AllowanceTypeID']] = $row['IsTaxable'];
        }
    }
    return $map;
}
$grade_allowance_map = getGradeAllowances($conn);
$allowance_taxable_map = getAllowanceTaxableMap($conn);

// Fetch Simulation Data
$simulation_query = $conn->query("
    SELECT 
        e.EmployeeID, e.EmployeeCode, e.FirstName, e.LastName,
        p.PositionName, d.DepartmentName,
        ei.BaseSalary, ei.SalaryGradeID, ei.DepartmentID,
        fpr.FinalRating
    FROM employee e
    JOIN employmentinformation ei ON e.EmployeeID = ei.EmployeeID
    LEFT JOIN positions p ON ei.PositionID = p.PositionID
    LEFT JOIN department d ON ei.DepartmentID = d.DepartmentID
    LEFT JOIN (
        SELECT r1.EmployeeID, r1.FinalRating
        FROM final_performance_rating r1
        INNER JOIN (
            SELECT EmployeeID, MAX(period_id) as MaxPeriod
            FROM final_performance_rating
            WHERE EvaluationStatus = 'Finalized'
            GROUP BY EmployeeID
        ) r2 ON r1.EmployeeID = r2.EmployeeID AND r1.period_id = r2.MaxPeriod
        WHERE r1.EvaluationStatus = 'Finalized'
    ) fpr ON e.EmployeeID = fpr.EmployeeID
    WHERE ei.EmploymentStatus IN ('Regular', 'Probationary')
    ORDER BY e.EmployeeCode ASC
");
$simulation_data = [];
if ($simulation_query) {
    while ($row = $simulation_query->fetch_assoc()) {
    $total_allow = 0;
    $taxable_allow = 0;
    if (isset($grade_allowance_map[$row['SalaryGradeID']])) {
        foreach ($grade_allowance_map[$row['SalaryGradeID']] as $type_id => $amt) {
            $total_allow += $amt;
            if (isset($allowance_taxable_map[$type_id]) && $allowance_taxable_map[$type_id]) {
                $taxable_allow += $amt;
            }
        }
    }
    $row['TotalAllowances'] = $total_allow;
    $row['TaxableAllowances'] = $taxable_allow;
    $simulation_data[] = $row;
}
}

// Fetch all departments for filter
$dept_query = $conn->query("SELECT DepartmentID, DepartmentName FROM department ORDER BY DepartmentName ASC");
$departments = [];
while ($d = ($dept_query) ? $dept_query->fetch_assoc() : null) {
    if ($d) $departments[] = $d;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Simulation</title>
  <link rel="stylesheet" href="../../css/admin_cycle.css?v=3.8">
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
        <div class="user-avatar" id="sidebarAvatar">
          <?php echo strtoupper(substr($_SESSION['username'] ?? 'A', 0, 1)); ?>
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
        <div class="header-notifications" style="position: relative;">
          <button class="icon-btn" id="bellIconBtn">
            <i data-lucide="bell"></i>
            <span class="notification-badge" id="notifBadge" style="display: none; position: absolute; top: 0; right: 0; background: #ef4444; color: white; border-radius: 50%; padding: 2px 6px; font-size: 10px; font-weight: bold;">0</span>
          </button>
          <div class="notification-dropdown" id="notifDropdown" style="display: none; position: absolute; top: 100%; right: 0; width: 320px; background: var(--surface-color, white); border: 1px solid var(--border-color, #e5e7eb); border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06); z-index: 1000; max-height: 400px; overflow-y: auto;">
            <div style="padding: 12px 16px; border-bottom: 1px solid var(--border-color, #e5e7eb); font-weight: 600; display: flex; justify-content: space-between; align-items: center; color: var(--text-primary, #111827);">
              Notifications
              <button id="markReadBtn" style="background: none; border: none; color: #2ca078; cursor: pointer; font-size: 12px; font-weight: 500;">Mark all as read</button>
            </div>
            <div id="notifList" style="padding: 0;">
              <div style="padding: 16px; text-align: center; color: var(--text-secondary, #6b7280); font-size: 14px;">Loading notifications...</div>
            </div>
          </div>
        </div>
      </div>
    </header>

    <div class="content-wrapper">
      <!-- Compensation Planning Tabs -->
      <div style="margin-bottom: 24px;">
        <div class="premium-tabs">
          <button class="tab-btn active" data-tab="strategic">
            <i data-lucide="target"></i>
            <span>Strategic Planning</span>
          </button>
          <button class="tab-btn" data-tab="salary-scale">
            <i data-lucide="layers"></i>
            <span>Salary Scale</span>
          </button>
          <button class="tab-btn" data-tab="simulation">
            <i data-lucide="calculator"></i>
            <span>Simulation</span>
          </button>
        </div>

        <div class="tabs-content">
          <!-- Strategic Planning Tab -->
          <div class="tab-panel active" id="strategic">
            <div class="planning-grid">
              <div class="planning-card main">
                <div class="p-card-header">
                  <h3>Cycle Configuration</h3>
                  <span class="badge <?php echo strtolower($period_data['status'] ?? 'Draft'); ?>">
                    <?php echo htmlspecialchars($period_data['status'] ?? 'Draft'); ?>
                  </span>
                </div>
                <div class="p-card-body">
                  <div class="form-grid">
                    <div class="form-group">
                      <label>Planning Cycle Name</label>
                      <input type="text" value="<?php echo htmlspecialchars($period_data['period_name'] ?? 'FY2025 Annual Merit Review'); ?>" placeholder="Enter cycle name...">
                    </div>
                    <div class="form-group" style="grid-column: span 2;">
                      <label>Total Budget Allocation</label>
                      <div class="input-with-symbol">
                        <span>&#8369;</span>
                        <input type="number" id="budgetAllocation" value="<?php echo (int)($period_data['budget_approved_amount'] > 0 ? $period_data['budget_approved_amount'] : 5000000); ?>">
                      </div>
                    </div>
                    <div class="form-group">
                      <label>Cycle Start Date</label>
                      <input type="date" id="cycleStartDate" value="<?php echo htmlspecialchars($period_data['start_date'] ?? date('Y-m-d')); ?>">
                    </div>
                    <div class="form-group">
                      <label>Effective Date</label>
                      <input type="date" id="effectiveDate" value="<?php echo htmlspecialchars($period_data['effective_date'] ?? '2026-03-01'); ?>">
                    </div>
                  </div>
                  <div class="action-buttons" style="margin-top: 24px;">
                    <button class="btn btn-primary" id="startCycleBtn" style="width: 100%; max-width: 300px; justify-content: center;">
                      <span>Start Simulation Cycle</span>
                      <i data-lucide="arrow-right"></i>
                    </button>
                  </div>
                </div>
              </div>
              <div class="planning-stats" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="mini-stat-card">
                  <span class="ms-label">Eligible Headcount</span>
                  <span class="ms-value"><?php echo number_format($target_employees); ?></span>
                </div>
                <div class="mini-stat-card">
                  <span class="ms-label">Matrix Cap</span>
                  <span class="ms-value"><?php echo $max_increase; ?>%</span>
                </div>
                <div class="mini-stat-card">
                  <span class="ms-label">Baseline Payroll</span>
                  <span class="ms-value">&#8369;<?php echo number_format($baseline_payroll, 2); ?></span>
                </div>
                <div class="mini-stat-card">
                  <span class="ms-label">Statutory Version</span>
                  <span class="ms-value" style="font-size: 14px;">2026 SSS/PH Tables</span>
                </div>
              </div>
            </div>

            <!-- Drafts Section at Bottom (Full Width) -->
            <div class="drafts-section" style="margin-top: 32px;">
              <h3 style="font-size: 16px; font-weight: 600; color: var(--text-primary); margin-bottom: 16px;">Recent Drafts & In-Progress Cycles</h3>
              <div class="payroll-table-container">
                <table class="payroll-table w-full text-left" style="border-collapse: collapse;">
                  <thead>
                    <tr>
                      <th>Cycle Name</th>
                      <th>Date Started</th>
                      <th>Last Saved</th>
                      <th>Budget Used (%)</th>
                      <th>Status</th>
                      <th style="text-align: right;">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (empty($saved_drafts)): ?>
                      <tr>
                        <td colspan="6" style="padding: 24px; text-align: center; color: var(--text-tertiary);">No recent drafts found.</td>
                      </tr>
                    <?php else: ?>
                      <?php foreach ($saved_drafts as $draft): ?>
                        <tr style="border-bottom: 1px solid var(--border-color); font-size: 13px;">
                          <td style="padding: 12px 16px; font-weight: 500; color: var(--text-primary);"><?php echo htmlspecialchars($draft['CycleName']); ?></td>
                          <td style="padding: 12px 16px; color: var(--text-secondary);"><?php echo date('M j, Y h:i A', strtotime($draft['DateStarted'])); ?></td>
                          <td style="padding: 12px 16px; color: var(--text-secondary);"><?php echo date('M j, Y h:i A', strtotime($draft['LastSaved'])); ?></td>
                          <td style="padding: 12px 16px; font-weight: 600; color: <?php echo ($draft['BudgetUsedPct'] > 100) ? '#ef4444' : '#10b981'; ?>;">
                            <?php echo number_format($draft['BudgetUsedPct'], 1); ?>%
                          </td>
                          <td style="padding: 12px 16px;">
                            <span class="badge <?php echo strtolower($draft['Status'] ?? 'Draft'); ?>" style="font-size: 11px; padding: 4px 8px;">
                              <?php echo htmlspecialchars($draft['Status'] ?? 'Draft'); ?>
                            </span>
                          </td>
                          <td style="padding: 12px 16px; text-align: right;">
                            <div style="display: flex; gap: 6px; justify-content: flex-end; align-items: center;">
                              <?php if (($draft['Status'] ?? 'Draft') === 'Draft'): ?>
                                <button class="btn btn-secondary btn-sm btn-continue-draft" data-draft-id="<?php echo $draft['DraftID']; ?>" title="Continue Editing" style="padding: 4px 8px; font-size: 11px;">
                                   <i data-lucide="play" style="width:12px; height:12px; margin-right:4px;"></i>Continue
                                </button>
                                <button class="btn btn-blue btn-sm btn-view-draft" data-draft-id="<?php echo $draft['DraftID']; ?>" data-cycle-name="<?php echo htmlspecialchars($draft['CycleName']); ?>" style="padding: 4px 8px; font-size: 11px; background: rgba(59, 130, 246, 0.1); color: #3b82f6; border: 1px solid #3b82f6;">
                                   <i data-lucide="eye" style="width:12px; height:12px; margin-right:4px;"></i>View
                                </button>
                              <?php else: ?>
                                <button class="btn btn-blue btn-sm btn-track-proposal" data-cycle-name="<?php echo htmlspecialchars($draft['CycleName']); ?>" style="padding: 4px 8px; font-size: 11px;">
                                  <i data-lucide="activity" style="width:12px; height:12px; margin-right:4px;"></i>Track
                                </button>
                                <button class="btn btn-secondary btn-sm btn-view-proposal" data-cycle-name="<?php echo htmlspecialchars($draft['CycleName']); ?>" style="padding: 4px 8px; font-size: 11px; background: rgba(107, 114, 128, 0.1); color: #6b7280; border: 1px solid #6b7280;">
                                   <i data-lucide="eye" style="width:12px; height:12px; margin-right:4px;"></i>View
                                </button>
                              <?php endif; ?>
                            </div>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>


          <!-- Salary Scale Tab -->
          <div class="tab-panel" id="salary-scale">
            <section class="sim-dashboard" style="margin-bottom: 24px; padding: 24px; background: var(--surface-color, white); border-radius: 12px; border: 1px solid var(--border-color, #e5e7eb);">
              <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                <div>
                  <h3 style="font-size: 18px; font-weight: 600; color: var(--text-primary);">Regional Salary Scales (Simulation)</h3>
                  <p style="font-size: 13px; color: var(--text-secondary);">Adjust base pay ranges for this simulation cycle.</p>
                </div>
              </div>

              <div class="payroll-table-container">
                <table class="payroll-table sim-salary-table">
                  <thead>
                    <tr>
                      <th>Job Grade</th>
                      <th>Level Name</th>
                      <th>Minimum (Monthly)</th>
                      <th>Midpoint</th>
                      <th>Maximum (Monthly)</th>
                      <th>Spread</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($salary_grades as $grade): ?>
                    <tr class="sim-grade-row" data-grade-id="<?php echo $grade['SalaryGradeID']; ?>">
                      <td style="font-weight: 600;"><?php echo htmlspecialchars($grade['GradeLevel']); ?></td>
                      <td><?php echo htmlspecialchars($grade['GradeName']); ?></td>
                      <td>
                        <div class="input-with-symbol">
                          <span>&#8369;</span>
                          <input type="number" value="<?php echo (int)$grade['MinSalary']; ?>" class="table-input-premium sim-min-salary" style="width: 120px;">
                        </div>
                      </td>
                      <td>
                        <div class="input-with-symbol">
                          <span>&#8369;</span>
                          <input type="number" value="<?php echo (int)$grade['MidSalary']; ?>" class="table-input-premium sim-mid-salary" style="width: 120px;" readonly>
                        </div>
                      </td>
                      <td>
                        <div class="input-with-symbol">
                          <span>&#8369;</span>
                          <input type="number" value="<?php echo (int)$grade['MaxSalary']; ?>" class="table-input-premium sim-max-salary" style="width: 120px;">
                        </div>
                      </td>
                      <td class="sim-spread-cell">
                        <?php 
                          $min = (float)$grade['MinSalary'];
                          $max = (float)$grade['MaxSalary'];
                          $spread = ($min > 0) ? (($max - $min) / $min) * 100 : 0;
                          echo number_format($spread, 1); 
                        ?>%
                      </td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </section>
          </div>


          <!-- Simulation Tab -->
          <div class="tab-panel" id="simulation">
            <div class="simulation-dashboard" style="margin-bottom: 24px;">
              <div class="sim-dashboard-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px;">
                <div class="stat-card-premium" style="padding: 12px 16px;">
                  <div class="stat-icon-wrapper" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6; width: 32px; height: 32px;">
                    <i data-lucide="users" style="width: 16px; height: 16px;"></i>
                  </div>
                  <div class="stat-info">
                    <span class="stat-label" style="font-size: 11px;">Staff Count</span>
                    <h3 class="stat-value" id="simStaffCount" style="font-size: 18px;"><?php echo count($simulation_data); ?> Active</h3>
                  </div>
                </div>
                
                <div class="stat-card-premium" style="padding: 12px 16px;">
                  <div class="stat-icon-wrapper" style="background: rgba(44, 160, 120, 0.1); color: var(--brand-green); width: 32px; height: 32px;">
                    <i data-lucide="trending-up" style="width: 16px; height: 16px;"></i>
                  </div>
                  <div class="stat-info">
                    <span class="stat-label" style="font-size: 11px;">Monthly Impact (Basic + ER)</span>
                    <h3 class="stat-value" id="totalMonthlyImpact" style="font-size: 18px;">+&#8369;42,140</h3>
                  </div>
                </div>

                <div class="stat-card-premium" style="padding: 12px 16px;">
                  <div class="stat-icon-wrapper" style="background: rgba(245, 158, 11, 0.1); color: var(--brand-yellow); width: 32px; height: 32px;">
                    <i data-lucide="calendar" style="width: 16px; height: 16px;"></i>
                  </div>
                  <div class="stat-info">
                    <span class="stat-label" style="font-size: 11px;">Yearly Impact</span>
                    <h3 class="stat-value" id="totalYearlyImpact" style="font-size: 18px;">&#8369;505,680</h3>
                  </div>
                </div>

                <div class="stat-card-premium" style="padding: 12px 16px;">
                  <div class="stat-icon-wrapper" style="background: rgba(139, 92, 246, 0.1); color: #8b5cf6; width: 32px; height: 32px;">
                    <i data-lucide="activity" style="width: 16px; height: 16px;"></i>
                  </div>
                  <div class="stat-info">
                    <span class="stat-label" style="font-size: 11px;">Avg. Compa-Ratio</span>
                    <h3 class="stat-value" id="avgCompaRatio" style="font-size: 18px;">82%</h3>
                  </div>
                </div>

                <!-- Budget Tracking Card -->
                <div class="stat-card-premium" style="padding: 12px 16px; border-left: 4px solid #3b82f6;">
                  <div class="stat-icon-wrapper" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6; width: 32px; height: 32px;">
                    <i data-lucide="wallet" style="width: 16px; height: 16px;"></i>
                  </div>
                  <div class="stat-info">
                    <span class="stat-label" style="font-size: 11px;">Remaining Budget</span>
                    <h3 class="stat-value" id="remainingBudget" style="font-size: 18px;">&#8369;0</h3>
                  </div>
                </div>
              </div>
            </div>

            <div class="simulation-header" style="margin-top: 24px;">
              <div class="sim-filters">
                <select class="form-select" id="deptFilter">
                  <option value="all">All Departments</option>
                  <?php foreach($departments as $dept): ?>
                    <option value="<?php echo htmlspecialchars($dept['DepartmentName']); ?>"><?php echo htmlspecialchars($dept['DepartmentName']); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="sim-actions" style="display: flex; gap: 8px; align-items: center;">
                <button class="btn btn-secondary" id="runAutoSim">
                  <i data-lucide="wand-2"></i>
                  <span>Run Auto-Simulation</span>
                </button>
                <button class="btn btn-blue" id="sendProposalBtn">
                  <i data-lucide="send"></i>
                  <span>Send Proposal</span>
                </button>
                <button class="btn btn-secondary" id="saveDraftBtn">
                  <i data-lucide="save"></i>
                  <span>Save Draft</span>
                </button>
              </div>
            </div>
            
            <div class="payroll-table-container" style="overflow-x: auto;">
               <table class="payroll-table simulation-table" style="white-space: nowrap;">
                <thead>
                  <tr>
                    <th>EE ID</th>
                    <th>Name</th>
                    <th>Rating</th>
                    <th>Salary</th>
                    <th>Band Status</th>
                    <th>Current Grade</th>
                    <th>Promote To</th>
                    <th style="color: var(--brand-blue);">Min</th>
                    <th style="color: var(--brand-blue);">Mid</th>
                    <th style="color: var(--brand-blue);">Max</th>
                    <th>Compa Ratio</th>
                    <th>Adjustment</th>
                    <th>Merit %</th>
                    <th style="color: var(--brand-green);">Increase</th>
                    <th>New Salary</th>
                    <th style="color: var(--brand-blue);">Impact</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($simulation_data as $emp): 
                    $initials = strtoupper(substr($emp['FirstName'] ?? 'U', 0, 1) . substr($emp['LastName'] ?? 'N', 0, 1));
                    $rating = $emp['FinalRating'] ?? 0;
                    $current_pay = (float)($emp['BaseSalary'] ?? 0);
                    $allowances = (float)($emp['TotalAllowances'] ?? 0);
                    $taxable_allowances = (float)($emp['TaxableAllowances'] ?? 0);
                    
                    // Find min, midpoint and max for this grade
                    $grade_id = $emp['SalaryGradeID'];
                    $min_salary = 0;
                    $midpoint = 0;
                    $max_salary = 0;
                    foreach($salary_grades as $g) {
                        if ($g['SalaryGradeID'] == $grade_id) {
                            $min_salary = (float)$g['MinSalary'];
                            $midpoint = (float)$g['MidSalary'];
                            $max_salary = (float)$g['MaxSalary'];
                            $current_grade_level = $g['GradeLevel'];
                            break;
                        }
                    }

                    // Pre-compute recommended increase % from merit matrix (server-side)
                    $recommended_pct = 0;
                    $compa_ratio = ($midpoint > 0) ? ($current_pay / $midpoint) : 1;
                    $compa_range = 'Mid';
                    if ($compa_ratio < 0.90) $compa_range = 'Low';
                    elseif ($compa_ratio > 1.10) $compa_range = 'High';
                    $rating_key = number_format((float)$rating, 1);
                    if (isset($merit_matrix[$rating_key][$compa_range])) {
                        $recommended_pct = (float)$merit_matrix[$rating_key][$compa_range]['min_increase_pct'];
                    }
                  ?>
                  <tr class="sim-row" 
                      data-ee-id="<?php echo $emp['EmployeeID']; ?>"
                      data-department="<?php echo htmlspecialchars($emp['DepartmentName'] ?? 'Unassigned'); ?>" 
                      data-taxable-allowances="<?php echo $taxable_allowances; ?>" 
                      data-grade-id="<?php echo $emp['SalaryGradeID']; ?>" 
                      data-original-base="<?php echo $current_pay; ?>"
                      data-base-salary="<?php echo $current_pay; ?>" 
                      data-rating="<?php echo $rating; ?>"
                      data-min-salary="<?php echo $min_salary; ?>"
                      data-midpoint="<?php echo $midpoint; ?>"
                      data-max-salary="<?php echo $max_salary; ?>"
                      data-recommended-pct="<?php echo $recommended_pct; ?>">
                    <td>
                      <div class="user-cell-stacked">
                        <div class="user-avatar-sm"><?php echo $initials; ?></div>
                        <div class="ee-code-group">
                          <span class="u-code"><?php echo htmlspecialchars($emp['EmployeeCode'] ?? '---'); ?></span>
                        </div>
                      </div>
                    </td>
                    <td>
                      <div class="user-info">
                        <span class="u-name-premium"><?php echo htmlspecialchars(($emp['FirstName'] ?? '') . ' ' . ($emp['LastName'] ?? '')); ?></span>
                        <span class="u-pos" style="font-size: 11px;"><?php echo htmlspecialchars($emp['PositionName'] ?? 'Position Not Set'); ?></span>
                      </div>
                    </td>
                    <td><span class="rating-badge rating-<?php echo floor($rating); ?>"><?php echo ($rating > 0) ? number_format($rating, 1) : 'N/A'; ?></span></td>
                     <td class="current-pay">&#8369;<?php echo number_format($current_pay, 0); ?></td>
                    <td class="band-status-cell">
                      <span class="badge" style="font-size: 10px; padding: 2px 6px;">---</span>
                    </td>
                    <td class="grade-label" style="font-weight: 600; color: var(--brand-green);">SG-<?php echo str_replace('SG-', '', $current_grade_level); ?></td>
                    <td class="promote-cell">
                      <div class="promote-current-label" style="cursor:pointer; display:flex; align-items:center; justify-content:center; gap:6px; background:rgba(44,160,120,0.1); border:1px dashed #2ca078; padding:2px 8px; border-radius:4px; color:#2ca078; font-weight:600; font-size:12px;" onclick="togglePromoteInline(this)">
                        <span>No Promotion</span>
                        <i data-lucide="chevron-down" style="width:12px;height:12px;"></i>
                      </div>
                      <div class="promote-inline" style="display:none; gap:6px; align-items:center; flex-wrap:nowrap; margin-top: 4px;">
                        <select class="form-select promote-grade-select" style="font-size:11px; padding:3px 6px; min-width:100px;">
                          <option value="<?php echo $grade_id; ?>">No Promotion</option>
                          <?php foreach($salary_grades as $g): ?>
                            <?php if ($g['SalaryGradeID'] != $grade_id): ?>
                              <option value="<?php echo $g['SalaryGradeID']; ?>">
                                SG-<?php echo str_replace('SG-', '', htmlspecialchars($g['GradeLevel'])); ?>
                              </option>
                            <?php endif; ?>
                          <?php endforeach; ?>
                        </select>
                        <button class="btn btn-primary btn-sm promote-inline-btn" title="Apply Promotion" style="font-size:10px; padding:2px 4px;">OK</button>
                        <button class="btn btn-secondary btn-sm promote-cancel-btn" title="Cancel" style="font-size:10px; padding:2px 4px;">&times;</button>
                      </div>
                    </td>
                    <td class="grade-min" style="color: var(--text-secondary); opacity: 0.8;">&#8369;<?php echo number_format($min_salary, 0); ?></td>
                    <td class="grade-midpoint" style="color: var(--text-secondary); opacity: 0.8;">&#8369;<?php echo number_format($midpoint, 0); ?></td>
                    <td class="grade-max" style="color: var(--text-secondary); opacity: 0.8;">&#8369;<?php echo number_format($max_salary, 0); ?></td>
                    <td class="compa-ratio" style="font-weight: 600;">0%</td>
                    <td class="market-adjustment" style="color: #f59e0b; font-weight: 600;">&#8369;0</td>
                    <td>
                      <input type="number" class="table-input prop-increase-input" value="" placeholder="0.0" step="0.1" style="width: 60px;" <?php echo ($rating <= 0) ? 'readonly disabled' : ''; ?>> %
                      <?php if($rating > 0): ?>
                        <div class="merit-range-hint" style="font-size: 9px; color: var(--text-tertiary); margin-top: 2px;"></div>
                      <?php endif; ?>
                    </td>
                    <td class="prop-increase-amount" style="color: #10b981; font-weight: 600;">&#8369;0</td>
                    <td class="proposed-gross" style="font-weight: 600;">&#8369;<?php echo number_format($current_pay, 0); ?></td>
                    <td class="row-impact" style="color: var(--brand-blue); font-weight: 600;">&#8369;0</td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <!-- Drafts Section moved back to Strategic Planning tab -->
    </div>

  </main>


  <script>
    // Expose Compensation Configuration to JS for Simulation
    window.compConfig = {
        meritMatrix: <?php echo json_encode($merit_matrix); ?>,
        salaryGrades: <?php echo json_encode($salary_grades); ?>,
        sssTable: <?php 
            $sss_table_q = $conn->query("SELECT min_salary, max_salary, ee_regular, er_regular, ee_wisp, er_wisp FROM sss_table ORDER BY min_salary ASC");
            $sss_table_data = [];
            if ($sss_table_q) { while($r = $sss_table_q->fetch_assoc()) $sss_table_data[] = $r; }
            echo json_encode($sss_table_data);
        ?>,
        sss: <?php echo json_encode($sss_data); ?>,
        philhealth: <?php echo json_encode($ph_data); ?>,
        pagibig: <?php echo json_encode($pi_data); ?>,
        tax: <?php echo json_encode($bir_data); ?>
    };
  </script>
  <script src="../../js/admin_cycle.js?v=3.5" defer></script>
  <script>
    lucide.createIcons();
  </script>
  <!-- Proposal Tracking Modal -->
  <div id="trackingModal" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; backdrop-filter: blur(4px);">
    <div class="modal-content-premium" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 90%; max-width: 900px; max-height: 90vh; background: var(--surface-color, white); border-radius: 16px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); overflow: hidden; display: flex; flex-direction: column;">
      <div class="modal-header" style="padding: 20px 24px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; background: #f8fafc;">
        <div>
          <h3 style="font-size: 18px; font-weight: 700; color: var(--text-primary);">Proposal Tracking View</h3>
          <p id="trackCycleName" style="font-size: 13px; color: var(--text-secondary);"></p>
        </div>
        <button class="close-modal-btn" onclick="document.getElementById('trackingModal').style.display='none'" style="background: none; border: none; font-size: 24px; cursor: pointer; color: var(--text-secondary);">&times;</button>
      </div>
      <div class="modal-body" style="padding: 24px; overflow-y: auto;">
        <div id="trackingStatus" style="margin-bottom: 24px; padding: 16px; border-radius: 12px; background: rgba(59, 130, 246, 0.05); border: 1px solid rgba(59, 130, 246, 0.1);">
           <div style="display: flex; justify-content: space-between; align-items: center;">
              <div style="display: flex; gap: 12px; align-items: center;">
                 <div id="statusIcon" style="width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: #3b82f6; color: white;">
                    <i data-lucide="send"></i>
                 </div>
                 <div>
                    <div style="font-weight: 600; color: var(--text-primary); font-size: 15px;">Proposal Status: <span id="trackStatusBadge">N/A</span></div>
                    <div style="font-size: 12px; color: var(--text-secondary);">Finance Reference: <span id="trackFinanceRef" style="font-family: monospace; font-weight: 600;">---</span></div>
                 </div>
              </div>
              <div style="text-align: right;">
                 <div style="font-size: 12px; color: var(--text-secondary);">Last Sync</div>
                 <div id="trackLastSync" style="font-weight: 600; color: var(--text-primary);">---</div>
              </div>
           </div>
        </div>

        <div class="tracking-details-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
           <div style="padding: 16px; border-radius: 12px; border: 1px solid var(--border-color);">
              <h4 style="font-size: 13px; color: var(--text-secondary); margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Financial Summary</h4>
              <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                 <span style="font-size: 14px; color: var(--text-secondary);">Total Budget:</span>
                 <span id="trackBudget" style="font-weight: 600; color: var(--text-primary);">&#8369;0</span>
              </div>
              <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                 <span style="font-size: 14px; color: var(--text-secondary);">Monthly Impact:</span>
                 <span id="trackImpact" style="font-weight: 600; color: var(--brand-blue);">&#8369;0</span>
              </div>
              <div style="display: flex; justify-content: space-between;">
                 <span style="font-size: 14px; color: var(--text-secondary);">Staff Count:</span>
                 <span id="trackEECount" style="font-weight: 600; color: var(--text-primary);">0 EE</span>
              </div>
           </div>
           <div style="padding: 16px; border-radius: 12px; border: 1px solid var(--border-color);">
              <h4 style="font-size: 13px; color: var(--text-secondary); margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Proposal Context</h4>
              <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                 <span style="font-size: 14px; color: var(--text-secondary);">Department:</span>
                 <span id="trackDept" style="font-weight: 600; color: var(--text-primary);">---</span>
              </div>
              <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                 <span style="font-size: 14px; color: var(--text-secondary);">Proposed By:</span>
                 <span id="trackBy" style="font-weight: 600; color: var(--text-primary);">---</span>
              </div>
              <div style="display: flex; justify-content: space-between;">
                 <span style="font-size: 14px; color: var(--text-secondary);">Proposal ID:</span>
                 <span id="trackPropID" style="font-weight: 600; color: var(--text-primary);">#0</span>
              </div>
           </div>
        </div>

        <div class="line-items-preview">
           <h4 style="font-size: 14px; font-weight: 600; color: var(--text-primary); margin-bottom: 12px;">Detailed Employee Adjustments</h4>
           <div class="payroll-table-container" style="max-height: 300px; overflow-y: auto;">
              <table class="payroll-table" style="width: 100%;">
                 <thead>
                     <tr>
                        <th>EE Name</th>
                        <th>Old Salary</th>
                        <th>Structure Adj</th>
                        <th>Merit %</th>
                        <th>Increase</th>
                        <th>New Salary</th>
                     </tr>
                 </thead>
                 <tbody id="trackItemsList">
                    <!-- Loaded dynamically -->
                 </tbody>
              </table>
           </div>
        </div>
      </div>
      <div class="modal-footer" style="padding: 16px 24px; border-top: 1px solid var(--border-color); display: flex; justify-content: flex-end; gap: 12px; background: #f8fafc;">
         <button class="btn btn-secondary" onclick="document.getElementById('trackingModal').style.display='none'">Close View</button>
      </div>
    </div>
  </div>
</body>
</html>







