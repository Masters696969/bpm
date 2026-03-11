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
    $stmt = $conn->prepare("UPDATE simulation_drafts SET Status = 'Verified', LastSaved = NOW() WHERE DraftID = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        // Notify Manager
        $notifMsg = "A compensation simulation has been verified by the Supervisor and awaits your review.";
        $notifStmt = $conn->prepare("INSERT INTO system_notifications (module_target, message, role_target) VALUES ('compensation_review', ?, 'hr manager')");
        $notifStmt->bind_param("s", $notifMsg);
        $notifStmt->execute();

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Update failed']);
    }
} elseif ($action === 'reject') {
    $reason = $input['reason'] ?? 'No reason provided';
    $stmt = $conn->prepare("UPDATE simulation_drafts SET Status = 'Rejected', RejectionReason = ?, LastSaved = NOW() WHERE DraftID = ?");
    $stmt->bind_param("si", $reason, $id);
    
    // Check if RejectionReason column exists
    $checkCol = $conn->query("SHOW COLUMNS FROM simulation_drafts LIKE 'RejectionReason'");
    if ($checkCol->num_rows == 0) {
        $conn->query("ALTER TABLE simulation_drafts ADD COLUMN RejectionReason TEXT");
        $stmt = $conn->prepare("UPDATE simulation_drafts SET Status = 'Rejected', RejectionReason = ?, LastSaved = NOW() WHERE DraftID = ?");
        $stmt->bind_param("si", $reason, $id);
    }

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Update failed']);
    }
}
?>
