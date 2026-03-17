<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ob_start();
header('Content-Type: application/json');

session_start();
$response = ['success' => false, 'data' => null, 'message' => ''];

try {
    require_once '../../../config/config.php';

    if (isset($_GET['id'])) {
        $draftId = (int)$_GET['id'];
        
        $stmt = $conn->prepare("SELECT CycleName, EmployeeData, SalaryScaleData, BudgetUsedPct FROM simulation_drafts WHERE DraftID = ?");
        if (!$stmt) throw new Exception('Prepare failed: ' . $conn->error);
        
        $stmt->bind_param("i", $draftId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $response['success'] = true;
            $response['data'] = json_decode($row['EmployeeData'] ?? '[]', true);
            $response['salary_scale_data'] = json_decode($row['SalaryScaleData'] ?? '[]', true);
            $response['cycle_name'] = $row['CycleName'];
        } else {
            $response['message'] = 'Draft not found.';
        }
        
        if ($stmt) $stmt->close();
    } else {
        $response['message'] = 'No Draft ID provided.';
    }
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = 'System Error: ' . $e->getMessage();
}

ob_end_clean();
echo json_encode($response);
?>
