<?php

$accountId = $_SESSION['account_id'] ?? $_SESSION['AccountID'] ?? $_SESSION['user_id'] ?? null;
$page = 'admin_roster';
$module = 'admin_roster';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Shift & Schedule Publisher</title>

  <link rel="icon" type="image/png" href="../../img/logo.png">
  <link rel="stylesheet" href="../../css/admin_roster.css?v=<?php echo time(); ?>">

  <script src="https://cdn.jsdelivr.net/npm/lucide@latest"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<?php include 'sidebar.php'; ?>

<main class="main-content">
  <header class="page-header">
    <div class="header-left">
      <h1>Shift & Schedule Publisher</h1>

      <div class="page-top-meta">
        <span class="status-badge published-ready" id="headerRosterStatus">
          <i data-lucide="send" class="meta-icon"></i>
          Ready to Publish
        </span>

        <span class="mini-info">
          <i data-lucide="users-round" class="meta-icon"></i>
          Received Employees Only
        </span>

        <span class="mini-info">
          <i data-lucide="calendar-range" class="meta-icon"></i>
          2 full work weeks (Mon–Sat)
        </span>

        <span class="mini-info autosave-ready" id="autoSaveIndicator">
          <i data-lucide="save" class="meta-icon"></i>
          Auto-save ready
        </span>

        <span class="mini-info" id="selectedDepartmentBadge">
          <i data-lucide="building-2" class="meta-icon"></i>
          All Departments
        </span>

        <span class="mini-info" id="visibleEmployeeBadge">
          <i data-lucide="list-filter" class="meta-icon"></i>
          Visible Employees: --
        </span>
      </div>
    </div>

    <div class="header-right action-buttons">
      <button class="btn-secondary" type="button" onclick="location.href='published_rosters.php'">
        <i data-lucide="history"></i>
        <span>Published Schedules</span>
      </button>

      <!-- ADDED: Department dropdown filter -->
      <div class="search-box" style="min-width: 220px;">
        <i data-lucide="building-2"></i>
        <select id="departmentFilter" style="width:100%; border:none; outline:none; background:transparent;">
          <option value="all">All Departments</option>
        </select>
      </div>

      <div class="search-box">
        <i data-lucide="search"></i>
        <input id="searchInput" type="search" placeholder="Search received employee...">
      </div>

      <button class="btn-primary" id="publishRosterBtn" type="button">
        <i data-lucide="upload-cloud"></i>
        <span>Publish Schedule</span>
      </button>
    </div>
  </header>

  <section class="roster-layout">
    <div class="roster-stats">
      <div class="stat-card">
        <div class="stat-top">
          <span class="stat-label">Received Employees</span>
          <i data-lucide="inbox" class="stat-icon"></i>
        </div>
        <strong class="stat-value" id="statEmployees">--</strong>
        <p class="stat-subtext">Only employees marked as Received in dispatch records</p>
      </div>

      <div class="stat-card">
        <div class="stat-top">
          <span class="stat-label">Coverage Period</span>
          <i data-lucide="calendar-days" class="stat-icon"></i>
        </div>
        <strong class="stat-value" id="statCoverage">--</strong>
        <p class="stat-subtext">Fixed 2-week Monday–Saturday schedule</p>
      </div>

      <div class="stat-card">
        <div class="stat-top">
          <span class="stat-label">Unassigned Slots</span>
          <i data-lucide="alert-circle" class="stat-icon"></i>
        </div>
        <strong class="stat-value" id="statUnassigned">--</strong>
        <p class="stat-subtext">Editable cells still needing assignment</p>
      </div>

      <div class="stat-card">
        <div class="stat-top">
          <span class="stat-label">Roster Status</span>
          <i data-lucide="clipboard-check" class="stat-icon"></i>
        </div>
        <strong class="stat-value" id="statRosterStatus">Draft</strong>
        <p class="stat-subtext">Admin can publish immediately without approval flow</p>
      </div>
    </div>

    <aside class="shift-sidebar">
      <div class="shift-sidebar-head">
        <div>
          <h3>Available Shifts</h3>
          <p>Select one shift, then click editable cells to assign faster.</p>
        </div>

        <div class="sidebar-mini-note">
          <i data-lucide="shield-check"></i>
          <span>Leave and holiday cells are protected. OFF shift is not allowed.</span>
        </div>
      </div>

      <div id="shiftSelector" class="shift-selector">
        <div class="shift-loading">Loading shifts…</div>
      </div>
    </aside>

    <section class="content-card">
      <div class="card-header-block">
        <div class="card-header-top">
          <div>
            <h3 class="card-title">Received Employee Duty Assignments</h3>
            <p class="card-subtitle">
              Only employees from <strong>master_data_dispatches</strong> with status <strong>Received</strong> can be assigned.
              All departments may be viewed together, but saving and publishing remain separated per department.
            </p>
          </div>

          <div class="roster-controls">
            <button class="icon-btn" id="prevPeriod" type="button" title="Previous Period">
              <i data-lucide="chevron-left"></i>
            </button>

            <span class="week-range" id="periodLabel">Loading…</span>

            <button class="icon-btn" id="nextPeriod" type="button" title="Next Period">
              <i data-lucide="chevron-right"></i>
            </button>
          </div>
        </div>

        <div class="card-toolbar">
          <div class="toolbar-left">
            <button class="btn-secondary" id="btnFillAll" type="button">
              <i data-lucide="wand-2"></i>
              <span>Fill Editable Range</span>
            </button>

            <button class="btn-secondary" id="btnAiSuggest" type="button">
              <i data-lucide="sparkles"></i>
              <span>AI Apply &amp; Review</span>
            </button>

            <button class="btn-secondary" id="btnClearRange" type="button">
              <i data-lucide="eraser"></i>
              <span>Clear Editable Range</span>
            </button>
          </div>

          <div class="toolbar-legend">
            <span class="legend-item">
              <span class="legend-dot editable"></span>
              Editable
            </span>
            <span class="legend-item">
              <span class="legend-dot locked"></span>
              Locked
            </span>
            <span class="legend-item">
              <span class="legend-dot holiday"></span>
              Holiday
            </span>
            <span class="legend-item">
              <span class="legend-dot leave"></span>
              Leave
            </span>
            <span class="legend-item">
              <span class="legend-dot ai"></span>
              AI Suggested
            </span>
          </div>
        </div>
      </div>

      <div class="card-body">
        <div id="aiReviewPanel" class="ai-review-panel hidden">
          <div class="ai-review-head">
            <div>
              <h4>AI Post-Apply Review</h4>
              <p>This summary reflects the schedule after AI suggestions were applied.</p>
            </div>

            <div class="ai-review-actions">
              <button type="button" class="btn-secondary" id="btnDismissAiReview">
                <i data-lucide="x"></i>
                <span>Dismiss</span>
              </button>
            </div>
          </div>

          <div class="ai-review-grid">
            <div class="review-metric">
              <span class="review-label">Employees Included</span>
              <strong id="aiEmployeesIncluded">--</strong>
            </div>
            <div class="review-metric">
              <span class="review-label">Fairness Score</span>
              <strong id="aiFairnessScore">--</strong>
            </div>
            <div class="review-metric">
              <span class="review-label">Coverage Score</span>
              <strong id="aiCoverageScore">--</strong>
            </div>
            <div class="review-metric">
              <span class="review-label">Compliance Score</span>
              <strong id="aiComplianceScore">--</strong>
            </div>
            <div class="review-metric">
              <span class="review-label">Unassigned Remaining</span>
              <strong id="aiUnassignedRemaining">--</strong>
            </div>
          </div>

          <div class="ai-review-columns">
            <div class="review-list-card">
              <h5>Warnings</h5>
              <ul id="aiWarningsList" class="review-list">
                <li>No AI review data yet.</li>
              </ul>
            </div>

            <div class="review-list-card">
              <h5>Errors / Conflicts</h5>
              <ul id="aiErrorsList" class="review-list">
                <li>No AI review data yet.</li>
              </ul>
            </div>
          </div>
        </div>

        <div class="roster-table-wrapper">
          <table class="roster-table">
            <thead id="rosterHead"></thead>
            <tbody id="rosterBody">
              <tr>
                <td style="text-align:center; padding:30px;">Loading received employees schedules…</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="helper-note">
          <i data-lucide="info"></i>
          <span>
            This admin scheduler only includes employees with dispatch status <strong>Received</strong>. Sundays are skipped. Approved leave dates appear as LEAVE and cannot be scheduled. Admin publishing is direct and does not require HR approval. Department filter only changes the visible list on screen; saving and publishing still stay separated per department automatically.
          </span>
        </div>
      </div>
    </section>
  </section>
</main>

<script>
  window.__ADMIN_ROSTER_CTX__ = {
    accountId: <?php echo $accountId ? (int)$accountId : 'null'; ?>,
    mode: "admin_publish",
    scope: "received_dispatch_only",
    rules: {
      fixedPeriodDays: 12,
      workDays: ["MON", "TUE", "WED", "THU", "FRI", "SAT"],
      skipSunday: true,
      leaveLocked: true,
      holidayLocked: true,
      requiresApproval: false,
      publishDirectly: true,
      allowOffShift: false
    }
  };

  document.addEventListener("DOMContentLoaded", function () {
    if (window.lucide) lucide.createIcons();
  });
</script>

<script src="../../js/admin_roster.js?v=<?php echo time(); ?>"></script>
</body>
</html>