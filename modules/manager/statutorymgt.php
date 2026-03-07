<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../../login.php");
    exit();
}
$page = 'statutorymgt';
$module = 'planning';

require_once '../../config/config.php';

// Fetch current settings (similar to cycle.php)
$period_id = 1; // Assume period 1 as in cycle.php for consistency
$sss_query = $conn->query("SELECT * FROM sss_settings WHERE period_id = $period_id");
$sss_data = $sss_query->fetch_assoc();

$ph_query = $conn->query("SELECT * FROM philhealth_settings WHERE period_id = $period_id");
$ph_data = $ph_query->fetch_assoc();

$pi_query = $conn->query("SELECT * FROM pagibig_settings WHERE period_id = $period_id");
$pi_data = $pi_query->fetch_assoc();

$bir_query = $conn->query("SELECT * FROM bir_tax_settings WHERE period_id = $period_id");
$bir_data = $bir_query->fetch_assoc();

/**
 * Helper to display values neatly
 */
function formatStatValue($val, $prefix = '₱') {
    return $prefix . number_format((float)$val, 2);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Statutory Management | HR Manager</title>
  <link rel="stylesheet" href="../../css/statutorymgt.css?v=1.2">
  <link rel="stylesheet" href="../../css/notifications.css?v=1.1">
  <script src="https://unpkg.com/lucide@latest"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="icon" type="image/png" href="../../img/logo.png">
  <style>
    .stat-cards-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }
    .stat-mini-card {
      background: var(--surface);
      border-radius: 12px;
      padding: 20px;
      border: 1px solid var(--border-color);
      box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
    }
    .smc-header {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 16px;
      padding-bottom: 12px;
      border-bottom: 1px solid var(--border-color);
    }
    .smc-header h4 { font-size: 16px; margin: 0; color: var(--text-primary); }
    .smc-row {
      display: flex;
      justify-content: space-between;
      margin-bottom: 8px;
      font-size: 13px;
    }
    .smc-label { color: var(--text-secondary); }
    .smc-val { font-weight: 600; color: var(--text-primary); }
    
    .type-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 99px;
        font-size: 12px;
        font-weight: 600;
        background: rgba(59, 130, 246, 0.1);
        color: #3b82f6;
    }
  </style>
