<?php
require_once '../../config/config.php';

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? $_POST['action'] ?? $_GET['action'] ?? '';

try {
    if ($action === 'fetch_endorsed') {
        $sql = "SELECT 
                    r.RequestID, r.EmployeeID, r.RequestType, r.RequestDate, r.Status, r.RequestData, r.ProofPath,
                    r.SupervisorID, r.SupervisorDate,
                    e.FirstName, e.LastName, d.DepartmentName,
                    s.FirstName as SupFirstName, s.LastName as SupLastName,
                    s_acct.FirstName as FirstName_Acct, s_acct.LastName as LastName_Acct,
                    ua.Username as SupUsername
                FROM employee_update_requests r
                JOIN employee e ON r.EmployeeID = e.EmployeeID
                LEFT JOIN employmentinformation ei ON e.EmployeeID = ei.EmployeeID
                LEFT JOIN department d ON ei.DepartmentID = d.DepartmentID
                -- Try joining directly to employee first (if SupervisorID is an EmployeeID)
                LEFT JOIN employee s ON r.SupervisorID = s.EmployeeID
                -- Fallback to useraccounts if SupervisorID is actually an AccountID
                LEFT JOIN useraccounts ua ON r.SupervisorID = ua.AccountID
                -- Then link the supervisor's employee record if we joined via AccountID
                LEFT JOIN employee s_acct ON ua.EmployeeID = s_acct.EmployeeID
                WHERE r.Status = 'Endorsed'
                ORDER BY r.SupervisorDate DESC";
        
        $result = $conn->query($sql);
        $requests = [];
        while ($row = $result->fetch_assoc()) {
            // Pick the correct name based on which join succeeded
            $row['SupFirstName'] = $row['SupFirstName'] ?? $row['FirstName_Acct'] ?? null;
            $row['SupLastName'] = $row['SupLastName'] ?? $row['LastName_Acct'] ?? null;
            $requests[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $requests]);
        exit;

    } elseif ($action === 'approve_request') {
        $input = json_decode(file_get_contents('php://input'), true);
        $requestId = $input['request_id'] ?? null;
        $managerId = $_SESSION['user_id'];

        if (!$requestId) {
            echo json_encode(['success' => false, 'message' => 'Request ID required']);
            exit;
        }

        $conn->begin_transaction();

        try {
            // 1. Fetch Request Data
            $stmt = $conn->prepare("SELECT EmployeeID, RequestData FROM employee_update_requests WHERE RequestID = ? AND Status = 'Endorsed'");
            $stmt->bind_param("i", $requestId);
            $stmt->execute();
            $res = $stmt->get_result();
            $request = $res->fetch_assoc();

            if (!$request) {
                throw new Exception("Request not found or not endorsed.");
            }

            $employeeId = $request['EmployeeID'];
            $changes = json_decode($request['RequestData'], true);

            $bankFields = [];
            $taxFields  = [];

            foreach ($changes as $key => $value) {
                if (in_array($key, ['BankName', 'BankAccountNumber', 'AccountType'])) {
                    $dbKey = ($key === 'BankAccountNumber') ? 'AccountNumber' : $key;
                    $bankFields[$dbKey] = $value;
                } elseif (in_array($key, ['TINNumber', 'SSSNumber', 'PhilHealthNumber', 'PagIBIGNumber', 'TaxStatus'])) {
                    $taxFields[$key] = $value;
                } elseif (in_array($key, ['FirstName', 'LastName', 'MiddleName', 'DateOfBirth', 'Gender', 'PhoneNumber', 'PersonalEmail', 'PermanentAddress'])) {
                    $stmtUpdate = $conn->prepare("UPDATE employee SET $key = ? WHERE EmployeeID = ?");
                    $stmtUpdate->bind_param("si", $value, $employeeId);
                    $stmtUpdate->execute();
                }
            }
            
            // Upsert bankdetails manually since ON DUPLICATE KEY might fail if multiple indices exist
            if (!empty($bankFields)) {
                $check = $conn->prepare("SELECT BankDetailID FROM bankdetails WHERE EmployeeID = ? LIMIT 1");
                $check->bind_param("i", $employeeId);
                $check->execute();
                $exists = $check->get_result()->fetch_assoc();

                if ($exists) {
                    $setClause = implode(', ', array_map(fn($k) => "`$k` = ?", array_keys($bankFields)));
                    $sql = "UPDATE bankdetails SET $setClause WHERE EmployeeID = ?";
                    $stmt = $conn->prepare($sql);
                    $types = str_repeat('s', count($bankFields)) . 'i';
                    $stmt->bind_param($types, ...array_merge(array_values($bankFields), [$employeeId]));
                    $stmt->execute();
                } else {
                    $cols = implode(', ', array_map(fn($k) => "`$k`", array_keys($bankFields)));
                    $placeholders = implode(', ', array_fill(0, count($bankFields), '?'));
                    $sql = "INSERT INTO bankdetails (EmployeeID, $cols) VALUES (?, $placeholders)";
                    $stmt = $conn->prepare($sql);
                    $types = 'i' . str_repeat('s', count($bankFields));
                    $stmt->bind_param($types, ...array_merge([$employeeId], array_values($bankFields)));
                    $stmt->execute();
                }
            }

            // Upsert taxbenefits manually
            if (!empty($taxFields)) {
                $check = $conn->prepare("SELECT BenefitID FROM taxbenefits WHERE EmployeeID = ? LIMIT 1");
                $check->bind_param("i", $employeeId);
                $check->execute();
                $exists = $check->get_result()->fetch_assoc();

                if ($exists) {
                    $setClause = implode(', ', array_map(fn($k) => "`$k` = ?", array_keys($taxFields)));
                    $sql = "UPDATE taxbenefits SET $setClause WHERE EmployeeID = ?";
                    $stmt = $conn->prepare($sql);
                    $types = str_repeat('s', count($taxFields)) . 'i';
                    $stmt->bind_param($types, ...array_merge(array_values($taxFields), [$employeeId]));
                    $stmt->execute();
                } else {
                    $cols = implode(', ', array_map(fn($k) => "`$k`", array_keys($taxFields)));
                    $placeholders = implode(', ', array_fill(0, count($taxFields), '?'));
                    $sql = "INSERT INTO taxbenefits (EmployeeID, $cols) VALUES (?, $placeholders)";
                    $stmt = $conn->prepare($sql);
                    $types = 'i' . str_repeat('s', count($taxFields));
                    $stmt->bind_param($types, ...array_merge([$employeeId], array_values($taxFields)));
                    $stmt->execute();
                }
            }

            // Update Request Status
            $stmt = $conn->prepare("UPDATE employee_update_requests SET Status = 'Approved', ReviewedBy = ?, ReviewDate = NOW() WHERE RequestID = ?");
            $stmt->bind_param("ii", $managerId, $requestId);
            $stmt->execute();

            $conn->commit();
            echo json_encode(['success' => true, 'message' => 'Request approved and records updated.']);

        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;

    } elseif ($action === 'reject_request') {
        $input = json_decode(file_get_contents('php://input'), true);
        $requestId = $input['request_id'] ?? null;
        $managerId = $_SESSION['user_id'];

        if (!$requestId) {
            echo json_encode(['success' => false, 'message' => 'Request ID required']);
            exit;
        }

        $stmt = $conn->prepare("UPDATE employee_update_requests SET Status = 'Rejected', ReviewedBy = ?, ReviewDate = NOW() WHERE RequestID = ?");
        $stmt->bind_param("ii", $managerId, $requestId);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Request rejected.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error.']);
        }
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'Invalid action']);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
