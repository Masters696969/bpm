<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../../login.php");
    exit();
}

require_once '../../config/config.php';

$period_id = 1;

// Fetch Salary Grades
$grades_query = $conn->query("
    SELECT sg.SalaryGradeID, sg.GradeLevel, sg.GradeName, sg.MinSalary, sg.MaxSalary, sg.MidSalary, sg.Description
    FROM salary_grades sg 
    WHERE sg.period_id = $period_id 
    ORDER BY sg.SalaryGradeID ASC
");
$salary_grades = [];
while ($row = $grades_query->fetch_assoc()) {
    $salary_grades[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Salary & Scales Management</title>
  <link rel="stylesheet" href="../../css/salary.css?v=1.0">
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
          <i data-lucide="layout-dashboard"></i>
          <span>Dashboard</span>
        </a>

        <div class="nav-item-group <?php echo ($module === 'hr') ? 'active' : ''; ?>">
          <button class="nav-item has-submenu" data-module="hr">
            <div class="nav-item-content">
              <i data-lucide="book-user"></i>
              <span>Core Human Capital</span>
            </div>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
          </button>
          <div class="submenu" id="submenu-hr">
            <a href="../admin/dispatch.php" class="submenu-item">
              <i data-lucide="send"></i>
              <span>Master Data Dispatch</span>
            </a>
            <a href="" class="submenu-item">
              <i data-lucide="user-plus"></i>
              <span>New Hired Onboard Request</span>
            </a>
            <a href="../admin/employeemaster.php" class="submenu-item <?php echo ($page === 'employeemaster') ? 'active' : ''; ?>">
              <i data-lucide="file-user"></i>
              <span>Employee Master Files</span>
            </a>
            <a href="bankform.php" class="submenu-item <?php echo ($page === 'bankform') ? 'active' : ''; ?>">
              <i data-lucide="file-text"></i>
              <span>Bank Form Management</span>
            </a>
            <a href="" class="submenu-item">
              <i data-lucide="user-cog"></i>
              <span>Security Settings</span>
            </a>
            <a href="../admin/auditlogs.php" class="submenu-item <?php echo ($page === 'auditlogs') ? 'active' : ''; ?>">
              <i data-lucide="book-user"></i>
              <span>Audit Logs</span>
            </a>
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
            <a href="intake.php" class="submenu-item <?php echo ($page === 'intake') ? 'active' : ''; ?>">
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
      </div>
      <div class="nav-section">
          <span class="nav-section-title">FINANCE</span>
          <div class="nav-item-group">
            <button class="nav-item has-submenu" data-module="budget">
              <div class="nav-item-content">
                <i data-lucide="hand-coins"></i>
                <span>Budget Management</span>
              </div>
              <i data-lucide="chevron-down" class="submenu-icon"></i>
            </button>
            <div class="submenu" id="submenu-budget">
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
      <section class="comp-panel">
        <div class="comp-panel-header">
          <div class="comp-panel-left">
            <div class="comp-panel-icon"><i data-lucide="layers"></i></div>
            <div class="comp-panel-titles">
              <h2>Regional Salary Scales (Philippines)</h2>
              <div class="comp-panel-sub">Base pay ranges adjusted for National Capital Region (NCR).</div>
            </div>
          </div>
          <div class="comp-panel-actions" style="display: flex; gap: 12px;">
            <button class="btn btn-outline" id="btnTrackStatus" style="border: 1px solid var(--border-color); color: var(--text-secondary);">
              <i data-lucide="eye"></i>
              <span>Track Status</span>
            </button>
            <button class="btn btn-primary" id="btnProposeChange">
              <i data-lucide="git-pull-request"></i>
              <span>Propose Change</span>
            </button>
          </div>
        </div>

        <div class="panel-body" style="margin-top: 32px;">
          <div class="table-responsive">
            <table class="comp-table editable-table" id="salaryGradeTable">
              <thead>
                <tr>
                  <th>Job Grade</th>
                  <th>Level Name</th>
                  <th>Description</th>
                  <th>Minimum (Monthly)</th>
                  <th>Midpoint</th>
                  <th>Maximum (Monthly)</th>
                  <th>Spread</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($salary_grades as $grade): ?>
                <tr data-id="<?php echo $grade['SalaryGradeID']; ?>">
                  <td><span class="table-text-premium"><?php echo htmlspecialchars($grade['GradeLevel']); ?></span></td>
                  <td><span class="table-text-premium"><?php echo htmlspecialchars($grade['GradeName']); ?></span></td>
                  <td><span class="table-text-premium text-muted"><?php echo htmlspecialchars($grade['Description'] ?? $grade['description'] ?? ''); ?></span></td>
                  <td><div class="input-with-symbol"><span>&#8369;</span><input type="number" value="<?php echo (int)$grade['MinSalary']; ?>" class="table-input-premium min-salary-input"></div></td>
                  <td><div class="input-with-symbol"><span>&#8369;</span><input type="number" value="<?php echo (int)$grade['MidSalary']; ?>" class="table-input-premium mid-salary-input" readonly></div></td>
                  <td><div class="input-with-symbol"><span>&#8369;</span><input type="number" value="<?php echo (int)$grade['MaxSalary']; ?>" class="table-input-premium max-salary-input"></div></td>
                  <td class="spread-cell"><?php 
                    $min = (float)$grade['MinSalary'];
                    $max = (float)$grade['MaxSalary'];
                    $spread = ($min > 0) ? (($max - $min) / $min) * 100 : 0;
                    echo number_format($spread, 1); 
                  ?>%</td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </section>
    </div>

    <!-- Modals from cycle.php -->
    <!-- Add Grade Modal -->
    <div id="gradeModal" class="modal" aria-hidden="true" style="display: none;">
      <div class="modal-dialog">
        <div class="comp-modal-hero">
          <div class="comp-modal-hero-inner">
            <div class="comp-modal-hero-icon">
              <i data-lucide="layers"></i>
            </div>
            <div class="comp-modal-hero-text">
              <h3>Add Salary Grade</h3>
              <p>Define a new pay level for the current period.</p>
            </div>
            <button class="rp-close-modal" id="closeGradeModalBtn" title="Close">&times;</button>
          </div>
        </div>
        <div class="modal-context-box">
           <div class="modal-context-icon">
             <i data-lucide="layers"></i>
           </div>
           <div class="modal-context-text">
             Configuring for: <strong>2026 Compensation Cycle</strong>
           </div>
        </div>
        <div class="modal-body modal-form-premium">
          <form id="gradeForm">
            <div class="form-group">
              <label>Job Grade <span class="required">*</span></label>
              <input type="text" name="grade_level" class="input-premium no-icon" placeholder="e.g. SG-7" required />
            </div>
            <div class="form-group">
              <label>Level Name <span class="required">*</span></label>
              <input type="text" name="grade_name" class="input-premium no-icon" placeholder="e.g. Senior Associate" required />
            </div>
            <div class="form-group">
              <label>Description</label>
              <textarea name="description" rows="2" class="input-premium no-icon" placeholder="Briefly describe the responsibilities..."></textarea>
            </div>
            <div class="form-row-triple">
              <div class="form-group">
                <label>Min Salary</label>
                <input type="number" name="min_salary" id="modal_min_salary" class="input-premium no-icon" value="0" />
              </div>
              <div class="form-group">
                <label>Midpoint</label>
                <input type="number" id="modal_mid_salary" class="input-premium no-icon" value="0" readonly title="Auto-calculated" />
              </div>
              <div class="form-group">
                <label>Max Salary</label>
                <input type="number" name="max_salary" id="modal_max_salary" class="input-premium no-icon" value="0" />
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer-premium">
          <button type="button" id="cancelGrade" class="btn-cancel-premium">Cancel</button>
          <button type="submit" form="gradeForm" class="btn-comp-submit">Save Grade</button>
        </div>
      </div>
    </div>

    <!-- Propose Change Modal -->
    <div id="proposeChangeModal" class="modal" aria-hidden="true" style="display: none;">
      <div class="modal-dialog" style="max-width: 800px;">
        <div class="comp-modal-hero">
          <div class="comp-modal-hero-inner">
            <div class="comp-modal-hero-icon">
              <i data-lucide="git-pull-request"></i>
            </div>
            <div class="comp-modal-hero-text">
              <h3>Propose Salary Scale Changes</h3>
              <p>Submit a proposal to update the minimum and maximum ranges for specific job grades.</p>
            </div>
            <button class="rp-close-modal" id="closeProposeModalBtn" title="Close">&times;</button>
          </div>
        </div>
        <div class="modal-body modal-form-premium" style="max-height: 50vh; overflow-y: auto;">
          <div class="table-responsive">
            <table class="comp-table editable-table" id="proposeScaleTable">
              <thead>
                <tr>
                  <th>Job Grade</th>
                  <th>Level Name</th>
                  <th>Current Min</th>
                  <th>Proposed Min</th>
                  <th>Current Max</th>
                  <th>Proposed Max</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($salary_grades as $grade): ?>
                <tr data-id="<?php echo $grade['SalaryGradeID']; ?>">
                  <td><strong><?php echo htmlspecialchars($grade['GradeLevel']); ?></strong></td>
                  <td><?php echo htmlspecialchars($grade['GradeName']); ?></td>
                  <td class="text-muted">&#8369;<?php echo number_format($grade['MinSalary'], 2); ?></td>
                  <td><div class="input-with-symbol"><span>&#8369;</span><input type="number" class="table-input-premium prop-min-input" data-original="<?php echo (int)$grade['MinSalary']; ?>" value="<?php echo (int)$grade['MinSalary']; ?>"></div></td>
                  <td class="text-muted">&#8369;<?php echo number_format($grade['MaxSalary'], 2); ?></td>
                  <td><div class="input-with-symbol"><span>&#8369;</span><input type="number" class="table-input-premium prop-max-input" data-original="<?php echo (int)$grade['MaxSalary']; ?>" value="<?php echo (int)$grade['MaxSalary']; ?>"></div></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <div class="form-group" style="margin-top: 20px;">
            <label>Reason for Proposal <span class="required">*</span></label>
            <textarea id="proposalReason" rows="3" class="input-premium no-icon" placeholder="Explain the rationale behind these proposed changes..."></textarea>
          </div>

          <div class="form-group" style="margin-top: 16px;">
            <label>Supporting Document <span style="font-weight:400; color:var(--text-tertiary);">(Optional — Government file, Salary Structure, etc.)</span></label>
            <div class="proof-upload-area" id="proofUploadArea">
              <input type="file" id="proofFileInput" accept=".pdf,.doc,.docx,image/*">
              <div class="proof-upload-icon"><i data-lucide="upload-cloud"></i></div>
              <span class="proof-upload-label">Click or drag to upload proof</span>
              <span class="proof-upload-hint">PDF, Word, or Image (Max 5MB)</span>
            </div>
            <div class="proof-file-badge" id="proofFileBadge">
              <i data-lucide="file-check"></i>
              <span id="proofBadgeName">No file selected</span>
            </div>
          </div>
        </div>
        <div class="modal-footer-premium">
          <button type="button" id="cancelProposeBtn" class="btn-cancel-premium">Cancel</button>
          <button type="button" id="submitProposalScaleBtn" class="btn-comp-submit">Submit Proposal</button>
        </div>
      </div>
    </div>

    <!-- Track Status Modal -->
    <div id="trackStatusModal" class="modal" aria-hidden="true" style="display: none;">
      <div class="modal-dialog" style="max-width: 800px;">
        <div class="comp-modal-hero">
          <div class="comp-modal-hero-inner">
            <div class="comp-modal-hero-icon" style="background: rgba(255, 255, 255, 0.2);">
              <i data-lucide="activity"></i>
            </div>
            <div class="comp-modal-hero-text">
              <h3>Track Proposal Status</h3>
              <p>Monitor your submitted scale changes through the review process.</p>
            </div>
            <button class="rp-close-modal" id="closeTrackModalBtn" title="Close">&times;</button>
          </div>
        </div>
        <div class="modal-body" style="padding: 24px; max-height: 70vh; overflow-y: auto;">
          <div id="stageAList">
              <div class="data-table-premium">
                  <table style="width: 100%; border-collapse: separate; border-spacing: 0 8px;">
                      <thead>
                          <tr style="background: transparent;">
                              <th style="padding: 12px; font-size: 11px; text-transform: uppercase; color: var(--text-tertiary); letter-spacing: 0.05em;">Reference</th>
                              <th style="padding: 12px; font-size: 11px; text-transform: uppercase; color: var(--text-tertiary); letter-spacing: 0.05em;">Date Submitted</th>
                              <th style="padding: 12px; font-size: 11px; text-transform: uppercase; color: var(--text-tertiary); letter-spacing: 0.05em;">Changes</th>
                              <th style="padding: 12px; font-size: 11px; text-transform: uppercase; color: var(--text-tertiary); letter-spacing: 0.05em;">Status</th>
                              <th style="padding: 12px; font-size: 11px; text-transform: uppercase; color: var(--text-tertiary); letter-spacing: 0.05em; text-align: right;">Action</th>
                          </tr>
                      </thead>
                      <tbody id="trackingBatchesBody">
                          <tr><td colspan="5" style="text-align:center; padding:60px; color: var(--text-tertiary);">
                              <i data-lucide="inbox" style="width: 48px; height: 48px; opacity: 0.2; margin-bottom: 12px;"></i>
                              <p>No tracking history available</p>
                          </td></tr>
                      </tbody>
                  </table>
              </div>
              <div class="pagination-premium" style="display: flex; justify-content: space-between; align-items: center; margin-top: 16px; padding: 0 8px;">
                  <div style="font-size: 13px; color: var(--text-secondary);">Showing <span id="salaryPageRange">0</span> of <span id="salaryTotalEntries">0</span></div>
                  <div style="display: flex; gap: 8px;">
                      <button id="prevSalaryPage" class="btn-pagination" disabled><i data-lucide="chevron-left"></i></button>
                      <button id="nextSalaryPage" class="btn-pagination" disabled><i data-lucide="chevron-right"></i></button>
                  </div>
              </div>
          </div>
          <div id="stageBDetails" style="display: none;">
              <button type="button" class="btn btn-outline" id="btnBackToTrackList" style="margin-bottom: 20px;">
                  <i data-lucide="arrow-left"></i> Back to List
              </button>
              <h4 style="margin-bottom: 8px;" id="stepperBatchTitle">Batch Details</h4>
              <p style="color: var(--text-secondary); margin-bottom: 24px;" id="stepperBatchReason"></p>



              <div class="stepper-wrapper" style="background:var(--surface-hover); border-radius:12px; padding:32px 16px; display: flex; justify-content: center;">
                  <div class="stepper-container">
                      <div class="stepper-line"><div class="stepper-line-fill" id="stepperLineFill" style="width: 0%;"></div></div>
                      <div class="stepper-step" id="step1">
                          <div class="step-circle"><i data-lucide="check"></i></div>
                          <div class="step-label">Step 1</div><div class="step-title">Submitted</div><div class="step-desc" id="step1Desc">Completed</div>
                      </div>
                      <div class="stepper-step" id="step2">
                          <div class="step-circle"><i data-lucide="circle-dot"></i></div>
                          <div class="step-label">Step 2</div><div class="step-title">Under Review</div><div class="step-desc" id="step2Desc">Pending</div>
                      </div>
                      <div class="stepper-step" id="step3">
                          <div class="step-circle"></div>
                          <div class="step-label">Step 3</div><div class="step-title">Endorsed</div><div class="step-desc" id="step3Desc">Pending</div>
                      </div>
                      <div class="stepper-step" id="step4">
                          <div class="step-circle"></div>
                          <div class="step-label">Step 4</div><div class="step-title" id="step4Title">Approved</div><div class="step-desc" id="step4Desc">Pending</div>
                      </div>
                  </div>
              </div>
          </div>
        </div>
      </div>
    </div>
  </main>
  <script src="../../js/salary.js"></script>
  <script>
    lucide.createIcons();
  </script>
</body>
</html>







