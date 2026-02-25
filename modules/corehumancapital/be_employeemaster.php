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
                    e.EmployeeCode,
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
                    e.EmployeeCode,
                    ei.*,
                    ei.BaseSalary as BaseSalary,
                    d.DepartmentName,
                    p.PositionName,
                    sg.GradeLevel, sg.MinSalary, sg.MaxSalary,
                    bd.BankName, bd.AccountNumber as BankAccountNumber, bd.AccountType,
                    tb.TINNumber, tb.SSSNumber, tb.PhilHealthNumber, tb.PagIBIGNumber, tb.TaxStatus,
                    ec.ContactName, ec.Relationship, ec.PhoneNumber as EmergencyPhone
                FROM employee e
                LEFT JOIN employmentinformation ei ON e.EmployeeID = ei.EmployeeID
                LEFT JOIN department d ON ei.DepartmentID = d.DepartmentID
                LEFT JOIN positions p ON ei.PositionID = p.PositionID
                LEFT JOIN salary_grades sg ON p.SalaryGradeID = sg.SalaryGradeID
                LEFT JOIN bankdetails bd ON e.EmployeeID = bd.EmployeeID
                LEFT JOIN taxbenefits tb ON e.EmployeeID = tb.EmployeeID
                LEFT JOIN emergency_contacts ec ON e.EmployeeID = ec.EmployeeID AND ec.IsPrimary = 1
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
        $baseSalary = $_POST['BaseSalary'] ?? 0;
        
        $tin = $_POST['TINNumber'] ?? '';
        $sss = $_POST['SSSNumber'] ?? '';
        $philhealth = $_POST['PhilHealthNumber'] ?? '';
        $pagibig = $_POST['PagIBIGNumber'] ?? '';
        $taxStatus = $_POST['TaxStatus'] ?? 'S'; // Default to Single

        $bankName = 'BDO'; // Forced to BDO
        $accountNumber = $_POST['BankAccountNumber'] ?? '';
        $accountType = 'Payroll'; // Forced to Payroll

        $contactName = $_POST['ContactName'] ?? '';
        $relationship = $_POST['Relationship'] ?? '';
        $emergencyPhone = $_POST['EmergencyPhone'] ?? '';

        $conn->begin_transaction();

        try {
            // Update Employee Table
            $sqlEmp = "UPDATE employee SET FirstName=?, LastName=?, MiddleName=?, DateOfBirth=?, Gender=?, PhoneNumber=?, PersonalEmail=?, PermanentAddress=? WHERE EmployeeID=?";
            $stmtEmp = $conn->prepare($sqlEmp);
            $stmtEmp->bind_param("ssssssssi", $firstName, $lastName, $middleName, $dob, $gender, $phone, $personalEmail, $address, $employeeId);
            $stmtEmp->execute();

            // Update Employment Information
            $sqlInfo = "UPDATE employmentinformation SET HiringDate=?, WorkEmail=?, EmploymentStatus=?, BaseSalary=? WHERE EmployeeID=?";
            $stmtInfo = $conn->prepare($sqlInfo);
            $stmtInfo->bind_param("sssdi", $hiringDate, $workEmail, $empStatus, $baseSalary, $employeeId);
            $stmtInfo->execute();

            // Update Tax Benefits
            $sqlCheckTax = "SELECT BenefitID FROM taxbenefits WHERE EmployeeID = ?";
            $stmtCheckTax = $conn->prepare($sqlCheckTax);
            $stmtCheckTax->bind_param("i", $employeeId);
            $stmtCheckTax->execute();
            if ($stmtCheckTax->get_result()->num_rows > 0) {
                $sqlTax = "UPDATE taxbenefits SET TINNumber=?, SSSNumber=?, PhilHealthNumber=?, PagIBIGNumber=?, TaxStatus=? WHERE EmployeeID=?";
                $stmtTax = $conn->prepare($sqlTax);
                $stmtTax->bind_param("sssssi", $tin, $sss, $philhealth, $pagibig, $taxStatus, $employeeId);
            } else {
                $sqlTax = "INSERT INTO taxbenefits (TINNumber, SSSNumber, PhilHealthNumber, PagIBIGNumber, TaxStatus, EmployeeID) VALUES (?, ?, ?, ?, ?, ?)";
                $stmtTax = $conn->prepare($sqlTax);
                $stmtTax->bind_param("sssssi", $tin, $sss, $philhealth, $pagibig, $taxStatus, $employeeId);
            }
            $stmtTax->execute();

            // Update Bank Details
            $sqlCheckBank = "SELECT BankDetailID FROM bankdetails WHERE EmployeeID = ?";
            $stmtCheckBank = $conn->prepare($sqlCheckBank);
            $stmtCheckBank->bind_param("i", $employeeId);
            $stmtCheckBank->execute();
            if ($stmtCheckBank->get_result()->num_rows > 0) {
                $sqlBank = "UPDATE bankdetails SET BankName=?, AccountNumber=?, AccountType=? WHERE EmployeeID=?";
                $stmtBank = $conn->prepare($sqlBank);
                $stmtBank->bind_param("sssi", $bankName, $accountNumber, $accountType, $employeeId);
            } else {
                $sqlBank = "INSERT INTO bankdetails (BankName, AccountNumber, AccountType, EmployeeID) VALUES (?, ?, ?, ?)";
                $stmtBank = $conn->prepare($sqlBank);
                $stmtBank->bind_param("sssi", $bankName, $accountNumber, $accountType, $employeeId);
            }
            $stmtBank->execute();

            // Update Emergency Contact
            $sqlCheckEC = "SELECT ContactID FROM emergency_contacts WHERE EmployeeID = ? AND IsPrimary = 1";
            $stmtCheckEC = $conn->prepare($sqlCheckEC);
            $stmtCheckEC->bind_param("i", $employeeId);
            $stmtCheckEC->execute();
            if ($stmtCheckEC->get_result()->num_rows > 0) {
                $sqlEC = "UPDATE emergency_contacts SET ContactName=?, Relationship=?, PhoneNumber=? WHERE EmployeeID=? AND IsPrimary = 1";
                $stmtEC = $conn->prepare($sqlEC);
                $stmtEC->bind_param("sssi", $contactName, $relationship, $emergencyPhone, $employeeId);
            } else {
                $sqlEC = "INSERT INTO emergency_contacts (ContactName, Relationship, PhoneNumber, EmployeeID, IsPrimary) VALUES (?, ?, ?, ?, 1)";
                $stmtEC = $conn->prepare($sqlEC);
                $stmtEC->bind_param("sssi", $contactName, $relationship, $emergencyPhone, $employeeId);
            }
            $stmtEC->execute();

            $conn->commit();
            echo json_encode(['success' => true, 'message' => 'Employee updated successfully']);
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
        exit;
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
