<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../../login.php");
    exit();
}

require_once '../../config/config.php';

$page = 'positioncatalog';
$module = 'corehumancapital';

// Handle Add Position (Direct Injection)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_position') {
    $posName = $_POST['position_name'] ?? '';
    $posCode = $_POST['position_code'] ?? '';
    $deptId = $_POST['department_id'] ?? '';
    $gradeId = $_POST['grade_id'] ?? '';
    $authorized = $_POST['authorized_headcount'] ?? 1;

    if (!empty($posName) && !empty($deptId) && !empty($gradeId)) {
        $stmt = $conn->prepare("INSERT INTO positions (PositionName, PositionCode, DepartmentID, SalaryGradeID, AuthorizedHeadcount) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiii", $posName, $posCode, $deptId, $gradeId, $authorized);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Position Created! The new role '$posName' is now live in the catalog.";
        } else {
            $_SESSION['error_message'] = "Error adding position: " . $conn->error;
        }
        $stmt->close();
    }
    header("Location: positioncatalog.php");
    exit();
}

// Handle Update Position (Redirect to Requests)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_position') {
    $posId = $_POST['position_id'] ?? '';
    $posName = $_POST['position_name'] ?? '';
    $posCode = $_POST['position_code'] ?? '';
    $deptId = $_POST['department_id'] ?? '';
    $gradeId = $_POST['grade_id'] ?? '';
    $authorized = $_POST['authorized_headcount'] ?? 1;
    $requestedBy = $_SESSION['username'] ?? 'System';

    if (!empty($posId) && !empty($posName)) {
        $stmt = $conn->prepare("INSERT INTO position_requests (RequestType, TargetPositionID, PositionName, PositionCode, DepartmentID, SalaryGradeID, AuthorizedHeadcount, RequestedBy, Status) VALUES ('Update', ?, ?, ?, ?, ?, ?, ?, 'Pending')");
        $stmt->bind_param("issiiis", $posId, $posName, $posCode, $deptId, $gradeId, $authorized, $requestedBy);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Change request for '$posName' has been submitted for approval.";
        } else {
            $_SESSION['error_message'] = "Error submitting update request: " . $conn->error;
        }
        $stmt->close();
    }
    header("Location: positioncatalog.php");
    exit();
}

// Handle Delete Position (Direct Action)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_position') {
    $posId = $_POST['position_id'] ?? '';
    $posName = $_POST['position_name'] ?? '';

    if (!empty($posId)) {
        $stmt = $conn->prepare("DELETE FROM positions WHERE PositionID = ?");
        $stmt->bind_param("i", $posId);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Position Deleted! '$posName' has been removed from the catalog.";
        } else {
            $_SESSION['error_message'] = "Error deleting position: " . $conn->error;
        }
        $stmt->close();
    }
    header("Location: positioncatalog.php");
    exit();
}

// Handle Send Requisition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'send_requisition') {
    $posId = $_POST['position_id'] ?? '';
    $requestedBy = $_SESSION['username'] ?? 'System';
    $logFile = '../../debug_log.txt';
    $log = date('[Y-m-d H:i:s]') . " REQUISITION REQUEST: posId=$posId, user=$requestedBy\n";
    
    if (!empty($posId)) {
        // Check if already exists
        $check = $conn->prepare("SELECT RequisitionID FROM recruitment_requisitions WHERE PositionID = ? AND Status IN ('Pending', 'Active', 'Posted')");
        $check->bind_param("i", $posId);
        $check->execute();
        $res = $check->get_result();
        if ($res->num_rows > 0) {
            $_SESSION['error_message'] = "A requisition for this position is already active.";
            $log .= "ERROR: Requisition already active for posId=$posId\n";
        } else {
            $stmt = $conn->prepare("INSERT INTO recruitment_requisitions (PositionID, RequestedBy, Status) VALUES (?, ?, 'Pending')");
            $stmt->bind_param("is", $posId, $requestedBy);
            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Hiring requisition sent to Recruitment module.";
                $log .= "SUCCESS: Requisition inserted for posId=$posId\n";
                file_put_contents($logFile, $log, FILE_APPEND);
                header("Location: positioncatalog.php");
                exit();
            } else {
                $_SESSION['error_message'] = "Error sending requisition: " . $conn->error;
                $log .= "DATABASE ERROR: " . $conn->error . "\n";
            }
            $stmt->close();
        }
        $check->close();
    } else {
        $log .= "ERROR: position_id is empty\n";
    }
    file_put_contents($logFile, $log, FILE_APPEND);
    header("Location: positioncatalog.php");
    exit();
}

