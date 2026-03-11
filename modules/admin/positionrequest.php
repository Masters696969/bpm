<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../../login.php");
    exit();
}
require_once '../../config/config.php';

$page = 'positionrequest';
$module = 'budget';

// Handle Actions (Approve/Reject)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $requestId = $_POST['request_id'] ?? '';
    $action = $_POST['action'];

    if ($action === 'approve_request' && !empty($requestId)) {
        // Fetch request details
        $reqQuery = $conn->prepare("SELECT * FROM position_requests WHERE RequestID = ?");
        $reqQuery->bind_param("i", $requestId);
        $reqQuery->execute();
        $request = $reqQuery->get_result()->fetch_assoc();
        $reqQuery->close();

        if ($request) {
            $conn->begin_transaction();
            try {
                $type = $request['RequestType'];
                $targetId = $request['TargetPositionID'];

                if ($type === 'Add') {
                    // Insert into positions
                    $stmt = $conn->prepare("INSERT INTO positions (PositionName, PositionCode, DepartmentID, SalaryGradeID, AuthorizedHeadcount) VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssiii", $request['PositionName'], $request['PositionCode'], $request['DepartmentID'], $request['SalaryGradeID'], $request['AuthorizedHeadcount']);
                    $stmt->execute();
                    $msg = "Request approved and position added to catalog.";
                } elseif ($type === 'Update') {
                    // Update existing position
                    $stmt = $conn->prepare("UPDATE positions SET PositionName = ?, PositionCode = ?, DepartmentID = ?, SalaryGradeID = ?, AuthorizedHeadcount = ? WHERE PositionID = ?");
                    $stmt->bind_param("ssiiii", $request['PositionName'], $request['PositionCode'], $request['DepartmentID'], $request['SalaryGradeID'], $request['AuthorizedHeadcount'], $targetId);
                    $stmt->execute();
                    $msg = "Change request approved and position updated.";
                } elseif ($type === 'Delete') {
                    // Delete position
                    $stmt = $conn->prepare("DELETE FROM positions WHERE PositionID = ?");
                    $stmt->bind_param("i", $targetId);
                    $stmt->execute();
                    $msg = "Deletion request approved and position removed.";
                }

                // Update request status
                $update = $conn->prepare("UPDATE position_requests SET Status = 'Approved' WHERE RequestID = ?");
                $update->bind_param("i", $requestId);
                $update->execute();

                $conn->commit();
                $_SESSION['success_message'] = $msg;
            } catch (Exception $e) {
                $conn->rollback();
                $_SESSION['error_message'] = "Error approving request: " . $e->getMessage();
            }
        }
    } elseif ($action === 'reject_request' && !empty($requestId)) {
        $update = $conn->prepare("UPDATE position_requests SET Status = 'Rejected' WHERE RequestID = ?");
        $update->bind_param("i", $requestId);
        if ($update->execute()) {
            $_SESSION['success_message'] = "Request has been rejected.";
        } else {
            $_SESSION['error_message'] = "Error rejecting request.";
        }
    }
    header("Location: positionrequest.php");
    exit();
}

// 1. Total Employee Cost (Current Basis: Filled Slots * Min Salary)
$totalCostQuery = "
    SELECT SUM(sg.MinSalary) as total 
    FROM employmentinformation ei
    JOIN positions p ON ei.PositionID = p.PositionID
    JOIN salary_grades sg ON p.SalaryGradeID = sg.SalaryGradeID
";
$totalCostResult = $conn->query($totalCostQuery);
$totalEmployeeCost = $totalCostResult->fetch_assoc()['total'] ?? 0;

// 2. Vacancy Cost (Projected monthly cost for open slots using MinSalary)
// Logic: (Authorized - Current) * Min Salary
$vacancySql = "
    SELECT SUM( (p.AuthorizedHeadcount - (SELECT COUNT(*) FROM employmentinformation ei WHERE ei.PositionID = p.PositionID)) * sg.MinSalary ) as vacancy_cost
    FROM positions p
    JOIN salary_grades sg ON p.SalaryGradeID = sg.SalaryGradeID
    WHERE p.AuthorizedHeadcount > (SELECT COUNT(*) FROM employmentinformation ei WHERE ei.PositionID = p.PositionID)
