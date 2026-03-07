<?php
session_start();
require_once '../../config/config.php';

header('Content-Type: application/json');

$response = ['success' => false, 'data' => null, 'message' => ''];

if (isset($_GET['id'])) {
    $draftId = (int)$_GET['id'];
    
    $stmt = $conn->prepare("SELECT CycleName, EmployeeData, BudgetUsedPct FROM simulation_drafts WHERE DraftID = ?");
    $stmt->bind_param("i", $draftId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $response['success'] = true;
        $response['data'] = json_decode($row['EmployeeData'], true);
        $response['cycle_name'] = $row['CycleName'];
    } else {
        $response['message'] = 'Draft not found.';
    }
    
    $stmt->close();
} else {
    $response['message'] = 'No Draft ID provided.';
}

echo json_encode($response);
?>
