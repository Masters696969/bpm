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

$action = $_GET['action'] ?? $_POST['action'] ?? '';

if ($action === 'list_gl') {
    $filter = $_GET['filter'] ?? 'all';
    
    $where = "1=1";
    if ($filter === 'posted') {
        $where = "status = 'Posted'";
    } else if ($filter === 'pending') {
        $where = "status = 'Pending'";
    }

    $sql = "SELECT * FROM general_ledger WHERE $where ORDER BY transaction_date DESC, id DESC";
    
    $res = $conn->query($sql);
    if (!$res) respond(false, ['error' => $conn->error], 500);
    
    $rows = [];
    while ($r = $res->fetch_assoc()) {
        $rows[] = $r;
    }
    respond(true, ['entries' => $rows]);
}

if ($action === 'stats') {
    $stats = [
        'total_debit' => 0,
        'total_credit' => 0,
        'transaction_count' => 0
    ];

    $res = $conn->query("SELECT SUM(debit) as d, SUM(credit) as c, COUNT(*) as cnt FROM general_ledger WHERE status = 'Posted'");
    if ($res && ($row = $res->fetch_assoc())) {
        $stats['total_debit'] = (float)($row['d'] ?? 0);
        $stats['total_credit'] = (float)($row['c'] ?? 0);
        $stats['transaction_count'] = (int)($row['cnt'] ?? 0);
    }
    
    respond(true, $stats);
}

respond(false, ['error' => 'Unknown action'], 400);
