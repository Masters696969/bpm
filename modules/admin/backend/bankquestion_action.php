<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../../config/config.php';

$action = isset($_POST['action']) ? $_POST['action'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'fetch_questions') {
        $query = "SELECT q.*, c.name as competency_name, cat.id as category_id, cat.name as category_name 
                  FROM competency_questions q
                  JOIN competencies c ON q.competency_id = c.id
                  LEFT JOIN competency_categories cat ON c.category_id = cat.id
                  ORDER BY q.id DESC";
        $result = $conn->query($query);
        $questions = [];
        while ($row = $result->fetch_assoc()) {
            $questions[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $questions]);
    }
    else if ($action === 'fetch_categories') {
        $query = "SELECT id, name FROM competency_categories ORDER BY name ASC";
        $result = $conn->query($query);
        $categories = [];
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $categories]);
    }
    else if ($action === 'fetch_competencies') {
        $query = "SELECT c.id, c.name, cat.name as category_name 
                  FROM competencies c 
                  LEFT JOIN competency_categories cat ON c.category_id = cat.id 
                  ORDER BY cat.name ASC, c.name ASC";
        $result = $conn->query($query);
        $competencies = [];
        while ($row = $result->fetch_assoc()) {
            $competencies[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $competencies]);
    }
    else if ($action === 'add' || $action === 'update') {
        $competency_id = intval($_POST['competency_id']);
        $question_text = trim($_POST['question_text']);
        $correct_answer = $_POST['correct_answer'];
        $is_active = isset($_POST['is_active']) ? intval($_POST['is_active']) : 1;

        $option_a = isset($_POST['option_a']) ? trim($_POST['option_a']) : null;
        $option_b = isset($_POST['option_b']) ? trim($_POST['option_b']) : null;
        $option_c = isset($_POST['option_c']) ? trim($_POST['option_c']) : null;
        $option_d = isset($_POST['option_d']) ? trim($_POST['option_d']) : null;

        if ($action === 'add') {
            $stmt = $conn->prepare("INSERT INTO competency_questions (competency_id, question_text, option_a, option_b, option_c, option_d, correct_answer, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("issssssi", $competency_id, $question_text, $option_a, $option_b, $option_c, $option_d, $correct_answer, $is_active);
        } else {
            $id = intval($_POST['id']);
            $stmt = $conn->prepare("UPDATE competency_questions SET competency_id = ?, question_text = ?, option_a = ?, option_b = ?, option_c = ?, option_d = ?, correct_answer = ?, is_active = ? WHERE id = ?");
            $stmt->bind_param("issssssii", $competency_id, $question_text, $option_a, $option_b, $option_c, $option_d, $correct_answer, $is_active, $id);
        }

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Question saved successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error saving question: ' . $conn->error]);
        }
    }
    else if ($action === 'delete') {
        $id = intval($_POST['id']);
        $stmt = $conn->prepare("DELETE FROM competency_questions WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Question deleted successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error deleting question: ' . $conn->error]);
        }
    }
    else if ($action === 'toggle_status') {
        $id = intval($_POST['id']);
        $status = intval($_POST['status']);
        $stmt = $conn->prepare("UPDATE competency_questions SET is_active = ? WHERE id = ?");
        $stmt->bind_param("ii", $status, $id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }

    if (isset($stmt)) $stmt->close();
    $conn->close();
}
?>
