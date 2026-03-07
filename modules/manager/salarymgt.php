<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../../login.php");
    exit();
}
$page = 'salarymgt';
$module = 'planning';

require_once '../../config/config.php';
$sql_grades = "SELECT * FROM salary_grades ORDER BY GradeLevel ASC";
$res_grades = $conn->query($sql_grades);
$salary_grades = [];
if ($res_grades) {
    while ($row = $res_grades->fetch_assoc()) {
        $salary_grades[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manager Dashboard</title>
  <link rel="stylesheet" href="../../css/salaryscales.css?v=1.2">
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

        <div class="nav-item-group">
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

        <div class="nav-item-group">
          <button class="nav-item has-submenu" data-module="payroll">
            <div class="nav-item-content">
              <i data-lucide="banknote-arrow-down"></i>
              <span>Payroll</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-payroll">
            <a href="#" class="submenu-item"><i data-lucide="file-plus"></i><span>Applications</span></a>
            <a href="#" class="submenu-item"><i data-lucide="check-circle"></i><span>Approvals</span></a>
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
                    <h3 class="card-title">Current Salary Scales</h3>
                    <p class="card-subtitle">Active compensation ranges across the organization.</p>
                </div>
            </div>
            <div class="card-body">
                <div class="data-table">
                    <table id="currentScalesTable">
                        <thead>
                            <tr>
                                <th>Job Grade</th>
                                <th>Name</th>
                                <th>Minimum</th>
                                <th>Midpoint</th>
                                <th>Maximum</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($salary_grades as $grade): 
                                $mid = ($grade['MinSalary'] + $grade['MaxSalary']) / 2;
                            ?>
                            <tr data-id="<?php echo $grade['SalaryGradeID']; ?>">
                                <td><strong><?php echo htmlspecialchars($grade['GradeLevel']); ?></strong></td>
                                <td><?php echo htmlspecialchars($grade['GradeName']); ?></td>
                                <td>&#8369;<?php echo number_format($grade['MinSalary'], 2); ?></td>
                                <td><span class="text-muted">&#8369;<?php echo number_format($mid, 2); ?></span></td>
                                <td>&#8369;<?php echo number_format($grade['MaxSalary'], 2); ?></td>
                                <td>
                                    <?php if($grade['IsActive']): ?>
                                        <span class="badge badge-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Inactive</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if(empty($salary_grades)): ?>
                            <tr><td colspan="6" style="text-align:center;">No salary grades found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Endorsed Proposals Table -->
        <div class="content-card" style="margin-top: 24px;">
            <div class="card-header">
                <div class="card-header-left">
                    <h3 class="card-title">Salary Scale Proposals - Endorsed</h3>
                    <p class="card-subtitle">Proposals endorsed by Supervisors awaiting your review before Finance.</p>
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
            <div class="rem-icon-box" style="background:var(--brand-green-light); color:var(--brand-green);">
              <i data-lucide="git-pull-request"></i>
            </div>
            <div class="rem-title-group">
              <h3 class="rem-title">Review Salary Scale Proposal</h3>
              <p class="rem-subtitle">Review endorsed adjustments before forwarding to Finance.</p>
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
            <i data-lucide="info" style="color:var(--brand-blue);"></i>
            Approved requests will be forwarded to the Finance department for final application.
          </div>
          <div style="display:flex; gap:12px;">
            <button type="button" class="rem-btn-send rem-btn-secondary" id="btnRejectProposal">
                <i data-lucide="x-circle" style="color:var(--brand-red);"></i> Reject
            </button>
            <button type="button" class="rem-btn-send rem-btn-blue" id="btnEndorseProposal">
                <i data-lucide="check-circle"></i> Approve & Send to Finance
            </button>
          </div>
        </div>
      </div>
    </div>
  </main>
  <script src="../../js/notifications.js?v=1.2"></script>
  <script src="../../js/salaryscales.js?v=<?php echo time(); ?>"></script>
</body>
</html>
