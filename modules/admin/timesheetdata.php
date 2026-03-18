<?php
session_start();
if (!isset($_SESSION['username'])) {
  header("Location: ../../login.php");
  exit();
}
require_once '../../config/config.php';

// â”€â”€ Timesheet Data Queries â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
$periodsRes = $conn->query("
    SELECT tp.PeriodID, tp.StartDate, tp.EndDate, tp.Status, d.DepartmentName,
           COUNT(tes.SummaryID) as EmpCount,
           SUM(tes.RegularHours) as TotalRegular,
           SUM(tes.OvertimeHours) as TotalOT,
           SUM(tes.NightDiffHours) as TotalND,
           SUM(tes.RegHolidayHours) as TotalRegHol,
           SUM(tes.SpecHolidayHours) as TotalSpecHol,
           SUM(tes.LateMinutes) as TotalLate,
           SUM(tes.UndertimeMinutes) as TotalUT,
           SUM(tes.AbsencesHours) as TotalAbsences,
           SUM(tes.PaidLeaveHours) as TotalPaidLeave,
           SUM(tes.UnpaidLeaveHours) as TotalUnpaidLeave,
           SUM(tes.TotalPayableHours) as TotalPayable
    FROM timesheet_period tp
    INNER JOIN timesheet_employee_summary tes ON tes.PeriodID = tp.PeriodID
    LEFT JOIN department d ON d.DepartmentID = tp.DepartmentID
    WHERE tp.IsArchived = 0
    GROUP BY tp.PeriodID
    ORDER BY tp.StartDate DESC
");
$periods = [];
if ($periodsRes) while ($row = $periodsRes->fetch_assoc()) $periods[] = $row;

$hasData = count($periods) > 0;

// Eligible periods (APPROVED/FINALIZED) available for payroll
$eligibleCount = 0;
foreach ($periods as $p) {
    if (in_array($p['Status'], ['APPROVED', 'FINALIZED'])) $eligibleCount++;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Timesheet Data</title>
  <link rel="stylesheet" href="../../css/timesheetdata.css?v=1.2">
  <script src="https://unpkg.com/lucide@latest"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="icon" type="image/png" href="../../img/logo.png">
</head>
<body>

  <!-- Sidebar -->
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
      <div class="logo-container">
        <div class="logo-wrapper"><img src="../../img/logo.png" alt="Logo" class="logo"></div>
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
        <span class="nav-section-title">ANALYTICS &amp; REPORTING</span>
        <a href="dashboard.php" class="nav-item"><i data-lucide="layout-dashboard"></i><span>HR ANALYTICS</span></a>
      </div>
      <div class="nav-section">
        <span class="nav-section-title">HUMAN RESOURCES I</span>
        <div class="nav-item-group">
          <button class="nav-item has-submenu" data-module="recruitment"><div class="nav-item-content"><i data-lucide="layers-plus"></i><span>Recruitment</span></div><i data-lucide="chevron-down" class="submenu-icon"></i></button>
          <div class="submenu" id="submenu-recruitment"><a href="recruitment.php" class="submenu-item"><i data-lucide="layers-plus"></i><span>Recruitment</span></a></div>
        </div>
        <div class="nav-item-group">
          <button class="nav-item has-submenu" data-module="applicationmgt"><div class="nav-item-content"><i data-lucide="contact-round"></i><span>Applicant Management</span></div><i data-lucide="chevron-down" class="submenu-icon"></i></button>
          <div class="submenu" id="submenu-applicationmgt"><a href="applicationmgt.php" class="submenu-item"><i data-lucide="contact-round"></i><span>Applicant Management</span></a></div>
        </div>
        <div class="nav-item-group">
          <button class="nav-item has-submenu" data-module="newhiredonboard"><div class="nav-item-content"><i data-lucide="user-plus"></i><span>New Hired Onboard</span></div><i data-lucide="chevron-down" class="submenu-icon"></i></button>
          <div class="submenu" id="submenu-newhiredonboard"><a href="newhiredonboard.php" class="submenu-item"><i data-lucide="user-plus"></i><span>New Hired Onboard</span></a></div>
        </div>
      </div>
      <div class="nav-section">
        <span class="nav-section-title">HUMAN RESOURCES II</span>
        <div class="nav-item-group">
          <button class="nav-item has-submenu" data-module="accounts"><div class="nav-item-content"><i data-lucide="users"></i><span>Account Management</span></div><i data-lucide="chevron-down" class="submenu-icon"></i></button>
          <div class="submenu" id="submenu-accounts">
            <a href="useraccount.php" class="submenu-item"><i data-lucide="user-plus"></i><span>User Accounts</span></a>
            <a href="rolespermission.php" class="submenu-item"><i data-lucide="contact-round"></i><span>Roles &amp; Permissions</span></a>
            <a href="securitysetting.php" class="submenu-item"><i data-lucide="user-cog"></i><span>Security Settings</span></a>
            <a href="auditlogs.php" class="submenu-item"><i data-lucide="book-user"></i><span>Audit Logs</span></a>
          </div>
        </div>
        <div class="nav-item-group">
          <button class="nav-item has-submenu" data-module="competency"><div class="nav-item-content"><i data-lucide="pickaxe"></i><span>Competency Management</span></div><i data-lucide="chevron-down" class="submenu-icon"></i></button>
          <div class="submenu" id="submenu-competency">
            <a href="competencylibrary.php" class="submenu-item"><i data-lucide="book-text"></i><span>Competency Library</span></a>
            <a href="competencycategory.php" class="submenu-item"><i data-lucide="chart-bar-stacked"></i><span>Competency Category</span></a>
            <a href="competencylevel.php" class="submenu-item"><i data-lucide="circle-gauge"></i><span>Competency Level</span></a>
            <a href="competencyposition.php" class="submenu-item"><i data-lucide="briefcase"></i><span>Competency Position</span></a>
            <a href="competencyemployee.php" class="submenu-item"><i data-lucide="square-user"></i><span>Competency Employee</span></a>
            <a href="bankquestion.php" class="submenu-item"><i data-lucide="book-open-check"></i><span>Bank Question</span></a>
          </div>
        </div>
        <div class="nav-item-group">
          <button class="nav-item has-submenu" data-module="training"><div class="nav-item-content"><i data-lucide="briefcase-business"></i><span>Training Management</span></div><i data-lucide="chevron-down" class="submenu-icon"></i></button>
          <div class="submenu" id="submenu-training"><a href="training.php" class="submenu-item"><i data-lucide="briefcase-business"></i><span>Training Management</span></a></div>
        </div>
        <div class="nav-item-group">
          <button class="nav-item has-submenu" data-module="succession"><div class="nav-item-content"><i data-lucide="notebook-pen"></i><span>Succession Planning</span></div><i data-lucide="chevron-down" class="submenu-icon"></i></button>
          <div class="submenu" id="submenu-succession"><a href="succession.php" class="submenu-item"><i data-lucide="notebook-pen"></i><span>Succession Planning</span></a></div>
        </div>
        <div class="nav-item-group">
          <button class="nav-item has-submenu" data-module="learning"><div class="nav-item-content"><i data-lucide="notebook-text"></i><span>Learning Management</span></div><i data-lucide="chevron-down" class="submenu-icon"></i></button>
          <div class="submenu" id="submenu-learning"><a href="learning.php" class="submenu-item"><i data-lucide="notebook-text"></i><span>Learning Management</span></a></div>
        </div>
      </div>
      <div class="nav-section">
        <span class="nav-section-title">HUMAN RESOURCES III</span>
        <div class="nav-item-group">
          <button class="nav-item has-submenu" data-module="shift"><div class="nav-item-content"><i data-lucide="calendar-check"></i><span>Shift &amp; Scheduling</span></div><i data-lucide="chevron-down" class="submenu-icon"></i></button>
          <div class="submenu" id="submenu-shift"><a href="admin_roster.php" class="submenu-item"><i data-lucide="send-to-back"></i><span>Shift &amp; Scheduling</span></a></div>
        </div>
        <div class="nav-item-group">
          <button class="nav-item has-submenu" data-module="claims"><div class="nav-item-content"><i data-lucide="receipt-text"></i><span>Claims &amp; Reimbursements</span></div><i data-lucide="chevron-down" class="submenu-icon"></i></button>
          <div class="submenu" id="submenu-claims"><a href="claims.php" class="submenu-item"><i data-lucide="receipt-text"></i><span>Claims &amp; Reimbursements</span></a></div>
        </div>
        <div class="nav-item-group">
          <button class="nav-item has-submenu" data-module="time"><div class="nav-item-content"><i data-lucide="clock"></i><span>Time &amp; Attendance</span></div><i data-lucide="chevron-down" class="submenu-icon"></i></button>
          <div class="submenu" id="submenu-time"><a href="time.php" class="submenu-item"><i data-lucide="clock"></i><span>Time &amp; Attendance</span></a></div>
        </div>
        <div class="nav-item-group">
          <button class="nav-item has-submenu" data-module="timesheet"><div class="nav-item-content"><i data-lucide="calendar-days"></i><span>Timesheet</span></div><i data-lucide="chevron-down" class="submenu-icon"></i></button>
          <div class="submenu" id="submenu-timesheet"><a href="timesheet.php" class="submenu-item"><i data-lucide="calendar-days"></i><span>Timesheet</span></a></div>
        </div>
        <div class="nav-item-group">
          <button class="nav-item has-submenu" data-module="leave"><div class="nav-item-content"><i data-lucide="tickets-plane"></i><span>Leave Management</span></div><i data-lucide="chevron-down" class="submenu-icon"></i></button>
          <div class="submenu" id="submenu-leave"><a href="leave.php" class="submenu-item"><i data-lucide="tickets-plane"></i><span>Leave Management</span></a></div>
        </div>
      </div>
      <div class="nav-section">
        <span class="nav-section-title">HUMAN RESOURCES IV</span>
        <div class="nav-item-group">
          <button class="nav-item has-submenu" data-module="corehumancapital"><div class="nav-item-content"><i data-lucide="book-user"></i><span>Core Human Capital</span></div><i data-lucide="chevron-down" class="submenu-icon"></i></button>
          <div class="submenu" id="submenu-corehumancapital">
            <a href="dispatch.php" class="submenu-item"><i data-lucide="send"></i><span>Master Data Dispatch</span></a>
            <a href="orgprofile.php" class="submenu-item"><i data-lucide="building-2"></i><span>Organization Profile</span></a>
            <a href="positioncatalog.php" class="submenu-item"><i data-lucide="user-star"></i><span>Position Catalog</span></a>
            <a href="employeemaster.php" class="submenu-item"><i data-lucide="file-user"></i><span>Employee Master Files</span></a>
            <a href="informationapproval.php" class="submenu-item"><i data-lucide="file-check"></i><span>Information Approval</span></a>
            <a href="bankform.php" class="submenu-item"><i data-lucide="file-text"></i><span>Bank Form Management</span></a>
            <a href="auditlogs.php" class="submenu-item"><i data-lucide="book-user"></i><span>Audit Logs</span></a>
          </div>
        </div>
        <div class="nav-item-group">
          <button class="nav-item has-submenu" data-module="planning"><div class="nav-item-content"><i data-lucide="circle-pile"></i><span>Compensation Planning</span></div><i data-lucide="chevron-down" class="submenu-icon"></i></button>
          <div class="submenu" id="submenu-planning">
            <a href="comintake.php" class="submenu-item"><i data-lucide="layout-dashboard"></i><span>Master Data Intake</span></a>
            <a href="salary.php" class="submenu-item"><i data-lucide="banknote"></i><span>Salary &amp; Scales Management</span></a>
            <a href="statutory.php" class="submenu-item"><i data-lucide="scale"></i><span>Statutory Contributions</span></a>
            <a href="matrix.php" class="submenu-item"><i data-lucide="percent"></i><span>Merit Matrix Structure</span></a>
            <a href="allowance.php" class="submenu-item"><i data-lucide="gift"></i><span>Allowance Structure</span></a>
            <a href="cycle.php" class="submenu-item"><i data-lucide="notebook-pen"></i><span>Compensation Structure Management</span></a>
          </div>
        </div>
        <div class="nav-item-group active">
          <button class="nav-item has-submenu active" data-module="payroll"><div class="nav-item-content"><i data-lucide="banknote"></i><span>Payroll Management</span></div><i data-lucide="chevron-down" class="submenu-icon"></i></button>
          <div class="submenu" id="submenu-payroll" style="max-height:500px;">
            <a href="timesheetdata.php" class="submenu-item active"><i data-lucide="calendar-range"></i><span>Timesheet Data</span></a>
            <a href="comperules.php" class="submenu-item"><i data-lucide="boxes"></i><span>Compensation Rules</span></a>
            <a href="payroll.php" class="submenu-item"><i data-lucide="play-circle"></i><span>Payroll Processing</span></a>
            <a href="#" class="submenu-item"><i data-lucide="history"></i><span>Payroll History</span></a>
            <a href="#" class="submenu-item"><i data-lucide="file-check"></i><span>Approvals</span></a>
          </div>
        </div>
      </div>
      <div class="nav-section">
        <span class="nav-section-title">FINANCE</span>
        <div class="nav-item-group">
          <button class="nav-item has-submenu" data-module="budget"><div class="nav-item-content"><i data-lucide="hand-coins"></i><span>Budget Management</span></div><i data-lucide="chevron-down" class="submenu-icon"></i></button>
          <div class="submenu" id="submenu-budget"><a href="positionrequest.php" class="submenu-item"><i data-lucide="badge-dollar-sign"></i><span>Position Requests</span></a></div>
        </div>
      </div>
      <div class="nav-section">
        <span class="nav-section-title">SETTINGS</span>
        <a href="#" class="nav-item"><i data-lucide="settings"></i><span>Configuration</span></a>
        <a href="#" class="nav-item"><i data-lucide="shield"></i><span>Security</span></a>
      </div>
    </nav>

    <div class="sidebar-footer">
      <div class="user-profile">
        <div class="user-avatar"><img src="../../img/profile.png" alt="User"></div>
        <div class="user-info">
          <span class="user-name"><?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?></span>
          <span class="user-role"><?php echo htmlspecialchars($_SESSION['user_role'] ?? 'Administrator'); ?></span>
        </div>
        <button class="user-menu-btn" id="userMenuBtn"><i data-lucide="more-vertical"></i></button>
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
        <button class="mobile-menu-btn" id="mobileMenuBtn"><i data-lucide="menu"></i></button>
        <div class="header-title">
          <h1>Timesheet Data</h1>
          <p>Review approved timesheet summaries before syncing to Payroll Processing.</p>
        </div>
      </div>
      <div class="header-right">
        <div class="header-clock"><span id="realTimeClock"></span></div>
        <button class="theme-toggle" id="themeToggle" aria-label="Toggle theme">
          <i data-lucide="sun" class="sun-icon"></i>
          <i data-lucide="moon" class="moon-icon"></i>
        </button>
        <button class="icon-btn"><i data-lucide="bell"></i></button>
      </div>
    </header>

    <div class="content-wrapper">

      <?php if (!$hasData): ?>
      <!-- ── EMPTY STATE ── -->
      <div class="empty-state-container">
        <div style="width:90px;height:90px;border-radius:24px;background:rgba(245,158,11,0.1);display:flex;align-items:center;justify-content:center;">
          <i data-lucide="calendar-off" style="width:44px;height:44px;color:#f59e0b;"></i>
        </div>
        <div>
          <h2 style="font-size:1.5rem;font-weight:700;color:var(--text-primary);margin-bottom:8px;">No Timesheet Data Available</h2>
          <p style="color:var(--text-secondary);font-size:14px;max-width:440px;line-height:1.7;">
            There are no timesheet records yet.<br>
            Timesheet data must be entered and approved before it can be synced to Payroll.
          </p>
        </div>
        <a href="timesheet.php" class="sync-banner-btn empty-state-link">
          <i data-lucide="calendar-plus" style="width:16px;"></i> Go to Timesheet
        </a>
      </div>

      <?php else:
        $totalEmp     = array_sum(array_column($periods, 'EmpCount'));
        $totalRegHrs  = array_sum(array_column($periods, 'TotalRegular'));
        $totalOTHrs   = array_sum(array_column($periods, 'TotalOT'));
        $totalPayable = array_sum(array_column($periods, 'TotalPayable'));
      ?>

      <!-- â”€â”€ KPI Summary â”€â”€ -->
      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(175px,1fr));gap:16px;margin-bottom:24px;">
        <div class="stat-card">
          <div class="stat-icon" style="background:rgba(44,160,120,.12);color:#2ca078;"><i data-lucide="calendar-range"></i></div>
          <div class="stat-content"><span class="stat-label">Periods with Data</span><h3 class="stat-value"><?= count($periods) ?></h3></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon" style="background:rgba(59,130,246,.12);color:#3b82f6;"><i data-lucide="users"></i></div>
          <div class="stat-content"><span class="stat-label">Employee Records</span><h3 class="stat-value"><?= $totalEmp ?></h3></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon" style="background:rgba(139,92,246,.12);color:#8b5cf6;"><i data-lucide="clock"></i></div>
          <div class="stat-content"><span class="stat-label">Total Regular Hrs</span><h3 class="stat-value"><?= number_format($totalRegHrs, 1) ?></h3></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon" style="background:rgba(245,158,11,.12);color:#f59e0b;"><i data-lucide="zap"></i></div>
          <div class="stat-content"><span class="stat-label">Total OT Hrs</span><h3 class="stat-value"><?= number_format($totalOTHrs, 1) ?></h3></div>
        </div>
        <div class="stat-card">
          <div class="stat-icon" style="background:rgba(16,185,129,.12);color:#10b981;"><i data-lucide="check-circle-2"></i></div>
          <div class="stat-content">
            <span class="stat-label">Ready for Payroll</span>
            <h3 class="stat-value"><?= $eligibleCount ?></h3>
            <div class="stat-trend <?= $eligibleCount > 0 ? 'positive' : '' ?>">
              <i data-lucide="<?= $eligibleCount > 0 ? 'check' : 'clock' ?>"></i>
              <span><?= $eligibleCount > 0 ? 'Eligible periods' : 'None approved yet' ?></span>
            </div>
          </div>
        </div>
      </div>

      <!-- ── Sync Banner ── -->
      <?php if ($eligibleCount > 0): ?>
      <div class="sync-banner">
        <div style="display:flex;align-items:center;gap:14px;">
          <div class="sync-banner-icon">
            <i data-lucide="send"></i>
          </div>
          <div>
            <div class="sync-banner-title"><?= $eligibleCount ?> period<?= $eligibleCount > 1 ? 's' : '' ?> ready to sync to payroll</div>
            <div class="sync-banner-subtitle">Periods marked APPROVED or FINALIZED can be used when creating a new payroll batch.</div>
          </div>
        </div>
        <a href="payroll.php" class="sync-banner-btn">
          <i data-lucide="play-circle" style="width:16px;"></i> Go to Payroll Processing
        </a>
      </div>
      <?php else: ?>
      <div class="alert-banner">
        <i data-lucide="alert-triangle" class="alert-banner-icon"></i>
        <div class="alert-banner-text">
          <strong style="color:var(--text-primary);">No periods eligible for payroll yet.</strong>
          Periods must have status <strong>APPROVED</strong> or <strong>FINALIZED</strong> before syncing to payroll.
        </div>
      </div>
      <?php endif; ?>

      <!-- â”€â”€ Period Summaries Table â”€â”€ -->
      <div class="content-card">
        <div class="card-header">
          <div>
            <h3 class="card-title">Timesheet Period Summaries</h3>
            <p class="card-subtitle">Aggregated from <code>timesheet_employee_summary</code> linked to <code>timesheet_period</code></p>
          </div>
        </div>
        <div class="card-body timesheet-table-wrapper" style="padding:0;">
          <table class="timesheet-table">
            <thead>
              <tr>
                <th>Period</th>
                <th>Dept</th>
                <th>Status</th>
                <th style="text-align:right;">Emps</th>
                <th style="text-align:right;">Reg Hrs</th>
                <th style="text-align:right;">OT Hrs</th>
                <th style="text-align:right;">Night Diff</th>
                <th style="text-align:right;">Reg Hol</th>
                <th style="text-align:right;">Spec Hol</th>
                <th style="text-align:right;">Late+UT</th>
                <th style="text-align:right;">Absences</th>
                <th style="text-align:right;color:var(--text-primary);">Payable Hrs</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($periods as $p):
                $sc = match($p['Status']) {
                    'APPROVED'          => '#2ca078',
                    'FINALIZED'         => '#3b82f6',
                    'PAYROLL_PROCESSED' => '#8b5cf6',
                    'FOR_REVIEW'        => '#f59e0b',
                    'RETURNED'          => '#ef4444',
                    default             => '#9ca3af'
                };
                $lateTotal = (int)($p['TotalLate'] ?? 0) + (int)($p['TotalUT'] ?? 0);
              ?>
              <tr>
                <td>
                  <div style="font-weight:600;color:var(--text-primary);font-size:14px;"><?= date('M d', strtotime($p['StartDate'])) ?> – <?= date('M d, Y', strtotime($p['EndDate'])) ?></div>
                  <div style="font-size:11px;color:var(--text-tertiary);margin-top:2px;">Period #<?= $p['PeriodID'] ?></div>
                </td>
                <td style="font-weight:500;"><?= htmlspecialchars($p['DepartmentName'] ?? '—') ?></td>
                <td>
                  <span style="background:<?=$sc?>15;color:<?=$sc?>;border:1px solid <?=$sc?>30;padding:4px 12px;border-radius:20px;font-size:11px;font-weight:700;display:inline-block;"><?= htmlspecialchars($p['Status']) ?></span>
                </td>
                <td style="text-align:right;font-weight:600;color:var(--text-primary);"><?= (int)$p['EmpCount'] ?></td>
                <td style="text-align:right;font-variant-numeric: tabular-nums;"><?= number_format($p['TotalRegular'], 2) ?></td>
                <td style="text-align:right;font-variant-numeric: tabular-nums;color:<?= $p['TotalOT'] > 0 ? '#f59e0b' : 'var(--text-secondary)' ?>;font-weight:<?= $p['TotalOT'] > 0 ? 600 : 400 ?>;"><?= number_format($p['TotalOT'], 2) ?></td>
                <td style="text-align:right;font-variant-numeric: tabular-nums;"><?= number_format($p['TotalND'], 2) ?></td>
                <td style="text-align:right;font-variant-numeric: tabular-nums;"><?= number_format($p['TotalRegHol'], 2) ?></td>
                <td style="text-align:right;font-variant-numeric: tabular-nums;"><?= number_format($p['TotalSpecHol'], 2) ?></td>
                <td style="text-align:right;font-variant-numeric: tabular-nums;color:<?= $lateTotal > 0 ? '#ef4444' : 'var(--text-secondary)' ?>;font-weight:<?= $lateTotal > 0 ? 600 : 400 ?>;"><?= $lateTotal > 0 ? $lateTotal.' min' : '—' ?></td>
                <td style="text-align:right;font-variant-numeric: tabular-nums;color:<?= $p['TotalAbsences'] > 0 ? '#ef4444' : 'var(--text-secondary)' ?>;font-weight:<?= $p['TotalAbsences'] > 0 ? 600 : 400 ?>;"><?= $p['TotalAbsences'] > 0 ? number_format($p['TotalAbsences'], 2) : '—' ?></td>
                <td style="text-align:right;font-weight:700;font-size:14px;color:var(--text-primary);font-variant-numeric: tabular-nums;"><?= number_format($p['TotalPayable'], 2) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

      <?php endif; ?>

    </div><!-- end content-wrapper -->
  </main>

  <script src="../../js/timesheetdata.js"></script>
  <script>lucide.createIcons();</script>
</body>
</html>
