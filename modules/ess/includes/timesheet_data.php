<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/auth_employee.php';
require_once __DIR__ . '/../../../config/config.php';

$mysqli = null;

if (isset($conn) && $conn instanceof mysqli) {
    $mysqli = $conn;
}
elseif (isset($db) && $db instanceof mysqli) {
    $mysqli = $db;
}

if (!$mysqli instanceof mysqli) {
    echo json_encode([
        'success' => false,
        'message' => 'Database connection not found.'
    ]);
    exit;
}

$employeeId = 0;
foreach (['employee_id', 'EmployeeID'] as $key) {
    if (!empty($_SESSION[$key])) {
        $employeeId = (int)$_SESSION[$key];
        break;
    }
}

if ($employeeId <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Employee session not found.'
    ]);
    exit;
}

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'get_periods':
        getPeriods($mysqli, $employeeId);
        break;

    case 'get_timesheet':
        getTimesheet($mysqli, $employeeId);
        break;

    default:
        echo json_encode([
            'success' => false,
            'message' => 'Invalid action.'
        ]);
        exit;
}

function getPeriods(mysqli $mysqli, int $employeeId): void
{
    $sql = "
        SELECT DISTINCT
            tp.PeriodID,
            tp.StartDate,
            tp.EndDate,
            tp.Status
        FROM timesheet_period tp
        INNER JOIN timesheet_daily td
            ON td.PeriodID = tp.PeriodID
        WHERE td.EmployeeID = ?
        ORDER BY tp.StartDate DESC, tp.PeriodID DESC
    ";

    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $employeeId);
    $stmt->execute();
    $result = $stmt->get_result();

    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = [
            'PeriodID' => (int)$row['PeriodID'],
            'StartDate' => $row['StartDate'],
            'EndDate' => $row['EndDate'],
            'Status' => $row['Status'] ?? 'OPEN',
        ];
    }

    echo json_encode($rows);
    exit;
}

function getTimesheet(mysqli $mysqli, int $employeeId): void
{
    $periodId = (int)($_GET['period_id'] ?? 0);

    if ($periodId <= 0) {
        echo json_encode([]);
        exit;
    }

    $sql = "
        SELECT
            td.TimesheetDayID,
            td.WorkDate,
            td.ShiftCode,
            td.ScheduledStart,
            td.ScheduledEnd,
            td.ActualTimeIn,
            td.ActualTimeOut,
            td.RegularMinutes,
            td.OvertimeMinutes,
            td.NightDiffMinutes,
            td.LateMinutes,
            td.UndertimeMinutes,
            td.DayStatus,
            td.Remarks,
            st.ShiftName,
            st.StartTime AS ShiftStartTime,
            st.EndTime AS ShiftEndTime,
            st.BreakMinutes
        FROM timesheet_daily td
        LEFT JOIN shift_type st
            ON st.ShiftCode = td.ShiftCode
        WHERE td.EmployeeID = ?
          AND td.PeriodID = ?
        ORDER BY td.WorkDate ASC
    ";

    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ii", $employeeId, $periodId);
    $stmt->execute();
    $result = $stmt->get_result();

    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $workDate = $row['WorkDate'];
        $dayName = $workDate ? date('D', strtotime($workDate)) : '';

        $scheduledDisplay = formatTimeRange($row['ScheduledStart'] ?? null, $row['ScheduledEnd'] ?? null);
        $actualInDisplay = formatDateTimeValue($row['ActualTimeIn'] ?? null);
        $actualOutDisplay = formatDateTimeValue($row['ActualTimeOut'] ?? null);
        $breakMinutes = (int)($row['BreakMinutes'] ?? 0);

        $rows[] = [
            'TimesheetDayID' => (int)$row['TimesheetDayID'],
            'WorkDate' => $row['WorkDate'],
            'WorkDateFormatted' => $workDate ? date('M d, Y', strtotime($workDate)) : '-',
            'DayName' => $dayName,
            'ShiftCode' => $row['ShiftCode'] ?: '-',
            'ShiftName' => $row['ShiftName'] ?? '',
            'ScheduledDisplay' => $scheduledDisplay ?: '-',
            'ActualTimeIn' => $row['ActualTimeIn'],
            'ActualTimeOut' => $row['ActualTimeOut'],
            'ActualTimeInDisplay' => $actualInDisplay ?: '-',
            'ActualTimeOutDisplay' => $actualOutDisplay ?: '-',
            'RegularMinutes' => (int)($row['RegularMinutes'] ?? 0),
            'OvertimeMinutes' => (int)($row['OvertimeMinutes'] ?? 0),
            'NightDiffMinutes' => (int)($row['NightDiffMinutes'] ?? 0),
            'LateMinutes' => (int)($row['LateMinutes'] ?? 0),
            'UndertimeMinutes' => (int)($row['UndertimeMinutes'] ?? 0),
            'BreakMinutes' => $breakMinutes,
            'BreakHours' => number_format($breakMinutes / 60, 2),
            'RegularHours' => number_format(((int)($row['RegularMinutes'] ?? 0)) / 60, 2),
            'OvertimeHours' => number_format(((int)($row['OvertimeMinutes'] ?? 0)) / 60, 2),
            'DayStatus' => $row['DayStatus'] ?? '',
            'Remarks' => $row['Remarks'] ?? '',
        ];
    }

    echo json_encode($rows);
    exit;
}

function formatTimeRange($start, $end): string
{
    $startFmt = formatDateTimeValue($start, true);
    $endFmt = formatDateTimeValue($end, true);

    if ($startFmt && $endFmt) {
        return $startFmt . ' - ' . $endFmt;
    }

    return $startFmt ?: ($endFmt ?: '');
}

function formatDateTimeValue($value, bool $timeOnly = false): string
{
    if (empty($value) || $value === '0000-00-00 00:00:00') {
        return '';
    }

    $timestamp = strtotime($value);
    if (!$timestamp) {
        return '';
    }

    return $timeOnly ? date('h:i A', $timestamp) : date('h:i A', $timestamp);
}