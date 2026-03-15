<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../../config/config.php';

$action = isset($_POST['action']) ? $_POST['action'] : 'add';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'add') {
        // Basic validation
        $category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;
        $name = isset($_POST['competency_name']) ? trim($_POST['competency_name']) : '';
        $description = isset($_POST['description']) ? trim($_POST['description']) : '';

        if (empty($category_id) || empty($name) || empty($description)) {
            echo json_encode(['success' => false, 'message' => 'All fields are required.']);
            exit;
        }

        $stmt = $conn->prepare("INSERT INTO competencies (category_id, name, description) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $category_id, $name, $description);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Competency added successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error adding competency: ' . $conn->error]);
        }
    } 
    else if ($action === 'update') {
        $id = isset($_POST['competency_id']) ? intval($_POST['competency_id']) : 0;
        $category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;
        $name = isset($_POST['competency_name']) ? trim($_POST['competency_name']) : '';
        $description = isset($_POST['description']) ? trim($_POST['description']) : '';

        if (empty($id) || empty($category_id) || empty($name) || empty($description)) {
            echo json_encode(['success' => false, 'message' => 'All fields are required for update.']);
            exit;
        }

        $stmt = $conn->prepare("UPDATE competencies SET category_id = ?, name = ?, description = ? WHERE id = ?");
        $stmt->bind_param("issi", $category_id, $name, $description, $id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Competency updated successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error updating competency: ' . $conn->error]);
        }
    }
    else if ($action === 'delete') {
        $id = isset($_POST['competency_id']) ? intval($_POST['competency_id']) : 0;

        if (empty($id)) {
            echo json_encode(['success' => false, 'message' => 'Invalid ID for deletion.']);
            exit;
        }

        $stmt = $conn->prepare("DELETE FROM competencies WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Competency deleted successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error deleting competency: ' . $conn->error]);
        }
    }

    if (isset($stmt)) $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
