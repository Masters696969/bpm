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
  <title>Simulation Approval</title>
  <link rel="stylesheet" href="../../css/simulationapproval.css?v=1.2">
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
        <a href="simulationapproval.php" class="nav-item active">
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
          <h1>Simulation Approval</h1>
          <p>Final review and commitment of compensation cycles.</p>
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
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 24px;">
            <div class="stat-card" style="background: var(--surface); padding: 20px; border-radius: 12px; display: flex; align-items: center; gap: 16px; border: 1px solid var(--border-color);">
                <div class="stat-icon" style="background: rgba(139, 92, 246, 0.1); color: var(--brand-purple); width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                    <i data-lucide="calculator"></i>
                </div>
                <div class="stat-content">
                    <span class="stat-label" style="display: block; font-size: 13px; color: var(--text-secondary); margin-bottom: 4px;">Reviewed & Pending Approval</span>
                    <span class="stat-value" id="pendingFinalCount" style="display: block; font-size: 24px; font-weight: 700; color: var(--text-primary);">0</span>
                </div>
            </div>
        </div>

        <!-- Simulations Table -->
        <div class="content-card" style="background: var(--surface); border-radius: 12px; border: 1px solid var(--border-color); overflow: hidden;">
            <div class="card-header" style="padding: 20px; border-bottom: 1px solid var(--border-color);">
                <h3 class="card-title" style="font-size: 18px; font-weight: 700; color: var(--text-primary);">Final Compensation Approval</h3>
                <p class="card-subtitle" style="font-size: 13px; color: var(--text-secondary);">Final review and application of merit simulations.</p>
            </div>
            <div class="card-body" style="padding: 20px;">
                <div class="data-table" style="overflow-x: auto;">
                    <table class="role-table" style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="text-align: left; border-bottom: 1px solid var(--border-color);">
                                <th style="padding: 12px; font-size: 13px; font-weight: 600; color: var(--text-secondary);">Cycle Name</th>
                                <th style="padding: 12px; font-size: 13px; font-weight: 600; color: var(--text-secondary);">Total Cost</th>
                                <th style="padding: 12px; font-size: 13px; font-weight: 600; color: var(--text-secondary);">Submitted By</th>
                                <th style="padding: 12px; font-size: 13px; font-weight: 600; color: var(--text-secondary);">Date Reviewed</th>
                                <th style="padding: 12px; font-size: 13px; font-weight: 600; color: var(--text-secondary);">Action</th>
                            </tr>
                        </thead>
                        <tbody id="financeTableBody">
                            <tr><td colspan="5" style="text-align:center; padding:40px; color:var(--text-secondary);">Loading simulations...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Final Approval Modal -->
    <div id="approvalModal" class="modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1050; align-items:center; justify-content:center; padding:20px;">
        <div class="modal-content" style="background: var(--surface); width: 100%; max-width: 1400px; max-height: 90vh; border-radius: 16px; display: flex; flex-direction: column; overflow: hidden; border: 1px solid var(--border-color); box-shadow: var(--shadow-lg);">
            <div class="modal-header" style="padding:20px; border-bottom:1px solid var(--border-color); display:flex; justify-content:space-between; align-items:center;">
                <div>
                    <h3 id="modalTitle" style="font-size:18px; font-weight:700; color:var(--text-primary); margin:0;">Final Finance Approval</h3>
                    <p id="modalSubtitle" style="font-size:13px; color:var(--text-secondary); margin:4px 0 0 0;">Mandatory final review of all calculations.</p>
                </div>
                <button id="closeModal" style="background:none; border:none; color:var(--text-secondary); cursor:pointer; position:relative; z-index:1051; padding:10px; border-radius:8px; display:flex; align-items:center; justify-content:center; transition: background 0.2s;" onmouseover="this.style.background='rgba(0,0,0,0.05)'" onmouseout="this.style.background='none'"><i data-lucide="x"></i></button>
            </div>
            
            <div class="modal-body" style="padding:0; flex-grow:1; overflow:auto;">
                <div id="modalTableContainer" style="padding:20px;">
                    <!-- Table injected here -->
                </div>
            </div>

            <div class="modal-footer" style="padding:20px; border-top:1px solid var(--border-color); display:flex; gap:12px; justify-content:flex-end; background:var(--background-alt);">
                <button class="action-btn btn-secondary" id="cancelApproval" style="padding:8px 20px; border-radius:8px; border:1px solid var(--border-color); background: var(--surface); color:var(--text-secondary); cursor:pointer; font-weight:600;">Cancel</button>
                <button class="action-btn btn-reject" id="rejectBtn" style="padding:8px 20px; border-radius:8px; border:1px solid var(--brand-red); background:rgba(239,68,68,0.1); color:var(--brand-red); cursor:pointer; font-weight:600;">Reject</button>
                <button class="action-btn btn-approve" id="approveBtn" style="padding:8px 20px; border-radius:8px; border:1px solid var(--brand-green); background:var(--brand-green); color:white; cursor:pointer; font-weight:600; display:flex; align-items:center; gap:8px;">
                    <i data-lucide="check-circle" style="width:16px;height:16px;"></i> Final Approve & Commit
                </button>
            </div>
        </div>
    </div>
  </main>
  <script src="../../js/simulationapproval.js"></script>
  <script>
    lucide.createIcons();
    // Initialize notifications with approval target
    if (typeof initGlobalNotifications === 'function') {
        initGlobalNotifications('compensation_approval');
    }
  </script>
</body>
</html>







