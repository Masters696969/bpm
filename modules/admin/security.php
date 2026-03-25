<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../../login.php");
    exit();
}

require_once '../../config/config.php';

// Handle immune account management
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add_immune' && !empty($_POST['email'])) {
            $email = trim($_POST['email']);
            
            // Create immune accounts table if not exists
            $createTableSql = "CREATE TABLE IF NOT EXISTS `immune_accounts` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `email` varchar(255) NOT NULL UNIQUE,
                `reason` varchar(255) DEFAULT NULL,
                `created_by` varchar(255) DEFAULT NULL,
                `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
                PRIMARY KEY (`id`),
                KEY `idx_email` (`email`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
            $conn->query($createTableSql);
            
            // Insert immune account
            $insertSql = "INSERT INTO immune_accounts (email, reason, created_by) VALUES (?, ?, ?) 
                         ON DUPLICATE KEY UPDATE reason = VALUES(reason), created_by = VALUES(created_by)";
            $stmt = $conn->prepare($insertSql);
            $stmt->bind_param("sss", $email, $_POST['reason'] ?? 'Demo account', $_SESSION['username']);
            $stmt->execute();
            
            $message = "Immune account added successfully!";
        }
        
        if ($_POST['action'] === 'remove_immune' && !empty($_POST['email'])) {
            $email = trim($_POST['email']);
            $deleteSql = "DELETE FROM immune_accounts WHERE email = ?";
            $stmt = $conn->prepare($deleteSql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            
            $message = "Immune account removed successfully!";
        }
        
        if ($_POST['action'] === 'unban_user' && !empty($_POST['email'])) {
            $email = trim($_POST['email']);
            
            // Clear recent failed attempts for this user/IP
            $clearAttemptsSql = "DELETE FROM login_attempts WHERE email = ? OR ip_address IN (SELECT ip_address FROM login_bans WHERE email = ?)";
            $clearStmt = $conn->prepare($clearAttemptsSql);
            $clearStmt->bind_param("ss", $email, $email);
            $clearStmt->execute();
            
            // Set ban as inactive
            $updateSql = "UPDATE login_bans SET is_active = 0 WHERE email = ?";
            $stmt = $conn->prepare($updateSql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            
            $message = "User unbanned successfully! All recent failed attempts cleared.";
        }
        
        if ($_POST['action'] === 'clear_attempts' && !empty($_POST['email'])) {
            $email = trim($_POST['email']);
            $deleteSql = "DELETE FROM login_attempts WHERE email = ?";
            $stmt = $conn->prepare($deleteSql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            
            $message = "Login attempts cleared successfully!";
        }
    }
}

// Get data for display
$loginAttemptsSql = "SELECT DISTINCT la.*
                    FROM login_attempts la
                    INNER JOIN login_bans lb ON (la.email = lb.email OR la.ip_address = lb.ip_address)
                    WHERE lb.is_active = 1
                    ORDER BY la.attempt_time DESC 
                    LIMIT 100";
$loginAttempts = $conn->query($loginAttemptsSql);

$activeBansSql = "SELECT lb.*
                   FROM login_bans lb
                   WHERE lb.is_active = 1
                   ORDER BY lb.ban_time DESC";
$activeBans = $conn->query($activeBansSql);

// Remove immune accounts section entirely
$immuneAccounts = null;

// Get statistics
$totalAttemptsSql = "SELECT COUNT(*) as total FROM login_attempts";
$totalAttempts = $conn->query($totalAttemptsSql)->fetch_assoc()['total'];

$failedAttemptsSql = "SELECT COUNT(*) as total FROM login_attempts WHERE success = 0";
$failedAttempts = $conn->query($failedAttemptsSql)->fetch_assoc()['total'];

$activeBansCountSql = "SELECT COUNT(*) as total FROM login_bans WHERE is_active = 1";
$activeBansCount = $conn->query($activeBansCountSql)->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Security Settings</title>
  <link rel="stylesheet" href="../../css/security.css?v=<?php echo time(); ?>">
  <script src="https://unpkg.com/lucide@latest"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="icon" type="image/png" href="../../img/logo.png">
  
  <!-- Critical backup styles -->
  <style>
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
    .stat-card { background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.1); display: flex; align-items: center; gap: 1rem; border: 1px solid #e2e8f0; }
    .stat-icon { width: 50px; height: 50px; border-radius: 10px; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
    .stat-content h3 { margin: 0; font-size: 2rem; font-weight: 700; color: #2d3748; }
    .stat-content p { margin: 0; color: #718096; font-size: 0.9rem; }
    .section { background: white; border-radius: 12px; padding: 1.5rem; margin-bottom: 2rem; box-shadow: 0 2px 8px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; }
    .section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid #e2e8f0; }
    .section-header h2 { display: flex; align-items: center; gap: 0.5rem; margin: 0; color: #2d3748; }
    .data-table { width: 100%; border-collapse: collapse; font-size: 0.9rem; }
    .data-table th, .data-table td { padding: 0.75rem; text-align: left; border-bottom: 1px solid #e2e8f0; }
    .data-table th { background: #f7fafc; font-weight: 600; color: #2d3748; }
    .badge { padding: 0.25rem 0.5rem; border-radius: 20px; font-size: 0.8rem; font-weight: 500; }
    .badge-success { background: #c6f6d5; color: #22543d; }
    .badge-danger { background: #fed7d7; color: #742a2a; }
    .text-center { text-align: center; color: #718096; }
    .alert { padding: 1rem; border-radius: 6px; margin-bottom: 1rem; }
    .alert-success { background: #c6f6d5; color: #22543d; border: 1px solid #9ae6b4; }
    .tab-filters { display: flex; gap: 0.5rem; margin-bottom: 1.5rem; border-bottom: 2px solid #e2e8f0; }
    .tab-btn { padding: 0.75rem 1.5rem; background: transparent; border: none; border-radius: 8px 8px 0 0; cursor: pointer; font-size: 0.9rem; font-weight: 500; color: #718096; transition: all 0.2s; }
    .tab-btn:hover { color: #2d3748; background: #f7fafc; }
    .tab-btn.active { color: #2ca078; background: white; border-bottom: 2px solid #2ca078; margin-bottom: -2px; }
    .tab-content { display: none; }
    .tab-content.active { display: block; }
    .user-agent { max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    
    /* Button Styles */
    .btn { 
      display: inline-flex; 
      align-items: center; 
      gap: 0.5rem; 
      padding: 0.5rem 1rem; 
      border: none; 
      border-radius: 6px; 
      font-size: 0.875rem; 
      font-weight: 500; 
      cursor: pointer; 
      transition: all 0.2s ease; 
      text-decoration: none; 
    }
    .btn-sm { 
      padding: 0.375rem 0.75rem; 
      font-size: 0.8rem; 
    }
    .btn-warning { 
      background: linear-gradient(135deg, #f6ad55 0%, #ed8936 100%); 
      color: white; 
      box-shadow: 0 2px 4px rgba(237, 137, 54, 0.2); 
    }
    .btn-warning:hover { 
      background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%); 
      transform: translateY(-1px); 
      box-shadow: 0 4px 8px rgba(237, 137, 54, 0.3); 
    }
    .btn-warning:active { 
      transform: translateY(0); 
      box-shadow: 0 1px 2px rgba(237, 137, 54, 0.2); 
    }
    .btn i { 
      width: 16px; 
      height: 16px; 
    }
  </style>
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
        <span class="nav-section-title">ANALYTICS & REPORTING</span>
        <a href="dashboard.php" class="nav-item active">
          <i data-lucide="layout-dashboard"></i>
          <span>HR ANALYTICS</span>
        </a>
      <div class="nav-section">
        <span class="nav-section-title">HUMAN RESOURCES I</span>
        <div class="nav-item-group active">
          <button class="nav-item has-submenu" data-module="recruitment">
            <div class="nav-item-content">
              <i data-lucide="layers-plus"></i>
              <span>Recruitment</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-recruitment">
           <a href="recruitment.php" class="nav-item <?php echo ($page === 'recruitment') ? 'active' : ''; ?>">
              <i data-lucide="layers-plus"></i>
              <span>Recruitment</span>
            </a>
          </div>
          <div class="nav-item-group active">
          <button class="nav-item has-submenu" data-module="applicationmgt">
            <div class="nav-item-content">
              <i data-lucide="contact-round"></i>
              <span>Applicant Management</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-applicationmgt">
            <a href="applicationmgt.php" class="nav-item <?php echo ($page === 'applicationmgt') ? 'active' : ''; ?>">
              <i data-lucide="contact-round"></i>
              <span>Applicant Management</span>
            </a>
          </div>
          <div class="nav-item-group active">
          <button class="nav-item has-submenu" data-module="newhiredonboard">
            <div class="nav-item-content">
              <i data-lucide="user-plus"></i>
              <span>New Hired Onboard</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-newhiredonboard">
            <a href="newhiredonboard.php" class="nav-item <?php echo ($page === 'newhiredonboard') ? 'active' : ''; ?>">
              <i data-lucide="user-plus"></i>
              <span>New Hired Onboard</span>
            </a>
          </div>
        </div>
        <div class="nav-section">
        <span class="nav-section-title">HUMAN RESOURCES II</span>
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
           <div class="nav-item-group <?php echo ($module === 'competency') ? 'active' : ''; ?>">
            <button class="nav-item has-submenu" data-module="competency">
              <div class="nav-item-content">
                <i data-lucide="pickaxe"></i>
                <span>Competency Management</span>
              </div>
              <i data-lucide="chevron-down" class="submenu-icon"></i>
            </button>
            <div class="submenu" id="submenu-competency">
              <a href="competencylibrary.php" class="submenu-item <?php echo ($page === 'competency') ? 'active' : ''; ?>">
                <i data-lucide="book-text"></i>
                <span>Competency Library</span>
              </a>
              <a href="competencycategory.php" class="submenu-item <?php echo ($page === 'competencycategory') ? 'active' : ''; ?>">
                <i data-lucide="chart-bar-stacked"></i>
                <span>Competency Category</span>
              </a>
               <a href="competencylevel.php" class="submenu-item <?php echo ($page === 'competencylevel') ? 'active' : ''; ?>">
                <i data-lucide="circle-gauge"></i>
                <span>Competency Level</span>
              </a>
              <a href="competencyposition.php" class="submenu-item <?php echo ($page === 'competencyposition') ? 'active' : ''; ?>">
                <i data-lucide="briefcase"></i>
                <span>Competency Position</span>
              </a>
              <a href="competencyemployee.php" class="submenu-item <?php echo ($page === 'competencyemployee') ? 'active' : ''; ?>">
                <i data-lucide="square-user"></i>
                <span>Competency Employee</span>
              </a>
                <a href="bankquestion.php" class="submenu-item <?php echo ($page === 'bankquestion') ? 'active' : ''; ?>">
                <i data-lucide="book-open-check"></i>
                <span>Bank Question</span>
              </a>
            </div>
        </div>
         <div class="nav-item-group <?php echo ($module === 'training') ? 'active' : ''; ?>">
            <button class="nav-item has-submenu" data-module="training">
              <div class="nav-item-content">
                <i data-lucide="briefcase-business"></i>
                <span>Training Management</span>
              </div>
              <i data-lucide="chevron-down" class="submenu-icon"></i>
            </button>
            <div class="submenu" id="submenu-training">
              <a href="training.php" class="submenu-item <?php echo ($page === 'training') ? 'active' : ''; ?>">
                <i data-lucide="briefcase-business"></i>
                <span>Training Management</span>
              </a>
            </div>
        </div>
         <div class="nav-item-group <?php echo ($module === 'succession') ? 'active' : ''; ?>">
            <button class="nav-item has-submenu" data-module="succession">
              <div class="nav-item-content">
                <i data-lucide="notebook-pen"></i>
                <span>Succession Planning</span>
              </div>
              <i data-lucide="chevron-down" class="submenu-icon"></i>
            </button>
            <div class="submenu" id="submenu-succession">
              <a href="succession.php" class="submenu-item <?php echo ($page === 'succession') ? 'active' : ''; ?>">
                <i data-lucide="notebook-pen"></i>
                <span>Succession Planning</span>
              </a>
            </div>
        </div>
         <div class="nav-item-group <?php echo ($module === 'learning') ? 'active' : ''; ?>">
            <button class="nav-item has-submenu" data-module="learning">
              <div class="nav-item-content">
                <i data-lucide="notebook-text"></i>
                <span>Learning Management</span>
              </div>
              <i data-lucide="chevron-down" class="submenu-icon"></i>
            </button>
            <div class="submenu" id="submenu-learning">
              <a href="learning.php" class="submenu-item <?php echo ($page === 'learning') ? 'active' : ''; ?>">
                <i data-lucide="notebook-text"></i>
                <span>Learning Management</span>
              </a>
            </div>
        </div>
          <div class="nav-section">   
            <span class="nav-section-title">HUMAN RESOURCES III</span>
          <div class="nav-item-group <?php echo ($module === 'shift') ? 'active' : ''; ?>">
            <button class="nav-item has-submenu" data-module="shift">
              <div class="nav-item-content">
                <i data-lucide="calendar-check"></i>
                <span>Shift & Scheduling</span>
              </div>
              <i data-lucide="chevron-down" class="submenu-icon"></i>
            </button>
            <div class="submenu" id="submenu-shift">
              <a href="#" class="submenu-item <?php echo ($page === 'shift') ? 'active' : ''; ?>">
                <i data-lucide="send-to-back"></i>
                <span>Shift & Scheduling</span>
              </a>
            </div>
            <div class="nav-item-group <?php echo ($module === 'claims') ? 'active' : ''; ?>">
            <button class="nav-item has-submenu" data-module="claims">
              <div class="nav-item-content">
                <i data-lucide="receipt-text"></i>
                <span>Claims & Reimbursements</span>
              </div>
              <i data-lucide="chevron-down" class="submenu-icon"></i>
            </button>
            <div class="submenu" id="submenu-claims">
              <a href="claims.php" class="submenu-item <?php echo ($page === 'claims') ? 'active' : ''; ?>">
                <i data-lucide="receipt-text"></i>
                <span>Claims & Reimbursements</span>
              </a>
            </div>
            <div class="nav-item-group <?php echo ($module === 'time') ? 'active' : ''; ?>">
            <button class="nav-item has-submenu" data-module="time">
              <div class="nav-item-content">
                <i data-lucide="clock"></i>
                <span>Time & Attendance</span>
              </div>
              <i data-lucide="chevron-down" class="submenu-icon"></i>
            </button>
            <div class="submenu" id="submenu-time">
              <a href="time.php" class="submenu-item <?php echo ($page === 'time') ? 'active' : ''; ?>">
                <i data-lucide="clock"></i>
                <span>Time & Attendance</span>
              </a>
            </div>
            <div class="nav-item-group <?php echo ($module === 'timesheet') ? 'active' : ''; ?>">
            <button class="nav-item has-submenu" data-module="timesheet">
              <div class="nav-item-content">
                <i data-lucide="calendar-days"></i>
                <span>Timesheet</span>
              </div>
              <i data-lucide="chevron-down" class="submenu-icon"></i>
            </button>
            <div class="submenu" id="submenu-timesheet">
              <a href="timesheet.php" class="submenu-item <?php echo ($page === 'timesheet') ? 'active' : ''; ?>">
                <i data-lucide="calendar-days"></i>
                <span>Timesheet</span>
              </a>
            </div>
             <div class="nav-item-group <?php echo ($module === 'leave') ? 'active' : ''; ?>">
            <button class="nav-item has-submenu" data-module="leave">
              <div class="nav-item-content">
                <i data-lucide="tickets-plane"></i>
                <span>Leave Management</span>
              </div>
              <i data-lucide="chevron-down" class="submenu-icon"></i>
            </button>
            <div class="submenu" id="submenu-leave">
              <a href="leave.php" class="submenu-item <?php echo ($page === 'leave') ? 'active' : ''; ?>">
                <i data-lucide="tickets-plane"></i>
                <span>Leave Management</span>
              </a>
            </div>
       <div class="nav-section">
        <span class="nav-section-title">HUMAN RESOURCES IV</span>
          <div class="nav-item-group <?php echo ($module === 'corehumancapital') ? 'active' : ''; ?>">
          <button class="nav-item has-submenu" data-module="corehumancapital">
            <div class="nav-item-content">
              <i data-lucide="book-user"></i>
              <span>Core Human Capital</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-corehumancapital">
            <a href="dispatch.php" class="submenu-item <?php echo ($page === 'dispatch') ? 'active' : ''; ?>">
              <i data-lucide="send"></i>
              <span>Master Data Dispatch</span>
            </a>
             <a href="orgprofile.php" class="submenu-item <?php echo ($page === 'orgprofile') ? 'active' : ''; ?>">
              <i data-lucide="building-2"></i>
              <span>Organization Profile</span>
            </a>
            <a href="positioncatalog.php" class="submenu-item <?php echo ($page === 'positioncatalog') ? 'active' : ''; ?>">
              <i data-lucide="user-star"></i>
              <span>Position Catalog</span>
            </a>
            <a href="employeemaster.php" class="submenu-item <?php echo ($page === 'employeemaster') ? 'active' : ''; ?>">
              <i data-lucide="file-user"></i>
              <span>Employee Master Files</span>
            </a>
            <a href="informationapproval.php" class="submenu-item <?php echo ($page === 'informationapproval') ? 'active' : ''; ?>">
              <i data-lucide="file-check"></i>
              <span>Information Approval</span>
            </a>
            <a href="bankform.php" class="submenu-item <?php echo ($page === 'bankform') ? 'active' : ''; ?>">
              <i data-lucide="file-text"></i>
              <span>Bank Form Management</span>
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
           <a href="comintake.php" class="submenu-item <?php echo ($page === 'intake') ? 'active' : ''; ?>">
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
        </div>
        <div class="nav-section">
        <span class="nav-section-title">FINANCE</span>
        
        <div class="nav-item-group <?php echo ($module === 'budget') ? 'active' : ''; ?>">
          <button class="nav-item has-submenu" data-module="budget">
            <div class="nav-item-content">
              <i data-lucide="hand-coins"></i>
              <span>Budget Management</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-budget">
            <a href="positionrequest.php" class="submenu-item <?php echo ($page === 'positionrequest') ? 'active' : ''; ?>">
              <i data-lucide="badge-dollar-sign"></i>
              <span>Position Requests</span>
            </a>
          </div>

      <div class="nav-section">
        <span class="nav-section-title">SETTINGS</span>
        
        <a href="#" class="nav-item">
          <i data-lucide="settings"></i>
          <span>Configuration</span>
        </a>

        <a href="#" class="nav-item active">
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
          <h1>Security Management</h1>
          <p>Manage login attempts, bans, and security settings</p>
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
      <?php if (isset($message)): ?>
        <div class="alert alert-success">
          <?php echo htmlspecialchars($message); ?>
        </div>
      <?php endif; ?>

      <!-- Statistics Cards -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon">
            <i data-lucide="user-check"></i>
          </div>
          <div class="stat-content">
            <h3><?php echo number_format($totalAttempts); ?></h3>
            <p>Total Login Attempts</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">
            <i data-lucide="user-x"></i>
          </div>
          <div class="stat-content">
            <h3><?php echo number_format($failedAttempts); ?></h3>
            <p>Failed Attempts</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">
            <i data-lucide="shield-x"></i>
          </div>
          <div class="stat-content">
            <h3><?php echo number_format($activeBansCount); ?></h3>
            <p>Active Bans</p>
          </div>
        </div>
      </div>

      <!-- Combined Security Table with Tabs -->
      <div class="section">
        <div class="section-header">
          <h2><i data-lucide="shield-x"></i> Security Monitor</h2>
        </div>
        
        <!-- Tab Filters -->
        <div class="tab-filters">
          <button class="tab-btn active" onclick="showTab('active-bans')">
            <i data-lucide="shield-x"></i> Active Bans
          </button>
          <button class="tab-btn" onclick="showTab('recent-attempts')">
            <i data-lucide="clock"></i> Recent Login Attempts
          </button>
          <button class="tab-btn" onclick="showTab('banned-attempts')">
            <i data-lucide="alert-triangle"></i> Banned Account Attempts
          </button>
        </div>

        <!-- Active Bans Tab -->
        <div id="active-bans" class="tab-content active">
          <div class="table-container">
            <table class="data-table">
              <thead>
                <tr>
                  <th>Email</th>
                  <th>IP Address</th>
                  <th>Ban Reason</th>
                  <th>Attempts</th>
                  <th>Ban Time</th>
                  <th>Lift Time</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                // Reset pointer and fetch active bans
                $activeBans->data_seek(0);
                if ($activeBans->num_rows > 0): 
                    while ($ban = $activeBans->fetch_assoc()): 
                ?>
                  <tr>
                    <td><?php echo htmlspecialchars($ban['email'] ?: 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($ban['ip_address'] ?: 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($ban['ban_reason']); ?></td>
                    <td><?php echo $ban['attempts_count']; ?></td>
                    <td><?php echo date('M d, Y H:i', strtotime($ban['ban_time'])); ?></td>
                    <td><?php echo date('M d, Y H:i', strtotime($ban['lift_time'])); ?></td>
                    <td>
                      <button class="btn btn-sm btn-warning" onclick="unbanUser('<?php echo htmlspecialchars($ban['email']); ?>')">
                        <i data-lucide="unlock"></i>
                        Unban
                      </button>
                    </td>
                  </tr>
                <?php 
                    endwhile; 
                else: 
                ?>
                  <tr>
                    <td colspan="7" class="text-center">No active bans found</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Recent Login Attempts Tab -->
        <div id="recent-attempts" class="tab-content">
          <div class="table-container">
            <table class="data-table">
              <thead>
                <tr>
                  <th>Email</th>
                  <th>IP Address</th>
                  <th>Success</th>
                  <th>Attempt Time</th>
                  <th>User Agent</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                // Get all recent attempts (not just banned)
                $allAttemptsSql = "SELECT * FROM login_attempts ORDER BY attempt_time DESC LIMIT 100";
                $allAttempts = $conn->query($allAttemptsSql);
                
                if ($allAttempts->num_rows > 0): 
                    while ($attempt = $allAttempts->fetch_assoc()): 
                ?>
                  <tr>
                    <td><?php echo htmlspecialchars($attempt['email']); ?></td>
                    <td><?php echo htmlspecialchars($attempt['ip_address']); ?></td>
                    <td>
                      <span class="badge <?php echo $attempt['success'] ? 'badge-success' : 'badge-danger'; ?>">
                        <?php echo $attempt['success'] ? 'Success' : 'Failed'; ?>
                      </span>
                    </td>
                    <td><?php echo date('M d, Y H:i:s', strtotime($attempt['attempt_time'])); ?></td>
                    <td class="user-agent"><?php echo htmlspecialchars(substr($attempt['user_agent'], 0, 50)) . '...'; ?></td>
                  </tr>
                <?php 
                    endwhile; 
                else: 
                ?>
                  <tr>
                    <td colspan="5" class="text-center">No login attempts found</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Banned Account Attempts Tab -->
        <div id="banned-attempts" class="tab-content">
          <div class="table-container">
            <table class="data-table">
              <thead>
                <tr>
                  <th>Email</th>
                  <th>IP Address</th>
                  <th>Success</th>
                  <th>Attempt Time</th>
                  <th>User Agent</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                // Reset pointer and fetch login attempts
                $loginAttempts->data_seek(0);
                if ($loginAttempts->num_rows > 0): 
                    while ($attempt = $loginAttempts->fetch_assoc()): 
                ?>
                  <tr>
                    <td><?php echo htmlspecialchars($attempt['email']); ?></td>
                    <td><?php echo htmlspecialchars($attempt['ip_address']); ?></td>
                    <td>
                      <span class="badge <?php echo $attempt['success'] ? 'badge-success' : 'badge-danger'; ?>">
                        <?php echo $attempt['success'] ? 'Success' : 'Failed'; ?>
                      </span>
                    </td>
                    <td><?php echo date('M d, Y H:i:s', strtotime($attempt['attempt_time'])); ?></td>
                    <td class="user-agent"><?php echo htmlspecialchars(substr($attempt['user_agent'], 0, 50)) . '...'; ?></td>
                  </tr>
                <?php 
                    endwhile; 
                else: 
                ?>
                  <tr>
                    <td colspan="5" class="text-center">No banned account attempts found</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Add Immune Account Modal -->
  <div class="modal" id="addImmuneModal">
    <div class="modal-content">
      <div class="modal-header">
        <h3><i data-lucide="shield-check"></i> Add Immune Account</h3>
        <button class="modal-close" onclick="hideAddImmuneModal()">
          <i data-lucide="x"></i>
        </button>
      </div>
      <form method="POST" class="modal-body">
        <input type="hidden" name="action" value="add_immune">
        
        <div class="form-group">
          <label for="email">Email Address</label>
          <input type="email" id="email" name="email" required 
                 value="suruiz.joshuabcp@gmail.com" class="form-control">
          <small>This account will bypass all login attempt limits</small>
        </div>
        
        <div class="form-group">
          <label for="reason">Reason</label>
          <textarea id="reason" name="reason" class="form-control" rows="3" 
                    placeholder="Demo account for testing purposes">Demo account for testing purposes</textarea>
        </div>
        
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" onclick="hideAddImmuneModal()">
            Cancel
          </button>
          <button type="submit" class="btn btn-primary">
            <i data-lucide="shield-check"></i>
            Add Immune Account
          </button>
        </div>
      </form>
    </div>
  </div>

  <script src="../../js/security.js"></script>
  <script src="../../js/session_timeout.js"></script>
  <script>
    lucide.createIcons();
  </script>
</body>
</html>







