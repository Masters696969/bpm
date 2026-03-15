<?php
require_once __DIR__ . "/auth_officer.php";
require_once __DIR__ . "/../../../config/config.php";

header('Content-Type: application/json; charset=utf-8');

function respond($ok, $data = [], $code = 200) {
    http_response_code($code);
    echo json_encode(array_merge(['ok' => $ok], $data));
    exit;
}

if (!isset($conn) || !($conn instanceof mysqli)) {
    respond(false, ['message' => 'Database connection not available.'], 500);
}

$accountId  = (int)($_SESSION['account_id'] ?? $_SESSION['AccountID'] ?? $_SESSION['user_id'] ?? 0);
$deptId     = (int)($_SESSION['department_id'] ?? 0);
$employeeId = (int)($_SESSION['employee_id'] ?? $_SESSION['EmployeeID'] ?? 0);

if ($accountId <= 0 || $deptId <= 0 || $employeeId <= 0) {
    respond(false, ['message' => 'Unauthorized session.'], 401);
}

function statusLabel($status) {
    return match($status) {
        'PENDING'              => 'Pending',
        'APPROVED_BY_OFFICER'  => 'Sent to HR',
        'APPROVED_BY_HR'       => 'Approved by HR',
        'PAID'                 => 'Paid',
        'REJECTED'             => 'Rejected',
        'CANCELLED'            => 'Cancelled',
        default                => $status
    };
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

/* =========================================================
   GET CLAIMS
   ========================================================= */
if ($method === 'GET') {
    $statusFilter = $_GET['status'] ?? 'PENDING';

    $allowedStatuses = [
        'PENDING',
        'APPROVED_BY_OFFICER',
        'REJECTED',
        'ALL'
    ];

    if (!in_array($statusFilter, $allowedStatuses, true)) {
        $statusFilter = 'PENDING';
    }

    $sql = "
        SELECT
            rc.ClaimID,
            rc.EmployeeID,
            rc.PeriodID,
            rc.ClaimDate,
            rc.Category,
            rc.Amount,
            rc.Description,
            rc.ReceiptImage,
            rc.Status,
            rc.OfficerApprovedBy,
            rc.HRApprovedBy,
            rc.OfficerNotes,
            rc.HRNotes,
            rc.CreatedAt,

            e.EmployeeCode,
            CONCAT(
                e.FirstName,
                IF(e.MiddleName IS NOT NULL AND e.MiddleName <> '', CONCAT(' ', e.MiddleName), ''),
                ' ',
                e.LastName
            ) AS EmployeeName,

            d.DepartmentName,
            p.PositionName,

            tp.StartDate,
            tp.EndDate

        FROM reimbursement_claims rc
        INNER JOIN employee e
            ON e.EmployeeID = rc.EmployeeID
        INNER JOIN employmentinformation ei
            ON ei.EmployeeID = rc.EmployeeID
        LEFT JOIN department d
            ON d.DepartmentID = ei.DepartmentID
        LEFT JOIN positions p
            ON p.PositionID = ei.PositionID
        LEFT JOIN timesheet_period tp
            ON tp.PeriodID = rc.PeriodID
        WHERE ei.DepartmentID = ?
    ";

    if ($statusFilter !== 'ALL') {
        $sql .= " AND rc.Status = ? ";
    }

    $sql .= " ORDER BY rc.CreatedAt DESC, rc.ClaimID DESC ";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        respond(false, ['message' => 'Failed to prepare query.', 'error' => $conn->error], 500);
    }

    if ($statusFilter !== 'ALL') {
        $stmt->bind_param("is", $deptId, $statusFilter);
    } else {
        $stmt->bind_param("i", $deptId);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $claims = [];
    while ($row = $result->fetch_assoc()) {
        $claims[] = $row;
    }

    $stmt->close();

    respond(true, ['claims' => $claims]);
}

/* =========================================================
   POST ACTIONS
   ========================================================= */
if ($method === 'POST') {
    $action  = $_POST['action'] ?? '';
    $claimId = (int)($_POST['claim_id'] ?? 0);
    $notes   = trim($_POST['notes'] ?? '');

    if ($claimId <= 0) {
        respond(false, ['message' => 'Invalid claim ID.'], 422);
    }

    $checkSql = "
        SELECT
            rc.ClaimID,
            rc.Status
        FROM reimbursement_claims rc
        INNER JOIN employmentinformation ei
            ON ei.EmployeeID = rc.EmployeeID
        WHERE rc.ClaimID = ?
          AND ei.DepartmentID = ?
        LIMIT 1
    ";

    $stmtCheck = $conn->prepare($checkSql);
    if (!$stmtCheck) {
        respond(false, ['message' => 'Failed to prepare validation query.'], 500);
    }

    $stmtCheck->bind_param("ii", $claimId, $deptId);
    $stmtCheck->execute();
    $res = $stmtCheck->get_result();
    $claim = $res->fetch_assoc();
    $stmtCheck->close();

    if (!$claim) {
        respond(false, ['message' => 'Claim not found or not under your department.'], 404);
    }

    if ($action === 'approve') {
        if ($claim['Status'] !== 'PENDING') {
            respond(false, ['message' => 'Only pending claims can be approved by officer.'], 422);
        }

        $newStatus = 'APPROVED_BY_OFFICER';

        $sql = "
            UPDATE reimbursement_claims
            SET Status = ?,
                OfficerApprovedBy = ?,
                OfficerNotes = ?
            WHERE ClaimID = ?
              AND Status = 'PENDING'
            LIMIT 1
        ";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            respond(false, ['message' => 'Failed to prepare approval query.'], 500);
        }

        $stmt->bind_param("sisi", $newStatus, $accountId, $notes, $claimId);

        if (!$stmt->execute()) {
            $stmt->close();
            respond(false, ['message' => 'Failed to approve claim.'], 500);
        }

        if ($stmt->affected_rows <= 0) {
            $stmt->close();
            respond(false, ['message' => 'No rows updated. Claim may already be processed.'], 409);
        }

        $stmt->close();

        respond(true, [
            'message' => 'Claim approved and sent to HR.',
            'new_status' => $newStatus,
            'new_label' => statusLabel($newStatus)
        ]);
    }

    if ($action === 'reject') {
        if ($claim['Status'] !== 'PENDING') {
            respond(false, ['message' => 'Only pending claims can be rejected by officer.'], 422);
        }

        $newStatus = 'REJECTED';

        $sql = "
            UPDATE reimbursement_claims
            SET Status = ?,
                OfficerApprovedBy = ?,
                OfficerNotes = ?
            WHERE ClaimID = ?
              AND Status = 'PENDING'
            LIMIT 1
        ";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            respond(false, ['message' => 'Failed to prepare rejection query.'], 500);
        }

        $stmt->bind_param("sisi", $newStatus, $accountId, $notes, $claimId);

        if (!$stmt->execute()) {
            $stmt->close();
            respond(false, ['message' => 'Failed to reject claim.'], 500);
        }

        if ($stmt->affected_rows <= 0) {
            $stmt->close();
            respond(false, ['message' => 'No rows updated. Claim may already be processed.'], 409);
        }

        $stmt->close();

        respond(true, [
            'message' => 'Claim rejected by officer.',
            'new_status' => $newStatus,
            'new_label' => statusLabel($newStatus)
        ]);
    }

    respond(false, ['message' => 'Invalid action.'], 400);
}

respond(false, ['message' => 'Method not allowed.'], 405);