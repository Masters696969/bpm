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

if ($action === 'stats') {
    $stats = [
        'pending_disbursement' => 0,
        'total_disbursed' => 0,
        'batch_count' => 0
    ];

    // Total for disbursement (Approved)
    $res = $conn->query("SELECT SUM(i.net_pay) as total FROM payroll_batch_items i INNER JOIN payroll_batches b ON i.batch_id = b.id WHERE b.status = 'Approved'");
    if ($res && ($row = $res->fetch_assoc())) {
        $stats['pending_disbursement'] = (float)($row['total'] ?? 0);
    }

    // Total disbursed (Disbursed or Archived)
    $res = $conn->query("SELECT SUM(i.net_pay) as total FROM payroll_batch_items i INNER JOIN payroll_batches b ON i.batch_id = b.id WHERE b.status IN ('Disbursed', 'Archived')");
    if ($res && ($row = $res->fetch_assoc())) {
        $stats['total_disbursed'] = (float)($row['total'] ?? 0);
    }

    // Counts
    $res = $conn->query("SELECT COUNT(*) as cnt FROM payroll_batches WHERE status = 'Approved'");
    if ($res && ($row = $res->fetch_assoc())) {
        $stats['pending_count'] = (int)$row['cnt'];
    }

    respond(true, $stats);
}

if ($action === 'list_batches') {
    $filter = $_GET['filter'] ?? 'ready'; // 'ready' or 'history'
    
    $status = ($filter === 'history') ? "'Disbursed', 'Archived'" : "'Approved'";
    
    $sql = "SELECT b.id, b.batch_code, b.period_start, b.period_end, b.pay_type, b.status, 
                   COALESCE(SUM(i.net_pay),0) AS total_distributed,
                   COUNT(i.id) as employee_count
            FROM payroll_batches b 
            LEFT JOIN payroll_batch_items i ON i.batch_id = b.id 
            WHERE b.status IN ($status)
            GROUP BY b.id 
            ORDER BY b.id DESC";
            
    $res = $conn->query($sql);
    if (!$res) {
        respond(false, ['error' => $conn->error], 500);
    }
    
    $rows = [];
    while ($r = $res->fetch_assoc()) {
        $rows[] = $r;
    }
    respond(true, ['batches' => $rows]);
}

if ($action === 'view_items') {
    $batchId = (int)($_GET['batch_id'] ?? 0);
    if ($batchId <= 0) respond(false, ['error' => 'Invalid ID'], 400);

    $sql = "SELECT i.*, e.FirstName, e.LastName, e.EmployeeCode,
                   COALESCE(ot.amount, 0) AS overtime_pay,
                   COALESCE(lu.amount, 0) AS late_undertime
            FROM payroll_batch_items i
            INNER JOIN employee e ON e.EmployeeID = i.employee_id
            LEFT JOIN (
                SELECT c.item_id, SUM(c.amount) AS amount
                FROM payroll_item_components c
                WHERE c.component_type='Allowance' AND c.component_name='Overtime Pay'
                GROUP BY c.item_id
            ) ot ON ot.item_id = i.id
            LEFT JOIN (
                SELECT c.item_id, SUM(c.amount) AS amount
                FROM payroll_item_components c
                WHERE c.component_type='Deduction' AND c.component_name='Late/Undertime'
                GROUP BY c.item_id
            ) lu ON lu.item_id = i.id
            WHERE i.batch_id = $batchId
            ORDER BY e.LastName ASC";
            
    $res = $conn->query($sql);
    if (!$res) respond(false, ['error' => $conn->error], 500);
    
    $rows = [];
    while ($r = $res->fetch_assoc()) {
        $rows[] = $r;
    }
    respond(true, ['items' => $rows]);
}

