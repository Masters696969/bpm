<?php
session_start();
header('Content-Type: application/json');
require_once '../../config/config.php';

if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Handle FormData (since we're sending files now)
$reason = $_POST['reason'] ?? '';
$proposalsJson = $_POST['proposals'] ?? '[]';
$proposals = json_decode($proposalsJson, true);

if (empty($reason) || empty($proposals)) {
    echo json_encode(['success' => false, 'message' => 'Missing reason or proposal data']);
    exit();
}

// Handle File Upload (Optional)
$proofFileUrl = null;
if (isset($_FILES['proof_file']) && $_FILES['proof_file']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = '../../uploads/proofs/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $fileExt = pathinfo($_FILES['proof_file']['name'], PATHINFO_EXTENSION);
    $fileName = uniqid('proof_', true) . '.' . $fileExt;
    $targetPath = $uploadDir . $fileName;
    
    if (move_uploaded_file($_FILES['proof_file']['tmp_name'], $targetPath)) {
        $proofFileUrl = 'uploads/proofs/' . $fileName; // Relative for database storage
    }
}

$conn->begin_transaction();
try {
    $proposedBy = $_SESSION['user_id'] ?? null;
    $batchRef = uniqid('batch_', true); // Generate unique batch ID

    $stmt = $conn->prepare("INSERT INTO salary_grade_proposals (SalaryGradeID, ProposedMinSalary, ProposedMaxSalary, Reason, Status, ProposedBy, BatchReference, proof_file_url) VALUES (?, ?, ?, ?, 'Pending', ?, ?, ?)");
    foreach ($proposals as $prop) {
        $gradeId = (int)$prop['SalaryGradeID'];
        $min = (float)$prop['ProposedMin'];
        $max = (float)$prop['ProposedMax'];
        $stmt->bind_param("iddsiss", $gradeId, $min, $max, $reason, $proposedBy, $batchRef, $proofFileUrl);
        if (!$stmt->execute()) {
            throw new Exception("Error inserting proposal: " . $stmt->error);
        }
    }
    $conn->commit();

    // Notify through system notifications
    $notifMsg = "New salary scale change proposal submitted by {$_SESSION['username']}. Batch: {$batchRef}";
    $notifStmt = $conn->prepare("INSERT INTO system_notifications (module_target, message, role_target) VALUES ('compensation_cycle', ?, 'supervisor')");
    $notifStmt->bind_param("s", $notifMsg);
    $notifStmt->execute();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    if ($conn) $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}


$conn->close();
?>
