<?php
// modules/admin/backend/be_request_payroll_budget.php (HR Laptop)
header('Content-Type: application/json');
require_once '../../../config/config.php';

$response = ['success' => false, 'message' => ''];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method.');
    }

    $batchId = isset($_POST['batch_id']) ? (int)$_POST['batch_id'] : 0;
    $amount = isset($_POST['amount']) ? (float)$_POST['amount'] : 0;

    if ($batchId <= 0) {
        throw new Exception('Invalid Batch ID.');
    }
    if ($amount <= 0) {
        throw new Exception('Invalid budget amount.');
    }

    // 1. Fetch Batch Information
    $batchRes = $conn->query("SELECT batch_code, period_start, period_end, budget_finance_ref FROM payroll_batches WHERE id = $batchId");
    if (!$batchRes || !($batch = $batchRes->fetch_assoc())) {
        throw new Exception('Payroll batch not found.');
    }
    $financeRef = $batch['budget_finance_ref'] ?? '';

    // 2. Update HR Local Database
    $stmt = $conn->prepare("UPDATE payroll_batches SET budget_status = 'Pending', budget_requested_amount = ? WHERE id = ?");
    $stmt->bind_param("di", $amount, $batchId);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to update local HR database: ' . $conn->error);
    }
    $stmt->close();

    // 3. Prepare Payload for Finance Laptop
    // Special ID offset for Payroll: 1,000,000 + BatchID (Negative to signify Request)
    $specialId = -(1000000 + $batchId); 

    $payload = [
        'cycle_name' => 'PAYROLL DISBURSEMENT',
        'period_id' => 1, // Generic period for payroll
        'dept_code' => 'PAYROLL',
        'entity_name' => 'Payroll Batch ' . $batch['batch_code'], 
        'allocation_type' => 'Department',
        'total_budget' => $amount,
        'proposed_by_id' => 1, // System/Admin
        'hr_proposal_id' => $specialId, 
        'finance_ref' => $financeRef, // Tracking Reference if it exists
        'fiscal_year' => date('Y'),   
        'items' => [] 
    ];

    // 4. Send via cURL to Finance Laptop
    $financeUrl = 'http://10.112.107.207/microfinancee/modules/disbursement/receive_payroll_request.php';
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
        throw new Exception('Could not reach Finance Laptop (10.112.107.207): ' . $error);
    }

    $decoded = json_decode($res, true);
    if (!is_array($decoded) || !isset($decoded['success'])) {
        error_log("Finance Response (HTTP $httpCode): " . $res);
        throw new Exception('Finance server returned invalid response (HTTP ' . $httpCode . '): ' . substr($res, 0, 100));
    }

    if ($decoded['success']) {
        $response['ok'] = true;
        $response['message'] = 'Budget request for ' . $batch['batch_code'] . ' sent successfully!';
        
        // Save the Finance Tracking Reference
        if (isset($decoded['finance_ref'])) {
            $ref = $decoded['finance_ref'];
            $updateRef = $conn->prepare("UPDATE payroll_batches SET budget_finance_ref = ? WHERE id = ?");
            $updateRef->bind_param("si", $ref, $batchId);
            $updateRef->execute();
            $updateRef->close();
        }
    } else {
        $response['message'] = 'Finance server rejected request: ' . ($decoded['message'] ?? 'Unknown error');
    }

} catch (Exception $e) {
    $response['ok'] = false;
    $response['message'] = $e->getMessage();
}

$response['ok'] = $response['ok'] ?? false;
echo json_encode($response);
