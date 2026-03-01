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
  <title>Dashboard</title>
  <link rel="stylesheet" href="../../css/payroll.css?v=1.3">
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

        <div class="nav-item-group">
          <button class="nav-item has-submenu" data-module="payroll">
            <div class="nav-item-content">
              <i data-lucide="banknote"></i>
              <span>Payroll Management</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-payroll">
            <a href="payroll.php" class="submenu-item">
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

        <div class="nav-item-group">
          <button class="nav-item has-submenu" data-module="hr">
            <div class="nav-item-content">
              <i data-lucide="users"></i>
              <span>Core Human Capital</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-hr">
            <a href="../corehumancapital/employeemaster.php" class="submenu-item">
              <i data-lucide="file-user"></i>
              <span>Employee Master</span>
            </a>
            <a href="../corehumancapital/bankform.php" class="submenu-item">
              <i data-lucide="landmark"></i>
              <span>Bank Forms</span>
            </a>
          </div>
        </div>

        <div class="nav-item-group">
          <button class="nav-item has-submenu" data-module="compensation">
            <div class="nav-item-content">
              <i data-lucide="pie-chart"></i>
              <span>Compensation</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-compensation">
            <a href="../compensation/dashboard.php" class="submenu-item">
              <i data-lucide="layout-dashboard"></i>
              <span>Comp Dashboard</span>
            </a>
            <a href="../compensation/cycle.php" class="submenu-item">
              <i data-lucide="refresh-cw"></i>
              <span>Comp Cycles</span>
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
          <h1>Dashboard Overview</h1>
          <p>Welcome back, <?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?>! Here's what's happening today.</p>
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
      <!-- Stats Grid -->
      <div class="stats-grid" style="margin-bottom: 32px;">
        <div class="stat-card-premium">
          <div class="stat-icon-wrapper" style="background: rgba(44, 160, 120, 0.1); color: var(--brand-green);">
            <i data-lucide="banknote"></i>
          </div>
          <div class="stat-info">
            <span class="stat-label">Monthly Disbursement</span>
            <h3 class="stat-value">&#8369;2,497,000</h3>
          </div>
        </div>

        <div class="stat-card-premium">
          <div class="stat-icon-wrapper" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">
            <i data-lucide="users-round"></i>
          </div>
          <div class="stat-info">
            <span class="stat-label">Active Employees</span>
            <h3 class="stat-value">124</h3>
          </div>
        </div>

        <div class="stat-card-premium">
          <div class="stat-icon-wrapper" style="background: rgba(255, 193, 7, 0.1); color: var(--brand-yellow);">
            <i data-lucide="calendar-check-2"></i>
          </div>
          <div class="stat-info">
            <span class="stat-label">Attendance Verified</span>
            <h3 class="stat-value">98.2%</h3>
          </div>
        </div>

        <div class="stat-card-premium">
          <div class="stat-icon-wrapper" style="background: rgba(139, 92, 246, 0.1); color: #8b5cf6;">
            <i data-lucide="file-text"></i>
          </div>
          <div class="stat-info">
            <span class="stat-label">Pending Approval</span>
            <h3 class="stat-value">12</h3>
          </div>
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
        <!-- Left Side: Active Processing -->
        <div class="content-card" style="padding: 24px; background: var(--surface); border: 1px solid var(--border-color); border-radius: 20px;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <h3 style="font-size: 18px; font-weight: 600;">Current Processing Status</h3>
            <span class="badge-premium badge-warning">Active Batch</span>
          </div>
          
          <div style="margin-bottom: 24px;">
             <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                <span style="font-size: 14px; color: var(--text-secondary);">Batch: PR-2026-005 (Mar 1-15)</span>
                <span style="font-size: 14px; font-weight: 600; color: var(--brand-green);">65% Complete</span>
             </div>
             <div style="height: 8px; background: var(--background); border-radius: 10px; overflow: hidden;">
                <div style="width: 65%; height: 100%; background: var(--brand-green); border-radius: 10px;"></div>
             </div>
          </div>

          <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px;">
             <div style="padding: 16px; background: var(--background); border-radius: 12px; text-align: center;">
                <i data-lucide="user-check" style="color: var(--brand-green); margin-bottom: 8px;"></i>
                <div style="font-size: 11px; color: var(--text-tertiary);">VERIFIED</div>
                <div style="font-size: 16px; font-weight: 600;">82 / 124</div>
             </div>
             <div style="padding: 16px; background: var(--background); border-radius: 12px; text-align: center;">
                <i data-lucide="alert-triangle" style="color: var(--brand-yellow); margin-bottom: 8px;"></i>
                <div style="font-size: 11px; color: var(--text-tertiary);">ISSUES</div>
                <div style="font-size: 16px; font-weight: 600;">3 Flagged</div>
             </div>
             <div style="padding: 16px; background: var(--background); border-radius: 12px; text-align: center;">
                <i data-lucide="clock" style="color: #3b82f6; margin-bottom: 8px;"></i>
                <div style="font-size: 11px; color: var(--text-tertiary);">TIME REMAINING</div>
                <div style="font-size: 16px; font-weight: 600;">2 Hours</div>
             </div>
          </div>
        </div>

        <!-- Right Side: Quick Actions -->
        <div class="content-card" style="padding: 24px; background: var(--surface); border: 1px solid var(--border-color); border-radius: 20px;">
          <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 20px;">Quick Actions</h3>
          <div style="display: flex; flex-direction: column; gap: 12px;">
             <a href="payroll.php" class="btn-premium" style="background: var(--brand-green); color: white; justify-content: flex-start; gap: 12px;">
                <i data-lucide="play-circle"></i> Go to Processing
             </a>
             <button class="btn-premium" style="background: var(--surface-hover); border: 1px solid var(--border-color); justify-content: flex-start; gap: 12px;">
                <i data-lucide="file-text"></i> Generate Reports
             </button>
             <button class="btn-premium" style="background: var(--surface-hover); border: 1px solid var(--border-color); justify-content: flex-start; gap: 12px;">
                <i data-lucide="users"></i> Manage Adjustments
             </button>
          </div>
        </div>
      </div>

      <!-- Recent Batches -->
      <div class="payroll-table-container" style="margin-top: 32px;">
        <div style="padding: 20px 24px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color);">
          <h3 style="font-size: 16px; font-weight: 600;">Recent Payroll History</h3>
          <a href="#" style="font-size: 13px; color: var(--brand-green); text-decoration: none;">View All History</a>
        </div>
        <table class="payroll-table">
          <thead>
            <tr>
              <th>Batch ID</th>
              <th>Period</th>
              <th>Status</th>
              <th>Amount</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><strong>PR-2026-004</strong></td>
              <td>Feb 16 - Feb 28, 2026</td>
              <td><span class="badge-premium badge-success">Completed</span></td>
              <td>&#8369;1,248,500.00</td>
              <td><i data-lucide="external-link" style="width: 16px; cursor: pointer; color: var(--text-tertiary);"></i></td>
            </tr>
            <tr>
              <td><strong>PR-2026-003</strong></td>
              <td>Feb 01 - Feb 15, 2026</td>
              <td><span class="badge-premium badge-success">Completed</span></td>
              <td>&#8369;1,248,500.00</td>
              <td><i data-lucide="external-link" style="width: 16px; cursor: pointer; color: var(--text-tertiary);"></i></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </main>
  <script src="../../js/payrolldashboard.js"></script>
  <script>
    lucide.createIcons();
  </script>
</body>
</html>







