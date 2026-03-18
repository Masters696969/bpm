<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header('Location: ../../login.php');
  exit;
}
$page = 'employeemaster';
$module = 'corehumancapital';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Employee Master</title>
  <link rel="stylesheet" href="../../css/employeemaster.css?v=1.1">
  <script src="https://unpkg.com/lucide@latest"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="icon" type="image/png" href="../../img/logo.png">
</head>
<body>

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
        <span class="nav-section-title">ANALYTICS & REPORTING</span>
        <a href="dashboard.php" class="nav-item <?php echo($page === 'dashboard') ? 'active' : ''; ?>">
          <i data-lucide="layout-dashboard"></i>
          <span>HR ANALYTICS</span>
        </a>
      </div>

      <div class="nav-section">
        <span class="nav-section-title">ADMINISTRATION</span>
        <div class="nav-item-group active">
          <button class="nav-item has-submenu" data-module="accounts">
            <div class="nav-item-content">
              <i data-lucide="users"></i>
              <span>Account Management</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-accounts">
            <a href="useraccount.php" class="submenu-item active">
              <i data-lucide="user-plus"></i>
              <span>User Accounts</span>
            </a>
            <a href="rolespermission.php" class="submenu-item">
              <i data-lucide="contact-round"></i>
              <span>Roles & Permissions</span>
            </a>
            <a href="securitysetting.php" class="submenu-item">
              <i data-lucide="user-cog"></i>
              <span>Security Settings</span>
            </a>
            <a href="auditlogs.php" class="submenu-item">
              <i data-lucide="book-user"></i>
              <span>Audit Logs</span>
            </a>
          </div>
        </div>
      </div>

      <div class="nav-section">
        <span class="nav-section-title">Human Resources</span>
        <div class="nav-item-group <?php echo($module === 'corehumancapital') ? 'active' : ''; ?>">
          <button class="nav-item has-submenu" data-module="corehumancapital">
            <div class="nav-item-content">
              <i data-lucide="book-user"></i>
              <span>Core Human Capital</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-corehumancapital">
            <a href="dispatch.php" class="submenu-item <?php echo($page === 'dispatch') ? 'active' : ''; ?>">
              <i data-lucide="send"></i>
              <span>Master Data Dispatch</span>
            </a>
            <a href="orgprofile.php" class="submenu-item <?php echo($page === 'orgprofile') ? 'active' : ''; ?>">
              <i data-lucide="building-2"></i>
              <span>Organization Profile</span>
            </a>
            <a href="positioncatalog.php" class="submenu-item <?php echo($page === 'positioncatalog') ? 'active' : ''; ?>">
              <i data-lucide="user-star"></i>
              <span>Position Catalog</span>
            </a>
            <a href="employeemaster.php" class="submenu-item <?php echo($page === 'employeemaster') ? 'active' : ''; ?>">
              <i data-lucide="file-user"></i>
              <span>Employee Master Files</span>
            </a>
            <a href="informationapproval.php" class="submenu-item <?php echo($page === 'informationapproval') ? 'active' : ''; ?>">
              <i data-lucide="file-check"></i>
              <span>Information Approval</span>
            </a>
            <a href="bankform.php" class="submenu-item <?php echo($page === 'bankform') ? 'active' : ''; ?>">
              <i data-lucide="file-text"></i>
              <span>Bank Form Management</span>
            </a>
            <a href="auditlogs.php" class="submenu-item <?php echo($page === 'auditlogs') ? 'active' : ''; ?>">
              <i data-lucide="book-user"></i>
              <span>Audit Logs</span>
            </a>
          </div>
        </div>

        <div class="nav-item-group <?php echo($module === 'planning') ? 'active' : ''; ?>">
          <button class="nav-item has-submenu" data-module="planning">
            <div class="nav-item-content">
              <i data-lucide="circle-pile"></i>
              <span>Compensation Planning</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-planning">
            <a href="salary.php" class="submenu-item <?php echo($page === 'salarymgt') ? 'active' : ''; ?>">
              <i data-lucide="banknote"></i>
              <span>Salary & Scales Management</span>
            </a>
            <a href="statutory.php" class="submenu-item <?php echo($page === 'statutory') ? 'active' : ''; ?>">
              <i data-lucide="scale"></i>
              <span>Statutory Contributions</span>
            </a>
            <a href="matrix.php" class="submenu-item <?php echo($page === 'matrix') ? 'active' : ''; ?>">
              <i data-lucide="scale"></i>
              <span>Merit Matrix Structure</span>
            </a>
            <a href="cycle.php" class="submenu-item <?php echo($page === 'cycle') ? 'active' : ''; ?>">
              <i data-lucide="notebook-pen"></i>
              <span>Compensation Structure Management</span>
            </a>
          </div>
        </div>

        <div class="nav-item-group">
          <button class="nav-item has-submenu" data-module="payroll">
            <div class="nav-item-content">
              <i data-lucide="banknote"></i>
              <span>Payroll Management</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-payroll">
            <a href="comperules.php" class="submenu-item">
              <i data-lucide="boxes"></i>
              <span>Compensation Rules</span>
            </a>
            <a href="payroll.php" class="submenu-item active">
              <i data-lucide="play-circle"></i>
              <span>Payroll Processing</span>
            </a>
            <a href="#" class="submenu-item">
              <i data-lucide="history"></i>
              <span>Payroll History</span>
            </a>
            <a href="#" class="submenu-item">
              <i data-lucide="file-check"></i>
              <span>Approvals</span>
            </a>
          </div>
        </div>

        <a href="recruitment.php" class="nav-item <?php echo($page === 'recruitment') ? 'active' : ''; ?>">
          <i data-lucide="layers-plus"></i>
          <span>Recruitment</span>
        </a>

        <a href="applicationmgt.php" class="nav-item <?php echo($page === 'applicationmgt') ? 'active' : ''; ?>">
          <i data-lucide="contact-round"></i>
          <span>Applicant Management</span>
        </a>

        <a href="newhiredonboard.php" class="nav-item <?php echo($page === 'newhiredonboard') ? 'active' : ''; ?>">
          <i data-lucide="user-plus"></i>
          <span>New Hired Onboard</span>
        </a>
      </div>

      <div class="nav-section">
        <span class="nav-section-title">FINANCE</span>
        <div class="nav-item-group <?php echo($module === 'budget') ? 'active' : ''; ?>">
          <button class="nav-item has-submenu" data-module="budget">
            <div class="nav-item-content">
              <i data-lucide="hand-coins"></i>
              <span>Budget Management</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-budget">
            <a href="positionrequest.php" class="submenu-item <?php echo($page === 'positionrequest') ? 'active' : ''; ?>">
              <i data-lucide="badge-dollar-sign"></i>
              <span>Position Requests</span>
            </a>
            <a href="intake.php" class="submenu-item <?php echo($page === 'intake') ? 'active' : ''; ?>">
              <i data-lucide="send-to-back"></i>
              <span>Master Data Intake</span>
            </a>
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

  <main class="main-content">
    <header class="page-header">
      <div class="header-left">
        <button class="mobile-menu-btn" id="mobileMenuBtn">
          <i data-lucide="menu"></i>
        </button>
        <div class="header-title">
          <h1>Employee Master File</h1>
          <p>Full administrative access to all employee records.</p>
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

    <div class="em-content-wrapper">

      <div class="stats-strip">
        <div class="stat-card">
          <div class="stat-icon em-total">
            <i data-lucide="users"></i>
          </div>
          <div class="stat-info">
            <span class="stat-value" id="statTotal">—</span>
            <span class="stat-label">Total Employees</span>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon em-regular">
            <i data-lucide="user-check"></i>
          </div>
          <div class="stat-info">
            <span class="stat-value" id="statRegular">—</span>
            <span class="stat-label">Regular</span>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon em-probationary">
            <i data-lucide="hourglass"></i>
          </div>
          <div class="stat-info">
            <span class="stat-value" id="statProbationary">—</span>
            <span class="stat-label">Probationary</span>
          </div>
        </div>
      </div>

      <div class="content-card">
        <div class="card-header">
          <div class="card-header-left">
            <h3 class="card-title">All Employee Records</h3>
            <p class="card-subtitle">Manage employee profiles, compensation, and status.</p>
          </div>

          <div class="card-header-right" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
            <button
              type="button"
              id="toggleSelectionModeBtn"
              style="display:inline-flex;align-items:center;gap:8px;border:none;border-radius:10px;padding:10px 14px;background:linear-gradient(135deg, #2ca078, #4fb97a);;color:#fff;font-weight:600;cursor:pointer;">
              <i data-lucide="check-square"></i>
              <span id="toggleSelectionModeText">Select Employees</span>
            </button>

            <button
              type="button"
              id="dispatchSelectedBtn"
              style="display:none;align-items:center;gap:8px;border:none;border-radius:10px;padding:10px 14px;background:#2ca078;color:#fff;font-weight:600;cursor:pointer;">
              <i data-lucide="send"></i>
              <span>Dispatch Selected</span>
            </button>

            <label class="table-search">
              <i data-lucide="search"></i>
              <input type="text" id="empTableSearch" placeholder="Search employees…">
            </label>
          </div>
        </div>

        <div id="selectionToolbar" style="display:none;padding:12px 16px;border-top:1px solid #e5e7eb;background:#f8fafc;font-size:13px;color:#475569;">
          Selection mode is active. Click any row to select employees for dispatch.
        </div>

        <div class="card-body" style="padding:0;">
          <div class="table-responsive">
            <table class="users-table" id="employeeTable">
              <thead>
                <tr>
                  <th id="selectColumnHeader" style="width:50px; text-align:center; display:none;">
                    <input type="checkbox" id="selectAllEmployees" title="Select All">
                  </th>
                  <th>Employee</th>
                  <th>Position</th>
                  <th>Department</th>
                  <th>Status</th>
                  <th>Salary Grade</th>
                  <th>Dispatch Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>

    <div id="employeeModal" class="modal">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h3 id="modalTitle">Employee Profile</h3>
            <button class="close-modal" onclick="closeModal()">&times;</button>
          </div>
          <div class="modal-body" id="modalBody"></div>
        </div>
      </div>
    </div>

  </main>

  <script src="../../js/employeemaster.js"></script>
  <script>
    lucide.createIcons();

    document.getElementById('empTableSearch')?.addEventListener('input', function() {
      const q = this.value.toLowerCase();
      document.querySelectorAll('#employeeTable tbody tr').forEach(r => {
        r.style.display = r.innerText.toLowerCase().includes(q) ? '' : 'none';
      });
    });
  </script>
</body>
</html>