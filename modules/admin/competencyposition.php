<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../../login.php");
    exit();
}

$page = 'competencyposition';
$module = 'competency';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link rel="stylesheet" href="../../css/competencyposition.css?v=1.2">
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
      <?php require_once __DIR__ . '/../../config/config.php'; ?>
      
      <!-- Stats Overview Section -->
      <div class="stats-overview-pos">
        <?php
        // Fetch stats for cards
        $stats_q = "SELECT 
            (SELECT COUNT(*) FROM positions) as total_positions,
            (SELECT COUNT(*) FROM position_competencies) as total_mappings,
            (SELECT COUNT(DISTINCT DepartmentID) FROM department) as total_depts";
        $stats_res = $conn->query($stats_q);
        $stats = $stats_res->fetch_assoc();
        ?>
        <div class="stat-card-pos">
          <div class="sc-icon-pos" style="background: rgba(44, 160, 120, 0.1); color: var(--brand-green);">
            <i data-lucide="briefcase"></i>
          </div>
          <div class="sc-info-pos">
            <span class="sc-label-pos">Total Positions</span>
            <h3 class="sc-value-pos"><?php echo number_format($stats['total_positions'] ?? 0); ?></h3>
          </div>
        </div>

        <div class="stat-card-pos">
          <div class="sc-icon-pos" style="background: rgba(52, 152, 219, 0.1); color: #3498db;">
            <i data-lucide="award"></i>
          </div>
          <div class="sc-info-pos">
            <span class="sc-label-pos">Active Mappings</span>
            <h3 class="sc-value-pos"><?php echo number_format($stats['total_mappings'] ?? 0); ?></h3>
          </div>
        </div>

        <div class="stat-card-pos">
          <div class="sc-icon-pos" style="background: rgba(155, 89, 182, 0.1); color: #9b59b6;">
            <i data-lucide="building-2"></i>
          </div>
          <div class="sc-info-pos">
            <span class="sc-label-pos">Departments</span>
            <h3 class="sc-value-pos"><?php echo number_format($stats['total_depts'] ?? 0); ?></h3>
          </div>
        </div>
      </div>

      <div class="action-bar-pos">
        <div class="ab-left-pos">
          <div class="ab-header-pos">
            <div class="ab-icon-pos">
              <i data-lucide="briefcase"></i>
            </div>
            <div class="ab-text-pos">
              <h2>Competency Position Mapping</h2>
              <p>Align proficiency requirements with specific organizational roles.</p>
            </div>
          </div>
        </div>
        <div class="ab-right-pos">
           <div class="search-filter-group">
              <div class="search-box-pos">
                <input type="text" id="positionSearch" placeholder="Search positions...">
              </div>
              <select id="deptFilter" class="filter-select-pos">
                <option value="">All Departments</option>
                <?php
                $depts = $conn->query("SELECT * FROM department ORDER BY DepartmentName ASC");
                while($d = $depts->fetch_assoc()) {
                    echo "<option value='".htmlspecialchars($d['DepartmentName'])."'>".htmlspecialchars($d['DepartmentName'])."</option>";
                }
                ?>
              </select>
           </div>
        </div>
      </div>

      <div class="mapping-grid">
        <table class="mapping-table" id="mainMappingTable">
          <thead>
            <tr>
              <th>Position</th>
              <th>Department</th>
              <th style="width: 220px; text-align: center;">Mapping Status</th>
              <th style="width: 140px; text-align: center;">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $query = "SELECT p.PositionID, p.PositionName, d.DepartmentName, 
                             (SELECT COUNT(*) FROM position_competencies pc WHERE pc.position_id = p.PositionID) as total_comp
                       FROM positions p
                       JOIN department d ON p.DepartmentID = d.DepartmentID
                       ORDER BY p.PositionID DESC";
            $result = $conn->query($query);
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $count = $row['total_comp'];
                    $count_label = ($count > 0) ? "$count Competencies" : "No Mappings";
                    $count_color = ($count > 0) ? 'var(--brand-green)' : '#94a3b8';
                    
                    // Department Badge Colors (Modern Palette)
                    $dept_clean = strtolower(trim($row['DepartmentName']));
                    $dept_color = '#64748b'; // Default gray
                    if (strpos($dept_clean, 'finance') !== false) $dept_color = '#3498db';
                    else if (strpos($dept_clean, 'hr') !== false || strpos($dept_clean, 'human') !== false) $dept_color = '#9b59b6';
                    else if (strpos($dept_clean, 'microfinance') !== false || strpos($dept_clean, 'loan') !== false) $dept_color = '#2ecc71';
                    else if (strpos($dept_clean, 'admin') !== false) $dept_color = '#f39c12';
            ?>
            <tr id="row-pos-<?php echo $row['PositionID']; ?>"
                data-pos-name="<?php echo strtolower(htmlspecialchars($row['PositionName'])); ?>" 
                data-dept-name="<?php echo htmlspecialchars($row['DepartmentName']); ?>">
              <td class="pos-name-cell">
                <div class="pos-icon-mini">
                  <i data-lucide="award"></i>
                </div>
                <strong><?php echo htmlspecialchars($row['PositionName']); ?></strong>
              </td>
              <td>
                <span class="dept-pill-pos" style="border-left: 3px solid <?php echo $dept_color; ?>;">
                  <?php echo htmlspecialchars($row['DepartmentName']); ?>
                </span>
              </td>
              <td style="text-align: center;">
                <span class="count-badge-pos v2" style="background: <?php echo $count_color; ?>10; color: <?php echo $count_color; ?>; border: 1px solid <?php echo $count_color; ?>20;">
                  <i data-lucide="<?php echo ($count > 0) ? 'check-circle' : 'alert-circle'; ?>" style="width: 14px; height: 14px; margin-right: 6px;"></i>
                  <?php echo $count_label; ?>
                </span>
              </td>
              <td style="text-align: center;">
                <button class="view-manage-btn" onclick="openManageModal(<?php echo $row['PositionID']; ?>)">
                  Manage
                </button>
              </td>
            </tr>
            <?php
                }
            } else {
                echo '<tr><td colspan="4" style="text-align: center; padding: 40px; color: var(--text-tertiary);">No positions found in the catalog.</td></tr>';
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Management Modal (Shows list of competencies for a position) -->
    <div id="manageCompetenciesModal" class="modal-overlay-pos">
      <div class="modal-content-pos" style="max-width: 800px;">
        <div class="modal-header-pos">
          <div class="mh-icon-pos"><i data-lucide="briefcase"></i></div>
          <div class="mh-info-pos">
            <h3 id="manageTitle">Position Name</h3>
            <span id="manageSubTitle">Department Name</span>
          </div>
          <button class="close-modal-pos" id="closeManageModal"><i data-lucide="x"></i></button>
        </div>
        
        <div class="manage-body-pos">
          <div class="manage-actions-pos">
             <h4>Mapped Competencies</h4>
             <button class="add-comp-inline-btn" id="openAddInlineBtn">
               <i data-lucide="plus"></i> Assign New
             </button>
          </div>

          <table class="manage-details-table">
            <thead>
              <tr>
                <th>Competency</th>
                <th style="width: 150px; text-align: center;">Required Level</th>
                <th style="width: 100px; text-align: center;">Actions</th>
              </tr>
            </thead>
            <tbody id="manageTableBody">
              <!-- Dynamically populated via JS -->
            </tbody>
          </table>
          <div class="pagination-container" id="managePagination">
            <button type="button" class="btn-page" id="prevManagePage"><i data-lucide="chevron-left"></i></button>
            <span class="page-info" id="managePageInfo">Page 1 of 1</span>
            <button type="button" class="btn-page" id="nextManagePage"><i data-lucide="chevron-right"></i></button>
          </div>
        </div>
      </div>
    </div>

    <!-- Add/Edit Inline Modal (Now Batch) -->
    <div id="inlineActionModal" class="modal-overlay-pos" style="z-index: 3000;">
      <div class="modal-content-pos" style="max-width: 600px;">
        <div class="modal-header-pos">
          <div class="mh-icon-pos" id="inlineIcon"><i data-lucide="check-square"></i></div>
          <div class="mh-info-pos">
            <h3 id="inlineTitle">Assign Competencies</h3>
            <span>Select skill and proficiency levels to assign or update</span>
          </div>
          <button class="close-modal-pos" id="closeInlineModal"><i data-lucide="x"></i></button>
        </div>
        <form id="inlineActionForm" class="modal-form-pos batch-assign-form">
          <input type="hidden" name="position_id" id="inline_pos_id">
          
          <div class="batch-table-container">
            <table class="batch-assign-table">
              <thead>
                <tr>
                  <th style="width: 50px; text-align: center;">
                    <input type="checkbox" id="selectAllBatch" class="batch-check">
                  </th>
                  <th>Competency</th>
                  <th style="width: 180px;">Required Level</th>
                </tr>
              </thead>
              <tbody id="batchTableBody">
                <!-- Populated via JS -->
              </tbody>
            </table>
          </div>
          <div class="pagination-container" id="batchPagination">
            <button type="button" class="btn-page" id="prevBatchPage"><i data-lucide="chevron-left"></i></button>
            <span class="page-info" id="batchPageInfo">Page 1 of 1</span>
            <button type="button" class="btn-page" id="nextBatchPage"><i data-lucide="chevron-right"></i></button>
          </div>

          <div class="modal-footer-pos">
            <button type="button" class="btn-cancel-pos" id="cancelInline">Cancel</button>
            <button type="submit" class="btn-save-pos" id="inlineSubmitBtn">Save Assignments</button>
          </div>
        </form>
      </div>
    </div>
  </main>
  <script src="../../js/competencyposition.js"></script>
  <script>
    // Improved search alignment using root CSS variables
    const style = document.createElement('style');
    style.textContent = `
      /* Enhanced Action Bar Styles */
      .action-bar-pos {
        display: flex;
        justify-content: space-between;
        align-items: stretch;
        margin-bottom: 32px;
        padding: 28px 32px;
        background: var(--surface, #ffffff);
        border: 1px solid var(--border-color, #e5e7eb);
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        gap: 32px;
        transition: all 0.3s ease;
      }
      
      .action-bar-pos:hover {
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
        transform: translateY(-2px);
      }
      
      .ab-left-pos {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-width: 0;
      }
      
      .ab-header-pos {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 20px;
      }
      
      .ab-icon-pos {
        width: 56px;
        height: 56px;
        background: linear-gradient(135deg, rgba(44, 160, 120, 0.1), rgba(44, 160, 120, 0.15));
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--brand-green, #2ca078);
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(44, 160, 120, 0.15);
      }
      
      .ab-icon-pos svg {
        width: 28px;
        height: 28px;
      }
      
      .ab-text-pos h2 {
        font-size: 24px;
        font-weight: 800;
        color: var(--text-primary, #111827);
        margin: 0 0 6px 0;
        line-height: 1.2;
        letter-spacing: -0.02em;
      }
      
      .ab-text-pos p {
        font-size: 14px;
        color: var(--text-secondary, #6b7280);
        margin: 0;
        line-height: 1.5;
      }
      
      .ab-right-pos {
        display: flex;
        align-items: center;
        gap: 16px;
        flex-shrink: 0;
      }
      
      /* Search and Filter Container */
      .search-filter-group {
        display: flex;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
      }
      
      /* Search Input */
      .search-box-pos {
        position: relative;
        display: flex;
        align-items: center;
        background: var(--input-bg, #ffffff);
        border: 2px solid var(--input-border, #e5e7eb);
        border-radius: 16px;
        padding: 0 20px;
        height: 48px;
        min-width: 320px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
      }
      
      .search-box-pos:hover {
        border-color: var(--brand-green, #2ca078);
        box-shadow: 0 4px 12px rgba(44, 160, 120, 0.15);
        transform: translateY(-1px);
      }
      
      .search-box-pos:focus-within {
        border-color: var(--brand-green, #2ca078);
        outline: none;
        box-shadow: 0 0 0 4px rgba(44, 160, 120, 0.1), 0 4px 12px rgba(44, 160, 120, 0.15);
        background: var(--surface, #ffffff);
      }
      
      .search-box-pos input {
        border: none;
        outline: none;
        flex: 1;
        font-size: 14px;
        font-weight: 500;
        color: var(--text-primary, #111827);
        background: transparent;
        line-height: 1.4;
        padding: 8px 0;
        margin: 0;
      }
      
      .search-box-pos input::placeholder {
        color: var(--text-tertiary, #9ca3af);
        font-weight: 400;
      }
      
      /* Filter Dropdown */
      .filter-select-pos {
        position: relative;
        display: flex;
        align-items: center;
        padding: 0 20px;
        height: 48px;
        background: var(--input-bg, #ffffff);
        border: 2px solid var(--input-border, #e5e7eb);
        border-radius: 16px;
        font-size: 14px;
        font-weight: 500;
        color: var(--text-primary, #111827);
        min-width: 200px;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        appearance: none;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%232ca078' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 16px center;
        background-repeat: no-repeat;
        background-size: 16px;
        padding-right: 52px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
      }
      
      .filter-select-pos:hover {
        border-color: var(--brand-green, #2ca078);
        box-shadow: 0 4px 12px rgba(44, 160, 120, 0.15);
        transform: translateY(-1px);
      }
      
      .filter-select-pos:focus {
        outline: none;
        border-color: var(--brand-green, #2ca078);
        box-shadow: 0 0 0 4px rgba(44, 160, 120, 0.1), 0 4px 12px rgba(44, 160, 120, 0.15);
      }
      
      .filter-select-pos option {
        padding: 12px 16px;
        background: var(--surface, #ffffff);
        color: var(--text-primary, #111827);
        font-size: 14px;
        font-weight: 400;
      }
      
      /* Dark Mode Support */
      body.dark-mode .action-bar-pos {
        background: var(--surface, #1a1a1a);
        border-color: var(--border-color, #2d2d2d);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
      }
      
      body.dark-mode .ab-icon-pos {
        background: linear-gradient(135deg, rgba(44, 160, 120, 0.15), rgba(44, 160, 120, 0.2));
      }
      
      body.dark-mode .search-box-pos {
        background: var(--dark-input-bg, #1f2937);
        border-color: var(--dark-input-border, #4b5563);
      }
      
      body.dark-mode .search-box-pos:hover,
      body.dark-mode .search-box-pos:focus-within {
        border-color: var(--brand-green, #2ca078);
        background: var(--surface, #1a1a1a);
      }
      
      body.dark-mode .search-box-pos input {
        color: var(--dark-text-primary, #f9fafb);
      }
      
      body.dark-mode .search-box-pos input::placeholder {
        color: var(--dark-text-tertiary, #9ca3af);
      }
      
      body.dark-mode .filter-select-pos {
        background: var(--dark-input-bg, #1f2937);
        border-color: var(--dark-input-border, #4b5563);
        color: var(--dark-text-primary, #f9fafb);
      }
      
      body.dark-mode .filter-select-pos:hover,
      body.dark-mode .filter-select-pos:focus {
        border-color: var(--brand-green, #2ca078);
        background: var(--surface, #1a1a1a);
      }
      
      body.dark-mode .filter-select-pos option {
        background: var(--surface, #1a1a1a);
        color: var(--dark-text-primary, #f9fafb);
      }
      
      /* Responsive Design */
      @media (max-width: 1024px) {
        .action-bar-pos {
          flex-direction: column;
          gap: 24px;
          align-items: stretch;
        }
        
        .ab-left-pos {
          flex: none;
        }
        
        .ab-right-pos {
          justify-content: stretch;
        }
        
        .search-filter-group {
          justify-content: stretch;
        }
        
        .search-box-pos {
          flex: 1;
          min-width: 200px;
        }
      }
      
      @media (max-width: 768px) {
        .action-bar-pos {
          padding: 20px;
          margin-bottom: 24px;
        }
        
        .ab-header-pos {
          flex-direction: column;
          align-items: flex-start;
          gap: 12px;
          margin-bottom: 16px;
        }
        
        .ab-icon-pos {
          width: 48px;
          height: 48px;
        }
        
        .ab-icon-pos svg {
          width: 24px;
          height: 24px;
        }
        
        .ab-text-pos h2 {
          font-size: 20px;
        }
        
        .search-filter-group {
          flex-direction: column;
        }
        
        .search-box-pos,
        .filter-select-pos {
          min-width: 100%;
          height: 44px;
        }
        
      }
    `;
    document.head.appendChild(style);
    
    lucide.createIcons();
  </script>
</body>
</html>






