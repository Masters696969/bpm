<?php
require_once '../../config/config.php';
header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? $_POST['action'] ?? $_GET['action'] ?? '';

try {
    if ($action === 'fetch_endorsed') {
        $sql = "SELECT p.BatchReference, p.Reason, p.Status, MAX(p.CreatedAt) as CreatedAt,
                       p.ProposedBy, u.Username as ProposedByName,
                       COUNT(p.ProposalID) as TotalChanges
                FROM salary_grade_proposals p
                LEFT JOIN useraccounts u ON p.ProposedBy = u.AccountID
                WHERE p.Status = 'Endorsed'
                GROUP BY p.BatchReference, p.Reason, p.Status, p.ProposedBy, u.Username
                ORDER BY MAX(p.UpdatedAt) DESC";
        $result = $conn->query($sql);
        $proposals = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $proposals[] = $row;
            }
        }
        echo json_encode(['success' => true, 'data' => $proposals]);
        exit;
    } elseif ($action === 'fetch_proposal_details') {
        $batchRef = $_GET['batch_reference'] ?? '';
        if (!$batchRef) {
            echo json_encode(['success' => false, 'message' => 'Batch Reference required']);
            exit;
        }
        $stmt = $conn->prepare("SELECT p.*, sg.GradeLevel, sg.GradeName, sg.MinSalary AS OldMin, sg.MaxSalary AS OldMax
                                FROM salary_grade_proposals p
                                JOIN salary_grades sg ON p.SalaryGradeID = sg.SalaryGradeID
                                WHERE p.BatchReference = ? AND p.Status = 'Endorsed'
                                ORDER BY sg.GradeLevel ASC");
        $stmt->bind_param("s", $batchRef);
        $stmt->execute();
        $res = $stmt->get_result();
        $details = [];
        while ($row = $res->fetch_assoc()) {
            $details[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $details]);
        exit;
    } elseif ($action === 'apply_batch') {
        $input = json_decode(file_get_contents('php://input'), true);
        $batchRef = $input['batch_reference'] ?? null;
        if (!$batchRef) {
            echo json_encode(['success' => false, 'message' => 'Batch Reference required']);
            exit;
        }

        $conn->begin_transaction();

        // Get proposal details in batch
        $stmt = $conn->prepare("SELECT ProposalID, SalaryGradeID, ProposedMinSalary, ProposedMaxSalary FROM salary_grade_proposals WHERE BatchReference = ? AND Status = 'Endorsed'");
        $stmt->bind_param("s", $batchRef);
        $stmt->execute();
        $res = $stmt->get_result();
        
        $proposalsFound = false;
        while ($row = $res->fetch_assoc()) {
            $proposalsFound = true;
            $gradeId = $row['SalaryGradeID'];
            $newMin = $row['ProposedMinSalary'];
            $newMax = $row['ProposedMaxSalary'];

            // Update main table
            $updateStmt = $conn->prepare("UPDATE salary_grades SET MinSalary = ?, MaxSalary = ? WHERE SalaryGradeID = ?");
            $updateStmt->bind_param("ddi", $newMin, $newMax, $gradeId);
            $updateStmt->execute();
        }

        if ($proposalsFound) {
            // Mark batch as applied
            $markStmt = $conn->prepare("UPDATE salary_grade_proposals SET Status = 'Applied', UpdatedAt = NOW() WHERE BatchReference = ? AND Status = 'Endorsed'");
            $markStmt->bind_param("s", $batchRef);
            $markStmt->execute();

            $conn->commit();
            echo json_encode(['success' => true, 'message' => 'Salary scale batch updated successfully.']);
        } else {
            $conn->rollback();
            echo json_encode(['success' => false, 'message' => 'Proposals not found or already processed.']);
        }
        exit;
    } elseif ($action === 'reject_batch') {
        $input = json_decode(file_get_contents('php://input'), true);
        $batchRef = $input['batch_reference'] ?? null;
        if (!$batchRef) {
            echo json_encode(['success' => false, 'message' => 'Batch Reference required']);
            exit;
        }
        $stmt = $conn->prepare("UPDATE salary_grade_proposals SET Status = 'Rejected', UpdatedAt = NOW() WHERE BatchReference = ? AND Status = 'Endorsed'");
        $stmt->bind_param("s", $batchRef);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Salary scale proposals rejected.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
        }
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'Invalid action ' . htmlspecialchars($action)]);

} catch (Exception $e) {
    if ($conn) {
        $conn->rollback();
    }
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
