<?php
require_once __DIR__ . '/../../config/config.php';
session_start();

header('Content-Type: application/json; charset=utf-8');

function respond($ok, $data = [], $httpCode = 200) {
    http_response_code($httpCode);
    echo json_encode(array_merge(['ok' => $ok], $data));
    exit;
}

if (!isset($_SESSION['username'])) {
    respond(false, ['error' => 'Unauthorized'], 401);
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'list_ap') {
    $status = $_GET['status'] ?? 'Pending';
    
    $sql = "SELECT ap.*, b.batch_code, b.period_start, b.period_end 
            FROM accounts_payable ap
            JOIN payroll_batches b ON b.id = ap.batch_id
            WHERE ap.status = ?
            ORDER BY ap.id DESC";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $status);
    $stmt->execute();
    $res = $stmt->get_result();
    
    $rows = [];
    while ($r = $res->fetch_assoc()) {
        $rows[] = $r;
    }
    respond(true, ['data' => $rows]);
}

if ($action === 'release_payment') {
    $apId = (int)($_POST['id'] ?? 0);
    if ($apId <= 0) respond(false, ['error' => 'Invalid ID'], 400);

    $stmt = $conn->prepare("UPDATE accounts_payable SET status='Paid' WHERE id=?");
    $stmt->bind_param('i', $apId);
    if ($stmt->execute()) {
        respond(true, ['message' => 'Payment released successfully']);
    } else {
        respond(false, ['error' => $stmt->error], 500);
    }
}

if ($action === 'get_voucher_details') {
    $apId = (int)($_GET['id'] ?? 0);
    if ($apId <= 0) respond(false, ['error' => 'Invalid ID'], 400);

    // Get the batch_id and category first
    $stmt = $conn->prepare("SELECT batch_id, category FROM accounts_payable WHERE id = ?");
    $stmt->bind_param('i', $apId);
    $stmt->execute();
    $ap = $stmt->get_result()->fetch_assoc();
    if (!$ap) respond(false, ['error' => 'Voucher not found'], 404);

    $batchId = $ap['batch_id'];
    $category = $ap['category'];

    // Define the amount formula based on category
    $amountFormula = "";
    if ($category === 'SSS') {
        $amountFormula = "(i.sss_regular_ee + i.sss_regular_er + i.sss_wisp_ee + i.sss_wisp_er)";
    } elseif ($category === 'PhilHealth') {
        $amountFormula = "(i.philhealth_ee + i.philhealth_er)";
    } elseif ($category === 'PagIBIG') {
        $amountFormula = "(i.pagibig_ee + i.pagibig_er)";
    } elseif ($category === 'BIR') {
        $amountFormula = "i.withholding_tax";
    } else {
        respond(false, ['error' => 'Invalid category'], 400);
    }

    $sql = "SELECT 
                e.FirstName, e.LastName,
                $amountFormula as amount,
                b.period_end as date
            FROM payroll_batch_items i
            JOIN employee e ON e.EmployeeID = i.employee_id
            JOIN payroll_batches b ON b.id = i.batch_id
            WHERE i.batch_id = ? AND $amountFormula > 0
            ORDER BY e.LastName ASC, e.FirstName ASC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $batchId);
    $stmt->execute();
    $res = $stmt->get_result();

    $details = [];
    while ($row = $res->fetch_assoc()) {
        $details[] = [
            'employee_name' => $row['LastName'] . ', ' . $row['FirstName'],
            'amount' => (float)$row['amount'],
            'date' => $row['date']
        ];
    }

    respond(true, ['data' => $details, 'category' => $category]);
}

respond(false, ['error' => 'Unknown action'], 400);
?>
