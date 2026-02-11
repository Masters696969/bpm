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
  <title>Dashboard</title>
  <link rel="stylesheet" href="../../css/informationrq.css?v=1.2">
  <link rel="stylesheet" href="../../css/sidebar-fix.css?v=1.0">
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
          <i data-lucide="chart-no-axes-combined"></i>
          <span>HR ANALYTICS</span>
        </a>

        <div class="nav-item-group">
          <button class="nav-item has-submenu" data-module="hr">
            <div class="nav-item-content">
              <i data-lucide="book-user"></i>
              <span>Core Human Capital</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-hr">
            <a href="" class="submenu-item active">
              <i data-lucide="user-plus"></i>
              <span>New Hired Onboard Request</span>
            </a>
            <a href="employeemaster.php" class="submenu-item">
              <i data-lucide="file-user"></i>
              <span>Employee Master Files</span>
            </a>
             <a href="informationrq.php" class="submenu-item">
              <i data-lucide="user-round-pen"></i>
              <span>Information Request</span>
            </a>
            <a href="" class="submenu-item">
              <i data-lucide="user-cog"></i>
              <span>Security Settings</span>
            </a>
            <a href="auditlogs.php" class="submenu-item">
              <i data-lucide="book-user"></i>
              <span>Audit Logs</span>
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

           <div class="nav-item-group">
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
         <span class="nav-section-title">HR 1</span>
        <div class="nav-item-group">
          <button class="nav-item has-submenu" data-module="ess">
            <div class="nav-item-content">
              <i data-lucide="user-round"></i>
              <span>Employee Self Service</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-ess">
             <a href="#" class="submenu-item">
              <i data-lucide="file-clock"></i>
              <span>Time Attendance</span>
            </a>
            <a href="../ess/information_management.php" class="submenu-item">
              <i data-lucide="user-pen"></i>
              <span>Information Management</span>
            </a>
            <a href="#" class="submenu-item">
              <i data-lucide="tickets-plane"></i>
              <span>Leave Management</span>
            </a>
             <a href="#" class="submenu-item">
              <i data-lucide="receipt-text"></i>
              <span>Claim Management</span>
            </a>
            <a href="#" class="submenu-item">
              <i data-lucide="ticket-check"></i>
              <span>View Payslip</span>
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
        <a href="../../login.php" class="nav-item">
            <i data-lucide="log-out"></i>
            <span>Logout</span>
        </a>
      </div>
    </nav>

    <div class="sidebar-footer">
      <div class="user-profile">
        <div class="user-avatar">
          <img src="../../img/profile.png" alt="User">
        </div>
        <div class="user-info">
          <span class="user-name"><?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></span>
          <span class="user-role"><?php echo htmlspecialchars($_SESSION['user_role'] ?? 'Employee'); ?></span>
        </div>
        <button class="user-menu-btn">
          <i data-lucide="more-vertical"></i>
        </button>
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
        <div class="search-box">
          <i data-lucide="search"></i>
          <input type="search" placeholder="Search...">
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
                <div>
                    <h3 class="card-title">Pending Information Updates</h3>
                    <p class="card-subtitle">Review and approve employee information changes.</p>
                </div>
            </div>
            <div class="card-body">
                <div class="data-table">
                    <table id="requestsTable" style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="text-align: left; border-bottom: 1px solid var(--border-color);">
                                <th style="padding: 12px;">Employee</th>
                                <th style="padding: 12px;">Department</th>
                                <th style="padding: 12px;">Request Type</th>
                                <th style="padding: 12px;">Date Requested</th>
                                <th style="padding: 12px;">Status</th>
                                <th style="padding: 12px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="requestsTableBody">
                            <!-- Rows will be populated by JS -->
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 20px;">Loading requests...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- View/Approve Modal -->
    <div class="modal-overlay hidden" id="requestActionModal">
      <div class="modal-content modal-content-styled">
        <div class="modal-header-styled">
            <div>
                <h3 class="modal-title-custom">Review Request</h3>
                <p class="modal-subtitle-custom">Compare old and new values.</p>
            </div>
          <button id="btnCloseActionModal" class="close-modal-btn">
            <i data-lucide="x" class="icon-sm"></i> Close
          </button>
        </div>
        <div class="modal-body-scroll" id="requestDetailsBody">
            <!-- Dynamic Content -->
        </div>
        <div class="modal-footer-styled">
            <button type="button" class="btn-create-master" id="btnReject" style="background-color: #ef4444; margin-right: auto;">
                <i data-lucide="x-circle" class="icon-sm"></i> Reject
            </button>
            <button type="button" class="btn-create-master" id="btnApprove" style="background-color: #10b981;">
                <i data-lucide="check-circle" class="icon-sm"></i> Approve
            </button>
        </div>
      </div>
    </div>
  </main>
  <script src="../../js/sidebar-active.js"></script>
  <script src="../../js/informationrq.js"></script>
  <script>
    lucide.createIcons();
  </script>
  
</body>
</html>
