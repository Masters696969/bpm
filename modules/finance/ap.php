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
  <title>AP Management | Microfinance</title>
  <link rel="stylesheet" href="../../css/ap.css?v=1.2">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@500;600;700&display=swap" rel="stylesheet">
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
          <i data-lucide="layout-dashboard"></i>
          <span>Dashboard</span>
        </a>
        <a href="Approvalq.php" class="nav-item active">
          <i data-lucide="check-circle"></i>
          <span>Approval Queue</span>
        </a>
         <a href="financeapproval.php" class="nav-item active">
          <i data-lucide="user-check"></i>
          <span>Finance Approval</span>
        </a>
           <a href="gl.php" class="nav-item active">
          <i data-lucide="receipt-text"></i>
          <span>General Ledger</span>
        </a>
          <a href="ap.php" class="nav-item active">
          <i data-lucide="scale"></i>
          <span>AP Management</span>
        </a>
        <a href="simulationapproval.php" class="nav-item">
              <i data-lucide="calculator"></i>
              <span>Simulation Approval</span>
            </a>
        <a href="disbursement.php" class="nav-item">
              <i data-lucide="banknote-arrow-up"></i>
              <span>Payroll Disbursement</span>
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
          <h1>AP Management Overview</h1>
          <p>Welcome back, <?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?>! Manage financial obligations here.</p>
        </div>
      </div>
      <div class="header-right">
        <div class="header-clock">
          <i data-lucide="clock"></i>
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
      <div class="stats-grid">
        <div class="stat-card-premium">
          <div class="stat-icon-wrapper" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">
            <i data-lucide="clock"></i>
          </div>
          <div class="stat-info">
            <span class="stat-label">Pending Vouchers</span>
            <h3 class="stat-value" id="statPendingVouchers">0</h3>
          </div>
        </div>

        <div class="stat-card-premium">
          <div class="stat-icon-wrapper" style="background: rgba(44, 160, 120, 0.1); color: var(--brand-green);">
            <i data-lucide="check-circle"></i>
          </div>
          <div class="stat-info">
            <span class="stat-label">Total Payable</span>
            <h3 class="stat-value" id="statTotalPayable">&#8369;0.00</h3>
          </div>
        </div>

        <div class="stat-card-premium">
          <div class="stat-icon-wrapper" style="background: rgba(255, 193, 7, 0.1); color: var(--brand-yellow);">
            <i data-lucide="history"></i>
          </div>
          <div class="stat-info">
            <span class="stat-label">Processed Today</span>
            <h3 class="stat-value" id="statProcessedToday">0</h3>
          </div>
        </div>
      </div>

      <!-- Control Header -->
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div class="premium-tabs">
          <button class="tab-btn active" data-tab="pending">
            <i data-lucide="list-todo" style="width: 18px;"></i>
            Pending Payments
          </button>
          <button class="tab-btn" data-tab="history">
            <i data-lucide="history" style="width: 18px;"></i>
            Payment History
          </button>
        </div>
        
        <div class="search-box">
          <i data-lucide="search"></i>
          <input type="search" id="apSearch" placeholder="Search vouchers...">
        </div>
      </div>

      <!-- Tab Content: Pending -->
      <div class="tab-panel active" id="pending">
        <div class="payroll-table-container">
          <table class="payroll-table">
            <thead>
              <tr>
                <th>Batch Code</th>
                <th>Employee</th>
                <th>Category</th>
                <th>Payee Name</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody id="apPendingBody">
              <tr>
                <td colspan="6" style="text-align: center; padding: 40px; color: var(--text-tertiary);">
                  <i data-lucide="loader" class="spin" style="margin-bottom: 12px; display: block; margin-left: auto; margin-right: auto;"></i>
                  Loading accounts payable data...
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Tab Content: History -->
      <div class="tab-panel" id="history">
        <div class="payroll-table-container">
          <table class="payroll-table">
            <thead>
              <tr>
                <th>Batch Code</th>
                <th>Employee</th>
                <th>Category</th>
                <th>Payee Name</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Released Date</th>
              </tr>
            </thead>
            <tbody id="apHistoryBody"></tbody>
          </table>
        </div>
      </div>
    </div>
    <!-- AP Details Modal -->
    <div id="apDetailsModal" class="modal-premium" style="display: none;">
        <div class="modal-content-premium">
            <div class="modal-header-premium">
                <div class="modal-title-wrapper">
                    <i data-lucide="info" class="modal-title-icon"></i>
                    <div>
                        <h2 id="modalVoucherTitle">Voucher Details</h2>
                        <p id="modalVoucherSubtitle">Employee Breakdown</p>
                    </div>
                </div>
                <button class="close-modal-btn" onclick="closeDetailsModal()">
                    <i data-lucide="x"></i>
                </button>
            </div>
            <div class="modal-body-premium">
                <div class="payroll-table-container" style="max-height: 400px; overflow-y: auto;">
                    <table class="payroll-table">
                        <thead>
                            <tr>
                                <th>Employee Name</th>
                                <th>Amount</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody id="apDetailsBody">
                            <!-- Details populated by JS -->
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer-premium" style="display: flex; justify-content: flex-end; padding-top: 20px;">
                    <button class="tab-btn" onclick="closeDetailsModal()" style="background: var(--surface-hover); color: var(--text-primary);">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../../js/ap.js"></script>
  <script>
    lucide.createIcons();
  </script>
</body>
</html>







