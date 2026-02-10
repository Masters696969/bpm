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
    if ($action === 'fetch_employees') {
        // Fetch employees with their position, department, and salary grade
        $sql = "SELECT 
                    e.EmployeeID, 
                    e.FirstName, 
                    e.LastName, 
                    ei.EmploymentStatus, 
                    d.DepartmentName, 
                    p.PositionName,
                    sg.GradeLevel
                FROM employee e
                LEFT JOIN employmentinformation ei ON e.EmployeeID = ei.EmployeeID
                LEFT JOIN department d ON ei.DepartmentID = d.DepartmentID
                LEFT JOIN positions p ON ei.PositionID = p.PositionID
                LEFT JOIN salary_grades sg ON p.SalaryGradeID = sg.SalaryGradeID
                ORDER BY e.LastName ASC";
        
        $result = $conn->query($sql);
        
        $employees = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $employees[] = $row;
            }
        }
        
        echo json_encode(['success' => true, 'data' => $employees]);
        exit;
    } elseif ($action === 'get_employee_details') {
        $employeeId = $_GET['id'] ?? 0;
        
        if (!$employeeId) {
            echo json_encode(['success' => false, 'message' => 'Invalid Employee ID']);
            exit;
        }

        // Fetch all details
        $sql = "SELECT 
                    e.*,
                    ei.*,
                    d.DepartmentName,
                    p.PositionName,
                    sg.GradeLevel, sg.MinSalary, sg.MaxSalary,
                    bd.BankName, bd.AccountNumber as BankAccountNumber, bd.AccountType,
                    tb.TINNumber, tb.SSSNumber, tb.PhilHealthNumber, tb.PagIBIGNumber, tb.TaxStatus
                FROM employee e
                LEFT JOIN employmentinformation ei ON e.EmployeeID = ei.EmployeeID
                LEFT JOIN department d ON ei.DepartmentID = d.DepartmentID
                LEFT JOIN positions p ON ei.PositionID = p.PositionID
                LEFT JOIN salary_grades sg ON p.SalaryGradeID = sg.SalaryGradeID
                LEFT JOIN bankdetails bd ON e.EmployeeID = bd.EmployeeID
                LEFT JOIN taxbenefits tb ON e.EmployeeID = tb.EmployeeID
                WHERE e.EmployeeID = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $employeeId);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        
        if ($data) {
            echo json_encode(['success' => true, 'data' => $data]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Employee not found']);
        }
        exit;
    } elseif ($action === 'update_employee') {
        // Collect POST data
        $employeeId = $_POST['EmployeeID'] ?? 0;
        $firstName = $_POST['FirstName'] ?? '';
        $lastName = $_POST['LastName'] ?? '';
        $middleName = $_POST['MiddleName'] ?? '';
        $dob = $_POST['DateOfBirth'] ?? null;
        $gender = $_POST['Gender'] ?? '';
        $phone = $_POST['PhoneNumber'] ?? '';
        $personalEmail = $_POST['PersonalEmail'] ?? '';
        $address = $_POST['PermanentAddress'] ?? '';
        
        $hiringDate = $_POST['HiringDate'] ?? null;
        $workEmail = $_POST['WorkEmail'] ?? '';
        $empStatus = $_POST['EmploymentStatus'] ?? '';
        
        $tin = $_POST['TINNumber'] ?? '';
        $sss = $_POST['SSSNumber'] ?? '';
        $philhealth = $_POST['PhilHealthNumber'] ?? '';
        $pagibig = $_POST['PagIBIGNumber'] ?? '';
        
        // Start Transaction
        $conn->begin_transaction();
        try {
            // Update Employee Table
            $stmt = $conn->prepare("UPDATE employee SET FirstName=?, LastName=?, MiddleName=?, DateOfBirth=?, Gender=?, PhoneNumber=?, PersonalEmail=?, PermanentAddress=? WHERE EmployeeID=?");
            $stmt->bind_param("ssssssssi", $firstName, $lastName, $middleName, $dob, $gender, $phone, $personalEmail, $address, $employeeId);
            $stmt->execute();
            
            // Update Employment Info
            $stmt = $conn->prepare("UPDATE employmentinformation SET HiringDate=?, WorkEmail=?, EmploymentStatus=? WHERE EmployeeID=?");
            $stmt->bind_param("sssi", $hiringDate, $workEmail, $empStatus, $employeeId);
            $stmt->execute();
            
            // Check if tax benefits exist, if not insert, else update
            $check = $conn->query("SELECT 1 FROM taxbenefits WHERE EmployeeID = $employeeId");
            if ($check && $check->num_rows > 0) {
                 $stmt = $conn->prepare("UPDATE taxbenefits SET TINNumber=?, SSSNumber=?, PhilHealthNumber=?, PagIBIGNumber=? WHERE EmployeeID=?");
                 $stmt->bind_param("ssssi", $tin, $sss, $philhealth, $pagibig, $employeeId);
                 $stmt->execute();
            } else {
                 $stmt = $conn->prepare("INSERT INTO taxbenefits (EmployeeID, TINNumber, SSSNumber, PhilHealthNumber, PagIBIGNumber) VALUES (?, ?, ?, ?, ?)");
                 $stmt->bind_param("issss", $employeeId, $tin, $sss, $philhealth, $pagibig);
                 $stmt->execute();
            }

            $conn->commit();
            echo json_encode(['success' => true, 'message' => 'Employee updated successfully']);
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
        exit;
    }
    

    elseif ($action === 'get_my_details') {
        $accountId = $_SESSION['user_id'];
        
        $stmt = $conn->prepare("SELECT EmployeeID FROM useraccounts WHERE AccountID = ?");
        $stmt->bind_param("i", $accountId);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        if (!$user || !$user['EmployeeID']) {
             echo json_encode(['success' => false, 'message' => 'No employee record linked to this account.']);
             exit;
        }
        
        $employeeId = $user['EmployeeID'];

        $sql = "SELECT 
                    e.*,
                    ei.*,
                    d.DepartmentName,
                    p.PositionName,
                    sg.GradeLevel, sg.MinSalary, sg.MaxSalary,
                    bd.BankName, bd.AccountNumber as BankAccountNumber, bd.AccountType,
                    tb.TINNumber, tb.SSSNumber, tb.PhilHealthNumber, tb.PagIBIGNumber, tb.TaxStatus
                FROM employee e
                LEFT JOIN employmentinformation ei ON e.EmployeeID = ei.EmployeeID
                LEFT JOIN department d ON ei.DepartmentID = d.DepartmentID
                LEFT JOIN positions p ON ei.PositionID = p.PositionID
                LEFT JOIN salary_grades sg ON p.SalaryGradeID = sg.SalaryGradeID
                LEFT JOIN bankdetails bd ON e.EmployeeID = bd.EmployeeID
                LEFT JOIN taxbenefits tb ON e.EmployeeID = tb.EmployeeID
                WHERE e.EmployeeID = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $employeeId);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        
        if ($data) {
            echo json_encode(['success' => true, 'data' => $data]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Employee details not found']);
        }
        exit;

    } elseif ($action === 'update_my_details') {
        $accountId = $_SESSION['user_id'];
        
        $stmt = $conn->prepare("SELECT EmployeeID FROM useraccounts WHERE AccountID = ?");
        $stmt->bind_param("i", $accountId);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        if (!$user || !$user['EmployeeID']) {
             echo json_encode(['success' => false, 'message' => 'No employee record linked to this account.']);
             exit;
        }
        
        $employeeId = $user['EmployeeID'];
        
        $firstName = $_POST['FirstName'] ?? '';
        $lastName = $_POST['LastName'] ?? '';
        $middleName = $_POST['MiddleName'] ?? '';
        $dob = $_POST['DateOfBirth'] ?? null;
        $gender = $_POST['Gender'] ?? '';
        $phone = $_POST['PhoneNumber'] ?? '';
        $personalEmail = $_POST['PersonalEmail'] ?? '';
        $address = $_POST['PermanentAddress'] ?? '';
        
        if (empty($firstName) || empty($lastName)) {
             echo json_encode(['success' => false, 'message' => 'Name fields are required.']);
             exit;
        }

        $stmt = $conn->prepare("UPDATE employee SET FirstName=?, LastName=?, MiddleName=?, DateOfBirth=?, Gender=?, PhoneNumber=?, PersonalEmail=?, PermanentAddress=? WHERE EmployeeID=?");
        $stmt->bind_param("ssssssssi", $firstName, $lastName, $middleName, $dob, $gender, $phone, $personalEmail, $address, $employeeId);
        
        if ($stmt->execute()) {
             echo json_encode(['success' => true, 'message' => 'Your information has been updated successfully.']);
        } else {
             echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
        }
        exit;
    }
    
    elseif ($action === 'request_update') {
        $input = json_decode(file_get_contents('php://input'), true);
        
        // Basic validation
        if (!isset($input['BankName']) && !isset($input['TINNumber'])) { 
             echo json_encode(['success' => false, 'message' => 'Invalid data']);
             exit;
        }

        $userId = $_SESSION['user_id']; 
        $stmt = $conn->prepare("SELECT EmployeeID FROM useraccounts WHERE AccountID = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $res = $stmt->get_result();
        $empRow = $res->fetch_assoc();

        if (!$empRow || !$empRow['EmployeeID']) {
            echo json_encode(['success' => false, 'message' => 'Employee record not found']);
            exit;
        }
        
        $employeeId = $empRow['EmployeeID'];
        $requestData = json_encode($input);
        
        $stmt = $conn->prepare("INSERT INTO employee_update_requests (EmployeeID, RequestType, RequestData, Status, RequestDate) VALUES (?, 'Update Information', ?, 'Pending', NOW())");
        $stmt->bind_param("is", $employeeId, $requestData);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Request submitted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
        }
        exit;

    } elseif ($action === 'fetch_pending_requests') {
        $sql = "SELECT r.*, e.FirstName, e.LastName, d.DepartmentName 
                FROM employee_update_requests r
                JOIN employee e ON r.EmployeeID = e.EmployeeID
                LEFT JOIN employmentinformation ei ON e.EmployeeID = ei.EmployeeID
                LEFT JOIN department d ON ei.DepartmentID = d.DepartmentID
                WHERE r.Status = 'Pending'
                ORDER BY r.RequestDate DESC";
        
        $result = $conn->query($sql);
        $requests = [];
        while ($row = $result->fetch_assoc()) {
            $requests[] = $row;
        }
        
        echo json_encode(['success' => true, 'data' => $requests]);
        exit;

    } elseif ($action === 'approve_request') {
        $input = json_decode(file_get_contents('php://input'), true);
        $requestId = $input['request_id'] ?? null;
        $reviewerId = $_SESSION['user_id']; // Assuming reviewer is logged in

        if (!$requestId) {
            echo json_encode(['success' => false, 'message' => 'Request ID required']);
            exit;
        }

        $conn->begin_transaction();

        try {
            // 1. Get Request Data
            $stmt = $conn->prepare("SELECT EmployeeID, RequestData FROM employee_update_requests WHERE RequestID = ? AND Status = 'Pending'");
            $stmt->bind_param("i", $requestId);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($res->num_rows === 0) {
                 throw new Exception("Request not found or already processed.");
            }
            $request = $res->fetch_assoc();
            $employeeId = $request['EmployeeID'];
            $changes = json_decode($request['RequestData'], true);

            // 2. Apply Changes to respective tables
            // Mapping fields to tables/columns
            $updates = [
                'bankdetails' => [],
                'taxbenefits' => []
            ];

            if (isset($changes['BankName'])) $updates['bankdetails']['BankName'] = $changes['BankName'];
            if (isset($changes['BankAccountNumber'])) $updates['bankdetails']['AccountNumber'] = $changes['BankAccountNumber'];
            
            if (isset($changes['TINNumber'])) $updates['taxbenefits']['TINNumber'] = $changes['TINNumber'];
            if (isset($changes['SSSNumber'])) $updates['taxbenefits']['SSSNumber'] = $changes['SSSNumber'];
            if (isset($changes['PhilHealthNumber'])) $updates['taxbenefits']['PhilHealthNumber'] = $changes['PhilHealthNumber'];
            if (isset($changes['PagIBIGNumber'])) $updates['taxbenefits']['PagIBIGNumber'] = $changes['PagIBIGNumber'];

            // Execute Updates
            foreach ($updates as $table => $fields) {
                if (!empty($fields)) {
                    $setClause = [];
                    $types = "";
                    $values = [];
                    foreach ($fields as $col => $val) {
                        $setClause[] = "$col = ?";
                        $types .= "s";
                        $values[] = $val;
                    }
                    $sql = "UPDATE $table SET " . implode(", ", $setClause) . " WHERE EmployeeID = ?";
                    $types .= "i";
                    $values[] = $employeeId;
                    
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param($types, ...$values);
                    $stmt->execute();
                }
            }

            // 3. Update Request Status
            $stmt = $conn->prepare("UPDATE employee_update_requests SET Status = 'Approved', ReviewedBy = ?, ReviewDate = NOW() WHERE RequestID = ?");
            $stmt->bind_param("ii", $reviewerId, $requestId);
            $stmt->execute();

            $conn->commit();
            echo json_encode(['success' => true, 'message' => 'Request approved and data updated.']);

        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['success' => false, 'message' => 'Error approving request: ' . $e->getMessage()]);
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
            echo json_encode(['success' => false, 'message' => 'Database error.']);
        }
        exit;

    }

    echo json_encode(['success' => false, 'message' => 'Invalid action']);


} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
    