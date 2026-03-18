<?php
require_once __DIR__ . "/auth_employee.php";
require_once __DIR__ . "/timesheet_record.php";

header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set('Asia/Manila');

function respond(bool $ok, array $data = [], int $code = 200): void
{
    http_response_code($code);
    echo json_encode(array_merge(['ok' => $ok], $data));
    exit;
}

function nextAttendanceEventType(array $events): string
{
    $hasTimeIn = false;
    $hasBreakIn = false;
    $hasBreakOut = false;
    $hasTimeOut = false;

    foreach ($events as $type) {
        $type = strtoupper((string) $type);
        if ($type === 'TIME_IN') $hasTimeIn = true;
        if ($type === 'BREAK_IN') $hasBreakIn = true;
        if ($type === 'BREAK_OUT') $hasBreakOut = true;
        if ($type === 'TIME_OUT') $hasTimeOut = true;
    }

    if (!$hasTimeIn) return 'TIME_IN';
    if (!$hasBreakIn) return 'BREAK_IN';
    if (!$hasBreakOut) return 'BREAK_OUT';
    if (!$hasTimeOut) return 'TIME_OUT';
    return 'COMPLETED';
}

function findOpenSession(mysqli $conn, int $employeeId): ?array
{
    $sql = "
        SELECT SessionID, WorkDate, AssignmentID, Status
        FROM attendance_session
        WHERE EmployeeID = ?
          AND Status = 'OPEN'
        ORDER BY WorkDate DESC, SessionID DESC
        LIMIT 1
    ";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        throw new Exception("Failed to prepare open session lookup: " . $conn->error);
    }

    $stmt->bind_param("i", $employeeId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result ? $result->fetch_assoc() : null;
    $stmt->close();

    return $row ?: null;
}

function resolveEventDateTime(?string $clientTimeRaw, ?string $clientTimezone): array
{
    $appTimezone = new DateTimeZone('Asia/Manila');
    $serverNow = new DateTime('now', $appTimezone);

    $eventDt = clone $serverNow;

    $clientTimeRaw = trim((string) $clientTimeRaw);
    $clientTimezone = trim((string) $clientTimezone);

    if ($clientTimeRaw !== '') {
        try {
            if (preg_match('/(Z|[+\-]\d{2}:\d{2})$/', $clientTimeRaw)) {
                $tmp = new DateTime($clientTimeRaw);
                $tmp->setTimezone($appTimezone);
                $eventDt = $tmp;
            } else {
                $sourceTz = new DateTimeZone($clientTimezone !== '' ? $clientTimezone : 'Asia/Manila');
                $tmp = new DateTime($clientTimeRaw, $sourceTz);
                $tmp->setTimezone($appTimezone);
                $eventDt = $tmp;
            }
        } catch (Throwable $e) {
            $eventDt = clone $serverNow;
        }
    }

    return [
        'event_time' => $eventDt->format('Y-m-d H:i:s'),
        'work_date'  => $eventDt->format('Y-m-d'),
    ];
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(false, ['message' => 'Invalid request method.'], 405);
}

if (!isset($conn) || !($conn instanceof mysqli)) {
    respond(false, ['message' => 'Database connection not available.'], 500);
}

$employeeId = $_SESSION['employee_id'] ?? $_SESSION['EmployeeID'] ?? null;
if (!$employeeId) {
    respond(false, ['message' => 'Employee session not found.'], 401);
}

$eventType = strtoupper(trim($_POST['event_type'] ?? 'TIME_IN'));

$latitude = isset($_POST['latitude']) && $_POST['latitude'] !== ''
    ? (float) $_POST['latitude']
    : null;

$longitude = isset($_POST['longitude']) && $_POST['longitude'] !== ''
    ? (float) $_POST['longitude']
    : null;

$locationId = isset($_POST['location_id']) && $_POST['location_id'] !== ''
    ? (int) $_POST['location_id']
    : null;

$distanceMeters = isset($_POST['distance_meters']) && $_POST['distance_meters'] !== ''
    ? (float) $_POST['distance_meters']
    : null;

