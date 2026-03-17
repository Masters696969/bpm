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
        
        <a href="dashboard.php" class="nav-item active">
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
      <!-- Page Title -->
      <div style="margin-bottom: 24px;">
        <h2 style="font-size: 20px; font-weight: 600; color: var(--text-primary);">My Payslips</h2>
        <p style="font-size: 14px; color: var(--text-secondary);">View your approved payroll receipts</p>
      </div>

      <!-- Stats Cards -->
      <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 24px; margin-bottom: 32px;">
        <div class="stat-card-premium">
          <div class="stat-icon-wrapper" style="background: linear-gradient(135deg, rgba(44, 160, 120, 0.15), rgba(16, 185, 129, 0.1)); color: var(--brand-green);">
            <i data-lucide="wallet"></i>
          </div>
          <div class="stat-info">
            <span class="stat-label">Total Net Pay (YTD)</span>
            <h3 class="stat-value" id="statTotalNet" style="color: var(--brand-green);">&#8369;0.00</h3>
          </div>
        </div>
        <div class="stat-card-premium">
          <div class="stat-icon-wrapper" style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.15), rgba(99, 102, 241, 0.1)); color: #3b82f6;">
            <i data-lucide="file-text"></i>
          </div>
          <div class="stat-info">
            <span class="stat-label">Payslips Received</span>
            <h3 class="stat-value" id="statPayslipCount">0</h3>
          </div>
        </div>
        <div class="stat-card-premium">
          <div class="stat-icon-wrapper" style="background: linear-gradient(135deg, rgba(168, 85, 247, 0.15), rgba(139, 92, 246, 0.1)); color: #a855f7;">
            <i data-lucide="trending-up"></i>
          </div>
          <div class="stat-info">
            <span class="stat-label">Average Net Pay</span>
            <h3 class="stat-value" id="statAvgNet">&#8369;0.00</h3>
          </div>
        </div>
      </div>

      <!-- Payslips Table -->
      <div class="table-card" style="background: var(--surface); border-radius: 16px; border: 1px solid var(--border-color); overflow: hidden;">
        <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
          <h3 style="font-size: 16px; font-weight: 600; color: var(--text-primary);">Payroll History</h3>
          <button class="btn-refresh" id="btnRefresh" style="padding: 8px 12px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--surface); cursor: pointer; color: var(--text-secondary);">
            <i data-lucide="refresh-cw" style="width: 16px; height: 16px;"></i>
          </button>
        </div>
        <table class="payslip-table" style="width: 100%; border-collapse: collapse;">
          <thead>
            <tr style="background: var(--background);">
              <th style="padding: 12px 16px; text-align: left; font-size: 12px; font-weight: 600; color: var(--text-tertiary); text-transform: uppercase;">Batch</th>
              <th style="padding: 12px 16px; text-align: left; font-size: 12px; font-weight: 600; color: var(--text-tertiary); text-transform: uppercase;">Period</th>
              <th style="padding: 12px 16px; text-align: left; font-size: 12px; font-weight: 600; color: var(--text-tertiary); text-transform: uppercase;">Pay Type</th>
              <th style="padding: 12px 16px; text-align: right; font-size: 12px; font-weight: 600; color: var(--text-tertiary); text-transform: uppercase;">Basic Pay</th>
              <th style="padding: 12px 16px; text-align: right; font-size: 12px; font-weight: 600; color: var(--text-tertiary); text-transform: uppercase;">Deductions</th>
              <th style="padding: 12px 16px; text-align: right; font-size: 12px; font-weight: 600; color: var(--text-tertiary); text-transform: uppercase;">Net Pay</th>
              <th style="padding: 12px 16px; text-align: center; font-size: 12px; font-weight: 600; color: var(--text-tertiary); text-transform: uppercase;">Action</th>
            </tr>
          </thead>
          <tbody id="payslipsBody">
            <tr>
              <td colspan="7" style="padding: 24px; text-align: center; color: var(--text-secondary);">Loading payslips...</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </main>
  <script src="../../js/payrollreceipt.js"></script>
  <script>
    lucide.createIcons();
  </script>
</body>
</html>