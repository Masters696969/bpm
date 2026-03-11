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
  <link rel="stylesheet" href="../../css/disbursement.css?v=1.2">
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
          <div class="stat-icon" style="color: var(--brand-green);">
            <i data-lucide="wallet"></i>
          </div>
          <div class="stat-info">
            <span class="stat-label">Pending</span>
            <span class="stat-value" id="statPendingPayout">&#8369;0.00</span>
          </div>
        </div>

        <div class="stat-card-minimal">
          <div class="stat-icon" style="color: #3b82f6;">
            <i data-lucide="check-circle-2"></i>
          </div>
          <div class="stat-info">
            <span class="stat-label">Successful</span>
            <span class="stat-value" id="statTotalPaid">&#8369;0.00</span>
          </div>
        </div>

        <div class="stat-card-minimal">
          <div class="stat-icon" style="color: #8b5cf6;">
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
                     <td colspan="6" style="padding: 60px; text-align: center; color: var(--text-secondary);">
                        <div class="loading-sync">
                            <i data-lucide="refresh-cw" class="spin" style="margin-bottom: 12px; opacity: 0.5;"></i>
                            <p style="font-weight: 500; letter-spacing: 0.5px;">Synchronizing batch data...</p>
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
                            <td colspan="6" style="padding: 40px; text-align: center; color: var(--text-secondary);">No history found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
          </div>
      </div>
    </div>

    <!-- Batch Detail Modal -->
    <div id="batchDetailModal" class="modal-overlay" style="display: none; position: fixed; inset: 0; background: var(--background); z-index: 1050; display: flex; align-items: center; justify-content: center; opacity: 0; pointer-events: none; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);">
      <div class="modal-premium" style="background: var(--surface-card); width: 90%; max-width: 800px; max-height: 85vh; border-radius: 20px; border: 1px solid var(--border-color); box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); display: flex; flex-direction: column; transform: scale(0.95); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);">
        <div class="section-header" style="padding: 24px; border-bottom: 1px solid var(--border-color); margin: 0;">
            <div class="section-title">
                <i data-lucide="users" style="color: var(--brand-green);"></i>
                <span>Batch Detail: <span id="currentBatchCodeLabel" style="color: var(--brand-green);"></span></span>
            </div>
            <div style="display: flex; gap: 12px; align-items: center;">
                <button class="btn-premium" id="btnPayAll" style="background: var(--brand-green); color: white; border-radius: 12px; padding: 10px 24px; font-weight: 700; box-shadow: 0 4px 6px -1px rgba(44, 160, 120, 0.2);">
                   <i data-lucide="send" style="width: 14px; margin-right: 6px;"></i> Execute Bulk
                </button>
                <button id="closeBatchModal" style="background: var(--background); border: 1px solid var(--border-color); width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; cursor: pointer; color: var(--text-secondary); transition: all 0.2s;">
                    <i data-lucide="x"></i>
                </button>
            </div>
        </div>
        <div class="payroll-table-container" style="padding: 24px; overflow-y: auto; flex: 1;">
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
                    <td colspan="6" style="padding: 40px; text-align: center; color: var(--text-secondary);">Select a batch.</td>
                </tr>
            </tbody>
            </table>
        </div>
      </div>
    </div>

    <style>
        .modal-overlay {
            background: var(--background) !important;
        }
        .modal-overlay.open {
            opacity: 1 !important;
            pointer-events: auto !important;
        }
        .modal-overlay.open .modal-premium {
            transform: scale(1) !important;
        }
        #closeBatchModal:hover {
            background: var(--surface-hover);
            color: #ef4444;
            border-color: #ef4444;
        }
        
        /* Modal Table Card Styles */
        #batchDetailModal .payroll-table {
            border-spacing: 0 10px;
            border-collapse: separate;
        }
        #batchDetailModal .payroll-table th {
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 1px;
            color: var(--text-tertiary);
            padding: 0 24px 12px;
            border: none;
        }
        #batchDetailModal .payroll-table tr td {
            background: var(--background);
            border-top: 1px solid var(--border-color);
            border-bottom: 1px solid var(--border-color);
            padding: 16px 24px;
        }
        #batchDetailModal .payroll-table tr td:first-child {
            border-left: 1px solid var(--border-color);
            border-radius: 16px 0 0 16px;
        }
        #batchDetailModal .payroll-table tr td:last-child {
            border-right: 1px solid var(--border-color);
            border-radius: 0 16px 16px 0;
        }
        
        #batchDetailModal .payroll-table tr:hover td {
            background: var(--surface-hover);
            border-color: var(--brand-green-light);
        }

        .institution-badge {
            background: rgba(59, 130, 246, 0.08);
            color: #3b82f6;
            padding: 4px 12px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 700;
            border: 1px solid rgba(59, 130, 246, 0.2);
            display: inline-flex;
            align-items: center;
        }
        
        .processed-indicator {
            color: var(--brand-green);
            font-size: 12px;
            font-weight: 600;
            display: flex;
            align-items: center;
            opacity: 0.8;
        }

        /* Modal Scrollbar */
        .payroll-table-container::-webkit-scrollbar {
            width: 6px;
        }
        .payroll-table-container::-webkit-scrollbar-track {
            background: transparent;
        }
        .payroll-table-container::-webkit-scrollbar-thumb {
            background: var(--border-color);
            border-radius: 10px;
        }
        .payroll-table-container::-webkit-scrollbar-thumb:hover {
            background: var(--text-tertiary);
        }

        .spin {
            animation: spin 2s linear infinite;
            display: inline-block;
        }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .loading-sync {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }

        /* Ensure SweetAlert is always on top */
        .swal2-container {
            z-index: 11000 !important;
        }
    </style>
  </main>
  <script src="../../js/disbursement.js"></script>
  <script>
    lucide.createIcons();
  </script>
</body>
</html>
