<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../../config/config.php';

$action = isset($_POST['action']) ? $_POST['action'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'add') {
        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        $rank = isset($_POST['rank_level']) ? intval($_POST['rank_level']) : 0;
        $description = isset($_POST['description']) ? trim($_POST['description']) : '';

        if (empty($name) || empty($description) || $rank <= 0) {
            echo json_encode(['success' => false, 'message' => 'All fields are required and rank must be positive.']);
            exit;
        }

        $stmt = $conn->prepare("INSERT INTO competency_levels (rank_level, name, description) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $rank, $name, $description);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Level added successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error adding level: ' . $conn->error]);
        }
    } 
    else if ($action === 'update') {
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        $rank = isset($_POST['rank_level']) ? intval($_POST['rank_level']) : 0;
        $description = isset($_POST['description']) ? trim($_POST['description']) : '';

        if (empty($id) || empty($name) || empty($description) || $rank <= 0) {
            echo json_encode(['success' => false, 'message' => 'All fields are required.']);
            exit;
        }

        $stmt = $conn->prepare("UPDATE competency_levels SET rank_level = ?, name = ?, description = ? WHERE id = ?");
        $stmt->bind_param("issi", $rank, $name, $description, $id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Level updated successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error updating level: ' . $conn->error]);
        }
    }
    else if ($action === 'delete') {
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

        if (empty($id)) {
            echo json_encode(['success' => false, 'message' => 'Invalid ID.']);
            exit;
        }

        $stmt = $conn->prepare("DELETE FROM competency_levels WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Level deleted successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error deleting level: ' . $conn->error]);
        }
    }

    if (isset($stmt)) $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>
