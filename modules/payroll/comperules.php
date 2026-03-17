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
  <title>Compensation Rules</title>
  <link rel="stylesheet" href="../../css/comperules.css?v=1.3">
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

        <div class="nav-item-group active">
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

        <div class="nav-item-group">
          <button class="nav-item has-submenu" data-module="hr">
            <div class="nav-item-content">
              <i data-lucide="users"></i>
              <span>Core Human Capital</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-hr">
            <a href="../corehumancapital/employeemaster.php" class="submenu-item">
              <i data-lucide="file-user"></i>
              <span>Employee Master</span>
            </a>
            <a href="../corehumancapital/bankform.php" class="submenu-item">
              <i data-lucide="landmark"></i>
              <span>Bank Forms</span>
            </a>
          </div>
        </div>

        <div class="nav-item-group">
          <button class="nav-item has-submenu" data-module="compensation">
            <div class="nav-item-content">
              <i data-lucide="pie-chart"></i>
              <span>Compensation</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-compensation">
            <a href="../compensation/dashboard.php" class="submenu-item">
              <i data-lucide="layout-dashboard"></i>
              <span>Comp Dashboard</span>
            </a>
            <a href="../compensation/cycle.php" class="submenu-item">
              <i data-lucide="refresh-cw"></i>
              <span>Comp Cycles</span>
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
      <!-- Stats Grid -->
      <div class="stats-grid" style="margin-bottom: 32px; display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px;">
        <div class="stat-card-premium" style="background: var(--surface); padding: 24px; border-radius: 20px; border: 1px solid var(--border-color); display: flex; align-items: center; gap: 20px; transition: var(--transition);">
          <div class="stat-icon-wrapper" style="width: 48px; height: 48px; border-radius: 12px; background: rgba(44, 160, 120, 0.1); color: var(--brand-green); display: flex; align-items: center; justify-content: center;">
            <i data-lucide="scale"></i>
          </div>
          <div class="stat-info">
            <span class="stat-label" style="font-size: 11px; font-weight: 700; color: var(--text-tertiary); text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 4px;">Contribution Rules</span>
            <h3 class="stat-value" style="font-size: 22px; font-weight: 800; color: var(--text-primary); letter-spacing: -0.02em;">3 Active</h3>
          </div>
        </div>

        <div class="stat-card-premium" style="background: var(--surface); padding: 24px; border-radius: 20px; border: 1px solid var(--border-color); display: flex; align-items: center; gap: 20px; transition: var(--transition);">
          <div class="stat-icon-wrapper" style="width: 48px; height: 48px; border-radius: 12px; background: rgba(59, 130, 246, 0.1); color: #3b82f6; display: flex; align-items: center; justify-content: center;">
            <i data-lucide="percent"></i>
          </div>
          <div class="stat-info">
            <span class="stat-label" style="font-size: 11px; font-weight: 700; color: var(--text-tertiary); text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 4px;">Merit Matrices</span>
            <h3 class="stat-value" style="font-size: 22px; font-weight: 800; color: var(--text-primary); letter-spacing: -0.02em;">1 Pending</h3>
          </div>
        </div>

        <div class="stat-card-premium" style="background: var(--surface); padding: 24px; border-radius: 20px; border: 1px solid var(--border-color); display: flex; align-items: center; gap: 20px; transition: var(--transition);">
          <div class="stat-icon-wrapper" style="width: 48px; height: 48px; border-radius: 12px; background: rgba(255, 193, 7, 0.1); color: var(--brand-yellow); display: flex; align-items: center; justify-content: center;">
            <i data-lucide="file-check"></i>
          </div>
          <div class="stat-info">
            <span class="stat-label" style="font-size: 11px; font-weight: 700; color: var(--text-tertiary); text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 4px;">Policy Review</span>
            <h3 class="stat-value" style="font-size: 22px; font-weight: 800; color: var(--text-primary); letter-spacing: -0.02em;">Weekly</h3>
          </div>
        </div>

        <div class="stat-card-premium" style="background: var(--surface); padding: 24px; border-radius: 20px; border: 1px solid var(--border-color); display: flex; align-items: center; gap: 20px; transition: var(--transition);">
          <div class="stat-icon-wrapper" style="width: 48px; height: 48px; border-radius: 12px; background: rgba(139, 92, 246, 0.1); color: #8b5cf6; display: flex; align-items: center; justify-content: center;">
            <i data-lucide="clock"></i>
          </div>
          <div class="stat-info">
            <span class="stat-label" style="font-size: 11px; font-weight: 700; color: var(--text-tertiary); text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 4px;">Last Update</span>
            <h3 class="stat-value" style="font-size: 22px; font-weight: 800; color: var(--text-primary); letter-spacing: -0.02em;"><?php echo date('M d'); ?></h3>
          </div>
        </div>
      </div>

      <!-- Main Rules Table -->
      <div class="comperules-table-container" style="background: var(--surface); border: 1px solid var(--border-color); border-radius: 20px; overflow: hidden; box-shadow: var(--shadow-sm);">
        <div style="padding: 24px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
          <div>
            <h3 style="font-size: 18px; font-weight: 700; color: var(--text-primary); margin-bottom: 4px;">Active Compensation Policies</h3>
            <p style="font-size: 13px; color: var(--text-secondary);">Manage the rules used by the payroll engine for calculations.</p>
          </div>
          <button class="btn btn-primary" style="padding: 10px 20px; border-radius: 12px; display: flex; align-items: center; gap: 8px; background: var(--brand-green); color: white; border: none; font-weight: 600; cursor: pointer;">
            <i data-lucide="plus"></i>
            <span>Request Rule Update</span>
          </button>
        </div>
        
        <div style="overflow-x: auto;">
          <table class="comperules-table" style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
              <tr style="background: var(--background); color: var(--text-tertiary); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">
                <th style="padding: 16px 24px;">Rule Category</th>
                <th style="padding: 16px 24px;">Active Version</th>
                <th style="padding: 16px 24px;">Last Audit</th>
                <th style="padding: 16px 24px;">Status</th>
                <th style="padding: 16px 24px; text-align: right;">Operations</th>
              </tr>
            </thead>
            <tbody>
              <tr style="border-bottom: 1px solid var(--border-color); transition: var(--transition);">
                <td style="padding: 16px 24px;">
                  <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(44, 160, 120, 0.1); color: var(--brand-green); display: flex; align-items: center; justify-content: center;">
                      <i data-lucide="landmark"></i>
                    </div>
                    <div>
                      <div style="font-size: 14px; font-weight: 600; color: var(--text-primary);">SSS Contribution Table</div>
                      <div style="font-size: 12px; color: var(--text-tertiary);">Statutory Employee & Employer Share</div>
                    </div>
                  </div>
                </td>
                <td style="padding: 16px 24px; font-size: 13px; color: var(--text-primary); font-weight: 500;">v2026.1 (Current)</td>
                <td style="padding: 16px 24px; font-size: 13px; color: var(--text-secondary);">Feb 15, 2026</td>
                <td style="padding: 16px 24px;">
                  <span class="badge active" style="background: rgba(44, 160, 120, 0.1); color: var(--brand-green); border: 1px solid rgba(44, 160, 120, 0.2); padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; text-transform: uppercase;">Active</span>
                </td>
                <td style="padding: 16px 24px; text-align: right;">
                  <a href="../compensation/statutory.php" style="background: none; border: none; color: var(--text-tertiary); cursor: pointer; padding: 4px; text-decoration: none;"><i data-lucide="external-link" style="width: 18px;"></i></a>
                </td>
              </tr>
              <tr style="border-bottom: 1px solid var(--border-color); transition: var(--transition);">
                <td style="padding: 16px 24px;">
                  <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(59, 130, 246, 0.1); color: #3b82f6; display: flex; align-items: center; justify-content: center;">
                      <i data-lucide="target"></i>
                    </div>
                    <div>
                      <div style="font-size: 14px; font-weight: 600; color: var(--text-primary);">Merit Increase Matrix</div>
                      <div style="font-size: 12px; color: var(--text-tertiary);">Performance-based Salary Adjustments</div>
                    </div>
                  </div>
                </td>
                <td style="padding: 16px 24px; font-size: 13px; color: var(--text-primary); font-weight: 500;">FY2026 Strategy</td>
                <td style="padding: 16px 24px; font-size: 13px; color: var(--text-secondary);">Mar 01, 2026</td>
                <td style="padding: 16px 24px;">
                  <span class="badge pending" style="background: rgba(255, 193, 7, 0.1); color: var(--brand-yellow); border: 1px solid rgba(255, 193, 7, 0.2); padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; text-transform: uppercase;">Pending Approval</span>
                </td>
                <td style="padding: 16px 24px; text-align: right;">
                  <a href="../compensation/matrix.php" style="background: none; border: none; color: var(--text-tertiary); cursor: pointer; padding: 4px; text-decoration: none;"><i data-lucide="external-link" style="width: 18px;"></i></a>
                </td>
              </tr>
              <tr style="border-bottom: 1px solid var(--border-color); transition: var(--transition);">
                <td style="padding: 16px 24px;">
                  <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(139, 92, 246, 0.1); color: #8b5cf6; display: flex; align-items: center; justify-content: center;">
                      <i data-lucide="tag"></i>
                    </div>
                    <div>
                      <div style="font-size: 14px; font-weight: 600; color: var(--text-primary);">Salary Grade Brackets</div>
                      <div style="font-size: 12px; color: var(--text-tertiary);">Grade Levels Min/Mid/Max Midpoint</div>
                    </div>
                  </div>
                </td>
                <td style="padding: 16px 24px; font-size: 13px; color: var(--text-primary); font-weight: 500;">v2.4 (FY2025)</td>
                <td style="padding: 16px 24px; font-size: 13px; color: var(--text-secondary);">Dec 10, 2025</td>
                <td style="padding: 16px 24px;">
                  <span class="badge active" style="background: rgba(44, 160, 120, 0.1); color: var(--brand-green); border: 1px solid rgba(44, 160, 120, 0.2); padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; text-transform: uppercase;">Active</span>
                </td>
                <td style="padding: 16px 24px; text-align: right;">
                  <a href="../compensation/salary.php" style="background: none; border: none; color: var(--text-tertiary); cursor: pointer; padding: 4px; text-decoration: none;"><i data-lucide="external-link" style="width: 18px;"></i></a>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>
  <script src="../../js/comperules.js"></script>
  <script>
    lucide.createIcons();
  </script>
</body>
</html>







