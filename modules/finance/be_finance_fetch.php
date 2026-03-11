<?php
session_start();
require_once '../../config/config.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? 'fetch';

if ($action === 'fetch') {
    // Fetch Reviewed simulations for Finance Final Approval
    $sql = "SELECT DraftID, CycleName, TotalCost, Status, CreatedAt, ProposedBy FROM simulation_drafts WHERE Status IN ('Reviewed', 'Approved') ORDER BY CreatedAt DESC";
    $result = $conn->query($sql);
    $sims = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $sims[] = $row;
        }
    }
    echo json_encode(['success' => true, 'data' => $sims]);
} elseif ($action === 'details') {
    $id = (int)($_GET['id'] ?? 0);
    $stmt = $conn->prepare("SELECT * FROM simulation_drafts WHERE DraftID = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        echo json_encode(['success' => true, 'data' => $res->fetch_assoc()]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Not found']);
    }
}
?>