";
$vacancyResult = $conn->query($vacancySql);
$totalVacancyCost = $vacancyResult->fetch_assoc()['vacancy_cost'] ?? 0;
// Note: Based on DB tables, this currently sums to 512,000 across all departments.
// HR/Admin specific vacancy is 29,000 (HR Officer).

// 3. Requested Impact (Net budget change from pending requests)
$impactSql = "
    SELECT 
        SUM(CASE 
            WHEN pr.RequestType = 'Add' THEN (pr.AuthorizedHeadcount * sg.MinSalary)
            WHEN pr.RequestType = 'Delete' THEN -(pr.AuthorizedHeadcount * sg.MinSalary)
            WHEN pr.RequestType = 'Update' THEN (
                (pr.AuthorizedHeadcount * sg.MinSalary) - 
                (SELECT p.AuthorizedHeadcount * sg2.MinSalary 
                 FROM positions p 
                 JOIN salary_grades sg2 ON p.SalaryGradeID = sg2.SalaryGradeID 
                 WHERE p.PositionID = pr.TargetPositionID)
            )
            ELSE 0 
        END) as impact
    FROM position_requests pr
    JOIN salary_grades sg ON pr.SalaryGradeID = sg.SalaryGradeID
    WHERE pr.Status = 'Pending'
";
$impactResult = $conn->query($impactSql);
$totalRequestedImpact = $impactResult->fetch_assoc()['impact'] ?? 0;

// Fetch Pending Requests
$requestsSql = "
    SELECT pr.*, d.DepartmentName, sg.GradeName, sg.MinSalary
    FROM position_requests pr
    JOIN department d ON pr.DepartmentID = d.DepartmentID
    JOIN salary_grades sg ON pr.SalaryGradeID = sg.SalaryGradeID
    WHERE pr.Status = 'Pending'
    ORDER BY pr.DateRequested DESC
