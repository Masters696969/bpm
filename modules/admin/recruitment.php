<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../../login.php");
    exit();
}

require_once '../../config/config.php';

$page = 'recruitment';
$module = 'recruitment';

// Fetch Active Requisitions
$query = "
    SELECT 
        r.*, 
        p.PositionName, 
        p.PositionCode, 
        d.DepartmentName,
        sg.GradeName,
        sg.MinSalary,
        sg.MaxSalary,
        jp.RequisitionID AS HasPosting,
        jp.Title AS PostTitle,
        jp.Description AS PostDesc,
        jp.Responsibilities AS PostResp,
        jp.Requirements AS PostReq,
        jp.JobType AS PostType,
        jp.SalaryType AS PostSalaryType,
        jp.SalaryRange AS PostSalaryRange,
        jp.Location AS PostLocation,
        p.JobDescription,
        (SELECT GROUP_CONCAT(CONCAT(c.name, ': ', c.description) SEPARATOR '\n') 
         FROM position_competencies pc 
         JOIN competencies c ON pc.competency_id = c.id 
         WHERE pc.position_id = p.PositionID) as Competencies
    FROM recruitment_requisitions r
    JOIN positions p ON r.PositionID = p.PositionID
    JOIN department d ON p.DepartmentID = d.DepartmentID
    JOIN salary_grades sg ON p.SalaryGradeID = sg.SalaryGradeID
    LEFT JOIN job_postings jp ON r.RequisitionID = jp.RequisitionID
    WHERE r.Status NOT IN ('Closed', 'Cancelled')
    ORDER BY r.CreatedAt DESC
";
$requisitions = $conn->query($query);

// Fetch Stats for Premium Cards
$stats_query = "
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN Status = 'Posted' THEN 1 ELSE 0 END) as posted,
        SUM(CASE WHEN Status NOT IN ('Closed', 'Cancelled', 'Posted') THEN 1 ELSE 0 END) as available
    FROM recruitment_requisitions 
    WHERE Status NOT IN ('Closed', 'Cancelled')
";
$stats_result = $conn->query($stats_query);
$stats = $stats_result->fetch_assoc();
$total_active = $stats['total'] ?? 0;
$already_posted = $stats['posted'] ?? 0;
$available_to_post = $stats['available'] ?? 0;

// Handle Actions (Create Job Post - Save to Database)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_job_post') {
    $reqId = $_POST['requisition_id'] ?? '';
    $title = $_POST['title'] ?? '';
    $department = $_POST['department'] ?? 'General';
    $type = $_POST['job_type'] ?? 'Full-time';
    $salaryType = $_POST['salary_type'] ?? 'Monthly';
    $salary = $_POST['salary_range'] ?? '';
    $location = $_POST['location'] ?? 'Quezon City';
    $description = $_POST['description'] ?? '';
    $responsibilities = $_POST['responsibilities'] ?? '';
    $requirements = $_POST['requirements'] ?? '';

    if (!empty($reqId) && !empty($title)) {
        $conn->begin_transaction();
        try {
            // 1. Insert into job_postings
            $stmt = $conn->prepare("INSERT INTO job_postings (RequisitionID, Title, Department, Category, JobType, SalaryType, SalaryRange, Location, Description, Responsibilities, Requirements) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("issssssssss", $reqId, $title, $department, $department, $type, $salaryType, $salary, $location, $description, $responsibilities, $requirements);
            $stmt->execute();
            $stmt->close();

            // 2. Update requisition status
            $update = $conn->prepare("UPDATE recruitment_requisitions SET Status = 'Posted' WHERE RequisitionID = ?");
            $update->bind_param("i", $reqId);
            $update->execute();
            $update->close();

            $conn->commit();
            // Trigger Pulse for cross-device sync
            file_put_contents('../../config/pulse_recruitment.txt', time());
            
            $_SESSION['success_message'] = "Job post created successfully!";
            header("Location: recruitment.php"); 
            exit();
        } catch (Exception $e) {
            $conn->rollback();
            $_SESSION['error_message'] = "Error creating job post: " . $e->getMessage();
        }
    }
    header("Location: recruitment.php");
    exit();
}

