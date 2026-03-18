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
  <link rel="stylesheet" href="../../css/payrollreceipt.css?v=1.2">
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
          <i data-lucide="chart-no-axes-combined"></i>
          <span>Dashboard</span>
        </a>

        <a href="#" class="nav-item">
              <i data-lucide="file-clock"></i>
              <span>Time Attendance</span>
            </a>
            <a href="information_management.php" class="nav-item">
              <i data-lucide="user-pen"></i>
              <span>Information Management</span>
            </a>
            <a href="applybank.php" class="nav-item">
              <i data-lucide="landmark"></i>
              <span>Apply Bank Account</span>
            </a>
            <a href="#" class="nav-item">
              <i data-lucide="tickets-plane"></i>
              <span>Leave Management</span>
            </a>
             <a href="#" class="nav-item">
              <i data-lucide="receipt-text"></i>
              <span>Claim Management</span>
            </a>
            <a href="payslip.php" class="nav-item">
              <i data-lucide="ticket-check"></i>
              <span>View Payslip</span>
            </a>
            <a href="learningmgt.php" class="nav-item active">
              <i data-lucide="book-open"></i>
              <span>Learning Management</span>
            </a>
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
          <span class="user-name"><?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></span>
          <span class="user-role"><?php echo htmlspecialchars($_SESSION['user_role'] ?? 'Employee'); ?></span>
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
          <h1>Learning Management</h1>
          <p>Complete your onboarding and assigned training modules here.</p>
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
      <!-- Page Title -->
      <div style="margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2 style="font-size: 20px; font-weight: 600; color: var(--text-primary);">My Assigned Modules</h2>
            <p style="font-size: 14px; color: var(--text-secondary);">Start and complete your required training program.</p>
        </div>
        <button class="btn-refresh" id="btnRefresh" style="padding: 8px 12px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--surface); cursor: pointer; color: var(--text-secondary); display:flex; align-items:center; gap:8px;">
            <i data-lucide="refresh-cw" style="width: 16px; height: 16px;"></i> Refresh
        </button>
      </div>

      <!-- Training Modules Grid -->
      <div id="learningModulesGrid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 24px;">
           <div class="empty-state" style="grid-column: 1 / -1; padding: 48px; background: var(--surface); border: 1px solid var(--border-color); border-radius: 16px; text-align: center;">
                <i data-lucide="loader-2" class="spin" style="color: var(--brand-green); width: 32px; height: 32px; margin-bottom: 16px;"></i>
                <h3 style="color: var(--text-primary); margin-bottom: 8px;">Loading your modules...</h3>
           </div>
      </div>

    </div>
  </main>
  
  <style>
    .module-card {
        background: var(--surface);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 24px;
        display: flex;
        flex-direction: column;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .module-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.05);
    }
    .module-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 16px;
    }
    .module-icon-container {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .module-icon-container.pending {
        background: rgba(245, 158, 11, 0.15);
        color: #f59e0b;
    }
    .module-icon-container.in-progress {
        background: rgba(59, 130, 246, 0.15);
        color: #3b82f6;
    }
    .module-icon-container.completed {
        background: rgba(44, 160, 120, 0.15);
        color: var(--brand-green);
    }
    .module-badge {
        font-size: 11px;
        font-weight: 600;
        padding: 4px 8px;
        border-radius: 6px;
        text-transform: uppercase;
    }
    .module-badge.pending {
        background: rgba(245, 158, 11, 0.1);
        color: #f59e0b;
    }
    .module-badge.in-progress {
        background: rgba(59, 130, 246, 0.1);
        color: #3b82f6;
    }
    .module-badge.completed {
        background: rgba(44, 160, 120, 0.1);
        color: var(--brand-green);
    }
    .module-title {
        font-size: 16px;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 8px;
        line-height: 1.3;
    }
    .module-desc {
        font-size: 13px;
        color: var(--text-secondary);
        flex: 1;
        margin-bottom: 20px;
    }
    .module-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 16px;
        border-top: 1px solid var(--border-color);
        margin-top: auto;
    }
    .btn-action {
        background: var(--brand-green);
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        transition: background 0.2s ease;
    }
    .btn-action:hover {
        background: #238462;
    }
    .btn-action.secondary {
        background: transparent;
        color: var(--text-secondary);
        border: 1px solid var(--border-color);
    }
    .btn-action.secondary:hover {
        background: var(--surface-hover);
        color: var(--text-primary);
    }
  </style>

  <script src="../../js/learningmgt.js?v=<?php echo time(); ?>"></script>
  <script>
    lucide.createIcons();
  </script>
</body>
</html>