<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../../config/config.php';
session_start();

if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

// We need the EmployeeID from useraccounts.
$username = $_SESSION['username'];
$stmt = $conn->prepare("SELECT EmployeeID FROM useraccounts WHERE Username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$userResult = $stmt->get_result()->fetch_assoc();

if (!$userResult || !$userResult['EmployeeID']) {
    echo json_encode(['success' => false, 'message' => 'Employee profile not linked to this account.']);
    exit();
}
$employeeId = $userResult['EmployeeID'];


$action = $_GET['action'] ?? '';

if ($action === 'fetch_my_modules') {
    // Fetch employee's assigned training modules
    $query = "
        SELECT 
            et.AssignmentID,
            et.Status,
            et.AssignedDate,
            et.CompletedDate,
            tm.ModuleName,
            tm.Description
        FROM employee_training et
        JOIN training_modules tm ON et.ModuleID = tm.ModuleID
        WHERE et.EmployeeID = ?
        ORDER BY et.Status DESC, et.AssignedDate DESC
    ";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $employeeId);
    $stmt->execute();
    $result = $stmt->get_result();
    
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

echo json_encode(['success' => false, 'message' => 'Invalid action.']);
?>
