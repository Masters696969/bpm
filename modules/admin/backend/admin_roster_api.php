<?php
require_once __DIR__ . '/../../../config/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

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

$accountId = $_SESSION['account_id'] ?? $_SESSION['AccountID'] ?? $_SESSION['user_id'] ?? null;
if (!$accountId) {
    respond(false, ['message' => 'Unauthorized.'], 401);
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$action = $_GET['action'] ?? $_POST['action'] ?? '';

function getJsonBody(): array
{
    $raw = file_get_contents('php://input');
    if (!$raw) {
        return [];
    }

    $decoded = json_decode($raw, true);
    return is_array($decoded) ? $decoded : [];
}

function normalizeDate(?string $date): ?string
{
    $date = trim((string) $date);
    if ($date === '') {
        return null;
    }

    $dt = DateTime::createFromFormat('Y-m-d', $date);
    return ($dt && $dt->format('Y-m-d') === $date) ? $date : null;
}

function getMondayStart(?string $date = null): string
{
    $dt = $date ? new DateTime($date) : new DateTime('today');
    $dt->modify('monday this week');
    return $dt->format('Y-m-d');
}

function getRosterDates(string $weekStart): array
{
    $dates = [];
    $dt = new DateTime($weekStart);

    while (count($dates) < 12) {
        if ((int) $dt->format('N') !== 7) {
            $dates[] = $dt->format('Y-m-d');
        }
        $dt->modify('+1 day');
    }

    return $dates;
}

function getRosterEndFromStart(string $weekStart): string
{
    $dates = getRosterDates($weekStart);
    return end($dates);
}

function getActiveShifts(mysqli $conn): array
{
    $sql = "
        SELECT ShiftCode, ShiftName, StartTime, EndTime, BreakMinutes, GraceMinutes
        FROM shift_type
        WHERE IsActive = 1
        ORDER BY ShiftName ASC, ShiftCode ASC
    ";
    $result = $conn->query($sql);

    $rows = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $rows[] = [
                'ShiftCode' => $row['ShiftCode'],
                'ShiftName' => $row['ShiftName'],
                'StartTime' => $row['StartTime'],
                'EndTime' => $row['EndTime'],
                'BreakMinutes' => (int) $row['BreakMinutes'],
                'GraceMinutes' => (int) $row['GraceMinutes'],
            ];
        }
    }
    return $rows;
}

function validateShiftExists(mysqli $conn, string $shiftCode): bool
{
    $stmt = $conn->prepare("SELECT ShiftCode FROM shift_type WHERE ShiftCode = ? AND IsActive = 1 LIMIT 1");
    if (!$stmt) {
        return false;
    }

    $stmt->bind_param('s', $shiftCode);
    $stmt->execute();
    $res = $stmt->get_result();
    $ok = (bool) $res->fetch_assoc();
    $stmt->close();

    return $ok;
}

