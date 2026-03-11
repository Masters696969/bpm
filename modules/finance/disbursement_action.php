<?php
require_once __DIR__ . '/../../config/config.php';
session_start();

header('Content-Type: application/json; charset=utf-8');

function respond($ok, $data = [], $httpCode = 200) {
    global $conn;
    http_response_code($httpCode);
    echo json_encode(array_merge(['ok' => $ok], $data));
    if (isset($conn)) $conn->close();
    exit;
}

if (!isset($_SESSION['username'])) {
    respond(false, ['error' => 'Unauthorized'], 401);
}

// Xendit API settings from config
$xendit_secret_key = $xendit_config['secret_key'] ?? '';
$xendit_endpoint = $xendit_config['payout_endpoint'] ?? 'https://api.xendit.co/v2/payouts';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'stats') {
    $stats = [
        'pending_payout' => 0,
        'total_paid' => 0
    ];

    $res = $conn->query("SELECT SUM(i.net_pay) as total FROM payroll_batch_items i INNER JOIN payroll_batches b ON i.batch_id = b.id WHERE b.status = 'Finance Approved' AND (i.status != 'Paid' OR i.status IS NULL)");
    if ($res && ($row = $res->fetch_assoc())) {
        $stats['pending_payout'] = (float)($row['total'] ?? 0);
    }

    $res = $conn->query("SELECT SUM(i.net_pay) as total FROM payroll_batch_items i INNER JOIN payroll_batches b ON i.batch_id = b.id WHERE i.status = 'Paid'");
    if ($res && ($row = $res->fetch_assoc())) {
        $stats['total_paid'] = (float)($row['total'] ?? 0);
    }

    respond(true, $stats);
}

if ($action === 'list_history') {
    $res = $conn->query("SELECT * FROM payout_history ORDER BY created_at DESC LIMIT 50");
    $rows = [];
    while ($r = $res->fetch_assoc()) $rows[] = $r;
    respond(true, ['history' => $rows]);
}

if ($action === 'list_batches') {
    $filter = $_GET['filter'] ?? 'pending';
    // History only shows Disbursed batches. Anything else (Finance Approved, Archived with remaining) stays in Pending.
    $statusCondition = ($filter === 'history') ? "b.status = 'Disbursed'" : "b.status IN ('Finance Approved', 'Archived')";

    $sql = "SELECT b.id, b.batch_code, b.period_start, b.period_end, b.pay_type, b.status, 
                   COUNT(i.id) as employee_count,
                   SUM(CASE WHEN i.status = 'Paid' THEN 1 ELSE 0 END) as paid_count
            FROM payroll_batches b 
            LEFT JOIN payroll_batch_items i ON i.batch_id = b.id 
            WHERE $statusCondition
            GROUP BY b.id 
            ORDER BY b.id DESC";
            
    $res = $conn->query($sql);
    if (!$res) respond(false, ['error' => $conn->error], 500);
    
    $rows = [];
    while ($r = $res->fetch_assoc()) {
        $r['employees_remaining'] = (int)$r['employee_count'] - (int)$r['paid_count'];
        
        // Auto-mark as Disbursed if all are paid and it was still Finance Approved
        if ($filter === 'pending' && $r['employees_remaining'] <= 0 && $r['employee_count'] > 0 && $r['status'] === 'Finance Approved') {
            $conn->query("UPDATE payroll_batches SET status = 'Disbursed' WHERE id = " . (int)$r['id']);
            continue;
        }

        // If filtering for history, we already have only 'Disbursed' from statusCondition.
        // If filtering for pending, we exclude 'Archived' batches if they have 0 unpaid (i.e. they are fully finished or empty)
        if ($filter === 'pending' && $r['status'] === 'Archived' && $r['employees_remaining'] <= 0) {
            continue;
        }

        $rows[] = $r;
    }
    respond(true, ['batches' => $rows]);
}

if ($action === 'list_employees') {
    $batchId = (int)($_GET['batch_id'] ?? 0);
    $showAll = isset($_GET['all']) && $_GET['all'] === '1';
    
    if ($batchId <= 0) respond(false, ['error' => 'Invalid batch ID'], 400);

    $statusClause = $showAll ? "" : " AND (i.status != 'Paid' OR i.status IS NULL)";

    $sql = "SELECT i.id as item_id, i.employee_id, i.net_pay, i.status as item_status,
                   e.FirstName, e.LastName,
                   COALESCE(bd.AccountNumber, 'Not Set') as account_number,
                   COALESCE(bd.BankName, 'BDO') as bank_name
            FROM payroll_batch_items i
            INNER JOIN employee e ON e.EmployeeID = i.employee_id
            LEFT JOIN bankdetails bd ON bd.EmployeeID = i.employee_id
            WHERE i.batch_id = $batchId $statusClause
            ORDER BY e.LastName ASC";
            
    $res = $conn->query($sql);
    if (!$res) respond(false, ['error' => $conn->error], 500);
    
    $rows = [];
    while ($r = $res->fetch_assoc()) {
        $rows[] = $r;
    }
    respond(true, ['employees' => $rows]);
}

