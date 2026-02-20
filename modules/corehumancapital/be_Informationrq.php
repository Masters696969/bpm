<?php
require_once '../../config/config.php';

header('Content-Type: application/json');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check authentication
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    if ($action === 'fetch_pending_requests') {
        $sql = "SELECT 
                    r.RequestID, r.EmployeeID, r.RequestType, r.RequestDate, r.Status, r.RequestData,
                    e.FirstName, e.LastName, d.DepartmentName
                FROM employee_update_requests r
                JOIN employee e ON r.EmployeeID = e.EmployeeID
                LEFT JOIN employmentinformation ei ON e.EmployeeID = ei.EmployeeID
                LEFT JOIN department d ON ei.DepartmentID = d.DepartmentID
                WHERE r.Status = 'Pending'
                ORDER BY r.RequestDate DESC";
        
        $result = $conn->query($sql);
        
        $requests = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $requests[] = $row;
            }
        }
        
        echo json_encode(['success' => true, 'data' => $requests]);
        exit;
    } elseif ($action === 'approve_request') {
        $input = json_decode(file_get_contents('php://input'), true);
        $requestId = $input['request_id'] ?? null;
        $reviewerId = $_SESSION['user_id']; 

        if (!$requestId) {
            echo json_encode(['success' => false, 'message' => 'Request ID required']);
            exit;
        }

        $conn->begin_transaction();

        try {
            // 1. Fetch Request Data
            $stmt = $conn->prepare("SELECT EmployeeID, RequestData FROM employee_update_requests WHERE RequestID = ?");
            $stmt->bind_param("i", $requestId);
            $stmt->execute();
            $res = $stmt->get_result();
            $request = $res->fetch_assoc();

            if (!$request) {
                throw new Exception("Request not found");
            }

            $employeeId = $request['EmployeeID'];
            $changes = json_decode($request['RequestData'], true);

            $updates = [
                'bankdetails' => [],
                'taxbenefits' => []
            ];

            // Map flat JSON keys to table columns
            // Simple mapping logic based on key names
            foreach ($changes as $key => $value) {
                if (in_array($key, ['BankName', 'AccountNumber', 'AccountType'])) {
                    // Map AccountNumber to DB column if different, assume AccountNumber -> AccountNumber
                    // Check if DB column is AccountNumber or BankAccountNumber. In get details it was AccountNumber as BankAccountNumber
                    $dbKey = ($key === 'BankAccountNumber' || $key === 'AccountNumber') ? 'AccountNumber' : $key;
                    $updates['bankdetails'][$dbKey] = $value;
                } elseif (in_array($key, ['TINNumber', 'SSSNumber', 'PhilHealthNumber', 'PagIBIGNumber', 'TaxStatus'])) {
                    $updates['taxbenefits'][$key] = $value;
                } else {
                    // Employee or Employment Info?
                    // This part depends on which table user can edit. 
                    // Previous edits were mostly personal info
                    // Assuming basic Employee table updates for now if names match
                    // or we ignore if we don't know the table.
                    // For now, let's assume valid column names for employee/employment tables are passed or handle specific known ones.
                    
                    // Actually, let's check what 'request_update' sends.
                    // It sends fields like 'FirstName', 'LastName', 'Address', etc.
                    // We need to know which table they belong to.
                    // For simplicity, let's try to update 'employee' table for common fields
                    $empFields = ['FirstName', 'LastName', 'MiddleName', 'DateOfBirth', 'Gender', 'PhoneNumber', 'PersonalEmail', 'PermanentAddress', 'CivilStatus'];
                    
                    if (in_array($key, $empFields)) {
                       $stmtUpdate = $conn->prepare("UPDATE employee SET $key = ? WHERE EmployeeID = ?");
                       $stmtUpdate->bind_param("si", $value, $employeeId);
                       $stmtUpdate->execute();
                    }
                }
            }
            
            // Apply grouped updates
            if (!empty($updates['bankdetails'])) {
                $setParts = [];
                $params = [];
                $types = "";
                foreach ($updates['bankdetails'] as $col => $val) {
                    $setParts[] = "$col = ?";
                    $params[] = $val;
                    $types .= "s";
                }
                if (!empty($setParts)) {
                     // Check if record exists
                    $check = $conn->query("SELECT 1 FROM bankdetails WHERE EmployeeID = $employeeId");
                    if ($check->num_rows > 0) {
                        $sql = "UPDATE bankdetails SET " . implode(', ', $setParts) . " WHERE EmployeeID = ?";
                        $params[] = $employeeId;
                        $types .= "i";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param($types, ...$params);
                        $stmt->execute();
                    } else {
                         // Insert - requires all mandatory fields or we update what we have? 
                         // Usually updates happen on existing. If new, we might need INSERT.
                         // For now, let's stick to update as per logic.
                    }
                }
            }

             if (!empty($updates['taxbenefits'])) {
                $setParts = [];
                $params = [];
                $types = "";
                foreach ($updates['taxbenefits'] as $col => $val) {
                    $setParts[] = "$col = ?";
                    $params[] = $val;
                    $types .= "s";
                }
                if (!empty($setParts)) {
                    $check = $conn->query("SELECT 1 FROM taxbenefits WHERE EmployeeID = $employeeId");
                    if ($check->num_rows > 0) {
                         $sql = "UPDATE taxbenefits SET " . implode(', ', $setParts) . " WHERE EmployeeID = ?";
                         $params[] = $employeeId;
                         $types .= "i";
                         $stmt = $conn->prepare($sql);
                         $stmt->bind_param($types, ...$params);
                         $stmt->execute();
                    }
                }
            }

            // Update Request Status
            $stmt = $conn->prepare("UPDATE employee_update_requests SET Status = 'Approved', ReviewedBy = ?, ReviewDate = NOW() WHERE RequestID = ?");
            $stmt->bind_param("ii", $reviewerId, $requestId);
            $stmt->execute();

            $conn->commit();
            echo json_encode(['success' => true, 'message' => 'Request approved and changes applied.']);

        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;

    } elseif ($action === 'reject_request') {
        $input = json_decode(file_get_contents('php://input'), true);
        $requestId = $input['request_id'] ?? null;
        $reviewerId = $_SESSION['user_id'];

        if (!$requestId) {
            echo json_encode(['success' => false, 'message' => 'Request ID required']);
            exit;
        }

        $stmt = $conn->prepare("UPDATE employee_update_requests SET Status = 'Rejected', ReviewedBy = ?, ReviewDate = NOW() WHERE RequestID = ?");
        $stmt->bind_param("ii", $reviewerId, $requestId);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Request rejected.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
        exit;
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
