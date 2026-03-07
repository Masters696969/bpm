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

    } elseif ($action === 'fetch_simulations') {
        $sql = "SELECT sd.DraftID as BatchReference, sd.CycleName, sd.TotalCost, sd.CreatedAt, 
                       u.Username as ProposedByName, 'Compensation' as Department
                FROM simulation_drafts sd
                LEFT JOIN useraccounts u ON sd.ProposedBy = u.AccountID
                WHERE sd.Status = 'Submitted'
                ORDER BY sd.CreatedAt DESC";
        
        $result = $conn->query($sql);
        $simulations = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $simulations[] = $row;
            }
        }
        echo json_encode(['success' => true, 'data' => $simulations]);
        exit;

    } elseif ($action === 'fetch_simulation_details') {
        $batchRef = $_GET['batch_reference'] ?? '';
        if (!$batchRef) {
            echo json_encode(['success' => false, 'message' => 'Batch Reference required']);
            exit;
        }
        $stmt = $conn->prepare("SELECT EmployeeData FROM simulation_drafts WHERE DraftID = ?");
        $stmt->bind_param("i", $batchRef);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            echo json_encode(['success' => true, 'data' => json_decode($row['EmployeeData'], true)]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Simulation not found']);
        }
        exit;

    } elseif ($action === 'endorse_simulation') {
        $input = json_decode(file_get_contents('php://input'), true);
        $batchRef = $input['batch_reference'] ?? null;
        if (!$batchRef) {
            echo json_encode(['success' => false, 'message' => 'Batch Reference required']);
            exit;
        }
        $stmt = $conn->prepare("UPDATE simulation_drafts SET Status = 'Endorsed', UpdatedAt = NOW() WHERE DraftID = ? AND Status = 'Submitted'");
        $stmt->bind_param("i", $batchRef);
        if ($stmt->execute()) {
            // Notify Manager
            $notifMsg = "A new compensation simulation has been endorsed by supervisor.";
            $notifStmt = $conn->prepare("INSERT INTO system_notifications (module_target, message, role_target) VALUES ('compensation_review', ?, 'hr manager')");
            $notifStmt->bind_param("s", $notifMsg);
            $notifStmt->execute();

            echo json_encode(['success' => true, 'message' => 'Simulation endorsed successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
        }
        exit;
    } elseif ($action === 'reject_simulation') {
        $input = json_decode(file_get_contents('php://input'), true);
        $batchRef = $input['batch_reference'] ?? null;
        if (!$batchRef) {
            echo json_encode(['success' => false, 'message' => 'Batch Reference required']);
            exit;
        }
        $stmt = $conn->prepare("UPDATE simulation_drafts SET Status = 'Rejected', UpdatedAt = NOW() WHERE DraftID = ? AND Status = 'Submitted'");
        $stmt->bind_param("i", $batchRef);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Simulation rejected.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
        }
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
            // Notify Manager
            $notifMsgManager = "New salary scale proposal endorsement from supervisor for batch {$batchRef}.";
            $notifStmt = $conn->prepare("INSERT INTO system_notifications (module_target, message, role_target) VALUES ('compensation_cycle', ?, 'hr manager')");
            $notifStmt->bind_param("s", $notifMsgManager);
            $notifStmt->execute();

            // Notify Supervisor (Confirmation)
            $notifMsgSupervisor = "Statutory proposed by you has been endorsed to manager. Batch: {$batchRef}";
            $notifStmt = $conn->prepare("INSERT INTO system_notifications (module_target, message, role_target) VALUES ('compensation_cycle', ?, 'supervisor')");
            $notifStmt->bind_param("s", $notifMsgSupervisor);
            $notifStmt->execute();

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
    } elseif ($action === 'fetch_statutory_proposals') {
        $sql = "SELECT p.BatchReference, p.Reason, p.Status, MAX(p.CreatedAt) as CreatedAt,
                       p.ProposedBy, u.Username as ProposedByName,
                       COUNT(p.ProposalID) as TotalChanges, p.ProofPath
                FROM statutory_proposals p
                LEFT JOIN useraccounts u ON p.ProposedBy = u.AccountID
                WHERE p.Status = 'Pending'
                GROUP BY p.BatchReference, p.Reason, p.Status, p.ProposedBy, u.Username, p.ProofPath
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
    } elseif ($action === 'fetch_statutory_details') {
        $batchRef = $_GET['batch_reference'] ?? '';
        if (!$batchRef) {
            echo json_encode(['success' => false, 'message' => 'Batch Reference required']);
            exit;
        }
        $stmt = $conn->prepare("SELECT * FROM statutory_proposals WHERE BatchReference = ? AND Status = 'Pending'");
        $stmt->bind_param("s", $batchRef);
        $stmt->execute();
        $res = $stmt->get_result();
        $details = [];
        while ($row = $res->fetch_assoc()) {
            $details[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $details]);
        exit;
    } elseif ($action === 'reject_statutory_batch') {
        $input = json_decode(file_get_contents('php://input'), true);
        $batchRef = $input['batch_reference'] ?? null;
        if (!$batchRef) {
            echo json_encode(['success' => false, 'message' => 'Batch Reference required']);
            exit;
        }
        $stmt = $conn->prepare("UPDATE statutory_proposals SET Status = 'Rejected', UpdatedAt = NOW() WHERE BatchReference = ? AND Status = 'Pending'");
        $stmt->bind_param("s", $batchRef);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Statutory proposal rejected.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
        }
        exit;
    } elseif ($action === 'endorse_statutory_batch') {
        $input = json_decode(file_get_contents('php://input'), true);
        $batchRef = $input['batch_reference'] ?? null;
        if (!$batchRef) {
            echo json_encode(['success' => false, 'message' => 'Batch Reference required']);
            exit;
        }
        $stmt = $conn->prepare("UPDATE statutory_proposals SET Status = 'Endorsed', UpdatedAt = NOW() WHERE BatchReference = ? AND Status = 'Pending'");
        $stmt->bind_param("s", $batchRef);
        if ($stmt->execute()) {
             // Notify through system notifications
             // Notify Manager
            $notifMsgManager = "New statutory proposal endorsement from supervisor for batch {$batchRef}.";
            $notifStmt = $conn->prepare("INSERT INTO system_notifications (module_target, message, role_target) VALUES ('compensation_cycle', ?, 'hr manager')");
            $notifStmt->bind_param("s", $notifMsgManager);
            $notifStmt->execute();

            // Notify Supervisor (Confirmation)
            $notifMsgSupervisor = "Statutory proposed by you has been endorsed to manager. Batch: {$batchRef}";
            $notifStmt = $conn->prepare("INSERT INTO system_notifications (module_target, message, role_target) VALUES ('compensation_cycle', ?, 'supervisor')");
            $notifStmt->bind_param("s", $notifMsgSupervisor);
            $notifStmt->execute();

            echo json_encode(['success' => true, 'message' => 'Statutory proposals endorsed successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
        }
        exit;
    } elseif ($action === 'reject_statutory_batch') {
        $input = json_decode(file_get_contents('php://input'), true);
        $batchRef = $input['batch_reference'] ?? null;
        if (!$batchRef) {
            echo json_encode(['success' => false, 'message' => 'Batch Reference required']);
            exit;
        }
        $stmt = $conn->prepare("UPDATE statutory_proposals SET Status = 'Rejected', UpdatedAt = NOW() WHERE BatchReference = ? AND Status = 'Pending'");
        $stmt->bind_param("s", $batchRef);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Statutory proposals rejected.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
        }
        exit;
    } elseif ($action === 'fetch_merit_proposals') {
        $sql = "SELECT p.BatchReference, p.Reason, p.Status, MAX(p.CreatedAt) as CreatedAt,
                       p.ProposedBy, u.Username as ProposedByName,
                       COUNT(p.BatchReference) as TotalChanges
                FROM merit_proposals p
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
    } elseif ($action === 'fetch_merit_details') {
        $batchRef = $_GET['batch_reference'] ?? '';
        if (!$batchRef) {
            echo json_encode(['success' => false, 'message' => 'Batch Reference required']);
            exit;
        }
        $stmt = $conn->prepare("SELECT * FROM merit_proposals WHERE BatchReference = ? AND Status = 'Pending'");
        $stmt->bind_param("s", $batchRef);
        $stmt->execute();
        $res = $stmt->get_result();
        $details = [];
        while ($row = $res->fetch_assoc()) {
            $details[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $details]);
        exit;
    } elseif ($action === 'endorse_merit_batch') {
        $input = json_decode(file_get_contents('php://input'), true);
        $batchRef = $input['batch_reference'] ?? null;
        if (!$batchRef) {
            echo json_encode(['success' => false, 'message' => 'Batch Reference required']);
            exit;
        }
        $stmt = $conn->prepare("UPDATE merit_proposals SET Status = 'Endorsed', UpdatedAt = NOW() WHERE BatchReference = ? AND Status = 'Pending'");
        $stmt->bind_param("s", $batchRef);
        if ($stmt->execute()) {
             // Notify Manager
            $notifMsgManager = "New merit matrix proposal endorsement from supervisor for batch {$batchRef}.";
            $notifStmt = $conn->prepare("INSERT INTO system_notifications (module_target, message, role_target) VALUES ('compensation_cycle', ?, 'hr manager')");
            $notifStmt->bind_param("s", $notifMsgManager);
            $notifStmt->execute();

            echo json_encode(['success' => true, 'message' => 'Merit proposals endorsed successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
        }
        exit;
    } elseif ($action === 'reject_merit_batch') {
        $input = json_decode(file_get_contents('php://input'), true);
        $batchRef = $input['batch_reference'] ?? null;
        if (!$batchRef) {
            echo json_encode(['success' => false, 'message' => 'Batch Reference required']);
            exit;
        }
        $stmt = $conn->prepare("UPDATE merit_proposals SET Status = 'Rejected', UpdatedAt = NOW() WHERE BatchReference = ? AND Status = 'Pending'");
        $stmt->bind_param("s", $batchRef);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Merit proposals rejected.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
        }
        exit;
    } elseif ($action === 'fetch_allowance_proposals') {
        $sql = "SELECT p.BatchReference, p.Reason, p.Status, MAX(p.CreatedAt) as CreatedAt,
                       p.ProposedBy, u.Username as ProposedByName,
                       COUNT(p.BatchReference) as TotalChanges
                FROM allowance_proposals p
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
    } elseif ($action === 'fetch_allowance_details') {
        $batchRef = $_GET['batch_reference'] ?? '';
        if (!$batchRef) {
            echo json_encode(['success' => false, 'message' => 'Batch Reference required']);
            exit;
        }
        $sql = "SELECT p.*, sg.GradeLevel, sg.GradeName, at.AllowanceName, COALESCE(ga.Amount, 0) AS OldAmount
                FROM allowance_proposals p
                JOIN salary_grades sg ON p.SalaryGradeID = sg.SalaryGradeID
                JOIN allowance_types at ON p.AllowanceTypeID = at.AllowanceTypeID
                LEFT JOIN grade_allowances ga ON p.SalaryGradeID = ga.SalaryGradeID AND p.AllowanceTypeID = ga.AllowanceTypeID
                WHERE p.BatchReference = ? AND p.Status = 'Pending'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $batchRef);
        $stmt->execute();
        $res = $stmt->get_result();
        $details = [];
        while ($row = $res->fetch_assoc()) {
            $details[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $details]);
        exit;
    } elseif ($action === 'endorse_allowance_batch') {
        $input = json_decode(file_get_contents('php://input'), true);
        $batchRef = $input['batch_reference'] ?? null;
        if (!$batchRef) {
            echo json_encode(['success' => false, 'message' => 'Batch Reference required']);
            exit;
        }
        $stmt = $conn->prepare("UPDATE allowance_proposals SET Status = 'Endorsed', UpdatedAt = NOW() WHERE BatchReference = ? AND Status = 'Pending'");
        $stmt->bind_param("s", $batchRef);
        if ($stmt->execute()) {
             // Notify Manager
            $notifMsgManager = "New allowance proposal endorsement from supervisor for batch {$batchRef}.";
            $notifStmt = $conn->prepare("INSERT INTO system_notifications (module_target, message, role_target) VALUES ('compensation_cycle', ?, 'hr manager')");
            $notifStmt->bind_param("s", $notifMsgManager);
            $notifStmt->execute();

            echo json_encode(['success' => true, 'message' => 'Allowance proposals endorsed successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
        }
        exit;
    } elseif ($action === 'reject_allowance_batch') {
        $input = json_decode(file_get_contents('php://input'), true);
        $batchRef = $input['batch_reference'] ?? null;
        if (!$batchRef) {
            echo json_encode(['success' => false, 'message' => 'Batch Reference required']);
            exit;
        }
        $stmt = $conn->prepare("UPDATE allowance_proposals SET Status = 'Rejected', UpdatedAt = NOW() WHERE BatchReference = ? AND Status = 'Pending'");
        $stmt->bind_param("s", $batchRef);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Allowance proposals rejected.']);
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
