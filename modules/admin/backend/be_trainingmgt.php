<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../../config/config.php';
session_start();

if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

$action = $_GET['action'] ?? '';

// 1. Fetch all available training programs
if ($action === 'fetch_all_modules') {
    $query = "SELECT ModuleID, ModuleName, Description, file_path FROM training_modules ORDER BY ModuleID ASC";
    
    $result = $conn->query($query);
    $modules = [];
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $modules[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $modules]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    }
    exit();
}

// 2. Fetch all employees and their assignment status for a specific module
if ($action === 'fetch_employees_for_module') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
        exit();
    }

    $moduleId = $_POST['module_id'] ?? null;
    
    if (!$moduleId) {
        echo json_encode(['success' => false, 'message' => 'Module ID is required.']);
        exit();
    }

    $query = "
        SELECT 
            e.EmployeeID,
            e.EmployeeCode,
            e.FirstName,
            e.LastName,
            p.PositionName,
            d.DepartmentName,
            IF(et.AssignmentID IS NOT NULL, 1, 0) as IsAssigned,
            et.Status
        FROM employee e
        JOIN employmentinformation ei ON e.EmployeeID = ei.EmployeeID
        LEFT JOIN positions p ON ei.PositionID = p.PositionID
        LEFT JOIN department d ON ei.DepartmentID = d.DepartmentID
        LEFT JOIN employee_training et ON e.EmployeeID = et.EmployeeID AND et.ModuleID = ?
        WHERE ei.EmploymentStatus != 'Terminated' AND ei.EmploymentStatus != 'Resigned'
        ORDER BY ei.HiringDate DESC, e.EmployeeID DESC
    ";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $moduleId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $employees = [];
    while ($row = $result->fetch_assoc()) {
        $employees[] = $row;
    }
    
    echo json_encode(['success' => true, 'data' => $employees]);
    exit();
}

// 3. Save Multiple Employees to One Module
if ($action === 'save_module_assignments') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
        exit();
    }

    $moduleId = $_POST['module_id'] ?? null;
    $employeeIdsJson = $_POST['employee_ids'] ?? '[]';
    
    if (!$moduleId) {
        echo json_encode(['success' => false, 'message' => 'Module ID is required.']);
        exit();
    }

    $employeeIds = json_decode($employeeIdsJson, true);
    
    if (!is_array($employeeIds)) {
         echo json_encode(['success' => false, 'message' => 'Invalid employee data.']);
         exit();
    }

    $conn->begin_transaction();

    try {
        // Fetch existing assignments for THIS module
        $existingQuery = "SELECT EmployeeID, Status FROM employee_training WHERE ModuleID = ?";
        $existingStmt = $conn->prepare($existingQuery);
        $existingStmt->bind_param("i", $moduleId);
        $existingStmt->execute();
        $existingResult = $existingStmt->get_result();
        
        $existingEmployees = [];
        while ($row = $existingResult->fetch_assoc()) {
            $existingEmployees[$row['EmployeeID']] = $row['Status'];
        }

        // Delete assignments that were unchecked (ONLY if 'Pending')
        $deleteQuery = "DELETE FROM employee_training WHERE ModuleID = ? AND EmployeeID = ? AND Status = 'Pending'";
        $deleteStmt = $conn->prepare($deleteQuery);
        
        foreach ($existingEmployees as $empId => $status) {
            if (!in_array($empId, $employeeIds) && $status === 'Pending') {
                $deleteStmt->bind_param("ii", $moduleId, $empId);
                $deleteStmt->execute();
            }
        }

        // Insert new assignments
        $insertQuery = "INSERT INTO employee_training (ModuleID, EmployeeID, Status) VALUES (?, ?, 'Pending')";
        $insertStmt = $conn->prepare($insertQuery);
        
        foreach ($employeeIds as $empId) {
            // Only insert if it doesn't already exist
            if (!isset($existingEmployees[$empId])) {
                $insertStmt->bind_param("ii", $moduleId, $empId);
                $insertStmt->execute();
            }
        }

        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Assignments saved successfully.']);
        
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Failed to save assignments: ' . $e->getMessage()]);
    }
    
    exit();
}

// 4. Create New Module and Upload PDF
if ($action === 'create_module') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
        exit();
    }

    $moduleName = $_POST['ModuleName'] ?? '';
    $description = $_POST['Description'] ?? '';
    
    if (empty($moduleName) || empty($description)) {
        echo json_encode(['success' => false, 'message' => 'Title and Description are required.']);
        exit();
    }

    $filePath = null;

    // Handle File Upload if present
    if (isset($_FILES['training_file']) && $_FILES['training_file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../../../uploads/training/';
        
        // Ensure directory exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = basename($_FILES['training_file']['name']);
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        if ($fileExt !== 'pdf') {
            echo json_encode(['success' => false, 'message' => 'Only PDF files are allowed.']);
            exit();
        }

        // Generate a unique filename to prevent overwriting
        $newFileName = uniqid('training_') . '.pdf';
        $targetPath = $uploadDir . $newFileName;
        
        if (move_uploaded_file($_FILES['training_file']['tmp_name'], $targetPath)) {
            // Save relative URL path for DB
            $filePath = '../../uploads/training/' . $newFileName;
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to move uploaded file.']);
            exit();
        }
    }

    $query = "INSERT INTO training_modules (ModuleName, Description, file_path) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    
    // In case no file was uploaded, $filePath is null
    $stmt->bind_param("sss", $moduleName, $description, $filePath);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Module created successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    }
    
    exit();
}

// 5. Edit Existing Module and Upload PDF
if ($action === 'edit_module') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
        exit();
    }

    $moduleId = $_POST['ModuleID'] ?? '';
    $moduleName = $_POST['ModuleName'] ?? '';
    $description = $_POST['Description'] ?? '';
    
    if (empty($moduleId) || empty($moduleName) || empty($description)) {
        echo json_encode(['success' => false, 'message' => 'Module ID, Title, and Description are required.']);
        exit();
    }

    $filePath = null;

    // Handle File Upload if present
    if (isset($_FILES['training_file']) && $_FILES['training_file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../../../uploads/training/';
        
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = basename($_FILES['training_file']['name']);
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        if ($fileExt !== 'pdf') {
            echo json_encode(['success' => false, 'message' => 'Only PDF files are allowed.']);
            exit();
        }

        $newFileName = uniqid('training_') . '.pdf';
        $targetPath = $uploadDir . $newFileName;
        
        if (move_uploaded_file($_FILES['training_file']['tmp_name'], $targetPath)) {
            $filePath = '../../uploads/training/' . $newFileName;
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to move uploaded file.']);
            exit();
        }
    }

    if ($filePath !== null) {
        // Update everything including file_path
        $query = "UPDATE training_modules SET ModuleName = ?, Description = ?, file_path = ? WHERE ModuleID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssi", $moduleName, $description, $filePath, $moduleId);
    } else {
        // Update only name and description (preserve old file)
        $query = "UPDATE training_modules SET ModuleName = ?, Description = ? WHERE ModuleID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssi", $moduleName, $description, $moduleId);
    }
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Module updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    }
    
    exit();
}

echo json_encode(['success' => false, 'message' => 'Invalid action.']);
?>
