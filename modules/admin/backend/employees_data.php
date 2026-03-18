<?php
require_once '../../../config/config.php';

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($conn) || !($conn instanceof mysqli)) {
    echo json_encode([
        'success' => false,
        'message' => 'Database connection not found.'
    ]);
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

function respond($success, $message = '', $data = [], $extra = [])
{
    echo json_encode(array_merge([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ], $extra));
    exit;
}

function normalizeEmployeeIds($raw): array
{
    if (is_string($raw)) {
        $decoded = json_decode($raw, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $raw = $decoded;
        }
    }

    if (!is_array($raw)) {
        return [];
    }

    $ids = [];
    foreach ($raw as $id) {
        $id = (int)$id;
        if ($id > 0) {
            $ids[] = $id;
        }
    }

    return array_values(array_unique($ids));
}

switch ($action) {
    case 'fetch_pending_dispatch':
        fetchPendingDispatch($conn);
        break;

    case 'receive_employees':
        receiveEmployees($conn);
        break;

    default:
        respond(false, 'Invalid action.');
}

function fetchPendingDispatch(mysqli $conn)
{
    $sql = "
        SELECT
            md.DispatchID,
            md.EmployeeID,
            md.Status AS DispatchStatus,
            md.DispatchDate,
            md.Remarks,

            e.EmployeeCode,
            e.FirstName,
            e.MiddleName,
            e.LastName,
            e.DateOfBirth,
            e.Gender,
            e.PersonalEmail,
            e.PhoneNumber,
            e.PermanentAddress,
            e.ProfilePhoto,

            ei.EmploymentID,
            ei.DepartmentID,
            ei.PositionID,
            ei.SalaryGradeID,
            ei.BaseSalary,
            ei.SalaryType,
            ei.HiringDate,
            ei.WorkEmail,
            ei.EmploymentStatus,
            ei.DigitalResume,
            ei.IDPicture,

            d.DepartmentName,
            p.PositionName

        FROM master_data_dispatches md
        INNER JOIN employee e
            ON e.EmployeeID = md.EmployeeID
        LEFT JOIN (
            SELECT x.*
            FROM employmentinformation x
            INNER JOIN (
                SELECT EmployeeID, MAX(EmploymentID) AS LatestEmploymentID
                FROM employmentinformation
                GROUP BY EmployeeID
            ) latest
                ON latest.EmployeeID = x.EmployeeID
               AND latest.LatestEmploymentID = x.EmploymentID
        ) ei
            ON ei.EmployeeID = md.EmployeeID
        LEFT JOIN department d
            ON d.DepartmentID = ei.DepartmentID
        LEFT JOIN positions p
            ON p.PositionID = ei.PositionID
        WHERE md.Status = 'Pending'
        ORDER BY md.DispatchID DESC
    ";

    $result = $conn->query($sql);

    if (!$result) {
        respond(false, 'Failed to fetch pending employees: ' . $conn->error);
    }

    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }

    respond(true, 'Pending dispatched employees fetched successfully.', $rows, [
        'count' => count($rows)
    ]);
}

function receiveEmployees(mysqli $conn)
{
    $employeeIds = normalizeEmployeeIds($_POST['employee_ids'] ?? []);

    if (empty($employeeIds)) {
        respond(false, 'No employee IDs provided.');
    }

    $checkSql = "
        SELECT DispatchID, EmployeeID, Status
        FROM master_data_dispatches
        WHERE EmployeeID = ?
        ORDER BY DispatchID DESC
        LIMIT 1
    ";

    $updateSql = "
        UPDATE master_data_dispatches
        SET Status = 'Received'
        WHERE DispatchID = ?
          AND Status = 'Pending'
    ";

    $checkStmt = $conn->prepare($checkSql);
    if (!$checkStmt) {
        respond(false, 'Failed to prepare check statement: ' . $conn->error);
    }

    $updateStmt = $conn->prepare($updateSql);
    if (!$updateStmt) {
        respond(false, 'Failed to prepare update statement: ' . $conn->error);
    }

    $updatedCount = 0;
    $alreadyReceivedCount = 0;
    $notFoundCount = 0;

    $updatedIds = [];
    $alreadyReceivedIds = [];
    $notFoundIds = [];

    $conn->begin_transaction();

    try {
        foreach ($employeeIds as $employeeId) {
            $checkStmt->bind_param('i', $employeeId);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();

            if (!$checkResult || $checkResult->num_rows === 0) {
                $notFoundCount++;
                $notFoundIds[] = $employeeId;
                continue;
            }

            $dispatchRow = $checkResult->fetch_assoc();
            $dispatchId = (int)$dispatchRow['DispatchID'];
            $status = trim((string)$dispatchRow['Status']);

            if (strcasecmp($status, 'Received') === 0) {
                $alreadyReceivedCount++;
                $alreadyReceivedIds[] = $employeeId;
                continue;
            }

            if (strcasecmp($status, 'Pending') !== 0) {
                $notFoundCount++;
                $notFoundIds[] = $employeeId;
                continue;
            }

            $updateStmt->bind_param('i', $dispatchId);
            $updateStmt->execute();

            if ($updateStmt->affected_rows > 0) {
                $updatedCount++;
                $updatedIds[] = $employeeId;
            }
            else {
                $notFoundCount++;
                $notFoundIds[] = $employeeId;
            }
        }

        $conn->commit();

        respond(true, 'Receive process completed successfully.', [], [
            'updated_count' => $updatedCount,
            'already_received_count' => $alreadyReceivedCount,
            'not_found_count' => $notFoundCount,
            'updated_ids' => $updatedIds,
            'already_received_ids' => $alreadyReceivedIds,
            'not_found_ids' => $notFoundIds
        ]);
    }
    catch (Throwable $e) {
        $conn->rollback();
        respond(false, 'Failed to receive employees: ' . $e->getMessage());
    }
}