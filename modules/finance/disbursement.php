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
  <title>Payroll Disbursement</title>
  <link rel="stylesheet" href="../../css/disbursement.css?v=1.3">
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
        <a href="Approvalq.php" class="nav-item">
          <i data-lucide="check-circle"></i>
          <span>Approval Queue</span>
        </a>
        <a href="financeapproval.php" class="nav-item">
          <i data-lucide="user-check"></i>
          <span>Finance Approval</span>
        </a>
           <a href="gl.php" class="nav-item">
          <i data-lucide="receipt-text"></i>
          <span>General Ledger</span>
        </a>
          <a href="ap.php" class="nav-item">
          <i data-lucide="scale"></i>
          <span>AP Management</span>
        </a>
        <a href="simulationapproval.php" class="nav-item">
              <i data-lucide="calculator"></i>
              <span>Simulation Approval</span>
            </a>
        <a href="disbursement.php" class="nav-item active">
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
          <h1>Payroll Disbursement</h1>
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
      <!-- Minimalist Stats Grid -->
      <div class="stats-grid">
        <div class="stat-card-minimal">
          <div class="stat-icon text-brand-green">
            <i data-lucide="wallet"></i>
          </div>
          <div class="stat-info">
            <span class="stat-label">Pending</span>
            <span class="stat-value" id="statPendingPayout">&#8369;0.00</span>
          </div>
        </div>

        <div class="stat-card-minimal">
          <div class="stat-icon text-accent-blue">
            <i data-lucide="check-circle-2"></i>
          </div>
          <div class="stat-info">
            <span class="stat-label">Successful</span>
            <span class="stat-value" id="statTotalPaid">&#8369;0.00</span>
          </div>
        </div>

        <div class="stat-card-minimal">
          <div class="stat-icon text-accent-purple">
            <i data-lucide="history"></i>
          </div>
          <div class="stat-info">
            <span class="stat-label">Recent</span>
            <span class="stat-value" id="statRecentCount">0</span>
          </div>
        </div>
      </div>

      <!-- Tab Navigation -->
      <nav class="tabs-nav">
          <button class="tab-link active" data-tab="pending">
              <i data-lucide="layers"></i> Pending Batches
          </button>
          <button class="tab-link" data-tab="history">
              <i data-lucide="file-text"></i> Payout History
          </button>
      </nav>

      <!-- Tab Contents -->
      <div id="pendingTab" class="tab-pane active">
          <!-- Pending Batches -->
          <div class="premium-container">
            <div class="payroll-table-container">
              <table class="payroll-table">
                <thead>
                  <tr>
                    <th>Batch Reference</th>
                    <th>Coverage Period</th>
                    <th>Classification</th>
                    <th>Remaining</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody id="disbursementBatchesBody">
                   <tr>
                     <td colspan="6" class="td-loading-state">
                        <div class="loading-sync">
                            <i data-lucide="refresh-cw" class="spin icon-loading-spin"></i>
                            <p class="text-sync-msg">Synchronizing batch data...</p>
                        </div>
                     </td>
                   </tr>
                </tbody>
              </table>
            </div>
          </div>
      </div>

      <div id="historyTab" class="tab-pane">
          <div class="premium-container">
            <div class="section-header">
                <div class="section-title">
                    <i data-lucide="history"></i>
                    <span>Disbursement History</span>
                </div>
                <div class="search-box">
                    <i data-lucide="search"></i>
                    <input type="search" placeholder="Search payouts..." id="searchPayouts">
                </div>
            </div>
            <div class="payroll-table-container">
                <table class="payroll-table">
                    <thead>
                        <tr>
                            <th>Batch Reference</th>
                            <th>Coverage Period</th>
                            <th>Classification</th>
                            <th>Remaining</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="payoutHistoryBody">
                        <tr>
                            <td colspan="6" class="td-empty-state">No history found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
          </div>
      </div>
    </div>

    <!-- Batch Detail Modal -->
    <div id="batchDetailModal" class="modal-overlay">
      <div class="modal-premium">
        <div class="modal-header-premium">
            <div class="section-title">
                <i data-lucide="users" class="text-brand-green"></i>
                <span>Batch Detail: <span id="currentBatchCodeLabel" class="text-brand-green"></span></span>
            </div>
            <div class="flex-center-gap">
                <button class="btn-execute-bulk" id="btnPayAll">
                   <i data-lucide="send" class="btn-icon-tiny"></i> Execute Bulk
                </button>
                <button id="closeBatchModal">
                    <i data-lucide="x"></i>
                </button>
            </div>
        </div>
        <div class="modal-body-premium">
            <table class="payroll-table">
            <thead>
                <tr>
                    <th>Beneficiary</th>
                    <th>Institution</th>
                    <th>Account Identifier</th>
                    <th>Net Amount</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="batchEmployeesBody">
                <tr>
                    <td colspan="6" class="td-empty-state">Select a batch.</td>
                </tr>
            </tbody>
            </table>
        </div>
      </div>
    </div>

    <br>
  </main>
  <script src="../../js/disbursement.js"></script>
  <script>
    lucide.createIcons();
  </script>
</body>
</html>
