<?php
require_once __DIR__ . "/includes/auth_employee.php";

$employeeName = $_SESSION['employee_name'] ?? $_SESSION['full_name'] ?? 'Employee';
$employeeId = (int)($_SESSION['employee_id'] ?? $_SESSION['EmployeeID'] ?? 0);
$accountId = (int)($_SESSION['account_id'] ?? $_SESSION['AccountID'] ?? $_SESSION['user_id'] ?? 0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Timesheet | <?php echo htmlspecialchars($employeeName); ?></title>

    <link rel="stylesheet" href="../../css/ess/timesheet.css?v=<?php echo time(); ?>">
    <script src="https://cdn.jsdelivr.net/npm/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<?php include 'sidebar.php'; ?>

<main class="main-content">
    <header class="page-header">
        <div class="header-left">
            <h1>My Timesheet</h1>
            <div class="page-top-meta">
                <span class="status-badge draft" id="periodStatusBadge">Loading...</span>

                <span class="mini-info">
                    <i data-lucide="user"></i>
                    <?php echo htmlspecialchars($employeeName); ?>
                </span>

                <span class="mini-info" id="selectedPeriodLabel">
                    <i data-lucide="calendar"></i>
                    Loading period...
                </span>
            </div>
        </div>

        <div class="header-right">
            <button class="btn-secondary" id="btnRefresh">
                <i data-lucide="refresh-cw"></i> Refresh
            </button>
            <?php include 'theme.php'; ?>
        </div>
    </header>

    <div class="control-panel-custom">
        <div class="control-left">
            <select class="select-styled" id="periodSelect">
                <option value="">Loading periods...</option>
            </select>
        </div>

        <div class="search-wrapper">
            <i data-lucide="search"></i>
            <input type="text" id="searchInput" class="search-input-styled" placeholder="Search date, shift, status, or remarks...">
        </div>
    </div>

    <div class="roster-stats">
        <div class="stat-card">
            <span class="stat-label">Regular Hours</span>
            <strong class="stat-value" id="statRegularHours">0.00</strong>
            <p class="stat-subtext">Total for selected period</p>
        </div>

        <div class="stat-card">
            <span class="stat-label">OT Hours</span>
            <strong class="stat-value" id="statOtHours">0.00</strong>
            <p class="stat-subtext">Overtime rendered</p>
        </div>

        <div class="stat-card">
            <span class="stat-label">Late (m)</span>
            <strong class="stat-value danger" id="statLateMinutes">0</strong>
            <p class="stat-subtext">Total tardiness</p>
        </div>

        <div class="stat-card">
            <span class="stat-label">Undertime</span>
            <strong class="stat-value warning" id="statUndertimeMinutes">0</strong>
            <p class="stat-subtext">Minutes below required</p>
        </div>

        <div class="stat-card">
            <span class="stat-label">Night Diff</span>
            <strong class="stat-value" id="statNightDiffHours">0.00</strong>
            <p class="stat-subtext">Night differential hours</p>
        </div>
    </div>

    <div class="content-card">
        <div class="card-header-block">
            <h3 class="card-title">Daily Timesheet Summary</h3>
            <p class="card-subtitle">Showing your daily attendance totals, including break time, for the selected cutoff period.</p>
        </div>

        <div class="card-body">
            <div class="roster-table-wrapper">
                <table class="roster-table" id="timesheetTable">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Day</th>
                            <th>Shift</th>
                            <th>Scheduled</th>
                            <th>Break</th>
                            <th>Time In</th>
                            <th>Time Out</th>
                            <th>Reg</th>
                            <th>OT</th>
                            <th>Late</th>
                            <th>Undertime</th>
                            <th>Status</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody id="tsBody">
                        <tr>
                            <td colspan="13" class="empty-row">Loading timesheet...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<script>
window.TIMESHEET_CTX = {
    employeeId: <?php echo (int)$employeeId; ?>,
    accountId: <?php echo (int)$accountId; ?>,
    employeeName: <?php echo json_encode($employeeName); ?>
};
</script>
<script src="../../js/ess/timesheet.js?v=<?php echo time(); ?>"></script>
<script>
    if (window.lucide) {
        lucide.createIcons();
    }
</script>
</body>
</html>