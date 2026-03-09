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
        jp.Location AS PostLocation
    FROM recruitment_requisitions r
    JOIN positions p ON r.PositionID = p.PositionID
    JOIN department d ON p.DepartmentID = d.DepartmentID
    JOIN salary_grades sg ON p.SalaryGradeID = sg.SalaryGradeID
    LEFT JOIN job_postings jp ON r.RequisitionID = jp.RequisitionID
    WHERE r.Status != 'Closed'
    ORDER BY r.CreatedAt DESC
";
$requisitions = $conn->query($query);

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
          <span class="app-tagline">Recruitment Hub</span>
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
       <div class="nav-section">
        <span class="nav-section-title">Human Resources</span>
          <div class="nav-item-group <?php echo ($module === 'corehumancapital') ? 'active' : ''; ?>">
          <button class="nav-item has-submenu" data-module="corehumancapital">
            <div class="nav-item-content">
              <i data-lucide="book-user"></i>
              <span>Core Human Capital</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-corehumancapital">
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
            <a href="salary.php" class="submenu-item <?php echo ($page === 'salarymgt') ? 'active' : ''; ?>">
              <i data-lucide="banknote"></i>
              <span>Salary & Scales Management</span>
            </a>
            <a href="statutory.php" class="submenu-item <?php echo ($page === 'statutory') ? 'active' : ''; ?>">
              <i data-lucide="scale"></i>
              <span>Statutory Contributions</span>
            </a>
            <a href="matrix.php" class="submenu-item <?php echo ($page === 'matrix') ? 'active' : ''; ?>">
              <i data-lucide="scale"></i>
              <span>Merit Matrix Structure</span>
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
            <a href="recruitment.php" class="nav-item <?php echo ($page === 'recruitment') ? 'active' : ''; ?>">
              <i data-lucide="layers-plus"></i>
              <span>Recruitment</span>
            </a>
            <a href="applicationmgt.php" class="nav-item <?php echo ($page === 'applicationmgt') ? 'active' : ''; ?>">
              <i data-lucide="contact-round"></i>
              <span>Application Management</span>
            </a>
      <a href="newhiredonboard.php" class="nav-item <?php echo ($page === 'newhiredonboard') ? 'active' : ''; ?>">
              <i data-lucide="user-plus"></i>
              <span>New Hired Onboard</span>
            </a>
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
                                        <button class="btn-post" onclick="createJobPost(<?php echo $req['RequisitionID']; ?>, '<?php echo addslashes($req['PositionName']); ?>', '<?php echo addslashes($req['DepartmentName']); ?>', '<?php echo number_format($req['MinSalary'] / 1000, 0); ?>k - <?php echo number_format($req['MaxSalary'] / 1000, 0); ?>k')">
                                            <i data-lucide="megaphone" style="width: 16px; height: 16px;"></i>
                                            <span>Create Post</span>
                                        </button>
                                    <?php endif; ?>

                                    <?php if ($req['Status'] === 'Posted'): ?>
                                        <button class="btn-post" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6; border: 1px solid rgba(59, 130, 246, 0.2);" 
                                            onclick="editJobPost({
                                                id: <?php echo $req['RequisitionID']; ?>,
                                                title: '<?php echo addslashes($req['PostTitle']); ?>',
                                                dept: '<?php echo addslashes($req['DepartmentName']); ?>',
                                                type: '<?php echo addslashes($req['PostType']); ?>',
                                                salaryType: '<?php echo addslashes($req['PostSalaryType']); ?>',
                                                salary: '<?php echo addslashes($req['PostSalaryRange']); ?>',
                                                location: '<?php echo addslashes($req['PostLocation']); ?>',
                                                desc: '<?php echo addslashes($req['PostDesc']); ?>',
                                                resp: '<?php echo addslashes($req['PostResp']); ?>',
                                                reqs: '<?php echo addslashes($req['PostReq']); ?>'
                                            })">
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
              <label style="display: block; font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 8px;">Key Responsibilities (One per line)</label>
              <textarea id="modalResponsibilities" rows="4" style="width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; resize: none;"></textarea>
          </div>

          <div class="form-group" style="margin-bottom: 30px;">
              <label style="display: block; font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 8px;">Requirements (One per line)</label>
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

    function createJobPost(reqId, posName, deptName, salaryRange) {
        currentReqId = reqId;
        currentPosName = posName;
        
        document.getElementById('postAction').value = 'save_job_post';
        document.getElementById('modalMainTitle').innerText = 'Create Official Job Posting';
        document.getElementById('modalSubmitBtn').innerText = 'Post Now';

        document.getElementById('modalPosDisplay').value = posName;
        document.getElementById('modalDeptDisplay').value = deptName;
        document.getElementById('modalSalary').value = `₱${salaryRange}`;
        document.getElementById('modalLocation').value = "Quezon City";
        
        // Clear fields
        document.getElementById('modalDescription').value = "";
        document.getElementById('modalResponsibilities').value = "";
        document.getElementById('modalRequirements').value = "";

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
                text: 'Please fill in the description, responsibilities, and requirements.'
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






