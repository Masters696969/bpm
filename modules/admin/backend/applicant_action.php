<?php
// applicant_action.php
require_once '../../../config/config.php';
session_start();

if (!isset($_SESSION['username'])) {
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

    $stmt = $conn->prepare("SELECT a.*, j.Title as JobTitle, e.AverageRating 
                            FROM applicants a 
                            JOIN job_postings j ON a.PostID = j.PostID 
                            LEFT JOIN interview_evaluations e ON a.ApplicantID = e.ApplicantID
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

elseif ($action === 'update_resume_score') {
    $id = $_POST['id'] ?? null;
    $score = $_POST['score'] ?? 0;

    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'ID missing']);
        exit;
    }

    $stmt = $conn->prepare("UPDATE applicants SET ResumeScore = ? WHERE ApplicantID = ?");
    $stmt->bind_param("ii", $score, $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Resume score updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
}

elseif ($action === 'generate_exam') {
    $applicant_id = $_GET['applicant_id'] ?? null;
    if (!$applicant_id) {
        echo json_encode(['success' => false, 'message' => 'Applicant ID missing']);
        exit;
    }

    // 1. Get Applicant's Department
    $stmt = $conn->prepare("SELECT d.DepartmentID 
                            FROM applicants a 
                            JOIN job_postings j ON a.PostID = j.PostID 
                            JOIN positions p ON j.RequisitionID = (SELECT RequisitionID FROM recruitment_requisitions WHERE PositionID = p.PositionID LIMIT 1) 
                            JOIN department d ON p.DepartmentID = d.DepartmentID 
                            WHERE a.ApplicantID = ?");
    // Actually, job_postings has Department field as varchar, let's look at recruitment_requisitions or positions
    // Let's simplify and get it from job_postings Title or Department if possible, or just join carefully.
    
    // Better Join: applicants -> job_postings -> recruitment_requisitions -> positions -> department
    $stmt = $conn->prepare("SELECT d.DepartmentID 
                            FROM applicants a 
                            JOIN job_postings jp ON a.PostID = jp.PostID 
                            JOIN recruitment_requisitions rr ON jp.RequisitionID = rr.RequisitionID 
                            JOIN positions p ON rr.PositionID = p.PositionID 
                            JOIN department d ON p.DepartmentID = d.DepartmentID 
                            WHERE a.ApplicantID = ?");
    $stmt->bind_param("i", $applicant_id);
    $stmt->execute();
    $dept_id = $stmt->get_result()->fetch_assoc()['DepartmentID'] ?? 0;

    // 2. Map Dept ID to Category ID
    $cat_map = [
        1 => 6, // Admin -> Admin Cat
        2 => 2, // HR -> HR Cat
        3 => 3, // Finance -> Finance Cat
        4 => 4, // Logistics -> Logistics Cat
        5 => 5  // Core Transaction -> Micro Cat
    ];
    $target_cat_id = $cat_map[$dept_id] ?? 0;

    // 3. Pull 15 random questions (Common Cat (ID 1) + Target Cat)
    $query = "SELECT q.id, q.question_text, q.option_a, q.option_b, q.option_c, q.option_d 
              FROM competency_questions q
              JOIN competencies c ON q.competency_id = c.id
              WHERE c.category_id = 1 OR c.category_id = ?
              ORDER BY RAND() LIMIT 15";
    
    $stmtQ = $conn->prepare($query);
    $stmtQ->bind_param("i", $target_cat_id);
    $stmtQ->execute();
    $res = $stmtQ->get_result();
    $questions = [];
    while ($row = $res->fetch_assoc()) {
        $questions[] = $row;
    }

    if (count($questions) > 0) {
        echo json_encode(['success' => true, 'data' => $questions]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Not enough questions in the bank for this department.']);
    }
}

elseif ($action === 'submit_exam') {
    $applicant_id = $_POST['applicant_id'] ?? null;
    $answers = json_decode($_POST['answers'] ?? '[]', true); // Format: { question_id: answer }
    
    if (!$applicant_id || empty($answers)) {
        echo json_encode(['success' => false, 'message' => 'Missing data']);
        exit;
    }

    $score = 0;
    foreach ($answers as $q_id => $ans) {
        $stmt = $conn->prepare("SELECT correct_answer FROM competency_questions WHERE id = ?");
        $stmt->bind_param("i", $q_id);
        $stmt->execute();
        $correct = $stmt->get_result()->fetch_assoc()['correct_answer'] ?? '';
        if ($correct === $ans) $score++;
    }

    $stmtU = $conn->prepare("UPDATE applicants SET ExamScore = ?, ExamStatus = 'Completed' WHERE ApplicantID = ?");
    $stmtU->bind_param("ii", $score, $applicant_id);
    
    if ($stmtU->execute()) {
        echo json_encode(['success' => true, 'message' => 'Exam submitted successfully!', 'score' => $score]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save results.']);
    }
}

else {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>
