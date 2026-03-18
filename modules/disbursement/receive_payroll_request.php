<?php
header('Content-Type: application/json');
require_once '../../config/config.php';

$expectedApiKey = 'HR_FINANCE_SECRET_2026';
$providedApiKey = $_SERVER['HTTP_X_API_KEY'] ?? '';

if ($providedApiKey !== $expectedApiKey) {
    echo json_encode(['success' => false, 'message' => 'Invalid API Key']);
    exit;
}

$payload = json_decode(file_get_contents('php://input'), true);
if (!$payload) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON payload']);
    exit;
}

// Required fields
$required = ['cycle_name', 'period_id', 'dept_code', 'entity_name', 'allocation_type', 'total_budget', 'proposed_by_id', 'hr_proposal_id', 'fiscal_year'];
foreach ($required as $field) {
    if (!isset($payload[$field])) {
        echo json_encode(['success' => false, 'message' => "Missing field: $field"]);
        exit;
    }
}

// Generate finance_ref
$finance_ref = 'FIN-' . date('YmdHis') . '-' . rand(1000, 9999);

// Insert into budget_proposals
$sql = "INSERT INTO budget_proposals (budget_id, cycle_name, period_id, dept_code, entity_name, allocation_type, total_budget, proposed_by_id, hr_proposal_id, finance_ref, fiscal_year, items, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
$stmt = $conn->prepare($sql);
$itemsJson = json_encode($payload['items'] ?? []);
$budget_id = $payload['hr_proposal_id']; // Use hr_proposal_id as budget_id
$stmt->bind_param('isisssdiissss', $budget_id, $payload['cycle_name'], $payload['period_id'], $payload['dept_code'], $payload['entity_name'], $payload['allocation_type'], $payload['total_budget'], $payload['proposed_by_id'], $payload['hr_proposal_id'], $finance_ref, $payload['fiscal_year'], $itemsJson);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'finance_ref' => $finance_ref]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to record payroll request: ' . $conn->error]);
}
$stmt->close();
$conn->close();
?>