if ($action === 'disburse_batch') {
    $batchId = (int)($_POST['batch_id'] ?? 0);
    if ($batchId <= 0) respond(false, ['error' => 'Invalid ID'], 400);

    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare("UPDATE payroll_batches SET status = 'Disbursed' WHERE id = ? AND status = 'Approved'");
        $stmt->bind_param("i", $batchId);
        $stmt->execute();
        $affectedRows = $stmt->affected_rows;
        $stmt->close();
        
        if ($affectedRows > 0) {
            $aggSql = "SELECT 
                SUM(sss_regular_ee + sss_regular_er + sss_wisp_ee + sss_wisp_er) as sss_total,
                SUM(philhealth_ee + philhealth_er) as philhealth_total,
                SUM(pagibig_ee + pagibig_er) as pagibig_total,
                SUM(withholding_tax) as tax_total,
                SUM(net_pay) as net_total,
                b.batch_code
            FROM payroll_batch_items i
            JOIN payroll_batches b ON b.id = i.batch_id
            WHERE i.batch_id = ?
            GROUP BY b.id";
            
            $stmtAgg = $conn->prepare($aggSql);
            $stmtAgg->bind_param('i', $batchId);
            $stmtAgg->execute();
            $resAgg = $stmtAgg->get_result();
            if ($row = $resAgg->fetch_assoc()) {
                $batchCode = $row['batch_code'];
                $netTotal = (float)$row['net_total'];
                
                $apData = [
                    ['SSS', 'Social Security System', (float)$row['sss_total']],
                    ['PhilHealth', 'PhilHealth Corporation', (float)$row['philhealth_total']],
                    ['PagIBIG', 'Pag-IBIG Fund', (float)$row['pagibig_total']],
                    ['BIR', 'Bureau of Internal Revenue', (float)$row['tax_total']]
                ];

                $stmtAp = $conn->prepare("INSERT INTO accounts_payable (batch_id, category, payee_name, description, amount, status) VALUES (?, ?, ?, ?, ?, 'Pending')");
                foreach ($apData as $ap) {
                    if ($ap[2] <= 0) continue;
                    $desc = "Payroll Deductions & Benefits for Batch $batchCode";
                    $stmtAp->bind_param('isssd', $batchId, $ap[0], $ap[1], $desc, $ap[2]);
                    $stmtAp->execute();
                }
                $stmtAp->close();

                // Create GL Entry for the net pay disbursed
                if ($netTotal > 0) {
                    $ref = "PAY-" . $batchCode;
                    $glDesc = "Payroll Disbursement for Batch $batchCode";
                    $stmtGl = $conn->prepare("INSERT INTO general_ledger (reference_id, account_name, description, debit, credit) VALUES (?, 'Salaries & Wages Payable', ?, ?, 0.00)");
                    $stmtGl->bind_param('ssd', $ref, $glDesc, $netTotal);
                    $stmtGl->execute();
                    $stmtGl->close();
                }
            }
            $stmtAgg->close();
            
            $conn->commit();
            respond(true, ['message' => 'Batch thoroughly disbursed. AP Vouchers & GL entries generated.']);
        } else {
            $conn->rollback();
            respond(false, ['error' => 'Batch not found or not in Approved status.']);
        }
    } catch (Exception $e) {
        $conn->rollback();
        respond(false, ['error' => $e->getMessage()], 500);
    }
}

if ($action === 'archive_batch') {
    $batchId = (int)($_POST['batch_id'] ?? 0);
    if ($batchId <= 0) respond(false, ['error' => 'Invalid ID'], 400);

    // Update status to Archived
    $stmt = $conn->prepare("UPDATE payroll_batches SET status = 'Archived' WHERE id = ? AND status = 'Approved'");
    $stmt->bind_param("i", $batchId);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            respond(true, ['message' => 'Batch successfully archived to history.']);
        } else {
            respond(false, ['error' => 'Batch not found or not in Approved status.']);
        }
    } else {
        respond(false, ['error' => $stmt->error], 500);
    }
    $stmt->close();
}

respond(false, ['error' => 'Unknown action'], 400);
