<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../../login.php");
    exit();
}

require_once '../../config/config.php';

// Fetch active merit matrix settings
$sql = "SELECT matrix_id as MeritMatrixID, performance_rating as Rating, compa_ratio_range as CompaRatioRange, min_increase_pct as MinIncrease, max_increase_pct as MaxIncrease 
        FROM merit_matrix_settings 
        WHERE period_id = 1 
        ORDER BY performance_rating DESC, compa_ratio_range ASC";
$result = $conn->query($sql);
$matrix_settings = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $matrix_settings[] = $row;
    }
}

$page = 'meritmatrixmgt';
$module = 'planning';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Merit Matrix Management</title>
  <link rel="stylesheet" href="../../css/meritmatrixmgt.css?v=1.2">
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
        <div class="content-card">
            <div class="card-header">
                <div class="card-header-left">
                    <h3 class="card-title">Current Merit Matrix Settings</h3>
                    <p class="card-subtitle">Active performance multipliers for the current compensation cycle.</p>
                </div>
            </div>
            <div class="card-body">
                <div class="data-table">
                    <table id="currentMatrixTable">
                        <thead>
                            <tr>
                                <th>Performance Rating</th>
                                <th>Compa-Ratio Range</th>
                                <th>Minimum Increase (%)</th>
                                <th>Maximum Increase (%)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($matrix_settings as $setting): ?>
                            <tr data-id="<?php echo $setting['MeritMatrixID']; ?>">
                                <td><strong><?php echo htmlspecialchars($setting['Rating']); ?></strong></td>
                                <td><?php echo htmlspecialchars($setting['CompaRatioRange']); ?></td>
                                <td><?php echo number_format($setting['MinIncrease'], 1); ?>%</td>
                                <td><?php echo number_format($setting['MaxIncrease'], 1); ?>%</td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if(empty($matrix_settings)): ?>
                            <tr><td colspan="4" style="text-align:center;">No active merit matrix settings found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="content-card" style="margin-top: 24px;">
            <div class="card-header">
                <div class="card-header-left">
                    <h3 class="card-title">Endorsed Merit Matrix Proposals</h3>
                    <p class="card-subtitle">Proposals reviewed by Supervisors awaiting Manager approval.</p>
                </div>
            </div>
            <div class="card-body">
                <div class="data-table">
                    <table id="endorsedProposalsTable">
                        <thead>
                            <tr>
                                <th>Proposed By</th>
                                <th>Request Type</th>
                                <th>Total Changes</th>
                                <th>Date Requested</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="endorsedProposalsBody">
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">
                                        <i data-lucide="loader-2"></i>
                                        <p>Loading endorsed proposals…</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Review Proposal Modal -->
    <div class="modal-overlay hidden" id="proposalActionModal">
      <div class="rem-dialog" style="max-width: 800px; width: 90%;">
        <!-- Header -->
        <div class="rem-header">
          <div class="rem-header-left">
            <div class="rem-icon-box" style="background: rgba(139, 92, 246, 0.1); color: #8b5cf6;">
              <i data-lucide="trending-up"></i>
            </div>
            <div class="rem-title-group">
              <h3 class="rem-title">Review Merit Matrix Proposal</h3>
              <p class="rem-subtitle">Final review of endorsed adjustments before making them official.</p>
            </div>
          </div>
          <button type="button" class="rem-close" id="btnCloseProposalModal">
            <i data-lucide="x"></i>
          </button>
        </div>

        <!-- Body -->
        <div class="rem-body">
            <div class="rem-section">
                <div class="rem-section-hdr rem-shdr-blue">
                    <i data-lucide="file-diff"></i> Proposed Merit Matrix Adjustments
                </div>
                <!-- Box for Reason -->
                <div style="background:var(--surface-hover); padding:16px; border-radius:8px; margin-bottom:16px;">
                    <strong>Reason for Proposal:</strong>
                    <div id="proposalReasonText" style="margin-top:8px; color:var(--text-secondary); white-space:pre-wrap; font-size:14px;"></div>
                </div>

                <div class="rem-table-wrapper" style="margin-top:20px;">
                  <table class="rem-table" id="proposalDetailsTable" style="margin: 0;">
                    <thead>
                      <tr>
                        <th>Performance Score</th>
                        <th>Compa-Ratio Range</th>
                        <th>Proposed Min Increase (%)</th>
                        <th>Proposed Max Increase (%)</th>
                      </tr>
                    </thead>
                    <tbody id="proposalDetailsBody">
                        <tr><td colspan="4" style="text-align:center;">Loading details...</td></tr>
                    </tbody>
                  </table>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="rem-footer">
          <div class="rem-footer-hint">
            <i data-lucide="info"></i>
            Approved requests will be forwarded to the Finance department for final review and application.
          </div>
          <div style="display:flex; gap:12px;">
            <button type="button" class="rem-btn-send rem-btn-secondary" id="btnRejectProposal">
                <i data-lucide="x-circle" style="color:var(--brand-red);"></i> Reject
            </button>
            <button type="button" class="rem-btn-send rem-btn-blue" id="btnEndorseProposal">
                <i data-lucide="check-circle"></i> Approve & Forward to Finance
            </button>
          </div>
        </div>
      </div>
    </div>

  </main>
  <script src="../../js/notifications.js?v=1.1"></script>
  <script src="../../js/meritmatrixmgt.js"></script>
</body>
</html>





