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
$target_employees_query = $conn->query("SELECT COUNT(*) as total FROM employmentinformation WHERE EmploymentStatus = 'Regular'");
$target_employees = ($target_employees_query) ? $target_employees_query->fetch_assoc()['total'] : 0;

$max_increase_query = $conn->query("SELECT MAX(max_increase_pct) as max_target FROM merit_matrix_settings WHERE period_id = $period_id");
$max_increase = ($max_increase_query && $max_increase_query->num_rows > 0) ? number_format($max_increase_query->fetch_assoc()['max_target'], 1) : "0.0";

$baseline_payroll_query = $conn->query("SELECT SUM(BaseSalary) as base_total FROM employmentinformation WHERE EmploymentStatus = 'Regular'");
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
        SELECT EmployeeID, FinalRating
        FROM final_performance_rating
        ORDER BY period_id DESC
    ) fpr ON e.EmployeeID = fpr.EmployeeID
    WHERE ei.EmploymentStatus = 'Regular'
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
  <link rel="stylesheet" href="../../css/cycle.css?v=3.5">
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
        <span class="nav-section-title">MAIN MENU</span>
        
        <a href="dashboard.php" class="nav-item">
          <i data-lucide="layout-dashboard"></i>
          <span>Dashboard</span>
        </a>

        <div class="nav-item-group">
          <button class="nav-item has-submenu" data-module="hr">
            <div class="nav-item-content">
              <i data-lucide="book-user"></i>
              <span>Core Human Capital</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-hr">
            <a href="" class="submenu-item">
              <i data-lucide="user-plus"></i>
              <span>New Hired Onboard Request</span>
            </a>
            <a href="employeemaster.php" class="submenu-item">
              <i data-lucide="file-user"></i>
              <span>Employee Master Files</span>
            </a>
            <a href="bankform.php" class="submenu-item">
              <i data-lucide="file-text"></i>
              <span>Bank Form Management</span>
            </a>
            <a href="" class="submenu-item">
              <i data-lucide="user-cog"></i>
              <span>Security Settings</span>
            </a>
            <a href="auditlogs.php" class="submenu-item">
              <i data-lucide="book-user"></i>
              <span>Audit Logs</span>
            </a>
          </div>
        </div>

        <div class="nav-item-group active">
          <button class="nav-item has-submenu active" data-module="planning">
            <div class="nav-item-content">
              <i data-lucide="circle-pile"></i>
              <span>Compensation Planning</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-planning" style="max-height: 500px;">
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
            <a href="allowance.php" class="submenu-item">
              <i data-lucide="gift"></i>
              <span>Allowance Structure</span>
            </a>
            <a href="cycle.php" class="submenu-item active">
              <i data-lucide="calculator"></i>
              <span>Simulation</span>
            </a>
            <a href="#" class="submenu-item">
              <i data-lucide="calendar-clock"></i>
              <span>Disbursements</span>
            </a>
            <a href="#" class="submenu-item">
              <i data-lucide="coins"></i>
              <span>Collections</span>
            </a>
          </div>
        </div>

           <div class="nav-item-group">
          <button class="nav-item has-submenu" data-module="payroll">
            <div class="nav-item-content">
              <i data-lucide="banknote-arrow-down"></i>
              <span>Payroll</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-payroll">
            <a href="#" class="submenu-item">
              <i data-lucide="file-plus"></i>
              <span>Applications</span>
            </a>
            <a href="#" class="submenu-item">
              <i data-lucide="check-circle"></i>
              <span>Approvals</span>
            </a>
            <a href="#" class="submenu-item">
              <i data-lucide="calendar-clock"></i>
              <span>Disbursements</span>
            </a>
            <a href="#" class="submenu-item">
              <i data-lucide="coins"></i>
              <span>Collections</span>
            </a>
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
        <div class="user-avatar" id="sidebarAvatar">
          <!-- Initials will be inserted by JS -->
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
      <div class="tabs-container">
        <div class="tabs-header">
          <button class="tab-btn active" data-tab="strategic">
            <i data-lucide="target"></i>
            <span>Strategic Planning</span>
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
                        <input type="number" id="budgetAllocation" value="5000000">
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

            <!-- Drafts Section -->
            <div class="drafts-section" style="margin-top: 32px;">
              <h3 style="font-size: 16px; font-weight: 600; color: var(--text-primary); margin-bottom: 16px;">Recent Drafts & In-Progress Cycles</h3>
              <div class="table-container">
                <table class="drafts-table w-full text-left" style="border-collapse: collapse;">
                  <thead>
                    <tr style="border-bottom: 1px solid var(--border-color); color: var(--text-tertiary); font-size: 12px; font-weight: 600; text-transform: uppercase;">
                      <th style="padding: 12px 16px;">Cycle Name</th>
                      <th style="padding: 12px 16px;">Date Started</th>
                      <th style="padding: 12px 16px;">Last Saved</th>
                      <th style="padding: 12px 16px;">Budget Used (%)</th>
                      <th style="padding: 12px 16px;">Status</th>
                      <th style="padding: 12px 16px; text-align: right;">Action</th>
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
                            <button class="btn btn-secondary btn-sm btn-continue-draft" data-draft-id="<?php echo $draft['DraftID']; ?>">Continue</button>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>

          </div>


          <!-- Simulation Tab -->
          <div class="tab-panel" id="simulation">
            <div class="simulation-dashboard">
              <div class="sim-dashboard-grid">
                <div class="sim-dash-box">
                  <div class="dash-box-label">Staff Count</div>
                  <div class="dash-box-value" id="simStaffCount">8 Active Employees</div>
                </div>
                <div class="sim-dash-box highlight-premium">
                  <div class="dash-box-label">Monthly Impact (Basic + ER)</div>
                  <div class="dash-box-value" id="totalMonthlyImpact">+&#8369;42,140.00</div>
                </div>
                <div class="sim-dash-box">
                  <div class="dash-box-label">Yearly Impact</div>
                  <div class="dash-box-value" id="totalYearlyImpact">&#8369;505,680.00</div>
                </div>
                <div class="sim-dash-box">
                  <div class="dash-box-label">Health Check (Avg. Compa-Ratio)</div>
                  <div class="dash-box-value" id="avgCompaRatio">82%</div>
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
                <button class="btn btn-secondary" id="saveDraftBtn">
                  <i data-lucide="save"></i>
                  <span>Save Draft</span>
                </button>
                <button class="btn btn-primary" id="submitProposalBtn">
                  <span>Submit</span>
                  <i data-lucide="check-circle"></i>
                </button>
              </div>
            </div>
            
            <div class="table-container">
               <table class="comp-table simulation-table">
                <thead>
                  <tr>
                    <th>EE ID</th>
                    <th>Name & Position</th>
                    <th>Rating</th>
                    <th>Status</th>
                    <th>Salary</th>
                    <th>Grade Midpoint</th>
                    <th>Compa-Ratio</th>
                    <th style="min-width:180px;">Promote</th>
                    <th>Prop. %</th>
                    <th>Prop. Increase (₱)</th>
                    <th>Basic (New)</th>
                    <th>Total Allowances</th>
                    <th>Gross Salary</th>
                    <th>Semi-Monthly</th>
                    <th>Daily</th>
                    <th>Hourly</th>
                    <th>Employer Share</th>
                    <th>Full Load</th>
                    <th>SSS Regular</th>
                    <th>SSS WISP</th>
                    <th>PhilHealth</th>
                    <th>Pag-IBIG</th>
                    <th>W. Tax</th>
                    <th>Net Pay</th>
                    <th>Increase</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($simulation_data as $emp): 
                    $initials = strtoupper(substr($emp['FirstName'] ?? 'U', 0, 1) . substr($emp['LastName'] ?? 'N', 0, 1));
                    $rating = $emp['FinalRating'] ?? 0;
                    $current_pay = (float)($emp['BaseSalary'] ?? 0);
                    $allowances = (float)($emp['TotalAllowances'] ?? 0);
                    $taxable_allowances = (float)($emp['TaxableAllowances'] ?? 0);
                    
                    // Find midpoint and max for this grade
                    $grade_id = $emp['SalaryGradeID'];
                    $midpoint = 0;
                    $max_salary = 0;
                    foreach($salary_grades as $g) {
                        if ($g['SalaryGradeID'] == $grade_id) {
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
                    } elseif (!empty($merit_matrix)) {
                        // Fallback: use lowest available rating entry
                        $fallback_key = min(array_keys($merit_matrix));
                        if (isset($merit_matrix[$fallback_key][$compa_range])) {
                            $recommended_pct = (float)$merit_matrix[$fallback_key][$compa_range]['min_increase_pct'];
                        }
                    }
                  ?>
                  <tr class="sim-row" 
                      data-ee-id="<?php echo $emp['EmployeeID']; ?>"
                      data-department="<?php echo htmlspecialchars($emp['DepartmentName'] ?? 'Unassigned'); ?>" 
                      data-taxable-allowances="<?php echo $taxable_allowances; ?>" 
                      data-grade-id="<?php echo $emp['SalaryGradeID']; ?>" 
                      data-base-salary="<?php echo $current_pay; ?>" 
                      data-rating="<?php echo $rating; ?>"
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
                    <td><span class="rating-badge rating-<?php echo floor($rating); ?>"><?php echo number_format($rating, 1); ?></span></td>
                    <td><span class="badge draft" style="font-size: 10px; padding: 2px 6px;">Draft</span></td>
                    <td class="current-pay">&#8369;<?php echo number_format($current_pay, 0); ?></td>
                    <td class="grade-midpoint">&#8369;<?php echo number_format($midpoint, 0); ?></td>
                    <td class="compa-ratio">0%</td>
                    <td class="promote-cell" style="min-width:180px; padding:8px 10px;">
                      <div class="promote-current-label" style="cursor:pointer; display:flex; align-items:center; justify-content:center; gap:6px; background:rgba(44,160,120,0.1); border:1px dashed #2ca078; padding:2px 8px; border-radius:4px; color:#2ca078; font-weight:600; font-size:12px;">
                        <span>SG-<?php echo $current_grade_level; ?></span>
                        <i data-lucide="chevron-down" style="width:12px;height:12px;"></i>
                      </div>
                      <div class="promote-inline" style="display:none; gap:6px; align-items:center; flex-wrap:nowrap;">
                        <select class="form-select promote-grade-select" style="font-size:11px; padding:3px 6px; min-width:120px;">
                          <?php foreach($salary_grades as $g): ?>
                            <option value="<?php echo $g['SalaryGradeID']; ?>" <?php echo ($g['SalaryGradeID'] == $grade_id) ? 'selected' : ''; ?>>
                              <?php echo htmlspecialchars($g['GradeLevel'] . ' – ' . $g['GradeName']); ?>
                            </option>
                          <?php endforeach; ?>
                        </select>
                        <button class="btn btn-primary btn-sm promote-inline-btn" title="Apply Promotion" style="font-size:11px; padding:3px 8px; white-space:nowrap;">
                           OK
                        </button>
                        <button class="btn btn-secondary btn-sm promote-cancel-btn" title="Cancel" style="font-size:11px; padding:3px 8px;">&times;</button>
                      </div>
                    </td>
                    <td>
                      <div class="input-tooltip-wrapper">
                        <input type="number" class="table-input prop-increase-input" value="0.0" step="0.5" max="100.0" min="0">%
                        <div class="input-tooltip">Grade Ceiling Reached - Promotion Required</div>
                      </div>
                    </td>
                    <td class="prop-increase-amount" contenteditable="true" style="color: #10b981; font-weight: 600;">+&#8369;0.00</td>
                    <td class="proposed-gross">&#8369;<?php echo number_format($current_pay, 0); ?></td>
                    <td class="total-allowances" data-value="<?php echo $allowances; ?>">&#8369;<?php echo number_format($allowances, 2); ?></td>
                    <td class="total-gross">&#8369;0.00</td>
                    <td class="rate-semi">&#8369;0.00</td>
                    <td class="rate-daily">&#8369;0.00</td>
                    <td class="rate-hourly">&#8369;0.00</td>
                    <td class="employer-share">&#8369;0.00</td>
                    <td class="full-load">&#8369;0.00</td>
                    <td class="deduction-sss">&#8369;0.00</td>
                    <td class="deduction-wisp" style="color: #ef4444; font-weight: 600;">&#8369;0.00</td>
                    <td class="deduction-ph">&#8369;0.00</td>
                    <td class="deduction-pi">&#8369;0.00</td>
                    <td class="deduction-tax">&#8369;0.00</td>
                    <td class="net-pay-cell">&#8369;0.00</td>
                    <td class="increase-cell" data-increase="0">+&#8369;0</td>
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
  <script src="../../js/cycle.js?v=3.5" defer></script>
  <script>
    lucide.createIcons();
  </script>
</body>
</html>







