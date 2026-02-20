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
  <link rel="stylesheet" href="../../css/applybank.css?v=1.2">
  <script src="https://unpkg.com/lucide@latest"></script>
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
          <span>Dashboard</span>
        </a>

        <a href="#" class="nav-item">
              <i data-lucide="file-clock"></i>
              <span>Time Attendance</span>
            </a>
            <a href="information_management.php" class="nav-item">
              <i data-lucide="user-pen"></i>
              <span>Information Management</span>
            </a>
            <a href="#" class="nav-item">
              <i data-lucide="tickets-plane"></i>
              <span>Leave Management</span>
            </a>
             <a href="#" class="nav-item">
              <i data-lucide="receipt-text"></i>
              <span>Claim Management</span>
            </a>
            <a href="#" class="nav-item">
              <i data-lucide="ticket-check"></i>
              <span>View Payslip</span>
            </a>

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
          <span class="user-name">John Doe</span>
          <span class="user-role">Administrator</span>
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
          <p>Welcome back, John! Here's what's happening today.</p>
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
  <script src="../../js/applybank.js"></script>
  <script>
    lucide.createIcons();
  </script>
  
</body>
</html>
