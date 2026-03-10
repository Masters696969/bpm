<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../../login.php");
    exit();
}

require_once '../../config/config.php';

// Fetch Active Compensation Period (Assume ID 1 for now)
$period_id = 1;
$period_query = $conn->query("SELECT * FROM compensation_period WHERE period_id = $period_id");
$period_data = $period_query->fetch_assoc();

// Fetch Merit Matrix
$matrix_query = $conn->query("SELECT * FROM merit_matrix_settings WHERE period_id = $period_id ORDER BY performance_rating DESC, compa_ratio_range ASC");
$merit_matrix = [];
if ($matrix_query) {
    while ($row = $matrix_query->fetch_assoc()) {
        $rating = (string)$row['performance_rating'];
        $range = $row['compa_ratio_range'];
        $merit_matrix[$rating][$range] = $row;
    }
}
?>
<script>
    // Expose Compensation Configuration to JS
    window.compConfig = {
        meritMatrix: <?php echo json_encode($merit_matrix); ?>
    };
</script>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Merit Matrix Structure</title>
  <link rel="stylesheet" href="../../css/compensationdashboard.css?v=1.3">
  <link rel="stylesheet" href="../../css/cycle.css?v=1.2">
  <link rel="stylesheet" href="../../css/metrix.css?v=1.2">
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

          <!-- Merit Matrix Tab -->
          <div class="tab-panel active" id="merit">
             <div class="section-header" style="margin-bottom: 24px; display: flex; justify-content: space-between; align-items: flex-start; padding-bottom: 16px; border-bottom: 1px solid var(--border-color);">
              <div class="sh-info">
                <h3 style="font-size: 20px; font-weight: 700; color: var(--text-primary); margin-bottom: 8px;">Annual Merit Increase Matrix</h3>
                <p style="color: var(--text-secondary); font-size: 14px;">Calculate increases based on performance and position within salary range (Compa-ratio). <strong>Note: Max allowed increase is 5.0%.</strong></p>
              </div>
              <div class="comp-panel-actions" style="display: flex; gap: 12px;">
                <button class="btn btn-outline" id="btnTrackMeritStatus" style="border: 1px solid var(--border-color); color: var(--text-secondary);">
                  <i data-lucide="eye"></i>
                  <span>Track Status</span>
                </button>
                <button class="btn btn-primary" id="btnProposeMeritChange">
                  <i data-lucide="git-pull-request"></i>
                  <span>Propose Change</span>
                </button>
              </div>
            </div>
            <div class="matrix-container">
              <div style="background:var(--surface-color); border-radius:12px; border:1px solid var(--border-color); overflow:hidden; box-shadow:var(--shadow-sm);">
                <table class="matrix-table" style="width: 100%; border-collapse: separate; border-spacing: 0;">
                  <thead>
                    <tr>
                      <th rowspan="2" style="background:var(--surface-hover); padding:16px; border-bottom:1px solid var(--border-color); border-right:1px solid var(--border-color); font-size:13px; text-transform:uppercase; color:var(--text-secondary); font-weight:600;">Performance Rating</th>
                      <th colspan="3" style="background:var(--surface-hover); padding:12px 16px; border-bottom:1px solid var(--border-color); text-align:center; font-size:13px; text-transform:uppercase; color:var(--text-secondary); font-weight:600;">Position in Range (Compa-Ratio)</th>
                    </tr>
                    <tr>
                      <th style="background:var(--surface-hover); padding:12px 16px; border-bottom:1px solid var(--border-color); border-right:1px solid var(--border-color); text-align:center; font-size:12px; color:var(--text-tertiary);">Low ( < 90% )</th>
                      <th style="background:var(--surface-hover); padding:12px 16px; border-bottom:1px solid var(--border-color); border-right:1px solid var(--border-color); text-align:center; font-size:12px; color:var(--text-tertiary);">At Mid ( 90-110% )</th>
                      <th style="background:var(--surface-hover); padding:12px 16px; border-bottom:1px solid var(--border-color); text-align:center; font-size:12px; color:var(--text-tertiary);">High ( > 110% )</th>
                    </tr>
                  </thead>
                  <tbody id="meritMatrixTbody">
                    <tr><td colspan="4" style="text-align: center; padding: 40px; color: var(--text-secondary);">Loading Merit Matrix Data...</td></tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
      <!-- Propose Merit Matrix Change Modal -->
      <div id="proposeMeritModal" class="modal" aria-hidden="true" style="display: none;">
        <div class="modal-dialog" style="max-width: 800px;">
          <form id="meritProposalForm">
            <div class="comp-modal-hero">
              <div class="comp-modal-hero-inner" style="padding: 12px 16px;">
                <div class="comp-modal-hero-icon" style="background: rgba(139, 92, 246, 0.2); width: 36px; height: 36px; color: #8b5cf6;">
                  <i data-lucide="trending-up" style="width: 18px; height: 18px;"></i>
                </div>
                <div class="comp-modal-hero-text">
                  <h3 style="font-size: 16px;">Propose Merit Matrix Adjustments</h3>
                  <p style="font-size: 11px;">Update the minimum and maximum increase percentages based on compa-ratio.</p>
                </div>
                <button type="button" class="rp-close-modal" id="closeProposeMeritModalBtn" title="Close">&times;</button>
              </div>
            </div>
            <div class="modal-body modal-form-premium" style="max-height: 50vh; overflow-y: auto;">
              <div class="table-responsive">
                <table class="matrix-table" id="proposeMeritTable" style="width: 100%; border-collapse: separate; border-spacing: 0;">
                  <thead>
                    <tr>
                      <th rowspan="2" style="background:var(--surface-hover); padding:16px; border-bottom:1px solid var(--border-color); border-right:1px solid var(--border-color); font-size:13px; text-transform:uppercase; color:var(--text-secondary); font-weight:600;">Performance Rating</th>
                      <th colspan="3" style="background:var(--surface-hover); padding:12px 16px; border-bottom:1px solid var(--border-color); text-align:center; font-size:13px; text-transform:uppercase; color:var(--text-secondary); font-weight:600;">Position in Range (Compa-Ratio)</th>
                    </tr>
                    <tr>
                      <th style="background:var(--surface-hover); padding:12px 16px; border-bottom:1px solid var(--border-color); border-right:1px solid var(--border-color); text-align:center; font-size:12px; color:var(--text-tertiary);">Low ( < 90% )</th>
                      <th style="background:var(--surface-hover); padding:12px 16px; border-bottom:1px solid var(--border-color); border-right:1px solid var(--border-color); text-align:center; font-size:12px; color:var(--text-tertiary);">At Mid ( 90-110% )</th>
                      <th style="background:var(--surface-hover); padding:12px 16px; border-bottom:1px solid var(--border-color); text-align:center; font-size:12px; color:var(--text-tertiary);">High ( > 110% )</th>
                    </tr>
                  </thead>
                  <tbody id="proposeMeritTbody">
                  </tbody>
                </table>
              </div>
              <div class="form-group" style="margin-top: 20px;">
                <label>Reason for Proposal <span class="required">*</span></label>
                <textarea id="meritProposalReason" rows="3" class="input-premium no-icon" placeholder="Explain the rationale behind these proposed matrix changes..." required></textarea>
              </div>
            </div>
            <div class="modal-footer-premium">
              <button type="button" id="cancelMeritProposeBtn" class="btn-cancel-premium">Cancel</button>
              <button type="submit" class="btn-comp-submit">Submit Proposal</button>
            </div>
          </form>
        </div>
      </div>

      <!-- Track Merit Matrix Status Modal -->
      <div id="trackMeritModal" class="modal" aria-hidden="true" style="display: none;">
        <div class="modal-dialog" style="max-width: 800px;">
          <div class="comp-modal-hero">
            <div class="comp-modal-hero-inner">
              <div class="comp-modal-hero-icon" style="background: rgba(139, 92, 246, 0.2); color: #8b5cf6;">
                <i data-lucide="eye"></i>
              </div>
              <div class="comp-modal-hero-text">
                <h3>Track Merit Proposals</h3>
                <p>Monitor the status of your submitted merit matrix adjustments.</p>
              </div>
              <button type="button" class="rp-close-modal" id="closeTrackMeritBtn" title="Close">&times;</button>
            </div>
          </div>
          <div class="modal-body modal-form-premium" style="min-height: 400px; max-height: 70vh; overflow-y: auto;">
            <!-- Stage A: List of Batches -->
            <div id="meritStageAList">
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
                        <tbody id="meritTrackingBody">
                            <tr><td colspan="6" style="text-align:center; padding:60px; color: var(--text-tertiary);">
                                <i data-lucide="inbox" style="width: 48px; height: 48px; opacity: 0.2; margin-bottom: 12px;"></i>
                                <p>No tracking history available</p>
                            </td></tr>
                        </tbody>
                    </table>
                </div>
                <!-- Merit Pagination -->
                <div class="pagination-premium" style="display: flex; justify-content: space-between; align-items: center; margin-top: 16px; padding: 0 8px;">
                    <div style="font-size: 13px; color: var(--text-secondary);">Showing <span id="meritPageRange">0</span> of <span id="meritTotalEntries">0</span></div>
                    <div style="display: flex; gap: 8px;">
                        <button id="prevMeritPage" class="btn-pagination" disabled><i data-lucide="chevron-left"></i></button>
                        <button id="nextMeritPage" class="btn-pagination" disabled><i data-lucide="chevron-right"></i></button>
                    </div>
                </div>
            </div>

            <!-- Stage B: Stepper & Details -->
            <div id="meritStageBDetails" style="display: none;">
                <button type="button" class="btn btn-secondary" id="btnBackToMeritList" style="margin-bottom: 24px; padding: 6px 12px; font-size: 13px;">
                    <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i> Back to List
                </button>

                <div class="stepper-hero-premium" style="background: linear-gradient(135deg, rgba(139, 92, 246, 0.05) 0%, rgba(59, 130, 246, 0.05) 100%); border-radius: 16px; padding: 32px 20px; border: 1px solid var(--border-color); margin-bottom: 32px; box-shadow: var(--shadow-sm);">
                    <div class="stepper-header" style="text-align: center; margin-bottom: 32px;">
                        <h4 id="meritStepperBatchTitle" style="font-size: 20px; font-weight: 800; color: var(--text-primary); margin-bottom: 8px; letter-spacing: -0.02em;">Batch: ----</h4>
                        <div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                            <i data-lucide="info" style="width: 14px; height: 14px; color: #8b5cf6;"></i>
                            <p id="meritStepperBatchReason" style="color: var(--text-secondary); font-size: 13px; font-style: italic;"></p>
                        </div>
                    </div>

                    <div class="stepper-container" style="max-width: 650px;">
                        <div class="stepper-line">
                            <div class="stepper-line-fill" id="meritStepperLineFill" style="width: 0%; background: #8b5cf6;"></div>
                        </div>

                        <div class="stepper-step merit-step" id="meritStep1">
                            <div class="step-circle"><i data-lucide="check"></i></div>
                            <div class="step-label">Step 1</div>
                            <div class="step-title" style="font-size: 13px;">Submitted</div>
                            <div class="step-desc" id="meritStep1Desc">Analyst</div>
                        </div>

                        <div class="stepper-step merit-step" id="meritStep2">
                            <div class="step-circle"><i data-lucide="search"></i></div>
                            <div class="step-label">Step 2</div>
                            <div class="step-title" style="font-size: 13px;">Under Review</div>
                            <div class="step-desc" id="meritStep2Desc">Pending</div>
                        </div>

                        <div class="stepper-step merit-step" id="meritStep3">
                            <div class="step-circle"><i data-lucide="user-check"></i></div>
                            <div class="step-label">Step 3</div>
                            <div class="step-title" style="font-size: 13px;">Endorsed</div>
                            <div class="step-desc" id="meritStep3Desc">Supervisor</div>
                        </div>

                        <div class="stepper-step merit-step" id="meritStep4">
                            <div class="step-circle"><i data-lucide="flag"></i></div>
                            <div class="step-label">Step 4</div>
                            <div class="step-title" id="meritStep4Title" style="font-size: 13px;">Finalized</div>
                            <div class="step-desc" id="meritStep4Desc">Pending</div>
                        </div>
                    </div>
                </div>

                <div class="details-section-premium">
                    <div style="display: flex; align-items:center; gap: 10px; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">
                        <i data-lucide="list-checks" style="color: #8b5cf6; width: 20px; height: 20px;"></i>
                        <h5 style="margin:0; font-size: 15px; font-weight: 700;">Adjustment Details</h5>
                    </div>
                    <div class="data-table">
                        <table style="width: 100%; border-collapse: separate; border-spacing: 0 4px;">
                            <thead>
                                <tr style="background: rgba(0,0,0,0.02);">
                                    <th style="border-radius: 8px 0 0 8px; padding: 12px; font-size: 12px; text-transform: uppercase;">Rating & Compa-Ratio</th>
                                    <th style="padding: 12px; font-size: 12px; text-transform: uppercase;">Old Min (%)</th>
                                    <th style="padding: 12px; font-size: 12px; text-transform: uppercase;">Old Max (%)</th>
                                    <th style="padding: 12px; font-size: 12px; text-transform: uppercase; color:var(--brand-green);">New Min (%)</th>
                                    <th style="border-radius: 0 8px 8px 0; padding: 12px; font-size: 12px; text-transform: uppercase; color:var(--brand-green);">New Max (%)</th>
                                </tr>
                            </thead>
                            <tbody id="meritTrackDetailsTableBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
          </div>
        </div>
      </div>
  </main>
  <script src="../../js/compensationdashboard.js?v=1.3"></script>
  <script src="../../js/matrix.js"></script>
  <script>
    lucide.createIcons();
  </script>
</body>
</html>







