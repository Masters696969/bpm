<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../../login.php");
    exit();
}
require_once('../../config/config.php');

// Handle Add Department POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_department') {
    $deptName = trim($_POST['department_name']);
    if (!empty($deptName)) {
        $stmt = $conn->prepare("INSERT INTO department (DepartmentName) VALUES (?)");
        $stmt->bind_param("s", $deptName);
        if ($stmt->execute()) {
            $_SESSION['msg'] = "Department added successfully!";
            $_SESSION['msg_type'] = "success";
        } else {
            $_SESSION['msg'] = "Error adding department.";
            $_SESSION['msg_type'] = "error";
        }
    }
    header("Location: orgprofile.php");
    exit();
}

// Handle delete department POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_department') {
    $deptId = intval($_POST['department_id']);
    // Only allow deleting non-fixed departments for safety? 
    // Or allow all? The user said "so you can delete departments".
    // I'll allow deleting if it's not one of the main ones (1,2,3,4,5) to protect the tree.
    $protectedIds = [1, 2, 3, 4, 5];
    if (!in_array($deptId, $protectedIds)) {
        $stmt = $conn->prepare("DELETE FROM department WHERE DepartmentID = ?");
        $stmt->bind_param("i", $deptId);
        if ($stmt->execute()) {
            $_SESSION['msg'] = "Department removed successfully!";
            $_SESSION['msg_type'] = "success";
        } else {
            $_SESSION['msg'] = "Error removing department.";
            $_SESSION['msg_type'] = "error";
        }
    } else {
        $_SESSION['msg'] = "Core departments cannot be removed.";
        $_SESSION['msg_type'] = "warning";
    }
    header("Location: orgprofile.php");
    exit();
}

// Define the hierarchy roles for styling
$hierarchy = [
    'top' => [1], // Administration
    'middle' => [4, 2, 3], // Logistics, HR, Finance
    'bottom' => [5] // Core Transaction
];

// Combine all IDs in the fixed hierarchy for later filtering
$fixedHierarchyIds = array_merge($hierarchy['top'], $hierarchy['middle'], $hierarchy['bottom']);

// Fetch all departments and their positions with headcount
$query = "SELECT d.DepartmentID, d.DepartmentName, p.PositionName, p.AuthorizedHeadcount,
          (SELECT COUNT(*) FROM employmentinformation ei WHERE ei.PositionID = p.PositionID) as CurrentHeadcount
          FROM department d 
          LEFT JOIN positions p ON d.DepartmentID = p.DepartmentID 
          ORDER BY d.DepartmentID";
$result = $conn->query($query);

$departments = [];
while ($row = $result->fetch_assoc()) {
    $deptId = $row['DepartmentID'];
    if (!isset($departments[$deptId])) {
        // Map icon based on deptId
        $icon = 'building-2';
        if ($deptId == 1) $icon = 'crown';
        if ($deptId == 2) $icon = 'users';
        if ($deptId == 3) $icon = 'banknote';
        if ($deptId == 4) $icon = 'truck';
        if ($deptId == 5) $icon = 'activity';

        $departments[$deptId] = [
            'name' => $row['DepartmentName'],
            'icon' => $icon,
            'positions' => []
        ];
    }
    if ($row['PositionName']) {
        $departments[$deptId]['positions'][] = [
            'name' => $row['PositionName'],
            'authorized' => (int)$row['AuthorizedHeadcount'],
            'current' => (int)$row['CurrentHeadcount']
        ];
    }
}

// Identify "Other" departments not in the fixed hierarchy
$otherDeptIds = array_diff(array_keys($departments), $fixedHierarchyIds);