function getHolidayMap(mysqli $conn, string $startDate, string $endDate): array
{
    $stmt = $conn->prepare("
        SELECT HolidayDate, HolidayName
        FROM holidays
        WHERE IsActive = 1
          AND HolidayDate BETWEEN ? AND ?
    ");
    if (!$stmt) {
        return [];
    }

    $stmt->bind_param('ss', $startDate, $endDate);
    $stmt->execute();
    $res = $stmt->get_result();

    $map = [];
    while ($row = $res->fetch_assoc()) {
        $map[$row['HolidayDate']] = $row['HolidayName'];
    }
    $stmt->close();

    return $map;
}

function getLatestReceivedEmployeesAll(mysqli $conn): array
{
    $sql = "
        SELECT
            e.EmployeeID,
            e.EmployeeCode,
            e.FirstName,
            e.MiddleName,
            e.LastName,
            ei.DepartmentID,
            d.DepartmentName,
            md.DispatchID,
            md.DispatchDate
        FROM (
            SELECT m1.*
            FROM master_data_dispatches m1
            INNER JOIN (
                SELECT EmployeeID, MAX(DispatchID) AS LatestDispatchID
                FROM master_data_dispatches
                GROUP BY EmployeeID
            ) latest
                ON latest.LatestDispatchID = m1.DispatchID
            WHERE m1.Status = 'Received'
        ) md
        INNER JOIN employee e
            ON e.EmployeeID = md.EmployeeID
        INNER JOIN employmentinformation ei
            ON ei.EmployeeID = e.EmployeeID
        INNER JOIN department d
            ON d.DepartmentID = ei.DepartmentID
        ORDER BY ei.DepartmentID ASC, e.LastName ASC, e.FirstName ASC
    ";

    $result = $conn->query($sql);
    $rows = [];

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $middle = trim((string) $row['MiddleName']);
            $fullName = $row['LastName'] . ', ' . $row['FirstName'] . ($middle !== '' ? (' ' . mb_substr($middle, 0, 1) . '.') : '');

            $rows[] = [
                'EmployeeID' => (int) $row['EmployeeID'],
                'EmployeeCode' => $row['EmployeeCode'],
                'FullName' => $fullName,
                'DepartmentID' => (int) $row['DepartmentID'],
                'DepartmentName' => $row['DepartmentName'],
                'DispatchID' => (int) $row['DispatchID'],
                'DispatchDate' => $row['DispatchDate'],
            ];
        }
    }

    return $rows;
}

function getReceivedEmployeeMap(mysqli $conn): array
{
    $all = getLatestReceivedEmployeesAll($conn);
    $map = [];

    foreach ($all as $row) {
        $map[(int) $row['EmployeeID']] = $row;
    }

    return $map;
}

function getApprovedLeaveMap(mysqli $conn, string $startDate, string $endDate, array $employeeIds): array
{
    if (empty($employeeIds)) {
        return [];
    }

    $placeholders = implode(',', array_fill(0, count($employeeIds), '?'));
    $types = str_repeat('i', count($employeeIds)) . 'ss';

    $sql = "
        SELECT EmployeeID, StartDate, EndDate
        FROM leave_requests
        WHERE Status = 'APPROVED_BY_HR'
          AND EmployeeID IN ($placeholders)
          AND EndDate >= ?
          AND StartDate <= ?
    ";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return [];
    }

    $params = array_merge($employeeIds, [$startDate, $endDate]);
    $bindArgs = [];
    $bindArgs[] = $types;
    foreach ($params as $k => $v) {
        $bindArgs[] = &$params[$k];
    }

    call_user_func_array([$stmt, 'bind_param'], $bindArgs);
    $stmt->execute();
    $res = $stmt->get_result();

    $map = [];
    while ($row = $res->fetch_assoc()) {
        $empId = (int) $row['EmployeeID'];
        $from = new DateTime($row['StartDate']);
        $to = new DateTime($row['EndDate']);

        while ($from <= $to) {
            $date = $from->format('Y-m-d');
            if ($date >= $startDate && $date <= $endDate) {
                $map[$empId][$date] = true;
            }
            $from->modify('+1 day');
        }
    }

    $stmt->close();
    return $map;
}

