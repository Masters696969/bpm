<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Training Management - HR System</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/lucide@latest"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="../../css/dashboard.css">
  <link rel="stylesheet" href="../../css/trainingmgt.css">

  <!-- Inject theme explicitly -->
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
          <h1>Training Management</h1>
          <p>Assign and manage training modules for newly hired employees.</p>
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
      
      <!-- Page Title -->
      <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 24px;">
        <div>
          <h2 style="font-size: 24px; font-weight: 600; color: var(--text-primary);">Training Programs</h2>
          <p style="font-size: 14px; color: var(--text-secondary);">Manage company training modules and assign them to employees.</p>
        </div>
        <div style="display:flex; gap:12px;">
          <button class="btn-refresh" id="btnRefresh" style="padding: 8px 16px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--surface); cursor: pointer; color: var(--text-secondary); display:flex; align-items:center; gap:8px;">
              <i data-lucide="refresh-cw" style="width: 16px; height: 16px;"></i> Refresh
          </button>
          <button class="btn-primary" id="btnAddProgram" style="padding: 8px 16px; border-radius: 8px; border: none; background: var(--brand-green); cursor: pointer; color: white; display:flex; align-items:center; gap:8px; font-weight:500;">
              <i data-lucide="plus" style="width: 16px; height: 16px;"></i> Add Program
          </button>
        </div>
      </div>

      <!-- Training Programs Grid -->
      <div id="trainingModulesGrid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 24px;">
        <!-- Filled by JS -->
        <div class="empty-state" style="grid-column: 1 / -1; padding: 48px; background: var(--surface); border: 1px solid var(--border-color); border-radius: 12px; text-align: center;">
            <i data-lucide="loader-2" class="spin" style="color: var(--brand-green); width: 32px; height: 32px; margin-bottom: 16px;"></i>
            <h3 style="color: var(--text-primary);">Loading training programs...</h3>
        </div>
      </div>

    </div>
  </main>

  <style>
    .program-card {
        background: var(--surface);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 24px;
        display: flex;
        flex-direction: column;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .program-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.05);
    }
    .program-header {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 16px;
    }
    .program-icon {
        width: 48px;
        height: 48px;
        background: rgba(44, 160, 120, 0.1);
        color: var(--brand-green);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .program-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
    }
    .program-desc {
        font-size: 14px;
        color: var(--text-secondary);
        flex: 1;
        margin-bottom: 24px;
    }
    .program-footer {
        padding-top: 16px;
        border-top: 1px solid var(--border-color);
        display: flex;
        justify-content: flex-end;
    }
    .btn-view {
        background: var(--brand-green);
        color: white;
        text-decoration: none;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: background 0.2s;
    }
    .btn-view:hover {
        background: #238462;
    }
  </style>

  <!-- Add Training Program Modal -->
  <div class="modal-overlay" id="addProgramModal">
    <div class="modal-content" style="max-width: 500px;">
        <div class="modal-header">
            <h3 style="font-size: 18px; font-weight: 600; color: var(--text-primary); margin: 0;">Add Training Program</h3>
            <button class="close-modal" id="closeAddProgramModal">&times;</button>
        </div>
        <form id="addProgramForm" enctype="multipart/form-data">
            <div style="padding: 24px;">
                <div style="margin-bottom: 16px;">
                    <label style="display:block; font-size:14px; font-weight:500; color:var(--text-secondary); margin-bottom:8px;">Program Title</label>
                    <input type="text" name="ModuleName" required placeholder="e.g. Sales Training" style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:8px; background:var(--background); color:var(--text-primary); outline:none; box-sizing:border-box;">
                </div>
                <div style="margin-bottom: 16px;">
                    <label style="display:block; font-size:14px; font-weight:500; color:var(--text-secondary); margin-bottom:8px;">Description</label>
                    <textarea name="Description" required rows="3" placeholder="Brief details about the module..." style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:8px; background:var(--background); color:var(--text-primary); outline:none; box-sizing:border-box; resize:none;"></textarea>
                </div>
                <div style="margin-bottom: 16px;">
                    <label style="display:block; font-size:14px; font-weight:500; color:var(--text-secondary); margin-bottom:8px;">PDF File (Optional but Recommended)</label>
                    <input type="file" name="training_file" accept=".pdf" style="width:100%; padding:8px; border:1px dashed var(--border-color); border-radius:8px; background:var(--surface); color:var(--text-primary); cursor:pointer; box-sizing:border-box;">
                    <small style="color:var(--text-secondary); display:block; margin-top:4px;">Upload max 10MB PDF document.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" id="cancelAddProgramModal">Cancel</button>
                <button type="submit" class="btn-primary" id="btnSaveProgram"><i data-lucide="upload" style="width:16px; height:16px;"></i> Upload Program</button>
            </div>
        </form>
    </div>
  </div>

  <!-- Edit Training Program Modal -->
  <div class="modal-overlay" id="editProgramModal">
    <div class="modal-content" style="max-width: 500px;">
        <div class="modal-header">
            <h3 style="font-size: 18px; font-weight: 600; color: var(--text-primary); margin: 0;">Edit Training Program</h3>
            <button class="close-modal" id="closeEditProgramModal">&times;</button>
        </div>
        <form id="editProgramForm" enctype="multipart/form-data">
            <div style="padding: 24px;">
                <input type="hidden" name="ModuleID" id="editModuleID">
                <div style="margin-bottom: 16px;">
                    <label style="display:block; font-size:14px; font-weight:500; color:var(--text-secondary); margin-bottom:8px;">Program Title</label>
                    <input type="text" name="ModuleName" id="editModuleName" required style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:8px; background:var(--background); color:var(--text-primary); outline:none; box-sizing:border-box;">
                </div>
                <div style="margin-bottom: 16px;">
                    <label style="display:block; font-size:14px; font-weight:500; color:var(--text-secondary); margin-bottom:8px;">Description</label>
                    <textarea name="Description" id="editModuleDesc" required rows="3" style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:8px; background:var(--background); color:var(--text-primary); outline:none; box-sizing:border-box; resize:none;"></textarea>
                </div>
                <div style="margin-bottom: 16px;">
                    <label style="display:block; font-size:14px; font-weight:500; color:var(--text-secondary); margin-bottom:8px;">PDF File</label>
                    <input type="file" name="training_file" accept=".pdf" style="width:100%; padding:8px; border:1px dashed var(--border-color); border-radius:8px; background:var(--surface); color:var(--text-primary); cursor:pointer; box-sizing:border-box;">
                    <small style="color:var(--text-secondary); display:block; margin-top:4px;">Upload a new PDF to replace the old one, or leave blank to keep the current file.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" id="cancelEditProgramModal">Cancel</button>
                <button type="submit" class="btn-primary" id="btnUpdateProgram"><i data-lucide="save" style="width:16px; height:16px;"></i> Update Program</button>
            </div>
        </form>
    </div>
  </div>

  <script src="../../js/trainingmgt.js?v=<?php echo time(); ?>"></script>
  <script>
    lucide.createIcons();
  </script>
</body>
</html>
