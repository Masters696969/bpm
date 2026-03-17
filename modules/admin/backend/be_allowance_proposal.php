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

    $batch_ref = 'ALW_' . strtoupper(uniqid());
    $reason = $_POST['reason'] ?? '';
    
    $changes_json = $_POST['changes'] ?? '[]';
    $changes = json_decode($changes_json, true);

    if (empty($changes)) {
        throw new Exception("No valid allowance changes provided.");
    }

    $stmt_insert = $conn->prepare("INSERT INTO allowance_proposals (BatchReference, SalaryGradeID, AllowanceTypeID, ProposedAmount, Reason, ProposedBy, Status) VALUES (?, ?, ?, ?, ?, ?, 'Pending')");
    if (!$stmt_insert) {
        throw new Exception("Prepare failed for allowance_proposals: " . $conn->error);
    }

    $proposed_by = $_SESSION['user_id'] ?? 1;
    
    foreach ($changes as $change) {
        $grade_id = $change['grade_id'];
        $type_id = $change['type_id'];
        $amount = $change['amount'];

        $stmt_insert->bind_param("siissi", $batch_ref, $grade_id, $type_id, $amount, $reason, $proposed_by);
        if (!$stmt_insert->execute()) {
             throw new Exception("Failed to insert allowance change record: " . $stmt_insert->error);
        }
    }

    // Add System Notification for Supervisor
    $message = "A new allowance adjustment batch ($batch_ref) has been proposed and requires your endorsement.";
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
        'message' => 'Allowance proposal submitted successfully. Batch Reference: ' . $batch_ref
    ]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?>
