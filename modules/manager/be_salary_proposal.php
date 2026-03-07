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
    } elseif ($action === 'fetch_manager_approved') {
        $sql = "SELECT p.BatchReference, p.Reason, p.Status, MAX(p.CreatedAt) as CreatedAt,
                       p.ProposedBy, u.Username as ProposedByName,
                       COUNT(p.ProposalID) as TotalChanges
                FROM salary_grade_proposals p
                LEFT JOIN useraccounts u ON p.ProposedBy = u.AccountID
                WHERE p.Status = 'Manager Approved'
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
    } elseif ($action === 'fetch_manager_approved_details') {
        $batchRef = $_GET['batch_reference'] ?? '';
        if (!$batchRef) {
            echo json_encode(['success' => false, 'message' => 'Batch Reference required']);
            exit;
        }
        $stmt = $conn->prepare("SELECT p.*, sg.GradeLevel, sg.GradeName, sg.MinSalary AS OldMin, sg.MaxSalary AS OldMax
                                FROM salary_grade_proposals p
                                JOIN salary_grades sg ON p.SalaryGradeID = sg.SalaryGradeID
                                WHERE p.BatchReference = ? AND p.Status = 'Manager Approved'
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
    } elseif ($action === 'fetch_financial_impact') {
        $batchRef = $_GET['batch_reference'] ?? '';
        if (!$batchRef) {
            echo json_encode(['success' => false, 'message' => 'Batch Reference required']);
            exit;
        }

        $stmt = $conn->prepare("SELECT p.SalaryGradeID, p.ProposedMinSalary, p.ProposedMaxSalary, sg.GradeLevel, sg.GradeName 
                                FROM salary_grade_proposals p
                                JOIN salary_grades sg ON p.SalaryGradeID = sg.SalaryGradeID
                                WHERE p.BatchReference = ? AND p.Status = 'Manager Approved'");
        $stmt->bind_param("s", $batchRef);
        $stmt->execute();
        $proposals = $stmt->get_result();
        
        $totalImpactedHeadcount = 0;
        $totalMonthlyIncrease = 0.0;
        $totalNewPayroll = 0.0;
        $gradesImpact = [];

        while ($prop = $proposals->fetch_assoc()) {
            $sgId = $prop['SalaryGradeID'];
            $newMin = (float)$prop['ProposedMinSalary'];
            $gradeName = $prop['GradeName'];
            $gradeLevel = $prop['GradeLevel'];

            // Get active employees in this grade
            $empStmt = $conn->prepare("SELECT BaseSalary FROM employmentinformation WHERE SalaryGradeID = ? AND EmploymentStatus NOT IN ('Resigned', 'Terminated', 'Inactive')");
            $empStmt->bind_param("i", $sgId);
            $empStmt->execute();
            $emps = $empStmt->get_result();

            $gradeImpactedCount = 0;
            $gradeMonthlyIncrease = 0.0;
            $gradeCurrentPayroll = 0.0;
            $gradeTotalNewPayroll = 0.0;
            $gradeTotalHeadcount = 0;

            while ($emp = $emps->fetch_assoc()) {
                $base = (float)$emp['BaseSalary'];
                $gradeTotalHeadcount++;
                $gradeCurrentPayroll += $base;
                
                if ($base < $newMin) {
                    $diff = $newMin - $base;
                    $gradeImpactedCount++;
                    $gradeMonthlyIncrease += $diff;
                    $gradeTotalNewPayroll += $newMin;
                } else {
                    $gradeTotalNewPayroll += $base;
                }
            }

            $totalImpactedHeadcount += $gradeImpactedCount;
            $totalMonthlyIncrease += $gradeMonthlyIncrease;
            $totalNewPayroll += $gradeTotalNewPayroll;

            if ($gradeTotalHeadcount > 0) {
                $gradesImpact[] = [
                    'GradeLevel' => $gradeLevel,
                    'GradeName' => $gradeName,
                    'TotalHeadcount' => $gradeTotalHeadcount,
                    'ImpactedHeadcount' => $gradeImpactedCount,
                    'CurrentGrossMonthly' => $gradeCurrentPayroll,
                    'NewGrossMonthly' => $gradeTotalNewPayroll,
                    'MonthlyIncrease' => $gradeMonthlyIncrease
                ];
            }
        }

        echo json_encode([
            'success' => true,
            'data' => [
                'impactedHeadcount' => $totalImpactedHeadcount,
                'monthlyIncrease' => $totalMonthlyIncrease,
                'annualRequirement' => $totalNewPayroll * 12,
                'gradesImpact' => $gradesImpact
            ]
        ]);
        exit;
    } elseif ($action === 'manager_approve_batch') {
        $batchRef = $input['batch_reference'] ?? null;
        if (!$batchRef) {
            echo json_encode(['success' => false, 'message' => 'Batch Reference required']);
            exit;
        }
        $stmt = $conn->prepare("UPDATE salary_grade_proposals SET Status = 'Manager Approved', UpdatedAt = NOW() WHERE BatchReference = ? AND Status = 'Endorsed'");
        $stmt->bind_param("s", $batchRef);
        if ($stmt->execute() && $stmt->affected_rows > 0) {
            $notifMsg = "Salary scale proposal batch {$batchRef} has been approved by the Manager and forwarded to Finance.";
            $notifStmt = $conn->prepare("INSERT INTO system_notifications (module_target, message, role_target) VALUES ('finance_approval', ?, 'finance')");
            $notifStmt->bind_param("s", $notifMsg);
            $notifStmt->execute();

            echo json_encode(['success' => true, 'message' => 'Proposals approved and forwarded to Finance.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Proposals not found or already processed.']);
        }
        exit;
    } elseif ($action === 'apply_batch') {
        $batchRef = $input['batch_reference'] ?? null;
        if (!$batchRef) {
            echo json_encode(['success' => false, 'message' => 'Batch Reference required']);
            exit;
        }

        $conn->begin_transaction();

        // Get proposal details in batch
        $stmt = $conn->prepare("SELECT ProposalID, SalaryGradeID, ProposedMinSalary, ProposedMaxSalary FROM salary_grade_proposals WHERE BatchReference = ? AND Status = 'Manager Approved'");
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
            $markStmt = $conn->prepare("UPDATE salary_grade_proposals SET Status = 'Applied', UpdatedAt = NOW() WHERE BatchReference = ? AND Status = 'Manager Approved'");
            $markStmt->bind_param("s", $batchRef);
            $markStmt->execute();

            $notifMsg = "Salary scale proposal batch {$batchRef} has been officially applied by Finance.";
            $notifStmt = $conn->prepare("INSERT INTO system_notifications (module_target, message, role_target) VALUES ('compensation_cycle', ?, 'hr manager')");
            $notifStmt->bind_param("s", $notifMsg);
            $notifStmt->execute();

            $conn->commit();
            echo json_encode(['success' => true, 'message' => 'Salary scale batch updated successfully.']);
        } else {
            $conn->rollback();
            echo json_encode(['success' => false, 'message' => 'Proposals not found or already processed.']);
        }
        exit;
    } elseif ($action === 'reject_batch') {
        $batchRef = $input['batch_reference'] ?? null;
        if (!$batchRef) {
            echo json_encode(['success' => false, 'message' => 'Batch Reference required']);
            exit;
        }
        // Reject from either Endorsed (Manager) or Manager Approved (Finance)
        $stmt = $conn->prepare("UPDATE salary_grade_proposals SET Status = 'Rejected', UpdatedAt = NOW() WHERE BatchReference = ? AND Status IN ('Endorsed', 'Manager Approved')");
        $stmt->bind_param("s", $batchRef);
        if ($stmt->execute() && $stmt->affected_rows > 0) {
            $notifMsg = "Salary scale proposal batch {$batchRef} has been rejected by the HR Manager.";
            $notifStmt = $conn->prepare("INSERT INTO system_notifications (module_target, message, role_target) VALUES ('compensation_cycle', ?, 'hr manager')");
            $notifStmt->bind_param("s", $notifMsg);
            $notifStmt->execute();

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
