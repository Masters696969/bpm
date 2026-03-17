<?php
require_once __DIR__ . "/includes/auth_employee.php";

$employeeName = $_SESSION['user_name'] ?? $_SESSION['username'] ?? $_SESSION['employee_name'] ?? 'Employee';
$accountId = (int)($_SESSION['account_id'] ?? $_SESSION['AccountID'] ?? $_SESSION['user_id'] ?? 0);
$employeeId = (int)($_SESSION['employee_id'] ?? $_SESSION['EmployeeID'] ?? 0);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Home | ESS</title>

<link rel="stylesheet" href="../../css/ess/dashboard.css?v=<?php echo time(); ?>">
<script src="https://cdn.jsdelivr.net/npm/lucide@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

<?php include 'sidebar.php'; ?>

<main class="main-content">

<header class="page-header">

<div class="header-left">
    <h1>Home</h1>

    <div class="page-top-meta">

        <span class="mini-info">
            <i data-lucide="user"></i>
            <?php echo htmlspecialchars($employeeName); ?>
        </span>

        <span class="mini-info" id="todayLabel">
            <i data-lucide="calendar-days"></i>
            Loading date...
        </span>

    </div>
</div>


<div class="header-right">

    <button class="btn-secondary" id="btnRefreshAnnouncements">
        <i data-lucide="refresh-cw"></i>
        Refresh
    </button>

    <?php include 'theme.php'; ?>

</div>

</header>



<section class="dashboard-layout">

<div class="content-card">

<div class="card-header-block">
<h3 class="card-title">Company Announcements</h3>
<p class="card-subtitle">
Latest updates, reminders, and notices for employees.
</p>
</div>


<div class="card-body">

<div id="announcementList" class="announcement-list">

<div class="loading-state">
<i data-lucide="loader-circle"></i>
<span>Loading announcements...</span>
</div>

</div>

</div>

</div>

</section>

</main>


<script>
window.ESS_DASHBOARD_CTX = {
accountId: <?php echo (int)$accountId; ?>,
employeeId: <?php echo (int)$employeeId; ?>,
employeeName: <?php echo json_encode($employeeName); ?>
};
</script>

<script src="../../js/ess/dashboard.js?v=<?php echo time(); ?>"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
if (window.lucide) {
lucide.createIcons();
}
});
</script>

</body>
</html>