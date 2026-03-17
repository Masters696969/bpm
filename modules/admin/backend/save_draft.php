<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ob_start(); // Buffer all output to ensure only JSON is sent
header('Content-Type: application/json');

session_start();
$response = ['success' => false, 'message' => ''];

try {
    require_once '../../../config/config.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $cycleName = isset($_POST['cycle_name']) ? trim($_POST['cycle_name']) : '';
        $periodId = isset($_POST['period_id']) ? (int)$_POST['period_id'] : 0;
        $budgetUsedPct = isset($_POST['budget_used']) ? (float)$_POST['budget_used'] : 0.00;
        $totalBudget = isset($_POST['total_budget']) ? (float)$_POST['total_budget'] : 0.00;
        $totalCost = isset($_POST['total_cost']) ? (float)$_POST['total_cost'] : 0.00;
        $employeeDataStr = isset($_POST['employee_data']) ? $_POST['employee_data'] : '';
        $salaryScaleData = isset($_POST['salary_scale_data']) ? $_POST['salary_scale_data'] : '';

        if (empty($cycleName) || $periodId === 0 || empty($employeeDataStr)) {
            $response['message'] = 'Missing required fields.';
        } else {
            $userId = $_SESSION['user_id'] ?? 0;
            


            // 2. Check if DraftID exists for this cycle
            $check_stmt = $conn->prepare("SELECT DraftID FROM simulation_drafts WHERE CycleName = ?");
            if (!$check_stmt) throw new Exception('Prepare failed (Check): ' . $conn->error);
            
            $check_stmt->bind_param("s", $cycleName);
            $check_stmt->execute();
            $result = $check_stmt->get_result();

            if ($result && $result->num_rows > 0) {
                // Update existing draft
                $row = $result->fetch_assoc();
                $draftId = $row['DraftID'];
                $update_stmt = $conn->prepare("UPDATE simulation_drafts SET EmployeeData = ?, SalaryScaleData = ?, BudgetUsedPct = ?, TotalBudget = ?, TotalCost = ?, Status = 'Draft', ProposedBy = ?, LastSaved = NOW() WHERE DraftID = ?");
                if (!$update_stmt) throw new Exception('Prepare failed (Update): ' . $conn->error);
                
                $update_stmt->bind_param("ssdddii", $employeeDataStr, $salaryScaleData, $budgetUsedPct, $totalBudget, $totalCost, $userId, $draftId);
                
                if ($update_stmt->execute()) {
                    $response['success'] = true;
                    $response['message'] = "Draft '$cycleName' updated successfully.";
                } else {
                    $response['message'] = 'Database update error: ' . $update_stmt->error;
                }
                $update_stmt->close();
            } else {
                // Insert new draft
                $insert_stmt = $conn->prepare("INSERT INTO simulation_drafts (CycleName, period_id, BudgetUsedPct, TotalBudget, TotalCost, EmployeeData, SalaryScaleData, Status, ProposedBy, LastSaved) VALUES (?, ?, ?, ?, ?, ?, ?, 'Draft', ?, NOW())");
                if (!$insert_stmt) throw new Exception('Prepare failed (Insert): ' . $conn->error);
                
                $insert_stmt->bind_param("sidddssi", $cycleName, $periodId, $budgetUsedPct, $totalBudget, $totalCost, $employeeDataStr, $salaryScaleData, $userId);
                
                if ($insert_stmt->execute()) {
                    $response['success'] = true;
                    $response['message'] = "Draft '$cycleName' saved successfully.";
                } else {
                    $response['message'] = 'Database insert error: ' . $insert_stmt->error;
                }
                $insert_stmt->close();
            }
            if ($check_stmt) $check_stmt->close();
        }
    } else {
        $response['message'] = 'Invalid request method.';
    }
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = 'System Error: ' . $e->getMessage();
}

ob_end_clean(); // Discard any output buffered (warnings, etc)
echo json_encode($response);
?>
