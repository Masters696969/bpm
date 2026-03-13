<?php
header('Content-Type: application/json');
session_start();
require_once '../../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit();
}

$applicantId = $_POST['applicant_id'] ?? null;
$tech = $_POST['rating_technical'] ?? 0;
$comm = $_POST['rating_communication'] ?? 0;
$fin = $_POST['rating_financial'] ?? 0;
$rel = $_POST['rating_reliability'] ?? 0;
$comments = $_POST['comments'] ?? '';
$decision = $_POST['decision'] ?? '';
$interviewerId = $_SESSION['user_id'] ?? 1;

// Calculate average
$avg = ($tech + $comm + $fin + $rel) / 4;

if (!$applicantId || !$comments || !$decision) {
    echo json_encode(['success' => false, 'error' => 'All fields (Ratings, Comments, and Decision) are required']);
    exit();
}

$conn->begin_transaction();

try {
    // 1. Insert Evaluation
    $stmt = $conn->prepare("INSERT INTO interview_evaluations 
        (ApplicantID, InterviewerID, TechnicalRating, CommunicationRating, FinancialRating, ReliabilityRating, AverageRating, Comments, Decision) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiiiddss", $applicantId, $interviewerId, $tech, $comm, $fin, $rel, $avg, $comments, $decision);
    $stmt->execute();

    // 2. Update Applicant Status based on decision
    // decision values: 'Strong Hire', 'Potential Hire', 'Do Not Hire'
    $newStatus = 'Shortlisted'; 
    if ($decision === 'Strong Hire') $newStatus = 'Accepted';
    if ($decision === 'Do Not Hire') $newStatus = 'Rejected';
    if ($decision === 'Potential Hire') $newStatus = 'Shortlisted';

    $stmt = $conn->prepare("UPDATE applicants SET Status = ?, ApprovalStatus = 'Pending Manager Approval' WHERE ApplicantID = ?");
    $stmt->bind_param("si", $newStatus, $applicantId);
    $stmt->execute();

    $conn->commit();
    echo json_encode(['success' => true, 'message' => "Evaluation saved. Candidate status updated to $newStatus."]);

} catch (Exception $e) {
    if (isset($conn) && $conn->connect_errno === 0 && $conn->ping()) {
        $conn->rollback();
    }
    echo json_encode(['success' => false, 'error' => 'Failed to save evaluation: ' . $e->getMessage()]);
}
?>