// Handle Cancel Requisition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cancel_requisition') {
    $posId = $_POST['position_id'] ?? '';
    if (!empty($posId)) {
        $stmt = $conn->prepare("UPDATE recruitment_requisitions SET Status = 'Cancelled' WHERE PositionID = ? AND (Status IN ('Pending', 'Active', 'Posted') OR Status IS NULL OR Status = '')");
        $stmt->bind_param("i", $posId);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Hiring requisition cancelled successfully.";
        } else {
            $_SESSION['error_message'] = "Error cancelling requisition: " . $conn->error;
        }
        $stmt->close();
    }
    header("Location: positioncatalog.php");
    exit();
}

// Fetch filter parameters
$filterDept = $_GET['dept'] ?? 'all';

// Fetch departments for filtering and modal
$deptsResult = $conn->query("SELECT * FROM department ORDER BY DepartmentName");
$departments = [];
while ($row = $deptsResult->fetch_assoc()) {
    $departments[] = $row;
}

// Fetch salary grades for modal
$gradesResult = $conn->query("SELECT SalaryGradeID, GradeName FROM salary_grades ORDER BY SalaryGradeID");
$salaryGrades = [];
while ($row = $gradesResult->fetch_assoc()) {
    $salaryGrades[] = $row;
}

// Fetch positions with vacancy priority and recruitment status
$sql = "SELECT p.*, d.DepartmentName, sg.GradeName,
        (SELECT COUNT(*) FROM employmentinformation ei WHERE ei.PositionID = p.PositionID) as CurrentHeadcount,
        (SELECT COUNT(*) FROM recruitment_requisitions rr WHERE rr.PositionID = p.PositionID AND rr.Status IN ('Pending', 'Active', 'Posted')) as HasRequisition
        FROM positions p
        JOIN department d ON p.DepartmentID = d.DepartmentID
        JOIN salary_grades sg ON p.SalaryGradeID = sg.SalaryGradeID";

if ($filterDept !== 'all') {
    $sql .= " WHERE p.DepartmentID = " . (int)$filterDept;
}

// Vacancy priority logic: (Authorized - Current) > 0 comes first
$sql .= " ORDER BY (p.AuthorizedHeadcount - (SELECT COUNT(*) FROM employmentinformation ei WHERE ei.PositionID = p.PositionID)) DESC, HasRequisition DESC, p.PositionName ASC";

