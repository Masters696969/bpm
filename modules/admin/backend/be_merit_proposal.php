<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

$conn = new mysqli("localhost", "root", "", "hr4");
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit();
}

try {
    $conn->begin_transaction();

    // 1. Generate unique Batch Reference
    $batch_ref = 'MRT_' . strtoupper(uniqid());
    $reason = $_POST['reason'] ?? '';
    
    // Parse the changes which was sent as JSON array of objects
    $changes_json = $_POST['changes'] ?? '[]';
    $changes = json_decode($changes_json, true);

    if (empty($changes)) {
        throw new Exception("No valid matrix changes provided.");
    }

    $stmt_insert = $conn->prepare("INSERT INTO merit_proposals (BatchReference, period_id, performance_rating, compa_ratio_range, ProposedMinIncrease, ProposedMaxIncrease, Reason, ProposedBy, Status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pending')");
    if (!$stmt_insert) {
        throw new Exception("Prepare failed for merit_proposals: " . $conn->error);
    }

    $proposed_by = $_SESSION['user_id'] ?? 1; // Fallback to 1 if user_id is missing
    $period_id = 1; // Assuming active period_id = 1; could be queried dynamically
    
    foreach ($changes as $change) {
        $rating = $change['rating'];
        $range = $change['range'];
        $min = $change['min'];
        $max = $change['max'];

        $stmt_insert->bind_param("sisssssi", $batch_ref, $period_id, $rating, $range, $min, $max, $reason, $proposed_by);
        if (!$stmt_insert->execute()) {
             throw new Exception("Failed to insert matrix change record: " . $stmt_insert->error);
        }
    }

    // 2. Add System Notification for Supervisor
    $message = "A new merit matrix adjustment batch ($batch_ref) has been proposed and requires your endorsement.";
    $role_target = "supervisor";
    $module_target = "compensation_cycle";

    $stmt_notif = $conn->prepare("INSERT INTO system_notifications (module_target, message, role_target) VALUES (?, ?, ?)");
    if (!$stmt_notif) {
        throw new Exception("Prepare failed for system_notifications: " . $conn->error);
    }
    $stmt_notif->bind_param("sss", $module_target, $message, $role_target);
    $stmt_notif->execute();

    $conn->commit();
    echo json_encode([
        'success' => true, 
        'message' => 'Merit Matrix proposal submitted successfully. Batch Reference: ' . $batch_ref
    ]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?>
