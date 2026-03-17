<?php
// modules/admin/backend/be_request_budget.php (HR Laptop)
header('Content-Type: application/json');
require_once '../../../config/config.php';

$response = ['success' => false, 'message' => ''];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method.');
    }

    $amount = isset($_POST['amount']) ? (float)$_POST['amount'] : 0;
    $periodId = isset($_POST['period_id']) ? (int)$_POST['period_id'] : 1;

    if ($amount <= 0) {
        throw new Exception('Invalid budget amount.');
    }

    // 1. Update HR Local Database
    $stmt = $conn->prepare("UPDATE compensation_period SET budget_status = 'Pending', budget_requested_amount = ? WHERE period_id = ?");
    $stmt->bind_param("di", $amount, $periodId);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to update local HR database: ' . $conn->error);
    }
    $stmt->close();

    // 2. Prepare Payload for Finance Laptop
    // Critical: Using NEGATIVE ID to signify it's a Budget Request, not a Proposal sync
    $negativeId = -$periodId; 

    $payload = [
        'cycle_name' => 'HR COMPENSATION BUDGET REQUEST',
        'period_id' => $periodId,
        'dept_code' => 'HR COMPENSATION',
        'entity_name' => 'HR COMPENSATION', 
        'allocation_type' => 'Department',
        'total_budget' => $amount,
        'proposed_by_id' => 1, // System/Admin
        'hr_proposal_id' => $negativeId, 
        'fiscal_year' => date('Y'),   
        'items' => [] // No specific items for a general request
    ];

    // 3. Send via cURL to Finance Laptop
    $financeUrl = 'http://10.112.107.207/microfinancee/modules/budget/receive_hr_proposal.php';
    $apiKey = 'HR_FINANCE_SECRET_2026';

    $ch = curl_init($financeUrl);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'X-API-KEY: ' . $apiKey
        ],
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_TIMEOUT => 15,
        CURLOPT_CONNECTTIMEOUT => 5
    ]);

    $res = curl_exec($ch);
    $error = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($res === false) {
        throw new Exception('Could not reach Finance Laptop: ' . $error);
    }

    $decoded = json_decode($res, true);
    if (!is_array($decoded) || !isset($decoded['success'])) {
        throw new Exception('Finance server returned invalid response (HTTP ' . $httpCode . ')');
    }

    if ($decoded['success']) {
        $response['success'] = true;
        $response['message'] = 'Budget request sent successfully! Finance will now review it.';
    } else {
        $response['message'] = 'Finance server rejected request: ' . ($decoded['message'] ?? 'Unknown error');
    }

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
