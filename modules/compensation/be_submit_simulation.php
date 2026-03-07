<?php
session_start();
require_once '../../config/config.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cycleName = isset($_POST['cycle_name']) ? trim($_POST['cycle_name']) : '';
    $totalCost = isset($_POST['total_cost']) ? (float)$_POST['total_cost'] : 0.00;
    $employeeDataStr = isset($_POST['employee_data']) ? $_POST['employee_data'] : '';

    if (empty($cycleName) || empty($employeeDataStr)) {
        $response['message'] = 'Missing required fields.';
    } else {
        // We will save this as a "Submitted" draft for now, or use a specific proposals table if available.
        // For maximum compatibility with existing code, we'll ensure simulation_drafts can handle a 'status'
        
        // Let's create a notification for the manager
        $submittedBy = $_SESSION['username'] ?? 'User';
        $notifMsg = "New Compensation Proposal submitted for cycle: $cycleName by $submittedBy. Total Impact: ₱" . number_format($totalCost, 2);
        
        $conn->begin_transaction();
        try {
            // 1. Check if Status column exists, if not add it (Self-healing schema)
            $checkCol = $conn->query("SHOW COLUMNS FROM simulation_drafts LIKE 'Status'");
            if ($checkCol->num_rows == 0) {
                $conn->query("ALTER TABLE simulation_drafts ADD COLUMN Status VARCHAR(20) DEFAULT 'Draft'");
            }

            // 2. Save/Update the simulation as 'Submitted'
            $check_stmt = $conn->prepare("SELECT DraftID FROM simulation_drafts WHERE CycleName = ?");
            $check_stmt->bind_param("s", $cycleName);
            $check_stmt->execute();
            $result = $check_stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $draftId = $row['DraftID'];
                $update_stmt = $conn->prepare("UPDATE simulation_drafts SET EmployeeData = ?, TotalCost = ?, Status = 'Submitted', ProposedBy = ?, UpdatedAt = NOW() WHERE DraftID = ?");
                $userId = $_SESSION['user_id'] ?? 0;
                $update_stmt->bind_param("sdii", $employeeDataStr, $totalCost, $userId, $draftId);
                $update_stmt->execute();
            } else {
                $insert_stmt = $conn->prepare("INSERT INTO simulation_drafts (CycleName, period_id, TotalCost, EmployeeData, Status, ProposedBy) VALUES (?, 1, ?, ?, 'Submitted', ?)");
                $userId = $_SESSION['user_id'] ?? 0;
                $insert_stmt->bind_param("sdsi", $cycleName, $totalCost, $employeeDataStr, $userId);
                $insert_stmt->execute();
            }

            // 3. Create System Notification
            $notifStmt = $conn->prepare("INSERT INTO system_notifications (module_target, message, role_target) VALUES ('compensation_review', ?, 'hr manager')");
            $notifStmt->bind_param("s", $notifMsg);
            $notifStmt->execute();

            $conn->commit();
            $response['success'] = true;
            $response['message'] = 'Proposal submitted successfully.';
        } catch (Exception $e) {
            $conn->rollback();
            $response['message'] = 'Error: ' . $e->getMessage();
        }
    }
} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
?>
