<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ob_start(); 
header('Content-Type: application/json');

session_start();
$response = ['success' => false, 'message' => ''];

try {
    require_once '../../../config/config.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $cycleName = isset($_POST['cycle_name']) ? trim($_POST['cycle_name']) : '';
        $periodId = isset($_POST['period_id']) ? (int)$_POST['period_id'] : 0;
        $totalBudget = isset($_POST['total_budget']) ? (float)$_POST['total_budget'] : 0.00;
        $totalImpact = isset($_POST['total_impact']) ? (float)$_POST['total_impact'] : 0.00;
        $remainingBudget = isset($_POST['remaining_budget']) ? (float)$_POST['remaining_budget'] : 0.00;
        $employeeData = isset($_POST['employee_data']) ? json_decode($_POST['employee_data'], true) : [];
        $salaryScaleData = isset($_POST['salary_scale_data']) ? $_POST['salary_scale_data'] : '';

        if (empty($cycleName) || empty($employeeData)) {
            $response['message'] = 'Missing required proposal data.';
        } else {
            $userId = $_SESSION['user_id'] ?? 0;

            // 2. Insert Proposal Summary
            $deptCode = isset($_POST['dept_code']) ? trim($_POST['dept_code']) : 'GLOBAL';
            


            $stmt = $conn->prepare("INSERT INTO simulation_proposals (CycleName, SalaryScaleData, PeriodID, DeptCode, TotalBudget, TotalImpact, RemainingBudget, ProposedBy) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssisdddi", $cycleName, $salaryScaleData, $periodId, $deptCode, $totalBudget, $totalImpact, $remainingBudget, $userId);
            
            if ($stmt->execute()) {
                $proposalId = $stmt->insert_id;
                $stmt->close();

                // 3. Insert Line Items
                $itemStmt = $conn->prepare("INSERT INTO simulation_proposal_items (ProposalID, EmployeeID, OriginalSalary, MarketAdjustment, MeritPct, MeritAmount, NewSalary, NewGradeID, CompaRatio) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                
                foreach ($employeeData as $emp) {
                    $eeId = (int)$emp['ee_id'];
                    $origSal = (float)$emp['original_salary'];
                    $mktAdj = (float)$emp['market_adjustment'];
                    $meritPct = (float)$emp['merit_pct'];
                    $meritAmt = (float)$emp['merit_amount'];
                    $newSal = (float)$emp['new_salary'];
                    $gradeId = (int)$emp['grade_id'];
                    $compa = (float)$emp['compa_ratio'];

                    $itemStmt->bind_param("iidddddid", $proposalId, $eeId, $origSal, $mktAdj, $meritPct, $meritAmt, $newSal, $gradeId, $compa);
                    $itemStmt->execute();
                }
                $itemStmt->close();

                // 3.5 Update Draft Status
                $updateDraft = $conn->prepare("UPDATE simulation_drafts SET Status = 'Sent to Finance' WHERE CycleName = ?");
                $updateDraft->bind_param("s", $cycleName);
                $updateDraft->execute();
                $updateDraft->close();

                // 4. Remote Sync to Finance Laptop
                $deptCodeParam = isset($_POST['dept_code']) ? trim($_POST['dept_code']) : 'HR COMPENSATION';
                
                $payload = [
                    'cycle_name' => $cycleName,
                    'period_id' => $periodId,
                    'dept_code' => 'HR COMPENSATION',
                    'entity_name' => 'HR COMPENSATION', // Used for allocation_entity_name on Finance
                    'allocation_type' => 'DEPARTMENT',
                    'total_budget' => $totalBudget,
                    'total_impact' => $totalImpact,
                    'remaining_budget' => $remainingBudget,
                    'proposed_by_id' => $userId,
                    'proposal_id' => $proposalId, 
                    'fiscal_year' => date('Y'),   
                    'salary_scale_data' => json_decode($salaryScaleData, true),
                    'items' => $employeeData
                ];

                $syncResult = sendHRProposalToBudget($payload);

                $response['success'] = true;
                $response['sync_status'] = $syncResult['success'];
                
                if ($syncResult['success']) {
                    $response['message'] = "Proposal saved locally. Finance Laptop sync successful!";
                } else {
                    $response['message'] = "Proposal saved locally. Finance Sync Error: " . $syncResult['message'];
                }
            } else {
                $response['message'] = "Database error: " . $conn->error;
            }
        }
    }
} catch (Exception $e) {
    $response['message'] = "System Error: " . $e->getMessage();
}

/**
 * Function to send HR Simulation totals to Finance Laptop
 */
function sendHRProposalToBudget(array $payload): array
{
    // Finance Laptop's IPv4 Address - Corrected directory to 'microfinancee'
    $financeUrl = 'http://10.112.107.207/microfinancee/modules/budget/receive_hr_proposal.php';
    $apiKey = 'HR_FINANCE_SECRET_2026';

    $ch = curl_init($financeUrl);

    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'X-API-KEY: ' . $apiKey
        ],
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_TIMEOUT => 15,
        CURLOPT_CONNECTTIMEOUT => 5
    ]);

    $response = curl_exec($ch);
    $curlError = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    if ($response === false || $curlError) {
        return [
            'success' => false,
            'message' => 'Connection to Finance Laptop failed: ' . $curlError
        ];
    }

    $decoded = json_decode($response, true);
    if (!is_array($decoded)) {
        return [
            'success' => false,
            'message' => 'Invalid response from Finance server (HTTP ' . $httpCode . '). Raw output: ' . substr(strip_tags($response), 0, 200),
            'raw' => $response
        ];
    }

    return $decoded;
}

ob_end_clean();
echo json_encode($response);
?>