function getRosterHeader(mysqli $conn, int $departmentId, string $weekStart, string $weekEnd): ?array
{
    $stmt = $conn->prepare("
        SELECT *
        FROM weekly_roster
        WHERE DepartmentID = ?
          AND WeekStart = ?
          AND WeekEnd = ?
        ORDER BY RosterID DESC
        LIMIT 1
    ");
    if (!$stmt) {
        return null;
    }

    $stmt->bind_param('iss', $departmentId, $weekStart, $weekEnd);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    $stmt->close();

    return $row ?: null;
}

function createRosterHeader(mysqli $conn, int $departmentId, string $weekStart, string $weekEnd, int $accountId): int
{
    $stmt = $conn->prepare("
        INSERT INTO weekly_roster (
            DepartmentID,
            WeekStart,
            WeekEnd,
            Status,
            CreatedByAccountID
        ) VALUES (?, ?, ?, 'DRAFT', ?)
    ");
    if (!$stmt) {
        throw new Exception('Failed to prepare roster header insert.');
    }

    $stmt->bind_param('issi', $departmentId, $weekStart, $weekEnd, $accountId);
    if (!$stmt->execute()) {
        throw new Exception('Failed to create roster header.');
    }

    $id = (int) $stmt->insert_id;
    $stmt->close();

    return $id;
}

function ensureRosterHeader(mysqli $conn, int $departmentId, string $weekStart, string $weekEnd, int $accountId): array
{
    $header = getRosterHeader($conn, $departmentId, $weekStart, $weekEnd);
    if ($header) {
        return $header;
    }

    createRosterHeader($conn, $departmentId, $weekStart, $weekEnd, $accountId);
    $header = getRosterHeader($conn, $departmentId, $weekStart, $weekEnd);

    if (!$header) {
        throw new Exception('Failed to create roster header.');
    }

    return $header;
}

function getAssignmentsByRosterIds(mysqli $conn, array $rosterIds): array
{
    if (empty($rosterIds)) {
        return [];
    }

    $placeholders = implode(',', array_fill(0, count($rosterIds), '?'));
    $types = str_repeat('i', count($rosterIds));

    $sql = "
        SELECT AssignmentID, RosterID, EmployeeID, WorkDate, ShiftCode
        FROM roster_assignment
        WHERE RosterID IN ($placeholders)
    ";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return [];
    }

    $params = $rosterIds;
    $bindArgs = [$types];
    foreach ($params as $k => $v) {
        $bindArgs[] = &$params[$k];
    }

    call_user_func_array([$stmt, 'bind_param'], $bindArgs);
    $stmt->execute();
    $res = $stmt->get_result();

    $map = [];
    while ($row = $res->fetch_assoc()) {
        $empId = (int) $row['EmployeeID'];
        $date = $row['WorkDate'];
        $map[$empId][$date] = [
            'AssignmentID' => (int) $row['AssignmentID'],
            'RosterID' => (int) $row['RosterID'],
            'ShiftCode' => $row['ShiftCode']
        ];
    }

    $stmt->close();
    return $map;
}

function upsertAssignment(mysqli $conn, int $rosterId, int $employeeId, string $workDate, string $shiftCode, int $accountId): void
{
    $stmt = $conn->prepare("
        SELECT AssignmentID
        FROM roster_assignment
        WHERE RosterID = ?
          AND EmployeeID = ?
          AND WorkDate = ?
        LIMIT 1
    ");
    if (!$stmt) {
        throw new Exception('Failed to prepare assignment lookup.');
    }

    $stmt->bind_param('iis', $rosterId, $employeeId, $workDate);
    $stmt->execute();
    $res = $stmt->get_result();
    $existing = $res->fetch_assoc();
    $stmt->close();

    if ($existing) {
        $assignmentId = (int) $existing['AssignmentID'];

        $stmt = $conn->prepare("
            UPDATE roster_assignment
            SET ShiftCode = ?, UpdatedByAccountID = ?
            WHERE AssignmentID = ?
        ");
        if (!$stmt) {
            throw new Exception('Failed to prepare assignment update.');
        }

        $stmt->bind_param('sii', $shiftCode, $accountId, $assignmentId);
        if (!$stmt->execute()) {
            throw new Exception('Failed to update assignment.');
        }
        $stmt->close();
        return;
    }

    $stmt = $conn->prepare("
        INSERT INTO roster_assignment (
            RosterID, EmployeeID, WorkDate, ShiftCode, UpdatedByAccountID
        ) VALUES (?, ?, ?, ?, ?)
    ");
    if (!$stmt) {
        throw new Exception('Failed to prepare assignment insert.');
    }

    $stmt->bind_param('iissi', $rosterId, $employeeId, $workDate, $shiftCode, $accountId);
    if (!$stmt->execute()) {
        throw new Exception('Failed to insert assignment.');
    }
    $stmt->close();
}

function deleteAssignment(mysqli $conn, int $rosterId, int $employeeId, string $workDate): void
{
    $stmt = $conn->prepare("
        DELETE FROM roster_assignment
        WHERE RosterID = ?
          AND EmployeeID = ?
          AND WorkDate = ?
    ");
    if (!$stmt) {
        throw new Exception('Failed to prepare assignment delete.');
    }

    $stmt->bind_param('iis', $rosterId, $employeeId, $workDate);
    if (!$stmt->execute()) {
        throw new Exception('Failed to delete assignment.');
    }
    $stmt->close();
}

function getAccountDisplayInfo(mysqli $conn, int $accountId): ?array
{
    $stmt = $conn->prepare("
        SELECT
            ua.AccountID,
            ua.EmployeeID,
            ua.Username,
            ua.Email,
            ua.AccountStatus
        FROM useraccounts ua
        WHERE ua.AccountID = ?
        LIMIT 1
    ");
    if (!$stmt) {
        return null;
    }

    $stmt->bind_param('i', $accountId);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    $stmt->close();

    if (!$row) {
        return null;
    }

    return [
        'AccountID' => (int) $row['AccountID'],
        'EmployeeID' => $row['EmployeeID'] !== null ? (int) $row['EmployeeID'] : null,
        'Username' => $row['Username'],
        'Email' => $row['Email'],
        'AccountStatus' => $row['AccountStatus'],
    ];
}

function formatTimesheetPeriodRow(array $row, ?array $preparedBy = null, ?array $reviewedBy = null, ?array $finalizedBy = null): array
{
    return [
        'PeriodID' => (int) $row['PeriodID'],
        'DepartmentID' => (int) $row['DepartmentID'],
        'StartDate' => $row['StartDate'],
        'EndDate' => $row['EndDate'],
        'Status' => $row['Status'],
        'PreparedByAccountID' => $row['PreparedByAccountID'] !== null ? (int) $row['PreparedByAccountID'] : null,
        'PreparedAt' => $row['PreparedAt'],
        'ReviewedByAccountID' => $row['ReviewedByAccountID'] !== null ? (int) $row['ReviewedByAccountID'] : null,
        'ReviewedAt' => $row['ReviewedAt'],
        'ReviewNotes' => $row['ReviewNotes'],
        'FinalizedByAccountID' => $row['FinalizedByAccountID'] !== null ? (int) $row['FinalizedByAccountID'] : null,
        'FinalizedAt' => $row['FinalizedAt'],
        'IsArchived' => isset($row['IsArchived']) ? (int) $row['IsArchived'] : 0,
        'preparedBy' => $preparedBy,
        'reviewedBy' => $reviewedBy,
        'finalizedBy' => $finalizedBy,
    ];
}

function getTimesheetPeriodById(mysqli $conn, int $periodId): ?array
{
    $stmt = $conn->prepare("
        SELECT *
        FROM timesheet_period
        WHERE PeriodID = ?
        LIMIT 1
    ");
    if (!$stmt) {
        return null;
    }

    $stmt->bind_param('i', $periodId);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    $stmt->close();

    if (!$row) {
        return null;
    }

    $preparedBy = !empty($row['PreparedByAccountID']) ? getAccountDisplayInfo($conn, (int) $row['PreparedByAccountID']) : null;
    $reviewedBy = !empty($row['ReviewedByAccountID']) ? getAccountDisplayInfo($conn, (int) $row['ReviewedByAccountID']) : null;
    $finalizedBy = !empty($row['FinalizedByAccountID']) ? getAccountDisplayInfo($conn, (int) $row['FinalizedByAccountID']) : null;

    return formatTimesheetPeriodRow($row, $preparedBy, $reviewedBy, $finalizedBy);
}

function getTimesheetPeriodsByRange(mysqli $conn, string $startDate, string $endDate): array
{
    $stmt = $conn->prepare("
        SELECT *
        FROM timesheet_period
        WHERE StartDate = ?
          AND EndDate = ?
        ORDER BY DepartmentID ASC, PeriodID DESC
    ");
    if (!$stmt) {
        return [];
    }

    $stmt->bind_param('ss', $startDate, $endDate);
    $stmt->execute();
    $res = $stmt->get_result();

    $rows = [];
    while ($row = $res->fetch_assoc()) {
        $deptId = (int) $row['DepartmentID'];

        if (!isset($rows[$deptId])) {
            $preparedBy = !empty($row['PreparedByAccountID']) ? getAccountDisplayInfo($conn, (int) $row['PreparedByAccountID']) : null;
            $reviewedBy = !empty($row['ReviewedByAccountID']) ? getAccountDisplayInfo($conn, (int) $row['ReviewedByAccountID']) : null;
            $finalizedBy = !empty($row['FinalizedByAccountID']) ? getAccountDisplayInfo($conn, (int) $row['FinalizedByAccountID']) : null;

            $rows[$deptId] = formatTimesheetPeriodRow($row, $preparedBy, $reviewedBy, $finalizedBy);
        }
    }

    $stmt->close();
    return $rows;
}

function ensureTimesheetPeriod(mysqli $conn, int $departmentId, string $startDate, string $endDate, int $accountId): int
{
    $stmt = $conn->prepare("
        SELECT PeriodID
        FROM timesheet_period
        WHERE DepartmentID = ?
          AND StartDate = ?
          AND EndDate = ?
        LIMIT 1
    ");
    if (!$stmt) {
        throw new Exception('Failed to prepare timesheet lookup.');
    }

    $stmt->bind_param('iss', $departmentId, $startDate, $endDate);
    $stmt->execute();
    $res = $stmt->get_result();
    $existing = $res->fetch_assoc();
    $stmt->close();

    $defaultReviewNote = 'Published roster auto-approved and auto-finalized by admin.';

    if ($existing) {
        $periodId = (int) $existing['PeriodID'];

        $stmt = $conn->prepare("
            UPDATE timesheet_period
            SET Status = 'approved',
                PreparedByAccountID = ?,
                PreparedAt = NOW(),
                ReviewedByAccountID = ?,
                ReviewedAt = NOW(),
                ReviewNotes = ?,
                FinalizedByAccountID = ?,
                FinalizedAt = NOW(),
                IsArchived = 0
            WHERE PeriodID = ?
        ");
        if (!$stmt) {
            throw new Exception('Failed to prepare timesheet update.');
        }

        $stmt->bind_param('iisii', $accountId, $accountId, $defaultReviewNote, $accountId, $periodId);
        if (!$stmt->execute()) {
            throw new Exception('Failed to update timesheet period.');
        }
        $stmt->close();

        return $periodId;
    }

    $stmt = $conn->prepare("
        INSERT INTO timesheet_period (
            DepartmentID,
            StartDate,
            EndDate,
            Status,
            PreparedByAccountID,
            PreparedAt,
            ReviewedByAccountID,
            ReviewedAt,
            ReviewNotes,
            FinalizedByAccountID,
            FinalizedAt,
            IsArchived
        ) VALUES (?, ?, ?, 'FINALIZED', ?, NOW(), ?, NOW(), ?, ?, NOW(), 0)
    ");
    if (!$stmt) {
        throw new Exception('Failed to prepare timesheet insert.');
    }

    $stmt->bind_param('issiisi', $departmentId, $startDate, $endDate, $accountId, $accountId, $defaultReviewNote, $accountId);
    if (!$stmt->execute()) {
        throw new Exception('Failed to create timesheet period.');
    }

    $periodId = (int) $stmt->insert_id;
    $stmt->close();

    return $periodId;
}

function buildCombinedRosterPayload(mysqli $conn, string $weekStart): array
{
    $weekEnd = getRosterEndFromStart($weekStart);
    $dates = getRosterDates($weekStart);
    $employees = getLatestReceivedEmployeesAll($conn);
    $employeeIds = array_map(fn($e) => (int) $e['EmployeeID'], $employees);
    $holidayMap = getHolidayMap($conn, $weekStart, $weekEnd);
    $leaveMap = getApprovedLeaveMap($conn, $weekStart, $weekEnd, $employeeIds);
    $timesheetPeriods = getTimesheetPeriodsByRange($conn, $weekStart, $weekEnd);

    $departmentHeaders = [];
    foreach ($employees as $emp) {
        $deptId = (int) $emp['DepartmentID'];
        if (!isset($departmentHeaders[$deptId])) {
            $departmentHeaders[$deptId] = getRosterHeader($conn, $deptId, $weekStart, $weekEnd);
        }
    }

    $rosterIds = [];
    foreach ($departmentHeaders as $hdr) {
        if ($hdr && !empty($hdr['RosterID'])) {
            $rosterIds[] = (int) $hdr['RosterID'];
        }
    }

    $assignMap = getAssignmentsByRosterIds($conn, $rosterIds);

    $groupedRows = [];
    foreach ($employees as $emp) {
        $empId = (int) $emp['EmployeeID'];
        $deptId = (int) $emp['DepartmentID'];

        if (!isset($groupedRows[$deptId])) {
            $groupedRows[$deptId] = [
                'DepartmentID' => $deptId,
                'DepartmentName' => $emp['DepartmentName'],
                'header' => $departmentHeaders[$deptId] ? [
                    'RosterID' => (int) $departmentHeaders[$deptId]['RosterID'],
                    'Status' => $departmentHeaders[$deptId]['Status'],
                    'PublishedAt' => $departmentHeaders[$deptId]['PublishedAt'],
                ] : null,
                'timesheet_period' => $timesheetPeriods[$deptId] ?? null,
                'rows' => []
            ];
        }

        $schedule = [];
        foreach ($dates as $date) {
            $isSunday = ((int) (new DateTime($date))->format('N') === 7);
            $isHoliday = isset($holidayMap[$date]);
            $isLeave = isset($leaveMap[$empId][$date]);
            $shiftCode = $assignMap[$empId][$date]['ShiftCode'] ?? '';

            $locked = false;
            $label = '';

            if ($isSunday) {
                $locked = true;
                $label = 'SUNDAY';
            } elseif ($isHoliday) {
                $locked = true;
                $label = 'HOLIDAY';
            } elseif ($isLeave) {
                $locked = true;
                $label = 'LEAVE';
            }

            $schedule[$date] = [
                'date' => $date,
                'shiftCode' => $shiftCode,
                'locked' => $locked,
                'isHoliday' => $isHoliday,
                'isLeave' => $isLeave,
                'isSunday' => $isSunday,
                'label' => $label,
                'holidayName' => $holidayMap[$date] ?? null
            ];
        }

        $groupedRows[$deptId]['rows'][] = [
            'EmployeeID' => $empId,
            'EmployeeCode' => $emp['EmployeeCode'],
            'FullName' => $emp['FullName'],
            'DepartmentID' => $deptId,
            'DepartmentName' => $emp['DepartmentName'],
            'schedule' => $schedule
        ];
    }

    $unassigned = 0;
    foreach ($groupedRows as $group) {
        foreach ($group['rows'] as $row) {
            foreach ($row['schedule'] as $cell) {
                if (!$cell['locked'] && trim((string) $cell['shiftCode']) === '') {
                    $unassigned++;
                }
            }
        }
    }

    return [
        'weekStart' => $weekStart,
        'weekEnd' => $weekEnd,
        'dates' => $dates,
        'departments' => array_values($groupedRows),
        'timesheet_periods' => array_values($timesheetPeriods),
        'stats' => [
            'employees' => count($employees),
            'departments' => count($groupedRows),
            'unassigned' => $unassigned
        ]
    ];
}

function groupChangesByDepartment(array $changes, array $employeeMap): array
{
    $grouped = [];

    foreach ($changes as $change) {
        $employeeId = (int) ($change['employee_id'] ?? 0);
        if ($employeeId <= 0 || !isset($employeeMap[$employeeId])) {
            throw new Exception('Invalid or non-received employee in changes.');
        }

        $deptId = (int) $employeeMap[$employeeId]['DepartmentID'];
        if (!isset($grouped[$deptId])) {
            $grouped[$deptId] = [];
        }
        $grouped[$deptId][] = $change;
    }

    return $grouped;
}

try {
    switch ($action) {
        case 'get_shifts':
            respond(true, ['shifts' => getActiveShifts($conn)]);
            break;

        case 'get_roster':
            $weekStart = normalizeDate($_GET['week_start'] ?? '') ?: getMondayStart();
            respond(true, buildCombinedRosterPayload($conn, $weekStart));
            break;

        case 'save_draft':
            if ($method !== 'POST') {
                respond(false, ['message' => 'Invalid request method.'], 405);
            }

            $body = getJsonBody();
            $weekStart = normalizeDate($body['week_start'] ?? '');
            $changes = $body['changes'] ?? [];

            if (!$weekStart) {
                respond(false, ['message' => 'Invalid week start.'], 422);
            }
            if (!is_array($changes)) {
                respond(false, ['message' => 'Invalid changes payload.'], 422);
            }

            $weekEnd = getRosterEndFromStart($weekStart);
            $dateSet = array_fill_keys(getRosterDates($weekStart), true);

            $employeeMap = getReceivedEmployeeMap($conn);
            $groupedChanges = groupChangesByDepartment($changes, $employeeMap);

            $holidayMap = getHolidayMap($conn, $weekStart, $weekEnd);
            $leaveMap = getApprovedLeaveMap($conn, $weekStart, $weekEnd, array_keys($employeeMap));

            $conn->begin_transaction();
            try {
                foreach ($groupedChanges as $departmentId => $deptChanges) {
                    $header = ensureRosterHeader($conn, (int) $departmentId, $weekStart, $weekEnd, (int) $accountId);
                    $rosterId = (int) $header['RosterID'];

                    foreach ($deptChanges as $change) {
                        $employeeId = (int) ($change['employee_id'] ?? 0);
                        $workDate = normalizeDate($change['work_date'] ?? '');
                        $shiftCode = trim((string) ($change['shift_code'] ?? ''));

                        if (!$workDate || !isset($dateSet[$workDate])) {
                            throw new Exception('Invalid work date.');
                        }
                        if (!isset($employeeMap[$employeeId])) {
                            throw new Exception('Employee is not in received scope.');
                        }
                        if ((int) $employeeMap[$employeeId]['DepartmentID'] !== (int) $departmentId) {
                            throw new Exception('Employee department mismatch.');
                        }
                        if (isset($holidayMap[$workDate])) {
                            throw new Exception('Holiday cells are locked.');
                        }
                        if (isset($leaveMap[$employeeId][$workDate])) {
                            throw new Exception('Leave cells are locked.');
                        }
                        if ((int) (new DateTime($workDate))->format('N') === 7) {
                            throw new Exception('Sunday is not schedulable.');
                        }

                        if ($shiftCode === '') {
                            deleteAssignment($conn, $rosterId, $employeeId, $workDate);
                        } else {
                            if (!validateShiftExists($conn, $shiftCode)) {
                                throw new Exception('Invalid shift code: ' . $shiftCode);
                            }
                            upsertAssignment($conn, $rosterId, $employeeId, $workDate, $shiftCode, (int) $accountId);
                        }
                    }

                    $stmt = $conn->prepare("
                        UPDATE weekly_roster
                        SET Status = 'DRAFT'
                        WHERE RosterID = ?
                          AND Status <> 'PUBLISHED'
                    ");
                    if ($stmt) {
                        $stmt->bind_param('i', $rosterId);
                        $stmt->execute();
                        $stmt->close();
                    }
                }

                $conn->commit();
                respond(true, array_merge(
                    ['message' => 'Draft saved successfully.'],
                    buildCombinedRosterPayload($conn, $weekStart)
                ));
            } catch (Throwable $e) {
                $conn->rollback();
                respond(false, ['message' => $e->getMessage()], 400);
            }
            break;

        case 'publish_roster':
            if ($method !== 'POST') {
                respond(false, ['message' => 'Invalid request method.'], 405);
            }

            $body = getJsonBody();
            $weekStart = normalizeDate($body['week_start'] ?? '');

            if (!$weekStart) {
                respond(false, ['message' => 'Invalid week start.'], 422);
            }

            $weekEnd = getRosterEndFromStart($weekStart);
            $dates = getRosterDates($weekStart);
            $employeeMap = getReceivedEmployeeMap($conn);
            $currentAdmin = getAccountDisplayInfo($conn, (int) $accountId);

            if (empty($employeeMap)) {
                respond(false, ['message' => 'No received employees found.'], 400);
            }

            $holidayMap = getHolidayMap($conn, $weekStart, $weekEnd);
            $leaveMap = getApprovedLeaveMap($conn, $weekStart, $weekEnd, array_keys($employeeMap));

            $employeesByDept = [];
            foreach ($employeeMap as $empId => $emp) {
                $deptId = (int) $emp['DepartmentID'];
                if (!isset($employeesByDept[$deptId])) {
                    $employeesByDept[$deptId] = [];
                }
                $employeesByDept[$deptId][] = (int) $empId;
            }

            $conn->begin_transaction();
            try {
                $publishedDepartments = [];

                foreach ($employeesByDept as $departmentId => $employeeIds) {
                    $header = ensureRosterHeader($conn, (int) $departmentId, $weekStart, $weekEnd, (int) $accountId);
                    $rosterId = (int) $header['RosterID'];

                    $assignments = getAssignmentsByRosterIds($conn, [$rosterId]);

                    foreach ($employeeIds as $employeeId) {
                        foreach ($dates as $date) {
                            if (isset($holidayMap[$date])) {
                                continue;
                            }
                            if (isset($leaveMap[$employeeId][$date])) {
                                continue;
                            }
                            if ((int) (new DateTime($date))->format('N') === 7) {
                                continue;
                            }

                            $shiftCode = $assignments[$employeeId][$date]['ShiftCode'] ?? '';
                            if ($shiftCode === '') {
                                throw new Exception(
                                    'Cannot publish. Unassigned cell found in department "' .
                                    $employeeMap[$employeeId]['DepartmentName'] .
                                    '" for employee ' . $employeeMap[$employeeId]['FullName'] . ' on ' . $date . '.'
                                );
                            }
                        }
                    }

                    $stmt = $conn->prepare("
                        UPDATE weekly_roster
                        SET Status = 'PUBLISHED',
                            PublishedByAccountID = ?,
                            PublishedAt = NOW()
                        WHERE RosterID = ?
                    ");
                    if (!$stmt) {
                        throw new Exception('Failed to prepare publish update.');
                    }

                    $acc = (int) $accountId;
                    $stmt->bind_param('ii', $acc, $rosterId);
                    if (!$stmt->execute()) {
                        throw new Exception('Failed to publish roster.');
                    }
                    $stmt->close();

                    $periodId = ensureTimesheetPeriod($conn, (int) $departmentId, $weekStart, $weekEnd, $acc);
                    $timesheetPeriod = getTimesheetPeriodById($conn, $periodId);

                    $publishedDepartments[] = [
                        'DepartmentID' => (int) $departmentId,
                        'DepartmentName' => $employeeMap[$employeeIds[0]]['DepartmentName'],
                        'RosterID' => $rosterId,
                        'PeriodID' => $periodId,
                        'Admin' => $currentAdmin,
                        'TimesheetPeriod' => $timesheetPeriod
                    ];
                }

                $conn->commit();

                respond(true, array_merge(
                    [
                        'message' => 'All department rosters published successfully.',
                        'admin' => $currentAdmin,
                        'published_departments' => $publishedDepartments
                    ],
                    buildCombinedRosterPayload($conn, $weekStart)
                ));
            } catch (Throwable $e) {
                $conn->rollback();
                respond(false, ['message' => $e->getMessage()], 400);
            }
            break;

        default:
            respond(false, ['message' => 'Invalid action.'], 400);
    }
} catch (Throwable $e) {
    respond(false, ['message' => 'Server error: ' . $e->getMessage()], 500);
}