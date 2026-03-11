<?php
// applicant_action.php
require_once '../../config/config.php';
session_start();

if (empty($_SESSION['user_id']) || strtolower($_SESSION['user_role'] ?? '') !== 'hr staff') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

if ($action === 'get_details') {
    $id = $_GET['id'] ?? null;
    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'ID missing']);
        exit;
    }

    $stmt = $conn->prepare("SELECT a.*, j.Title as JobTitle 
                            FROM applicants a 
                            JOIN job_postings j ON a.PostID = j.PostID 
                            WHERE a.ApplicantID = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if ($result) {
        echo json_encode(['success' => true, 'data' => $result]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Applicant not found']);
    }
} 

elseif ($action === 'get_evaluation') {
    $id = $_GET['id'] ?? null;
    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'Evaluation ID missing']);
        exit;
    }

    $stmt = $conn->prepare("SELECT e.*, a.FirstName, a.LastName, j.Title as JobTitle 
                            FROM interview_evaluations e
                            JOIN applicants a ON e.ApplicantID = a.ApplicantID
                            JOIN job_postings j ON a.PostID = j.PostID
                            WHERE e.EvaluationID = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if ($result) {
        echo json_encode(['success' => true, 'data' => $result]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Evaluation not found']);
    }
}

elseif ($action === 'update_status') {
    $id = $_POST['id'] ?? null;
    $status = $_POST['status'] ?? null;

    if (!$id || !$status) {
        echo json_encode(['success' => false, 'message' => 'Missing parameters']);
        exit;
    }

    $allowed = ['New', 'Reviewed', 'Shortlisted', 'Interview', 'Rejected', 'Accepted'];
    if (!in_array($status, $allowed)) {
        echo json_encode(['success' => false, 'message' => 'Invalid status']);
        exit;
    }

    $stmt = $conn->prepare("UPDATE applicants SET Status = ? WHERE ApplicantID = ?");
    $stmt->bind_param("si", $status, $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
}

elseif ($action === 'approve_hire') {
    $id = $_POST['id'] ?? null;
    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'ID missing']);
        exit;
    }

    $stmt = $conn->prepare("UPDATE applicants SET ApprovalStatus = 'Approved', Status = 'Accepted' WHERE ApplicantID = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Hiring approved successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
}

elseif ($action === 'decline_hire') {
    $id = $_POST['id'] ?? null;
    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'ID missing']);
        exit;
    }

    $stmt = $conn->prepare("UPDATE applicants SET ApprovalStatus = 'Declined', Status = 'Rejected' WHERE ApplicantID = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Hiring declined']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
}

else {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>
