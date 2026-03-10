<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../../login.php");
    exit();
}
$page = 'dispatch';
$module = 'corehumancapital';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Master Data Dispatch | Admin</title>
  <link rel="stylesheet" href="../../css/dispatch.css"> <!-- Consolidated styles -->
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
        <a href="dashboard.php" class="nav-item">
          <i data-lucide="layout-dashboard"></i>
          <span>HR ANALYTICS</span>
        </a>
      <div class="nav-section">
        <span class="nav-section-title">ADMINISTRATION</span>
        <div class="nav-item-group">
          <button class="nav-item has-submenu" data-module="accounts">
            <div class="nav-item-content">
              <i data-lucide="users"></i>
              <span>Account Management</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-accounts">
            <a href="useraccount.php" class="submenu-item">
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
          <div class="nav-item-group active">
          <button class="nav-item has-submenu" data-module="corehumancapital">
            <div class="nav-item-content">
              <i data-lucide="book-user"></i>
              <span>Core Human Capital</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu active" id="submenu-corehumancapital">
            <a href="dispatch.php" class="submenu-item active">
              <i data-lucide="send"></i>
              <span>Master Data Dispatch</span>
            </a>
             <a href="orgprofile.php" class="submenu-item">
              <i data-lucide="building-2"></i>
              <span>Organization Profile</span>
            </a>
            <a href="positioncatalog.php" class="submenu-item">
              <i data-lucide="user-star"></i>
              <span>Position Catalog</span>
            </a>
            <a href="employeemaster.php" class="submenu-item">
              <i data-lucide="file-user"></i>
              <span>Employee Master Files</span>
            </a>
            <a href="informationapproval.php" class="submenu-item">
              <i data-lucide="file-check"></i>
              <span>Information Approval</span>
            </a>
            <a href="bankform.php" class="submenu-item">
              <i data-lucide="file-text"></i>
              <span>Bank Form Management</span>
            </a>
          </div>
        </div>
          <div class="nav-item-group">
          <button class="nav-item has-submenu" data-module="planning">
            <div class="nav-item-content">
              <i data-lucide="circle-pile"></i>
              <span>Compensation Planning</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-planning">
            <a href="salary.php" class="submenu-item">
              <i data-lucide="banknote"></i>
              <span>Salary & Scales Management</span>
            </a>
            <a href="statutory.php" class="submenu-item">
              <i data-lucide="scale"></i>
              <span>Statutory Contributions</span>
            </a>
            <a href="matrix.php" class="submenu-item">
              <i data-lucide="scale"></i>
              <span>Merit Matrix Structure</span>
            </a>
            <a href="cycle.php" class="submenu-item">
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
            <a href="payroll.php" class="submenu-item">
              <i data-lucide="play-circle"></i>
              <span>Payroll Processing</span>
            </a>
          </div>
        </div>
            <a href="recruitment.php" class="nav-item">
              <i data-lucide="layers-plus"></i>
              <span>Recruitment</span>
            </a>
            <a href="applicationmgt.php" class="nav-item">
              <i data-lucide="contact-round"></i>
              <span>Application Management</span>
            </a>
      <a href="newhiredonboard.php" class="nav-item">
              <i data-lucide="user-plus"></i>
              <span>New Hired Onboard</span>
            </a>
        </div>

        <div class="nav-section">
        <span class="nav-section-title">FINANCE</span>
        <div class="nav-item-group">
          <button class="nav-item has-submenu" data-module="budget">
            <div class="nav-item-content">
              <i data-lucide="hand-coins"></i>
              <span>Budget Management</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-budget">
            <a href="positionrequest.php" class="submenu-item">
              <i data-lucide="badge-dollar-sign"></i>
              <span>Position Requests</span>
            </a>
            <a href="intake.php" class="submenu-item">
              <i data-lucide="send-to-back"></i>
              <span>Master Data Intake</span>
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
          <h1>Master Data Dispatch</h1>
          <p>Review and dispatch employee master data to the Intake module.</p>
        </div>
      </div>
      <div class="header-right" style="display: flex; align-items: center; gap: 15px;">
        <div class="header-clock">
          <span id="realTimeClock"></span>
        </div>
        <button class="theme-toggle" id="themeToggle" aria-label="Toggle theme">
          <i data-lucide="sun" class="sun-icon"></i>
          <i data-lucide="moon" class="moon-icon"></i>
        </button>
      </div>
    </header>

    <div class="content-wrapper">
      <div class="dispatch-container">
        <!-- Main Data Table -->
        <div class="data-section">
            <div class="section-header">
                <div>
                    <h2>Data Verification Queue</h2>
                    <p style="font-size: 13px; color: var(--text-secondary); margin: 4px 0 0 0;">
                        The table below shows employee records waiting to be dispatched.
                    </p>
                </div>
                <button class="btn-refresh" style="padding: 8px 16px; background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; border-radius: 8px; cursor: pointer; display: flex; align-items: center; gap: 8px;" onclick="location.reload()">
                    <i data-lucide="rotate-cw" style="width: 14px;"></i>
                    Refresh
                </button>
            </div>

            <div class="dispatch-table-container">
                <table class="dispatch-table">
                    <thead>
                        <tr>
                            <th>Dispatcher Name</th>
                            <th>Position</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="dispatchTableBody">
                        <!-- Will be populated with 1 row for the dispatcher -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </div>

    <!-- View Employee Modal -->
    <div id="viewEmployeeModal" class="modal" style="display: none; position: fixed; z-index: 2000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center;">
        <div class="modal-content" style="background: var(--surface); padding: 32px; border-radius: 16px; width: 90%; max-width: 900px; max-height: 90vh; overflow-y: auto; position: relative; box-shadow: var(--shadow-lg);">
            <button onclick="closeViewModal()" style="position: absolute; right: 24px; top: 24px; background: none; border: none; font-size: 24px; cursor: pointer; color: var(--text-tertiary);">&times;</button>
            
            <div style="margin-bottom: 24px;">
                <h2 style="font-size: 22px; font-weight: 700; color: var(--text-primary);">Employees Pending Dispatch</h2>
                <p style="font-size: 14px; color: var(--text-secondary);">The following records will be sent to the Intake module.</p>
            </div>

            <div class="dispatch-table-container">
                <table class="dispatch-table">
                    <thead>
                        <tr>
                            <th>Employee Name</th>
                            <th>Employee Code</th>
                            <th>Department</th>
                            <th>Position</th>
                        </tr>
                    </thead>
                    <tbody id="modalEmployeeList">
                        <!-- List of employees -->
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 32px; padding-top: 24px; border-top: 1px solid var(--border-color); display: flex; justify-content: flex-end; gap: 12px;">
                <button class="btn-refresh" onclick="closeViewModal()" style="padding: 10px 24px; border-radius: 10px; border: 1px solid var(--border-color); background: transparent; cursor: pointer; font-weight: 600; color: var(--text-secondary);">Close</button>
                <button class="btn-dispatch-single" id="modalDispatchBtn" style="background: var(--brand-green); color: white; border: none;" onclick="dispatchAll()">
                    <i data-lucide="send" style="width: 14px;"></i>
                    Dispatch All
                </button>
            </div>
        </div>
    </div>
  </main>
  <script src="../../js/dispatch.js"></script>
  <script>
    if (window.lucide) window.lucide.createIcons();
    
    // Real-time clock
    function updateClock() {
        const now = new Date();
        const options = { 
            weekday: 'short', year: 'numeric', month: 'short', day: 'numeric',
            hour: '2-digit', minute: '2-digit', second: '2-digit'
        };
        document.getElementById('realTimeClock').textContent = now.toLocaleDateString('en-US', options);
    }
    setInterval(updateClock, 1000);
    updateClock();
  </script>
</body>
</html>
