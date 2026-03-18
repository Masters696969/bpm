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
            $check_stmt = $conn->prepare("SELECT DraftID, TotalBudget FROM simulation_drafts WHERE CycleName = ?");
            $check_stmt->bind_param("s", $cycleName);
            $check_stmt->execute();
            $result = $check_stmt->get_result();

            $totalBudget = isset($_POST['total_budget']) ? (float)$_POST['total_budget'] : 5000000;
            $userId = $_SESSION['user_id'] ?? 0;
            
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $draftId = $row['DraftID'];
                $dbTotalBudget = (float)($row['TotalBudget'] > 0 ? $row['TotalBudget'] : $totalBudget);
                $budgetUsedPct = ($dbTotalBudget > 0) ? (($totalCost * 12) / $dbTotalBudget) * 100 : 0;
                
                $update_stmt = $conn->prepare("UPDATE simulation_drafts SET EmployeeData = ?, SalaryScaleData = ?, TotalCost = ?, TotalBudget = ?, BudgetUsedPct = ?, Status = 'Sent to Finance', ProposedBy = ?, LastSaved = NOW() WHERE DraftID = ?");
                $update_stmt->bind_param("ssdddii", $employeeDataStr, $salaryScaleDataStr, $totalCost, $dbTotalBudget, $budgetUsedPct, $userId, $draftId);
                $update_stmt->execute();
            } else {
                $budgetUsedPct = ($totalBudget > 0) ? (($totalCost * 12) / $totalBudget) * 100 : 0;
                $insert_stmt = $conn->prepare("INSERT INTO simulation_drafts (CycleName, period_id, TotalCost, TotalBudget, BudgetUsedPct, EmployeeData, SalaryScaleData, Status, ProposedBy) VALUES (?, 1, ?, ?, ?, ?, ?, 'Sent to Finance', ?)");
                $insert_stmt->bind_param("sdddssi", $cycleName, $totalCost, $totalBudget, $budgetUsedPct, $employeeDataStr, $salaryScaleDataStr, $userId);
                $insert_stmt->execute();
            }

            // 3. Create System Notification for Finance
            $notifStmt = $conn->prepare("INSERT INTO system_notifications (module_target, message, role_target) VALUES ('compensation_finance', ?, 'finance')");
            $notifStmt->bind_param("s", $notifMsg);
            $notifStmt->execute();

            $conn->commit();
            $response['ok'] = true;
            $response['message'] = 'Proposal submitted to Finance successfully.';
        } catch (Exception $e) {
            $conn->rollback();
            $response['ok'] = false;
            $response['message'] = 'Error: ' . $e->getMessage();
        }
    }
} else {
    $response['ok'] = false;
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
?>
