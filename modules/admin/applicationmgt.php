<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../../login.php");
    exit();
}
require_once '../../config/config.php';

$page = 'applicationmgt';
$module = 'applicationmgt';

// Fetch Statistics
$stats = [
    'Total' => $conn->query("SELECT COUNT(*) FROM applicants")->fetch_row()[0],
    'New' => $conn->query("SELECT COUNT(*) FROM applicants WHERE Status = 'New'")->fetch_row()[0],
    'Shortlisted' => $conn->query("SELECT COUNT(*) FROM applicants WHERE Status = 'Shortlisted'")->fetch_row()[0],
    'Accepted' => $conn->query("SELECT COUNT(*) FROM applicants WHERE Status = 'Accepted'")->fetch_row()[0]
];

// Fetch Applicants with Job Titles
$query = "SELECT a.*, j.Title as JobTitle 
          FROM applicants a 
          JOIN job_postings j ON a.PostID = j.PostID 
          ORDER BY a.AppliedAt DESC";
$applicants = $conn->query($query);

// Fetch Interview Schedules
$interviewQuery = "SELECT s.*, a.FirstName, a.LastName, a.Email, a.Status, j.Title as JobTitle, u.Username as InterviewerName
                  FROM interview_schedules s
                  JOIN applicants a ON s.ApplicantID = a.ApplicantID
                  JOIN job_postings j ON a.PostID = j.PostID
                  JOIN useraccounts u ON s.InterviewerID = u.AccountID
                  ORDER BY s.InterviewDate ASC, s.InterviewTime ASC";
$interviews = $conn->query($interviewQuery);
if (!$interviews) {
    error_log("Recruitment Query Error: " . $conn->error);
    $interviews = $conn->query("SELECT 1 FROM applicants LIMIT 0");
}

// Fetch Evaluated Candidates for Hiring Approval
$evalQuery = "SELECT e.*, a.FirstName, a.LastName, a.ApprovalStatus, a.ExamScore, a.ExamStatus, j.Title as JobTitle 
              FROM interview_evaluations e
              JOIN applicants a ON e.ApplicantID = a.ApplicantID
              JOIN job_postings j ON a.PostID = j.PostID
              ORDER BY e.CreatedAt DESC";
$evalList = $conn->query($evalQuery);
if (!$evalList) {
    error_log("Evaluation Query Error: " . $conn->error);
    $evalList = $conn->query("SELECT 1 FROM applicants LIMIT 0");
}

// Fetch Selection Rankings (Top 10) - Candidates MUST have completed Exam and Evaluation
$selectionQuery = "SELECT a.*, j.Title as JobTitle, e.AverageRating,
       (COALESCE(a.ResumeScore, 0) * 0.20) + 
       (COALESCE(e.AverageRating, 0) * 8) + 
       (COALESCE(a.ExamScore, 0) * 2.6667) as TotalScore