$faceImage = $_POST['face_image'] ?? '';
$faceStatus = strtoupper(trim($_POST['face_status'] ?? 'MATCH'));
$livenessStatus = strtoupper(trim($_POST['liveness_status'] ?? 'NOT_CHECKED'));
$faceScore = isset($_POST['face_score']) && $_POST['face_score'] !== ''
    ? round((float) $_POST['face_score'], 2)
    : null;

if ($latitude === null || $longitude === null || $faceImage === '') {
    respond(false, ['message' => 'Missing required attendance data.'], 422);
}

$allowedEventTypes = ['TIME_IN', 'BREAK_IN', 'BREAK_OUT', 'TIME_OUT'];
if (!in_array($eventType, $allowedEventTypes, true)) {
    respond(false, ['message' => 'Invalid attendance event type.'], 422);
}

$allowedFaceStatuses = ['MATCH', 'NO_MATCH'];
if (!in_array($faceStatus, $allowedFaceStatuses, true)) {
    $faceStatus = 'NO_MATCH';
}

$livenessStatus = str_replace(['PASS', 'FAIL'], ['PASSED', 'FAILED'], $livenessStatus);
$allowedLivenessStatuses = ['PASSED', 'FAILED', 'NOT_CHECKED'];
if (!in_array($livenessStatus, $allowedLivenessStatuses, true)) {
    $livenessStatus = 'NOT_CHECKED';
}

$geoStatus = 'IN_GEOFENCE';

if (!preg_match('/^data:image\/(\w+);base64,/', $faceImage, $matches)) {
    respond(false, ['message' => 'Invalid face image format.'], 422);
}

$imageType = strtolower($matches[1]);
if (!in_array($imageType, ['jpg', 'jpeg', 'png'], true)) {
    respond(false, ['message' => 'Unsupported image type.'], 422);
}

$base64Data = substr($faceImage, strpos($faceImage, ',') + 1);
$decodedImage = base64_decode($base64Data, true);

if ($decodedImage === false) {
    respond(false, ['message' => 'Failed to decode face image.'], 422);
}

$uploadDir = __DIR__ . "/../../../uploads/attendance_capture/";
if (!is_dir($uploadDir) && !mkdir($uploadDir, 0777, true)) {
    respond(false, ['message' => 'Failed to create upload directory.'], 500);
}

$fileName = "emp_" . $employeeId . "_" . date("Ymd_His") . "_" . substr((string) microtime(true), -6) . "." . $imageType;
$filePath = $uploadDir . $fileName;
$relativePath = "uploads/attendance_capture/" . $fileName;

if (!file_put_contents($filePath, $decodedImage)) {
    respond(false, ['message' => 'Failed to save face capture.'], 500);
}

$conn->begin_transaction();

