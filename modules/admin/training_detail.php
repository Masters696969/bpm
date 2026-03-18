<?php
session_start();
require_once '../../config/config.php';

if (!isset($_SESSION['username'])) {
    header("Location: ../../index.php");
    exit();
}

$module_id = $_GET['id'] ?? null;

if (!$module_id) {
    echo "Invalid Training Module ID.";
    exit();
}

$query = "SELECT * FROM training_modules WHERE ModuleID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $module_id);
$stmt->execute();
$module = $stmt->get_result()->fetch_assoc();

if (!$module) {
    echo "Training program not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Training Detail - <?php echo htmlspecialchars($module['ModuleName']); ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/lucide@latest"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="../../css/dashboard.css">
  <link rel="stylesheet" href="../../css/trainingmgt.css">

  <script>
    if (localStorage.getItem("theme") === "dark") {
        document.documentElement.classList.add("dark-mode");
    }
  </script>
</head>
<body class="dashboard-body">

  <!-- Sidebar Component -->
  <?php include 'sidebar.php'; ?>

  <!-- Main Content -->
  <main class="main-content">
    <header class="page-header">
      <div class="header-left">
        <button class="mobile-menu-btn" id="mobileMenuBtn">
          <i data-lucide="menu"></i>
        </button>
        <div class="header-title">
          <h1>Training Module Viewer</h1>
          <p>Review standard training material before distribution.</p>
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
      
      <div style="margin-bottom: 24px;">
        <a href="trainingmgt.php" style="display:inline-flex; align-items:center; gap:8px; color:var(--text-secondary); text-decoration:none; font-size:14px;">
            <i data-lucide="arrow-left" style="width:16px; height:16px;"></i> Back to Training Programs
        </a>
      </div>

      <div style="background:var(--surface); border:1px solid var(--border-color); border-radius:12px; padding:32px;">
          <h2 style="font-size: 28px; font-weight: 600; color: var(--text-primary); margin-top:0; margin-bottom: 8px;">
            <?php echo htmlspecialchars($module['ModuleName']); ?>
          </h2>
          <p style="font-size: 15px; color: var(--text-secondary); margin-bottom: 32px; padding-bottom: 24px; border-bottom: 1px solid var(--border-color);">
            <?php echo htmlspecialchars($module['Description']); ?>
          </p>

          <div style="margin-bottom: 32px;">
            <?php if (!empty($module['file_path'])): ?>
                <h3 style="font-size:16px; font-weight:600; color:var(--text-primary); margin-bottom:16px;">Reference File</h3>
                <iframe src="<?php echo htmlspecialchars($module['file_path']); ?>" width="100%" height="500px" style="border:1px solid var(--border-color); border-radius:8px; margin-bottom: 12px;"></iframe>
            <?php endif; ?>

            <?php if (!empty($module['Content'])): ?>
                <h3 style="font-size:16px; font-weight:600; color:var(--text-primary); margin-bottom:16px; margin-top:24px;">Content text</h3>
                <div style="background:var(--background); padding:24px; border-radius:8px; border:1px solid var(--border-color); font-size:14.5px; line-height:1.6;">
                    <?php echo $module['Content']; ?>
                </div>
            <?php endif; ?>
          </div>

          <div style="border-top:1px solid var(--border-color); padding-top:24px;">
              <button id="btnOpenAssignModal" class="btn-primary" style="padding: 12px 24px; font-size: 15px; border-radius: 8px;">
                  Assign to Employees
              </button>
          </div>
      </div>

    </div>
  </main>

  <!-- Assign Training to Employees Modal -->
  <div class="modal-overlay" id="assignEmployeesModal">
    <div class="modal-content" style="max-width: 600px;">
        <div class="modal-header">
            <h3 style="font-size: 18px; font-weight: 600; color: var(--text-primary); margin: 0;">Assign Training</h3>
            <button class="close-modal" id="closeAssignModal">&times;</button>
        </div>
        <div style="padding: 24px;">
            <p style="font-size: 14px; color: var(--text-secondary); margin-bottom: 16px; margin-top:0;">
                Select employees to assign <strong><?php echo htmlspecialchars($module['ModuleName']); ?></strong>.
            </p>

            <!-- Quick Employee Search -->
            <input type="text" id="employeeSearch" placeholder="Search employees by name, role, or ID..." style="width:100%; padding:10px 12px; border:1px solid var(--border-color); border-radius:8px; margin-bottom:16px; background:var(--background); color:var(--text-primary); outline:none; box-sizing:border-box;">

            <!-- Hidden explicit module ID -->
            <input type="hidden" id="assignModuleId" value="<?php echo htmlspecialchars($module_id); ?>">
            
            <div id="employeesList" style="display: flex; flex-direction: column; gap: 8px; max-height: 350px; overflow-y: auto; padding-right: 8px;">
                <!-- Filled by JS -->
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-cancel" id="cancelAssignModal">Cancel</button>
            <button class="btn-primary" id="btnSaveAssignments"><i data-lucide="save"></i> Save Assignments</button>
        </div>
    </div>
  </div>

  <script src="../../js/training_detail.js?v=<?php echo time(); ?>"></script>
  <script>
    lucide.createIcons();
  </script>
</body>
</html>