";
$requestsResult = $conn->query($requestsSql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link rel="stylesheet" href="../../css/positionrequest.css?v=1.2">
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
        <span class="nav-section-title">ADMINISTRATION</span>
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
        </div>
       <div class="nav-section">
        <span class="nav-section-title">Human Resources</span>
          <div class="nav-item-group <?php echo ($module === 'corehumancapital') ? 'active' : ''; ?>">
          <button class="nav-item has-submenu" data-module="corehumancapital">
            <div class="nav-item-content">
              <i data-lucide="book-user"></i>
              <span>Core Human Capital</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-corehumancapital">
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
            <a href="salary.php" class="submenu-item <?php echo ($page === 'salarymgt') ? 'active' : ''; ?>">
              <i data-lucide="banknote"></i>
              <span>Salary & Scales Management</span>
            </a>
            <a href="statutory.php" class="submenu-item <?php echo ($page === 'statutory') ? 'active' : ''; ?>">
              <i data-lucide="scale"></i>
              <span>Statutory Contributions</span>
            </a>
            <a href="matrix.php" class="submenu-item <?php echo ($page === 'matrix') ? 'active' : ''; ?>">
              <i data-lucide="scale"></i>
              <span>Merit Matrix Structure</span>
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
            <a href="recruitment.php" class="nav-item <?php echo ($page === 'recruitment') ? 'active' : ''; ?>">
              <i data-lucide="layers-plus"></i>
              <span>Recruitment</span>
            </a>
            <a href="applicationmgt.php" class="nav-item <?php echo ($page === 'applicationmgt') ? 'active' : ''; ?>">
              <i data-lucide="contact-round"></i>
              <span>Applicant Management</span>
            </a>
      <a href="newhiredonboard.php" class="nav-item <?php echo ($page === 'newhiredonboard') ? 'active' : ''; ?>">
              <i data-lucide="user-plus"></i>
              <span>New Hired Onboard</span>
            </a>
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
          <h1>Position Requests</h1>
          <p>Financial impact and organizational approval management.</p>
        </div>
      </div>
        <div class="header-clock">
          <span id="realTimeClock"></span>
        </div>
        <?php if (isset($_SESSION['success_message'])): ?>
          <script>
            document.addEventListener('DOMContentLoaded', () => {
              Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: '<?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
              });
            });
          </script>
        <?php endif; ?>
        <?php if (isset($_SESSION['error_message'])): ?>
          <script>
            document.addEventListener('DOMContentLoaded', () => {
              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '<?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>',
                confirmButtonColor: '#ef4444'
              });
            });
          </script>
        <?php endif; ?>

        <button class="theme-toggle" id="themeToggle" aria-label="Toggle theme">
          <i data-lucide="sun" class="sun-icon"></i>
          <i data-lucide="moon" class="moon-icon"></i>
        </button>
        <div class="header-notifications" style="position: relative;">
          <button class="icon-btn" id="bellIconBtn">
            <i data-lucide="bell"></i>
            <span class="notification-badge" id="notifBadge" 
               style="<?php echo ($requestsResult->num_rows > 0) ? 'display: flex;' : 'display: none;'; ?>">
               <?php echo $requestsResult->num_rows; ?>
            </span>
          </button>
          
          <div class="notification-dropdown" id="notifDropdown" style="display: none;">
            <div class="notif-dropdown-header">
              <span>Notifications</span>
              <button id="markReadBtn">Mark all as read</button>
            </div>
            <div id="notifList">
              <?php if ($requestsResult->num_rows === 0): ?>
                <div class="notif-empty">No pending requests.</div>
              <?php else: ?>
                <?php 
                  $requestsResult->data_seek(0);
                  while($r = $requestsResult->fetch_assoc()): 
                ?>
                  <div class="notif-item">
                    <div class="notif-item-content">
                      <span class="notif-title"><?php echo htmlspecialchars($r['PositionName']); ?></span>
                      <span class="notif-desc">New request for <?php echo htmlspecialchars($r['DepartmentName']); ?> (Headcount: <?php echo $r['AuthorizedHeadcount']; ?>)</span>
                    </div>
                    <span class="notif-time">Pending</span>
                  </div>
                <?php endwhile; $requestsResult->data_seek(0); ?>
              <?php endif; ?>
            </div>
            <div class="notif-dropdown-footer">
              <a href="#requests-table">View all requests</a>
            </div>
          </div>
        </div>
      </div>
    </header>

    <div class="content-wrapper">
      <!-- Stats Grid -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">
            <i data-lucide="wallet"></i>
          </div>
          <div class="stat-content">
            <span class="stat-label">Total Employee Cost</span>
            <h3 class="stat-value">&#8369;<?php echo number_format($totalEmployeeCost, 2); ?></h3>
            <div class="stat-trend">
              <span>Current Monthly Payroll</span>
            </div>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon" style="background: rgba(255, 193, 7, 0.1); color: var(--brand-yellow);">
            <i data-lucide="alert-circle"></i>
          </div>
          <div class="stat-content">
            <span class="stat-label">Projected Vacancy Cost</span>
            <h3 class="stat-value">&#8369;<?php echo number_format($totalVacancyCost, 2); ?></h3>
            <div class="stat-trend">
              <span>Cost of Open Positions</span>
            </div>
          </div>
        </div>

        <div class="stat-card highlight-card">
          <div class="stat-icon" style="background: rgba(44, 160, 120, 0.1); color: var(--brand-green);">
            <i data-lucide="trending-up"></i>
          </div>
          <div class="stat-content">
            <span class="stat-label">Requested Budget Impact</span>
            <h3 class="stat-value">&#8369;<?php echo number_format($totalRequestedImpact, 2); ?></h3>
            <div class="stat-trend positive">
              <i data-lucide="plus-circle"></i>
              <span>Potential Addition</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Requests Table -->
      <div class="content-card">
        <div class="card-header">
          <div>
            <h3 class="card-title">Pending Position Requests</h3>
            <p class="card-subtitle">New positions awaiting budget and organizational approval</p>
          </div>
          <div class="badge-status pending" style="padding: 6px 12px; border-radius: 20px;">
            <?php echo $requestsResult->num_rows; ?> Pending
          </div>
        </div>
        <div class="card-body" style="padding: 0;">
          <div class="table-responsive">
            <table class="role-table" style="width: 100%; border-collapse: collapse;">
              <thead>
                <tr style="text-align: left; border-bottom: 1px solid var(--border-color);">
                  <th style="padding: 16px;">Type</th>
                  <th style="padding: 16px;">Position Details</th>
                  <th style="padding: 16px;">Department</th>
                  <th style="padding: 16px;">Grade</th>
                  <th style="padding: 16px; text-align: center;">Headcount</th>
                  <th style="padding: 16px; text-align: right;">Monthly Impact</th>
                  <th style="padding: 16px; text-align: center;">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($requestsResult->num_rows > 0): ?>
                  <?php while ($req = $requestsResult->fetch_assoc()): ?>
                    <tr class="role-row-item">
                      <td style="padding: 16px;">
                        <?php 
                          $type = $req['RequestType'];
                          $typeBg = 'rgba(44, 160, 120, 0.1)';
                          $typeColor = 'var(--brand-green)';
                          if ($type === 'Update') { $typeBg = 'rgba(59, 130, 246, 0.1)'; $typeColor = '#3b82f6'; }
                          if ($type === 'Delete') { $typeBg = 'rgba(239, 68, 68, 0.1)'; $typeColor = '#ef4444'; }
                        ?>
                        <span style="padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 700; text-transform: uppercase; background: <?php echo $typeBg; ?>; color: <?php echo $typeColor; ?>;">
                          <?php echo $type; ?>
                        </span>
                      </td>
                      <td style="padding: 16px;">
                        <div style="font-weight: 600; color: var(--text-primary);"><?php echo htmlspecialchars($req['PositionName']); ?></div>
                        <div style="font-size: 12px; color: var(--text-tertiary);"><?php echo htmlspecialchars($req['PositionCode']); ?></div>
                      </td>
                      <td style="padding: 16px; color: var(--text-secondary);"><?php echo htmlspecialchars($req['DepartmentName']); ?></td>
                      <td style="padding: 16px;">
                        <span class="badge-status review" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                          <?php echo htmlspecialchars($req['GradeName']); ?>
                        </span>
                      </td>
                      <td style="padding: 16px; text-align: center; font-weight: 500;">
                        <?php echo $req['AuthorizedHeadcount']; ?>
                      </td>
                      <td style="padding: 16px; text-align: right; font-weight: 600; color: var(--brand-green);">
                        &#8369;<?php echo number_format($req['MinSalary'] * $req['AuthorizedHeadcount'], 2); ?>
                      </td>
                      <td style="padding: 16px; text-align: center;">
                        <div style="display: flex; gap: 8px; justify-content: center;">
                          <form id="approve-form-<?php echo $req['RequestID']; ?>" method="POST">
                            <input type="hidden" name="request_id" value="<?php echo $req['RequestID']; ?>">
                            <input type="hidden" name="action" value="approve_request">
                            <button type="button" class="action-btn" onclick="confirmAction('Approve Request?', 'This will add the position to the catalog.', 'question', 'Yes, Approve', 'approve-form-<?php echo $req['RequestID']; ?>')" style="padding: 6px 12px; background: rgba(44, 160, 120, 0.1); color: var(--brand-green); border: none; border-radius: 6px; cursor: pointer;">
                              <i data-lucide="check" style="width: 16px; height: 16px;"></i>
                            </button>
                          </form>
                          <form id="reject-form-<?php echo $req['RequestID']; ?>" method="POST">
                            <input type="hidden" name="request_id" value="<?php echo $req['RequestID']; ?>">
                            <input type="hidden" name="action" value="reject_request">
                            <button type="button" class="action-btn" onclick="confirmAction('Reject Request?', 'This request will be marked as rejected.', 'warning', 'Yes, Reject', 'reject-form-<?php echo $req['RequestID']; ?>')" style="padding: 6px 12px; background: rgba(239, 68, 68, 0.1); color: #ef4444; border: none; border-radius: 6px; cursor: pointer;">
                              <i data-lucide="x" style="width: 16px; height: 16px;"></i>
                            </button>
                          </form>
                        </div>
                      </td>
                    </tr>
                  <?php endwhile; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="7" style="padding: 48px; text-align: center; color: var(--text-tertiary);">
                      <i data-lucide="inbox" style="width: 48px; height: 48px; opacity: 0.2; margin-bottom: 12px;"></i>
                      <p>No pending position requests</p>
                    </td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </main>
  <script src="../../js/positionrequest.js"></script>
  <script>
    lucide.createIcons();
    <?php if (isset($_SESSION['success_message'])): ?>
        Swal.fire({
            icon: 'success',
            title: '<?php echo $_SESSION['success_message']; ?>',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['error_message'])): ?>
        Swal.fire({
            icon: 'error',
            title: '<?php echo $_SESSION['error_message']; ?>',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true
        });
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>
  </script>
</body>
</html>






