<?php
// modules/admin/backend/be_receive_budget_approval.php (HR Laptop)
header('Content-Type: application/json');
require_once '../../../config/config.php';

// Simple log function for debugging callbacks
function cb_log($msg) {
    $log = "[" . date('Y-m-d H:i:s') . "] " . $msg . "\n";
    file_put_contents('budget_callback.log', $log, FILE_APPEND);
}

try {
    // 1. Security Check (API Key)
    $expectedToken = 'HR_FINANCE_SECRET_2026'; 
    $headers = function_exists('getallheaders') ? getallheaders() : [];
    $token = $headers['X-API-KEY'] ?? $headers['x-api-key'] ?? '';

    if ($token !== $expectedToken) {
        cb_log("UNAUTHORIZED access attempt with token: $token");
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit();
    }

    // 2. Parse Payload from Finance
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);

    if (!$data) {
        cb_log("ERROR: Invalid JSON payload: " . $raw);
        echo json_encode(['success' => false, 'message' => 'Invalid JSON']);
        exit();
    }

    $periodId = (int)($data['period_id'] ?? 1);
    $approvedAmount = (float)($data['approved_amount'] ?? 0);
    $status = trim((string)($data['status'] ?? 'Approved'));
    $financeRef = trim((string)($data['finance_ref'] ?? ''));

    cb_log("RECEIVED APPROVAL: Period=$periodId, Amount=$approvedAmount, Ref=$financeRef");

    // 3. Update HR Database
    $stmt = $conn->prepare("UPDATE compensation_period SET budget_status = ?, budget_approved_amount = ?, budget_finance_ref = ?, budget_approved_at = NOW() WHERE period_id = ?");
    $stmt->bind_param("sdsi", $status, $approvedAmount, $financeRef, $periodId);
    
    if ($stmt->execute()) {
        cb_log("SUCCESS: HR Database updated for Period $periodId");
        echo json_encode(['success' => true, 'message' => 'HR Laptop updated successfully.']);
    } else {
        cb_log("ERROR: DB Update failed: " . $conn->error);
        echo json_encode(['success' => false, 'message' => 'Database update failed.']);
    }
    $stmt->close();

} catch (Exception $e) {
    cb_log("SYSTEM ERROR: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'System error: ' . $e->getMessage()]);
}
