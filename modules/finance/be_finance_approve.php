<?php
session_start();
require_once '../../config/config.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';
$id = (int)($input['id'] ?? 0);

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Invalid ID']);
    exit;
}

if ($action === 'approve') {
    $stmt = $conn->prepare("UPDATE simulation_drafts SET Status = 'Approved', LastSaved = NOW() WHERE DraftID = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $notifMsg = "Compensation simulation for cycle has been FINALLY APPROVED by Finance.";
        $notifStmt = $conn->prepare("INSERT INTO system_notifications (module_target, message, role_target) VALUES ('compensation_cycle', ?, 'hr manager')");
        $notifStmt->bind_param("s", $notifMsg);
        $notifStmt->execute();

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Update failed']);
    }
} elseif ($action === 'reject') {
    $reason = $input['reason'] ?? 'Rejected by Finance';
    $stmt = $conn->prepare("UPDATE simulation_drafts SET Status = 'Rejected', RejectionReason = ?, LastSaved = NOW() WHERE DraftID = ?");
    $stmt->bind_param("si", $reason, $id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Update failed']);
    }
}
?>
