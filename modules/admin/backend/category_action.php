<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../../config/config.php';

$action = isset($_POST['action']) ? $_POST['action'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'add') {
        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        $subtitle = isset($_POST['subtitle']) ? trim($_POST['subtitle']) : '';

        if (empty($name) || empty($subtitle)) {
            echo json_encode(['success' => false, 'message' => 'Name and subtitle are required.']);
            exit;
        }

        // Icon is now hardcoded in the UI, so we don't save it to the DB anymore
        $stmt = $conn->prepare("INSERT INTO competency_categories (name, subtitle) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $subtitle);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Category added successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error adding category: ' . $conn->error]);
        }
    } 
    else if ($action === 'update') {
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        $subtitle = isset($_POST['subtitle']) ? trim($_POST['subtitle']) : '';

        if (empty($id) || empty($name) || empty($subtitle)) {
            echo json_encode(['success' => false, 'message' => 'All fields are required.']);
            exit;
        }

        $stmt = $conn->prepare("UPDATE competency_categories SET name = ?, subtitle = ? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $subtitle, $id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Category updated successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error updating category: ' . $conn->error]);
        }
    }
    else if ($action === 'delete') {
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

        if (empty($id)) {
            echo json_encode(['success' => false, 'message' => 'Invalid ID.']);
            exit;
        }

        // Check for dependencies
        $check = $conn->prepare("SELECT COUNT(*) as count FROM competencies WHERE category_id = ?");
        $check->bind_param("i", $id);
        $check->execute();
        $countResult = $check->get_result()->fetch_assoc();
        
        if ($countResult['count'] > 0) {
            echo json_encode(['success' => false, 'message' => 'Cannot delete. This category contains competencies.']);
            exit;
        }

        $stmt = $conn->prepare("DELETE FROM competency_categories WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Category deleted successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error deleting category: ' . $conn->error]);
        }
    }

    if (isset($stmt)) $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>
