<?php
require_once __DIR__ . '/../../../config/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set('Asia/Manila');

function respond(bool $ok, array $data = [], int $code = 200): void
{
    http_response_code($code);
    echo json_encode(array_merge(['success' => $ok], $data), JSON_UNESCAPED_UNICODE);
    exit;
}

if (!isset($conn) || !($conn instanceof mysqli)) {
    respond(false, ['message' => 'Database connection not found.'], 500);
}

$conn->set_charset('utf8mb4');

$accountId = (int)($_SESSION['account_id'] ?? $_SESSION['AccountID'] ?? $_SESSION['user_id'] ?? 0);
if ($accountId <= 0) {
    respond(false, ['message' => 'Unauthorized. Missing account session.'], 401);
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$action = $_GET['action'] ?? $_POST['action'] ?? '';

function esc(mysqli $conn, string $value): string
{
    return mysqli_real_escape_string($conn, $value);
}

function fmtPeriodLabel(?string $start, ?string $end): string
{
    if (!$start || !$end) {
        return 'Unknown Period';
    }

    $startTs = strtotime($start);
    $endTs = strtotime($end);

    if (!$startTs || !$endTs) {
        return $start . ' - ' . $end;
    }

    return date('M d, Y', $startTs) . ' - ' . date('M d, Y', $endTs);
}

function statusClass(string $status): string
{
    $status = strtoupper(trim($status));

    return match ($status) {
        'FINALIZED' => 'approved',
        'APPROVED' => 'approved',
        'FOR_REVIEW' => 'pending',
        'RETURNED' => 'danger',
        'DRAFT' => 'draft',
        default => 'draft',
    };
}

function normalizeEmploymentStatus(?string $status): string
{
    return strtoupper(trim((string)$status));
}

function isActiveEmployment(?string $status): bool
{
    $status = normalizeEmploymentStatus($status);

    if ($status === '') {
        return true;
    }

    $inactive = [
        'RESIGNED',
        'TERMINATED',
        'INACTIVE',
        'SEPARATED',
        'DISMISSED',
        'AWOL'
    ];

    return !in_array($status, $inactive, true);
}

function computeSummaryForEmployee(mysqli $conn, int $periodId, int $employeeId, int $departmentId, int $positionId): array
{
    $sql = "
        SELECT
            COALESCE(SUM(td.RegularMinutes), 0) AS regular_minutes,
            COALESCE(SUM(td.OvertimeMinutes), 0) AS overtime_minutes,
            COALESCE(SUM(td.NightDiffMinutes), 0) AS nightdiff_minutes,
            COALESCE(SUM(td.LateMinutes), 0) AS late_minutes,
            COALESCE(SUM(td.UndertimeMinutes), 0) AS undertime_minutes,
            COALESCE(SUM(CASE WHEN td.DayStatus = 'ABSENT' THEN 8 ELSE 0 END), 0) AS absence_hours,
            COALESCE(SUM(CASE WHEN td.DayStatus = 'LEAVE_PAID' THEN 8 ELSE 0 END), 0) AS paid_leave_hours,
            COALESCE(SUM(CASE WHEN td.DayStatus = 'LEAVE_UNPAID' THEN 8 ELSE 0 END), 0) AS unpaid_leave_hours,
            COALESCE(SUM(CASE WHEN td.DayStatus IN ('INCOMPLETE','FLAGGED','NO_SCHEDULE') THEN 1 ELSE 0 END), 0) AS issue_count,
            COUNT(*) AS day_count
        FROM timesheet_daily td
        WHERE td.PeriodID = ? AND td.EmployeeID = ?
    ";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Failed to prepare timesheet summary query.');
    }

    $stmt->bind_param('ii', $periodId, $employeeId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result ? $result->fetch_assoc() : null;
    $stmt->close();

    $regularHours = round(((int)($row['regular_minutes'] ?? 0)) / 60, 2);
    $overtimeHours = round(((int)($row['overtime_minutes'] ?? 0)) / 60, 2);
    $nightDiffHours = round(((int)($row['nightdiff_minutes'] ?? 0)) / 60, 2);
    $lateMinutes = (int)($row['late_minutes'] ?? 0);
    $undertimeMinutes = (int)($row['undertime_minutes'] ?? 0);
    $absenceHours = round((float)($row['absence_hours'] ?? 0), 2);
    $paidLeaveHours = round((float)($row['paid_leave_hours'] ?? 0), 2);
    $unpaidLeaveHours = round((float)($row['unpaid_leave_hours'] ?? 0), 2);
    $issueCount = (int)($row['issue_count'] ?? 0);
    $dayCount = (int)($row['day_count'] ?? 0);

    $totalPayableHours = round(
        $regularHours
        + $overtimeHours
        + $nightDiffHours
        + $paidLeaveHours,
        2
    );

    $notes = 'Auto-recomputed from timesheet_daily.';
    if ($dayCount === 0) {
        $notes = 'No daily timesheet data found for this employee in the selected period.';
    } elseif ($issueCount > 0) {
        $notes = 'Auto-recomputed with flagged/incomplete day entries detected.';
    }

    return [
        'PeriodID' => $periodId,
        'EmployeeID' => $employeeId,
        'DepartmentID' => $departmentId,
        'PositionID' => $positionId,
        'IsEligibleForHolidayPay' => 1,
        'RegularHours' => $regularHours,
        'OvertimeHours' => $overtimeHours,
        'NightDiffHours' => $nightDiffHours,
        'RegHolidayHours' => 0,
        'SpecHolidayHours' => 0,
        'UnworkedHolidayHours' => 0,
        'HolidayOvertimeHours' => 0,
        'LateMinutes' => $lateMinutes,
        'UndertimeMinutes' => $undertimeMinutes,
        'AbsencesHours' => $absenceHours,
        'PaidLeaveHours' => $paidLeaveHours,
        'UnpaidLeaveHours' => $unpaidLeaveHours,
        'TotalPayableHours' => $totalPayableHours,
        'Notes' => $notes,
        'IssueCount' => $issueCount,
        'DayCount' => $dayCount,
    ];
}

function recomputeDepartmentPeriod(mysqli $conn, int $departmentId, int $periodId): array
{
    $employeesSql = "
        SELECT
            e.EmployeeID,
            ei.DepartmentID,
            ei.PositionID,
            ei.EmploymentStatus
        FROM employmentinformation ei
        INNER JOIN employee e ON e.EmployeeID = ei.EmployeeID
        WHERE ei.DepartmentID = ?
        ORDER BY e.LastName, e.FirstName
    ";

    $stmtEmployees = $conn->prepare($employeesSql);
    if (!$stmtEmployees) {
        throw new Exception('Failed to prepare employee list query.');
    }

    $stmtEmployees->bind_param('i', $departmentId);
    $stmtEmployees->execute();
    $employeesResult = $stmtEmployees->get_result();

    $employees = [];
    while ($row = $employeesResult->fetch_assoc()) {
        if (!isActiveEmployment($row['EmploymentStatus'] ?? null)) {
            continue;
        }
        $employees[] = $row;
    }
    $stmtEmployees->close();

    $conn->begin_transaction();

    try {
        $deleteSql = "DELETE FROM timesheet_employee_summary WHERE PeriodID = ? AND DepartmentID = ?";
        $stmtDelete = $conn->prepare($deleteSql);
        if (!$stmtDelete) {
            throw new Exception('Failed to prepare summary delete query.');
        }
        $stmtDelete->bind_param('ii', $periodId, $departmentId);
        $stmtDelete->execute();
        $stmtDelete->close();

        $insertSql = "
            INSERT INTO timesheet_employee_summary (
                PeriodID,
                EmployeeID,
                DepartmentID,
                PositionID,
                IsEligibleForHolidayPay,
                RegularHours,
                OvertimeHours,
                NightDiffHours,
                RegHolidayHours,
                SpecHolidayHours,
                UnworkedHolidayHours,
                HolidayOvertimeHours,
                LateMinutes,
                UndertimeMinutes,
                AbsencesHours,
                PaidLeaveHours,
                UnpaidLeaveHours,
                TotalPayableHours,
                Notes
            ) VALUES (
                ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?, ?, ?
            )
        ";

        $stmtInsert = $conn->prepare($insertSql);
        if (!$stmtInsert) {
            throw new Exception('Failed to prepare summary insert query.');
        }

        $inserted = 0;

        foreach ($employees as $emp) {
            $summary = computeSummaryForEmployee(
                $conn,
                $periodId,
                (int)$emp['EmployeeID'],
                (int)$emp['DepartmentID'],
                (int)$emp['PositionID']
            );

            $stmtInsert->bind_param(
                'iiiiidddddddiidddds',
                $summary['PeriodID'],
                $summary['EmployeeID'],
                $summary['DepartmentID'],
                $summary['PositionID'],
                $summary['IsEligibleForHolidayPay'],
                $summary['RegularHours'],
                $summary['OvertimeHours'],
                $summary['NightDiffHours'],
                $summary['RegHolidayHours'],
                $summary['SpecHolidayHours'],
                $summary['UnworkedHolidayHours'],
                $summary['HolidayOvertimeHours'],
                $summary['LateMinutes'],
                $summary['UndertimeMinutes'],
                $summary['AbsencesHours'],
                $summary['PaidLeaveHours'],
                $summary['UnpaidLeaveHours'],
                $summary['TotalPayableHours'],
                $summary['Notes']
            );
            $stmtInsert->execute();
            $inserted++;
        }

        $stmtInsert->close();
        $conn->commit();

        return [
            'employees_processed' => count($employees),
            'summaries_inserted' => $inserted,
        ];
    } catch (Throwable $e) {
        $conn->rollback();
        throw $e;
    }
}

try {
    if ($action === 'get_departments') {
        $sql = "
            SELECT
                d.DepartmentID AS department_id,
                d.DepartmentName AS department_name
            FROM department d
            ORDER BY d.DepartmentName ASC
        ";

        $result = $conn->query($sql);
        if (!$result) {
            throw new Exception('Failed to load departments.');
        }

        $departments = [];
        while ($row = $result->fetch_assoc()) {
            $departments[] = $row;
        }

        respond(true, ['departments' => $departments]);
    }

    if ($action === 'get_periods') {
        $departmentId = (int)($_GET['department_id'] ?? 0);
        if ($departmentId <= 0) {
            respond(false, ['message' => 'Invalid department.'], 422);
        }

        $sql = "
            SELECT
                tp.PeriodID,
                tp.StartDate,
                tp.EndDate,
                tp.Status
            FROM timesheet_period tp
            WHERE tp.DepartmentID = ? AND tp.IsArchived = 0
            ORDER BY tp.StartDate DESC, tp.PeriodID DESC
        ";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception('Failed to prepare periods query.');
        }

        $stmt->bind_param('i', $departmentId);
        $stmt->execute();
        $result = $stmt->get_result();

        $periods = [];
        while ($row = $result->fetch_assoc()) {
            $periods[] = [
                'period_id' => (int)$row['PeriodID'],
                'start_date' => $row['StartDate'],
                'end_date' => $row['EndDate'],
                'status' => $row['Status'],
                'label' => fmtPeriodLabel($row['StartDate'], $row['EndDate']),
            ];
        }

        $stmt->close();
        respond(true, ['periods' => $periods]);
    }

    if ($action === 'get_period_data') {
        $departmentId = (int)($_GET['department_id'] ?? 0);
        $periodId = (int)($_GET['period_id'] ?? 0);

        if ($departmentId <= 0 || $periodId <= 0) {
            respond(false, ['message' => 'Invalid department or period.'], 422);
        }

        $periodSql = "
            SELECT
                tp.PeriodID,
                tp.DepartmentID,
                tp.StartDate,
                tp.EndDate,
                tp.Status,
                d.DepartmentName
            FROM timesheet_period tp
            INNER JOIN department d ON d.DepartmentID = tp.DepartmentID
            WHERE tp.PeriodID = ? AND tp.DepartmentID = ? AND tp.IsArchived = 0
            LIMIT 1
        ";

        $stmtPeriod = $conn->prepare($periodSql);
        if (!$stmtPeriod) {
            throw new Exception('Failed to prepare period query.');
        }

        $stmtPeriod->bind_param('ii', $periodId, $departmentId);
        $stmtPeriod->execute();
        $periodResult = $stmtPeriod->get_result();
        $period = $periodResult ? $periodResult->fetch_assoc() : null;
        $stmtPeriod->close();

        if (!$period) {
            respond(false, ['message' => 'Timesheet period not found.'], 404);
        }

        $rowsSql = "
            SELECT
                tes.SummaryID,
                tes.EmployeeID,
                tes.RegularHours,
                tes.OvertimeHours,
                tes.NightDiffHours,
                tes.LateMinutes,
                tes.UndertimeMinutes,
                tes.AbsencesHours,
                tes.PaidLeaveHours,
                tes.UnpaidLeaveHours,
                tes.TotalPayableHours,
                tes.Notes,
                e.EmployeeCode,
                e.FirstName,
                e.MiddleName,
                e.LastName,
                p.PositionName,
                COALESCE(issue_data.issue_count, 0) AS issue_count,
                COALESCE(issue_data.has_data, 0) AS has_data
            FROM timesheet_employee_summary tes
            INNER JOIN employee e ON e.EmployeeID = tes.EmployeeID
            LEFT JOIN positions p ON p.PositionID = tes.PositionID
            LEFT JOIN (
                SELECT
                    td.EmployeeID,
                    COUNT(*) AS has_data,
                    SUM(
                        CASE
                            WHEN td.DayStatus IN ('INCOMPLETE', 'FLAGGED', 'NO_SCHEDULE')
                            THEN 1 ELSE 0
                        END
                    ) AS issue_count
                FROM timesheet_daily td
                WHERE td.PeriodID = ?
                GROUP BY td.EmployeeID
            ) AS issue_data ON issue_data.EmployeeID = tes.EmployeeID
            WHERE tes.PeriodID = ? AND tes.DepartmentID = ?
            ORDER BY e.LastName ASC, e.FirstName ASC
        ";

        $stmtRows = $conn->prepare($rowsSql);
        if (!$stmtRows) {
            throw new Exception('Failed to prepare rows query.');
        }

        $stmtRows->bind_param('iii', $periodId, $periodId, $departmentId);
        $stmtRows->execute();
        $rowsResult = $stmtRows->get_result();

        $rows = [];
        $totalOtHours = 0;
        $totalLateMinutes = 0;

        while ($row = $rowsResult->fetch_assoc()) {
            $fullName = trim(
                ($row['LastName'] ?? '') . ', ' .
                ($row['FirstName'] ?? '') .
                (($row['MiddleName'] ?? '') !== '' ? ' ' . $row['MiddleName'] : '')
            );

            $issueCount = (int)$row['issue_count'];
            $hasData = (int)$row['has_data'];

            $status = 'Ready';
            if ($hasData === 0) {
                $status = 'No Data';
            } elseif ($issueCount > 0) {
                $status = 'Review';
            }

            $reg = (float)$row['RegularHours'];
            $ot = (float)$row['OvertimeHours'];
            $late = (int)$row['LateMinutes'];
            $abs = (float)$row['AbsencesHours'];
            $paidLeave = (float)$row['PaidLeaveHours'];
            $unpaidLeave = (float)$row['UnpaidLeaveHours'];
            $undertimeMins = (int)$row['UndertimeMinutes'];
            $deduction = round($unpaidLeave + $abs + ($late / 60) + ($undertimeMins / 60), 2);

            $rows[] = [
                'employee_id' => (int)$row['EmployeeID'],
                'name' => $fullName,
                'code' => $row['EmployeeCode'] ?? '',
                'position' => $row['PositionName'] ?? 'Unknown Position',
                'reg' => $reg,
                'ot' => $ot,
                'late' => $late,
                'abs' => $abs,
                'leave_credits' => 0,
                'paid_leave' => $paidLeave,
                'excused' => round($undertimeMins / 60, 2),
                'deduction' => $deduction,
                'final' => (float)$row['TotalPayableHours'],
                'status' => $status,
                'notes' => $row['Notes'] ?? '',
            ];

            $totalOtHours += $ot;
            $totalLateMinutes += $late;
        }

        $stmtRows->close();

        $issuesSql = "
            SELECT
                td.WorkDate,
                td.DayStatus,
                td.Remarks,
                e.FirstName,
                e.MiddleName,
                e.LastName
            FROM timesheet_daily td
            INNER JOIN employee e ON e.EmployeeID = td.EmployeeID
            INNER JOIN employmentinformation ei ON ei.EmployeeID = td.EmployeeID
            WHERE td.PeriodID = ?
              AND ei.DepartmentID = ?
              AND td.DayStatus IN ('INCOMPLETE', 'FLAGGED', 'NO_SCHEDULE', 'ABSENT', 'LEAVE_UNPAID')
            ORDER BY td.WorkDate ASC, e.LastName ASC, e.FirstName ASC
        ";

        $stmtIssues = $conn->prepare($issuesSql);
        if (!$stmtIssues) {
            throw new Exception('Failed to prepare issues query.');
        }

        $stmtIssues->bind_param('ii', $periodId, $departmentId);
        $stmtIssues->execute();
        $issuesResult = $stmtIssues->get_result();

        $issues = [];
        while ($issue = $issuesResult->fetch_assoc()) {
            $employeeName = trim(
                ($issue['LastName'] ?? '') . ', ' .
                ($issue['FirstName'] ?? '') .
                (($issue['MiddleName'] ?? '') !== '' ? ' ' . $issue['MiddleName'] : '')
            );

            $message = match ($issue['DayStatus']) {
                'INCOMPLETE' => 'Incomplete day log detected.',
                'FLAGGED' => 'Flagged timesheet entry detected.',
                'NO_SCHEDULE' => 'No schedule matched for this work date.',
                'ABSENT' => 'Employee marked absent.',
                'LEAVE_UNPAID' => 'Unpaid leave recorded.',
                default => 'Issue detected.'
            };

            if (!empty($issue['Remarks'])) {
                $message .= ' ' . trim((string)$issue['Remarks']);
            }

            $issues[] = [
                'employee_name' => $employeeName,
                'work_date' => $issue['WorkDate'],
                'message' => $message,
            ];
        }
        $stmtIssues->close();

        $aiItems = [];
        if (count($issues) > 0) {
            $aiItems[] = 'Review all flagged and incomplete daily entries before finalizing this period.';
        }
        if ($totalLateMinutes > 0) {
            $aiItems[] = 'Late minutes were detected in this period and may affect payroll review.';
        }
        if (count($rows) === 0) {
            $aiItems[] = 'No employee summaries are available yet for this selected period.';
        }

        $aiReview = [
            'summary' => count($issues) > 0
                ? 'System review found entries that may need manual verification before publishing.'
                : 'No major blocking issues detected. Period appears ready for final review.',
            'items' => $aiItems,
        ];

        respond(true, [
            'period' => [
                'period_id' => (int)$period['PeriodID'],
                'department_id' => (int)$period['DepartmentID'],
                'department_name' => $period['DepartmentName'],
                'label' => fmtPeriodLabel($period['StartDate'], $period['EndDate']),
                'status' => $period['Status'],
                'status_class' => statusClass($period['Status']),
                'start_date' => $period['StartDate'],
                'end_date' => $period['EndDate'],
            ],
            'stats' => [
                'employees' => count($rows),
                'ot_hours' => round($totalOtHours, 2),
                'late_minutes' => $totalLateMinutes,
                'issues' => count($issues),
            ],
            'rows' => $rows,
            'issues' => $issues,
            'ai_review' => $aiReview,
        ]);
    }

    if ($action === 'employee_logs') {
        $departmentId = (int)($_GET['department_id'] ?? 0);
        $periodId = (int)($_GET['period_id'] ?? 0);
        $employeeId = (int)($_GET['employee_id'] ?? 0);

        if ($departmentId <= 0 || $periodId <= 0 || $employeeId <= 0) {
            respond(false, ['message' => 'Invalid request parameters.'], 422);
        }

        $sql = "
            SELECT
                td.WorkDate,
                td.ShiftCode,
                td.ScheduledStart,
                td.ScheduledEnd,
                td.ActualTimeIn,
                td.ActualTimeOut,
                td.DayStatus,
                td.Remarks
            FROM timesheet_daily td
            INNER JOIN employmentinformation ei ON ei.EmployeeID = td.EmployeeID
            WHERE td.PeriodID = ?
              AND td.EmployeeID = ?
              AND ei.DepartmentID = ?
            ORDER BY td.WorkDate ASC
        ";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception('Failed to prepare employee logs query.');
        }

        $stmt->bind_param('iii', $periodId, $employeeId, $departmentId);
        $stmt->execute();
        $result = $stmt->get_result();

        $logs = [];
        while ($row = $result->fetch_assoc()) {
            $logs[] = $row;
        }

        $stmt->close();
        respond(true, ['logs' => $logs]);
    }

    if ($action === 'recompute_all') {
        if ($method !== 'POST') {
            respond(false, ['message' => 'Invalid request method.'], 405);
        }

        $departmentId = (int)($_POST['department_id'] ?? 0);
        $periodId = (int)($_POST['period_id'] ?? 0);

        if ($departmentId <= 0 || $periodId <= 0) {
            respond(false, ['message' => 'Invalid department or period.'], 422);
        }

        $checkSql = "
            SELECT PeriodID, Status
            FROM timesheet_period
            WHERE PeriodID = ? AND DepartmentID = ? AND IsArchived = 0
            LIMIT 1
        ";

        $stmtCheck = $conn->prepare($checkSql);
        if (!$stmtCheck) {
            throw new Exception('Failed to prepare period validation query.');
        }

        $stmtCheck->bind_param('ii', $periodId, $departmentId);
        $stmtCheck->execute();
        $checkResult = $stmtCheck->get_result();
        $period = $checkResult ? $checkResult->fetch_assoc() : null;
        $stmtCheck->close();

        if (!$period) {
            respond(false, ['message' => 'Selected timesheet period not found.'], 404);
        }

        if (strtoupper((string)$period['Status']) === 'FINALIZED') {
            respond(false, ['message' => 'Cannot recompute a finalized timesheet period.'], 422);
        }

        $summary = recomputeDepartmentPeriod($conn, $departmentId, $periodId);

        respond(true, [
            'message' => 'Timesheet summaries recomputed successfully.',
            'meta' => $summary,
        ]);
    }

    if ($action === 'publish_now') {
        if ($method !== 'POST') {
            respond(false, ['message' => 'Invalid request method.'], 405);
        }

        $departmentId = (int)($_POST['department_id'] ?? 0);
        $periodId = (int)($_POST['period_id'] ?? 0);

        if ($departmentId <= 0 || $periodId <= 0) {
            respond(false, ['message' => 'Invalid department or period.'], 422);
        }

        $sql = "
            UPDATE timesheet_period
            SET
                Status = 'FINALIZED',
                FinalizedByAccountID = ?,
                FinalizedAt = NOW(),
                UpdatedAt = CURRENT_TIMESTAMP
            WHERE PeriodID = ? AND DepartmentID = ? AND IsArchived = 0
            LIMIT 1
        ";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception('Failed to prepare publish query.');
        }

        $stmt->bind_param('iii', $accountId, $periodId, $departmentId);
        $stmt->execute();

        if ($stmt->affected_rows <= 0) {
            $stmt->close();
            respond(false, ['message' => 'No timesheet period was updated.'], 404);
        }

        $stmt->close();

        respond(true, ['message' => 'Timesheet period finalized successfully.']);
    }

    respond(false, ['message' => 'Invalid action.'], 400);
} catch (Throwable $e) {
    respond(false, [
        'message' => $e->getMessage(),
    ], 500);
}