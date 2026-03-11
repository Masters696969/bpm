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
  <title>Finance Disbursement</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../../css/disbursement.css?v=1.1">
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
        <div class="header-title">
          <h1>Payroll Disbursement</h1>
          <p>Review and release approved payroll funds to accounts.</p>
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
        <button class="icon-btn"><i data-lucide="bell"></i></button>
      </div>
    </header>
    <div class="content-wrapper">
      <!-- Stats Grid -->
      <div class="stats-grid">
        <div class="stat-card-premium">
          <div class="stat-icon-wrapper" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">
            <i data-lucide="loader"></i>
          </div>
          <div class="stat-info">
            <span class="stat-label">Pending Disbursement</span>
            <h3 class="stat-value" id="statPendingDisbursement">&#8369;0.00</h3>
          </div>
        </div>

        <div class="stat-card-premium">
          <div class="stat-icon-wrapper" style="background: rgba(44, 160, 120, 0.1); color: var(--brand-green);">
            <i data-lucide="check-circle"></i>
          </div>
          <div class="stat-info">
            <span class="stat-label">Total Disbursed</span>
            <h3 class="stat-value" id="statTotalDisbursed">&#8369;0.00</h3>
          </div>
        </div>

        <div class="stat-card-premium">
          <div class="stat-icon-wrapper" style="background: rgba(255, 193, 7, 0.1); color: var(--brand-yellow);">
            <i data-lucide="package"></i>
          </div>
          <div class="stat-info">
            <span class="stat-label">Ready Batches</span>
            <h3 class="stat-value" id="statPendingCount">0 Batches</h3>
          </div>
        </div>
      </div>

      <!-- Control Header -->
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <div class="premium-tabs">
          <button class="tab-btn active" data-tab="ready">
            <i data-lucide="play-circle" style="width: 18px;"></i>
            Ready for Disbursement
          </button>
          <button class="tab-btn" data-tab="history">
            <i data-lucide="history" style="width: 18px;"></i>
            Disbursement History
          </button>
        </div>
        
        <div class="search-box">
          <i data-lucide="search"></i>
          <input type="search" placeholder="Search batch code...">
        </div>
      </div>

      <!-- Tab Content: Ready -->
      <div class="tab-panel active" id="ready">
        <div class="payroll-table-container">
          <table class="payroll-table">
            <thead>
              <tr>
                <th>Batch ID</th>
                <th>Period</th>
                <th>Type</th>
                <th>Total Distributed</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody id="disbursementBatchesBody"></tbody>
          </table>
        </div>
      </div>

      <!-- Tab Content: History -->
      <div class="tab-panel" id="history">
        <div class="payroll-table-container">
          <table class="payroll-table">
            <thead>
              <tr>
                <th>Batch ID</th>
                <th>Period</th>
                <th>Type</th>
                <th>Total Distributed</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody id="disbursementHistoryBody"></tbody>
          </table>
        </div>
      </div>

    </div>
  </main>

  <script src="../../js/disbursement.js"></script>
</body>
</html>
