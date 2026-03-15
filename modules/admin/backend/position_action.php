<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../../config/config.php';

$action = isset($_POST['action']) ? $_POST['action'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'save_batch_assignments') {
        $pos_id = isset($_POST['position_id']) ? intval($_POST['position_id']) : 0;
        $assignments = isset($_POST['assignments']) ? $_POST['assignments'] : []; // Should be array of {comp_id, level_id}

        if (!$pos_id) {
            echo json_encode(['success' => false, 'message' => 'Position ID is required.']);
            exit;
        }

        // Start Transaction
        $conn->begin_transaction();
        try {
            // 1. Delete existing for this position to sync
            $del = $conn->prepare("DELETE FROM position_competencies WHERE position_id = ?");
            $del->bind_param("i", $pos_id);
            $del->execute();

            // 2. Insert new ones
            if (!empty($assignments)) {
                $ins = $conn->prepare("INSERT INTO position_competencies (position_id, competency_id, level_id) VALUES (?, ?, ?)");
                foreach ($assignments as $a) {
                    $c_id = intval($a['competency_id']);
                    $l_id = intval($a['level_id']);
                    if ($c_id && $l_id) {
                        $ins->bind_param("iii", $pos_id, $c_id, $l_id);
                        $ins->execute();
                    }
                }
            }

            $conn->commit();
            
            // Fetch new count for live update
            $cntQuery = $conn->prepare("SELECT COUNT(*) as new_cnt FROM position_competencies WHERE position_id = ?");
            $cntQuery->bind_param("i", $pos_id);
            $cntQuery->execute();
            $new_count = $cntQuery->get_result()->fetch_assoc()['new_cnt'];

            echo json_encode(['success' => true, 'message' => 'Competencies synced successfully!', 'new_count' => $new_count]);
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['success' => false, 'message' => 'Error saving assignments: ' . $e->getMessage()]);
        }
        exit;
    }
    else if ($action === 'delete') {
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'Invalid ID.']);
            exit;
        }

        // Get position_id before deleting
        $posQuery = $conn->prepare("SELECT position_id FROM position_competencies WHERE id = ?");
        $posQuery->bind_param("i", $id);
        $posQuery->execute();
        $pos_id = $posQuery->get_result()->fetch_assoc()['position_id'] ?? 0;

        $stmt = $conn->prepare("DELETE FROM position_competencies WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $new_count = 0;
            if ($pos_id) {
                $cntQuery = $conn->prepare("SELECT COUNT(*) as new_cnt FROM position_competencies WHERE position_id = ?");
                $cntQuery->bind_param("i", $pos_id);
                $cntQuery->execute();
                $new_count = $cntQuery->get_result()->fetch_assoc()['new_cnt'];
            }
            echo json_encode(['success' => true, 'message' => 'Mapping removed successfully!', 'new_count' => $new_count]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error removing mapping: ' . $conn->error]);
        }
    }
    else if ($action === 'get_position_competencies') {
        $pos_id = isset($_POST['position_id']) ? intval($_POST['position_id']) : 0;
        if (!$pos_id) {
            echo json_encode(['success' => false, 'message' => 'Position ID is required.']);
            exit;
        }

        // Fetch Position info (including DepartmentID)
        $posQuery = "SELECT p.PositionName, p.DepartmentID, d.DepartmentName 
                     FROM positions p 
                     JOIN department d ON p.DepartmentID = d.DepartmentID 
                     WHERE p.PositionID = ?";
        $stmtP = $conn->prepare($posQuery);
        $stmtP->bind_param("i", $pos_id);
        $stmtP->execute();
        $posInfo = $stmtP->get_result()->fetch_assoc();
        
        if (!$posInfo) {
            echo json_encode(['success' => false, 'message' => 'Position not found.']);
            exit;
        }

        $dept_id = $posInfo['DepartmentID'];

        // Fetch Mapped Competencies
        $query = "SELECT pc.*, c.name as competency_name, cl.name as level_name, cl.rank_level 
                  FROM position_competencies pc
                  JOIN competencies c ON pc.competency_id = c.id
                  JOIN competency_levels cl ON pc.level_id = cl.id
                  WHERE pc.position_id = ?
                  ORDER BY c.name ASC";
        $stmtM = $conn->prepare($query);
        $stmtM->bind_param("i", $pos_id);
        $stmtM->execute();
        $res = $stmtM->get_result();
        $mappings = [];
        while ($row = $res->fetch_assoc()) {
            $mappings[] = $row;
        }

        // Fetch ALL Available Competencies for this Department (or Common)
        // Ordered by Dept-Specific first, then Common
        $availQuery = "SELECT c.id, c.name, cat.department_id as comp_dept_id 
                       FROM competencies c
                       JOIN competency_categories cat ON c.category_id = cat.id
                       WHERE cat.department_id = ? OR cat.department_id IS NULL OR cat.department_id = 0
                       ORDER BY (cat.department_id IS NOT NULL AND cat.department_id != 0) DESC, c.name ASC";
        $stmtA = $conn->prepare($availQuery);
        $stmtA->bind_param("i", $dept_id);
        $stmtA->execute();
        $resA = $stmtA->get_result();
        $available = [];
        while ($row = $resA->fetch_assoc()) {
            $available[] = $row;
        }
        
        echo json_encode([
            'success' => true, 
            'position_name' => $posInfo['PositionName'],
            'department_name' => $posInfo['DepartmentName'],
            'data' => $mappings,
            'available_competencies' => $available
        ]);
        $stmtP->close();
        $stmtM->close();
        $stmtA->close();
        exit;
    }

    if (isset($stmt)) $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>
