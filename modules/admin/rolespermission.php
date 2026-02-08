<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['user_role']) !== 'administrator') {
    header('Location: ../../login.php');
    exit;
}

require_once '../../config/config.php';

// Fetch all roles from database
$rolesSql = "SELECT * FROM roles ORDER BY RoleID ASC";
$rolesResult = $conn->query($rolesSql);
$roles = [];
if ($rolesResult) {
    while ($row = $rolesResult->fetch_assoc()) {
        $roles[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Roles & Permissions - Microfinance</title>
  <!-- Base styles from useraccount.css for layout consistency -->
  <link rel="stylesheet" href="../../css/useraccount.css?v=1.4"> 
  <!-- Specific styles for this page -->
  <link rel="stylesheet" href="../../css/rolespermission.css?v=1.0">
  <link rel="stylesheet" href="../../css/sidebar-fix.css?v=1.0">
  <script src="https://unpkg.com/lucide@0.474.0/dist/umd/lucide.js" crossorigin="anonymous"></script>
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

        <div class="nav-item-group active">
          <button class="nav-item has-submenu" data-module="hr">
            <div class="nav-item-content">
              <i data-lucide="users"></i>
              <span>Account Management</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-hr">
            <a href="useraccount.php" class="submenu-item">
              <i data-lucide="user-plus"></i>
              <span>User Accounts</span>
            </a>
            <a href="rolespermission.php" class="submenu-item active">
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
        
        <!-- Reuse other menus... -->
        <div class="nav-item-group">
          <button class="nav-item has-submenu" data-module="finance">
            <div class="nav-item-content">
              <i data-lucide="banknote"></i>
              <span>Finance</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-finance">
            <a href="#" class="submenu-item"><i data-lucide="receipt"></i><span>Accounting</span></a>
            <a href="#" class="submenu-item"><i data-lucide="file-text"></i><span>Invoicing</span></a>
            <a href="#" class="submenu-item"><i data-lucide="pie-chart"></i><span>Budget Planning</span></a>
          </div>
        </div>

        <div class="nav-item-group">
            <button class="nav-item has-submenu" data-module="loans">
            <div class="nav-item-content">
                <i data-lucide="hand-coins"></i>
                <span>Loan Management</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
            </button>
            <div class="submenu" id="submenu-loans">
            <a href="#" class="submenu-item"><i data-lucide="file-plus"></i><span>Applications</span></a>
            <a href="#" class="submenu-item"><i data-lucide="check-circle"></i><span>Approvals</span></a>
            <a href="#" class="submenu-item"><i data-lucide="calendar-clock"></i><span>Disbursements</span></a>
            <a href="#" class="submenu-item"><i data-lucide="coins"></i><span>Collections</span></a>
            </div>
        </div>

        <a href="#" class="nav-item"><i data-lucide="users-round"></i><span>Clients</span></a>
        <a href="#" class="nav-item"><i data-lucide="file-bar-chart"></i><span>Reports</span></a>
      </div>

      <div class="nav-section">
        <span class="nav-section-title">SETTINGS</span>
        <a href="#" class="nav-item"><i data-lucide="settings"></i><span>Configuration</span></a>
        <a href="#" class="nav-item"><i data-lucide="shield"></i><span>Security</span></a>
        <a href="../../logout.php" class="nav-item" onclick="return confirm ('Are you sure you want to log out?')">
            <i data-lucide="log-out"></i><span>Logout</span>
        </a>
      </div>
    </nav>

    <div class="sidebar-footer">
      <div class="user-profile">
        <div class="user-avatar">
          <img src="../../img/profile.png" alt="User">
        </div>
        <div class="user-info">
          <span class="user-name"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
          <span class="user-role"><?php echo htmlspecialchars($_SESSION['user_role']); ?></span>
        </div>
        <button class="user-menu-btn">
          <i data-lucide="more-vertical"></i>
        </button>
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
          <h1>Roles & Permissions</h1>
          <p>Manage user roles and access rights.</p>
        </div>
      </div>
      <div class="header-right">
        <div class="search-box">
          <i data-lucide="search"></i>
          <input type="search" placeholder="Search roles...">
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
      <section class="users-panel">
          <div class="panel-header">
          <h2>Defined Roles</h2>
          <div class="panel-actions">
            <button id="addRoleBtn" class="btn btn-primary">+ Add Role</button>
          </div>
        </div>

        <div class="panel-body">
          <div class="table-responsive">
            <table id="rolesTable" class="users-table">
              <thead>
                <tr>
                  <th>Role Name</th>
                  <th>Description</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($roles)): ?>
                <tr><td colspan="3" style="text-align:center;">No roles found.</td></tr>
                <?php else: ?>
                    <?php foreach ($roles as $role): ?>
                    <tr>
                    <td><?php echo htmlspecialchars($role['RoleName']); ?></td>
                    <td><?php echo htmlspecialchars($role['Description'] ?? 'No description'); ?></td>
                    <td>
                        <div class="action-buttons">
                        <button class="btn btn-sm btn-edit" data-role-id="<?php echo $role['RoleID']; ?>" onclick="editRole(<?php echo $role['RoleID']; ?>, '<?php echo htmlspecialchars($role['RoleName']); ?>')">
                            <i data-lucide="edit-2"></i>
                            Edit
                        </button>
                        <button class="btn btn-sm btn-delete" data-role-id="<?php echo $role['RoleID']; ?>" onclick="archiveRole(<?php echo $role['RoleID']; ?>)">
                            <i data-lucide="archive"></i>
                            Archive
                        </button>
                        <button class="btn-permission">
                            <i data-lucide="shield-check"></i>
                            Permission
                        </button>
                        </div>
                    </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- Add/Edit Role Modal -->
      <div id="roleModal" class="modal" aria-hidden="true">
        <div class="modal-dialog">
          <header class="modal-header">
            <h3 id="modalTitle">Add New Role</h3>
            <button class="close-modal" id="closeModalBtn">&times;</button>
          </header>
          <div class="modal-body">
            <form id="roleForm">
              <input type="hidden" id="roleId" name="role_id" value="">
              
              <div class="form-row">
                <label for="roleName">Role Name <span class="required">*</span></label>
                <input id="roleName" name="role_name" type="text" placeholder="Enter role name" required />
              </div>

               <div class="form-row">
                <label for="roleDescription">Description</label>
                <textarea id="roleDescription" name="description" rows="3" placeholder="Enter description"></textarea>
              </div>

              <div class="form-actions">
                <button type="submit" class="btn btn-primary" id="modalSubmitBtn">Save Role</button>
                <button type="button" id="cancelRole" class="btn">Cancel</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </div>
  </main>
  <script src="../../js/sidebar-active.js"></script>
  <script src="../../js/rolespermission.js?v=<?php echo time(); ?>"></script>
  <script>
    // Initialize icons safely
    if (window.lucide) {
      window.lucide.createIcons();
    }
  </script>
</body>
</html>
