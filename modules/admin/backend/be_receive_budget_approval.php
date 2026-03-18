<?php
// modules/admin/backend/be_receive_budget_approval.php (HR Laptop)
header('Content-Type: application/json');
require_once '../../../config/config.php';

// Simple log function for debugging callbacks
function cb_log($msg) {
    $logFile = 'c:/xamppp/htdocs/microfinance-backup/modules/admin/backend/budget_callback.log';
    $log = "[" . date('Y-m-d H:i:s') . "] " . $msg . "\n";
    file_put_contents($logFile, $log, FILE_APPEND);
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
    
    // Normalize status for Payroll (Finance uses "Done" but HR uses "Approved" to trigger disbursement)
    if ($periodId > 1000000 && (strtolower($status) === 'done' || strtolower($status) === 'completed')) {
        $status = 'Approved'; 
    }

    $isFinal = ($status === 'Final' || $status === 'Closed' || $status === 'Approved' || ($data['is_final'] ?? false));

    cb_log("RECEIVED APPROVAL: Status=$status, IsFinal=" . ($isFinal ? 'YES' : 'NO') . " | Period=$periodId, Amount=$approvedAmount, Ref=$financeRef");

    // 3. Update HR Database (Status and Amount)
    if ($periodId > 1000000) {
        $batchId = $periodId - 1000000;
        cb_log("TARGETING PAYROLL BATCH: $batchId");
        
        $stmt = $conn->prepare("UPDATE payroll_batches SET 
                                    budget_status = ?, 
                                    budget_approved_amount = ?, 
                                    budget_finance_ref = ?, 
                                    budget_approved_at = NOW()
                                WHERE id = ?");
        $stmt->bind_param("sdsi", $status, $approvedAmount, $financeRef, $batchId);
        $stmt->execute();
        $stmt->close();
    } else {
        cb_log("TARGETING COMPENSATION PERIOD: $periodId");
        
        $stmt = $conn->prepare("UPDATE compensation_period SET budget_status = ?, budget_approved_amount = ?, budget_finance_ref = ?, budget_approved_at = NOW() WHERE period_id = ?");
        $stmt->bind_param("sdsi", $status, $approvedAmount, $financeRef, $periodId);
        $stmt->execute();
        $stmt->close();

        // 4. If FINAL, trigger salary updates (Only for Compensation Periods)
        if ($isFinal) {
            cb_log("TRIGGERING FINALIZATION: Applying salary changes for Period $periodId...");
            
            // Find the latest approved proposal for this period
            $propStmt = $conn->prepare("SELECT ProposalID, SalaryScaleData FROM simulation_proposals WHERE PeriodID = ? ORDER BY CreatedAt DESC LIMIT 1");
            $propStmt->bind_param("i", $periodId);
            $propStmt->execute();
            $propStmt->bind_result($proposalId, $salaryScaleJson);
            
            if ($propStmt->fetch()) {
                $propStmt->close();
                
                // Execute the same logic as be_finalize_cycle without a new request
                require_once 'be_finalize_cycle_logic.php';
                $finalResult = applyFinalSalaryChanges($conn, $proposalId, $periodId, $salaryScaleJson);
                
                if ($finalResult['success']) {
                    cb_log("FINALIZATION SUCCESS: Salaries and grades updated.");
                } else {
                    cb_log("FINALIZATION ERROR: " . $finalResult['message']);
                }
            } else {
                $propStmt->close();
                cb_log("FINALIZATION ERROR: No approved simulation proposal found for Period $periodId.");
            }
        }
    }

    echo json_encode(['ok' => true, 'message' => 'HR Laptop updated successfully.']);


} catch (Exception $e) {
    cb_log("SYSTEM ERROR: " . $e->getMessage());
    echo json_encode(['ok' => false, 'message' => 'System error: ' . $e->getMessage()]);
}
