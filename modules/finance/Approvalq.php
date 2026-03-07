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
  <title>Approval Queue</title>
  <link rel="stylesheet" href="../../css/salaryscales.css?v=1.2">
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

        <a href="Approvalq.php" class="nav-item active">
          <i data-lucide="check-circle"></i>
          <span>Approval Queue</span>
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
            <a href="" class="submenu-item">
              <i data-lucide="user-plus"></i>
              <span>New Hired Onboard Request</span>
            </a>
            <a href="employeemaster.php" class="submenu-item <?php echo ($page === 'employeemaster') ? 'active' : ''; ?>">
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
            <a href="auditlogs.php" class="submenu-item <?php echo ($page === 'auditlogs') ? 'active' : ''; ?>">
              <i data-lucide="book-user"></i>
              <span>Audit Logs</span>
            </a>
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
        <div class="content-card">
            <div class="card-header">
                <div class="card-header-left">
                    <h3 class="card-title">Universal Approval Queue</h3>
                    <p class="card-subtitle">Endorsed proposals awaiting final Finance review and approval.</p>
                </div>
            </div>
            <div class="card-body">
                <div class="data-table">
                    <table id="universalProposalsTable">
                        <thead>
                            <tr>
                                <th>Proposed By</th>
                                <th>Request Type</th>
                                <th>Details</th>
                                <th>Date Requested</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="universalProposalsBody">
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">
                                        <i data-lucide="loader-2" class="spin"></i>
                                        <p>Loading proposals…</p>
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
            <div class="rem-icon-box" style="background:var(--brand-green-light); color:var(--brand-green);">
              <i data-lucide="git-pull-request"></i>
            </div>
            <div class="rem-title-group">
              <h3 class="rem-title">Review Salary Scale Proposal</h3>
              <p class="rem-subtitle">Final review of endorsed adjustments before officially applying them.</p>
            </div>
          </div>
          <button type="button" class="rem-close" id="btnCloseProposalModal">
            <i data-lucide="x"></i>
          </button>
        </div>

        <!-- Body -->
        <div class="rem-body">
            <div class="rem-section">
                <div class="rem-section-hdr rem-shdr-green">
                    <i data-lucide="check-circle"></i> Endorsed Salary Scale Adjustments
                </div>
                <!-- Box for Reason -->
                <div style="background:var(--surface-hover); padding:16px; border-radius:8px; margin-bottom:16px;">
                    <strong>Reason for Proposal:</strong>
                    <div id="proposalReasonText" style="margin-top:8px; color:var(--text-secondary); white-space:pre-wrap; font-size:14px;"></div>
                </div>

                <div class="data-table">
                  <table id="proposalDetailsTable" style="margin: 0;">
                    <thead>
                      <tr>
                        <th>Job Grade</th>
                        <th>Level Name</th>
                        <th>Old Min</th>
                        <th>Proposed Min</th>
                        <th>Old Max</th>
                        <th>Proposed Max</th>
                      </tr>
                    </thead>
                    <tbody id="proposalDetailsBody">
                        <tr><td colspan="6" style="text-align:center;">Loading details...</td></tr>
                    </tbody>
                  </table>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="rem-footer">
          <div class="rem-footer-hint">
            <i data-lucide="alert-triangle" style="color:var(--brand-yellow);"></i>
            Approved requests will instantly overwrite and update the organization's official salary scales.
          </div>
          <div style="display:flex; gap:12px;">
            <button type="button" class="rem-btn-send rem-btn-secondary" id="btnRejectProposal">
                <i data-lucide="x-circle" style="color:var(--brand-red);"></i> Reject
            </button>
            <button type="button" class="rem-btn-send rem-btn-secondary" id="btnViewSummary">
                <i data-lucide="bar-chart-2" style="color:var(--brand-blue);"></i> View Financial Impact
            </button>
            <button type="button" class="rem-btn-send rem-btn-blue" id="btnEndorseProposal" disabled title="Please view the Financial Impact Summary first">
                <i data-lucide="lock"></i> Final Approve & Apply
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Financial Impact Summary Modal -->
    <div class="modal-overlay hidden" id="financialSummaryModal">
      <div class="rem-dialog" style="max-width: 900px; width: 95%;">
        <div class="rem-header">
          <div class="rem-header-left">
            <div class="rem-icon-box" style="background:rgba(59, 130, 246, 0.1); color:var(--brand-blue);">
              <i data-lucide="pie-chart"></i>
            </div>
            <div class="rem-title-group">
              <h3 class="rem-title">Financial Impact Summary</h3>
              <p class="rem-subtitle">Calculated cost to apply the new salary scales.</p>
            </div>
          </div>
          <button type="button" class="rem-close" id="btnCloseSummaryModal">
            <i data-lucide="x"></i>
          </button>
        </div>

        <div class="rem-body">
            <div class="rem-section" id="summaryLoadingState" style="text-align:center; padding: 40px 0;">
                <i data-lucide="loader-2" style="color:var(--brand-blue); animation: spin 1s linear infinite;"></i>
                <p style="margin-top: 10px; color:var(--text-secondary);">Calculating financial impact...</p>
            </div>

            <div class="rem-section hidden" id="summaryLoadedState">
                <!-- Summary Stats -->
                <div style="display:grid; grid-template-columns: repeat(2, 1fr); gap: 16px; margin-bottom: 24px;">
                    <div style="background:var(--surface-hover); padding:16px; border-radius:8px; border:1px solid var(--border-color);">
                        <div style="color:var(--text-secondary); font-size:13px; margin-bottom:4px;">Impacted Headcount</div>
                        <div id="statImpactedCount" style="font-size:24px; font-weight:700; color:var(--text-primary);">0</div>
                        <div style="font-size:12px; color:var(--text-secondary); margin-top:4px;">Employees below new minimum</div>
                    </div>
                    <div style="background:var(--surface-hover); padding:16px; border-radius:8px; border:1px solid var(--border-color);">
                        <div style="color:var(--text-secondary); font-size:13px; margin-bottom:4px;">Monthly Correction Cost</div>
                        <div id="statMonthlyIncrease" style="font-size:24px; font-weight:700; color:var(--brand-green);">&#8369;0.00</div>
                        <div style="font-size:12px; color:var(--text-secondary); margin-top:4px;">Total gap to reach new minimum</div>
                    </div>
                    <div style="background:var(--surface-hover); padding:16px; border-radius:8px; border:1px solid var(--border-color); grid-column: span 2;">
                        <div style="color:var(--text-secondary); font-size:13px; margin-bottom:4px;">Annualized Funding Requirement</div>
                        <div id="statAnnualRequirement" style="font-size:24px; font-weight:700; color:var(--brand-blue);">&#8369;0.00</div>
                        <div style="font-size:12px; color:var(--text-secondary); margin-top:4px;">(Total projected monthly payroll &times; 12)</div>
                    </div>
                </div>

                <div class="rem-section-hdr rem-shdr-blue">
                    <i data-lucide="users"></i> Full Grade Cost Detail
                </div>
                <div class="data-table">
                  <table style="margin: 0;">
                    <thead>
                      <tr>
                        <th>Job Grade</th>
                        <th>Headcount (Total / Impacted)</th>
                        <th>Current Monthly Payroll</th>
                        <th>Projected Monthly Payroll</th>
                        <th>Total Monthly Increase</th>
                      </tr>
                    </thead>
                    <tbody id="summaryDetailsBody">
                    </tbody>
                  </table>
                </div>
            </div>
        </div>

        <div class="rem-footer">
          <div class="rem-footer-hint">
            <i data-lucide="check" style="color:var(--brand-green);"></i>
            Viewing this summary unlocks the Final Approve button.
          </div>
          <button type="button" class="rem-btn-send" id="btnAcknowledgeSummary" style="background-color: var(--brand-blue); color: white;">
              Acknowledge & Unlock
          </button>
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
              <p class="rem-subtitle">Final review of government-mandated adjustments before application.</p>
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
                <div id="statProofContainer" style="padding: 16px; background: rgba(44, 160, 120, .05); border: 1px dashed var(--brand-green); border-radius: 10px; display: flex; align-items: center; justify-content: space-between;">
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
            <i data-lucide="alert-triangle" style="color:var(--brand-yellow);"></i> Approved requests will instantly overwrite and update the system's statutory settings.
          </div>
          <div style="display:flex; gap:12px;">
            <button type="button" class="rem-btn-send rem-btn-secondary" id="btnRejectStatutory">
                <i data-lucide="x-circle" style="color:var(--brand-red);"></i> Reject
            </button>
            <button type="button" class="rem-btn-send rem-btn-secondary" id="btnViewStatutorySummary">
                <i data-lucide="bar-chart-2" style="color:var(--brand-blue);"></i> View Financial Impact
            </button>
            <button type="button" class="rem-btn-send rem-btn-blue" id="btnApproveStatutory" disabled title="Please view the Financial Impact Summary first">
                <i data-lucide="lock"></i> Final Approve & Apply
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Statutory Financial Impact Summary Modal -->
    <div class="modal-overlay hidden" id="statutorySummaryModal">
      <div class="rem-dialog" style="max-width: 900px; width: 95%;">
        <div class="rem-header">
          <div class="rem-header-left">
            <div class="rem-icon-box" style="background:rgba(59, 130, 246, 0.1); color:var(--brand-blue);">
              <i data-lucide="pie-chart"></i>
            </div>
            <div class="rem-title-group">
              <h3 class="rem-title">Statutory Financial Impact Summary</h3>
              <p class="rem-subtitle">Calculated employer cost to apply the new statutory rates.</p>
            </div>
          </div>
          <button type="button" class="rem-close" id="btnCloseStatutorySummaryModal">
            <i data-lucide="x"></i>
          </button>
        </div>

        <div class="rem-body">
            <div class="rem-section" id="statSummaryLoadingState" style="text-align:center; padding: 40px 0;">
                <i data-lucide="loader-2" style="color:var(--brand-blue); animation: spin 1s linear infinite;"></i>
                <p style="margin-top: 10px; color:var(--text-secondary);">Calculating statutory impact...</p>
            </div>

            <div class="rem-section hidden" id="statSummaryLoadedState">
                <!-- Summary Stats -->
                <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 16px;">
                    <div style="background:var(--surface-hover); padding:16px; border-radius:8px; border:1px solid var(--border-color);">
                        <div style="color:var(--text-secondary); font-size:13px; margin-bottom:4px;">Total Employees Affected</div>
                        <div id="statAffectedCount" style="font-size:24px; font-weight:700; color:var(--text-primary);">0</div>
                        <div style="font-size:12px; color:var(--text-secondary); margin-top:4px;">Total active employees evaluated</div>
                    </div>
                    <div style="background:var(--surface-hover); padding:16px; border-radius:8px; border:1px solid var(--border-color);">
                        <div style="color:var(--text-secondary); font-size:13px; margin-bottom:4px;">Monthly Employer Cost Increase</div>
                        <div id="statMonthlyIncreaseTotalER" style="font-size:24px; font-weight:700; color:var(--brand-green);">&#8369;0.00</div>
                        <div style="font-size:12px; color:var(--text-secondary); margin-top:4px;">Sum of all ER rate increases</div>
                    </div>
                    <div style="background:var(--surface-hover); padding:16px; border-radius:8px; border:1px solid var(--border-color);">
                        <div style="color:var(--text-secondary); font-size:13px; margin-bottom:4px;">Annual Funding Requirement</div>
                        <div id="statAnnualRequirementTotalER" style="font-size:24px; font-weight:700; color:var(--brand-green);">&#8369;0.00</div>
                        <div style="font-size:12px; color:var(--text-secondary); margin-top:4px;">(ER Share &times; 12)</div>
                    </div>
                </div>
                <div style="display:grid; grid-template-columns: repeat(2, 1fr); gap: 16px; margin-bottom: 24px;">
                    <div style="background:var(--surface-hover); padding:16px; border-radius:8px; border:1px solid var(--border-color);">
                        <div style="color:var(--text-secondary); font-size:13px; margin-bottom:4px;">Monthly Take-Home Impact</div>
                        <div id="statMonthlyIncreaseTotalEE" style="font-size:24px; font-weight:700; color:var(--brand-red);">&#8369;0.00</div>
                        <div style="font-size:12px; color:var(--text-secondary); margin-top:4px;">Sum of all EE deductions</div>
                    </div>
                    <div style="background:var(--surface-hover); padding:16px; border-radius:8px; border:1px solid var(--border-color);">
                        <div style="color:var(--text-secondary); font-size:13px; margin-bottom:4px;">Annualized Take-Home Reduction</div>
                        <div id="statAnnualRequirementTotalEE" style="font-size:24px; font-weight:700; color:var(--brand-red);">&#8369;0.00</div>
                        <div style="font-size:12px; color:var(--text-secondary); margin-top:4px;">(EE Share &times; 12)</div>
                    </div>
                </div>

                <div class="rem-section-hdr rem-shdr-blue">
                    <i data-lucide="bar-chart-2"></i> Contribution Rate Comparison
                </div>
                <div class="data-table">
                  <table style="margin: 0;">
                    <thead>
                      <tr>
                        <th>Contribution Type</th>
                        <th>Old Rate</th>
                        <th>Proposed Rate</th>
                        <th>Monthly Cost Difference</th>
                      </tr>
                    </thead>
                    <tbody id="statSummaryDetailsBody">
                    </tbody>
                  </table>
                </div>
            </div>
        </div>

        <div class="rem-footer">
          <div class="rem-footer-hint">
            <i data-lucide="check" style="color:var(--brand-green);"></i>
            Viewing this summary unlocks the Final Approve button.
          </div>
          <button type="button" class="rem-btn-send" id="btnAcknowledgeStatutorySummary" style="background-color: var(--brand-blue); color: white;">
              Acknowledge & Unlock
          </button>
        </div>
      </div>
    </div>
    <!-- Merit Action Modal -->
    <div class="modal-overlay hidden" id="meritActionModal">
      <div class="rem-dialog" style="max-width: 800px; width: 90%;">
        <div class="rem-header">
          <div class="rem-header-left">
            <div class="rem-icon-box" style="background: rgba(139, 92, 246, 0.1); color: #8b5cf6;">
              <i data-lucide="trending-up"></i>
            </div>
            <div class="rem-title-group">
              <h3 class="rem-title">Review Merit Matrix Proposal</h3>
              <p class="rem-subtitle">Final review of endorsed adjustments before officially applying them.</p>
            </div>
          </div>
          <button type="button" class="rem-close" id="btnCloseMeritModal"><i data-lucide="x"></i></button>
        </div>
        <div class="rem-body">
            <div class="rem-section">
                <div class="rem-section-hdr rem-shdr-blue"><i data-lucide="info"></i> Reason for Proposal</div>
                <div id="meritReasonText" style="background:var(--surface-hover); padding:16px; border-radius:8px; color:var(--text-secondary); white-space:pre-wrap; font-size:14px; margin-bottom: 24px;"></div>
                <div class="rem-section-hdr rem-shdr-blue"><i data-lucide="file-diff"></i> Proposed Merit Matrix Adjustments</div>
                <div class="data-table">
                  <table style="margin: 0;">
                    <thead>
                      <tr>
                        <th>Performance Score</th>
                        <th>Compa-Ratio Range</th>
                        <th>Proposed Min Increase (%)</th>
                        <th>Proposed Max Increase (%)</th>
                      </tr>
                    </thead>
                    <tbody id="meritDetailsBody">
                        <tr><td colspan="4" style="text-align:center;">Loading details...</td></tr>
                    </tbody>
                  </table>
                </div>
            </div>
        </div>
        <div class="rem-footer">
          <div class="rem-footer-hint">
            <i data-lucide="alert-triangle" style="color:var(--brand-yellow);"></i> Approved requests will instantly overwrite and update the system's Merit Matrix.
          </div>
          <div style="display:flex; gap:12px;">
            <button type="button" class="rem-btn-send rem-btn-secondary" id="btnRejectMerit">
                <i data-lucide="x-circle" style="color:var(--brand-red);"></i> Reject
            </button>
            <button type="button" class="rem-btn-send rem-btn-secondary" id="btnViewMeritSummary">
                <i data-lucide="bar-chart-2" style="color:var(--brand-purple);"></i> View Financial Impact
            </button>
            <button type="button" class="rem-btn-send rem-btn-purple" id="btnApproveMerit" disabled title="Please view the Financial Impact Summary first">
                <i data-lucide="lock"></i> Final Approve & Apply
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Merit Financial Impact Summary Modal -->
    <div class="modal-overlay hidden" id="meritSummaryModal">
      <div class="rem-dialog" style="max-width: 900px; width: 95%;">
        <div class="rem-header">
          <div class="rem-header-left">
            <div class="rem-icon-box" style="background:rgba(139, 92, 246, 0.1); color:#8b5cf6;">
              <i data-lucide="pie-chart"></i>
            </div>
            <div class="rem-title-group">
              <h3 class="rem-title">Merit Matrix Financial Summary</h3>
              <p class="rem-subtitle">Calculated cost simulation based on the proposed matrix rates.</p>
            </div>
          </div>
          <button type="button" class="rem-close" id="btnCloseMeritSummaryModal"><i data-lucide="x"></i></button>
        </div>
        <div class="rem-body">
            <div class="rem-section" id="meritSummaryLoadingState" style="text-align:center; padding: 40px 0;">
                <i data-lucide="loader-2" style="color:#8b5cf6; animation: spin 1s linear infinite;"></i>
                <p style="margin-top: 10px; color:var(--text-secondary);">Simulating merit impact...</p>
            </div>
            <div class="rem-section hidden" id="meritSummaryLoadedState">
                <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px;">
                    <div style="background:var(--surface-hover); padding:16px; border-radius:8px; border:1px solid var(--border-color);">
                        <div style="color:var(--text-secondary); font-size:13px; margin-bottom:4px;">Eligible Headcount</div>
                        <div id="statMeritHeadcount" style="font-size:24px; font-weight:700; color:var(--text-primary);">0</div>
                        <div style="font-size:12px; color:var(--text-secondary); margin-top:4px;">Total active employees</div>
                    </div>
                    <div style="background:var(--surface-hover); padding:16px; border-radius:8px; border:1px solid var(--border-color);">
                        <div style="color:var(--text-secondary); font-size:13px; margin-bottom:4px;">Max Budget Exposure</div>
                        <div id="statMeritMaxExposure" style="font-size:24px; font-weight:700; color:var(--brand-red);">&#8369;0.00</div>
                        <div style="font-size:12px; color:var(--text-secondary); margin-top:4px;">Cost if ALL received the max allowed %</div>
                    </div>
                    <div style="background:var(--surface-hover); padding:16px; border-radius:8px; border:1px solid var(--border-color);">
                        <div style="color:var(--text-secondary); font-size:13px; margin-bottom:4px;">Annualized Exposure</div>
                        <div id="statMeritAnnual" style="font-size:24px; font-weight:700; color:#8b5cf6;">&#8369;0.00</div>
                        <div style="font-size:12px; color:var(--text-secondary); margin-top:4px;">Total new money needed per year (&times; 12)</div>
                    </div>
                </div>
                <div class="rem-section-hdr rem-shdr-blue">
                    <i data-lucide="bar-chart-2"></i> Rate Comparison Table
                </div>
                <div class="data-table">
                  <table style="margin: 0;">
                    <thead>
                      <tr>
                        <th>Performance Score</th>
                        <th>Compa-Ratio Range</th>
                        <th>Old Max Rate</th>
                        <th>Proposed Max Rate</th>
                        <th>Variance</th>
                      </tr>
                    </thead>
                    <tbody id="meritSummaryDetailsBody">
                    </tbody>
                  </table>
                </div>
            </div>
        </div>
        <div class="rem-footer">
          <div class="rem-footer-hint">
            <i data-lucide="check" style="color:var(--brand-green);"></i> Viewing this summary unlocks the Final Approve button.
          </div>
          <button type="button" class="rem-btn-send" id="btnAcknowledgeMeritSummary" style="background-color: #8b5cf6; color: white;">
              Acknowledge & Unlock
          </button>
        </div>
      </div>
    </div>

    <!-- Allowance Action Modal -->
    <div class="modal-overlay hidden" id="allowanceActionModal">
      <div class="rem-dialog" style="max-width: 800px; width: 90%;">
        <div class="rem-header">
          <div class="rem-header-left">
            <div class="rem-icon-box" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
              <i data-lucide="gift"></i>
            </div>
            <div class="rem-title-group">
              <h3 class="rem-title">Review Allowance Proposal</h3>
              <p class="rem-subtitle">Final review of endorsed adjustments before officially applying them.</p>
            </div>
          </div>
          <button type="button" class="rem-close" id="btnCloseAllowanceModal"><i data-lucide="x"></i></button>
        </div>
        <div class="rem-body">
            <div class="rem-section">
                <div class="rem-section-hdr rem-shdr-blue"><i data-lucide="info"></i> Reason for Proposal</div>
                <div id="allowanceReasonText" style="background:var(--surface-hover); padding:16px; border-radius:8px; color:var(--text-secondary); white-space:pre-wrap; font-size:14px; margin-bottom: 24px;"></div>
                <div class="rem-section-hdr rem-shdr-blue"><i data-lucide="file-diff"></i> Proposed Allowance Adjustments</div>
                <div class="data-table">
                  <table style="margin: 0;">
                    <thead>
                      <tr>
                        <th>Grade</th>
                        <th>Allowance Type</th>
                        <th>Old Amount</th>
                        <th>Proposed Amount</th>
                      </tr>
                    </thead>
                    <tbody id="allowanceDetailsBody">
                        <tr><td colspan="4" style="text-align:center;">Loading details...</td></tr>
                    </tbody>
                  </table>
                </div>
            </div>
        </div>
        <div class="rem-footer">
          <div class="rem-footer-hint">
            <i data-lucide="alert-triangle" style="color:var(--brand-yellow);"></i> Approved requests will instantly overwrite and update the system's Allowances.
          </div>
          <div style="display:flex; gap:12px;">
            <button type="button" class="rem-btn-send rem-btn-secondary" id="btnRejectAllowance">
                <i data-lucide="x-circle" style="color:var(--brand-red);"></i> Reject
            </button>
            <button type="button" class="rem-btn-send rem-btn-secondary" id="btnViewAllowanceSummary">
                <i data-lucide="bar-chart-2" style="color:var(--brand-orange);"></i> View Financial Impact
            </button>
            <button type="button" class="rem-btn-send rem-btn-orange" id="btnApproveAllowance" disabled title="Please view the Financial Impact Summary first">
                <i data-lucide="lock"></i> Final Approve & Apply
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Allowance Financial Impact Summary Modal -->
    <div class="modal-overlay hidden" id="allowanceSummaryModal">
      <div class="rem-dialog" style="max-width: 900px; width: 95%;">
        <div class="rem-header">
          <div class="rem-header-left">
            <div class="rem-icon-box" style="background:rgba(245, 158, 11, 0.1); color:#f59e0b;">
              <i data-lucide="pie-chart"></i>
            </div>
            <div class="rem-title-group">
              <h3 class="rem-title">Allowance Impact Summary</h3>
              <p class="rem-subtitle">Calculated cost simulation based on the proposed allowance grades.</p>
            </div>
          </div>
          <button type="button" class="rem-close" id="btnCloseAllowanceSummaryModal"><i data-lucide="x"></i></button>
        </div>
        <div class="rem-body">
            <div class="rem-section" id="allowanceSummaryLoadingState" style="text-align:center; padding: 40px 0;">
                <i data-lucide="loader-2" style="color:#f59e0b; animation: spin 1s linear infinite;"></i>
                <p style="margin-top: 10px; color:var(--text-secondary);">Simulating allowance impact...</p>
            </div>
            <div class="rem-section hidden" id="allowanceSummaryLoadedState">
                <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px;">
                    <div style="background:var(--surface-hover); padding:16px; border-radius:8px; border:1px solid var(--border-color);">
                        <div style="color:var(--text-secondary); font-size:13px; margin-bottom:4px;">Total Monthly Liability</div>
                        <div id="statAllwTotalLiab" style="font-size:24px; font-weight:700; color:var(--text-primary);">&#8369;0.00</div>
                        <div style="font-size:12px; color:var(--text-secondary); margin-top:4px;">Sum of all 5 types for Headcount</div>
                    </div>
                    <div style="background:var(--surface-hover); padding:16px; border-radius:8px; border:1px solid var(--border-color);">
                        <div style="color:var(--text-secondary); font-size:13px; margin-bottom:4px;">Monthly Budget Change</div>
                        <div id="statAllwMonthlyChange" style="font-size:24px; font-weight:700; color:var(--brand-green);">&#8369;0.00</div>
                        <div style="font-size:12px; color:var(--text-secondary); margin-top:4px;">Increase against current structures</div>
                    </div>
                    <div style="background:var(--surface-hover); padding:16px; border-radius:8px; border:1px solid var(--border-color);">
                        <div style="color:var(--text-secondary); font-size:13px; margin-bottom:4px;">Annualized Allowance Funding</div>
                        <div id="statAllwAnnual" style="font-size:24px; font-weight:700; color:#f59e0b;">&#8369;0.00</div>
                        <div style="font-size:12px; color:var(--text-secondary); margin-top:4px;">(Total Monthly Liability &times; 12)</div>
                    </div>
                </div>
                <div class="rem-section-hdr rem-shdr-blue">
                    <i data-lucide="bar-chart-2"></i> Grade Distribution Breakdown
                </div>
                <div class="data-table">
                  <table style="margin: 0;">
                    <thead>
                      <tr>
                        <th>Job Grade</th>
                        <th>Old Total Pkg</th>
                        <th>Proposed Total Pkg</th>
                        <th>De Minimis Value</th>
                        <th>Taxable Value</th>
                      </tr>
                    </thead>
                    <tbody id="allowanceSummaryDetailsBody">
                    </tbody>
                  </table>
                </div>
            </div>
        </div>
        <div class="rem-footer">
          <div class="rem-footer-hint">
            <i data-lucide="check" style="color:var(--brand-green);"></i> Viewing this summary unlocks the Final Approve button.
          </div>
          <button type="button" class="rem-btn-send" id="btnAcknowledgeAllowanceSummary" style="background-color: #f59e0b; color: white;">
              Acknowledge & Unlock
          </button>
        </div>
      </div>
    </div>
    <!-- Simulation Review Modal -->
    <div class="modal-overlay hidden" id="simulationActionModal">
      <div class="rem-dialog" style="max-width: 1000px; width: 95%;">
        <!-- Header -->
        <div class="rem-header">
          <div class="rem-header-left">
            <div class="rem-icon-box" style="background: rgba(37, 99, 235, 0.1); color: #2563eb;">
              <i data-lucide="calculator"></i>
            </div>
            <div class="rem-title-group">
              <h3 class="rem-title">Review Compensation Simulation</h3>
              <p class="rem-subtitle">Final review of proposed salary adjustments before official application.</p>
            </div>
          </div>
          <button type="button" class="rem-close" id="btnCloseSimulationModal">
            <i data-lucide="x"></i>
          </button>
        </div>

        <!-- Body -->
        <div class="rem-body">
            <div class="rem-section">
                <div class="rem-section-hdr rem-shdr-blue">
                    <i data-lucide="users"></i> Proposed Salary Adjustments
                </div>
                
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                  <table class="comp-table" style="margin: 0; min-width: 900px;">
                    <thead>
                      <tr>
                        <th>Employee</th>
                        <th>Job Grade</th>
                        <th>Current Salary</th>
                        <th>Prop. %</th>
                        <th>Prop. Inc (₱)</th>
                        <th>New Salary</th>
                      </tr>
                    </thead>
                    <tbody id="simulationDetailsBody">
                        <tr><td colspan="6" style="text-align:center;">Loading simulation details...</td></tr>
                    </tbody>
                  </table>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="rem-footer">
          <div class="rem-footer-hint">
            <i data-lucide="info"></i>
            Proposals must have a viewing summary before final approval.
          </div>
          <div style="display:flex; gap:12px;">
            <button type="button" class="btn-summary" id="btnViewSimulationSummary">
                <i data-lucide="bar-chart-3"></i> View Summary Total
            </button>
            <button type="button" class="rem-btn-send" id="btnRejectSimulation" style="background-color: var(--brand-red);">
                <i data-lucide="x-circle"></i> Reject
            </button>
            <button type="button" class="rem-btn-send" id="btnFinalApproveSimulation" style="background-color: var(--brand-blue); opacity: 0.6; cursor: not-allowed;" disabled>
                <i data-lucide="check-circle"></i> Final Approve & Apply
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Simulation Summary Modal -->
    <div class="modal-overlay hidden" id="simulationSummaryModal">
      <div class="rem-dialog" style="max-width: 700px; width: 95%;">
        <!-- Header -->
        <div class="rem-header">
          <div class="rem-header-left">
            <div class="rem-icon-box" style="background: rgba(37, 99, 235, 0.1); color: #2563eb;">
              <i data-lucide="pie-chart"></i>
            </div>
            <div class="rem-title-group">
              <h3 class="rem-title">Financial Impact Summary</h3>
              <p class="rem-subtitle" id="simSummaryTitle">Compensation Simulation Cycle</p>
            </div>
          </div>
          <button type="button" class="rem-close" id="btnCloseSimSummary">
            <i data-lucide="x"></i>
          </button>
        </div>

        <!-- Body -->
        <div class="rem-body">
            <div class="rem-section hidden" id="simSummaryLoading" style="text-align:center; padding: 40px 0;">
                <i data-lucide="loader-2" style="color:var(--brand-blue); animation: spin 1s linear infinite;"></i>
                <p style="margin-top: 10px; color:var(--text-secondary);">Calculating financial impact...</p>
            </div>

            <div class="rem-section" id="simSummaryLoaded">
                <!-- Summary Stats -->
                <div style="display:grid; grid-template-columns: repeat(2, 1fr); gap: 16px; margin-bottom: 24px;">
                    <div style="background:var(--surface-hover); padding:16px; border-radius:8px; border:1px solid var(--border-color);">
                        <div style="color:var(--text-secondary); font-size:13px; margin-bottom:4px;">Impacted Headcount</div>
                        <div id="simImpactCount" style="font-size:24px; font-weight:700; color:var(--text-primary);">0</div>
                        <div style="font-size:12px; color:var(--text-secondary); margin-top:4px;">Total active employees</div>
                    </div>
                    <div style="background:var(--surface-hover); padding:16px; border-radius:8px; border:1px solid var(--border-color);">
                        <div style="color:var(--text-secondary); font-size:13px; margin-bottom:4px;">Total Monthly Increase</div>
                        <div id="simMonthlyIncrease" style="font-size:24px; font-weight:700; color:var(--brand-green);">&#8369;0.00</div>
                        <div style="font-size:12px; color:var(--text-secondary); margin-top:4px;">Increase from current salary</div>
                    </div>
                    <div style="background:var(--surface-hover); padding:16px; border-radius:8px; border:1px solid var(--border-color); grid-column: span 2;">
                        <div style="color:var(--text-secondary); font-size:13px; margin-bottom:4px;">Projected Annual Requirement</div>
                        <div id="simAnnualRequirement" style="font-size:24px; font-weight:700; color:var(--brand-blue);">&#8369;0.00</div>
                        <div style="font-size:12px; color:var(--text-secondary); margin-top:4px;">(Total projected monthly payroll &times; 12)</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="rem-footer">
          <div class="rem-footer-hint">
            <i data-lucide="check" style="color:var(--brand-green);"></i>
            Viewing this summary unlocks the Final Approve button.
          </div>
          <button type="button" class="rem-btn-send" id="btnAcknowledgeSimSummary" style="background-color: var(--brand-blue); color: white;">
              Acknowledge & Unlock
          </button>
        </div>
      </div>
    </div>
  </main>
  <script src="../../js/approveq.js?v=<?php echo time(); ?>"></script>
  <script>
    lucide.createIcons();
  </script>
</body>
</html>







