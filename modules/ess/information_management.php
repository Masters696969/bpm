
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
  <link rel="stylesheet" href="../../css/informationmanagement.css?v=1.1">
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
        
        <a href="dashboard.php" class="nav-item">
          <i data-lucide="chart-no-axes-combined"></i>
          <span>Dashboard</span>
        </a>

        <a href="#" class="nav-item">
              <i data-lucide="file-clock"></i>
              <span>Time Attendance</span>
            </a>
            <a href="information_management.php" class="nav-item active">
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
          <h1>Information Management</h1>
          <p>View and manage your personal information.</p>
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
        <div class="resume-container">
            <div class="resume-header">
                <div class="header-content">
                    <div class="profile-photo-wrapper">
                        <div class="profile-photo" id="profilePhotoContainer">
                             <div class="avatar-placeholder" id="avatarPlaceholder"></div>
                        </div>
                    </div>
                    <div class="header-text">
                        <h2 id="employeeName">Loading...</h2>
                        <p class="position" id="employeePosition">Loading...</p>
                        <p class="department" id="employeeDepartment"><i data-lucide="building-2"></i> Loading...</p>
                    </div>
                </div>
                <div class="header-status" style="display: flex; gap: 10px; align-items: center;">
                    <button type="submit" form="myInfoForm" class="btn btn-save" style="margin-right: 10px;">
                        <i data-lucide="save"></i> Save Changes
                    </button>
                    <button type="button" class="btn btn-save" id="btnRequestEdit">
                        <i data-lucide="pencil"></i> Request Edit
                    </button>
                </div>
            </div>

            <form id="myInfoForm">
                <div class="resume-grid">
                    <!-- Personal Information (Editable) -->
                    <div class="resume-section">
                        <div class="section-header">
                            <h3><i data-lucide="user"></i> Personal Information</h3>
                            <span class="badge-edit">Editable</span>
                        </div>
                        <div class="info-grid">
                            <div class="info-item">
                                <label>First Name</label>
                                <input type="text" name="FirstName" id="FirstName" class="form-control" required>
                            </div>
                            <div class="info-item">
                                <label>Last Name</label>
                                <input type="text" name="LastName" id="LastName" class="form-control" required>
                            </div>
                            <div class="info-item">
                                <label>Middle Name</label>
                                <input type="text" name="MiddleName" id="MiddleName" class="form-control">
                            </div>
                            <div class="info-item">
                                <label>Date of Birth</label>
                                <input type="date" name="DateOfBirth" id="DateOfBirth" class="form-control" readonly>
                            </div>
                            <div class="info-item">
                                <label>Gender</label>
                                <select name="Gender" id="Gender" class="form-control" readonly>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                            <div class="info-item full-width">
                                <label>Permanent Address</label>
                                <input type="text" name="PermanentAddress" id="PermanentAddress" class="form-control">
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information (Editable) -->
                    <div class="resume-section">
                        <div class="section-header">
                            <h3><i data-lucide="phone"></i> Contact Information</h3>
                            <span class="badge-edit">Editable</span>
                        </div>
                        <div class="info-grid">
                            <div class="info-item">
                                <label>Phone Number</label>
                                <input type="text" name="PhoneNumber" id="PhoneNumber" class="form-control">
                            </div>
                            <div class="info-item full-width">
                                <label>Personal Email</label>
                                <input type="email" name="PersonalEmail" id="PersonalEmail" class="form-control">
                            </div>
                        </div>
                    </div>

                    <!-- Emergency Contact (Editable) -->
                    <div class="resume-section">
                        <div class="section-header">
                            <h3><i data-lucide="phone-call"></i> Emergency Contact</h3>
                            <span class="badge-edit">Editable</span>
                        </div>
                        <div class="info-grid">
                            <div class="info-item">
                                <label>Contact Name</label>
                                <input type="text" name="ContactName" id="ContactName" class="form-control">
                            </div>
                            <div class="info-item">
                                <label>Relationship</label>
                                <input type="text" name="Relationship" id="Relationship" class="form-control">
                            </div>
                            <div class="info-item">
                                <label>Phone Number</label>
                                <input type="text" name="EmergencyPhone" id="EmergencyPhone" class="form-control">
                            </div>
                        </div>
                    </div>

                    <!-- Employment Details (Read-Only) -->
                    <div class="resume-section read-only">
                         <div class="section-header">
                            <h3><i data-lucide="briefcase"></i> Employment Details</h3>
                            <span class="badge-readonly"><i data-lucide="lock"></i> Read Only</span>
                        </div>
                        <div class="info-grid">
                            <div class="info-item">
                                <label>Employee Code</label>
                                <input type="text" id="EmployeeCode" class="form-control" readonly>
                            </div>
                            <div class="info-item">
                                <label>Date Hired</label>
                                <input type="text" id="HiringDate" class="form-control" readonly>
                            </div>
                            <div class="info-item">
                                <label>Work Email</label>
                                <input type="text" id="WorkEmail" class="form-control" readonly>
                            </div>
                            <div class="info-item">
                                <label>Digital Resume</label>
                                <div class="readonly-value" id="DigitalResumeContainer">
                                    No resume uploaded
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Compensation (Read-Only) -->
                    <div class="resume-section read-only">
                        <div class="section-header">
                            <h3><i data-lucide="wallet"></i> Compensation</h3>
                             <span class="badge-readonly"><i data-lucide="lock"></i> Read Only</span>
                        </div>
                        <div class="info-grid">
                            <div class="info-item">
                                <label>Salary Grade</label>
                                <input type="text" id="GradeLevel" class="form-control" readonly>
                            </div>
                            <div class="info-item">
                                <label>Salary Range</label>
                                <input type="text" id="SalaryRange" class="form-control" readonly>
                            </div>
                        </div>
                    </div>

                    <!-- Bank Details (Read-Only) -->
                    <div class="resume-section read-only">
                        <div class="section-header">
                            <h3><i data-lucide="landmark"></i> Bank Details</h3>
                             <span class="badge-readonly"><i data-lucide="lock"></i> Read Only</span>
                        </div>
                        <div class="info-grid">
                            <div class="info-item">
                                <label>Bank Name</label>
                                <input type="text" id="BankName" class="form-control" readonly>
                            </div>
                            <div class="info-item">
                                <label>Account Number</label>
                                <input type="text" id="BankAccountNumber" class="form-control" readonly>
                            </div>
                             <div class="info-item">
                                <label>Account Type</label>
                                <input type="text" id="AccountType" class="form-control" readonly>
                            </div>
                        </div>
                    </div>

                    <!-- Government Numbers (Read-Only) -->
                    <div class="resume-section read-only">
                         <div class="section-header">
                            <h3><i data-lucide="file-check"></i> Government Numbers</h3>
                             <span class="badge-readonly"><i data-lucide="lock"></i> Read Only</span>
                        </div>
                        <div class="info-grid">
                            <div class="info-item">
                                <label>TIN</label>
                                <input type="text" id="TINNumber" class="form-control" readonly>
                            </div>
                            <div class="info-item">
                                <label>SSS</label>
                                <input type="text" id="SSSNumber" class="form-control" readonly>
                            </div>
                            <div class="info-item">
                                <label>PhilHealth</label>
                                <input type="text" id="PhilHealthNumber" class="form-control" readonly>
                            </div>
                            <div class="info-item">
                                <label>Pag-IBIG</label>
                                <input type="text" id="PagIBIGNumber" class="form-control" readonly>
                            </div>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
    <!-- Request Edit Modal -->
    <div class="modal-overlay hidden" id="requestEditModal">
      <div class="modal-content modal-content-styled">
        <div class="modal-header-styled">
            <div>
                <h3 class="modal-title-custom">Request Information Update</h3>
                <p class="modal-subtitle-custom">Changes will be reviewed by HR before applying.</p>
            </div>
          <button id="btnCloseRequestModal" class="close-modal-btn">
            <i data-lucide="x" class="icon-sm"></i> Close
          </button>
        </div>
        <div class="modal-body-scroll">
            <form id="requestEditForm">
                <!-- Compensation -->
                <h4 class="section-title text-blue">Compensation</h4>
                <div class="info-group">
                     <div class="info-row">
                        <label>Bank Name</label>
                        <input type="text" name="BankName" class="form-control" placeholder="Enter Bank Name">
                    </div>
                    <div class="info-row">
                        <label>Account Number</label>
                        <input type="text" name="BankAccountNumber" class="form-control" placeholder="Enter Account Number">
                    </div>
                </div>

                <!-- Government Numbers -->
                <h4 class="section-title text-green">Government Numbers</h4>
                 <div class="info-group">
                    <div class="info-row">
                        <label>TIN</label>
                        <input type="text" name="TINNumber" class="form-control">
                    </div>
                    <div class="info-row">
                        <label>SSS</label>
                        <input type="text" name="SSSNumber" class="form-control">
                    </div>
                     <div class="info-row">
                        <label>PhilHealth</label>
                        <input type="text" name="PhilHealthNumber" class="form-control">
                    </div>
                     <div class="info-row">
                        <label>Pag-IBIG</label>
                        <input type="text" name="PagIBIGNumber" class="form-control">
                    </div>
                </div>
                
                 <div class="modal-footer-styled">
                    <button type="submit" class="btn-create-master">
                        <i data-lucide="send" class="icon-sm"></i> Send Request
                    </button>
                </div>
            </form>
        </div>
      </div>
    </div>
</main>
  <script src="../../js/sidebar-active.js"></script>
  <script src="../../js/chcdashboard.js"></script>
  <script src="../../js/hr1informationmanagement.js?v=<?php echo time(); ?>"></script>
  <script>
    lucide.createIcons();
  </script>
  
</body>
</html>
