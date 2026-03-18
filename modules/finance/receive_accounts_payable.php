<?php
// modules/finance/receive_accounts_payable.php (Finance Laptop)
// Receives payroll deductions (SSS, PhilHealth, etc.) from the HR Laptop.
header('Content-Type: application/json');

// ── DB connection (PDO) ──────────────────────────────────────────────────────
require_once '../../config/database.php';
$database = new Database();
$db = $database->getConnection();

// ── Parse incoming JSON ────────────────────────────────────────────────────────
$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON payload']);
    exit();
}

$batchId = (int)($data['batch_id'] ?? 0);
$payables = $data['payables'] ?? []; // Array of objects

$count = 0;

try {
    $db->beginTransaction();

    // 1. Process AP Payables
    if (!empty($payables)) {
        $sqlAP = "INSERT INTO accounts_payable (batch_id, employee_name, payee_name, category, description, amount, status, created_at)
                VALUES (?, ?, ?, ?, ?, ?, 'Pending', NOW())";
        $stmtAP = $db->prepare($sqlAP);

        foreach ($payables as $ap) {
            $empName = $ap['employee_name'] ?? '';
            $payee = $ap['payee_name'] ?? '';
            $cat = $ap['category'] ?? 'Other';
            $desc = $ap['description'] ?? '';
            $amt = (float)($ap['amount'] ?? 0);

            if ($amt > 0) {
                $stmtAP->execute([$batchId, $empName, $payee, $cat, $desc, $amt]);
                $count++;
            }
        }
    }

    $db->commit();

    echo json_encode([
        'success' => true,
        'message' => "Successfully recorded $count payables in Finance.",
        'received_count' => $count
    ]);

} catch (PDOException $e) {
    if ($db->inTransaction()) $db->rollBack();
    echo json_encode(['success' => false, 'message' => 'DB error: ' . $e->getMessage()]);
}
?>
