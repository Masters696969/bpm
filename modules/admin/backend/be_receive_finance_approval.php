<?php
// modules/admin/backend/be_receive_finance_approval.php (HR LAPTOP)
header('Content-Type: application/json');

require_once '../../../config/config.php';

// Helper to log actions
function file_log($msg) {
    $log = "[" . date('Y-m-d H:i:s') . "] " . $msg . "\n";
    file_put_contents('debug_approval.log', $log, FILE_APPEND);
}

try {
    // 1. Security Check (Must match the key on Finance Laptop)
    $expectedToken = 'HR_FINANCE_SECRET_2026'; 
    $headers = function_exists('getallheaders') ? getallheaders() : [];
    $token = $headers['X-API-KEY'] ?? $headers['x-api-key'] ?? '';

    if ($token !== $expectedToken) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit();
    }

    // 2. Parse Payload from Finance
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);

    if (!$data) {
        file_log("ERROR: Invalid JSON payload received: " . $raw);
        echo json_encode(['success' => false, 'message' => 'Invalid JSON payload']);
        exit();
    }

    // --- DATA EXTRACTION ---
    $proposalId = (int)($data['hr_proposal_id'] ?? $data['proposal_id'] ?? 0);
    $periodId   = (int)($data['period_id'] ?? 0);
    $financeRef = trim((string)($data['finance_ref'] ?? ''));
    $newStatus  = trim((string)($data['status'] ?? 'Approved'));
    $approvedAmt = (float)($data['approved_amount'] ?? $data['total_budget'] ?? 0);
    $cycleNameFromFinance = trim((string)($data['cycle_name'] ?? ''));

    file_log("Processing: ID=$proposalId, Period=$periodId, Ref=$financeRef, Status=$newStatus. Data: " . json_encode($data));

    // --- PAYROLL BATCH SYNC ---
    // Detect if this is a payroll batch (ID range 1,000,000+)
    $absId = abs($proposalId);
    if ($absId >= 1000000 || strpos($financeRef, 'FALL-') === 0) {
        $batchId = ($absId >= 1000000) ? ($absId - 1000000) : $periodId;
        if ($batchId <= 0 && $proposalId > 0) $batchId = $proposalId; // Direct ID fallback

        file_log("DETECTED PAYROLL SYNC: BatchID=$batchId");
        
        $stmt = $conn->prepare("UPDATE payroll_batches SET 
                                    budget_status = ?, 
                                    budget_approved_amount = ?, 
                                    budget_finance_ref = ?, 
                                    budget_approved_at = NOW()
                                WHERE id = ? OR budget_finance_ref = ?");
        $stmt->bind_param("sdsis", $newStatus, $approvedAmt, $financeRef, $batchId, $financeRef);
        $stmt->execute();
        $stmt->close();

        echo json_encode(['success' => true, 'message' => 'Payroll budget status updated successfully.']);
        exit;
    }

    // --- SIMULATION PROPOSAL MATCH ---
    $targetProposalId = 0;
    $scaleDataJson = ''; 

    if ($proposalId > 0) {
        $check = $conn->prepare("SELECT ProposalID, CycleName, SalaryScaleData FROM simulation_proposals WHERE ProposalID = ?");
        $check->bind_param("i", $proposalId);
        $check->execute();
        $res = $check->get_result();
        if ($row = $res->fetch_assoc()) {
            $targetProposalId = $row['ProposalID'];
            $scaleDataJson = $row['SalaryScaleData'];
        }
        $check->close();
    }

    // Fallback: If ID is wrong or not provided, but Cycle Name is provided, use Cycle Name
    if ($targetProposalId === 0 && !empty($cycleNameFromFinance)) {
        $check = $conn->prepare("SELECT ProposalID, SalaryScaleData FROM simulation_proposals WHERE TRIM(CycleName) = TRIM(?) ORDER BY ProposalID DESC LIMIT 1");
        $check->bind_param("s", $cycleNameFromFinance);
        $check->execute();
        $res = $check->get_result();
        if ($row = $res->fetch_assoc()) {
            $targetProposalId = $row['ProposalID'];
            $scaleDataJson = $row['SalaryScaleData'];
            file_log("ID Mismatch or missing. Falling back to CycleName match: New ID = $targetProposalId");
        }
        $check->close();
    }

    if ($targetProposalId === 0) {
        file_log("ERROR: No matching proposal found for ID=$proposalId or Cycle=$cycleNameFromFinance");
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => "Proposal ID $proposalId not found and no fallback matching possible."]);
        exit();
    }

    // 3. Update local HR record
    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare("UPDATE simulation_proposals SET Status = ?, FinanceRef = ? WHERE ProposalID = ?");
        $stmt->bind_param("ssi", $newStatus, $financeRef, $proposalId);
        $stmt->execute();

        // 4. LIVE SALARY COMMIT (Only if status is Approved)
        if ($newStatus === 'Approved') {
            $updateSql = "UPDATE employmentinformation ei
                         JOIN simulation_proposal_items spi ON ei.EmployeeID = spi.EmployeeID
                         SET ei.BaseSalary = spi.NewSalary
                         WHERE spi.ProposalID = ?";
            
            $stmtUpdate = $conn->prepare($updateSql);
            $stmtUpdate->bind_param("i", $proposalId);
            $stmtUpdate->execute();
            
            // 5. LIVE SALARY SCALE COMMIT
            if (!empty($scaleDataJson)) {
                $scales = json_decode($scaleDataJson, true);
                if (is_array($scales)) {
                    $updGrade = $conn->prepare("UPDATE salary_grades SET MinSalary = ?, MaxSalary = ? WHERE SalaryGradeID = ?");
                    foreach ($scales as $s) {
                        $sid = (int)$s['SalaryGradeID'];
                        $min = (float)$s['MinSalary'];
                        $max = (float)$s['MaxSalary'];
                        $updGrade->bind_param("ddi", $min, $max, $sid);
                        $updGrade->execute();
                    }
                    $updGrade->close();
                }
            }

            // 5.5 Sync Draft Status (Robust matching)
            $syncDraft = $conn->prepare("UPDATE simulation_drafts 
                                       SET Status = ? 
                                       WHERE TRIM(CycleName) = (SELECT TRIM(CycleName) FROM simulation_proposals WHERE ProposalID = ?)
                                       ORDER BY DraftID DESC LIMIT 1");
            if ($syncDraft) {
                $syncDraft->bind_param("si", $newStatus, $proposalId);
                $syncDraft->execute();
                $syncDraft->close();
            }

            // Log the action for audit
            $auditMsg = "System auto-committed salaries and scale update for Proposal #$proposalId via Finance Approval ($financeRef)";
            $stmtLog = $conn->prepare("INSERT INTO system_logs (LogAction, LogDetails) VALUES ('Salary Commit', ?)");
            if ($stmtLog) {
                $stmtLog->bind_param("s", $auditMsg);
                $stmtLog->execute();
            }
        }

        $conn->commit();
        echo json_encode([
            'success' => true, 
            'message' => "Proposal #$proposalId approved. Live salaries updated."
        ]);

    } catch (Exception $dbEx) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'HR DB Update Failed: ' . $dbEx->getMessage()]);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'System Error: ' . $e->getMessage()]);
}
?>
