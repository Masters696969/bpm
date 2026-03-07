<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

$conn = new mysqli("localhost", "root", "", "hr4");
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit();
}

$inputData = json_decode(file_get_contents('php://input'), true);
$action = $_GET['action'] ?? $_POST['action'] ?? ($inputData['action'] ?? '');

try {
    if ($action === 'fetch_endorsed_proposals') {
        $sql = "SELECT p.BatchReference, p.Reason, p.Status, MAX(p.UpdatedAt) as UpdatedAt,
                       p.ProposedBy, u.Username as ProposedByName,
                       COUNT(p.BatchReference) as TotalChanges
                FROM merit_proposals p
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
        $stmt = $conn->prepare("SELECT * FROM merit_proposals WHERE BatchReference = ? AND Status = 'Endorsed'");
        $stmt->bind_param("s", $batchRef);
        $stmt->execute();
        $res = $stmt->get_result();
        $details = [];
        while ($row = $res->fetch_assoc()) {
            $details[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $details]);
        exit;
    } elseif ($action === 'approve_proposal') {
        $batchRef = $inputData['batch_reference'] ?? null;
        if (!$batchRef) {
            echo json_encode(['success' => false, 'message' => 'Batch Reference required']);
            exit;
        }

        $conn->begin_transaction();

        // Mark the proposal as 'Manager Approved' to forward to Finance
        $markStmt = $conn->prepare("UPDATE merit_proposals SET Status = 'Manager Approved', UpdatedAt = NOW() WHERE BatchReference = ? AND Status = 'Endorsed'");
        $markStmt->bind_param("s", $batchRef);
        $markStmt->execute();

        // Send notifications to Supervisor and Analyst (Compensation Module)
        $notifMsg = "Your merit matrix proposal batch $batchRef has been APPROVED by the Manager and forwarded to Finance.";
        $notifyStmt = $conn->prepare("INSERT INTO system_notifications (module_target, message, role_target) VALUES ('compensation_cycle', ?, 'all')");
        $notifyStmt->bind_param("s", $notifMsg);
        $notifyStmt->execute();

        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Merit Matrix proposal forwarded to Finance successfully.']);
        exit;

    } elseif ($action === 'reject_proposal') {
        $batchRef = $inputData['batch_reference'] ?? null;
        if (!$batchRef) {
            echo json_encode(['success' => false, 'message' => 'Batch Reference required']);
            exit;
        }

        $stmt = $conn->prepare("UPDATE merit_proposals SET Status = 'Rejected', UpdatedAt = NOW() WHERE BatchReference = ? AND Status = 'Endorsed'");
        $stmt->bind_param("s", $batchRef);
        if ($stmt->execute()) {
            
            $notifMsg = "Your merit matrix proposal batch $batchRef was REJECTED by the manager.";
            $notifyStmt = $conn->prepare("INSERT INTO system_notifications (module_target, message, role_target) VALUES ('compensation_cycle', ?, 'all')");
            $notifyStmt->bind_param("s", $notifMsg);
            $notifyStmt->execute();

            echo json_encode(['success' => true, 'message' => 'Proposal rejected successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
        }
        exit;
    } elseif ($action === 'fetch_manager_approved') {
        $sql = "SELECT p.BatchReference, p.Reason, p.Status, MAX(p.UpdatedAt) as CreatedAt,
                       p.ProposedBy, u.Username as ProposedByName,
                       COUNT(p.BatchReference) as TotalChanges
                FROM merit_proposals p
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
        $stmt = $conn->prepare("SELECT * FROM merit_proposals WHERE BatchReference = ? AND Status = 'Manager Approved'");
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
        
        $stmt = $conn->prepare("SELECT * FROM merit_proposals WHERE BatchReference = ?");
        $stmt->bind_param("s", $batchRef);
        $stmt->execute();
        $result = $stmt->get_result();
        $proposals = [];
        $overallMaxIncrease = 0;
        while ($row = $result->fetch_assoc()) {
            $proposals[] = $row;
            if (floatval($row['ProposedMaxIncrease']) > $overallMaxIncrease) {
                $overallMaxIncrease = floatval($row['ProposedMaxIncrease']);
            }
        }

        if (empty($proposals)) {
             echo json_encode(['success' => false, 'message' => 'No proposals found for batch: ' . $batchRef]);
             exit;
        }

        // Fetch actual BaseSalary for all active employees
        $sqlSum = "SELECT BaseSalary FROM employmentinformation e JOIN employee em ON e.EmployeeID = em.EmployeeID WHERE e.EmploymentStatus = 'Regular'";
        $resSum = $conn->query($sqlSum);
        $salaries = [];
        $totalMonthlyPayroll = 0;
        while ($row = $resSum->fetch_assoc()) {
            $val = floatval($row['BaseSalary']);
            $salaries[] = $val;
            $totalMonthlyPayroll += $val;
        }
        $headcount = count($salaries);
        
        // Simulation based on the Overall Max Increase proposed in the batch (target: 5.00%)
        // The user specifically asks to multiply Proposed Max Increase by current BaseSalary of all 8 employees.
        $maxExposureMonthly = $totalMonthlyPayroll * ($overallMaxIncrease / 100);
        $annualizedExposure = $maxExposureMonthly * 12;
        
        $gradesImpact = [];
        foreach ($proposals as $p) {
            $newMax = floatval($p['ProposedMaxIncrease']);
            $oldMax = 5.0; // Assuming 5.0% as the standard baseline per user instruction
            
            $gradesImpact[] = [
                'performance_rating' => $p['performance_rating'],
                'compa_ratio_range' => $p['compa_ratio_range'],
                'old_max' => number_format($oldMax, 1) . '%', 
                'new_max' => number_format($newMax, 2) . '%',
                'variance' => number_format($newMax - $oldMax, 2) . '%'
            ];
        }

        echo json_encode([
            'success' => true, 
            'data' => [
                'eligibleHeadcount' => $headcount,
                'maxBudgetExposure' => $maxExposureMonthly,
                'projectedPerformanceCost' => $maxExposureMonthly, // Simplified as "New Money" needed
                'annualizedOutcome' => $annualizedExposure,
                'gradesImpact' => $gradesImpact
            ]
        ]);
        exit;

    } elseif ($action === 'apply_batch') {
        $batchRef = $inputData['batch_reference'] ?? null;
        if (!$batchRef) {
            echo json_encode(['success' => false, 'message' => 'Batch Reference required']);
            exit;
        }

        $conn->begin_transaction();

        // 1. Fetch exact changes
        $stmt = $conn->prepare("SELECT * FROM merit_proposals WHERE BatchReference = ? AND Status = 'Manager Approved'");
        $stmt->bind_param("s", $batchRef);
        $stmt->execute();
        $changes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        if (empty($changes)) {
            throw new Exception("No approved proposal found for this batch reference.");
        }

        // 2. Apply the changes
        $updateStmt = $conn->prepare("UPDATE merit_matrix_settings SET min_increase_pct = ?, max_increase_pct = ? WHERE performance_rating = ? AND compa_ratio_range = ? AND period_id = ?");
        $insertStmt = $conn->prepare("INSERT INTO merit_matrix_settings (period_id, performance_rating, compa_ratio_range, min_increase_pct, max_increase_pct) VALUES (?, ?, ?, ?, ?)");
        $checkStmt = $conn->prepare("SELECT matrix_id FROM merit_matrix_settings WHERE performance_rating = ? AND compa_ratio_range = ? AND period_id = ?");

        foreach ($changes as $change) {
            $period_id = $change['period_id'];
            $rating = $change['performance_rating'];
            $range = $change['compa_ratio_range'];
            $minInc = $change['ProposedMinIncrease'];
            $maxInc = $change['ProposedMaxIncrease'];

            $checkStmt->bind_param("dsi", $rating, $range, $period_id);
            $checkStmt->execute();
            if ($checkStmt->get_result()->num_rows > 0) {
                $updateStmt->bind_param("dddsi", $minInc, $maxInc, $rating, $range, $period_id);
                $updateStmt->execute();
            } else {
                $insertStmt->bind_param("idsdd", $period_id, $rating, $range, $minInc, $maxInc);
                $insertStmt->execute();
            }
        }

        // 3. Status applied
        $markStmt = $conn->prepare("UPDATE merit_proposals SET Status = 'Applied', UpdatedAt = NOW() WHERE BatchReference = ?");
        $markStmt->bind_param("s", $batchRef);
        $markStmt->execute();

        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Merit Matrix adjustments formally applied.']);
        exit;

    } elseif ($action === 'reject_batch') {
        $batchRef = $inputData['batch_reference'] ?? null;
        if (!$batchRef) {
            echo json_encode(['success' => false, 'message' => 'Batch Reference required']);
            exit;
        }
        $stmt = $conn->prepare("UPDATE merit_proposals SET Status = 'Rejected', UpdatedAt = NOW() WHERE BatchReference = ? AND Status = 'Manager Approved'");
        $stmt->bind_param("s", $batchRef);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Proposal rejected successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
        }
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'Invalid action']);

} catch (Exception $e) {
    if (isset($conn) && $conn->ping()) {
        $conn->rollback();
    }
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?>
