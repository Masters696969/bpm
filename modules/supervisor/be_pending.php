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
    if ($action === 'fetch_pending') {
        $sql = "SELECT 
                    r.RequestID, r.EmployeeID, r.RequestType, r.RequestDate, r.Status, r.RequestData, r.ProofPath,
                    e.FirstName, e.LastName, d.DepartmentName
                FROM employee_update_requests r
                JOIN employee e ON r.EmployeeID = e.EmployeeID
                LEFT JOIN employmentinformation ei ON e.EmployeeID = ei.EmployeeID
                LEFT JOIN department d ON ei.DepartmentID = d.DepartmentID
                WHERE r.Status = 'Pending Supervisor'
                ORDER BY r.RequestDate DESC";
        
        $result = $conn->query($sql);
        $requests = [];
        while ($row = $result->fetch_assoc()) {
            $requests[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $requests]);
        exit;

    } elseif ($action === 'endorse_request') {
        $input = json_decode(file_get_contents('php://input'), true);
        $requestId = $input['request_id'] ?? null;
        $supervisorId = $_SESSION['user_id'];

        if (!$requestId) {
            echo json_encode(['success' => false, 'message' => 'Request ID required']);
            exit;
        }

        $stmt = $conn->prepare("UPDATE employee_update_requests SET Status = 'Endorsed', SupervisorID = ?, SupervisorDate = NOW() WHERE RequestID = ?");
        $stmt->bind_param("ii", $supervisorId, $requestId);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Request endorsed successfully. It will now be forwarded to the Manager.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
        }
        exit;
    } elseif ($action === 'fetch_proposals') {
        $sql = "SELECT p.BatchReference, p.Reason, p.Status, MAX(p.CreatedAt) as CreatedAt,
                       p.ProposedBy, u.Username as ProposedByName,
                       COUNT(p.ProposalID) as TotalChanges
                FROM salary_grade_proposals p
                LEFT JOIN useraccounts u ON p.ProposedBy = u.AccountID
                WHERE p.Status = 'Pending'
                GROUP BY p.BatchReference, p.Reason, p.Status, p.ProposedBy, u.Username
                ORDER BY MAX(p.CreatedAt) DESC";
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
                                WHERE p.BatchReference = ? AND p.Status = 'Pending'
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
    } elseif ($action === 'endorse_batch') {
        $input = json_decode(file_get_contents('php://input'), true);
        $batchRef = $input['batch_reference'] ?? null;
        if (!$batchRef) {
            echo json_encode(['success' => false, 'message' => 'Batch Reference required']);
            exit;
        }
        $stmt = $conn->prepare("UPDATE salary_grade_proposals SET Status = 'Endorsed', UpdatedAt = NOW() WHERE BatchReference = ? AND Status = 'Pending'");
        $stmt->bind_param("s", $batchRef);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Salary scale proposals endorsed successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
        }
        exit;
    } elseif ($action === 'reject_batch') {
        $input = json_decode(file_get_contents('php://input'), true);
        $batchRef = $input['batch_reference'] ?? null;
        if (!$batchRef) {
            echo json_encode(['success' => false, 'message' => 'Batch Reference required']);
            exit;
        }
        $stmt = $conn->prepare("UPDATE salary_grade_proposals SET Status = 'Rejected', UpdatedAt = NOW() WHERE BatchReference = ? AND Status = 'Pending'");
        $stmt->bind_param("s", $batchRef);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Salary scale proposals rejected.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
        }
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'Invalid action']);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