// Handle Actions (Edit Job Post)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit_job_post') {
    $reqId = $_POST['requisition_id'] ?? '';
    $type = $_POST['job_type'] ?? 'Full-time';
    $salaryType = $_POST['salary_type'] ?? 'Monthly';
    $description = $_POST['description'] ?? '';
    $responsibilities = $_POST['responsibilities'] ?? '';
    $requirements = $_POST['requirements'] ?? '';

    if (!empty($reqId)) {
        $stmt = $conn->prepare("UPDATE job_postings SET JobType = ?, SalaryType = ?, Description = ?, Responsibilities = ?, Requirements = ? WHERE RequisitionID = ?");
        $stmt->bind_param("sssssi", $type, $salaryType, $description, $responsibilities, $requirements, $reqId);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Job post updated successfully!";
            // Trigger Pulse for cross-device sync
            file_put_contents('../../config/pulse_recruitment.txt', time());
        } else {
            $_SESSION['error_message'] = "Error updating job post: " . $conn->error;
        }
        $stmt->close();
    }
    header("Location: recruitment.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recruitment Management</title>
    <link rel="stylesheet" href="../../css/recruitment.css?v=1.3">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="icon" type="image/png" href="../../img/logo.png">
    <style>
        .requisition-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 8px;
        }
        .req-row {
            background: var(--surface);
            transition: var(--transition);
        }
        .req-row:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }
        .req-row td {
            padding: 16px;
            border-top: 1px solid var(--border-color);
            border-bottom: 1px solid var(--border-color);
        }
        .req-row td:first-child {
            border-left: 1px solid var(--border-color);
            border-radius: 12px 0 0 12px;
        }
        .req-row td:last-child {
            border-right: 1px solid var(--border-color);
            border-radius: 0 12px 12px 0;
        }
        .status-pill {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-pending { background: rgba(255, 193, 7, 0.1); color: #b45309; }
        .status-active { background: rgba(59, 130, 246, 0.1); color: #1d4ed8; }
        .status-posted { background: rgba(16, 185, 129, 0.1); color: #047857; }
        
        .btn-post {
            background: linear-gradient(135deg, #2ca078, #4fb97a);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: var(--transition);
        }
        .btn-post:hover {
            box-shadow: 0 4px 12px rgba(44, 160, 120, 0.2);
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 24px;
            margin-bottom: 24px;
        }
        .stat-card {
            background: var(--surface);
            padding: 24px;
            border-radius: 16px;
            border: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: var(--transition);
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
            border-color: var(--primary-color);
        }
        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .stat-info {
            display: flex;
            flex-direction: column;
        }
        .stat-value {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-primary);
            line-height: 1;
            margin-bottom: 4px;
        }
        .stat-label {
            font-size: 13px;
            font-weight: 500;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .icon-available { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
        .icon-posted { background: rgba(16, 185, 129, 0.1); color: #10b981; }
        .icon-total { background: rgba(139, 92, 246, 0.1); color: #8b5cf6; }
    </style>
</head>
<body>

  <!-- Sidebar (Same as other pages) -->
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
        <a href="dashboard.php" class="nav-item <?php echo ($page === 'dashboard') ? 'active' : ''; ?>">
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
          <div class="submenu <?php echo ($module === 'recruitment') ? 'active' : ''; ?>" id="submenu-recruitment">
            <a href="recruitment.php" class="submenu-item <?php echo ($page === 'recruitment') ? 'active' : ''; ?>">
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
          <div class="submenu <?php echo ($module === 'applicationmgt') ? 'active' : ''; ?>" id="submenu-applicationmgt">
            <a href="applicationmgt.php" class="submenu-item <?php echo ($page === 'applicationmgt') ? 'active' : ''; ?>">
              <i data-lucide="contact-round"></i>
              <span>Applicant Management</span>
            </a>
          </div>
        </div>
        <div class="nav-item-group <?php echo ($module === 'newhiredonboard') ? 'active' : ''; ?>">
          <button class="nav-item has-submenu <?php echo ($module === 'newhiredonboard') ? 'active' : ''; ?>" data-module="newhiredonboard">
            <div class="nav-item-content">
              <i data-lucide="user-plus"></i>
              <span>New Hired Onboard</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu <?php echo ($module === 'newhiredonboard') ? 'active' : ''; ?>" id="submenu-newhiredonboard">
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
          <div class="submenu <?php echo ($module === 'competency') ? 'active' : ''; ?>" id="submenu-competency">
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
          <div class="submenu <?php echo ($module === 'training') ? 'active' : ''; ?>" id="submenu-training">
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
          <div class="submenu <?php echo ($module === 'succession') ? 'active' : ''; ?>" id="submenu-succession">
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
          <div class="submenu <?php echo ($module === 'learning') ? 'active' : ''; ?>" id="submenu-learning">
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
          <div class="submenu <?php echo ($module === 'shift') ? 'active' : ''; ?>" id="submenu-shift">
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
          <div class="submenu <?php echo ($module === 'claims') ? 'active' : ''; ?>" id="submenu-claims">
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
          <div class="submenu <?php echo ($module === 'time') ? 'active' : ''; ?>" id="submenu-time">
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
          <div class="submenu <?php echo ($module === 'timesheet') ? 'active' : ''; ?>" id="submenu-timesheet">
            <a href="timesheet.php" class="submenu-item <?php echo ($page === 'timesheet') ? 'active' : ''; ?>">
              <i data-lucide="calendar-days"></i>
              <span>Timesheet</span>
            </a>
          </div>
        </div>

        <div class="nav-item-group <?php echo ($module === 'leave') ? 'active' : ''; ?>">
          <button class="nav-item has-submenu <?php echo ($module === 'leave') ? 'active' : ''; ?>" data-module="leave">
            <div class="nav-item-content">
              <i data-lucide="tickets-plane"></i>
              <span>Leave Management</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu <?php echo ($module === 'leave') ? 'active' : ''; ?>" id="submenu-leave">
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
          <div class="submenu <?php echo ($module === 'corehumancapital') ? 'active' : ''; ?>" id="submenu-corehumancapital">
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
          <div class="submenu <?php echo ($module === 'planning') ? 'active' : ''; ?>" id="submenu-planning">
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
          <div class="submenu <?php echo ($module === 'payroll') ? 'active' : ''; ?>" id="submenu-payroll">
            <a href="comperules.php" class="submenu-item <?php echo ($page === 'comperules') ? 'active' : ''; ?>">
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
          <div class="submenu <?php echo ($module === 'budget') ? 'active' : ''; ?>" id="submenu-budget">
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
          <span class="user-role">Recruitment Officer</span>
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
          <h1>Hiring Requisitions</h1>
          <p>Manage and process hiring requests from different departments.</p>
        </div>
      </div>
      <div class="header-right">
        <div class="header-clock">
          <span id="realTimeClock"></span>
        </div>
        <button class="theme-toggle" id="themeToggle">
          <i data-lucide="sun" class="sun-icon"></i>
          <i data-lucide="moon" class="moon-icon"></i>
        </button>
        <button class="icon-btn">
          <i data-lucide="bell"></i>
        </button>
      </div>
    </header>

    <div class="content-wrapper">
      <?php if (isset($_SESSION['success_message'])): ?>
          <script>
              document.addEventListener('DOMContentLoaded', () => {
                  Swal.fire({
                      toast: true,
                      position: 'top-end',
                      icon: 'success',
                      title: '<?php echo $_SESSION['success_message']; ?>',
                      showConfirmButton: false,
                      timer: 3000,
                      timerProgressBar: true
                  });
                  // Broadcast update to other tabs
                  const channel = new BroadcastChannel('recruitment-updates');
                  channel.postMessage({ type: 'refresh' });
              });
          </script>
          <?php unset($_SESSION['success_message']); endif; ?>

      <?php if (isset($_SESSION['error_message'])): ?>
          <script>
              document.addEventListener('DOMContentLoaded', () => {
                  Swal.fire({
                      icon: 'error',
                      title: 'Error',
                      text: '<?php echo $_SESSION['error_message']; ?>',
                      confirmButtonColor: '#ef4444'
                  });
              });
          </script>
          <?php unset($_SESSION['error_message']); endif; ?>

      <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon icon-available">
                <i data-lucide="megaphone"></i>
            </div>
            <div class="stat-info">
                <span class="stat-value"><?php echo $available_to_post; ?></span>
                <span class="stat-label">Available to Post</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon icon-posted">
                <i data-lucide="check-circle-2"></i>
            </div>
            <div class="stat-info">
                <span class="stat-value"><?php echo $already_posted; ?></span>
                <span class="stat-label">Already Posted</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon icon-total">
                <i data-lucide="layers"></i>
            </div>
            <div class="stat-info">
                <span class="stat-value"><?php echo $total_active; ?></span>
                <span class="stat-label">Total Active Reqs</span>
            </div>
        </div>
      </div>

      <div class="management-card" style="background: var(--surface); border-radius: 16px; padding: 24px; border: 1px solid var(--border-color);">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <h2 style="font-size: 18px; font-weight: 700; color: var(--text-primary);">Active Requisitions</h2>
        </div>
        
        <div class="table-container">
            <table class="requisition-table">
                <thead>
                    <tr style="text-align: left; color: var(--text-secondary); font-size: 13px; font-weight: 600;">
                        <th style="padding: 12px 16px;">POSITION & CODE</th>
                        <th style="padding: 12px 16px;">DEPARTMENT</th>
                        <th style="padding: 12px 16px;">REQUESTED BY</th>
                        <th style="padding: 12px 16px;">STATUS</th>
                        <th style="padding: 12px 16px; text-align: center;">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($requisitions->num_rows === 0): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 40px; color: var(--text-tertiary);">
                                No pending requisitions found.
                            </td>
                        </tr>
                    <?php else: while ($req = $requisitions->fetch_assoc()): ?>
                        <tr class="req-row">
                            <td>
                                <div style="display: flex; flex-direction: column; gap: 2px;">
                                    <span style="font-weight: 600; color: var(--text-primary);"><?php echo htmlspecialchars($req['PositionName']); ?></span>
                                    <span style="font-size: 12px; color: var(--text-secondary);"><?php echo htmlspecialchars($req['PositionCode']); ?></span>
                                </div>
                            </td>
                            <td>
                                <span style="color: var(--text-primary); font-size: 14px;"><?php echo htmlspecialchars($req['DepartmentName']); ?></span>
                            </td>
                            <td>
                                <div style="display: flex; flex-direction: column;">
                                    <span style="font-size: 14px; color: var(--text-primary);"><?php echo htmlspecialchars($req['RequestedBy']); ?></span>
                                    <span style="font-size: 12px; color: var(--text-tertiary);"><?php echo date('M d, Y', strtotime($req['CreatedAt'])); ?></span>
                                </div>
                            </td>
                            <td>
                                <span class="status-pill status-<?php echo strtolower($req['Status']); ?>">
                                    <?php echo $req['Status']; ?>
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; justify-content: center; gap: 8px;">
                                    <?php if ($req['Status'] === 'Posted'): ?>
                                        <button class="btn-post" style="background: #f1f5f9; color: #94a3b8; border-color: #e2e8f0; cursor: not-allowed;" disabled>
                                            <i data-lucide="check-circle" style="width: 16px; height: 16px;"></i>
                                            <span>Already Posted</span>
                                        </button>
                                        <a href="../../jobposting.php" class="btn-post" style="text-decoration: none; background: rgba(44, 160, 120, 0.1); color: #2ca078; border: 1px solid rgba(44, 160, 120, 0.2); display: flex; align-items: center; gap: 8px;" target="_blank">
                                            <i data-lucide="external-link" style="width: 16px; height: 16px;"></i>
                                            <span>View Live</span>
                                        </a>
                                    <?php else: ?>
                                      <button class="btn-post" onclick='createJobPost(<?php echo htmlspecialchars(json_encode([
                                            "id" => $req["RequisitionID"],
                                            "title" => $req["PositionName"],
                                            "dept" => $req["DepartmentName"],
                                            "salary" => number_format($req["MinSalary"] / 1000, 0) . "k - " . number_format($req["MaxSalary"] / 1000, 0) . "k",
                                            "description" => $req["JobDescription"] ?? "",
                                            "competencies" => $req["Competencies"] ?? ""
                                        ]), ENT_QUOTES, "UTF-8"); ?>)'>
                                            <i data-lucide="megaphone" style="width: 16px; height: 16px;"></i>
                                            <span>Create Post</span>
                                        </button>
                                    <?php endif; ?>

                                    <?php if ($req['Status'] === 'Posted'): ?>
                                        <button class="btn-post" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6; border: 1px solid rgba(59, 130, 246, 0.2);" 
                                            onclick='editJobPost(<?php echo htmlspecialchars(json_encode([
                                                "id" => $req["RequisitionID"],
                                                "title" => $req["PostTitle"],
                                                "dept" => $req["DepartmentName"],
                                                "type" => $req["PostType"],
                                                "salaryType" => $req["PostSalaryType"],
                                                "salary" => $req["PostSalaryRange"],
                                                "location" => $req["PostLocation"],
                                                "desc" => $req["PostDesc"],
                                                "resp" => $req["PostResp"],
                                                "reqs" => $req["PostReq"]
                                            ]), ENT_QUOTES, "UTF-8"); ?>)'>
                                            <i data-lucide="edit-3" style="width: 16px; height: 16px;"></i>
                                            <span>Edit Post</span>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; endif; ?>
                </tbody>
            </table>
        </div>
      </div>
    </div>
  </main>

  <form id="postActionForm" method="POST" style="display: none;">
      <input type="hidden" name="action" id="postAction" value="save_job_post">
      <input type="hidden" name="requisition_id" id="postReqId">
      <input type="hidden" name="title" id="postTitle">
      <input type="hidden" name="department" id="postDepartment">
      <input type="hidden" name="job_type" id="postType">
      <input type="hidden" name="salary_type" id="postSalaryType">
      <input type="hidden" name="salary_range" id="postSalary">
      <input type="hidden" name="location" id="postLocation">
      <input type="hidden" name="description" id="postDescription">
      <input type="hidden" name="responsibilities" id="postResponsibilities">
      <input type="hidden" name="requirements" id="postRequirements">
  </form>

  <!-- Create Job Post Modal -->
  <div id="jobPostModal" class="modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; padding: 20px;">
      <div class="modal-content" style="background: white; width: 100%; max-width: 700px; border-radius: 16px; padding: 32px; box-shadow: 0 20px 50px rgba(0,0,0,0.2); max-height: 90vh; overflow-y: auto;">
          <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
              <h2 id="modalMainTitle" style="font-size: 20px; font-weight: 700; color: #1e293b;">Create Official Job Posting</h2>
              <button onclick="closeJobModal()" style="background: none; border: none; cursor: pointer; color: #64748b;"><i data-lucide="x"></i></button>
          </div>
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
              <div class="form-group">
                  <label style="display: block; font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 8px;">Position Title</label>
                  <input type="text" id="modalPosDisplay" value="..." style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; background: #f8fafc; cursor: not-allowed;" readonly>
              </div>
              <div class="form-group">
                  <label style="display: block; font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 8px;">Department</label>
                  <input type="text" id="modalDeptDisplay" value="..." style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; background: #f8fafc; cursor: not-allowed;" readonly>
              </div>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
              <div class="form-group">
                  <label style="display: block; font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 8px;">Job Type</label>
                  <select id="modalType" onchange="updateSalaryType()" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px;">
                      <option value="Full-time">Full-time</option>
                      <option value="Part-time">Part-time</option>
                      <option value="Contract">Contract</option>
                  </select>
              </div>
              <div class="form-group">
                  <label style="display: block; font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 8px;">Salary Type</label>
                  <input type="text" id="modalSalaryType" value="Monthly" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; background: #f8fafc; cursor: not-allowed;" readonly>
              </div>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div class="form-group">
                    <label style="display: block; font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 8px;">Location</label>
                    <input type="text" id="modalLocation" value="Quezon City" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; background: #f1f5f9; cursor: not-allowed;" readonly>
                </div>
                <div class="form-group">
                    <label style="display: block; font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 8px;">Salary Range</label>
                    <input type="text" id="modalSalary" placeholder="₱..." style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; background: #f1f5f9; cursor: not-allowed;" readonly>
                </div>
          </div>

          <div class="form-group" style="margin-bottom: 20px;">
              <label style="display: block; font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 8px;">Short Description</label>
              <textarea id="modalDescription" rows="3" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; resize: none;"></textarea>
          </div>

          <div class="form-group" style="margin-bottom: 20px;">
              <label style="display: block; font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 8px;">Skill Requirement (One per line)</label>
              <textarea id="modalResponsibilities" rows="4" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; resize: none;"></textarea>
          </div>

          <div class="form-group" style="margin-bottom: 30px;">
              <label style="display: block; font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 8px;">Qualification</label>
              <textarea id="modalRequirements" rows="4" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; resize: none;"></textarea>
          </div>

          <div style="display: flex; gap: 12px; justify-content: flex-end;">
              <button onclick="closeJobModal()" style="padding: 12px 24px; border: 1px solid #e2e8f0; border-radius: 12px; font-weight: 600; cursor: pointer; background: white; color: #64748b;">Cancel</button>
              <button id="modalSubmitBtn" onclick="submitJobPost()" style="padding: 12px 32px; background: linear-gradient(135deg, #2ca078, #4fb97a); color: white; border: none; border-radius: 12px; font-weight: 600; cursor: pointer; box-shadow: 0 4px 15px rgba(44, 160, 120, 0.2);">Post Now</button>
          </div>
      </div>
  </div>

  <script src="../../js/recruitment.js"></script>
  <script>
    lucide.createIcons();

    let currentReqId = null;
    let currentPosName = null;

    function createJobPost(data) {
        currentReqId = data.id;
        currentPosName = data.title;
        
        document.getElementById('postAction').value = 'save_job_post';
        document.getElementById('modalMainTitle').innerText = 'Create Official Job Posting';
        document.getElementById('modalSubmitBtn').innerText = 'Post Now';

        document.getElementById('modalPosDisplay').value = data.title;
        document.getElementById('modalDeptDisplay').value = data.dept;
        document.getElementById('modalSalary').value = `₱${data.salary}`;
        document.getElementById('modalLocation').value = "Quezon City";
        
        // Pre-populate fields
        document.getElementById('modalDescription').value = data.description || "";
        document.getElementById('modalResponsibilities').value = data.competencies || ""; // Competencies moved to Skills
        document.getElementById('modalRequirements').value = "Bachelor’s degree in a relevant field\nAt least 1–2 years of experience in a related field\nMinimum 3 years of work experience\nFresh graduates are welcome to apply\nRelevant professional certifications are an advantage but not required\nGood moral character and professional attitude"; // Default qualification

        document.getElementById('jobPostModal').style.display = 'flex';
        lucide.createIcons();
    }

    function editJobPost(data) {
        currentReqId = data.id;
        currentPosName = data.title;

        document.getElementById('postAction').value = 'edit_job_post';
        document.getElementById('modalMainTitle').innerText = 'Update Job Posting';
        document.getElementById('modalSubmitBtn').innerText = 'Update Changes';

        document.getElementById('modalPosDisplay').value = data.title;
        document.getElementById('modalDeptDisplay').value = data.dept;
        document.getElementById('modalType').value = data.type;
        document.getElementById('modalSalaryType').value = data.salaryType;
        document.getElementById('modalSalary').value = data.salary;
        document.getElementById('modalLocation').value = data.location;
        document.getElementById('modalDescription').value = data.desc;
        document.getElementById('modalResponsibilities').value = data.resp;
        document.getElementById('modalRequirements').value = data.reqs;

        document.getElementById('jobPostModal').style.display = 'flex';
        lucide.createIcons();
    }

    function updateSalaryType() {
        const type = document.getElementById('modalType').value;
        const typeMap = {
            'Full-time': 'Monthly',
            'Part-time': 'Daily',
            'Contract': 'Hourly'
        };
        document.getElementById('modalSalaryType').value = typeMap[type] || 'Monthly';
    }

    function closeJobModal() {
        document.getElementById('jobPostModal').style.display = 'none';
    }

    function submitJobPost() {
        const title = currentPosName;
        const dept = document.getElementById('modalDeptDisplay').value;
        const type = document.getElementById('modalType').value;
        const salaryType = document.getElementById('modalSalaryType').value;
        const salary = document.getElementById('modalSalary').value;
        const location = document.getElementById('modalLocation').value;
        const desc = document.getElementById('modalDescription').value;
        const resp = document.getElementById('modalResponsibilities').value;
        const reqs = document.getElementById('modalRequirements').value;

        if (!desc || !resp || !reqs) {
            Swal.fire({
                icon: 'warning',
                title: 'Missing Details',
                text: 'Please fill in the description, skills, and documentation requirements.'
            });
            return;
        }

        document.getElementById('postReqId').value = currentReqId;
        document.getElementById('postTitle').value = title;
        document.getElementById('postDepartment').value = dept;
        document.getElementById('postType').value = type;
        document.getElementById('postSalaryType').value = salaryType;
        document.getElementById('postSalary').value = salary;
        document.getElementById('postLocation').value = location;
        document.getElementById('postDescription').value = desc;
        document.getElementById('postResponsibilities').value = resp;
        document.getElementById('postRequirements').value = reqs;

        document.getElementById('postActionForm').submit();
    }
  </script>
</body>
</html>






