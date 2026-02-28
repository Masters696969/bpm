<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

require_once '../../config/config.php';

$userId = $_SESSION['user_id'];
$employee = null;

// Fetch employee details
$sql = "SELECT e.*, d.DepartmentName, p.PositionName 
        FROM employee e
        JOIN useraccounts ua ON e.EmployeeID = ua.EmployeeID
        LEFT JOIN employmentinformation ei ON e.EmployeeID = ei.EmployeeID
        LEFT JOIN department d ON ei.DepartmentID = d.DepartmentID
        LEFT JOIN positions p ON ei.PositionID = p.PositionID
        WHERE ua.AccountID = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
if ($result && $result->num_rows > 0) {
    $employee = $result->fetch_assoc();
}

$fullName = $employee ? trim(($employee['FirstName'] ?? '') . ' ' . ($employee['MiddleName'] ?? '') . ' ' . ($employee['LastName'] ?? '')) : 'Unknown User';
$initials = $employee ? strtoupper(substr($employee['FirstName'] ?? 'U', 0, 1) . substr($employee['LastName'] ?? 'N', 0, 1)) : '??';
$profilePhoto = !empty($employee['ProfilePhoto']) ? '../../' . $employee['ProfilePhoto'] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Profile | Microfinance</title>
  <link rel="stylesheet" href="../../css/profile.css?v=1.3">
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
          <button class="nav-item has-submenu" data-module="hr">
            <div class="nav-item-content">
              <i data-lucide="users"></i>
              <span>Account Management</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-hr">
            <a href="useraccount.php" class="submenu-item">
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
          <h1>My Profile</h1>
          <p>Welcome back, <?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?>! Manage your personal information.</p>
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
      <!-- Top Stats Section -->
      <div class="profile-stats-grid">
        <div class="stat-card-premium">
          <div class="stat-info">
            <span class="stat-label">Total Activities</span>
            <h2 class="stat-value">282</h2>
            <p class="stat-sub">All recorded activities</p>
          </div>
          <div class="stat-icon-box activity">
            <i data-lucide="line-chart"></i>
          </div>
        </div>

        <div class="stat-card-premium">
          <div class="stat-info">
            <span class="stat-label">Total Logins</span>
            <h2 class="stat-value">103</h2>
            <p class="stat-sub">Successful logins</p>
          </div>
          <div class="stat-icon-box login">
            <i data-lucide="log-in"></i>
          </div>
        </div>
      </div>

      <!-- Main Profile Grid -->
      <div class="profile-main-grid">
        <!-- Left Sidebar Column -->
        <aside class="profile-sidebar">
          <div class="profile-card-main">
            <div class="profile-avatar-wrapper">
              <div class="profile-avatar-circle" id="profile-photo-container">
                <?php if ($profilePhoto): ?>
                  <img src="<?php echo htmlspecialchars($profilePhoto); ?>" alt="Profile Photo" id="sidebar-profile-img">
                <?php else: ?>
                  <span id="sidebar-profile-initials"><?php echo $initials; ?></span>
                <?php endif; ?>
                <div class="btn-change-photo" onclick="document.getElementById('ProfilePhotoInput').click()">
                  <i data-lucide="camera"></i>
                </div>
              </div>
            </div>
            
            <div class="profile-basic-info">
              <h1 class="profile-name"><?php echo htmlspecialchars($fullName); ?></h1>
              <p class="profile-role-title"><?php echo htmlspecialchars($employee['PositionName'] ?? 'Position'); ?></p>
            </div>

            <div class="profile-sidebar-cards">
              <div class="sidebar-mini-card">
                <div class="mini-card-icon id">
                  <i data-lucide="hash"></i>
                </div>
                <div class="mini-card-text">
                  <span class="mini-label">Employee ID</span>
                  <span class="mini-value"><?php echo htmlspecialchars($employee['EmployeeCode'] ?? 'N/A'); ?></span>
                </div>
              </div>

              <div class="sidebar-mini-card">
                <div class="mini-card-icon dept">
                  <i data-lucide="building-2"></i>
                </div>
                <div class="mini-card-text">
                  <span class="mini-label">Department</span>
                  <span class="mini-value"><?php echo htmlspecialchars($employee['DepartmentName'] ?? 'No Department'); ?></span>
                </div>
              </div>
            </div>
          </div>
        </aside>

        <!-- Right Content Section -->
        <section class="profile-details-main">
          <div class="details-card">
            <div class="details-header">
              <div class="header-text">
                <h2 class="section-title">Profile Information</h2>
                <p class="section-subtitle">Update your personal details and contact information</p>
              </div>
              <button class="btn-edit-trigger" id="btnEditProfile">
                <i data-lucide="edit-3"></i>
                <span>EDIT PROFILE</span>
              </button>
            </div>

            <div class="details-grid">
              <div class="detail-field">
                <label>Full Name</label>
                <div class="field-value-box">
                  <i data-lucide="user"></i>
                  <?php echo htmlspecialchars($fullName); ?>
                </div>
              </div>

              <div class="detail-field">
                <label>Employee ID</label>
                <div class="field-value-box">
                  <i data-lucide="fingerprint"></i>
                  <?php echo htmlspecialchars($employee['EmployeeCode'] ?? 'N/A'); ?>
                </div>
              </div>

              <div class="detail-field">
                <label>Email Address</label>
                <div class="field-value-box">
                  <i data-lucide="mail"></i>
                  <?php echo htmlspecialchars($employee['PersonalEmail'] ?? 'N/A'); ?>
                </div>
              </div>

              <div class="detail-field">
                <label>Department</label>
                <div class="field-value-box">
                  <i data-lucide="building-2"></i>
                  <?php echo htmlspecialchars($employee['DepartmentName'] ?? 'N/A'); ?>
                </div>
              </div>

              <div class="detail-field">
                <label>Role</label>
                <div class="field-value-box">
                  <i data-lucide="briefcase"></i>
                  <?php echo htmlspecialchars($employee['PositionName'] ?? 'N/A'); ?>
                </div>
              </div>

              <div class="detail-field">
                <label>Mobile Number</label>
                <div class="field-value-box">
                  <i data-lucide="phone"></i>
                  <?php echo htmlspecialchars($employee['PhoneNumber'] ?? 'N/A'); ?>
                </div>
              </div>

              <div class="detail-field full-width">
                <label>Address</label>
                <div class="field-value-box">
                  <i data-lucide="map-pin"></i>
                  <?php echo htmlspecialchars($employee['PermanentAddress'] ?? 'N/A'); ?>
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>
    </div>
  </main>

  <!-- Edit Profile Modal -->
  <div id="editProfileModal" class="modal-overlay">
    <div class="modal-card">
      <div class="modal-header">
        <div class="modal-header-content">
          <div class="modal-icon-title">
            <div class="mti-icon"><i data-lucide="user-cog"></i></div>
            <div class="mti-text">
              <h3>Edit Profile Information</h3>
              <p>Update your personal and contact details</p>
            </div>
          </div>
          <button class="btn-close-modern" id="closeModal">
            <i data-lucide="x"></i>
          </button>
        </div>
      </div>
      <form id="editProfileForm" enctype="multipart/form-data">
        <div class="modal-body">
          <div class="edit-profile-layout">
            <!-- Left: Photo Upload -->
            <div class="photo-upload-section">
              <div class="photo-preview-box" id="photo-preview-box">
                <?php if ($profilePhoto): ?>
                  <img src="<?php echo htmlspecialchars($profilePhoto); ?>" alt="Preview" id="modal-img-preview">
                <?php else: ?>
                  <div class="preview-placeholder" id="modal-preview-initials"><?php echo $initials; ?></div>
                <?php endif; ?>
                <div class="upload-overlay" onclick="document.getElementById('ProfilePhotoInput').click()">
                  <div class="uo-content">
                    <i data-lucide="camera"></i>
                    <span>Change Photo</span>
                  </div>
                </div>
              </div>
              <input type="file" name="ProfilePhoto" id="ProfilePhotoInput" hidden accept="image/*">
              <p class="upload-hint">Recommended: JPG or PNG<br>Max size: 2MB</p>
            </div>

            <!-- Right: Form Fields -->
            <div class="form-grid">
              <div class="form-group-modern">
                <label><i data-lucide="user"></i> First Name</label>
                <div class="input-with-icon">
                  <input type="text" name="FirstName" value="<?php echo htmlspecialchars($employee['FirstName'] ?? ''); ?>" required placeholder="Enter first name">
                </div>
              </div>
              <div class="form-group-modern">
                <label><i data-lucide="user"></i> Last Name</label>
                <div class="input-with-icon">
                  <input type="text" name="LastName" value="<?php echo htmlspecialchars($employee['LastName'] ?? ''); ?>" required placeholder="Enter last name">
                </div>
              </div>
              <div class="form-group-modern">
                <label><i data-lucide="mail"></i> Email Address</label>
                <div class="input-with-icon">
                  <input type="email" name="PersonalEmail" value="<?php echo htmlspecialchars($employee['PersonalEmail'] ?? ''); ?>" placeholder="example@mail.com">
                </div>
              </div>
              <div class="form-group-modern">
                <label><i data-lucide="phone"></i> Phone Number</label>
                <div class="input-with-icon">
                  <input type="text" name="PhoneNumber" value="<?php echo htmlspecialchars($employee['PhoneNumber'] ?? ''); ?>" placeholder="0912 345 6789">
                </div>
              </div>
              <div class="form-group-modern full-width">
                <label><i data-lucide="map-pin"></i> Permanent Address</label>
                <div class="input-with-icon">
                  <textarea name="PermanentAddress" placeholder="Enter your full home address"><?php echo htmlspecialchars($employee['PermanentAddress'] ?? ''); ?></textarea>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-cancel-modern" id="btnCancel">Discard</button>
          <button type="submit" class="btn-save-modern">
            <i data-lucide="save"></i>
            <span>Save Changes</span>
          </button>
        </div>
      </form>
    </div>
  </div>
  <script src="../../js/profile.js"></script>
  <script>
    lucide.createIcons();
  </script>
</body>
</html>