$positionsResult = $conn->query($sql);
$groupedPositions = [];
while ($row = $positionsResult->fetch_assoc()) {
    $deptName = $row['DepartmentName'];
    if (!isset($groupedPositions[$deptName])) {
        $groupedPositions[$deptName] = [
            'DepartmentID' => $row['DepartmentID'],
            'Positions' => [],
            'TotalVacancies' => 0,
            'TotalRequisitions' => 0
        ];
    }
    $groupedPositions[$deptName]['Positions'][] = $row;
    if ($row['CurrentHeadcount'] < $row['AuthorizedHeadcount']) {
        $groupedPositions[$deptName]['TotalVacancies']++;
        if ($row['HasRequisition'] > 0) {
            $groupedPositions[$deptName]['TotalRequisitions']++;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link rel="stylesheet" href="../../css/chcpositioncatalog.css?v=1.2">
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
        
        <a href="dashboard.php" class="nav-item <?php echo ($page === 'dashboard') ? 'active' : ''; ?>">
          <i data-lucide="chart-no-axes-combined"></i>
          <span>HR ANALYTICS</span>
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
            <a href="dispatch.php" class="submenu-item">
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
             <a href="informationrq.php" class="submenu-item <?php echo ($page === 'informationrq') ? 'active' : ''; ?>">
              <i data-lucide="user-round-pen"></i>
              <span>Information Request</span>
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
          <h1>Position Catalog</h1>
          <p>Manage company positions, authorized headcount, and monitoring vacancies.</p>
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
<?php if (isset($_SESSION['success_message'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: <?php echo json_encode($_SESSION['success_message']); ?>,
                confirmButtonColor: '#2ca078'
            });
        });
    </script>
<?php unset($_SESSION['success_message']); endif; ?>

<?php if (isset($_SESSION['error_message'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: <?php echo json_encode($_SESSION['error_message']); ?>,
                confirmButtonColor: '#ef4444'
            });
        });
    </script>
<?php unset($_SESSION['error_message']); endif; ?>

      <!-- Management Header -->
      <div class="management-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
          <div class="filter-group" style="display: flex; gap: 12px; align-items: center;">
              <span style="font-size: 14px; font-weight: 600; color: var(--text-secondary);">Filter by Dept:</span>
              <select id="deptFilter" onchange="filterByDept(this.value)" style="padding: 8px 16px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--surface); color: var(--text-primary); outline: none;">
                  <option value="all" <?php echo $filterDept === 'all' ? 'selected' : ''; ?>>All Departments</option>
                  <?php foreach ($departments as $dept): ?>
                      <option value="<?php echo $dept['DepartmentID']; ?>" <?php echo (int)$filterDept === (int)$dept['DepartmentID'] ? 'selected' : ''; ?>>
                          <?php echo htmlspecialchars($dept['DepartmentName']); ?>
                      </option>
                  <?php endforeach; ?>
              </select>
          </div>
          <button class="btn-add" onclick="launchAddPositionModal()" style="display: flex; align-items: center; gap: 8px; padding: 10px 20px; background: linear-gradient(135deg, #2ca078, #4fb97a); color: white; border: none; border-radius: 12px; font-weight: 600; cursor: pointer; transition: var(--transition); box-shadow: 0 4px 15px rgba(44, 160, 120, 0.2);">
              <i data-lucide="plus-circle" style="width: 18px; height: 18px;"></i>
              <span>Add New Position</span>
          </button>
      </div>

      <!-- Positions Accordions -->
      <div class="accordions-container">
          <?php if (empty($groupedPositions)): ?>
              <div class="content-card">
                  <div class="card-body" style="padding: 40px; text-align: center; color: var(--text-secondary);">
                      No positions found for the selected criteria.
                  </div>
              </div>
          <?php else: foreach ($groupedPositions as $deptName => $data): 
              $hasVacancies = $data['TotalVacancies'] > 0;
          ?>
              <div class="dept-accordion <?php echo $hasVacancies ? 'has-vacancies' : ''; ?>" id="accordion-<?php echo str_replace(' ', '-', $deptName); ?>">
                  <div class="accordion-header" onclick="toggleAccordion('<?php echo str_replace(' ', '-', $deptName); ?>')">
                      <div class="header-left">
                          <div class="dept-icon">
                              <?php 
                                  // Simple icon logic based on name
                                  $icon = 'building-2';
                                  if (stripos($deptName, 'HR') !== false) $icon = 'users';
                                  if (stripos($deptName, 'Finance') !== false) $icon = 'banknote';
                                  if (stripos($deptName, 'Adm') !== false) $icon = 'briefcase';
                                  if (stripos($deptName, 'Log') !== false) $icon = 'truck';
                                  if (stripos($deptName, 'Core') !== false) $icon = 'activity';
                              ?>
                              <i data-lucide="<?php echo $icon; ?>"></i>
                          </div>
                          <div class="dept-info">
                              <h3><?php echo htmlspecialchars($deptName); ?></h3>
                              <span class="dept-stats">
                                  <?php echo count($data['Positions']); ?> Positions • 
                                  <span class="<?php echo $hasVacancies ? 'text-danger' : 'text-success'; ?>">
                                      <?php echo $data['TotalVacancies']; ?> Vacancies
                                  </span>
                              </span>
                          </div>
                      </div>
                      <div class="header-right">
                          <?php if ($hasVacancies): ?>
                              <?php if ($data['TotalVacancies'] === $data['TotalRequisitions']): ?>
                                  <span class="badge-alert" style="background: rgba(59, 130, 246, 0.1); color: #1d4ed8;">IN RECRUITMENT</span>
                              <?php else: ?>
                                  <span class="badge-alert">VACANT</span>
                              <?php endif; ?>
                          <?php endif; ?>
                          <i data-lucide="chevron-down" class="accordion-chevron"></i>
                      </div>
                  </div>
                  <div class="accordion-content">
                      <table class="role-table">
                          <thead>
                              <tr>
                                  <th>Position Name</th>
                                  <th>Salary Grade</th>
                                  <th>Headcount Status</th>
                                  <th style="text-align: center;">Actions</th>
                              </tr>
                          </thead>
                          <tbody>
                              <?php foreach ($data['Positions'] as $pos): 
                                  $current = (int)$pos['CurrentHeadcount'];
                                  $auth = (int)$pos['AuthorizedHeadcount'];
                                  $pct = ($auth > 0) ? ($current / $auth) * 100 : 0;
                                  $isVacancy = $current < $auth;
                                  
                                  $barColor = '#3b82f6'; // Blue for partial
                                  if ($pct >= 100) $barColor = '#10b981'; // Green for full
                                  if ($current === 0) $barColor = '#94a3b8'; // Grey for empty
                              ?>
                                  <tr class="role-row-item">
                                      <td>
                                          <div class="pos-name-wrapper">
                                              <span class="pos-name"><?php echo htmlspecialchars($pos['PositionName']); ?></span>
                                              <span class="pos-code"><?php echo htmlspecialchars($pos['PositionCode'] ?? 'POS-' . $pos['PositionID']); ?></span>
                                          </div>
                                      </td>
                                      <td><?php echo htmlspecialchars($pos['GradeName']); ?></td>
                                      <td>
                                          <div class="headcount-progress-wrapper">
                                              <div class="progress-info">
                                                  <span class="counts <?php echo $isVacancy ? 'text-danger' : 'text-success'; ?>">
                                                      <?php echo $current; ?> / <?php echo $auth; ?>
                                                  </span>
                                                  <span class="percentage"><?php echo round($pct); ?>%</span>
                                              </div>
                                              <div class="progress-bar-bg">
                                                  <div class="progress-bar-fill" style="width: <?php echo $pct; ?>%; background-color: <?php echo $barColor; ?>;"></div>
                                              </div>
                                          </div>
                                      </td>
                                      <td style="text-align: center;">
                                          <div style="display: flex; gap: 8px; justify-content: center; align-items: center;">
                                              <?php if ($isVacancy): ?>
                                                  <?php if ($pos['HasRequisition'] > 0): ?>
                                                       <div style="display: flex; gap: 8px; align-items: center;">
                                                           <button class="btn-recruit" style="background: rgba(59, 130, 246, 0.1); color: #1d4ed8; border: 1px solid rgba(59, 130, 246, 0.2);" onclick="event.stopPropagation(); window.location.href='recruitment.php'">
                                                               <span class="default-text" style="color: #1d4ed8;">IN RECRUITMENT</span>
                                                               <span class="hover-text">View in Recruitment</span>
                                                           </button>
                                                           <button class="btn-cancel-recruit" onclick="event.stopPropagation(); cancelRequisition(<?php echo $pos['PositionID']; ?>, '<?php echo addslashes($pos['PositionName']); ?>')" title="Cancel Recruitment" style="padding: 8px; background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); border-radius: 8px; cursor: pointer; transition: var(--transition);">
                                                               <i data-lucide="x-circle" style="width: 16px; height: 16px;"></i>
                                                           </button>
                                                       </div>
                                                  <?php else: ?>
                                                      <button class="btn-recruit" onclick="event.stopPropagation(); sendRequisition(<?php echo $pos['PositionID']; ?>, '<?php echo addslashes($pos['PositionName']); ?>')">
                                                          <span class="default-text">VACANT</span>
                                                          <span class="hover-text">Send Requisition</span>
                                                      </button>
                                                  <?php endif; ?>
                                              <?php else: ?>
                                                  <div class="status-filled">
                                                      <span class="dot"></span>
                                                      <span>Fully Staffed</span>
                                                  </div>
                                              <?php endif; ?>
                                              
                                              <button class="btn-manage-pos" onclick="event.stopPropagation(); launchManagePositionModal({
                                                  id: <?php echo $pos['PositionID']; ?>,
                                                  name: '<?php echo addslashes($pos['PositionName']); ?>',
                                                  code: '<?php echo addslashes($pos['PositionCode'] ?? ''); ?>',
                                                  deptId: <?php echo $pos['DepartmentID']; ?>,
                                                  gradeId: <?php echo $pos['SalaryGradeID']; ?>,
                                                  auth: <?php echo $pos['AuthorizedHeadcount']; ?>
                                              })" title="Manage Position">
                                                  <i data-lucide="settings"></i>
                                              </button>
                                              <button class="btn-delete-pos" onclick="event.stopPropagation(); confirmDeletePosition(<?php echo $pos['PositionID']; ?>, '<?php echo addslashes($pos['PositionName']); ?>')" title="Delete Position" style="padding: 8px; background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); border-radius: 8px; cursor: pointer; transition: var(--transition);">
                                                  <i data-lucide="trash-2" style="width: 16px; height: 16px;"></i>
                                              </button>
                                          </div>
                                      </td>
                                  </tr>
                              <?php endforeach; ?>
                          </tbody>
                      </table>
                  </div>
              </div>
          <?php endforeach; endif; ?>
      </div>

      <!-- Hidden Form for Add Position -->
      <form id="addPositionForm" method="POST" style="display: none;">
          <input type="hidden" name="action" value="add_position">
          <input type="hidden" name="position_name" id="formPosName">
          <input type="hidden" name="position_code" id="formPosCode">
          <input type="hidden" name="department_id" id="formDeptId">
          <input type="hidden" name="grade_id" id="formGradeId">
          <input type="hidden" name="authorized_headcount" id="formAuthHeadcount">
      </form>

      <!-- Hidden Form for Update Position -->
      <form id="updatePositionForm" method="POST" style="display: none;">
          <input type="hidden" name="action" value="update_position">
          <input type="hidden" name="position_id" id="updatePosId">
          <input type="hidden" name="position_name" id="updatePosName">
          <input type="hidden" name="position_code" id="updatePosCode">
          <input type="hidden" name="department_id" id="updateDeptId">
          <input type="hidden" name="grade_id" id="updateGradeId">
          <input type="hidden" name="authorized_headcount" id="updateAuthHeadcount">
      </form>

    <form id="deletePositionForm" method="POST" style="display: none;">
        <input type="hidden" name="action" value="delete_position">
        <input type="hidden" name="position_id" id="deletePosId">
    </form>

    <form id="requisitionForm" method="POST" style="display: none;">
        <input type="hidden" name="action" value="send_requisition">
        <input type="hidden" name="position_id" id="reqPosId">
    </form>

    <form id="cancelRequisitionForm" method="POST" style="display: none;">
        <input type="hidden" name="action" value="cancel_requisition">
        <input type="hidden" name="position_id" id="cancelReqPosId">
    </form>

      <!-- Data for JS -->
      <script id="dept-data" type="application/json"><?php echo json_encode($departments); ?></script>
      <script id="grade-data" type="application/json"><?php echo json_encode($salaryGrades); ?></script>

      <style>
          .badge-status.vacancy {
              background: rgba(239, 68, 68, 0.1);
              color: #ef4444;
          }
          .badge-status.filled {
              background: rgba(44, 160, 120, 0.1);
              color: var(--brand-green);
          }
          .btn-add:hover {
              transform: translateY(-2px);
              box-shadow: 0 6px 20px rgba(44, 160, 120, 0.3);
          }
          .role-table th {
              background: var(--background);
          }
      </style>
    </div>
  </main>
  <script src="../../js/chcpositioncatalog.js"></script>
  <script>
    lucide.createIcons();
  </script>
</body>
</html>







