<?php
session_start();
require_once '../../config/config.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $periodId = isset($_POST['period_id']) ? (int)$_POST['period_id'] : 1; // Defaulting to 1 for this phase, extend dynamically later
    $cycleName = isset($_POST['cycle_name']) ? trim($_POST['cycle_name']) : '';
    $startDate = isset($_POST['start_date']) ? $_POST['start_date'] : '';
    $effectiveDate = isset($_POST['effective_date']) ? $_POST['effective_date'] : '';
    $budgetAllocation = isset($_POST['budget_allocation']) ? (float)$_POST['budget_allocation'] : 0;

    if (empty($cycleName) || empty($startDate) || empty($effectiveDate)) {
        $response['message'] = 'Missing required configuration fields.';
    } else {
        $stmt = $conn->prepare("UPDATE compensation_period SET period_name = ?, start_date = ?, effective_date = ?, status = 'Active' WHERE period_id = ?");
        $stmt->bind_param("sssi", $cycleName, $startDate, $effectiveDate, $periodId);
        
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Cycle Configuration formalized and initiated successfully.';
        } else {
            $response['message'] = 'Database error: ' . $conn->error;
        }
        $stmt->close();
    }
} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
?>