// Function to render a node
function renderNode($deptId, $departments) {
    if (!isset($departments[$deptId])) return '';
    
    $dept = $departments[$deptId];
    $posNames = array_column($dept['positions'], 'name');
    $posList = implode(', ', array_slice($posNames, 0, 3));
    if (count($posNames) > 3) $posList .= '...';
    
    $icon = $dept['icon'];

    // Prepare JSON for JS modal
    $deptJson = json_encode($dept);

    $html = '<div class="tree-node" data-dept-id="' . $deptId . '" onclick=\'showDeptDetails(' . htmlspecialchars($deptJson, ENT_QUOTES, 'UTF-8') . ')\'>';
    $html .= '  <div class="node-card">';
    $html .= '    <div class="node-icon"><i data-lucide="' . $icon . '"></i></div>';
    $html .= '    <div class="node-info">';
    $html .= '      <h3 class="node-title">' . htmlspecialchars($dept['name']) . '</h3>';
    $html .= '      <p class="node-subtitle">' . htmlspecialchars($posList) . '</p>';
    $html .= '    </div>';
    $html .= '  </div>';
    $html .= '</div>';
    return $html;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Organizational Profile | Microfinance</title>
  <link rel="stylesheet" href="../../css/chcorgprofile.css?v=1.4">
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
          <h1>Organizational Profile</h1>
          <p>Visualizing the structure of Microfinance Inc.</p>
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
      <div class="content-actions">
        <button class="btn-mgmt btn-add" id="addDeptBtn">
          <div class="btn-icon"><i data-lucide="plus"></i></div>
          <span>Add Department</span>
        </button>
        <button class="btn-mgmt btn-delete" id="deleteDeptBtn">
          <div class="btn-icon"><i data-lucide="trash-2"></i></div>
          <span>Remove Department</span>
        </button>
      </div>
      <div class="org-tree-container">
        <!-- Level 1: Top -->
        <div class="tree-level top-level">
          <?php echo renderNode(1, $departments); ?>
        </div>

        <div class="tree-connector vertical"></div>

        <!-- Level 2: Middle -->
        <div class="tree-level middle-level">
          <div class="middle-nodes-container">
            <?php 
              foreach ($hierarchy['middle'] as $id) {
                echo renderNode($id, $departments);
              }
            ?>
          </div>
        </div>

        <div class="tree-connector vertical"></div>

        <!-- Level 3: Bottom -->
        <div class="tree-level bottom-level">
          <?php echo renderNode(5, $departments); ?>
        </div>

        <?php if (!empty($otherDeptIds)): ?>
          <div class="tree-connector vertical"></div>
          <!-- Level 4: Others -->
          <div class="tree-level other-level">
            <h4 class="level-indicator">Additional Departments</h4>
            <div class="middle-nodes-container other-nodes">
              <?php 
                foreach ($otherDeptIds as $id) {
                  echo renderNode($id, $departments);
                }
              ?>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </main>
  
  <!-- Hidden Forms for submission -->
  <form id="addDeptForm" method="POST" style="display: none;">
      <input type="hidden" name="action" value="add_department">
      <input type="hidden" name="department_name" id="deptInput">
  </form>
  
  <form id="deleteDeptForm" method="POST" style="display: none;">
      <input type="hidden" name="action" value="delete_department">
      <input type="hidden" name="department_id" id="deleteDeptIdInput">
  </form>

  <script>
    // Pass non-fixed departments to JS for deletion list
    const deletableDepts = <?php 
        $deletable = [];
        foreach ($otherDeptIds as $id) {
            $deletable[] = ['id' => $id, 'name' => $departments[$id]['name']];
        }
        echo json_encode($deletable);
    ?>;
  </script>

  <script src="../../js/chcorgprofile.js"></script>
  <script>
    lucide.createIcons();
    <?php if (isset($_SESSION['msg'])): ?>
        Swal.fire({
            icon: '<?php echo $_SESSION['msg_type']; ?>',
            title: '<?php echo $_SESSION['msg']; ?>',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });
        <?php unset($_SESSION['msg'], $_SESSION['msg_type']); ?>
    <?php endif; ?>
  </script>
</body>
</html>






