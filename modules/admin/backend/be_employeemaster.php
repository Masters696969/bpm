<?php
require_once '../../../config/config.php';

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if (!isset($conn) || !($conn instanceof mysqli)) {
    echo json_encode(['success' => false, 'message' => 'Database connection not found.']);
    exit;
}

$action = '';
if (isset($_POST['action']) && $_POST['action'] !== '') {
    $action = trim((string)$_POST['action']);
}
elseif (isset($_GET['action']) && $_GET['action'] !== '') {
    $action = trim((string)$_GET['action']);
}

function normalizeDateOrNull($value)
{
    $value = trim((string)$value);
    return $value !== '' ? $value : null;
}

function resolveCurrentAccountId(mysqli $conn): int
{
    if (!empty($_SESSION['account_id'])) {
        return (int)$_SESSION['account_id'];
    }

    $sessionUserId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
    $sessionUsername = trim($_SESSION['username'] ?? '');

    if ($sessionUserId > 0) {
        $sql = "SELECT AccountID FROM useraccounts WHERE AccountID = ? OR EmployeeID = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("ii", $sessionUserId, $sessionUserId);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            if ($row)
                return (int)$row['AccountID'];
        }
    }

    if ($sessionUsername !== '') {
        $sql = "SELECT AccountID FROM useraccounts WHERE Username = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("s", $sessionUsername);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            if ($row)
                return (int)$row['AccountID'];
        }
    }
    return 0;
}

try {
    if ($action === 'fetch_employees') {
        $sql = "
            SELECT 
                e.EmployeeID, e.EmployeeCode, e.FirstName, e.LastName,
                ei.EmploymentStatus, d.DepartmentName, p.PositionName, sg.GradeLevel,
                COALESCE(mdd_latest.Status, 'Ready') AS DispatchStatus
            FROM employee e
            LEFT JOIN (
                SELECT ei1.* FROM employmentinformation ei1
                INNER JOIN (
                    SELECT EmployeeID, MAX(EmploymentID) AS LatestEmploymentID
                    FROM employmentinformation GROUP BY EmployeeID
                ) latest_ei ON latest_ei.EmployeeID = ei1.EmployeeID AND latest_ei.LatestEmploymentID = ei1.EmploymentID
            ) ei ON ei.EmployeeID = e.EmployeeID
            LEFT JOIN department d ON ei.DepartmentID = d.DepartmentID
            LEFT JOIN positions p ON ei.PositionID = p.PositionID
            LEFT JOIN salary_grades sg ON sg.SalaryGradeID = COALESCE(ei.SalaryGradeID, p.SalaryGradeID)
            LEFT JOIN (
                SELECT m1.EmployeeID, m1.Status
                FROM master_data_dispatches m1
                INNER JOIN (
                    SELECT EmployeeID, MAX(DispatchID) AS LatestDispatchID
                    FROM master_data_dispatches GROUP BY EmployeeID
                ) latest ON latest.EmployeeID = m1.EmployeeID AND latest.LatestDispatchID = m1.DispatchID
            ) mdd_latest ON mdd_latest.EmployeeID = e.EmployeeID
            ORDER BY e.EmployeeID DESC";

        $result = $conn->query($sql);
        $employees = [];
        while ($row = $result->fetch_assoc()) {
            $employees[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $employees]);
        exit;
    }

    if ($action === 'get_employee_details') {
        $employeeId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $sql = "
            SELECT 
                e.*, ei.BaseSalary, ei.HiringDate, ei.WorkEmail, ei.EmploymentStatus,
                d.DepartmentName, p.PositionName, sg.GradeLevel, sg.MinSalary, sg.MaxSalary,
                bd.BankName, bd.AccountNumber AS BankAccountNumber, bd.AccountType,
                tb.TINNumber, tb.SSSNumber, tb.PhilHealthNumber, tb.PagIBIGNumber, tb.TaxStatus,
                ec.ContactName, ec.Relationship, ec.PhoneNumber AS EmergencyPhone
            FROM employee e
            LEFT JOIN (
                SELECT ei1.* FROM employmentinformation ei1
                INNER JOIN (SELECT EmployeeID, MAX(EmploymentID) AS LatestEmploymentID FROM employmentinformation GROUP BY EmployeeID) le ON le.EmployeeID = ei1.EmployeeID AND le.LatestEmploymentID = ei1.EmploymentID
            ) ei ON ei.EmployeeID = e.EmployeeID
            LEFT JOIN department d ON ei.DepartmentID = d.DepartmentID
            LEFT JOIN positions p ON ei.PositionID = p.PositionID
            LEFT JOIN salary_grades sg ON sg.SalaryGradeID = COALESCE(ei.SalaryGradeID, p.SalaryGradeID)
            LEFT JOIN bankdetails bd ON bd.EmployeeID = e.EmployeeID
            LEFT JOIN taxbenefits tb ON tb.EmployeeID = e.EmployeeID
            LEFT JOIN emergency_contacts ec ON ec.EmployeeID = e.EmployeeID AND ec.IsPrimary = 1
            WHERE e.EmployeeID = ? LIMIT 1";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $employeeId);
        $stmt->execute();
        $data = $stmt->get_result()->fetch_assoc();
        echo json_encode(['success' => true, 'data' => $data]);
        exit;
    }

    if ($action === 'dispatch_employees') {
        $decoded = json_decode($_POST['employee_ids'] ?? '[]', true);
        $dispatchedBy = resolveCurrentAccountId($conn);

        if (empty($decoded) || $dispatchedBy <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid request or session expired.']);
            exit;
        }

        $conn->begin_transaction();
        try {
            $inserted = 0;
            $updated = 0;
            $ignored = 0;

            foreach ($decoded as $empId) {
                $check = $conn->prepare("SELECT DispatchID, Status FROM master_data_dispatches WHERE EmployeeID = ? ORDER BY DispatchID DESC LIMIT 1");
                $check->bind_param("i", $empId);
                $check->execute();
                $res = $check->get_result()->fetch_assoc();

                if (!$res) {
                    $ins = $conn->prepare("INSERT INTO master_data_dispatches (EmployeeID, DispatchedByAccountID, Status) VALUES (?, ?, 'Pending')");
                    $ins->bind_param("ii", $empId, $dispatchedBy);
                    $ins->execute();
                    $inserted++;
                }
                else {
                    $status = strtolower($res['Status']);
                    if ($status === 'pending') {
                        $ignored++;
                    }
                    else {
                        $upd = $conn->prepare("UPDATE master_data_dispatches SET Status = 'Pending', DispatchedByAccountID = ?, DispatchedAt = CURRENT_TIMESTAMP WHERE DispatchID = ?");
                        $upd->bind_param("ii", $dispatchedBy, $res['DispatchID']);
                        $upd->execute();
                        $updated++;
                    }
                }
            }
            $conn->commit();
            echo json_encode(['success' => true, 'inserted_count' => $inserted, 'updated_count' => $updated, 'already_pending_count' => $ignored]);
        }
        catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

}
catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>