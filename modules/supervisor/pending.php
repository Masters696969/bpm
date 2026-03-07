<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../../login.php");
    exit();
}
$page = 'pending';
$module = 'hr'; 
$pageHeader = 'Pending Reviews';
$pageSubtitle = 'Review and endorse employee information updates.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pending Request</title>
  <link rel="stylesheet" href="../../css/pending.css?v=2.4">
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
        
        <a href="dashboard.php" class="nav-item">
          <i data-lucide="layout-dashboard"></i>
          <span>Dashboard</span>
        </a>
         <a href="pending.php" class="nav-item active">
          <i data-lucide="circle-ellipsis"></i>
          <span>Pending Reviews</span>
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

        <div class="nav-item-group <?php echo ($module === 'planning') ? 'active' : ''; ?>">
          <button class="nav-item has-submenu" data-module="planning">
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
            <a href="cycle.php" class="submenu-item <?php echo ($page === 'cycle') ? 'active' : ''; ?>">
              <i data-lucide="notebook-pen"></i>
              <span>Compensation Structure Management</span>
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
        <span class="nav-section-title">SETTINGS</span>
        
        <a href="#" class="nav-item">
          <i data-lucide="circle-ellipsis"></i>
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
                    <h3 class="card-title">Pending Requests</h3>
                    <p class="card-subtitle">Review and endorse employee information updates and salary scale proposals.</p>
                </div>
            </div>
            <div class="card-body">
                <div class="data-table">
                    <table id="requestsTable">
                        <thead>
                            <tr>
                                <th>Employee / Proposer</th>
                                <th>Request Type</th>
                                <th>Date Submitted</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="requestsTableBody">
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">
                                        <i data-lucide="loader-2"></i>
                                        <p>Loading requests…</p>
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
            <div class="rem-icon-box">
              <i data-lucide="git-pull-request"></i>
            </div>
            <div class="rem-title-group">
              <h3 class="rem-title">Review Salary Scale Proposal</h3>
              <p class="rem-subtitle">Review the requested changes before endorsing to the Manager.</p>
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
                    <i data-lucide="file-diff"></i> Proposed Salary Scale Adjustments
                </div>
                <!-- Box for Reason -->
                <div style="background:var(--surface-hover); padding:16px; border-radius:8px; margin-bottom:16px;">
                    <strong>Reason for Proposal:</strong>
                    <div id="proposalReasonText" style="margin-top:8px; color:var(--text-secondary); white-space:pre-wrap; font-size:14px;"></div>
                </div>

                <div class="table-responsive">
                  <table class="comp-table editable-table" id="proposalDetailsTable" style="margin: 0;">
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
            <i data-lucide="info"></i>
            Endorsed requests are sent to the Manager. Rejected requests are discarded.
          </div>
          <div style="display:flex; gap:12px;">
            <button type="button" class="rem-btn-send" id="btnRejectProposal" style="background-color: var(--brand-red);">
                <i data-lucide="x-circle"></i> Reject
            </button>
            <button type="button" class="rem-btn-send" id="btnEndorseProposal" style="background-color: var(--brand-green);">
                <i data-lucide="check-circle"></i> Endorse
            </button>
          </div>
        </div>
      </div>
    </div>
    <!-- View/Endorse Modal styled like Information Management Request Edit -->
    <div class="modal-overlay hidden" id="requestActionModal">
      <div class="rem-dialog">
        <!-- Header -->
        <div class="rem-header">
          <div class="rem-header-left">
            <div class="rem-icon-box">
              <i data-lucide="file-check-2"></i>
            </div>
            <div class="rem-title-group">
              <h3 class="rem-title">Review & Endorse Request</h3>
              <p class="rem-subtitle">Review the requested changes before endorsing.</p>
            </div>
          </div>
          <button type="button" class="rem-close" id="btnCloseActionModal">
            <i data-lucide="x"></i>
          </button>
        </div>

        <!-- Body -->
        <div class="rem-body">
            <div class="rem-section" id="requestDetailsBody">
                <!-- Dynamic Content injected by pending.js -->
            </div>
        </div>

        <!-- Footer -->
        <div class="rem-footer">
          <div class="rem-footer-hint">
            <i data-lucide="clock"></i>
            Endorsed requests are sent to the Manager for final approval.
          </div>
          <button type="button" class="rem-btn-send" id="btnEndorse" style="background-color: var(--brand-green);">
            <i data-lucide="check-circle"></i> Endorse Now
          </button>
        </div>
      </div>
    </div>
    <!-- Statutory Proposal Review Modal -->
    <div class="modal-overlay hidden" id="statutoryActionModal">
      <div class="rem-dialog" style="max-width: 800px;">
        <!-- Header -->
        <div class="rem-header">
          <div class="rem-header-left">
            <div class="rem-icon-box" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">
              <i data-lucide="landmark"></i>
            </div>
            <div class="rem-title-group">
              <h3 class="rem-title">Review Statutory Adjustments</h3>
              <p class="rem-subtitle">Review proposed government-mandated rate/limit changes.</p>
            </div>
          </div>
          <button type="button" class="rem-close" id="btnCloseStatutoryModal">
            <i data-lucide="x"></i>
          </button>
        </div>

        <!-- Body -->
        <div class="rem-body">
            <div class="rem-section">
                <div class="rem-section-hdr rem-shdr-blue">
                    <i data-lucide="info"></i> Reason for Proposal
                </div>
                <div id="statutoryReasonText" style="padding: 16px; font-size: 14px; color: var(--text-secondary); background: var(--surface-hover); border-radius: 10px; line-height: 1.5;">
                    -
                </div>
            </div>

            <div class="rem-section" style="margin-top: 24px;">
                <div class="rem-section-hdr rem-shdr-blue">
                    <i data-lucide="list"></i> Proposed Changes
                </div>
                <div class="rem-table-container">
                    <table class="rem-table">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Setting / Field</th>
                                <th>Current</th>
                                <th>Proposed</th>
                            </tr>
                        </thead>
                        <tbody id="statutoryDetailsBody">
                            <!-- Injected by pending.js -->
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rem-section" style="margin-top: 24px;">
                <div class="rem-section-hdr rem-shdr-green">
                    <i data-lucide="file-check"></i> Government Proof
                </div>
                <div style="padding: 16px; background: rgba(44, 160, 120, .05); border: 1px dashed var(--brand-green); border-radius: 10px; display: flex; align-items: center; justify-content: space-between;">
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

        <!-- Footer -->
        <div class="rem-footer">
          <div class="rem-footer-hint">
            <i data-lucide="shield-check"></i>
            Endorsing will send these proposals to the Manager for final approval and application.
          </div>
          <div style="display: flex; gap: 12px;">
            <button type="button" class="rem-btn-send" id="btnRejectStatutory" style="background-color: var(--brand-red);">
                <i data-lucide="x-circle"></i> Reject
            </button>
            <button type="button" class="rem-btn-send" id="btnEndorseStatutory" style="background-color: #3b82f6;">
                <i data-lucide="check-circle"></i> Endorse Proposal
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Merit Action Modal -->
    <div class="modal-overlay hidden" id="meritActionModal">
      <div class="rem-dialog" style="max-width: 800px; width: 90%;">
        <!-- Header -->
        <div class="rem-header">
          <div class="rem-header-left">
            <div class="rem-icon-box" style="background: rgba(139, 92, 246, 0.1); color: #8b5cf6;">
              <i data-lucide="trending-up"></i>
            </div>
            <div class="rem-title-group">
              <h3 class="rem-title">Review Merit Matrix Proposal</h3>
              <p class="rem-subtitle">Review the requested changes before endorsing to the Manager.</p>
            </div>
          </div>
          <button type="button" class="rem-close" id="btnCloseMeritModal">
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
                    <div id="meritReasonText" style="margin-top:8px; color:var(--text-secondary); white-space:pre-wrap; font-size:14px;"></div>
                </div>

                <div class="table-responsive">
                  <table class="comp-table editable-table" id="meritDetailsTable" style="margin: 0;">
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

        <!-- Footer -->
        <div class="rem-footer">
          <div class="rem-footer-hint">
            <i data-lucide="info"></i>
            Endorsed requests are sent to the Manager. Rejected requests are discarded.
          </div>
          <div style="display:flex; gap:12px;">
            <button type="button" class="rem-btn-send" id="btnRejectMerit" style="background-color: var(--brand-red);">
                <i data-lucide="x-circle"></i> Reject
            </button>
            <button type="button" class="rem-btn-send" id="btnEndorseMerit" style="background-color: var(--brand-green);">
                <i data-lucide="check-circle"></i> Endorse
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Allowance Action Modal -->
    <div class="modal-overlay hidden" id="allowanceActionModal">
      <div class="rem-dialog" style="max-width: 800px; width: 90%;">
        <!-- Header -->
        <div class="rem-header">
          <div class="rem-header-left">
            <div class="rem-icon-box" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
              <i data-lucide="gift"></i>
            </div>
            <div class="rem-title-group">
              <h3 class="rem-title">Review Allowance Proposal</h3>
              <p class="rem-subtitle">Review the requested changes before endorsing to the Manager.</p>
            </div>
          </div>
          <button type="button" class="rem-close" id="btnCloseAllowanceModal">
            <i data-lucide="x"></i>
          </button>
        </div>

        <!-- Body -->
        <div class="rem-body">
            <div class="rem-section">
                <div class="rem-section-hdr rem-shdr-blue">
                    <i data-lucide="file-diff"></i> Proposed Allowance Adjustments
                </div>
                <!-- Box for Reason -->
                <div style="background:var(--surface-hover); padding:16px; border-radius:8px; margin-bottom:16px;">
                    <strong>Reason for Proposal:</strong>
                    <div id="allowanceReasonText" style="margin-top:8px; color:var(--text-secondary); white-space:pre-wrap; font-size:14px;"></div>
                </div>

                <div class="table-responsive">
                  <table class="comp-table editable-table" id="allowanceDetailsTable" style="margin: 0;">
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

        <!-- Footer -->
        <div class="rem-footer">
          <div class="rem-footer-hint">
            <i data-lucide="info"></i>
            Endorsed requests are sent to the Manager. Rejected requests are discarded.
          </div>
          <div style="display:flex; gap:12px;">
            <button type="button" class="rem-btn-send" id="btnRejectAllowance" style="background-color: var(--brand-red);">
                <i data-lucide="x-circle"></i> Reject
            </button>
            <button type="button" class="rem-btn-send" id="btnEndorseAllowance" style="background-color: var(--brand-green);">
                <i data-lucide="check-circle"></i> Endorse
            </button>
          </div>
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
              <p class="rem-subtitle">Review proposed salary adjustments before endorsing to the Manager.</p>
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
                
                <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                  <table class="comp-table" id="simulationDetailsTable" style="margin: 0; min-width: 900px;">
                    <thead>
                      <tr>
                        <th>Employee</th>
                        <th>Job Grade</th>
                        <th>Current Salary</th>
                        <th>Prop. %</th>
                        <th>Prop. Inc (₱)</th>
                        <th>New Salary</th>
                        <th>Promotion</th>
                      </tr>
                    </thead>
                    <tbody id="simulationDetailsBody">
                        <tr><td colspan="7" style="text-align:center;">Loading simulation details...</td></tr>
                    </tbody>
                  </table>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="rem-footer">
          <div class="rem-footer-hint">
            <i data-lucide="info"></i>
            Endorsing will forward this simulation to the Manager for budget approval.
          </div>
          <div style="display:flex; gap:12px;">
            <button type="button" class="rem-btn-send" id="btnRejectSimulation" style="background-color: #ef4444 !important; color: #ffffff !important;">
                <i data-lucide="x-circle"></i> Reject
            </button>
            <button type="button" class="rem-btn-send" id="btnEndorseSimulation" style="background-color: #2563eb !important; color: #ffffff !important;">
                <i data-lucide="check-circle"></i> Endorse Simulation
            </button>
          </div>
        </div>
      </div>
    </div>
  </main>
  <script src="../../js/notifications.js?v=1.1"></script>
  <script src="../../js/pending.js?v=1.5"></script>
  <script>
    lucide.createIcons();
  </script>
</body>






