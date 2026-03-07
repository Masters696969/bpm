<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../../login.php");
    exit();
}
require_once __DIR__ . '/../../config/config.php';

// Fetch employees for dropdown
$employees = [];
$empRes = $conn->query("SELECT e.EmployeeID, e.EmployeeCode, e.FirstName, e.LastName FROM employee e ORDER BY e.LastName, e.FirstName");
if ($empRes) {
    while ($r = $empRes->fetch_assoc()) $employees[] = $r;
}

// Fetch latest summaries
$summaries = [];
$sumSql = "SELECT t.*,
                  e.EmployeeCode, e.FirstName, e.LastName
           FROM timesheet_employee_summary t
           INNER JOIN employee e ON e.EmployeeID = t.EmployeeID
           INNER JOIN (
                SELECT EmployeeID, MAX(UpdatedAt) AS MaxUpdated
                FROM timesheet_employee_summary
                GROUP BY EmployeeID
           ) latest ON latest.EmployeeID = t.EmployeeID AND latest.MaxUpdated = t.UpdatedAt
           ORDER BY e.LastName, e.FirstName";
$sumRes = $conn->query($sumSql);
if ($sumRes) {
    while ($r = $sumRes->fetch_assoc()) $summaries[] = $r;
}

function h($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Timesheet Summary</title>
  <link rel="stylesheet" href="../../css/payroll.css?v=1.2">
  <script src="https://unpkg.com/lucide@latest"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="icon" type="image/png" href="../../img/logo.png">
  <style>
    .ts-wrap { padding: 32px; }
    .ts-card { background: var(--surface); border: 1px solid var(--border-color); border-radius: 20px; box-shadow: var(--shadow); overflow:hidden; }
    .ts-card-header { padding: 20px 24px; border-bottom: 1px solid var(--border-color); display:flex; justify-content:space-between; align-items:center; }
    .ts-card-header h2 { font-size: 18px; font-weight: 700; }
    .ts-form { padding: 20px 24px; display:grid; grid-template-columns: repeat(4, 1fr); gap: 14px; }
    .ts-field { display:flex; flex-direction:column; gap:6px; }
    .ts-field label { font-size: 11px; color: var(--text-tertiary); text-transform: uppercase; letter-spacing: .06em; font-weight: 700; }
    .ts-field input, .ts-field select { padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 12px; background: var(--background); color: var(--text-primary); outline:none; }
    .ts-actions { grid-column: 1 / -1; display:flex; justify-content:flex-end; gap:10px; }
    .ts-table-wrap { overflow-x:auto; }
    .hint { font-size: 12px; color: var(--text-secondary); padding: 0 24px 18px; }
  </style>
</head>
<body>
  <main class="main-content" style="margin-left:0">
    <div class="ts-wrap">
      <div class="ts-card">
        <div class="ts-card-header">
          <div>
            <h2>Timesheet Employee Summary</h2>
            <div class="hint">This page inserts a new summary row (audit-friendly). Payroll uses the latest UpdatedAt per employee.</div>
          </div>
          <a class="btn-premium" href="payroll.php" style="background: var(--surface-hover); border: 1px solid var(--border-color); text-decoration:none;">Back to Payroll</a>
        </div>

        <form class="ts-form" id="timesheetForm">
          <div class="ts-field">
            <label>Employee</label>
            <select name="EmployeeID" required>
              <option value="">Select employee</option>
              <?php foreach ($employees as $e): ?>
                <option value="<?= (int)$e['EmployeeID'] ?>"><?= h($e['LastName'] . ', ' . $e['FirstName'] . ' (' . $e['EmployeeCode'] . ')') ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="ts-field">
            <label>PeriodID</label>
            <input type="number" name="PeriodID" value="1" min="1" required />
          </div>

          <div class="ts-field">
            <label>DepartmentID</label>
            <input type="number" name="DepartmentID" value="1" min="1" required />
          </div>

          <div class="ts-field">
            <label>PositionID</label>
            <input type="number" name="PositionID" value="1" min="1" required />
          </div>

          <div class="ts-field">
            <label>RegularHours</label>
            <input type="number" step="0.01" name="RegularHours" value="160" min="0" required />
          </div>

          <div class="ts-field">
            <label>OvertimeHours</label>
            <input type="number" step="0.01" name="OvertimeHours" value="0" min="0" required />
          </div>

          <div class="ts-field">
            <label>LateMinutes</label>
            <input type="number" name="LateMinutes" value="0" min="0" required />
          </div>

          <div class="ts-field">
            <label>UndertimeMinutes</label>
            <input type="number" name="UndertimeMinutes" value="0" min="0" required />
          </div>

          <div class="ts-field">
            <label>TotalPayableHours</label>
            <input type="number" step="0.01" name="TotalPayableHours" value="160" min="0" required />
          </div>

          <div class="ts-field" style="grid-column: span 3;">
            <label>Notes</label>
            <input type="text" name="Notes" placeholder="Optional" />
          </div>

          <div class="ts-actions">
            <button class="btn-premium btn-primary-premium" type="submit">
              Save Summary
            </button>
          </div>
        </form>

        <div class="ts-table-wrap">
          <table class="payroll-table">
            <thead>
              <tr>
                <th>Employee</th>
                <th>PeriodID</th>
                <th>Regular</th>
                <th>OT</th>
                <th>Late (min)</th>
                <th>UT (min)</th>
                <th>Total Payable</th>
                <th>Updated</th>
              </tr>
            </thead>
            <tbody>
              <?php if (count($summaries) === 0): ?>
                <tr><td colspan="8" style="padding:16px 24px; color: var(--text-secondary);">No timesheet summaries yet.</td></tr>
              <?php endif; ?>
              <?php foreach ($summaries as $s): ?>
                <tr>
                  <td><strong><?= h($s['LastName'] . ', ' . $s['FirstName']) ?></strong><div style="font-size:12px;color:var(--text-secondary);"><?= h($s['EmployeeCode']) ?></div></td>
                  <td><?= h($s['PeriodID']) ?></td>
                  <td><?= h($s['RegularHours']) ?></td>
                  <td><?= h($s['OvertimeHours']) ?></td>
                  <td><?= h($s['LateMinutes']) ?></td>
                  <td><?= h($s['UndertimeMinutes']) ?></td>
                  <td><?= h($s['TotalPayableHours']) ?></td>
                  <td><?= h($s['UpdatedAt']) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

      </div>
    </div>
  </main>

  <script>
    lucide.createIcons();

    const form = document.getElementById('timesheetForm');
    if (form) {
      form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const data = new URLSearchParams(new FormData(form));
        data.set('action', 'save_timesheet');

        try {
          const res = await fetch('payroll_action.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: data.toString()
          });
          const json = await res.json().catch(() => ({}));
          if (!res.ok || !json.ok) {
            throw new Error(json.error || 'Failed to save');
          }

          await Swal.fire({
            icon: 'success',
            title: 'Saved',
            text: 'Timesheet summary saved. Reloading…',
            timer: 1200,
            showConfirmButton: false
          });
          window.location.reload();

        } catch (err) {
          Swal.fire({ icon: 'error', title: 'Failed', text: err.message || 'Error' });
        }
      });
    }
  </script>
</body>
</html>