if ($action === 'pay_employee') {
    $itemId = (int)($_POST['item_id'] ?? 0);
    if ($itemId <= 0) respond(false, ['error' => 'Invalid Item ID'], 400);

    $sql = "SELECT i.net_pay, b.batch_code, e.FirstName, e.LastName, 
                   bd.AccountNumber, bd.BankName, i.batch_id
            FROM payroll_batch_items i
            INNER JOIN payroll_batches b ON b.id = i.batch_id
            INNER JOIN employee e ON e.EmployeeID = i.employee_id
            LEFT JOIN bankdetails bd ON bd.EmployeeID = i.employee_id
            WHERE i.id = $itemId AND (i.status != 'Paid' OR i.status IS NULL)";
            
    $res = $conn->query($sql);
    if (!$res || $res->num_rows === 0) {
        respond(false, ['error' => 'Item not found or already paid.'], 404);
    }
    $row = $res->fetch_assoc();

    $accountNum = preg_replace('/[^0-9]/', '', $row['AccountNumber']);
    if (empty($accountNum)) {
        respond(false, ['error' => 'Invalid bank account number. It must contain only digits.'], 400);
    }

    $amount = (float)$row['net_pay'];
    if ($amount <= 0) {
        respond(false, ['error' => 'Net pay must be greater than zero to process a payout.'], 400);
    }

    $bankName = strtoupper($row['BankName'] ?? 'BDO');
    
    // Map Bank Name to Xendit Channel Code
    $channelCode = 'PH_BDO';
    if (strpos($bankName, 'BPI') !== false) $channelCode = 'PH_BPI';
    if (strpos($bankName, 'METRO') !== false) $channelCode = 'PH_METROBANK';
    if (strpos($bankName, 'UNION') !== false) $channelCode = 'PH_UNIONBANK';
    if (strpos($bankName, 'GCASH') !== false) $channelCode = 'PH_GCASH';
    if (strpos($bankName, 'PAYMAYA') !== false || strpos($bankName, 'MAYA') !== false) $channelCode = 'PH_MAYA';

    $accountHolderName = trim($row['FirstName'] . ' ' . $row['LastName']);
    // Xendit usually prefers alphanumeric + spaces
    $accountHolderName = preg_replace('/[^A-Za-z0-9 ]/', '', $accountHolderName);

    $payload = [
        'reference_id' => 'PAYOUT-' . $itemId . '-' . time(),
        'channel_code' => $channelCode,
        'amount' => $amount,
        'currency' => 'PHP',
        'channel_properties' => [
            'account_number' => $accountNum,
            'account_holder_name' => $accountHolderName
        ],
        'description' => "Payroll Batch " . $row['batch_code']
    ];

    $ch = curl_init($xendit_endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Basic ' . base64_encode($xendit_secret_key . ':'),
        'Idempotency-key: ' . uniqid()
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $resData = json_decode($response, true);

    if ($httpCode >= 200 && $httpCode < 300) {
        $xenditData = $resData;
        $xenditId = $xenditData['id'] ?? '';
        
        $employeeName = $accountHolderName;

        // Success (ACCEPTED or COMPLETED) - Mark item as Paid
        $conn->query("UPDATE payroll_batch_items SET status = 'Paid' WHERE id = $itemId");

        // Log to Payout History
        $stmt = $conn->prepare("INSERT INTO payout_history (reference_id, employee_id, employee_name, amount, bank_name, account_number, status, xendit_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $status = $xenditData['status'] ?? 'ACCEPTED';
        $stmt->bind_param("sisdssss", $payload['reference_id'], $row['employee_id'], $employeeName, $amount, $channelCode, $accountNum, $status, $xenditId);
        $stmt->execute();

        $batchId = (int)$row['batch_id'];
        $checkRes = $conn->query("SELECT COUNT(*) as unpaid FROM payroll_batch_items WHERE batch_id = $batchId AND (status != 'Paid' OR status IS NULL)");
        if ($checkRes && $checkRow = $checkRes->fetch_assoc()) {
            if ((int)$checkRow['unpaid'] === 0) {
                $conn->query("UPDATE payroll_batches SET status = 'Disbursed' WHERE id = $batchId");
            }
        }

        respond(true, ['message' => 'Payout initiated successfully via Xendit!', 'xendit' => $resData]);
    } else {
        $errorMsg = $resData['message'] ?? 'Xendit API Request Failed';
        
        // If there are validation errors, extract them for the UI
        if (isset($resData['errors']) && is_array($resData['errors'])) {
            $details = [];
            foreach ($resData['errors'] as $err) {
                if (isset($err['messages'])) {
                    $details[] = implode(', ', $err['messages']);
                }
            }
            if (!empty($details)) {
                $errorMsg .= ': ' . implode('; ', $details);
            }
        }
        
        respond(false, ['error' => $errorMsg, 'details' => $resData], 500);
    }
}

respond(false, ['error' => 'Unknown action'], 400);