try {
    $clientTimeRaw = $_POST['client_time'] ?? '';
    $clientTimezone = $_POST['client_timezone'] ?? 'Asia/Manila';

    $resolvedDateTime = resolveEventDateTime($clientTimeRaw, $clientTimezone);
    $eventTime = $resolvedDateTime['event_time'];
    $currentDate = $resolvedDateTime['work_date'];

    $openSession = findOpenSession($conn, (int) $employeeId);
    $workDate = $openSession['WorkDate'] ?? $currentDate;

    $assignmentId = null;
    $departmentId = null;
    $shiftCode = null;
    $scheduledStart = null;
    $scheduledEnd = null;
    $breakMinutesPlanned = 0;
    $graceMinutes = 0;

    $assignmentSql = "
        SELECT
            ra.AssignmentID,
            ra.ShiftCode,
            wr.DepartmentID,
            st.StartTime,
            st.EndTime,
            st.BreakMinutes,
            st.GraceMinutes
        FROM roster_assignment ra
        INNER JOIN weekly_roster wr ON wr.RosterID = ra.RosterID
        LEFT JOIN shift_type st ON st.ShiftCode = ra.ShiftCode
        WHERE ra.EmployeeID = ?
          AND ra.WorkDate = ?
        LIMIT 1
    ";
    $assignmentStmt = $conn->prepare($assignmentSql);

    if (!$assignmentStmt) {
        throw new Exception("Failed to prepare roster assignment query: " . $conn->error);
    }

    $assignmentStmt->bind_param("is", $employeeId, $workDate);
    $assignmentStmt->execute();
    $assignmentResult = $assignmentStmt->get_result();
    $assignmentRow = $assignmentResult ? $assignmentResult->fetch_assoc() : null;
    $assignmentStmt->close();

    if ($assignmentRow) {
        $assignmentId = (int) $assignmentRow['AssignmentID'];
        $departmentId = isset($assignmentRow['DepartmentID']) ? (int) $assignmentRow['DepartmentID'] : null;
        $shiftCode = $assignmentRow['ShiftCode'] ?? null;
        $scheduledStart = $assignmentRow['StartTime'] ?? null;
        $scheduledEnd = $assignmentRow['EndTime'] ?? null;
        $breakMinutesPlanned = isset($assignmentRow['BreakMinutes']) ? (int) $assignmentRow['BreakMinutes'] : 0;
        $graceMinutes = isset($assignmentRow['GraceMinutes']) ? (int) $assignmentRow['GraceMinutes'] : 0;
    } else {
        $deptSql = "
            SELECT DepartmentID
            FROM employmentinformation
            WHERE EmployeeID = ?
            ORDER BY EmploymentID DESC
            LIMIT 1
        ";
        $deptStmt = $conn->prepare($deptSql);

        if (!$deptStmt) {
            throw new Exception("Failed to prepare department lookup: " . $conn->error);
        }

        $deptStmt->bind_param("i", $employeeId);
        $deptStmt->execute();
        $deptResult = $deptStmt->get_result();
        $deptRow = $deptResult ? $deptResult->fetch_assoc() : null;
        $deptStmt->close();

        if ($deptRow) {
            $departmentId = (int) ($deptRow['DepartmentID'] ?? 0);
        }
    }

    $existingEventTypes = [];

    $existingSql = "
        SELECT ae.EventType
        FROM attendance_event ae
        INNER JOIN attendance_session s ON s.SessionID = ae.SessionID
        WHERE s.EmployeeID = ?
          AND s.WorkDate = ?
        ORDER BY ae.EventTime ASC, ae.EventID ASC
    ";
    $existingStmt = $conn->prepare($existingSql);

    if (!$existingStmt) {
        throw new Exception("Failed to prepare attendance sequence check: " . $conn->error);
    }

    $existingStmt->bind_param("is", $employeeId, $workDate);
    $existingStmt->execute();
    $existingResult = $existingStmt->get_result();

    while ($row = $existingResult->fetch_assoc()) {
        $existingEventTypes[] = strtoupper((string) ($row['EventType'] ?? ''));
    }
    $existingStmt->close();

    $expectedEventType = nextAttendanceEventType($existingEventTypes);

    if ($expectedEventType === 'COMPLETED') {
        throw new Exception("Attendance for this work date is already completed.");
    }

    if ($eventType !== $expectedEventType) {
        throw new Exception("Invalid attendance sequence. Expected next event: " . $expectedEventType . ".");
    }

    $sessionId = null;

    $sessionFindSql = "
        SELECT SessionID, Status
        FROM attendance_session
        WHERE EmployeeID = ?
          AND WorkDate = ?
        LIMIT 1
    ";
    $sessionFindStmt = $conn->prepare($sessionFindSql);

    if (!$sessionFindStmt) {
        throw new Exception("Failed to prepare attendance session lookup: " . $conn->error);
    }

    $sessionFindStmt->bind_param("is", $employeeId, $workDate);
    $sessionFindStmt->execute();
    $sessionFindResult = $sessionFindStmt->get_result();
    $sessionRow = $sessionFindResult ? $sessionFindResult->fetch_assoc() : null;
    $sessionFindStmt->close();

    if ($sessionRow) {
        $sessionId = (int) $sessionRow['SessionID'];

        if (($sessionRow['Status'] ?? '') === 'CLOSED') {
            throw new Exception("Attendance session for this work date is already closed.");
        }
    } else {
        if ($eventType !== 'TIME_IN') {
            throw new Exception("No open attendance session found for this work date. Please time in first.");
        }

        $sessionInsertSql = "
            INSERT INTO attendance_session
            (
                EmployeeID,
                WorkDate,
                AssignmentID,
                Status
            )
            VALUES
            (
                ?,
                ?,
                ?,
                'OPEN'
            )
        ";
        $sessionInsertStmt = $conn->prepare($sessionInsertSql);

        if (!$sessionInsertStmt) {
            throw new Exception("Failed to prepare attendance session insert: " . $conn->error);
        }

        $sessionInsertStmt->bind_param("isi", $employeeId, $workDate, $assignmentId);

        if (!$sessionInsertStmt->execute()) {
            throw new Exception("Failed to create attendance session: " . $sessionInsertStmt->error);
        }

        $sessionId = $sessionInsertStmt->insert_id;
        $sessionInsertStmt->close();
    }

    $eventSql = "
        INSERT INTO attendance_event
        (
            SessionID,
            EventType,
            EventTime,
            Latitude,
            Longitude,
            LocationID,
            DistanceMeters,
            GeoStatus,
            FaceStatus,
            FaceScore,
            LivenessStatus
        )
        VALUES
        (
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?
        )
    ";

    $eventStmt = $conn->prepare($eventSql);

    if (!$eventStmt) {
        throw new Exception("Failed to prepare attendance_event insert: " . $conn->error);
    }

    $eventStmt->bind_param(
        "issddidssds",
        $sessionId,
        $eventType,
        $eventTime,
        $latitude,
        $longitude,
        $locationId,
        $distanceMeters,
        $geoStatus,
        $faceStatus,
        $faceScore,
        $livenessStatus
    );

    if (!$eventStmt->execute()) {
        throw new Exception("Failed to insert attendance_event: " . $eventStmt->error);
    }

    $eventId = $eventStmt->insert_id;
    $eventStmt->close();

    $captureSql = "
        INSERT INTO attendance_capture
        (
            EventID,
            ImagePath
        )
        VALUES
        (
            ?,
            ?
        )
    ";

    $captureStmt = $conn->prepare($captureSql);

    if (!$captureStmt) {
        throw new Exception("Failed to prepare attendance_capture insert: " . $conn->error);
    }

    $captureStmt->bind_param("is", $eventId, $relativePath);

    if (!$captureStmt->execute()) {
        throw new Exception("Failed to insert attendance_capture: " . $captureStmt->error);
    }

    $captureStmt->close();

    $timesheetResult = recordTimesheetAttendance(
        $conn,
        (int) $employeeId,
        (string) $workDate,
        (int) $sessionId,
        (string) $eventType,
        (string) $eventTime,
        $assignmentId,
        $departmentId,
        $shiftCode,
        $scheduledStart,
        $scheduledEnd,
        (int) $breakMinutesPlanned,
        (int) $graceMinutes
    );

    if ($eventType === 'TIME_OUT') {
        $closeSql = "
            UPDATE attendance_session
            SET
                Status = 'CLOSED',
                ClosedAt = ?
            WHERE SessionID = ?
        ";
        $closeStmt = $conn->prepare($closeSql);

        if (!$closeStmt) {
            throw new Exception("Failed to prepare attendance session close: " . $conn->error);
        }

        $closeStmt->bind_param("si", $eventTime, $sessionId);

        if (!$closeStmt->execute()) {
            throw new Exception("Failed to close attendance session: " . $closeStmt->error);
        }

        $closeStmt->close();
    }

    $conn->commit();

    respond(true, [
        'message' => $eventType . ' submitted successfully.',
        'saved_event_time' => $eventTime,
        'client_time_received' => $clientTimeRaw,
        'client_timezone_received' => $clientTimezone,
        'work_date' => $workDate,
        'session_id' => $sessionId,
        'event_id' => $eventId,
        'image_path' => $relativePath,
        'timesheet_updated' => $timesheetResult['timesheet_updated'],
        'timesheet_message' => $timesheetResult['timesheet_message'],
        'timesheet_summary_updated' => $timesheetResult['timesheet_summary_updated'],
        'summary_message' => $timesheetResult['summary_message']
    ]);
} catch (Throwable $e) {
    $conn->rollback();

    if (isset($filePath) && is_file($filePath)) {
        @unlink($filePath);
    }

    respond(false, [
        'message' => $e->getMessage()
    ], 500);
}