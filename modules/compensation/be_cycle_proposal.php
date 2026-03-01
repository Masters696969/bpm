<?php
session_start();
header('Content-Type: application/json');
require_once '../../config/config.php';

if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

$reason = $data['reason'] ?? '';
$proposals = $data['proposals'] ?? [];

if (empty($reason) || empty($proposals)) {
    echo json_encode(['success' => false, 'message' => 'Missing reason or proposal data']);
    exit();
}

$conn->begin_transaction();
try {
    $proposedBy = $_SESSION['user_id'] ?? null;
    $batchRef = uniqid('batch_', true); // Generate unique batch ID

    $stmt = $conn->prepare("INSERT INTO salary_grade_proposals (SalaryGradeID, ProposedMinSalary, ProposedMaxSalary, Reason, Status, ProposedBy, BatchReference) VALUES (?, ?, ?, ?, 'Pending', ?, ?)");
    foreach ($proposals as $prop) {
        $gradeId = (int)$prop['SalaryGradeID'];
        $min = (float)$prop['ProposedMin'];
        $max = (float)$prop['ProposedMax'];
        $stmt->bind_param("iddsis", $gradeId, $min, $max, $reason, $proposedBy, $batchRef);
        if (!$stmt->execute()) {
            throw new Exception("Error inserting proposal: " . $stmt->error);
        }
    }
    $conn->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    if ($conn) $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?>
