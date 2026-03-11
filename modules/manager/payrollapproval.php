<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../../login.php");
    exit();
}
$page = 'payrollapproval';
$module = 'payroll';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payroll Approval | Microfinance</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../../css/payrollapproval.css?v=1.3">
  <link rel="stylesheet" href="../../css/notifications.css?v=1.1">
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
          <span>HR Analytics</span>
        </a>

        <div class="nav-item-group <?php echo ($module === 'banking') ? 'active' : ''; ?>">
          <button class="nav-item has-submenu <?php echo ($module === 'banking') ? 'active' : ''; ?>" data-module="banking">
            <div class="nav-item-content">
              <i data-lucide="book-user"></i>
              <span>Core Human Capital</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu <?php echo ($module === 'banking') ? 'active' : ''; ?>" id="submenu-banking">
            <a href="Bankverification.php" class="submenu-item <?php echo ($page === 'Bankverification') ? 'active' : ''; ?>">
              <i data-lucide="shield-check"></i>
              <span>Bank Verification</span>
            </a>
            <a href="informationapproval.php" class="submenu-item <?php echo ($page === 'informationapproval') ? 'active' : ''; ?>">
              <i data-lucide="file-check"></i>
              <span>Information Approval</span>
            </a>
          </div>
        </div>

        <div class="nav-item-group <?php echo ($module === 'planning') ? 'active' : ''; ?>">
          <button class="nav-item has-submenu <?php echo ($module === 'planning') ? 'active' : ''; ?>" data-module="planning">
            <div class="nav-item-content">
              <i data-lucide="circle-pile"></i>
              <span>Compensation Planning</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
           <div class="submenu <?php echo ($module === 'planning') ? 'active' : ''; ?>" id="submenu-planning">
             <a href="salarymgt.php" class="submenu-item <?php echo ($page === 'salarymgt') ? 'active' : ''; ?>">
              <i data-lucide="banknote"></i>
              <span>Salary & Scales Management</span>
            </a>
             <a href="statutorymgt.php" class="submenu-item <?php echo ($page === 'statutorymgt') ? 'active' : ''; ?>">
              <i data-lucide="scale"></i>
              <span>Statutory Contribution Management</span>
            </a>
            <a href="meritmatrixmgt.php" class="submenu-item <?php echo ($page === 'meritmatrixmgt') ? 'active' : ''; ?>">
              <i data-lucide="badge-percent"></i>
              <span>Merit Matrix Management</span>
            </a>
             <a href="allowancemgt.php" class="submenu-item <?php echo ($page === 'allowancemgt') ? 'active' : ''; ?>">
              <i data-lucide="gift"></i>
              <span>Allowances Management</span>
            </a>
             <a href="compensationrev.php" class="submenu-item <?php echo ($page === 'compensationrev') ? 'active' : ''; ?>">
              <i data-lucide="boxes"></i>
              <span>Compensation Review</span>
            </a>
          </div>
        </div>

        <div class="nav-item-group <?php echo ($module === 'payroll') ? 'active' : ''; ?>">
          <button class="nav-item has-submenu <?php echo ($module === 'payroll') ? 'active' : ''; ?>" data-module="payroll">
            <div class="nav-item-content">
              <i data-lucide="banknote-arrow-down"></i>
              <span>Payroll</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu <?php echo ($module === 'payroll') ? 'active' : ''; ?>" id="submenu-payroll">
            <a href="#" class="submenu-item <?php echo ($page === 'applications') ? 'active' : ''; ?>"><i data-lucide="file-plus"></i><span>Applications</span></a>
            <a href="payrollapproval.php" class="submenu-item <?php echo ($page === 'payrollapproval') ? 'active' : ''; ?>"><i data-lucide="check-circle"></i><span>Payroll Approval</span></a>
            <a href="#" class="submenu-item <?php echo ($page === 'approvals') ? 'active' : ''; ?>"><i data-lucide="check-circle"></i><span>Approvals</span></a>
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
          <span class="user-name"><?php echo htmlspecialchars($_SESSION['username'] ?? 'Manager'); ?></span>
          <span class="user-role"><?php echo htmlspecialchars($_SESSION['user_role'] ?? 'HR Manager'); ?></span>
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
        <div class="header-title">
          <h1>Payroll Approval</h1>
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

      <!-- Stats Cards -->
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px; margin-bottom: 32px;">
        <div class="stat-card-premium">
          <div class="stat-icon-wrapper" style="background: linear-gradient(135deg, rgba(255, 193, 7, 0.15), rgba(255, 152, 0, 0.1)); color: var(--brand-yellow);">
            <i data-lucide="clock"></i>
          </div>
          <div class="stat-info">
            <span class="stat-label">Pending Approval</span>
            <h3 class="stat-value" id="statPendingCount" style="color: var(--brand-yellow);">0</h3>
          </div>
        </div>
        <div class="stat-card-premium">
          <div class="stat-icon-wrapper" style="background: linear-gradient(135deg, rgba(44, 160, 120, 0.15), rgba(16, 185, 129, 0.1)); color: var(--brand-green);">
            <i data-lucide="check-circle"></i>
          </div>
          <div class="stat-info">
            <span class="stat-label">Approved This Month</span>
            <h3 class="stat-value" id="statApprovedCount" style="color: var(--brand-green);">0</h3>
          </div>
        </div>
        <div class="stat-card-premium">
          <div class="stat-icon-wrapper" style="background: linear-gradient(135deg, rgba(239, 68, 68, 0.15), rgba(220, 38, 38, 0.1)); color: #ef4444;">
            <i data-lucide="x-circle"></i>
          </div>
          <div class="stat-info">
            <span class="stat-label">Rejected</span>
            <h3 class="stat-value" id="statRejectedCount" style="color: #ef4444;">0</h3>
          </div>
        </div>
      </div>

      <!-- Pending Batches Section -->
      <div style="background: var(--surface); border-radius: 20px; border: 1px solid var(--border-color); overflow: hidden;">
        <div style="padding: 20px 24px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
          <div style="display: flex; align-items: center; gap: 12px;">
            <div style="width: 40px; height: 40px; border-radius: 12px; background: rgba(255, 193, 7, 0.1); display: flex; align-items: center; justify-content: center;">
              <i data-lucide="inbox" style="width: 20px; height: 20px; color: var(--brand-yellow);"></i>
            </div>
            <div>
              <h3 style="font-size: 18px; font-weight: 600; color: var(--text-primary);">Approval Queue</h3>
              <p style="font-size: 13px; color: var(--text-tertiary);">Batches awaiting your review</p>
            </div>
          </div>
          <button id="btnRefresh" style="padding: 10px 16px; border-radius: 10px; border: 1px solid var(--border-color); background: var(--surface); cursor: pointer; color: var(--text-secondary); display: flex; align-items: center; gap: 8px; font-weight: 500; transition: all 0.2s ease;">
            <i data-lucide="refresh-cw" style="width: 16px; height: 16px;"></i>
            <span>Refresh</span>
          </button>
        </div>
        
        <div id="pendingBatchesContainer">
          <div id="pendingBatchesBody" style="padding: 16px;">
            <!-- Batches will be rendered here as cards -->
          </div>
        </div>
      </div>
      </div>
    </div>
  </main>
  <script src="../../js/notifications.js?v=1.1"></script>
  <script src="../../js/payrollapproval.js"></script>
</body>





