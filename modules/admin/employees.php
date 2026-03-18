<?php
require_once '../../config/config.php';

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

$page = 'employees';
$module = 'employees';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR3 | Receive Employees</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="stylesheet" href="../../css/employees.css">
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <main class="main">
        <div class="header">
            <div>
                <div class="page-kicker">HR3 • EMPLOYEES</div>
                <h1 class="page-title">Receive Employees</h1>
                <p class="page-subtitle">
                    Receive dispatched employees first before they become available in your shift and scheduling records.
                </p>
            </div>

            <div class="header-actions">
                <div class="clock-box" id="realTimeClock">--</div>
            </div>
        </div>

        <section class="hero-card">
            <div class="hero-left">
                <div class="hero-icon">
                    <i data-lucide="inbox"></i>
                </div>
                <div>
                    <h2>Employee Receiving Queue</h2>
                    <p>Employees listed here are waiting to be received into HR3 from the dispatching module.</p>
                </div>
            </div>

            <div class="hero-actions">
                <button type="button" class="btn btn-outline" id="refreshBtn">
                    <i data-lucide="refresh-cw"></i>
                    Refresh
                </button>
                <button type="button" class="btn btn-primary" id="receiveSelectedBtn">
                    <i data-lucide="check-check"></i>
                    Receive Selected
                </button>
            </div>
        </section>

        <section class="stats-grid">
            <div class="card stat-card">
                <div class="stat-icon green">
                    <i data-lucide="users"></i>
                </div>
                <div>
                    <div class="stat-value" id="statTotal">0</div>
                    <div class="stat-label">Total Pending</div>
                </div>
            </div>

            <div class="card stat-card">
                <div class="stat-icon blue">
                    <i data-lucide="user-check"></i>
                </div>
                <div>
                    <div class="stat-value" id="statReady">0</div>
                    <div class="stat-label">Ready to Receive</div>
                </div>
            </div>

            <div class="card stat-card">
                <div class="stat-icon purple">
                    <i data-lucide="building-2"></i>
                </div>
                <div>
                    <div class="stat-value" id="statDepartments">0</div>
                    <div class="stat-label">Departments</div>
                </div>
            </div>
        </section>

        <section class="card">
            <div class="card-title">Receiving List</div>

            <div class="toolbar">
                <div class="search-box">
                    <i data-lucide="search"></i>
                    <input type="text" id="searchInput" placeholder="Search employee, code, department, position...">
                </div>

                <div class="filter-box">
                    <i data-lucide="building-2"></i>
                    <select id="departmentFilter">
                        <option value="">All Departments</option>
                    </select>
                </div>

                <div class="toolbar-note">
                    Only employees with <strong>Pending</strong> dispatch status can be received here.
                </div>
            </div>

            <div class="table-wrap">
                <table class="roster-table" id="receiveTable">
                    <thead>
                        <tr>
                            <th style="width: 52px; text-align: center;">
                                <input type="checkbox" id="selectAllEmployees">
                            </th>
                            <th class="emp-column">Employee</th>
                            <th>Position</th>
                            <th>Department</th>
                            <th>Employment Status</th>
                            <th>Dispatch Status</th>
                            <th style="min-width: 180px;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="receiveTableBody">
                        <tr>
                            <td colspan="7" class="empty-state-cell">Loading employees...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <script src="../../js/employees.js"></script>
    
</body>
</html>