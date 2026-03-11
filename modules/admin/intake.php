<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../../login.php");
    exit();
}
$module = 'shiftmanagement';
$page = 'intake';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link rel="stylesheet" href="../../css/adminintake.css?v=1.2">
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
        <div class="nav-item-group <?php echo ($module === 'accounts') ? 'active' : ''; ?>">
          <button class="nav-item has-submenu" data-module="accounts">
            <div class="nav-item-content">
              <i data-lucide="users"></i>
              <span>Account Management</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-accounts">
            <a href="useraccount.php" class="submenu-item <?php echo ($page === 'useraccount') ? 'active' : ''; ?>">
              <i data-lucide="user-plus"></i>
              <span>User Accounts</span>
            </a>
            <a href="rolespermission.php" class="submenu-item <?php echo ($page === 'rolespermission') ? 'active' : ''; ?>">
              <i data-lucide="contact-round"></i>
              <span>Roles & Permissions</span>
            </a>
            <a href="securitysetting.php" class="submenu-item <?php echo ($page === 'securitysetting') ? 'active' : ''; ?>">
              <i data-lucide="user-cog"></i>
              <span>Security Settings</span>
            </a>
            <a href="auditlogs.php" class="submenu-item <?php echo ($page === 'auditlogs') ? 'active' : ''; ?>">
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
        <div class="nav-item-group <?php echo ($module === 'payroll') ? 'active' : ''; ?>">
          <button class="nav-item has-submenu" data-module="payroll">
            <div class="nav-item-content">
              <i data-lucide="banknote"></i>
              <span>Payroll Management</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-payroll">
            <a href="comperules.php" class="submenu-item <?php echo ($page === 'comperules') ? 'active' : ''; ?>">
              <i data-lucide="boxes"></i>
              <span>Compensation Rules</span>
            </a>
            <a href="payroll.php" class="submenu-item <?php echo ($page === 'payroll') ? 'active' : ''; ?>">
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
         <div class="nav-section">        
        <div class="nav-item-group <?php echo ($module === 'shiftmanagement') ? 'active' : ''; ?>">
          <button class="nav-item has-submenu" data-module="shiftmanagement">
            <div class="nav-item-content">
              <i data-lucide="calendar-check"></i>
              <span>Shift & Scheduling</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu active" id="submenu-shiftmanagement">
            <a href="intake.php" class="submenu-item active">
              <i data-lucide="send-to-back"></i>
              <span>Master Data Intake</span>
             </a>
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
              <span class="umd-name"><?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?></span>
              <span class="umd-role"><?php echo htmlspecialchars($_SESSION['user_role'] ?? 'Administrator'); ?></span>
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
          <h1 style="font-size: 24px; font-weight: 600;">Master Data Intake</h1>
          <p style="color: var(--text-secondary); font-size: 14px;">Review and sync incoming employee master data dispatches.</p>
        </div>
      </div>
      <div class="header-right" style="display: flex; align-items: center; gap: 15px;">
        <div class="header-clock">
          <span id="realTimeClock"></span>
        </div>
        <button class="icon-btn" aria-label="Notifications">
            <i data-lucide="bell"></i>
            <span class="badge">0</span>
        </button>
        <button class="theme-toggle" id="themeToggle" aria-label="Toggle theme">
          <i data-lucide="sun" class="sun-icon"></i>
          <i data-lucide="moon" class="moon-icon"></i>
        </button>
      </div>
    </header>

    <div class="content-wrapper">
      <!-- Stats Grid -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon">
            <i data-lucide="package"></i>
          </div>
          <div class="stat-content">
            <span class="stat-label">Pending Batches</span>
            <h3 class="stat-value" id="statPendingBatches">0</h3>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">
            <i data-lucide="users"></i>
          </div>
          <div class="stat-content">
            <span class="stat-label">Total Records</span>
            <h3 class="stat-value" id="statTotalRecords">0</h3>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon" style="background: rgba(255, 193, 7, 0.1); color: var(--brand-yellow);">
            <i data-lucide="clock"></i>
          </div>
          <div class="stat-content">
            <span class="stat-label">Last Sync</span>
            <h3 class="stat-value" id="statLastSync" style="font-size: 20px;">--:--</h3>
          </div>
        </div>
      </div>

      <!-- Main Content Card -->
      <div class="content-card">
        <div class="card-header">
          <div>
            <h3 class="card-title">Intake Queue</h3>
            <p class="card-subtitle">Grouped employee records awaiting final system synchronization.</p>
          </div>
          <button class="btn-refresh" onclick="location.reload()" style="padding: 10px 18px; border-radius: 10px; display: flex; align-items: center; gap: 8px;">
            <i data-lucide="refresh-cw" style="width: 16px;"></i>
            Refresh Queue
          </button>
        </div>
        
        <div class="dispatch-table-container">
          <table class="dispatch-table" id="intakeTable">
            <thead>
              <tr>
                <th style="width: 250px;">Dispatcher Name</th>
                <th style="width: 200px;">Position</th>
                <th style="width: 150px; text-align: center;">Records</th>
                <th style="width: 180px; text-align: center;">Last Activity</th>
                <th style="width: 150px; text-align: center;">Status</th>
                <th style="width: 200px; text-align: center;">Actions</th>
              </tr>
            </thead>
            <tbody id="intakeTableBody">
                <tr id="loadingState">
                    <td colspan="6" style="text-align: center; padding: 60px;">
                        <div style="display: flex; flex-direction: column; align-items: center; gap: 12px;">
                            <i data-lucide="loader-2" class="animate-spin" style="width: 32px; color: var(--brand-green);"></i>
                            <p style="color: var(--text-secondary); font-weight: 500;">Retrieving intake queue...</p>
                        </div>
                    </td>
                </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Batch Review Modal -->
    <div id="batchReviewModal" class="modal" style="display: none; position: fixed; z-index: 2000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); align-items: center; justify-content: center;">
        <div class="modal-content" style="background: var(--surface); border-radius: 20px; width: 95%; max-width: 950px; max-height: 85vh; overflow: hidden; display: flex; flex-direction: column; position: relative; box-shadow: var(--shadow-lg); border: 1px solid var(--border-color);">
            
            <!-- Modal Header -->
            <div style="padding: 24px 32px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between; background: var(--background);">
                <div>
                    <h2 style="font-size: 20px; font-weight: 700; color: var(--text-primary); margin-bottom: 4px;">Batch Review Queue</h2>
                    <p id="batchReviewSummary" style="font-size: 13px; color: var(--text-secondary);">Review individual records within this dispatch batch.</p>
                </div>
                <button onclick="closeBatchModal()" style="width: 36px; height: 36px; border-radius: 10px; border: 1px solid var(--border-color); background: var(--surface); color: var(--text-secondary); display: flex; align-items: center; justify-content: center; cursor: pointer; transition: var(--transition);">
                    <i data-lucide="x" style="width: 20px;"></i>
                </button>
            </div>
            
            <!-- Modal Body -->
            <div style="padding: 32px; overflow-y: auto; flex: 1;">
                <div class="dispatch-table-container" style="border-radius: 12px; border: 1px solid var(--border-color); overflow: hidden;">
                    <table class="dispatch-table">
                        <thead style="background: var(--background);">
                            <tr>
                                <th style="padding: 16px;">Employee Name</th>
                                <th style="padding: 16px;">System Code</th>
                                <th style="padding: 16px;">Department</th>
                                <th style="padding: 16px;">Current Position</th>
                            </tr>
                        </thead>
                        <tbody id="modalBatchEmployeeList" style="font-size: 14px;">
                            <!-- List of employees -->
                        </tbody>
                    </table>
                </div>

                <div style="margin-top: 28px;">
                    <label style="display: block; font-size: 12px; font-weight: 600; color: var(--text-tertiary); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 10px;">Reviewer Remarks</label>
                    <textarea id="batchRemarks" style="width: 100%; padding: 16px; border-radius: 12px; border: 1px solid var(--border-color); background: var(--background); color: var(--text-primary); outline: none; resize: none; font-family: inherit; font-size: 14px; min-height: 100px; transition: var(--transition);" placeholder="Internal notes regarding this batch sync..."></textarea>
                </div>
            </div>

            <!-- Modal Footer -->
            <div style="padding: 24px 32px; border-top: 1px solid var(--border-color); display: flex; justify-content: flex-end; gap: 12px; background: var(--background);">
                <button onclick="closeBatchModal()" style="padding: 12px 24px; border-radius: 12px; border: 1px solid var(--border-color); background: var(--surface); color: var(--text-secondary); cursor: pointer; font-weight: 600; font-size: 14px; transition: var(--transition);">Cancel</button>
                <button onclick="processBatch('Rejected')" style="padding: 12px 24px; border-radius: 12px; border: none; background: #ef4444; color: white; cursor: pointer; font-weight: 600; font-size: 14px; transition: var(--transition);">Reject Batch</button>
                <button onclick="processBatch('Received')" style="padding: 12px 24px; border-radius: 12px; border: none; background: var(--brand-green); color: white; cursor: pointer; font-weight: 600; font-size: 14px; display: flex; align-items: center; gap: 8px; transition: var(--transition);">
                    <i data-lucide="refresh-cw" style="width: 16px;"></i>
                    Sync Batch
                </button>
            </div>
        </div>
    </div>
  </main>

  <script src="../../js/adminintake.js"></script>
  <script>
    if (window.lucide) window.lucide.createIcons();

    // Real-time clock
    function updateClock() {
        const now = new Date();
        const options = { 
            weekday: 'short', year: 'numeric', month: 'short', day: 'numeric',
            hour: '2-digit', minute: '2-digit', second: '2-digit'
        };
        const clockEl = document.getElementById('realTimeClock');
        if (clockEl) clockEl.textContent = now.toLocaleDateString('en-US', options);
    }
    setInterval(updateClock, 1000);
    updateClock();
  </script>
</body>
</html>







