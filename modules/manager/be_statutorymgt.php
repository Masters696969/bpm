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
                       COUNT(p.ProposalID) as TotalChanges, p.ProofPath
                FROM statutory_proposals p
                LEFT JOIN useraccounts u ON p.ProposedBy = u.AccountID
                WHERE p.Status = 'Endorsed'
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
    } elseif ($action === 'fetch_proposal_details') {
        $batchRef = $_GET['batch_reference'] ?? '';
        if (!$batchRef) {
            echo json_encode(['success' => false, 'message' => 'Batch Reference required']);
            exit;
        }
        $stmt = $conn->prepare("SELECT * FROM statutory_proposals WHERE BatchReference = ? AND Status = 'Endorsed' ORDER BY Category, FieldName");
        $stmt->bind_param("s", $batchRef);
        $stmt->execute();
        $res = $stmt->get_result();
        $details = [];
        while ($row = $res->fetch_assoc()) {
            $details[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $details]);
        exit;
    } elseif ($action === 'manager_approve_batch') {
        $batchRef = $input['batch_reference'] ?? null;
        if (!$batchRef) {
            echo json_encode(['success' => false, 'message' => 'Batch Reference required']);
            exit;
        }

        $stmt = $conn->prepare("UPDATE statutory_proposals SET Status = 'Manager Approved', UpdatedAt = NOW() WHERE BatchReference = ? AND Status = 'Endorsed'");
        $stmt->bind_param("s", $batchRef);
        
        if ($stmt->execute() && $stmt->affected_rows > 0) {
            $notifMsg = "Statutory adjustment batch {$batchRef} has been approved by the Manager and forwarded to Finance.";
            $notifStmt = $conn->prepare("INSERT INTO system_notifications (module_target, message, role_target) VALUES ('finance_approval', ?, 'finance')");
            $notifStmt->bind_param("s", $notifMsg);
            $notifStmt->execute();

            echo json_encode(['success' => true, 'message' => 'Proposals approved and forwarded to Finance.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Proposals not found or already processed.']);
        }
        exit;
    } elseif ($action === 'fetch_manager_approved') {
        $sql = "SELECT p.BatchReference, p.Reason, p.Status, MAX(p.CreatedAt) as CreatedAt,
                       p.ProposedBy, u.Username as ProposedByName,
                       COUNT(p.ProposalID) as TotalChanges, p.ProofPath
                FROM statutory_proposals p
                LEFT JOIN useraccounts u ON p.ProposedBy = u.AccountID
                WHERE p.Status = 'Manager Approved'
                GROUP BY p.BatchReference, p.Reason, p.Status, p.ProposedBy, u.Username, p.ProofPath
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
        $stmt = $conn->prepare("SELECT * FROM statutory_proposals WHERE BatchReference = ? AND Status = 'Manager Approved' ORDER BY Category, FieldName");
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

        // Fetch proposed changes
        $stmt = $conn->prepare("SELECT * FROM statutory_proposals WHERE BatchReference = ? AND Status = 'Manager Approved'");
        $stmt->bind_param("s", $batchRef);
        $stmt->execute();
        $proposalsRes = $stmt->get_result();
        
        $changes = [];
        while($row = $proposalsRes->fetch_assoc()) {
            $cat = $row['Category'];
            if(!isset($changes[$cat])) $changes[$cat] = [];
            $changes[$cat][$row['FieldName']] = [
                'old' => (float)$row['OldValue'],
                'new' => (float)$row['ProposedValue']
            ];
        }

        // Current active settings
        $period_id = 1;
        $sss = $conn->query("SELECT * FROM sss_settings WHERE period_id = $period_id")->fetch_assoc();
        $ph = $conn->query("SELECT * FROM philhealth_settings WHERE period_id = $period_id")->fetch_assoc();
        $pi = $conn->query("SELECT * FROM pagibig_settings WHERE period_id = $period_id")->fetch_assoc();

        // Get employees
        $empRes = $conn->query("SELECT BaseSalary FROM employmentinformation WHERE EmploymentStatus NOT IN ('Resigned', 'Terminated', 'Inactive')");
        
        $totalEmployeesAffected = 0;
        $totalMonthlyCostIncreaseER = 0.0;
        $totalMonthlyCostIncreaseEE = 0.0;
        
        $impacts = [
            'SSS' => ['er' => ['oldRate' => '-', 'newRate' => '-', 'costIncrease' => 0.0], 'ee' => ['oldRate' => '-', 'newRate' => '-', 'costIncrease' => 0.0]],
            'PhilHealth' => ['er' => ['oldRate' => '-', 'newRate' => '-', 'costIncrease' => 0.0], 'ee' => ['oldRate' => '-', 'newRate' => '-', 'costIncrease' => 0.0]],
            'Pag-IBIG' => ['er' => ['oldRate' => '-', 'newRate' => '-', 'costIncrease' => 0.0], 'ee' => ['oldRate' => '-', 'newRate' => '-', 'costIncrease' => 0.0]]
        ];

        while($emp = $empRes->fetch_assoc()) {
            $salary = (float)$emp['BaseSalary'];
            $empAffected = false;

            // SSS Calculation
            if(isset($changes['SSS'])) {
                $oldERPct = $changes['SSS']['employer_share_pct']['old'] ?? (float)$sss['employer_share_pct'];
                $newERPct = $changes['SSS']['employer_share_pct']['new'] ?? $oldERPct;
                $oldEEPct = $changes['SSS']['employee_share_pct']['old'] ?? (float)$sss['employee_share_pct'];
                $newEEPct = $changes['SSS']['employee_share_pct']['new'] ?? $oldEEPct;
                $oldMax = $changes['SSS']['max_msc_monthly']['old'] ?? (float)$sss['max_msc_monthly'];
                $newMax = $changes['SSS']['max_msc_monthly']['new'] ?? $oldMax;

                $impacts['SSS']['er']['oldRate'] = $oldERPct . '% (Max MSC: ₱' . number_format($oldMax, 0) . ')';
                $impacts['SSS']['er']['newRate'] = $newERPct . '% (Max MSC: ₱' . number_format($newMax, 0) . ')';
                $impacts['SSS']['ee']['oldRate'] = $oldEEPct . '% (Max MSC: ₱' . number_format($oldMax, 0) . ')';
                $impacts['SSS']['ee']['newRate'] = $newEEPct . '% (Max MSC: ₱' . number_format($newMax, 0) . ')';

                $oldER = min($salary, $oldMax) * ($oldERPct / 100);
                $newER = min($salary, $newMax) * ($newERPct / 100);
                $diffER = $newER - $oldER;
                
                $oldEE = min($salary, $oldMax) * ($oldEEPct / 100);
                $newEE = min($salary, $newMax) * ($newEEPct / 100);
                $diffEE = $oldEE - $newEE; // Reduction in take-home pay, positive means more money taken out (bad for EE)
                
                if($diffER != 0 || $diffEE != 0) {
                    $impacts['SSS']['er']['costIncrease'] += $diffER;
                    $impacts['SSS']['ee']['costIncrease'] += $diffEE;
                    $totalMonthlyCostIncreaseER += $diffER;
                    $totalMonthlyCostIncreaseEE += $diffEE;
                    $empAffected = true;
                }
            }

            // PhilHealth
            if(isset($changes['PhilHealth'])) {
                $oldERPct = $changes['PhilHealth']['employer_share_pct']['old'] ?? (float)$ph['employer_share_pct'];
                $newERPct = $changes['PhilHealth']['employer_share_pct']['new'] ?? $oldERPct;
                $oldEEPct = $changes['PhilHealth']['employee_share_pct']['old'] ?? (float)$ph['employee_share_pct'];
                $newEEPct = $changes['PhilHealth']['employee_share_pct']['new'] ?? $oldEEPct;
                $oldCeil = $changes['PhilHealth']['salary_ceiling']['old'] ?? (float)$ph['salary_ceiling'];
                $newCeil = $changes['PhilHealth']['salary_ceiling']['new'] ?? $oldCeil;

                $impacts['PhilHealth']['er']['oldRate'] = $oldERPct . '% (Ceiling: ₱' . number_format($oldCeil, 0) . ')';
                $impacts['PhilHealth']['er']['newRate'] = $newERPct . '% (Ceiling: ₱' . number_format($newCeil, 0) . ')';
                $impacts['PhilHealth']['ee']['oldRate'] = $oldEEPct . '% (Ceiling: ₱' . number_format($oldCeil, 0) . ')';
                $impacts['PhilHealth']['ee']['newRate'] = $newEEPct . '% (Ceiling: ₱' . number_format($newCeil, 0) . ')';

                // Use lower bound 10,000 as typical PH base minimum
                $oldBase = min(max($salary, 10000), $oldCeil);
                $newBase = min(max($salary, 10000), $newCeil);
                
                $oldER = $oldBase * ($oldERPct / 100);
                $newER = $newBase * ($newERPct / 100);
                $diffER = $newER - $oldER;

                $oldEE = $oldBase * ($oldEEPct / 100);
                $newEE = $newBase * ($newEEPct / 100);
                $diffEE = $oldEE - $newEE;

                if($diffER != 0 || $diffEE != 0) {
                    $impacts['PhilHealth']['er']['costIncrease'] += $diffER;
                    $impacts['PhilHealth']['ee']['costIncrease'] += $diffEE;
                    $totalMonthlyCostIncreaseER += $diffER;
                    $totalMonthlyCostIncreaseEE += $diffEE;
                    $empAffected = true;
                }
            }

            // Pag-IBIG
            if(isset($changes['Pag-IBIG'])) {
                $oldERCap = $changes['Pag-IBIG']['monthly_cap_er']['old'] ?? (float)$pi['monthly_cap_er'];
                $newERCap = $changes['Pag-IBIG']['monthly_cap_er']['new'] ?? $oldERCap;
                $oldEECap = $changes['Pag-IBIG']['monthly_cap_ee']['old'] ?? (float)$pi['monthly_cap_ee'];
                $newEECap = $changes['Pag-IBIG']['monthly_cap_ee']['new'] ?? $oldEECap;
                
                $impacts['Pag-IBIG']['er']['oldRate'] = 'Cap: ₱' . number_format($oldERCap, 2);
                $impacts['Pag-IBIG']['er']['newRate'] = 'Cap: ₱' . number_format($newERCap, 2);
                $impacts['Pag-IBIG']['ee']['oldRate'] = 'Cap: ₱' . number_format($oldEECap, 2);
                $impacts['Pag-IBIG']['ee']['newRate'] = 'Cap: ₱' . number_format($newEECap, 2);

                $diffER = $newERCap - $oldERCap;
                $diffEE = $oldEECap - $newEECap;

                if($diffER != 0 || $diffEE != 0) {
                    $impacts['Pag-IBIG']['er']['costIncrease'] += $diffER;
                    $impacts['Pag-IBIG']['ee']['costIncrease'] += $diffEE;
                    $totalMonthlyCostIncreaseER += $diffER;
                    $totalMonthlyCostIncreaseEE += $diffEE;
                    $empAffected = true;
                }
            }

            $totalEmployeesAffected++;
        }

        $impactArray = [];
        foreach($impacts as $type => $data) {
            if($data['er']['costIncrease'] != 0 || $data['ee']['costIncrease'] != 0 || $data['er']['oldRate'] !== '-' || $data['ee']['oldRate'] !== '-') {
                $impactArray[] = [
                    'Category' => $type,
                    'ER' => [
                        'OldRate' => $data['er']['oldRate'],
                        'NewRate' => $data['er']['newRate'],
                        'MonthlyIncrease' => $data['er']['costIncrease']
                    ],
                    'EE' => [
                        'OldRate' => $data['ee']['oldRate'],
                        'NewRate' => $data['ee']['newRate'],
                        'MonthlyIncrease' => $data['ee']['costIncrease']
                    ]
                ];
            }
        }

        echo json_encode([
            'success' => true,
            'data' => [
                'impactedHeadcount' => $totalEmployeesAffected,
                'monthlyIncreaseER' => $totalMonthlyCostIncreaseER,
                'annualRequirementER' => $totalMonthlyCostIncreaseER * 12,
                'monthlyIncreaseEE' => $totalMonthlyCostIncreaseEE,
                'annualRequirementEE' => $totalMonthlyCostIncreaseEE * 12,
                'statutoryImpacts' => $impactArray
            ]
        ]);
        exit;
    } elseif ($action === 'apply_batch') {
        $batchRef = $input['batch_reference'] ?? null;
        if (!$batchRef) {
            echo json_encode(['success' => false, 'message' => 'Batch Reference required']);
            exit;
        }

        $conn->begin_transaction();

        $stmt = $conn->prepare("SELECT Category, FieldName, ProposedValue FROM statutory_proposals WHERE BatchReference = ? AND Status = 'Manager Approved'");
        $stmt->bind_param("s", $batchRef);
        $stmt->execute();
        $res = $stmt->get_result();
        
        $proposalsFound = false;
        $period_id = 1; // Assume period 1 as in cycle.php

        while ($row = $res->fetch_assoc()) {
            $proposalsFound = true;
            $category = $row['Category'];
            $fieldName = $row['FieldName'];
            $newValue = $row['ProposedValue'];

            $table = '';
            if ($category === 'SSS') $table = 'sss_settings';
            elseif ($category === 'PhilHealth') $table = 'philhealth_settings';
            elseif ($category === 'Pag-IBIG') $table = 'pagibig_settings';
            elseif ($category === 'BIR') $table = 'bir_tax_settings';

            if ($table) {
                // Update table. We assume there is only one row per period_id for these specific settings
                // as evidenced by cycle.php fetching with just period_id.
                $updateSql = "UPDATE $table SET $fieldName = ? WHERE period_id = ?";
                $updateStmt = $conn->prepare($updateSql);
                $updateStmt->bind_param("di", $newValue, $period_id);
                $updateStmt->execute();
            }
        }

        if ($proposalsFound) {
            $markStmt = $conn->prepare("UPDATE statutory_proposals SET Status = 'Applied', UpdatedAt = NOW() WHERE BatchReference = ? AND Status = 'Manager Approved'");
            $markStmt->bind_param("s", $batchRef);
            $markStmt->execute();

            $notifMsg = "Statutory adjustment batch {$batchRef} has been approved and applied.";
            $notifStmt = $conn->prepare("INSERT INTO system_notifications (module_target, message, role_target) VALUES ('compensation_cycle', ?, 'hr manager')");
            $notifStmt->bind_param("s", $notifMsg);
            $notifStmt->execute();

            $conn->commit();
            echo json_encode(['success' => true, 'message' => 'Statutory adjustments updated successfully.']);
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
        $stmt = $conn->prepare("UPDATE statutory_proposals SET Status = 'Rejected', UpdatedAt = NOW() WHERE BatchReference = ? AND Status IN ('Endorsed', 'Manager Approved')");
        $stmt->bind_param("s", $batchRef);
        if ($stmt->execute()) {
            $notifMsg = "Statutory adjustment batch {$batchRef} has been rejected by the HR Manager.";
            $notifStmt = $conn->prepare("INSERT INTO system_notifications (module_target, message, role_target) VALUES ('compensation_cycle', ?, 'hr manager')");
            $notifStmt->bind_param("s", $notifMsg);
            $notifStmt->execute();

            echo json_encode(['success' => true, 'message' => 'Statutory proposals rejected.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'Invalid action']);

} catch (Exception $e) {
    if ($conn) $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
