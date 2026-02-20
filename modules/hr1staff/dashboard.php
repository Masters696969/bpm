<?php
session_start();
if (empty($_SESSION['user_id']) || strtolower($_SESSION['user_role'] ?? '') !== 'hr staff') {
  header("Location: /microfinance/login.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link rel="stylesheet" href="../../css/staffdashboard.css?v=1.2">
  <script src="https://unpkg.com/lucide@latest"></script>
  <link rel="icon" type="image/png" href="../../img/logo.png">
</head>
<body>

  <!-- Sidebar -->
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
      <div class="logo-container">
        <div class="logo-wrapper">
          <img src=" ../../img/logo.png" alt="Logo" class="logo">
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
        
        <a href="#" class="nav-item active">
          <i data-lucide="layout-dashboard"></i>
          <span>Dashboard</span>
        </a>

        <div class="nav-item-group">
          <button class="nav-item has-submenu" data-module="hr">
            <div class="nav-item-content">
              <i data-lucide="users"></i>
              <span>Human Resources</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-hr">
            <a href="payroll.html" class="submenu-item">
              <i data-lucide="wallet"></i>
              <span>Payroll</span>
            </a>
            <a href="compensation.html" class="submenu-item">
              <i data-lucide="trending-up"></i>
              <span>Compensation Planning</span>
            </a>
            <a href="#" class="submenu-item">
              <i data-lucide="user-circle"></i>
              <span>Core Human Capital</span>
            </a>
            <a href="#" class="submenu-item">
              <i data-lucide="bar-chart-3"></i>
              <span>HR Analytics</span>
            </a>
          </div>
        </div>

        <div class="nav-item-group">
          <button class="nav-item has-submenu" data-module="finance">
            <div class="nav-item-content">
              <i data-lucide="banknote"></i>
              <span>Finance</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-finance">
            <a href="#" class="submenu-item">
              <i data-lucide="receipt"></i>
              <span>Accounting</span>
            </a>
            <a href="#" class="submenu-item">
              <i data-lucide="file-text"></i>
              <span>Invoicing</span>
            </a>
            <a href="#" class="submenu-item">
              <i data-lucide="pie-chart"></i>
              <span>Budget Planning</span>
            </a>
          </div>
        </div>

        <div class="nav-item-group">
          <button class="nav-item has-submenu" data-module="loans">
            <div class="nav-item-content">
              <i data-lucide="hand-coins"></i>
              <span>Loan Management</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-loans">
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

        <a href="#" class="nav-item">
          <i data-lucide="users-round"></i>
          <span>Clients</span>
        </a>

        <a href="#" class="nav-item">
          <i data-lucide="file-bar-chart"></i>
          <span>Reports</span>
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

        <a href="../../logout.php" class="nav-item" onclick="return confirm ('Are you sure you want to log out?')">
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
          <span class="user-role"><?php echo htmlspecialchars(ucwords($_SESSION['user_role'] ?? '')); ?></span>
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
     
    </div>
  </main>
  <script src="../../js/staffdashboard.js"></script>
  <script>
    lucide.createIcons();
  </script>
  
</body>
</html>
