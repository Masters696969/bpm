<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../../login.php");
    exit();
}

require_once '../../config/config.php';

// Fetch Active Compensation Period (Assume ID 1 for now)
$period_id = 1;

// Fetch Statutory Settings
$sss_query = $conn->query("SELECT * FROM sss_settings WHERE period_id = $period_id");
$sss_data = $sss_query->fetch_assoc();

$ph_query = $conn->query("SELECT * FROM philhealth_settings WHERE period_id = $period_id");
$ph_data = $ph_query->fetch_assoc();

$pi_query = $conn->query("SELECT * FROM pagibig_settings WHERE period_id = $period_id");
$pi_data = $pi_query->fetch_assoc();

$bir_query = $conn->query("SELECT * FROM bir_tax_settings WHERE period_id = $period_id");
$bir_data = $bir_query->fetch_assoc();

$module = 'planning';
$page = 'statutory';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Statutory Contributions</title>
  <link rel="stylesheet" href="../../css/compensationdashboard.css?v=1.4">
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

        <div class="nav-item-group <?php echo ($module === 'hr') ? 'active' : ''; ?>">
          <button class="nav-item has-submenu" data-module="hr">
            <div class="nav-item-content">
              <i data-lucide="book-user"></i>
              <span>Core Human Capital</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-hr">
            <a href="../admin/dispatch.php" class="submenu-item">
              <i data-lucide="send"></i>
              <span>Master Data Dispatch</span>
            </a>
            <a href="" class="submenu-item">
              <i data-lucide="user-plus"></i>
              <span>New Hired Onboard Request</span>
            </a>
            <a href="../admin/employeemaster.php" class="submenu-item <?php echo ($page === 'employeemaster') ? 'active' : ''; ?>">
              <i data-lucide="file-user"></i>
              <span>Employee Master Files</span>
            </a>
            <a href="bankform.php" class="submenu-item <?php echo ($page === 'bankform') ? 'active' : ''; ?>">
              <i data-lucide="file-text"></i>
              <span>Bank Form Management</span>
            </a>
            <a href="" class="submenu-item">
              <i data-lucide="user-cog"></i>
              <span>Security Settings</span>
            </a>
            <a href="../admin/auditlogs.php" class="submenu-item <?php echo ($page === 'auditlogs') ? 'active' : ''; ?>">
              <i data-lucide="book-user"></i>
              <span>Audit Logs</span>
            </a>
          </div>
          <div class="nav-item-group <?php echo ($module === 'planning') ? 'active' : ''; ?>">
          <button class="nav-item has-submenu" data-module="planning">
            <div class="nav-item-content">
              <i data-lucide="circle-pile"></i>
              <span>Compensation Planning</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-planning">
            <a href="intake.php" class="submenu-item <?php echo ($page === 'intake') ? 'active' : ''; ?>">
              <i data-lucide="layout-dashboard"></i>
              <span>Master Data Intake</span>
            </a>
            <a href="salary.php" class="submenu-item <?php echo ($page === 'salarymgt') ? 'active' : ''; ?>">
              <i data-lucide="banknote"></i>
              <span>Salary & Scales Management</span>
            </a>
            <a href="statutory.php" class="submenu-item <?php echo ($page === 'statutory') ? 'active' : ''; ?>">
              <i data-lucide="scale"></i>
              <span>Statutory Contributions</span>
            </a>
            <a href="matrix.php" class="submenu-item <?php echo ($page === 'matrix') ? 'active' : ''; ?>">
              <i data-lucide="percent"></i>
              <span>Merit Matrix Structure</span>
            </a>
            <a href="allowance.php" class="submenu-item <?php echo ($page === 'allowance') ? 'active' : ''; ?>">
              <i data-lucide="gift"></i>
              <span>Allowance Structure</span>
            </a>
            <a href="cycle.php" class="submenu-item <?php echo ($page === 'cycle') ? 'active' : ''; ?>">
              <i data-lucide="notebook-pen"></i>
              <span>Compensation Structure Management</span>
            </a>
          </div>
        </div>
        <div class="nav-item-group <?php echo ($module === 'payroll') ? 'active' : ''; ?>">
          <button class="nav-item has-submenu" data-module="payroll">
            <div class="nav-item-content">
              <i data-lucide="banknote-arrow-down"></i>
              <span>Payroll</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-payroll">
            <a href="#" class="submenu-item">
              <i data-lucide="file-plus"></i>
              <span>Applications</span>
            </a>
            <a href="#" class="submenu-item">
              <i data-lucide="check-circle"></i>
              <span>Approvals</span>
            </a>
            <a href="#" class="submenu-item">
              <i data-lucide="calendar-clock"></i>
              <span>Disbursements</span>
            </a>
            <a href="#" class="submenu-item">
              <i data-lucide="coins"></i>
              <span>Collections</span>
            </a>
          </div>
        </div>
      </div>
      <div class="nav-section">
          <span class="nav-section-title">FINANCE</span>
          <div class="nav-item-group">
            <button class="nav-item has-submenu" data-module="budget">
              <div class="nav-item-content">
                <i data-lucide="hand-coins"></i>
                <span>Budget Management</span>
              </div>
              <i data-lucide="chevron-down" class="submenu-icon"></i>
            </button>
            <div class="submenu" id="submenu-budget">
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
    </div>
    </nav>
    
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
      <div class="section-header" style="margin-bottom: 24px;">
        <div class="sh-info">
          <h3>Statutory Compliance Settings</h3>
          <p>Configure government-mandated contribution rates and tax thresholds for the current period.</p>
        </div>
        <div class="comp-panel-actions" style="display: flex; gap: 12px;">
          <button class="btn btn-outline" id="btnTrackStatutoryStatus">
            <i data-lucide="eye"></i>
            <span>Track Status</span>
          </button>
          <button class="btn btn-primary" id="btnProposeStatutoryChange">
            <i data-lucide="git-pull-request"></i>
            <span>Propose Change</span>
          </button>
        </div>
      </div>
      <div class="comp-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px;">
        <!-- SSS Configuration -->
        <div class="stat-group-card" style="background: var(--surface); border: 1px solid var(--border-color); border-radius: 12px; padding: 20px;">
          <div class="sg-header" style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 16px;">
            <i data-lucide="shield-check" style="color:#2ca078"></i>
            <h4 style="margin: 0; font-size: 16px; color: var(--text-primary);">SSS Contribution</h4>
          </div>
          <div class="sg-body">
            <p class="sg-desc" style="color: var(--text-secondary); font-size: 13px; margin-bottom: 16px;">Manage Social Security rates and WISP mandatory provident fund thresholds.</p>
            <div class="editable-form" style="display: flex; flex-direction: column; gap: 12px;">
              <div class="form-group-inline" style="display: flex; justify-content: space-between; align-items: center;">
                <label style="font-size: 13px; color: var(--text-secondary);">Employee Share (%)</label>
                <input type="number" step="0.1" value="<?php echo number_format($sss_data['employee_share_pct'] ?? 5.0, 1); ?>" class="stat-input" style="width: 120px; padding: 6px 10px; border: 1px solid var(--border-color); border-radius: 6px; text-align: right; background: var(--background); color: var(--text-primary);">
              </div>
              <div class="form-group-inline" style="display: flex; justify-content: space-between; align-items: center;">
                <label style="font-size: 13px; color: var(--text-secondary);">Employer Share (%)</label>
                <input type="number" step="0.1" value="<?php echo number_format($sss_data['employer_share_pct'] ?? 10.0, 1); ?>" class="stat-input" style="width: 120px; padding: 6px 10px; border: 1px solid var(--border-color); border-radius: 6px; text-align: right; background: var(--background); color: var(--text-primary);">
              </div>
              <div class="form-group-inline" style="display: flex; justify-content: space-between; align-items: center;">
                <label style="font-size: 13px; color: var(--text-secondary);">Max MSC (Monthly)</label>
                <div class="inline-input-symbol" style="position: relative; width: 120px;">
                  <span style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--text-tertiary); font-size: 13px;">&#8369;</span>
                  <input type="number" value="<?php echo (int)($sss_data['max_msc_monthly'] ?? 30000); ?>" class="stat-input" style="width: 100%; padding: 6px 10px 6px 24px; border: 1px solid var(--border-color); border-radius: 6px; text-align: right; background: var(--background); color: var(--text-primary);">
                </div>
              </div>
              <div class="form-group-inline" style="display: flex; justify-content: space-between; align-items: center;">
                <label style="font-size: 13px; color: var(--text-secondary);">WISP Threshold</label>
                <div class="inline-input-symbol" style="position: relative; width: 120px;">
                  <span style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--text-tertiary); font-size: 13px;">&#8369;</span>
                  <input type="number" value="<?php echo (int)($sss_data['wisp_threshold'] ?? 20000); ?>" class="stat-input" style="width: 100%; padding: 6px 10px 6px 24px; border: 1px solid var(--border-color); border-radius: 6px; text-align: right; background: var(--background); color: var(--text-primary);">
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- PhilHealth Configuration -->
        <div class="stat-group-card" style="background: var(--surface); border: 1px solid var(--border-color); border-radius: 12px; padding: 20px;">
          <div class="sg-header" style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 16px;">
            <i data-lucide="heart" style="color:#ef4444"></i>
            <h4 style="margin: 0; font-size: 16px; color: var(--text-primary);">PhilHealth Premium</h4>
          </div>
          <div class="sg-body">
            <p class="sg-desc" style="color: var(--text-secondary); font-size: 13px; margin-bottom: 16px;">Current premium rate is 5.0% split equally between EE and ER.</p>
            <div class="editable-form" style="display: flex; flex-direction: column; gap: 12px;">
              <div class="form-group-inline" style="display: flex; justify-content: space-between; align-items: center;">
                <label style="font-size: 13px; color: var(--text-secondary);">Employee Share (%)</label>
                <input type="number" step="0.01" value="<?php echo number_format($ph_data['employee_share_pct'] ?? 2.50, 2); ?>" class="stat-input" style="width: 120px; padding: 6px 10px; border: 1px solid var(--border-color); border-radius: 6px; text-align: right; background: var(--background); color: var(--text-primary);">
              </div>
              <div class="form-group-inline" style="display: flex; justify-content: space-between; align-items: center;">
                <label style="font-size: 13px; color: var(--text-secondary);">Employer Share (%)</label>
                <input type="number" step="0.01" value="<?php echo number_format($ph_data['employer_share_pct'] ?? 2.50, 2); ?>" class="stat-input" style="width: 120px; padding: 6px 10px; border: 1px solid var(--border-color); border-radius: 6px; text-align: right; background: var(--background); color: var(--text-primary);">
              </div>
              <div class="form-group-inline" style="display: flex; justify-content: space-between; align-items: center;">
                <label style="font-size: 13px; color: var(--text-secondary);">Salary Ceiling</label>
                <div class="inline-input-symbol" style="position: relative; width: 120px;">
                  <span style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--text-tertiary); font-size: 13px;">&#8369;</span>
                  <input type="number" value="<?php echo (int)($ph_data['salary_ceiling'] ?? 100000); ?>" class="stat-input" style="width: 100%; padding: 6px 10px 6px 24px; border: 1px solid var(--border-color); border-radius: 6px; text-align: right; background: var(--background); color: var(--text-primary);">
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Pag-IBIG Configuration -->
        <div class="stat-group-card" style="background: var(--surface); border: 1px solid var(--border-color); border-radius: 12px; padding: 20px;">
          <div class="sg-header" style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 16px;">
            <i data-lucide="home" style="color:#ffc107"></i>
            <h4 style="margin: 0; font-size: 16px; color: var(--text-primary);">Pag-IBIG (HDMF)</h4>
          </div>
          <div class="sg-body">
            <p class="sg-desc" style="color: var(--text-secondary); font-size: 13px; margin-bottom: 16px;">Contribution based on percentage or fixed amount caps.</p>
            <div class="editable-form" style="display: flex; flex-direction: column; gap: 12px;">
              <div class="form-group-inline" style="display: flex; justify-content: space-between; align-items: center;">
                <label style="font-size: 13px; color: var(--text-secondary);">Employee Rate (%)</label>
                <input type="number" step="0.1" value="<?php echo number_format($pi_data['employee_rate_pct'] ?? 2.0, 1); ?>" class="stat-input" style="width: 120px; padding: 6px 10px; border: 1px solid var(--border-color); border-radius: 6px; text-align: right; background: var(--background); color: var(--text-primary);">
              </div>
              <div class="form-group-inline" style="display: flex; justify-content: space-between; align-items: center;">
                <label style="font-size: 13px; color: var(--text-secondary);">Monthly Cap (EE)</label>
                <div class="inline-input-symbol" style="position: relative; width: 120px;">
                  <span style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--text-tertiary); font-size: 13px;">&#8369;</span>
                  <input type="number" value="<?php echo (int)($pi_data['monthly_cap_ee'] ?? 200); ?>" class="stat-input" style="width: 100%; padding: 6px 10px 6px 24px; border: 1px solid var(--border-color); border-radius: 6px; text-align: right; background: var(--background); color: var(--text-primary);">
                </div>
              </div>
              <div class="form-group-inline" style="display: flex; justify-content: space-between; align-items: center;">
                <label style="font-size: 13px; color: var(--text-secondary);">Monthly Cap (ER)</label>
                <div class="inline-input-symbol" style="position: relative; width: 120px;">
                  <span style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--text-tertiary); font-size: 13px;">&#8369;</span>
                  <input type="number" value="<?php echo (int)($pi_data['monthly_cap_er'] ?? 200); ?>" class="stat-input" style="width: 100%; padding: 6px 10px 6px 24px; border: 1px solid var(--border-color); border-radius: 6px; text-align: right; background: var(--background); color: var(--text-primary);">
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- BIR Tax Configuration -->
        <div class="stat-group-card" style="background: var(--surface); border: 1px solid var(--border-color); border-radius: 12px; padding: 20px;">
          <div class="sg-header" style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 16px;">
            <i data-lucide="file-text" style="color:#3b82f6"></i>
            <h4 style="margin: 0; font-size: 16px; color: var(--text-primary);">BIR Tax (TRAIN)</h4>
          </div>
          <div class="sg-body">
            <p class="sg-desc" style="color: var(--text-secondary); font-size: 13px; margin-bottom: 16px;">Withholding tax settings and tax-exempt benefit caps.</p>
            <div class="editable-form" style="display: flex; flex-direction: column; gap: 12px;">
              <div class="form-group-inline" style="display: flex; justify-content: space-between; align-items: center;">
                <label style="font-size: 13px; color: var(--text-secondary);">Tax Exempt Limit</label>
                <div class="inline-input-symbol" style="position: relative; width: 120px;">
                  <span style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--text-tertiary); font-size: 13px;">&#8369;</span>
                  <input type="number" value="<?php echo (int)($bir_data['tax_exempt_limit'] ?? 250000); ?>" class="stat-input" style="width: 100%; padding: 6px 10px 6px 24px; border: 1px solid var(--border-color); border-radius: 6px; text-align: right; background: var(--background); color: var(--text-primary);">
                </div>
              </div>
              <div class="form-group-inline" style="display: flex; justify-content: space-between; align-items: center;">
                <label style="font-size: 13px; color: var(--text-secondary);">De Minimis Cap</label>
                <div class="inline-input-symbol" style="position: relative; width: 120px;">
                  <span style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--text-tertiary); font-size: 13px;">&#8369;</span>
                  <input type="number" value="<?php echo (int)($bir_data['de_minimis_cap'] ?? 90000); ?>" class="stat-input" style="width: 100%; padding: 6px 10px 6px 24px; border: 1px solid var(--border-color); border-radius: 6px; text-align: right; background: var(--background); color: var(--text-primary);">
                </div>
              </div>
              <div class="form-group-inline" style="display: flex; justify-content: space-between; align-items: center;">
                <label style="font-size: 13px; color: var(--text-secondary);">13th Month Cap</label>
                <div class="inline-input-symbol" style="position: relative; width: 120px;">
                  <span style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--text-tertiary); font-size: 13px;">&#8369;</span>
                  <input type="number" value="<?php echo (int)($bir_data['thirteenth_month_cap'] ?? 90000); ?>" class="stat-input" style="width: 100%; padding: 6px 10px 6px 24px; border: 1px solid var(--border-color); border-radius: 6px; text-align: right; background: var(--background); color: var(--text-primary);">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
      <!-- Propose Statutory Change Modal -->
      <div id="proposeStatutoryModal" class="modal" aria-hidden="true" style="display: none;">
        <div class="modal-dialog" style="max-width: 650px;">
          <form id="statutoryProposalForm" enctype="multipart/form-data">
            <div class="comp-modal-hero">
              <div class="comp-modal-hero-inner" style="padding: 12px 16px;">
                <div class="comp-modal-hero-icon" style="background: rgba(59, 130, 216, 0.2); width: 36px; height: 36px;">
                  <i data-lucide="landmark" style="width: 18px; height: 18px;"></i>
                </div>
                <div class="comp-modal-hero-text">
                  <h3 style="font-size: 16px;">Propose Statutory Adjustments</h3>
                  <p style="font-size: 11px;">Update government contribution rates and tax limits.</p>
                </div>
                <button type="button" class="rp-close-modal" id="closeStatutoryModalBtn" title="Close">&times;</button>
              </div>
            </div>

            <div class="modal-body modal-form-premium" style="padding: 15px; max-height: 70vh; overflow-y: auto;">
              <div class="form-grid-stat-compact" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px;">
                
                <!-- SSS Column -->
                <div class="stat-column-box">
                  <div class="box-group-header" style="margin-bottom: 10px; padding-bottom: 5px;">
                    <i data-lucide="shield-check" style="color:#2ca078; width: 14px; height: 14px;"></i>
                    <span style="font-size: 12px;">SSS Contribution</span>
                  </div>
                  
                  <div class="form-group-premium-box mini">
                    <label>Max MSC</label>
                    <p class="current-mini">Current: &#8369;<?php echo number_format($sss_data['max_msc_monthly'] ?? 30000, 0); ?></p>
                    <div class="input-with-symbol">
                      <span>&#8369;</span>
                      <input type="number" name="proposed_sss_msc" class="input-premium no-icon" value="<?php echo (int)($sss_data['max_msc_monthly'] ?? 30000); ?>" required style="padding: 5px 8px; font-size: 12px;">
                    </div>
                  </div>

                  <div class="form-group-premium-box mini">
                    <label>EE Share (%)</label>
                    <p class="current-mini">Current: <?php echo number_format($sss_data['employee_share_pct'] ?? 5.0, 1); ?>%</p>
                    <input type="number" step="0.1" name="proposed_sss_ee_pct" class="input-premium no-icon" value="<?php echo number_format($sss_data['employee_share_pct'] ?? 5.0, 1); ?>" required style="padding: 5px 8px; font-size: 12px;">
                  </div>

                  <div class="form-group-premium-box mini">
                    <label>ER Share (%)</label>
                    <p class="current-mini">Current: <?php echo number_format($sss_data['employer_share_pct'] ?? 10.0, 1); ?>%</p>
                    <input type="number" step="0.1" name="proposed_sss_er_pct" class="input-premium no-icon" value="<?php echo number_format($sss_data['employer_share_pct'] ?? 10.0, 1); ?>" required style="padding: 5px 8px; font-size: 12px;">
                  </div>

                  <div class="form-group-premium-box mini">
                    <label>WISP Threshold</label>
                    <p class="current-mini">Current: &#8369;<?php echo number_format($sss_data['wisp_threshold'] ?? 20000, 0); ?></p>
                    <div class="input-with-symbol">
                      <span>&#8369;</span>
                      <input type="number" name="proposed_sss_wisp" class="input-premium no-icon" value="<?php echo (int)($sss_data['wisp_threshold'] ?? 20000); ?>" required style="padding: 5px 8px; font-size: 12px;">
                    </div>
                  </div>
                </div>

                <!-- PhilHealth Column -->
                <div class="stat-column-box">
                  <div class="box-group-header" style="margin-bottom: 10px; padding-bottom: 5px;">
                    <i data-lucide="heart" style="color:#ef4444; width: 14px; height: 14px;"></i>
                    <span style="font-size: 12px;">PhilHealth Premium</span>
                  </div>

                  <div class="form-group-premium-box mini">
                    <label>Salary Ceiling</label>
                    <p class="current-mini">Current: &#8369;<?php echo number_format($ph_data['salary_ceiling'] ?? 100000, 0); ?></p>
                    <div class="input-with-symbol">
                      <span>&#8369;</span>
                      <input type="number" name="proposed_ph_ceiling" class="input-premium no-icon" value="<?php echo (int)($ph_data['salary_ceiling'] ?? 100000); ?>" required style="padding: 5px 8px; font-size: 12px;">
                    </div>
                  </div>

                  <div class="form-group-premium-box mini">
                    <label>EE Share (%)</label>
                    <p class="current-mini">Current: <?php echo number_format($ph_data['employee_share_pct'] ?? 2.50, 2); ?>%</p>
                    <input type="number" step="0.01" name="proposed_ph_ee_pct" class="input-premium no-icon" value="<?php echo number_format($ph_data['employee_share_pct'] ?? 2.50, 2); ?>" required style="padding: 5px 8px; font-size: 12px;">
                  </div>

                  <div class="form-group-premium-box mini">
                    <label>ER Share (%)</label>
                    <p class="current-mini">Current: <?php echo number_format($ph_data['employer_share_pct'] ?? 2.50, 2); ?>%</p>
                    <input type="number" step="0.01" name="proposed_ph_er_pct" class="input-premium no-icon" value="<?php echo number_format($ph_data['employer_share_pct'] ?? 2.50, 2); ?>" required style="padding: 5px 8px; font-size: 12px;">
                  </div>
                </div>

                <!-- Pag-IBIG Column -->
                <div class="stat-column-box">
                  <div class="box-group-header" style="margin-bottom: 10px; padding-bottom: 5px;">
                    <i data-lucide="home" style="color:#ffc107; width: 14px; height: 14px;"></i>
                    <span style="font-size: 12px;">Pag-IBIG (HDMF)</span>
                  </div>

                  <div class="form-group-premium-box mini">
                    <label>EE Monthly Cap</label>
                    <p class="current-mini">Current: &#8369;<?php echo number_format($pi_data['monthly_cap_ee'] ?? 200, 0); ?></p>
                    <div class="input-with-symbol">
                      <span>&#8369;</span>
                      <input type="number" name="proposed_pi_cap_ee" class="input-premium no-icon" value="<?php echo (int)($pi_data['monthly_cap_ee'] ?? 200); ?>" required style="padding: 5px 8px; font-size: 12px;">
                    </div>
                  </div>

                  <div class="form-group-premium-box mini">
                    <label>ER Monthly Cap</label>
                    <p class="current-mini">Current: &#8369;<?php echo number_format($pi_data['monthly_cap_er'] ?? 200, 0); ?></p>
                    <div class="input-with-symbol">
                      <span>&#8369;</span>
                      <input type="number" name="proposed_pi_cap_er" class="input-premium no-icon" value="<?php echo (int)($pi_data['monthly_cap_er'] ?? 200); ?>" required style="padding: 5px 8px; font-size: 12px;">
                    </div>
                  </div>

                  <div class="form-group-premium-box mini">
                    <label>EE Rate (%)</label>
                    <p class="current-mini">Current: <?php echo number_format($pi_data['employee_rate_pct'] ?? 2.0, 1); ?>%</p>
                    <input type="number" step="0.1" name="proposed_pi_ee_rate_pct" class="input-premium no-icon" value="<?php echo number_format($pi_data['employee_rate_pct'] ?? 2.0, 1); ?>" required style="padding: 5px 8px; font-size: 12px;">
                  </div>
                </div>

                <!-- BIR Column -->
                <div class="stat-column-box">
                  <div class="box-group-header" style="margin-bottom: 10px; padding-bottom: 5px;">
                    <i data-lucide="file-text" style="color:#3b82f6; width: 14px; height: 14px;"></i>
                    <span style="font-size: 12px;">BIR Tax (TRAIN)</span>
                  </div>

                  <div class="form-group-premium-box mini">
                    <label>Tax Exempt Limit</label>
                    <p class="current-mini">Current: &#8369;<?php echo number_format($bir_data['tax_exempt_limit'] ?? 250000, 0); ?></p>
                    <div class="input-with-symbol">
                      <span>&#8369;</span>
                      <input type="number" name="proposed_bir_limit" class="input-premium no-icon" value="<?php echo (int)($bir_data['tax_exempt_limit'] ?? 250000); ?>" required style="padding: 5px 8px; font-size: 12px;">
                    </div>
                  </div>

                  <div class="form-group-premium-box mini">
                    <label>De Minimis Cap</label>
                    <p class="current-mini">Current: &#8369;<?php echo number_format($bir_data['de_minimis_cap'] ?? 90000, 0); ?></p>
                    <div class="input-with-symbol">
                      <span>&#8369;</span>
                      <input type="number" name="proposed_bir_de_minimis" class="input-premium no-icon" value="<?php echo (int)($bir_data['de_minimis_cap'] ?? 90000); ?>" required style="padding: 5px 8px; font-size: 12px;">
                    </div>
                  </div>

                  <div class="form-group-premium-box mini">
                    <label>13th Month Cap</label>
                    <p class="current-mini">Current: &#8369;<?php echo number_format($bir_data['thirteenth_month_cap'] ?? 90000, 0); ?></p>
                    <div class="input-with-symbol">
                      <span>&#8369;</span>
                      <input type="number" name="proposed_bir_13th_month" class="input-premium no-icon" value="<?php echo (int)($bir_data['thirteenth_month_cap'] ?? 90000); ?>" required style="padding: 5px 8px; font-size: 12px;">
                    </div>
                  </div>
                </div>
              </div>

              <div class="form-group" style="margin-top: 15px;">
                <label style="font-size: 12px;">Reason for Proposal <span class="required">*</span></label>
                <textarea name="proposal_reason" rows="2" class="input-premium no-icon" placeholder="Rationale..." required style="padding: 8px; font-size: 12px;"></textarea>
              </div>

              <div class="form-group" style="margin-top: 10px;">
                <label style="font-size: 12px;">Proof (PDF/Image) <span class="required">*</span></label>
                <div class="file-upload-premium">
                  <input type="file" name="gov_proof" id="govProofFile" accept=".pdf,image/*" required style="display: none;">
                  <button type="button" class="btn-file-select" onclick="document.getElementById('govProofFile').click()" style="width: 100%; padding: 8px; border: 1px dashed var(--border-color); border-radius: 6px; background: var(--surface-hover); color: var(--text-secondary); display: flex; align-items: center; justify-content: center; gap: 6px; cursor: pointer; transition: all 0.2s; font-size: 12px;">
                    <i data-lucide="upload-cloud" style="width: 16px; height: 16px;"></i>
                    <span id="fileNameLabel">Upload proof...</span>
                  </button>
                </div>
              </div>
            </div>

            <style>
              .form-group-premium-box.mini {
                padding: 5px 7px;
                border: 1px solid var(--border-color);
                border-radius: 5px;
                margin-bottom: 6px;
                background: var(--surface-hover);
              }
              .form-group-premium-box.mini label {
                display: block;
                font-size: 9px;
                font-weight: 700;
                color: var(--text-secondary);
                text-transform: uppercase;
                margin-bottom: 0px;
              }
              .current-mini {
                font-size: 8px;
                color: var(--text-secondary);
                margin-bottom: 3px;
                font-weight: 500;
              }
              .box-group-header {
                display: flex;
                align-items: center;
                gap: 5px;
                margin-bottom: 8px;
                padding-bottom: 5px;
                border-bottom: 1px solid var(--border-color);
              }
              .box-group-header span {
                font-weight: 700;
                font-size: 12px;
                color: var(--text-primary);
              }
              #proposeStatutoryModal .input-premium {
                height: 28px;
                padding: 4px 8px;
                font-size: 12px;
              }
            </style>

            <div class="modal-footer-premium">
              <button type="button" id="cancelStatutoryBtn" class="btn-cancel-premium">Cancel</button>
              <button type="submit" class="btn-comp-submit">
                Submit Proposal
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Statutory Tracking Modal -->
      <div id="statutoryTrackingModal" class="modal" aria-hidden="true" style="display: none;">
        <div class="modal-dialog" style="max-width: 800px;">
          <div class="comp-modal-hero">
            <div class="comp-modal-hero-inner">
              <div class="comp-modal-hero-icon" style="background: rgba(16, 185, 129, 0.2);">
                <i data-lucide="eye"></i>
              </div>
              <div class="comp-modal-hero-text">
                <h3 id="statutoryTrackerTitle">Statutory Proposal Tracking</h3>
                <p>Monitor the status of your submitted statutory adjustments.</p>
              </div>
              <button type="button" class="rp-close-modal" id="closeStatutoryTrackModalBtn" title="Close">&times;</button>
            </div>
          </div>

          <div class="modal-body modal-form-premium" style="min-height: 400px; max-height: 70vh; overflow-y: auto;">
            <!-- Stage A: List of Batches -->
            <div id="statutoryStageAList">
                <div class="data-table-premium">
                    <table style="width: 100%; border-collapse: separate; border-spacing: 0 8px;">
                        <thead>
                            <tr style="background: transparent;">
                                <th style="padding: 12px; font-size: 11px; text-transform: uppercase; color: var(--text-tertiary); letter-spacing: 0.05em;">Reference</th>
                                <th style="padding: 12px; font-size: 11px; text-transform: uppercase; color: var(--text-tertiary); letter-spacing: 0.05em;">Type</th>
                                <th style="padding: 12px; font-size: 11px; text-transform: uppercase; color: var(--text-tertiary); letter-spacing: 0.05em;">Date</th>
                                <th style="padding: 12px; font-size: 11px; text-transform: uppercase; color: var(--text-tertiary); letter-spacing: 0.05em;">Adjustments</th>
                                <th style="padding: 12px; font-size: 11px; text-transform: uppercase; color: var(--text-tertiary); letter-spacing: 0.05em;">Status</th>
                                <th style="padding: 12px; font-size: 11px; text-transform: uppercase; color: var(--text-tertiary); letter-spacing: 0.05em; text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="statutoryTrackingBody">
                            <tr><td colspan="6" style="text-align:center; padding:60px; color: var(--text-tertiary);">
                                <i data-lucide="inbox" style="width: 48px; height: 48px; opacity: 0.2; margin-bottom: 12px;"></i>
                                <p>No tracking history available</p>
                            </td></tr>
                        </tbody>
                    </table>
                </div>
                <!-- Statutory Pagination -->
                <div class="pagination-premium" style="display: flex; justify-content: space-between; align-items: center; margin-top: 16px; padding: 0 8px;">
                    <div style="font-size: 13px; color: var(--text-secondary);">Showing <span id="statPageRange">0</span> of <span id="statTotalEntries">0</span></div>
                    <div style="display: flex; gap: 8px;">
                        <button id="prevStatPage" class="btn-pagination" disabled><i data-lucide="chevron-left"></i></button>
                        <button id="nextStatPage" class="btn-pagination" disabled><i data-lucide="chevron-right"></i></button>
                    </div>
                </div>
            </div>

            <!-- Stage B: Stepper & Details -->
            <div id="statutoryStageBDetails" style="display: none;">
                <button type="button" class="btn btn-secondary" id="btnBackToStatutoryList" style="margin-bottom: 24px; padding: 6px 12px; font-size: 13px;">
                    <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i> Back to List
                </button>

                <div class="stepper-hero-premium" style="background: linear-gradient(135deg, rgba(44, 160, 120, 0.05) 0%, rgba(59, 130, 246, 0.05) 100%); border-radius: 16px; padding: 32px 20px; border: 1px solid var(--border-color); margin-bottom: 32px; box-shadow: var(--shadow-sm);">
                    <div class="stepper-header" style="text-align: center; margin-bottom: 32px;">
                        <h4 id="statutoryStepperBatchTitle" style="font-size: 20px; font-weight: 800; color: var(--text-primary); margin-bottom: 8px; letter-spacing: -0.02em;">Batch: ----</h4>
                        <div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                            <i data-lucide="info" style="width: 14px; height: 14px; color: var(--brand-green);"></i>
                            <p id="statutoryStepperBatchReason" style="color: var(--text-secondary); font-size: 13px; font-style: italic;"></p>
                        </div>
                    </div>

                    <div class="stepper-container" style="max-width: 650px;">
                        <div class="stepper-line">
                            <div class="stepper-line-fill" id="statutoryStepperLineFill" style="width: 0%;"></div>
                        </div>

                        <div class="stepper-step" id="statStep1">
                            <div class="step-circle"><i data-lucide="check"></i></div>
                            <div class="step-label">Step 1</div>
                            <div class="step-title" style="font-size: 13px;">Submitted</div>
                            <div class="step-desc" id="statStep1Desc">Analyst</div>
                        </div>

                        <div class="stepper-step" id="statStep2">
                            <div class="step-circle"><i data-lucide="search"></i></div>
                            <div class="step-label">Step 2</div>
                            <div class="step-title" style="font-size: 13px;">Under Review</div>
                            <div class="step-desc" id="statStep2Desc">Pending</div>
                        </div>

                        <div class="stepper-step" id="statStep3">
                            <div class="step-circle"><i data-lucide="user-check"></i></div>
                            <div class="step-label">Step 3</div>
                            <div class="step-title" style="font-size: 13px;">Endorsed</div>
                            <div class="step-desc" id="statStep3Desc">Supervisor</div>
                        </div>

                        <div class="stepper-step" id="statStep4">
                            <div class="step-circle"><i data-lucide="flag"></i></div>
                            <div class="step-label">Step 4</div>
                            <div class="step-title" id="statStep4Title" style="font-size: 13px;">Finalized</div>
                            <div class="step-desc" id="statStep4Desc">Pending</div>
                        </div>
                    </div>
                </div>

                <div class="details-section-premium">
                    <div style="display: flex; align-items:center; gap: 10px; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">
                        <i data-lucide="list-checks" style="color: var(--brand-green); width: 20px; height: 20px;"></i>
                        <h5 style="margin:0; font-size: 15px; font-weight: 700;">Adjustment Details</h5>
                    </div>
                    <div class="data-table">
                        <table style="width: 100%; border-collapse: separate; border-spacing: 0 4px;">
                            <thead>
                                <tr style="background: rgba(0,0,0,0.02);">
                                    <th style="border-radius: 8px 0 0 8px; padding: 12px; font-size: 12px; text-transform: uppercase;">Setting / Field</th>
                                    <th style="padding: 12px; font-size: 12px; text-transform: uppercase;">Old Value</th>
                                    <th style="border-radius: 0 8px 8px 0; padding: 12px; font-size: 12px; text-transform: uppercase;">Proposed</th>
                                </tr>
                            </thead>
                            <tbody id="statutoryTrackDetailsTableBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
          </div>
        </div>
      </div>
  </main>
  <script src="../../js/compensationdashboard.js"></script>
  <script src="../../js/statutory.js"></script>
  <script>
    lucide.createIcons();
  </script>
</body>
</html>