</head>
<body>

  <!-- Sidebar (Same as salarymgt.php) -->
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
          <span>HR Analytics</span>
        </a>
        <div class="nav-item-group">
          <button class="nav-item has-submenu" data-module="banking">
            <div class="nav-item-content">
              <i data-lucide="book-user"></i>
              <span>Core Human Capital</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-banking">
            <a href="Bankverification.php" class="submenu-item"><i data-lucide="shield-check"></i><span>Bank Verification</span></a>
            <a href="informationapproval.php" class="submenu-item"><i data-lucide="file-check"></i><span>Information Approval</span></a>
          </div>
        </div>
        <div class="nav-item-group active">
          <button class="nav-item has-submenu active" data-module="planning">
            <div class="nav-item-content">
              <i data-lucide="circle-pile"></i>
              <span>Compensation Planning</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
         <div class="submenu" id="submenu-planning">
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
      </div>
    </nav>

    <div class="sidebar-footer">
      <div class="user-profile">
        <div class="user-avatar"><img src="../../img/profile.png" alt="User"></div>
        <div class="user-info">
          <span class="user-name"><?php echo htmlspecialchars($_SESSION['username'] ?? 'Manager'); ?></span>
          <span class="user-role"><?php echo htmlspecialchars($_SESSION['user_role'] ?? 'HR Manager'); ?></span>
        </div>
        <button class="user-menu-btn" id="userMenuBtn"><i data-lucide="more-vertical"></i></button>
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
        <button class="mobile-menu-btn" id="mobileMenuBtn"><i data-lucide="menu"></i></button>
        <div class="header-title">
          <h1>Statutory Management</h1>
          <p>Review and approve government-mandated contribution rates and tax thresholds.</p>
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
        <!-- Current Settings Snapshot -->
        <h2 style="font-size: 18px; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
            <i data-lucide="history"></i> Current Active Settings
        </h2>
        <div class="stat-cards-grid">
            <!-- SSS -->
            <div class="stat-mini-card">
                <div class="smc-header">
                    <i data-lucide="shield-check" style="color:#2ca078"></i>
                    <h4>SSS Contribution</h4>
                </div>
                <div class="smc-row"><span class="smc-label">EE Share</span><span class="smc-val"><?php echo number_format($sss_data['employee_share_pct'] ?? 0, 1); ?>%</span></div>
                <div class="smc-row"><span class="smc-label">ER Share</span><span class="smc-val"><?php echo number_format($sss_data['employer_share_pct'] ?? 0, 1); ?>%</span></div>
                <div class="smc-row"><span class="smc-label">Max MSC</span><span class="smc-val"><?php echo formatStatValue($sss_data['max_msc_monthly'] ?? 0); ?></span></div>
                <div class="smc-row"><span class="smc-label">WISP Threshold</span><span class="smc-val"><?php echo formatStatValue($sss_data['wisp_threshold'] ?? 0); ?></span></div>
            </div>
            <!-- PhilHealth -->
            <div class="stat-mini-card">
                <div class="smc-header">
                    <i data-lucide="heart" style="color:#ef4444"></i>
                    <h4>PhilHealth Premium</h4>
                </div>
                <div class="smc-row"><span class="smc-label">EE Rate</span><span class="smc-val"><?php echo number_format($ph_data['employee_share_pct'] ?? 0, 2); ?>%</span></div>
                <div class="smc-row"><span class="smc-label">ER Rate</span><span class="smc-val"><?php echo number_format($ph_data['employer_share_pct'] ?? 0, 2); ?>%</span></div>
                <div class="smc-row"><span class="smc-label">Salary Ceiling</span><span class="smc-val"><?php echo formatStatValue($ph_data['salary_ceiling'] ?? 0); ?></span></div>
            </div>
            <!-- Pag-IBIG -->
            <div class="stat-mini-card">
                <div class="smc-header">
                    <i data-lucide="home" style="color:#ffc107"></i>
                    <h4>Pag-IBIG (HDMF)</h4>
                </div>
                <div class="smc-row"><span class="smc-label">EE Rate</span><span class="smc-val"><?php echo number_format($pi_data['employee_rate_pct'] ?? 0, 1); ?>%</span></div>
                <div class="smc-row"><span class="smc-label">Monthly Cap (EE)</span><span class="smc-val"><?php echo formatStatValue($pi_data['monthly_cap_ee'] ?? 0); ?></span></div>
                <div class="smc-row"><span class="smc-label">Monthly Cap (ER)</span><span class="smc-val"><?php echo formatStatValue($pi_data['monthly_cap_er'] ?? 0); ?></span></div>
            </div>
            <!-- BIR -->
            <div class="stat-mini-card">
                <div class="smc-header">
                    <i data-lucide="file-text" style="color:#3b82f6"></i>
                    <h4>BIR Tax (TRAIN)</h4>
                </div>
                <div class="smc-row"><span class="smc-label">Tax Exempt Limit</span><span class="smc-val"><?php echo formatStatValue($bir_data['tax_exempt_limit'] ?? 0); ?></span></div>
                <div class="smc-row"><span class="smc-label">De Minimis Cap</span><span class="smc-val"><?php echo formatStatValue($bir_data['de_minimis_cap'] ?? 0); ?></span></div>
                <div class="smc-row"><span class="smc-label">13th Month Cap</span><span class="smc-val"><?php echo formatStatValue($bir_data['thirteenth_month_cap'] ?? 0); ?></span></div>
            </div>
        </div>

        <!-- Endorsed Proposals -->
        <div class="content-card">
            <div class="card-header">
                <div class="card-header-left">
                    <h3 class="card-title">Endorsed Statutory Proposals</h3>
                    <p class="card-subtitle">Changes reviewed by Supervisors awaiting your final approval.</p>
                </div>
            </div>
            <div class="card-body">
                <div class="data-table">
                    <table id="endorsedStatutoryTable">
                        <thead>
                            <tr>
                                <th>Proposed By</th>
                                <th>Category</th>
                                <th>Reason</th>
                                <th>Date Requested</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="endorsedStatutoryBody">
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">
                                        <i data-lucide="loader-2" class="spin"></i>
                                        <p>Fetching proposals…</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Review Statutory Modal -->
    <div class="modal-overlay hidden" id="statutoryActionModal">
      <div class="rem-dialog" style="max-width: 800px; width: 90%;">
        <div class="rem-header">
          <div class="rem-header-left">
            <div class="rem-icon-box" style="background:rgba(59, 130, 246, 0.1); color:#3b82f6;">
              <i data-lucide="landmark"></i>
            </div>
            <div class="rem-title-group">
              <h3 class="rem-title">Review Statutory Proposal</h3>
              <p class="rem-subtitle">Final review of government-mandated adjustments.</p>
            </div>
          </div>
          <button type="button" class="rem-close" id="btnCloseStatutoryModal"><i data-lucide="x"></i></button>
        </div>

        <div class="rem-body">
            <div class="rem-section">
                <div class="rem-section-hdr rem-shdr-blue"><i data-lucide="info"></i> Reason for Proposal</div>
                <div id="statutoryReasonText" style="background:var(--surface-hover); padding:16px; border-radius:8px; color:var(--text-secondary); white-space:pre-wrap; font-size:14px; margin-bottom: 24px;"></div>

                <div class="rem-section-hdr rem-shdr-blue"><i data-lucide="list"></i> Proposed Adjustments</div>
                <div class="data-table">
                  <table style="margin: 0;">
                    <thead>
                      <tr>
                        <th>Category</th>
                        <th>Setting / Field</th>
                        <th>Old Value</th>
                        <th>Proposed Value</th>
                      </tr>
                    </thead>
                    <tbody id="statutoryDetailsBody">
                        <tr><td colspan="4" style="text-align:center;">Loading details...</td></tr>
                    </tbody>
                  </table>
                </div>

                <div class="rem-section-hdr rem-shdr-green" style="margin-top:24px;"><i data-lucide="file-check"></i> Government Proof</div>
                <div id="proofContainer" style="padding: 16px; background: rgba(44, 160, 120, .05); border: 1px dashed var(--brand-green); border-radius: 10px; display: flex; align-items: center; justify-content: space-between;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <i data-lucide="file-text" style="color: var(--brand-green);"></i>
                        <span style="font-size: 14px; font-weight: 500;">Supporting Documentation</span>
                    </div>
                    <a href="#" id="statutoryProofLink" target="_blank" class="btn-review" style="background: var(--brand-green); color: white; border: none; padding: 6px 12px; font-size: 12px; border-radius: 6px; text-decoration: none;">
                        <i data-lucide="external-link" style="width: 14px; height: 14px;"></i> View Document
                    </a>
                </div>
            </div>
        </div>

        <div class="rem-footer">
          <div class="rem-footer-hint">
            <i data-lucide="shield-check"></i> Approved requests will be forwarded to the Finance department for final application.
          </div>
          <div style="display:flex; gap:12px;">
            <button type="button" class="rem-btn-send rem-btn-secondary" id="btnRejectStatutory">
                <i data-lucide="x-circle" style="color:var(--brand-red);"></i> Reject
            </button>
            <button type="button" class="rem-btn-send rem-btn-blue" id="btnApproveStatutory">
                <i data-lucide="check-circle"></i> Approve & Send to Finance
            </button>
          </div>
        </div>
      </div>
    </div>
  </main>
  <script src="../../js/notifications.js?v=1.1"></script>
  <script src="../../js/statutorymgt.js?v=<?php echo time(); ?>"></script>
</body>
</html>
