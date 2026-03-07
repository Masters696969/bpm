<?php
session_start();
require_once '../../config/config.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cycleName = isset($_POST['cycle_name']) ? trim($_POST['cycle_name']) : '';
    $periodId = isset($_POST['period_id']) ? (int)$_POST['period_id'] : 0;
    $budgetUsedPct = isset($_POST['budget_used']) ? (float)$_POST['budget_used'] : 0.00;
    $totalBudget = isset($_POST['total_budget']) ? (float)$_POST['total_budget'] : 0.00;
    $totalCost = isset($_POST['total_cost']) ? (float)$_POST['total_cost'] : 0.00;
    $employeeDataStr = isset($_POST['employee_data']) ? $_POST['employee_data'] : '';

    if (empty($cycleName) || $periodId === 0 || empty($employeeDataStr)) {
        $response['message'] = 'Missing required fields.';
    } else {
        // Validate JSON
        $jsonData = json_decode($employeeDataStr);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $response['message'] = 'Invalid Employee Data format.';
        } else {
            // Check if draft for this cycle already exists
            $check_stmt = $conn->prepare("SELECT DraftID FROM simulation_drafts WHERE CycleName = ?");
            $check_stmt->bind_param("s", $cycleName);
            $check_stmt->execute();
            $result = $check_stmt->get_result();

            if ($result->num_rows > 0) {
                // Update existing draft
                $row = $result->fetch_assoc();
                $draftId = $row['DraftID'];
                $update_stmt = $conn->prepare("UPDATE simulation_drafts SET EmployeeData = ?, BudgetUsedPct = ?, TotalBudget = ?, TotalCost = ? WHERE DraftID = ?");
                $update_stmt->bind_param("sdddi", $employeeDataStr, $budgetUsedPct, $totalBudget, $totalCost, $draftId);
                
                if ($update_stmt->execute()) {
                    $response['success'] = true;
                    $response['message'] = 'Draft updated successfully.';
                } else {
                    $response['message'] = 'Failed to update draft: ' . $conn->error;
                }
                $update_stmt->close();
            } else {
                // Insert new draft
                $insert_stmt = $conn->prepare("INSERT INTO simulation_drafts (CycleName, period_id, BudgetUsedPct, TotalBudget, TotalCost, EmployeeData) VALUES (?, ?, ?, ?, ?, ?)");
                $insert_stmt->bind_param("siddds", $cycleName, $periodId, $budgetUsedPct, $totalBudget, $totalCost, $employeeDataStr);
                
                if ($insert_stmt->execute()) {
                    $response['success'] = true;
                    $response['message'] = 'Draft saved successfully.';
                } else {
                    $response['message'] = 'Failed to save draft: ' . $conn->error;
                }
                $insert_stmt->close();
            }
            $check_stmt->close();
        }
    }
} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
?>
