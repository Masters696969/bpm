<?php
// modules/admin/backend/be_finalize_cycle.php (HR Laptop)
header('Content-Type: application/json');
session_start();
require_once '../../../config/config.php';

$response = ['success' => false, 'message' => ''];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method.');
    }

    $proposalId = isset($_POST['proposal_id']) ? (int)$_POST['proposal_id'] : 0;
    if ($proposalId <= 0) {
        throw new Exception('Missing Proposal ID.');
    }

    // 1. Fetch Proposal Details
    $stmt = $conn->prepare("SELECT PeriodID, SalaryScaleData FROM simulation_proposals WHERE ProposalID = ?");
    $stmt->bind_param("i", $proposalId);
    $stmt->execute();
    $stmt->bind_result($periodId, $salaryScaleJson);
    if (!$stmt->fetch()) {
        throw new Exception('Proposal not found.');
    }
    $stmt->close();

    // 2. Execute Shared Finalization Logic
    require_once 'be_finalize_cycle_logic.php';
    
    // Begin Transaction
    $conn->begin_transaction();
    
    $result = applyFinalSalaryChanges($conn, $proposalId, $periodId, $salaryScaleJson);
    
    if (!$result['success']) {
        throw new Exception($result['message']);
    }

    // Commit Transaction
    $conn->commit();

    $response['ok'] = true;
    $response['message'] = 'Planning cycle finalized! Salaries and grades have been updated successfully.';

} catch (Exception $e) {
    if (isset($conn)) $conn->rollback();
    $response['ok'] = false;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>
