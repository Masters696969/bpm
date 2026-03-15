<?php
require_once '../../../config/config.php';

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    if ($action === 'get_assigned') {
        $employeeId = $_GET['employee_id'] ?? 0;
        if (!$employeeId) throw new Exception("Invalid Employee ID");

        $sql = "SELECT ec.*, c.name as competency_name, cl.name as level_name
                FROM employee_competencies ec
                JOIN competencies c ON ec.competency_id = c.id
                JOIN competency_levels cl ON ec.level_id = cl.id
                WHERE ec.employee_id = ?
                ORDER BY ec.assessed_at DESC";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $employeeId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        
        echo json_encode(['success' => true, 'data' => $data]);

    } elseif ($action === 'get_available') {
        $deptId = $_GET['dept_id'] ?? 0;
        
        // Fetch competencies linked to this department or common (dept_id is NULL)
        $sql = "SELECT c.*, cat.name as category_name 
                FROM competencies c
                JOIN competency_categories cat ON c.category_id = cat.id
                WHERE cat.department_id = ? OR cat.department_id IS NULL
                ORDER BY cat.name, c.name";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $deptId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        
        echo json_encode(['success' => true, 'data' => $data]);

    } elseif ($action === 'save_competency') {
        $employeeId = $_POST['employee_id'] ?? 0;
        $assignmentsJson = $_POST['assignments'] ?? '[]';
        $assignments = json_decode($assignmentsJson, true);

        if (!$employeeId || empty($assignments)) throw new Exception("Missing required fields or no competencies selected");

        $conn->begin_transaction();
        try {
            foreach ($assignments as $item) {
                $competencyId = $item['competency_id'];
                $levelId = $item['level_id'];

                if (!$competencyId || !$levelId) continue;

                // Check if already exists
                $check = "SELECT id FROM employee_competencies WHERE employee_id = ? AND competency_id = ?";
                $stmtCheck = $conn->prepare($check);
                $stmtCheck->bind_param("ii", $employeeId, $competencyId);
                $stmtCheck->execute();
                $resCheck = $stmtCheck->get_result();

                if ($resCheck->num_rows > 0) {
                    // Update
                    $sql = "UPDATE employee_competencies SET level_id = ?, assessed_at = CURRENT_TIMESTAMP WHERE employee_id = ? AND competency_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("iii", $levelId, $employeeId, $competencyId);
                } else {
                    // Insert
                    $sql = "INSERT INTO employee_competencies (employee_id, competency_id, level_id) VALUES (?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("iii", $employeeId, $competencyId, $levelId);
                }
                $stmt->execute();
            }
            $conn->commit();
            echo json_encode(['success' => true, 'message' => 'Assignments saved successfully']);
        } catch (Exception $e) {
            $conn->rollback();
            throw $e;
        }

    } elseif ($action === 'get_stats') {
        $res = $conn->query("SELECT COUNT(*) FROM employee_competencies");
        $activeAssessments = ($res) ? $res->fetch_row()[0] : 0;
        echo json_encode(['success' => true, 'data' => ['active_assessments' => $activeAssessments]]);
    } elseif ($action === 'delete_competency') {
        $id = $_POST['id'] ?? 0;
        if (!$id) throw new Exception("Invalid ID");

        $sql = "DELETE FROM employee_competencies WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Competency removed successfully']);
        } else {
            throw new Exception("Execution failed: " . $stmt->error);
        }
    } else {
        throw new Exception("Invalid action: " . $action);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
