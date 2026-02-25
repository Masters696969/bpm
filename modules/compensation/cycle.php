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
$grades_query = $conn->query("SELECT SalaryGradeID, GradeLevel, GradeName, MinSalary, MaxSalary, MidSalary, Description FROM salary_grades WHERE period_id = $period_id ORDER BY SalaryGradeID ASC");
$salary_grades = [];
while ($row = $grades_query->fetch_assoc()) {
    $salary_grades[] = $row;
}

// Fetch Statutory Settings
$sss_query = $conn->query("SELECT * FROM sss_settings WHERE period_id = $period_id");
$sss_data = $sss_query->fetch_assoc();

$ph_query = $conn->query("SELECT * FROM philhealth_settings WHERE period_id = $period_id");
$ph_data = $ph_query->fetch_assoc();

$pi_query = $conn->query("SELECT * FROM pagibig_settings WHERE period_id = $period_id");
$pi_data = $pi_query->fetch_assoc();

$bir_query = $conn->query("SELECT * FROM bir_tax_settings WHERE period_id = $period_id");
$bir_data = $bir_query->fetch_assoc();

// Fetch Merit Matrix
$matrix_query = $conn->query("SELECT * FROM merit_matrix_settings WHERE period_id = $period_id ORDER BY performance_rating DESC, compa_ratio_range ASC");
$merit_matrix = [];
if ($matrix_query) {
    while ($row = $matrix_query->fetch_assoc()) {
        $rating = (string)$row['performance_rating'];
        $range = $row['compa_ratio_range'];
        $merit_matrix[$rating][$range] = $row;
    }
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

// Fetch Simulation Data
$simulation_query = $conn->query("
    SELECT 
        e.EmployeeID, e.EmployeeCode, e.FirstName, e.LastName,
        p.PositionName,
        ei.BaseSalary, ei.SalaryGradeID,
        fpr.FinalRating
    FROM employee e
    JOIN employmentinformation ei ON e.EmployeeID = ei.EmployeeID
    LEFT JOIN positions p ON ei.PositionID = p.PositionID
    LEFT JOIN final_performance_rating fpr ON e.EmployeeID = fpr.EmployeeID AND fpr.period_id = $period_id
    ORDER BY e.EmployeeCode ASC
");
$simulation_data = [];
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link rel="stylesheet" href="../../css/cycle.css?v=1.2">
  <link rel="stylesheet" href="../../css/sidebar-fix.css?v=1.1">
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
        
        <a href="dashboard.php" class="nav-item active">
          <i data-lucide="layout-dashboard"></i>
          <span>Dashboard</span>
        </a>

         <div class="nav-item-group <?php echo ($module === 'hr') ? 'active' : ''; ?>">
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
            <a href="employeemaster.php" class="submenu-item <?php echo ($page === 'employeemaster') ? 'active' : ''; ?>">
              <i data-lucide="file-user"></i>
              <span>Employee Master Files</span>
            </a>
             <a href="informationrq.php" class="submenu-item <?php echo ($page === 'informationrq') ? 'active' : ''; ?>">
              <i data-lucide="user-round-pen"></i>
              <span>Information Request</span>
            </a>
            <a href="bankform.php" class="submenu-item <?php echo ($page === 'bankform') ? 'active' : ''; ?>">
              <i data-lucide="file-text"></i>
              <span>Bank Form Management</span>
            </a>
            <a href="" class="submenu-item">
              <i data-lucide="user-cog"></i>
              <span>Security Settings</span>
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
            <a href="#" class="submenu-item">
              <i data-lucide="notebook"></i>
              <span>Current Compensation Structure</span>
            </a>
            <a href="cycle.php" class="submenu-item <?php echo ($page === 'cycle') ? 'active' : ''; ?>">
              <i data-lucide="notebook-pen"></i>
              <span>Compensation Structure Management</span>
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

           <div class="nav-item-group <?php echo ($module === 'payroll') ? 'active' : ''; ?>">
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
        <div class="search-box">
          <i data-lucide="search"></i>
          <input type="search" placeholder="Search...">
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
      <!-- Compensation Planning Tabs -->
      <div class="tabs-container">
        <div class="tabs-header">
          <button class="tab-btn active" data-tab="strategic">
            <i data-lucide="target"></i>
            <span>Strategic Planning</span>
          </button>
          <button class="tab-btn" data-tab="salary">
            <i data-lucide="banknote"></i>
            <span>Salary & Scales</span>
          </button>
          <button class="tab-btn" data-tab="statutory">
            <i data-lucide="landmark"></i>
            <span>Statutory (PH)</span>
          </button>
          <button class="tab-btn" data-tab="merit">
            <i data-lucide="trending-up"></i>
            <span>Merit Matrix</span>
          </button>
          <button class="tab-btn" data-tab="allowances">
            <i data-lucide="gift"></i>
            <span>Allowances</span>
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
                    <div class="form-group">
                      <label>Total Budget Allocation</label>
                      <div class="input-with-symbol">
                        <span>₱</span>
                        <input type="number" id="budgetAllocation" value="5000000">
                      </div>
                    </div>
                    <div class="form-group">
                      <label>Implementation Date</label>
                      <input type="date" value="<?php echo htmlspecialchars($period_data['effective_date'] ?? '2025-01-01'); ?>">
                    </div>
                  </div>
                  <div class="form-actions" style="margin-top: 24px;">
                    <button class="btn btn-primary" id="startCycleBtn">
                      <span>Start Cycle</span>
                      <i data-lucide="arrow-right"></i>
                    </button>
                  </div>
                </div>
              </div>
              <div class="planning-stats">
                <div class="mini-stat-card">
                  <span class="ms-label">Target Employees</span>
                  <span class="ms-value">1,248</span>
                </div>
                <div class="mini-stat-card">
                  <span class="ms-label">Avg. Increase Target</span>
                  <span class="ms-value">4.5%</span>
                </div>
                <div class="mini-stat-card">
                  <span class="ms-label">PH Tax Jurisdiction</span>
                  <span class="ms-value">TRAIN Law</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Salary & Scales Tab -->
          <div class="tab-panel" id="salary">
            <section class="comp-panel">
              <div class="comp-panel-header">
                <div class="comp-panel-left">
                  <div class="comp-panel-icon"><i data-lucide="layers"></i></div>
                  <div class="comp-panel-titles">
                    <h2>Regional Salary Scales (Philippines)</h2>
                    <div class="comp-panel-sub">Base pay ranges adjusted for National Capital Region (NCR).</div>
                  </div>
                </div>
                <div class="comp-panel-actions">
                  <button class="btn-premium-add" id="addGradeBtn">
                    <i data-lucide="plus"></i> Add Grade
                  </button>
                </div>
              </div>

              <div class="panel-body">
                <div class="table-responsive">
                  <table class="comp-table editable-table" id="salaryGradeTable">
                <thead>
                  <tr>
                    <th>Job Grade</th>
                    <th>Level Name</th>
                    <th>Description</th>
                    <th>Minimum (Monthly)</th>
                    <th>Midpoint</th>
                    <th>Maximum (Monthly)</th>
                    <th>Spread</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($salary_grades as $grade): ?>
                  <tr data-id="<?php echo $grade['SalaryGradeID']; ?>">
                    <td><input type="text" value="<?php echo htmlspecialchars($grade['GradeLevel']); ?>" class="table-input-premium grade-level-input"></td>
                    <td><input type="text" value="<?php echo htmlspecialchars($grade['GradeName']); ?>" class="table-input-premium grade-name-input"></td>
                    <td><input type="text" value="<?php echo htmlspecialchars($grade['Description'] ?? $grade['description'] ?? ''); ?>" class="table-input-premium description-input" placeholder="Role details..."></td>
                    <td><div class="input-with-symbol"><span>₱</span><input type="number" value="<?php echo (int)$grade['MinSalary']; ?>" class="table-input-premium min-salary-input"></div></td>
                    <td><div class="input-with-symbol"><span>₱</span><input type="number" value="<?php echo (int)$grade['MidSalary']; ?>" class="table-input-premium mid-salary-input" readonly></div></td>
                    <td><div class="input-with-symbol"><span>₱</span><input type="number" value="<?php echo (int)$grade['MaxSalary']; ?>" class="table-input-premium max-salary-input"></div></td>
                    <td class="spread-cell"><?php 
                      $min = (float)$grade['MinSalary'];
                      $max = (float)$grade['MaxSalary'];
                      $spread = ($min > 0) ? (($max - $min) / $min) * 100 : 0;
                      echo number_format($spread, 1); 
                    ?>%</td>
                    <td>
                      <button class="btn-icon archive-grade-btn" title="Archive Grade">
                        <i data-lucide="archive"></i>
                      </button>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </section>
      </div>

          <!-- Statutory Tab -->
          <div class="tab-panel" id="statutory">
            <div class="comp-grid">
              <!-- SSS Configuration -->
              <div class="stat-group-card">
                <div class="sg-header">
                  <i data-lucide="shield-check" style="color:#2ca078"></i>
                  <h4>SSS Contribution</h4>
                </div>
                <div class="sg-body">
                  <p class="sg-desc">Manage Social Security rates and WISP mandatory provident fund thresholds.</p>
                  <div class="editable-form">
                    <div class="form-group-inline">
                      <label>Employee Share (%)</label>
                      <input type="number" step="0.1" value="<?php echo number_format($sss_data['employee_share_pct'] ?? 5.0, 1); ?>" class="stat-input">
                    </div>
                    <div class="form-group-inline">
                      <label>Employer Share (%)</label>
                      <input type="number" step="0.1" value="<?php echo number_format($sss_data['employer_share_pct'] ?? 10.0, 1); ?>" class="stat-input">
                    </div>
                    <div class="form-group-inline">
                      <label>Max MSC (Monthly)</label>
                      <div class="inline-input-symbol">
                        <span>₱</span>
                        <input type="number" value="<?php echo (int)($sss_data['max_msc_monthly'] ?? 30000); ?>" class="stat-input">
                      </div>
                    </div>
                    <div class="form-group-inline">
                      <label>WISP Threshold</label>
                      <div class="inline-input-symbol">
                        <span>₱</span>
                        <input type="number" value="<?php echo (int)($sss_data['wisp_threshold'] ?? 20000); ?>" class="stat-input">
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- PhilHealth Configuration -->
              <div class="stat-group-card">
                <div class="sg-header">
                  <i data-lucide="heart" style="color:#ef4444"></i>
                  <h4>PhilHealth Premium</h4>
                </div>
                <div class="sg-body">
                  <p class="sg-desc">Current premium rate is 5.0% split equally between EE and ER.</p>
                  <div class="editable-form">
                    <div class="form-group-inline">
                      <label>Employee Share (%)</label>
                      <input type="number" step="0.01" value="<?php echo number_format($ph_data['employee_share_pct'] ?? 2.50, 2); ?>" class="stat-input">
                    </div>
                    <div class="form-group-inline">
                      <label>Employer Share (%)</label>
                      <input type="number" step="0.01" value="<?php echo number_format($ph_data['employer_share_pct'] ?? 2.50, 2); ?>" class="stat-input">
                    </div>
                    <div class="form-group-inline">
                      <label>Salary Ceiling</label>
                      <div class="inline-input-symbol">
                        <span>₱</span>
                        <input type="number" value="<?php echo (int)($ph_data['salary_ceiling'] ?? 100000); ?>" class="stat-input">
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Pag-IBIG Configuration -->
              <div class="stat-group-card">
                <div class="sg-header">
                  <i data-lucide="home" style="color:#ffc107"></i>
                  <h4>Pag-IBIG (HDMF)</h4>
                </div>
                <div class="sg-body">
                  <p class="sg-desc">Contribution based on percentage or fixed amount caps.</p>
                  <div class="editable-form">
                    <div class="form-group-inline">
                      <label>Employee Rate (%)</label>
                      <input type="number" step="0.1" value="<?php echo number_format($pi_data['employee_rate_pct'] ?? 2.0, 1); ?>" class="stat-input">
                    </div>
                    <div class="form-group-inline">
                      <label>Monthly Cap (EE)</label>
                      <div class="inline-input-symbol">
                        <span>₱</span>
                        <input type="number" value="<?php echo (int)($pi_data['monthly_cap_ee'] ?? 200); ?>" class="stat-input">
                      </div>
                    </div>
                    <div class="form-group-inline">
                      <label>Monthly Cap (ER)</label>
                      <div class="inline-input-symbol">
                        <span>₱</span>
                        <input type="number" value="<?php echo (int)($pi_data['monthly_cap_er'] ?? 200); ?>" class="stat-input">
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- BIR Tax Configuration -->
              <div class="stat-group-card">
                <div class="sg-header">
                  <i data-lucide="file-text" style="color:#3b82f6"></i>
                  <h4>BIR Tax (TRAIN)</h4>
                </div>
                <div class="sg-body">
                  <p class="sg-desc">Withholding tax settings and tax-exempt benefit caps.</p>
                  <div class="editable-form">
                    <div class="form-group-inline">
                      <label>Tax Exempt Limit</label>
                      <div class="inline-input-symbol">
                        <span>₱</span>
                        <input type="number" value="<?php echo (int)($bir_data['tax_exempt_limit'] ?? 250000); ?>" class="stat-input">
                      </div>
                    </div>
                    <div class="form-group-inline">
                      <label>De Minimis Cap</label>
                      <div class="inline-input-symbol">
                        <span>₱</span>
                        <input type="number" value="<?php echo (int)($bir_data['de_minimis_cap'] ?? 90000); ?>" class="stat-input">
                      </div>
                    </div>
                    <div class="form-group-inline">
                      <label>13th Month Cap</label>
                      <div class="inline-input-symbol">
                        <span>₱</span>
                        <input type="number" value="<?php echo (int)($bir_data['thirteenth_month_cap'] ?? 90000); ?>" class="stat-input">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Merit Matrix Tab -->
          <div class="tab-panel" id="merit">
             <div class="section-header">
              <div class="sh-info">
                <h3>Annual Merit Increase Matrix</h3>
                <p>Calculate increases based on performance and position within salary range (Compa-ratio). <strong>Note: Max allowed increase is 5.0%.</strong></p>
              </div>
            </div>
            <div class="matrix-container">
              <table class="matrix-table">
                <thead>
                  <tr>
                    <th rowspan="2">Performance Rating</th>
                    <th colspan="3">Position in Range (Compa-Ratio)</th>
                  </tr>
                  <tr>
                    <th>Low ( < 90% )</th>
                    <th>At Mid ( 90-110% )</th>
                    <th>High ( > 110% )</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                  $ratings = ['5.0', '4.0', '3.0'];
                  $ranges = ['Low', 'Mid', 'High'];
                  foreach ($ratings as $r): 
                    $rating_label = ($r == '5.0') ? 'Exceptional' : (($r == '4.0') ? 'Exceeds' : 'Meets');
                  ?>
                  <tr>
                    <td><strong><?php echo $r; ?> (<?php echo $rating_label; ?>)</strong></td>
                    <?php foreach ($ranges as $range): 
                      $cell = $merit_matrix[$r][$range] ?? null;
                      $val = $cell ? number_format($cell['min_increase_pct'], 1) . "% - " . number_format($cell['max_increase_pct'], 1) . "%" : "0.0% - 0.0%";
                      $impact_class = ($range == 'Low') ? 'impact-high' : (($range == 'Mid') ? 'impact-med' : 'impact-low');
                      if ($r == '3.0' && $range == 'High') $impact_class = 'impact-none';
                    ?>
                    <td class="<?php echo $impact_class; ?>"><input type="text" value="<?php echo $val; ?>" class="matrix-input"></td>
                    <?php endforeach; ?>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Allowances Tab -->
          <div class="tab-panel" id="allowances">
            <div class="section-header">
              <div class="sh-info">
                <h3>Allowance & Benefit Structures</h3>
                <p>Define standard non-taxable (De Minimis) and taxable allowances by employee grade.</p>
              </div>
              <button class="btn btn-outline">
                <i data-lucide="plus"></i>
                <span>Add Allowance Grade</span>
              </button>
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
                        <span>₱</span>
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

          <!-- Simulation Tab -->
          <div class="tab-panel" id="simulation">
            <div class="simulation-header">
              <div class="sim-filters">
                <select class="form-select">
                  <option>All Departments</option>
                  <option>Operations</option>
                  <option>IT & Systems</option>
                </select>
                <button class="btn btn-secondary">Run Auto-Simulation</button>
                <button class="btn btn-primary" id="submitProposalBtn">
                  <i data-lucide="send"></i>
                  <span>Submit Proposal to Manager</span>
                </button>
              </div>
              <div class="sim-totals-group">
                <div class="sim-total">
                  <span>Total Monthly Budget Increase:</span>
                  <h3 id="totalSimulationCost">₱0.00</h3>
                </div>
                <div class="sim-total">
                  <span>Total Proposed Monthly Expenditure:</span>
                  <h3 id="totalExpenditure">₱0.00</h3>
                </div>
              </div>
            </div>
            <div class="table-container">
               <table class="comp-table simulation-table">
                <thead>
                  <tr>
                    <th>EE ID</th>
                    <th>Name & Position</th>
                    <th>Rating</th>
                    <th>Salary</th>
                    <th>Prop. %</th>
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
                    $current_pay = $emp['BaseSalary'];
                    $allowances = $emp['TotalAllowances'];
                    $taxable_allowances = $emp['TaxableAllowances'];
                  ?>
                  <tr data-taxable-allowances="<?php echo $taxable_allowances; ?>" data-grade-id="<?php echo $emp['SalaryGradeID']; ?>">
                    <td>
                      <div class="user-cell-stacked">
                        <div class="user-avatar-sm"><?php echo $initials; ?></div>
                        <span class="u-code"><?php echo htmlspecialchars($emp['EmployeeCode'] ?? '---'); ?></span>
                      </div>
                    </td>
                    <td>
                      <div class="user-info">
                        <span class="u-name-premium"><?php echo htmlspecialchars(($emp['FirstName'] ?? '') . ' ' . ($emp['LastName'] ?? '')); ?></span>
                        <span class="u-pos"><?php echo htmlspecialchars($emp['PositionName'] ?? 'Position Not Set'); ?></span>
                      </div>
                    </td>
                    <td><span class="rating-badge rating-<?php echo floor($rating); ?>"><?php echo number_format($rating, 1); ?></span></td>
                    <td class="current-pay">₱<?php echo number_format($current_pay, 0); ?></td>
                    <td><input type="number" class="table-input" value="0.0" step="0.5" max="5.0">%</td>
                    <td class="proposed-gross">₱<?php echo number_format($current_pay, 0); ?></td>
                    <td class="total-allowances">₱<?php echo number_format($allowances, 2); ?></td>
                    <td class="total-gross">₱0.00</td>
                    <td class="rate-semi">₱0.00</td>
                    <td class="rate-daily">₱0.00</td>
                    <td class="rate-hourly">₱0.00</td>
                    <td class="employer-share">₱0.00</td>
                    <td class="full-load">₱0.00</td>
                    <td class="deduction-sss">₱0.00</td>
                    <td class="deduction-wisp" style="color: #ef4444; font-weight: 600;">₱0.00</td>
                    <td class="deduction-ph">₱0.00</td>
                    <td class="deduction-pi">₱0.00</td>
                    <td class="deduction-tax">₱0.00</td>
                    <td class="net-pay-cell">₱0.00</td>
                    <td class="increase-cell">+₱0</td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      <!-- Add Grade Modal -->
      <div id="gradeModal" class="modal" aria-hidden="true">
        <div class="modal-dialog">
          <div class="comp-modal-hero">
            <div class="comp-modal-hero-inner">
              <div class="comp-modal-hero-icon">
                <i data-lucide="layers"></i>
              </div>
              <div class="comp-modal-hero-text">
                <h3>Add Salary Grade</h3>
                <p>Define a new pay level for the current period.</p>
              </div>
              <button class="rp-close-modal" id="closeGradeModalBtn" title="Close">&times;</button>
            </div>
          </div>

          <div class="modal-context-box">
             <div class="modal-context-icon">
               <i data-lucide="layers"></i>
             </div>
             <div class="modal-context-text">
               Configuring for: <strong>2026 Compensation Cycle</strong>
             </div>
          </div>

          <div class="modal-body modal-form-premium">
            <form id="gradeForm">
              <div class="form-group">
                <label>Job Grade <span class="required">*</span></label>
                <input type="text" name="grade_level" class="input-premium no-icon" placeholder="e.g. SG-7" required />
              </div>

              <div class="form-group">
                <label>Level Name <span class="required">*</span></label>
                <input type="text" name="grade_name" class="input-premium no-icon" placeholder="e.g. Senior Associate" required />
              </div>

              <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="2" class="input-premium no-icon" placeholder="Briefly describe the responsibilities..."></textarea>
              </div>

              <div class="form-row-triple">
                <div class="form-group">
                  <label>Min Salary</label>
                  <input type="number" name="min_salary" id="modal_min_salary" class="input-premium no-icon" value="0" />
                </div>
                <div class="form-group">
                  <label>Midpoint</label>
                  <input type="number" id="modal_mid_salary" class="input-premium no-icon" value="0" readonly title="Auto-calculated" />
                </div>
                <div class="form-group">
                  <label>Max Salary</label>
                  <input type="number" name="max_salary" id="modal_max_salary" class="input-premium no-icon" value="0" />
                </div>
              </div>
            </form>
          </div>

          <div class="modal-footer-premium">
            <button type="button" id="cancelGrade" class="btn-cancel-premium">Cancel</button>
            <button type="submit" form="gradeForm" class="btn-comp-submit">
               Save Grade
            </button>
          </div>
        </div>
      </div>
  </main>
  <script src="../../js/sidebar-active.js"></script>
  <script src="../../js/cycle.js?v=2.2"></script>
  <script>
    lucide.createIcons();
  </script>
  
  <script src="../../js/user-menu.js"></script>
</body>
</html>