FROM interview_evaluations e
JOIN applicants a ON e.ApplicantID = a.ApplicantID
JOIN job_postings j ON a.PostID = j.PostID
ORDER BY TotalScore DESC
LIMIT 10";
$selectionList = $conn->query($selectionQuery);
if (!$selectionList) {
    error_log("Selection Query Error: " . $conn->error);
    $selectionList = $conn->query("SELECT 1 FROM applicants LIMIT 0");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Applicant Management - Microfinance</title>
  <link rel="stylesheet" href="../../css/applicationmgt.css?v=<?php echo time(); ?>">
  <!-- Interviewsched styles merged here -->
  <style>
    .tabs-container {
      display: flex;
      gap: 12px;
      margin-bottom: 24px;
      padding: 4px;
      background: var(--surface);
      border-radius: 12px;
      width: fit-content;
      border: 1px solid var(--border-color);
    }
    .tab-btn {
      padding: 10px 20px;
      border: none;
      background: transparent;
      color: var(--text-secondary);
      font-weight: 600;
      border-radius: 10px;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 8px;
      transition: all 0.2s ease;
    }
    .tab-btn i { width: 18px; }
    .tab-btn.active {
      background: var(--brand-green);
      color: white;
      box-shadow: 0 4px 6px -1px rgba(44, 160, 120, 0.2);
    }
    .tab-content { display: none; }
    .tab-content.active { display: block; }
    
    /* Evaluation Stars */
    .rating-stars {
      display: flex;
      gap: 8px;
      margin: 12px 0;
    }
    .star-btn {
      background: none;
      border: none;
      padding: 0;
      cursor: pointer;
      color: #e5e7eb;
      transition: all 0.2s ease;
    }
    .star-btn.active { color: #f59e0b; }
    .star-btn:hover { transform: scale(1.1); }
    
    /* Reveal Modals */
    .modal-overlay.active {
      display: flex !important;
    }
  </style>
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
        <span class="nav-section-title">ANALYTICS & REPORTING</span>
        <a href="dashboard.php" class="nav-item">
          <i data-lucide="layout-dashboard"></i>
          <span>HR ANALYTICS</span>
        </a>
      </div>
      <div class="nav-section">
        <span class="nav-section-title">HUMAN RESOURCES I</span>
        <div class="nav-item-group <?php echo ($module === 'recruitment') ? 'active' : ''; ?>">
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
        </div>
        <div class="nav-item-group <?php echo ($module === 'applicationmgt') ? 'active' : ''; ?>">
          <button class="nav-item has-submenu <?php echo ($module === 'applicationmgt') ? 'active' : ''; ?>" data-module="applicationmgt">
            <div class="nav-item-content">
              <i data-lucide="contact-round"></i>
              <span>Applicant Management</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-applicationmgt">
            <a href="applicationmgt.php" class="submenu-item <?php echo ($page === 'applicationmgt') ? 'active' : ''; ?>">
              <i data-lucide="contact-round"></i>
              <span>Applicant Management</span>
            </a>
          </div>
        </div>
        <div class="nav-item-group <?php echo ($module === 'newhiredonboard') ? 'active' : ''; ?>">
          <button class="nav-item has-submenu" data-module="newhiredonboard">
            <div class="nav-item-content">
              <i data-lucide="user-plus"></i>
              <span>New Hired Onboard</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-newhiredonboard">
            <a href="newhiredonboard.php" class="submenu-item <?php echo ($page === 'newhiredonboard') ? 'active' : ''; ?>">
              <i data-lucide="user-plus"></i>
              <span>New Hired Onboard</span>
            </a>
          </div>
        </div>
      </div>
      <div class="nav-section">
        <span class="nav-section-title">HUMAN RESOURCES II</span>
        <div class="nav-item-group <?php echo ($module === 'accounts') ? 'active' : ''; ?>">
          <button class="nav-item has-submenu <?php echo ($module === 'accounts') ? 'active' : ''; ?>" data-module="accounts">
            <div class="nav-item-content">
              <i data-lucide="users"></i>
              <span>Account Management</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu <?php echo ($module === 'accounts') ? 'active' : ''; ?>" id="submenu-accounts">
            <a href="useraccount.php" class="submenu-item <?php echo ($page === 'useraccount') ? 'active' : ''; ?>">
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
        <div class="nav-item-group <?php echo ($module === 'competency') ? 'active' : ''; ?>">
          <button class="nav-item has-submenu <?php echo ($module === 'competency') ? 'active' : ''; ?>" data-module="competency">
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
          <button class="nav-item has-submenu <?php echo ($module === 'training') ? 'active' : ''; ?>" data-module="training">
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
          <button class="nav-item has-submenu <?php echo ($module === 'succession') ? 'active' : ''; ?>" data-module="succession">
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
          <button class="nav-item has-submenu <?php echo ($module === 'learning') ? 'active' : ''; ?>" data-module="learning">
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
      </div>
      <div class="nav-section">   
            <span class="nav-section-title">HUMAN RESOURCES III</span>
          <div class="nav-item-group <?php echo ($module === 'shift') ? 'active' : ''; ?>">
            <button class="nav-item has-submenu <?php echo ($module === 'shift') ? 'active' : ''; ?>" data-module="shift">
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
        </div>
        <div class="nav-item-group <?php echo ($module === 'claims') ? 'active' : ''; ?>">
          <button class="nav-item has-submenu <?php echo ($module === 'claims') ? 'active' : ''; ?>" data-module="claims">
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
        </div>
        <div class="nav-item-group <?php echo ($module === 'time') ? 'active' : ''; ?>">
          <button class="nav-item has-submenu <?php echo ($module === 'time') ? 'active' : ''; ?>" data-module="time">
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
        </div>
        <div class="nav-item-group <?php echo ($module === 'timesheet') ? 'active' : ''; ?>">
          <button class="nav-item has-submenu <?php echo ($module === 'timesheet') ? 'active' : ''; ?>" data-module="timesheet">
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
            <button class="nav-item has-submenu <?php echo ($module === 'leave') ? 'active' : ''; ?>" data-module="leave">
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
        </div>
      </div>
      <div class="nav-section">
        <span class="nav-section-title">HUMAN RESOURCES IV</span>
          <div class="nav-item-group <?php echo ($module === 'corehumancapital') ? 'active' : ''; ?>">
            <button class="nav-item has-submenu <?php echo ($module === 'corehumancapital') ? 'active' : ''; ?>" data-module="corehumancapital">
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
          <button class="nav-item has-submenu <?php echo ($module === 'planning') ? 'active' : ''; ?>" data-module="planning">
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
        <div class="nav-item-group <?php echo ($module === 'payroll') ? 'active' : ''; ?>">
          <button class="nav-item has-submenu <?php echo ($module === 'payroll') ? 'active' : ''; ?>" data-module="payroll">
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
            <a href="payroll.php" class="submenu-item <?php echo ($page === 'payroll') ? 'active' : ''; ?>">
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
          <button class="nav-item has-submenu <?php echo ($module === 'budget') ? 'active' : ''; ?>" data-module="budget">
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
          <span class="user-role">Administrator</span>
        </div>
        <button class="user-menu-btn" id="userMenuBtn">
          <i data-lucide="more-vertical"></i>
        </button>
        <div class="user-menu-dropdown" id="userMenuDropdown">
          <div class="umd-header">
            <div class="umd-avatar" id="umdAvatar"><?php echo strtoupper(substr($_SESSION['username'] ?? 'A', 0, 1)); ?></div>
            <div class="umd-info">
              <span class="umd-signed">Signed in as</span>
              <span class="umd-name" id="umdName"><?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?></span>
              <span class="umd-role" id="umdRole">Administrator</span>
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
          <h1>Recruitment Center</h1>
          <p>Manage applications and track interview progress in one place.</p>
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
          <span class="badge" id="notifBadge" style="background:#ef4444; position:absolute; top:-5px; right:-5px; font-size:10px; padding:2px 5px; color:white; border-radius:50%;"><?php echo $stats['New']; ?></span>
        </button>
      </div>
    </header>

    <div class="content-wrapper">
      <!-- Tabs Selector -->
      <div class="tabs-container">
        <button class="tab-btn active" data-tab="applicants">
          <i data-lucide="users"></i>
          <span>Applicants List</span>
        </button>
        <button class="tab-btn" data-tab="interviews">
          <i data-lucide="calendar"></i>
          <span>Interview Schedule</span>
        </button>
        <button class="tab-btn" data-tab="hiring">
          <i data-lucide="check-square"></i>
          <span>Examination Center</span>
        </button>
        <button class="tab-btn" data-tab="selection">
          <i data-lucide="award"></i>
          <span>Applicant Selection</span>
        </button>
      </div>

      <!-- Tab: Applicants -->
      <div id="applicantsTab" class="tab-content active">
        <!-- Stats Grid -->
        <div class="stats-grid">
          <div class="stat-card">
            <div class="stat-icon" style="background: rgba(44, 160, 120, 0.1); color: var(--brand-green);">
              <i data-lucide="users"></i>
            </div>
            <div class="stat-content">
              <span class="stat-label">Total Applicants</span>
              <h3 class="stat-value"><?php echo number_format($stats['Total']); ?></h3>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">
              <i data-lucide="user-plus"></i>
            </div>
            <div class="stat-content">
              <span class="stat-label">New Applications</span>
              <h3 class="stat-value"><?php echo $stats['New']; ?></h3>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
              <i data-lucide="calendar-clock"></i>
            </div>
            <div class="stat-content">
              <span class="stat-label">For Interview</span>
              <h3 class="stat-value"><?php echo ($interviews ? $interviews->num_rows : 0); ?></h3>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
              <i data-lucide="check-circle"></i>
            </div>
            <div class="stat-content">
              <span class="stat-label">Hired</span>
              <h3 class="stat-value"><?php echo $stats['Accepted']; ?></h3>
            </div>
          </div>
        </div>

        <!-- Toolbar -->
        <div class="toolbar">
          <div class="search-box">
            <i data-lucide="search"></i>
            <input type="text" id="applicantSearch" placeholder="Search by name, email or job title...">
          </div>
          <div class="filter-group">
            <select id="statusFilter" class="filter-select">
              <option value="all">All Statuses</option>
              <option value="New">New</option>
              <option value="Reviewed">Reviewed</option>
              <option value="Shortlisted">Shortlisted</option>
              <option value="Interview">Interview</option>
              <option value="Rejected">Rejected</option>
              <option value="Accepted">Accepted</option>
            </select>
          </div>
        </div>

       <!-- Table Container -->
      <div class="table-container">
        <table class="applicant-table">
          <thead>
            <tr>
              <th>Applicant Details</th>
              <th>Applied For</th>
              <th>Applied Date</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="applicantTableBody">
            <?php 
            $applicants->data_seek(0); // Reset pointer
            while($row = $applicants->fetch_assoc()): 
               $initials = strtoupper(substr($row['FirstName'], 0, 1) . substr($row['LastName'], 0, 1));
               $statusClass = "badge-" . strtolower($row['Status']);
            ?>
            <tr class="applicant-row" data-status="<?php echo $row['Status']; ?>">
              <td>
                <div class="user-info-cell">
                  <div class="initials-avatar"><?php echo $initials; ?></div>
                  <div class="name-email">
                    <span class="applicant-name"><?php echo htmlspecialchars($row['FirstName'] . ' ' . $row['LastName']); ?></span>
                    <span class="applicant-email"><?php echo htmlspecialchars($row['Email']); ?></span>
                  </div>
                </div>
              </td>
              <td>
                <div style="font-weight:500; color:var(--text-primary);"><?php echo htmlspecialchars($row['JobTitle']); ?></div>
              </td>
              <td style="color:var(--text-secondary);">
                <?php echo date('M d, Y', strtotime($row['AppliedAt'])); ?>
              </td>
              <td>
                <span class="badge <?php echo $statusClass; ?>"><?php echo $row['Status']; ?></span>
              </td>
              <td style="display:flex; gap:8px;">
                <button class="action-btn view-details" data-id="<?php echo $row['ApplicantID']; ?>" style="background:var(--brand-green); color:white; border:none; padding:6px 14px; border-radius:8px; font-size:12px; font-weight:600; cursor:pointer; display:flex; align-items:center; gap:6px;">
                  <i data-lucide="eye" style="width:14px;"></i>
                  <span>View Details</span>
                </button>
                <button class="action-btn download-resume" onclick="window.open('../../<?php echo $row['ResumePath']; ?>')" style="background:rgba(59,130,246,0.1); color:#3b82f6; border:none; padding:6px 14px; border-radius:8px; font-size:12px; font-weight:600; cursor:pointer; display:flex; align-items:center; gap:6px;">
                  <i data-lucide="file-text" style="width:14px;"></i>
                  <span>Resume</span>
                </button>
              </td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div> <!-- End Applicants Tab -->

    <!-- Tab: Interviews -->
    <div id="interviewsTab" class="tab-content">
      <div class="table-container">
        <table class="applicant-table">
          <thead>
            <tr>
              <th>Candidate</th>
              <th>Interviewer</th>
              <th>Schedule Details</th>
              <th>Mode/Location</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php while($i = $interviews->fetch_assoc()): ?>
            <tr>
              <td>
                <div style="font-weight:600; color:var(--text-primary);"><?php echo htmlspecialchars($i['FirstName'] . ' ' . $i['LastName']); ?></div>
                <div style="font-size:12px; color:var(--text-secondary);"><?php echo htmlspecialchars($i['JobTitle']); ?></div>
              </td>
              <td>
                <div class="user-info-cell">
                  <?php $intInitials = strtoupper(substr($i['InterviewerName'], 0, 2)); ?>
                  <div class="initials-avatar" style="width:30px; height:30px; font-size:12px;"><?php echo $intInitials; ?></div>
                  <span><?php echo htmlspecialchars($i['InterviewerName']); ?></span>
                </div>
              </td>
              <td>
                <div style="font-weight:500;"><i data-lucide="calendar" style="width:14px; margin-right:4px;"></i><?php echo date('M d, Y', strtotime($i['InterviewDate'])); ?></div>
                <div style="font-size:13px; color:var(--text-secondary);"><i data-lucide="clock" style="width:14px; margin-right:4px;"></i><?php echo date('h:i A', strtotime($i['InterviewTime'])); ?></div>
              </td>
              <td>
                <span class="badge" style="background:rgba(44,160,120,0.1); color:var(--brand-green);"><?php echo $i['InterviewMode']; ?></span>
                <div style="font-size:12px; color:var(--text-secondary); margin-top:4px;"><?php echo htmlspecialchars($i['LocationOrLink']); ?></div>
              </td>
              <td>
                <?php if ($i['Status'] === 'Interview'): ?>
                <button class="action-btn evaluate-candidate" data-id="<?php echo $i['ApplicantID']; ?>" data-name="<?php echo htmlspecialchars($i['FirstName'].' '.$i['LastName']); ?>" style="background:#f59e0b; color:white; border:none; padding:6px 14px; border-radius:8px; font-size:12px; font-weight:600; cursor:pointer; display:flex; align-items:center; gap:6px;">
                   <i data-lucide="star" style="width:14px;"></i>
                   <span>Ratings</span>
                </button>
                <?php else: ?>
                <span class="badge" style="background:rgba(44,160,120,0.1); color:var(--brand-green); font-size:11px; padding:4px 10px;">Already Evaluated</span>
                <?php endif; ?>
              </td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div> <!-- End Interviews Tab -->

    <!-- Tab: Examination Center -->
    <div id="hiringTab" class="tab-content">
      <div class="table-container">
        <table class="applicant-table">
          <thead>
            <tr>
              <th>Candidate</th>
              <th>Avg Rating</th>
              <th>Exam Status</th>
              <th>Score</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if($evalList && $evalList->num_rows > 0): ?>
                <?php while($e = $evalList->fetch_assoc()): ?>
                <tr>
                  <td>
                    <div style="font-weight:600; color:var(--text-primary);"><?php echo htmlspecialchars($e['FirstName'] . ' ' . $e['LastName']); ?></div>
                    <div style="font-size:12px; color:var(--text-secondary);"><?php echo htmlspecialchars($e['JobTitle']); ?></div>
                  </td>
                  <td>
                    <div style="font-weight:700; color:var(--brand-green);"><?php echo number_format($e['AverageRating'], 1); ?> / 5.0</div>
                  </td>
                  <td>
                    <?php 
                      $statusColor = '#6b7280';
                      if($e['ExamStatus'] === 'Completed') $statusColor = '#10b981';
                      if($e['ExamStatus'] === 'Pending') $statusColor = '#f59e0b';
                    ?>
                    <span class="badge" style="background:<?php echo $statusColor; ?>15; color:<?php echo $statusColor; ?>;"><?php echo $e['ExamStatus'] ?? 'Pending'; ?></span>
                  </td>
                  <td>
                    <div style="font-weight:700; color:var(--brand-green);"><?php echo $e['ExamScore'] !== null ? $e['ExamScore'] . ' / 15' : '-'; ?></div>
                  </td>
                  <td>
                    <div style="display:flex; gap:8px;">
                        <?php if($e['ExamStatus'] === 'Completed'): ?>
                        <button type="button" disabled style="background:var(--border-color); color:var(--text-secondary); border:none; padding:6px 14px; border-radius:8px; font-size:12px; font-weight:600; cursor:not-allowed; display:flex; align-items:center; gap:6px;">
                           <i data-lucide="check-circle" style="width:14px;"></i>
                           <span>Exam Taken</span>
                        </button>
                        <?php else: ?>
                        <button type="button" class="action-btn start-exam" data-id="<?php echo $e['ApplicantID']; ?>" style="background:var(--brand-green); color:white; border:none; padding:6px 14px; border-radius:8px; font-size:12px; font-weight:600; cursor:pointer; display:flex; align-items:center; gap:6px;">
                           <i data-lucide="play" style="width:14px;"></i>
                           <span>Start Exam</span>
                        </button>
                        <?php endif; ?>
                    </div>
                  </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align:center; padding:40px; color:var(--text-tertiary);">
                        <i data-lucide="inbox" style="width:32px; height:32px; margin-bottom:12px; opacity:0.5;"></i>
                        <p>No candidates waiting for hiring approval.</p>
                    </td>
                </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div> <!-- End Hiring Approval Tab -->

    <!-- Tab: Applicant Selection -->
    <div id="selectionTab" class="tab-content">
      <div class="table-container">
        <table class="applicant-table">
          <thead>
            <tr>
              <th>Rank</th>
              <th>Candidate</th>
              <th>Resume (20%)</th>
              <th>Interview (40%)</th>
              <th>Exam (40%)</th>
              <th>Total Score</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if($selectionList && $selectionList->num_rows > 0): ?>
                <?php $rank = 1; while($s = $selectionList->fetch_assoc()): ?>
                <tr>
                   <td>
                     <div style="width:30px; height:30px; background:<?php echo ($rank <= 3) ? 'var(--brand-green)' : 'rgba(0,0,0,0.05)'; ?>; color:<?php echo ($rank <= 3) ? 'white' : 'var(--text-secondary)'; ?>; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:12px;">
                        <?php echo $rank++; ?>
                     </div>
                   </td>
                   <td>
                     <div style="font-weight:600; color:var(--text-primary);"><?php echo htmlspecialchars($s['FirstName'] . ' ' . $s['LastName']); ?></div>
                     <div style="font-size:12px; color:var(--text-secondary);"><?php echo htmlspecialchars($s['JobTitle']); ?></div>
                   </td>
                   <td>
                     <div style="font-weight:500;"><?php echo $s['ResumeScore'] ?? 0; ?> / 100</div>
                   </td>
                   <td>
                     <div style="font-weight:500;"><?php echo number_format(($s['AverageRating'] ?? 0) * 20, 1); ?>% (<?php echo number_format($s['AverageRating'] ?? 0, 1); ?>/5.0)</div>
                   </td>
                   <td>
                     <div style="font-weight:500;"><?php echo number_format((($s['ExamScore'] ?? 0) / 15) * 100, 1); ?>% (<?php echo $s['ExamScore'] ?? 0; ?>/15)</div>
                   </td>
                   <td>
                     <div style="font-weight:800; color:<?php echo ($s['TotalScore'] >= 75) ? 'var(--brand-green)' : '#ef4444'; ?>; font-size:16px;"><?php echo number_format($s['TotalScore'], 1); ?> / 100</div>
                   </td>
                   <td>
                     <?php if($s['ApprovalStatus'] === 'Hired'): ?>
                       <div style="display:flex; align-items:center; gap:6px; color:var(--brand-green); background:rgba(44, 160, 120, 0.05); padding:8px 16px; border-radius:10px; font-size:12px; font-weight:700; border:1px solid rgba(44, 160, 120, 0.1);">
                           <i data-lucide="check-circle" style="width:16px;"></i>
                           <span>Hired</span>
                       </div>
                     <?php elseif($s['ApprovalStatus'] === 'Approved'): ?>
                        <div style="display:flex; align-items:center; gap:6px; color:#3b82f6; background:rgba(59, 130, 246, 0.05); padding:8px 16px; border-radius:10px; font-size:12px; font-weight:700; border:1px solid rgba(59, 130, 246, 0.1);">
                            <i data-lucide="clock" style="width:16px;"></i>
                            <span>In Onboarding</span>
                        </div>
                     <?php elseif($s['TotalScore'] >= 75): ?>
                        <button class="action-btn approve-hire" data-id="<?php echo $s['ApplicantID']; ?>" style="background:var(--brand-green); color:white; border:none; padding:8px 16px; border-radius:10px; font-size:12px; font-weight:700; cursor:pointer; display:flex; align-items:center; gap:8px; transition:all 0.2s ease;">
                            <i data-lucide="user-plus" style="width:16px;"></i>
                            <span>Hire Now</span>
                        </button>
                     <?php else: ?>
                        <div style="display:flex; align-items:center; gap:6px; color:#ef4444; background:rgba(239, 68, 68, 0.05); padding:6px 12px; border-radius:8px; font-size:11px; font-weight:600; border:1px solid rgba(239, 68, 68, 0.1);">
                            <i data-lucide="x-circle" style="width:14px;"></i>
                            <span>Not Eligible</span>
                        </div>
                     <?php endif; ?>
                   </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align:center; padding:40px; color:var(--text-tertiary);">
                        <p>No candidates qualified for selection ranking yet.</p>
                    </td>
                </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div> <!-- End Selection Tab -->
    </div> <!-- End Content Wrapper -->
  </main>

  <!-- Applicant Details Modal -->
  <div class="modal-overlay" id="applicantModal">
      <div class="modal-content detail-modal-content" style="max-width: 850px;">
          <div class="modal-header">
              <div class="header-profile">
                  <div class="large-avatar" id="modalAvatar" style="background: linear-gradient(135deg, var(--brand-green), #14532d); font-size: 24px; font-weight: 800; border: 4px solid var(--surface);">JS</div>
                  <div>
                      <h2 id="modalName" style="font-size: 24px; font-weight: 800; color: var(--text-primary); margin-bottom: 4px;">Candidate Name</h2>
                      <div class="modal-subtitle" style="display: flex; gap: 16px; align-items: center;">
                          <span id="modalJob" style="display: flex; align-items: center; gap: 6px; font-size: 13px; color: var(--text-secondary); background: rgba(0,0,0,0.05); padding: 4px 10px; border-radius: 6px;"><i data-lucide="briefcase" style="width: 14px;"></i> <span>Position</span></span>
                          <span id="modalApplied" style="display: flex; align-items: center; gap: 6px; font-size: 13px; color: var(--text-secondary);"><i data-lucide="calendar" style="width: 14px;"></i> <span>Date</span></span>
                      </div>
                  </div>
              </div>
              <button id="closeModal" class="btn-icon-close" style="background: rgba(0,0,0,0.05); border-radius: 50%; width: 40px; height: 40px;"><i data-lucide="x"></i></button>
          </div>

          <div class="modal-body" style="background: var(--background); padding: 30px;">
              <div class="detail-grid" style="display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 24px;">
                  <!-- Left Column: Information -->
                  <div style="display: flex; flex-direction: column; gap: 24px;">
                      <div style="background: var(--surface); padding: 24px; border-radius: 16px; border: 1px solid var(--border-color); box-shadow: 0 4px 12px rgba(0,0,0,0.02);">
                          <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">
                              <div style="color: var(--brand-green);"><i data-lucide="user-circle" style="width: 20px;"></i></div>
                              <h3 style="font-size: 15px; font-weight: 700; color: var(--text-primary); text-transform: uppercase; letter-spacing: 0.05em;">Personal Profile</h3>
                          </div>
                          
                          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                              <div class="info-item">
                                  <label style="font-size: 11px; font-weight: 700; color: var(--text-tertiary); display: block; margin-bottom: 4px; text-transform: uppercase;">Email Address</label>
                                  <span id="modalEmail" style="font-size: 14px; font-weight: 600; color: var(--text-primary);">-</span>
                              </div>
                              <div class="info-item">
                                  <label style="font-size: 11px; font-weight: 700; color: var(--text-tertiary); display: block; margin-bottom: 4px; text-transform: uppercase;">Phone Number</label>
                                  <span id="modalPhone" style="font-size: 14px; font-weight: 600; color: var(--text-primary);">-</span>
                              </div>
                              <div class="info-item">
                                  <label style="font-size: 11px; font-weight: 700; color: var(--text-tertiary); display: block; margin-bottom: 4px; text-transform: uppercase;">Gender & DOB</label>
                                  <span id="modalGenderBirth" style="font-size: 14px; font-weight: 600; color: var(--text-primary);">-</span>
                              </div>
                              <div class="info-item full" style="grid-column: span 2;">
                                  <label style="font-size: 11px; font-weight: 700; color: var(--text-tertiary); display: block; margin-bottom: 4px; text-transform: uppercase;">Residential Address</label>
                                  <span id="modalAddress" style="font-size: 14px; font-weight: 600; color: var(--text-primary); line-height: 1.5;">-</span>
                              </div>
                          </div>
                      </div>

                      <div style="background: var(--surface); padding: 24px; border-radius: 16px; border: 1px solid var(--border-color); box-shadow: 0 4px 12px rgba(0,0,0,0.02);">
                          <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">
                              <div style="color: #ef4444;"><i data-lucide="shield-alert" style="width: 20px;"></i></div>
                              <h3 style="font-size: 15px; font-weight: 700; color: var(--text-primary); text-transform: uppercase; letter-spacing: 0.05em;">Emergency Contact</h3>
                          </div>
                          <div class="info-item full">
                              <label style="font-size: 11px; font-weight: 700; color: var(--text-tertiary); display: block; margin-bottom: 4px; text-transform: uppercase;">Point of Contact</label>
                              <span id="modalEmergency" style="font-size: 14px; font-weight: 600; color: var(--text-primary);">-</span>
                          </div>
                      </div>

                      <div style="background: var(--surface); padding: 24px; border-radius: 16px; border: 1px solid var(--border-color); box-shadow: 0 4px 12px rgba(0,0,0,0.02);">
                          <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">
                              <div style="color: var(--brand-green);"><i data-lucide="award" style="width: 20px;"></i></div>
                              <h3 style="font-size: 15px; font-weight: 700; color: var(--text-primary); text-transform: uppercase; letter-spacing: 0.05em;">Candidate Assessment</h3>
                          </div>
                          <div style="display: flex; flex-direction: column; gap: 15px;">
                              <div class="info-item">
                                  <label style="font-size: 11px; font-weight: 700; color: var(--text-tertiary); display: block; margin-bottom: 8px; text-transform: uppercase;">Resume Score (20% Weight)</label>
                                  <div style="display: flex; gap: 10px; align-items: center;">
                                      <input type="number" id="resumeScoreInput" min="0" max="100" placeholder="Score 0-100" style="flex: 1; padding: 10px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 14px;">
                                      <button id="saveResumeScoreBtn" style="background: var(--brand-green); color: white; border: none; padding: 10px 16px; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 13px;">Save</button>
                                  </div>
                              </div>
                              <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; padding-top: 10px; border-top: 1px dashed var(--border-color);">
                                  <div>
                                      <label style="font-size: 11px; font-weight: 700; color: var(--text-tertiary); display: block; margin-bottom: 4px; text-transform: uppercase;">Interview Rating</label>
                                      <span id="modalRatingText" style="font-size: 14px; font-weight: 700; color: var(--brand-green);">- / 5.0</span>
                                  </div>
                                  <div>
                                      <label style="font-size: 11px; font-weight: 700; color: var(--text-tertiary); display: block; margin-bottom: 4px; text-transform: uppercase;">Exam Score</label>
                                      <span id="modalExamText" style="font-size: 14px; font-weight: 700; color: var(--brand-green);">- / 15</span>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>

                  <!-- Right Column: Docs -->
                  <div style="display: flex; flex-direction: column; gap: 24px;">
                      <div style="background: var(--surface); padding: 24px; border-radius: 16px; border: 1px solid var(--border-color); box-shadow: 0 4px 12px rgba(0,0,0,0.02); min-height: 100%;">
                          <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">
                              <div style="color: #3b82f6;"><i data-lucide="file-check" style="width: 20px;"></i></div>
                              <h3 style="font-size: 15px; font-weight: 700; color: var(--text-primary); text-transform: uppercase; letter-spacing: 0.05em;">Applicant Documents</h3>
                          </div>
                          <div id="modalDocs" class="doc-links-grid" style="display: grid; gap: 12px;">
                              <!-- Links injected by JS -->
                          </div>
                      </div>
                  </div>
              </div>
          </div>

          <div class="modal-footer" style="padding: 24px 30px; border-top: 1px solid var(--border-color); background: var(--surface); display: flex; align-items: center; justify-content: space-between; border-bottom-left-radius: 20px; border-bottom-right-radius: 20px;">
              <div style="display: flex; align-items: center; gap: 10px; color: var(--text-tertiary);">
                  <div style="background: rgba(0,0,0,0.03); padding: 8px; border-radius: 8px;"><i data-lucide="shield" style="width: 16px;"></i></div>
                  <span style="font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Management Hub</span>
              </div>
              <div class="status-controls" style="display: flex; gap: 12px;">
                  <button id="evaluateBtn" class="btn-evaluate hidden" style="background: #f59e0b; color: white; border: none; padding: 12px 24px; border-radius: 12px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 10px; transition: all 0.2s ease; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2);">
                      <i data-lucide="medal" style="width: 18px;"></i>
                      <span>Perform Evaluation</span>
                  </button>
                  <button id="scheduleInterviewBtn" class="btn-schedule-interview" style="background: var(--brand-green); color: white; border: none; padding: 12px 28px; border-radius: 12px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 10px; transition: all 0.2s ease; box-shadow: 0 4px 12px rgba(44, 160, 120, 0.2);">
                      <i data-lucide="calendar-check" style="width: 18px;"></i>
                      <span>Schedule Interview</span>
                  </button>
              </div>
          </div>
      </div>
  </div>

  <!-- Scheduling Modal -->
  <div class="modal-overlay" id="scheduleModal">
    <div class="modal-content" style="max-width: 900px; border-radius: 24px; overflow: hidden;">
        <div class="modal-header" style="background: linear-gradient(135deg, var(--brand-green), #14532d); color: white; padding: 30px; border-bottom: none;">
            <div style="display:flex; align-items:center; gap:16px;">
                <div style="background:rgba(255,255,255,0.2); color:white; width:48px; height:48px; border-radius:14px; display:flex; align-items:center; justify-content:center; backdrop-filter: blur(4px);">
                    <i data-lucide="calendar-plus" style="width: 24px; height: 24px;"></i>
                </div>
                <div>
                    <h2 style="margin:0; font-size: 20px; font-weight: 800; letter-spacing: -0.02em; color: white;">Schedule Interview</h2>
                    <p style="margin:0; font-size: 13px; opacity: 0.8; font-weight: 500;">Configure specific assessment details</p>
                </div>
            </div>
            <button class="btn-icon-close" onclick="document.getElementById('scheduleModal').classList.remove('active')" style="background: rgba(255,255,255,0.1); color: white; border-radius: 50%; width: 40px; height: 40px; border: none; cursor: pointer;"><i data-lucide="x"></i></button>
        </div>
        <form id="scheduleForm" class="modal-body" style="padding: 0; background: var(--background);">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0;">
                <!-- Left Column: Settings -->
                <div style="padding: 30px; border-right: 1px solid var(--border-color); display: flex; flex-direction: column; gap: 24px;">
                    <input type="hidden" name="applicant_id" id="schedApplicantId">
                    
                    <div style="background: var(--surface); padding: 24px; border-radius: 18px; border: 1px solid var(--border-color); box-shadow: 0 4px 12px rgba(0,0,0,0.02);">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 15px;">
                            <div style="color: var(--brand-green);"><i data-lucide="user-plus" style="width: 18px;"></i></div>
                            <h3 style="font-size: 13px; font-weight: 700; color: var(--text-primary); text-transform: uppercase; letter-spacing: 0.05em; margin: 0;">Personnel & Mode</h3>
                        </div>
                        
                        <div style="display: flex; flex-direction: column; gap: 20px;">
                            <div class="form-group">
                                <label style="display:block; font-size: 11px; font-weight: 700; color: var(--text-tertiary); margin-bottom: 8px; text-transform: uppercase;">Assign Interviewer</label>
                                <select name="interviewer_id" id="interviewerSelect" required style="width:100%; padding:12px; border-radius:10px; border:1px solid var(--border-color); background:var(--background); color:var(--text-primary); font-weight:600; cursor:pointer;">
                                    <?php 
                                    $intRes = $conn->query("SELECT AccountID, Username FROM useraccounts");
                                    if ($intRes):
                                        while($u = $intRes->fetch_assoc()): ?>
                                            <option value="<?php echo $u['AccountID']; ?>"><?php echo htmlspecialchars($u['Username']); ?></option>
                                        <?php endwhile; 
                                    endif; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label style="display:block; font-size: 11px; font-weight: 700; color: var(--text-tertiary); margin-bottom: 8px; text-transform: uppercase;">Interview Delivery</label>
                                <select name="interview_mode" id="interviewMode" required style="width:100%; padding:12px; border-radius:10px; border:1px solid var(--border-color); background:var(--background); color:var(--text-primary); font-weight:600; cursor:pointer;">
                                    <option value="Face-to-Face">Face-to-Face Office</option>
                                    <option value="Online">Online Video Call</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label style="display:block; font-size: 11px; font-weight: 700; color: var(--text-tertiary); margin-bottom: 8px; text-transform: uppercase;">Meeting Instructions</label>
                        <textarea name="notes" placeholder="e.g., Bring original documents, prepare a 5-minute presentation..." style="width:100%; padding:14px; border-radius:10px; border:1px solid var(--border-color); background:var(--background); color:var(--text-primary); min-height:100px; font-family:inherit; resize: none;"></textarea>
                    </div>
                </div>

                <!-- Right Column: Logistics -->
                <div style="padding: 30px; display: flex; flex-direction: column; gap: 24px; background: rgba(0,0,0,0.01);">
                    <div style="background: var(--surface); padding: 24px; border-radius: 18px; border: 1px solid var(--border-color); box-shadow: 0 4px 12px rgba(0,0,0,0.02);">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 15px;">
                            <div style="color: #3b82f6;"><i data-lucide="clock" style="width: 18px;"></i></div>
                            <h3 style="font-size: 13px; font-weight: 700; color: var(--text-primary); text-transform: uppercase; letter-spacing: 0.05em; margin: 0;">Timing Details</h3>
                        </div>
                        <div style="display:flex; flex-direction: column; gap:20px;">
                            <div class="form-group">
                                <label style="display:block; font-size: 11px; font-weight: 700; color: var(--text-tertiary); margin-bottom: 8px; text-transform: uppercase;">Scheduled Date</label>
                                <input type="date" name="interview_date" id="schedDate" required style="width:100%; padding:12px; border-radius:10px; border:1px solid var(--border-color); background:var(--background); color:var(--text-primary); font-weight:600;">
                            </div>
                            <div class="form-group">
                                <label style="display:block; font-size: 11px; font-weight: 700; color: var(--text-tertiary); margin-bottom: 8px; text-transform: uppercase;">Start Time</label>
                                <input type="time" name="interview_time" id="schedTime" required style="width:100%; padding:12px; border-radius:10px; border:1px solid var(--border-color); background:var(--background); color:var(--text-primary); font-weight:600;">
                            </div>
                        </div>
                    </div>

                    <div style="background: var(--surface); padding: 24px; border-radius: 18px; border: 1px solid var(--border-color); box-shadow: 0 4px 12px rgba(0,0,0,0.02);">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 15px;">
                            <div style="color: #ef4444;"><i data-lucide="map-pin" style="width: 18px;"></i></div>
                            <h3 style="font-size: 13px; font-weight: 700; color: var(--text-primary); text-transform: uppercase; letter-spacing: 0.05em; margin: 0;">Venue / Link</h3>
                        </div>
                        <label id="locationLabel" style="display:block; font-size: 11px; font-weight: 700; color: var(--text-tertiary); margin-bottom: 8px; text-transform: uppercase;">Office Location</label>
                        <input type="text" name="location_link" id="locationLink" required placeholder="Room Number or Link..." style="width:100%; padding:14px; border-radius:10px; border:1px solid var(--border-color); background:var(--background); color:var(--text-primary); font-weight:500;">
                    </div>
                </div>
            </div>

            <div class="modal-footer" style="padding:24px 30px; border-top:1px solid var(--border-color); background: var(--surface); display: flex; justify-content: flex-end; gap: 12px;">
                <button type="button" onclick="document.getElementById('scheduleModal').classList.remove('active')" style="background:rgba(0,0,0,0.03); color:var(--text-secondary); border:none; padding:12px 24px; border-radius:12px; font-weight:700; cursor:pointer; transition: all 0.2s ease;">Discard</button>
                <button type="submit" id="submitScheduleBtn" style="background: linear-gradient(to right, var(--brand-green), #14532d); color:white; border:none; padding:12px 32px; border-radius:12px; font-weight:700; cursor:pointer; display:flex; align-items:center; gap:10px; transition:all 0.2s ease; box-shadow: 0 4px 12px rgba(44, 160, 120, 0.3);">
                    <i data-lucide="calendar-check-2" style="width:18px;"></i> Finalize Invitation
                </button>
            </div>
        </form>
    </div>
  </div>

  <!-- Evaluation Modal -->
  <div class="modal-overlay" id="evaluationModal">
    <div class="modal-content detail-modal-content" style="max-width: 900px;">
        <div class="modal-header">
            <div style="display:flex; align-items:center; gap:16px;">
                <div style="background:linear-gradient(135deg, #f59e0b, #d97706); color:white; width:48px; height:48px; border-radius:14px; display:flex; align-items:center; justify-content:center; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.25);">
                    <i data-lucide="star" style="width:24px; height:24px;"></i>
                </div>
                <div>
                    <h2 id="evalCandidateName" style="font-size:20px; font-weight:700; color:var(--text-primary);">Candidate Evaluation</h2>
                    <p style="font-size:13px; color:var(--text-secondary); margin-top:2px;">Detailed assessment based on key performance categories</p>
                </div>
            </div>
            <button class="btn-icon-close" onclick="document.getElementById('evaluationModal').classList.remove('active')"><i data-lucide="x"></i></button>
        </div>
        <form id="evaluationForm" class="modal-body" style="padding: 0;">
            <input type="hidden" name="applicant_id" id="evalApplicantId">
            <div style="display: grid; grid-template-columns: 1fr 1fr; min-height: 480px;">
                <!-- Left Column: Ratings -->
                <div style="padding: 32px; border-right: 1px solid var(--border-color); background: rgba(0,0,0,0.01);">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
                        <span style="font-size:12px; font-weight:700; color:var(--text-tertiary); letter-spacing:0.05em; text-transform:uppercase;">Performance Categories</span>
                        <div style="background:var(--brand-green); color:white; padding:6px 14px; border-radius:20px; font-weight:700; font-size:14px; display:flex; align-items:center; gap:8px;">
                            <i data-lucide="trending-up" style="width:14px;"></i>
                            Avg Score: <span id="avgScoreDisplay">0.0</span>
                        </div>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 24px;">
                        <!-- Technical Skills -->
                        <div class="rating-category-box">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                                <span style="font-size:14px; font-weight:600; color:var(--text-primary);">Technical Skills</span>
                            </div>
                            <div class="rating-stars" data-category="technical" style="display:flex; gap:10px;">
                                <input type="hidden" name="rating_technical" id="rating_technical" value="0">
                                <?php for($star=1; $star<=5; $star++): ?>
                                    <button type="button" class="star-btn" data-value="<?php echo $star; ?>" style="transition:all 0.2s;"><i data-lucide="star" style="width:28px; height:28px;"></i></button>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <!-- Communication -->
                        <div class="rating-category-box">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                                <span style="font-size:14px; font-weight:600; color:var(--text-primary);">Communication</span>
                            </div>
                            <div class="rating-stars" data-category="communication" style="display:flex; gap:10px;">
                                <input type="hidden" name="rating_communication" id="rating_communication" value="0">
                                <?php for($star=1; $star<=5; $star++): ?>
                                    <button type="button" class="star-btn" data-value="<?php echo $star; ?>" style="transition:all 0.2s;"><i data-lucide="star" style="width:28px; height:28px;"></i></button>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <!-- Financial Knowledge -->
                        <div class="rating-category-box">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                                <span style="font-size:14px; font-weight:600; color:var(--text-primary);">Financial Knowledge</span>
                            </div>
                            <div class="rating-stars" data-category="financial" style="display:flex; gap:10px;">
                                <input type="hidden" name="rating_financial" id="rating_financial" value="0">
                                <?php for($star=1; $star<=5; $star++): ?>
                                    <button type="button" class="star-btn" data-value="<?php echo $star; ?>" style="transition:all 0.2s;"><i data-lucide="star" style="width:28px; height:28px;"></i></button>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <!-- Reliability -->
                        <div class="rating-category-box">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                                <span style="font-size:14px; font-weight:600; color:var(--text-primary);">Reliability</span>
                            </div>
                            <div class="rating-stars" data-category="reliability" style="display:flex; gap:10px;">
                                <input type="hidden" name="rating_reliability" id="rating_reliability" value="0">
                                <?php for($star=1; $star<=5; $star++): ?>
                                    <button type="button" class="star-btn" data-value="<?php echo $star; ?>" style="transition:all 0.2s;"><i data-lucide="star" style="width:28px; height:28px;"></i></button>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Comments -->
                <div style="padding: 32px; background: var(--surface);">
                    <div class="form-group" style="margin-bottom:24px;">
                        <label style="font-size:11px; font-weight:700; color:var(--text-tertiary); text-transform:uppercase; letter-spacing:0.05em; display:block; margin-bottom:10px;">Evaluation Comments</label>
                        <textarea name="comments" required placeholder="Provide specific feedback on candidate's performance, strengths, and areas for improvement..." style="width:100%; padding:14px; border-radius:12px; border:1px solid var(--border-color); background:var(--background); color:var(--text-primary); min-height:180px; font-family:inherit; resize:none; font-size:14px; transition:border-color 0.2s;"></textarea>
                    </div>

                    <div class="form-group" style="margin-bottom:32px;">
                        <label style="font-size:11px; font-weight:700; color:var(--text-tertiary); text-transform:uppercase; letter-spacing:0.05em; display:block; margin-bottom:10px;">Final Recommendation</label>
                        <div style="position:relative;">
                            <select name="decision" required style="width:100%; padding:14px; border-radius:12px; border:1px solid var(--border-color); background:var(--background); color:var(--text-primary); font-weight:600; cursor:pointer; font-size:14px; appearance:none;">
                                <option value="">Select Recommendation</option>
                                <option value="Strong Hire" style="color:#10b981;">Strong Hire - Excellent Candidate</option>
                                <option value="Potential Hire" style="color:#f59e0b;">Potential Hire - Consider Further</option>
                                <option value="Do Not Hire" style="color:#ef4444;">Do Not Hire - Not a Fit</option>
                            </select>
                            <i data-lucide="chevron-down" style="position:absolute; right:14px; top:50%; transform:translateY(-50%); pointer-events:none; width:16px; color:var(--text-tertiary);"></i>
                        </div>
                    </div>

                    <div style="display:flex; justify-content:flex-end;">
                        <button type="submit" style="background:var(--brand-green); color:white; border:none; padding:14px 40px; border-radius:12px; font-weight:700; cursor:pointer; transition:all 0.2s ease; display:flex; align-items:center; gap:10px; box-shadow: 0 4px 12px rgba(44, 160, 120, 0.3);">
                            Submit Final Evaluation <i data-lucide="check-circle" style="width:18px;"></i>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
  </div>

  <!-- View Evaluation Modal -->
  <div class="modal-overlay" id="viewEvalModal">
    <div class="modal-content" style="max-width: 650px;">
        <div class="modal-header">
            <h2 id="viewEvalTitle" style="font-size: 20px; font-weight: 700; color: var(--text-primary);">Evaluation Review</h2>
            <button class="btn-icon-close" onclick="document.getElementById('viewEvalModal').classList.remove('active')"><i data-lucide="x"></i></button>
        </div>
        <div class="modal-body" style="padding: 30px;">
            <div id="evalDetailContent">
                 <div style="display: flex; flex-direction: column; gap: 20px;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="info-item">
                            <label style="font-size: 11px; font-weight: 700; color: var(--text-tertiary); text-transform: uppercase;">Average Score</label>
                            <div id="displayAvg" style="font-size: 24px; font-weight: 800; color: var(--brand-green);">0.0</div>
                        </div>
                        <div class="info-item">
                            <label style="font-size: 11px; font-weight: 700; color: var(--text-tertiary); text-transform: uppercase;">Recommendation</label>
                            <div id="displayDecision" style="font-weight: 600; font-size: 15px;">-</div>
                        </div>
                    </div>
                    
                    <div style="background: rgba(0,0,0,0.02); padding: 20px; border-radius: 12px; border: 1px solid var(--border-color);">
                        <label style="font-size: 11px; font-weight: 700; color: var(--text-tertiary); text-transform: uppercase; display: block; margin-bottom: 10px;">Interviewer Comments</label>
                        <p id="displayComments" style="font-size: 14px; line-height: 1.6; color: var(--text-primary);">-</p>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div style="display: flex; justify-content: space-between; font-size: 13px; padding-bottom: 8px; border-bottom: 1px solid var(--border-color);">
                            <span>Technical:</span> <span id="val_tech" style="font-weight: 700;">0 / 5</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 13px; padding-bottom: 8px; border-bottom: 1px solid var(--border-color);">
                            <span>Communication:</span> <span id="val_comm" style="font-weight: 700;">0 / 5</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 13px; padding-bottom: 8px; border-bottom: 1px solid var(--border-color);">
                            <span>Financial Knowledge:</span> <span id="val_fin" style="font-weight: 700;">0 / 5</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 13px; padding-bottom: 8px; border-bottom: 1px solid var(--border-color);">
                            <span>Reliability:</span> <span id="val_rel" style="font-weight: 700;">0 / 5</span>
                        </div>
                    </div>
                 </div>
            </div>
        </div>
        <div class="modal-footer" style="padding: 24px 30px; border-top: 1px solid var(--border-color); display: flex; justify-content: flex-end; background: var(--surface);">
            <button onclick="document.getElementById('viewEvalModal').classList.remove('active')" style="padding: 12px 24px; border-radius: 12px; background: rgba(0,0,0,0.05); color: var(--text-secondary); border: none; font-weight: 700; cursor: pointer; transition: all 0.2s ease;">Close Review</button>
        </div>
    </div>
  </div>

  <script src="../../js/applicationmgt.js?v=<?php echo time(); ?>"></script>
  <script>
    // New handler for Start Exam to open in new tab
    document.addEventListener('click', function(e) {
        const startBtn = e.target.closest('.start-exam');
        if (startBtn) {
            const id = startBtn.dataset.id;
            window.open(`take_exam.php?id=${id}`, '_blank');
        }
    });

    if (window.lucide) {
        lucide.createIcons();
    }
  </script>
</body>
</html>






