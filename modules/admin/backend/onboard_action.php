<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../../config/config.php';
session_start();

if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Helper to get next ID for frontend display
if (isset($_GET['get_next_id'])) {
    $res = $conn->query("SELECT COUNT(EmployeeID) as emp_count FROM employee");
    $countRow = $res->fetch_assoc();
    $nextId = ($countRow['emp_count'] ?? 0) + 1;
    echo json_encode(['next_id' => str_pad($nextId, 4, '0', STR_PAD_LEFT)]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$applicantId = $_POST['applicant_id'] ?? null;
$positionId = $_POST['position_id'] ?? null;
$salaryGradeId = $_POST['salary_grade_id'] ?? null;
$hiringDate = $_POST['hiring_date'] ?? date('Y-m-d');
$employmentStatus = $_POST['employment_status'] ?? 'Probationary';
$salaryType = $_POST['salary_type'] ?? 'Monthly';

if (!$applicantId || !$positionId || !$salaryGradeId) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit();
}

$conn->begin_transaction();

try {
    // 1. Fetch Applicant Data
    $stmt = $conn->prepare("SELECT * FROM applicants WHERE ApplicantID = ?");
    $stmt->bind_param("i", $applicantId);
    $stmt->execute();
    $applicant = $stmt->get_result()->fetch_assoc();

    if (!$applicant) throw new Exception("Applicant not found");

    // 2. Fetch Position Code
    $posStmt = $conn->prepare("SELECT PositionName, PositionCode, DepartmentID FROM positions WHERE PositionID = ?");
    $posStmt->bind_param("i", $positionId);
    $posStmt->execute();
    $posData = $posStmt->get_result()->fetch_assoc();
    $rawPosName = $posData['PositionName'] ?? 'General Staff';
    $positionCode = $posData['PositionCode'] ?? 'EMP';
    $departmentId = $posData['DepartmentID'] ?? 1;

    // 3. Generate Employee Code (PositionCode + Year + ID)
    $res = $conn->query("SELECT COUNT(EmployeeID) as emp_count FROM employee");
    $countRow = $res->fetch_assoc();
    $nextIdNum = ($countRow['emp_count'] ?? 0) + 1;
    $year = date('Y', strtotime($hiringDate));
    $employeeCode = $positionCode . $year . str_pad($nextIdNum, 4, '0', STR_PAD_LEFT);

    // 4. Insert into employee table (Insert 1: Identity Creation)
    $stmt = $conn->prepare("INSERT INTO employee (EmployeeCode, FirstName, MiddleName, LastName, DateOfBirth, Gender, PersonalEmail, PhoneNumber, PermanentAddress, ProfilePhoto) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssss", 
        $employeeCode, 
        $applicant['FirstName'], 
        $applicant['MiddleName'], 
        $applicant['LastName'], 
        $applicant['DateOfBirth'], 
        $applicant['Gender'], 
        $applicant['Email'], 
        $applicant['Phone'], 
        $applicant['PermanentAddress'],
        $applicant['IDPicturePath']
    );
    $stmt->execute();
    $newEmployeeId = $conn->insert_id;

    // 5. Insert into emergency_contacts (Insert 2: Emergency Migration)
    if (!empty($applicant['EmergencyContactName'])) {
        $emergencyStmt = $conn->prepare("INSERT INTO emergency_contacts (EmployeeID, ContactName, Relationship, PhoneNumber, IsPrimary) VALUES (?, ?, ?, ?, 1)");
        $emergencyStmt->bind_param("isss", 
            $newEmployeeId, 
            $applicant['EmergencyContactName'], 
            $applicant['EmergencyRelationship'], 
            $applicant['EmergencyPhone']
        );
        $emergencyStmt->execute();
    }

    // 6. Fetch Base Salary from Salary Grade
    $gradeStmt = $conn->prepare("SELECT MinSalary FROM salary_grades WHERE SalaryGradeID = ?");
    $gradeStmt->bind_param("i", $salaryGradeId);
    $gradeStmt->execute();
    $gradeData = $gradeStmt->get_result()->fetch_assoc();
    $baseSalary = $gradeData['MinSalary'] ?? 0;

    // 7. Insert into employmentinformation (Insert 3: Contract Linking with Consolidated Documents)
    $allDocs = array_filter([
        $applicant['ResumePath'], 
        $applicant['GovIDPath'], 
        $applicant['ClearancePath'], 
        $applicant['TORPath']
    ]);
    $consolidatedResume = implode(',', $allDocs);

    $stmt = $conn->prepare("INSERT INTO employmentinformation 
        (EmployeeID, DepartmentID, PositionID, SalaryGradeID, BaseSalary, SalaryType, HiringDate, EmploymentStatus, DigitalResume, IDPicture) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->bind_param("iiiidsssss", 
        $newEmployeeId, 
        $departmentId, 
        $positionId, 
        $salaryGradeId, 
        $baseSalary, 
        $salaryType,
        $hiringDate, 
        $employmentStatus, 
        $consolidatedResume, 
        $applicant['IDPicturePath']
    );
    $stmt->execute();

    // 8. Fetch Evaluation for Email
    $evalStmt = $conn->prepare("SELECT AverageRating, Decision FROM interview_evaluations WHERE ApplicantID = ? ORDER BY CreatedAt DESC LIMIT 1");
    $evalStmt->bind_param("i", $applicantId);
    $evalStmt->execute();
    $evaluation = $evalStmt->get_result()->fetch_assoc();

    // Calculate Performance Scores
    $resumeScore = $applicant['ResumeScore'] ?? 0;
    $interviewScore = $evaluation['AverageRating'] ?? 0;
    $examScore = $applicant['ExamScore'] ?? 0;
    
    // Weighted Total Score (Formula from applicationmgt.php)
    // Resume(20%) + Interview(40% scaled 0-5 -> 0-40) + Exam(40% scaled 0-15 -> 0-40)
    $totalScore = ($resumeScore * 0.20) + ($interviewScore * 8) + ($examScore * 2.6667);

    $performanceData = [
        'ResumeScore' => $resumeScore,
        'InterviewScore' => $interviewScore,
        'ExamScore' => $examScore,
        'TotalScore' => round($totalScore, 2),
        'Decision' => $evaluation['Decision'] ?? 'Approved'
    ];

    // 8. Insert into useraccounts (Insert 4: Automatic Account Activation with Patterned Password)
    $username = strtolower($applicant['FirstName'] . '.' . $applicant['LastName']);
    
    // Generate Password Pattern: [PositionName]@12345
    $pClean = preg_replace('/[^A-Za-z0-9]/', '', ucwords(strtolower($rawPosName)));
    $plainPassword = $pClean . "@12345";
    $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

    $accountStmt = $conn->prepare("INSERT INTO useraccounts (EmployeeID, Username, Email, PasswordHash, IsVerified, AccountStatus) VALUES (?, ?, ?, ?, 1, 'Active')");
    $accountStmt->bind_param("isss", 
        $newEmployeeId, 
        $username, 
        $applicant['Email'], 
        $hashedPassword
    );
    $accountStmt->execute();
    $newAccountId = $conn->insert_id;

    // 9. Assign Default Role (Insert 5: Role Assignment)
    // We'll try to find a matching role or default to 'HR Staff' or similar if it's an admin test,
    // but typically we should map it based on position. For now, we'll assign 'Department Officer' (ID 17) 
    // or a role that matches the position name if found.
    $roleId = 17; // Default: Department Officer
    
    // Check if there's a role matching the position name
    $roleMatchStmt = $conn->prepare("SELECT RoleID FROM roles WHERE RoleName LIKE ? LIMIT 1");
    $searchTerm = "%$rawPosName%";
    $roleMatchStmt->bind_param("s", $searchTerm);
    $roleMatchStmt->execute();
    $roleMatch = $roleMatchStmt->get_result()->fetch_assoc();
    if ($roleMatch) {
        $roleId = $roleMatch['RoleID'];
    }

    $roleStmt = $conn->prepare("INSERT INTO useraccountroles (AccountID, RoleID) VALUES (?, ?)");
    $roleStmt->bind_param("ii", $newAccountId, $roleId);
    $roleStmt->execute();

    // 10. Update Applicant Status to 'Hired'
    $statusStmt = $conn->prepare("UPDATE applicants SET Status = 'Accepted', ApprovalStatus = 'Hired' WHERE ApplicantID = ?");
    $statusStmt->bind_param("i", $applicantId);
    $statusStmt->execute();

    // 11. Send Hiring Email
    $emailSent = sendHiringEmail(
        $applicant['Email'], 
        $applicant['FirstName'] . ' ' . $applicant['LastName'],
        $rawPosName,
        $hiringDate,
        $username,
        $plainPassword,
        $evaluation
    );

    $conn->commit();
    
    $emailMsg = $emailSent ? "\nHiring email sent to candidate." : "\nNote: Hiring email could not be sent (check mail config).";
    echo json_encode(['success' => true, 'message' => "Onboarding successful! The employee record has been created.$emailMsg"]);

} catch (Exception $e) {
    if ($conn) $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
