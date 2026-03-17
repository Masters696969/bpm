<?php
session_start();
require_once '../../../config/config.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cycleName = isset($_POST['cycle_name']) ? trim($_POST['cycle_name']) : '';
    $totalCost = isset($_POST['total_cost']) ? (float)$_POST['total_cost'] : 0.00;
    $employeeDataStr = isset($_POST['employee_data']) ? $_POST['employee_data'] : '';
    $salaryScaleDataStr = isset($_POST['salary_scale_data']) ? $_POST['salary_scale_data'] : '';

    if (empty($cycleName) || empty($employeeDataStr)) {
        $response['message'] = 'Missing required fields.';
    } else {
        // Status: 'Sent to Finance'
        
        // Notification for Finance
        $submittedBy = $_SESSION['username'] ?? 'User';
        $notifMsg = "New Compensation Proposal sent to Finance for cycle: $cycleName by $submittedBy. Awaiting review.";
        
        $conn->begin_transaction();
        try {


            // 2. Save/Update the simulation as 'Sent to Finance'
            $check_stmt = $conn->prepare("SELECT DraftID FROM simulation_drafts WHERE CycleName = ?");
            $check_stmt->bind_param("s", $cycleName);
            $check_stmt->execute();
            $result = $check_stmt->get_result();

            $userId = $_SESSION['user_id'] ?? 0;
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $draftId = $row['DraftID'];
                $update_stmt = $conn->prepare("UPDATE simulation_drafts SET EmployeeData = ?, SalaryScaleData = ?, TotalCost = ?, Status = 'Sent to Finance', ProposedBy = ?, LastSaved = NOW() WHERE DraftID = ?");
                $update_stmt->bind_param("ssdii", $employeeDataStr, $salaryScaleDataStr, $totalCost, $userId, $draftId);
                $update_stmt->execute();
            } else {
                $insert_stmt = $conn->prepare("INSERT INTO simulation_drafts (CycleName, period_id, TotalCost, EmployeeData, SalaryScaleData, Status, ProposedBy) VALUES (?, 1, ?, ?, ?, 'Sent to Finance', ?)");
                $insert_stmt->bind_param("sdssi", $cycleName, $totalCost, $employeeDataStr, $salaryScaleDataStr, $userId);
                $insert_stmt->execute();
            }

            // 3. Create System Notification for Finance
            $notifStmt = $conn->prepare("INSERT INTO system_notifications (module_target, message, role_target) VALUES ('compensation_finance', ?, 'finance')");
            $notifStmt->bind_param("s", $notifMsg);
            $notifStmt->execute();

            $conn->commit();
            $response['success'] = true;
            $response['message'] = 'Proposal submitted to Finance successfully.';
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
