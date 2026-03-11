<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Verify Simulation</title>
  <link rel="stylesheet" href="../../css/verifysimulation.css?v=1.3">
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
        
        <a href="dashboard.php" class="nav-item">
          <i data-lucide="layout-dashboard"></i>
          <span>Dashboard</span>
        </a>
         <a href="pending.php" class="nav-item">
          <i data-lucide="circle-ellipsis"></i>
          <span>Pending Reviews</span>
        </a>
         <a href="simulatiomverify.php" class="nav-item active">
          <i data-lucide="calculator"></i>
          <span>Verify Simulation</span>
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
            <a href="" class="submenu-item">
              <i data-lucide="user-plus"></i>
              <span>New Hired Onboard Request</span>
            </a>
            <a href="employeemaster.php" class="submenu-item <?php echo ($page === 'employeemaster') ? 'active' : ''; ?>">
              <i data-lucide="file-user"></i>
              <span>Employee Master Files</span>
            </a>
            <a href="bankform.php" class="submenu-item <?php echo ($page === 'bankform') ? 'active' : ''; ?>">
              <i data-lucide="file-text"></i>
              <span>Bank Form Management</span>
            </a>
            <a href="" class="submenu-item">
              <i data-lucide="user-cog"></i>
              <span>Security Settings</span>
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
            <a href="salarymgt.php" class="submenu-item <?php echo ($page === 'salarymgt') ? 'active' : ''; ?>">
              <i data-lucide="banknote"></i>
              <span>Salary & Scales Management</span>
            </a>
            <a href="cycle.php" class="submenu-item <?php echo ($page === 'cycle') ? 'active' : ''; ?>">
              <i data-lucide="notebook-pen"></i>
              <span>Compensation Structure Management</span>
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
          <h1>Simulation Verification</h1>
          <p>Verify submitted compensation cycles for approval.</p>
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
        <div class="header-notifications" style="position: relative;">
          <button class="icon-btn" id="notifBtn">
            <i data-lucide="bell"></i>
            <span class="badge hidden" id="notifBadge">0</span>
          </button>
          <div id="notifDropdown" class="notif-dropdown hidden">
              <div class="notif-header">
                  <h3>Notifications</h3>
                  <button id="markReadAll" class="btn-text">Mark all as read</button>
              </div>
              <div id="notifList" class="notif-list">
                  <div class="notif-empty">No new notifications</div>
              </div>
          </div>
        </div>
      </div>
    </header>

    <div class="content-wrapper">
        <!-- Stats Grid -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 24px;">
            <div class="stat-card" style="background: var(--surface); padding: 20px; border-radius: 12px; display: flex; align-items: center; gap: 16px; border: 1px solid var(--border-color);">
                <div class="stat-icon" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6; width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                    <i data-lucide="clipboard-list"></i>
                </div>
                <div class="stat-content">
                    <span class="stat-label" style="display: block; font-size: 13px; color: var(--text-secondary); margin-bottom: 4px;">For Verification</span>
                    <span class="stat-value" id="pendingVerifyCount" style="display: block; font-size: 24px; font-weight: 700; color: var(--text-primary);">0</span>
                </div>
            </div>
        </div>

        <!-- Simulations Table -->
        <div class="content-card" style="background: var(--surface); border-radius: 12px; border: 1px solid var(--border-color); overflow: hidden;">
            <div class="card-header" style="padding: 20px; border-bottom: 1px solid var(--border-color);">
                <h3 class="card-title" style="font-size: 18px; font-weight: 700; color: var(--text-primary);">Simulation Verification</h3>
                <p class="card-subtitle" style="font-size: 13px; color: var(--text-secondary);">Verify submitted compensation simulations before endorsement.</p>
            </div>
            <div class="card-body" style="padding: 20px;">
                <div class="data-table" style="overflow-x: auto;">
                    <table class="role-table" style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="text-align: left; border-bottom: 1px solid var(--border-color);">
                                <th style="padding: 12px; font-size: 13px; font-weight: 600; color: var(--text-secondary);">Cycle Name</th>
                                <th style="padding: 12px; font-size: 13px; font-weight: 600; color: var(--text-secondary);">Total Cost</th>
                                <th style="padding: 12px; font-size: 13px; font-weight: 600; color: var(--text-secondary);">Submitted By</th>
                                <th style="padding: 12px; font-size: 13px; font-weight: 600; color: var(--text-secondary);">Date</th>
                                <th style="padding: 12px; font-size: 13px; font-weight: 600; color: var(--text-secondary);">Action</th>
                            </tr>
                        </thead>
                        <tbody id="verifyTableBody">
                            <tr><td colspan="5" style="text-align:center; padding:40px; color:var(--text-secondary);">Loading simulations...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Full Screen Review Modal -->
    <div id="reviewModal" class="modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1050; align-items:center; justify-content:center; padding:20px;">
        <div class="modal-content" style="background: var(--surface); width: 100%; max-width: 1400px; max-height: 90vh; border-radius: 16px; display: flex; flex-direction: column; overflow: hidden; border: 1px solid var(--border-color); box-shadow: var(--shadow-lg);">
            <div class="modal-header" style="padding:20px; border-bottom:1px solid var(--border-color); display:flex; justify-content:space-between; align-items:center;">
                <div>
                    <h3 id="modalTitle" style="font-size:18px; font-weight:700; color:var(--text-primary); margin:0;">Verify Compensation Simulation</h3>
                    <p id="modalSubtitle" style="font-size:13px; color:var(--text-secondary); margin:4px 0 0 0;">Review full calculation details below.</p>
                </div>
                <button id="closeModal" style="background:none; border:none; color:var(--text-secondary); cursor:pointer; position:relative; z-index:1051; padding:10px; border-radius:8px; display:flex; align-items:center; justify-content:center; transition: background 0.2s;" onmouseover="this.style.background='rgba(0,0,0,0.05)'" onmouseout="this.style.background='none'"><i data-lucide="x"></i></button>
            </div>
            
            <div class="modal-body" style="padding:0; flex-grow:1; overflow:auto;">
                <div id="modalTableContainer" style="padding:20px;">
                    <!-- Full calculation table injected here -->
                </div>
            </div>

            <div class="modal-footer" style="padding:20px; border-top:1px solid var(--border-color); display:flex; gap:12px; justify-content:flex-end; background:var(--background-alt);">
                <div id="rejectionReason" style="display:none; flex-grow:1; margin-right:20px;">
                    <input type="text" id="rejectReasonInput" placeholder="Enter reason for rejection..." style="width:100%; padding:8px 12px; border:1px solid var(--brand-red); border-radius:8px; font-size:14px; background:var(--surface);">
                </div>
                <button class="action-btn btn-secondary" id="cancelReview" style="padding:8px 20px; border-radius:8px; border:1px solid var(--border-color); background:var(--surface); color:var(--text-secondary); cursor:pointer; font-weight:600;">Cancel</button>
                <button class="action-btn btn-reject" id="rejectBtn" style="padding:8px 20px; border-radius:8px; border:1px solid var(--brand-red); background:rgba(239,68,68,0.1); color:var(--brand-red); cursor:pointer; font-weight:600; display:flex; align-items:center; gap:8px;">
                    <i data-lucide="x-circle" style="width:16px;height:16px;"></i> Reject
                </button>
                <button class="action-btn btn-approve" id="approveBtn" style="padding:8px 20px; border-radius:8px; border:1px solid var(--brand-green); background:var(--brand-green); color:white; cursor:pointer; font-weight:600; display:flex; align-items:center; gap:8px;">
                    <i data-lucide="check-circle" style="width:16px;height:16px;"></i> Approve & Forward
                </button>
            </div>
        </div>
    </div>    
  </main>
  <script src="../../js/verifysimulation.js?v=2.2"></script>
  <script>
    lucide.createIcons();
    // Initialize notifications with verify target
    if (typeof initGlobalNotifications === 'function') {
        initGlobalNotifications('compensation_verify');
    }
  </script>
</body>
</html>







