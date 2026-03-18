<?php
/**
 * Shared logic for applying final salary and grade changes from a simulation.
 */
function applyFinalSalaryChanges($conn, $proposalId, $periodId, $salaryScaleJson) {
    try {
        // 1. Update Employee Salaries (employmentinformation)
        $itemStmt = $conn->prepare("SELECT EmployeeID, NewSalary, NewGradeID FROM simulation_proposal_items WHERE ProposalID = ?");
        $itemStmt->bind_param("i", $proposalId);
        $itemStmt->execute();
        $result = $itemStmt->get_result();
        
        $updateStmt = $conn->prepare("UPDATE employmentinformation SET BaseSalary = ?, SalaryGradeID = ? WHERE EmployeeID = ?");
        
        while ($item = $result->fetch_assoc()) {
            $updateStmt->bind_param("dii", $item['NewSalary'], $item['NewGradeID'], $item['EmployeeID']);
            $updateStmt->execute();
        }
        $updateStmt->close();
        $itemStmt->close();

        // 2. Update Salary Structure (salary_grades) for this Period
        if (!empty($salaryScaleJson)) {
            $scales = json_decode($salaryScaleJson, true);
            if (is_array($scales)) {
                $gradeStmt = $conn->prepare("UPDATE salary_grades SET MinSalary = ?, MaxSalary = ? WHERE SalaryGradeID = ? AND period_id = ?");
                foreach ($scales as $scale) {
                    $min = (float)($scale['min'] ?? $scale['MinSalary'] ?? 0);
                    $max = (float)($scale['max'] ?? $scale['MaxSalary'] ?? 0);
                    $gradeId = (int)($scale['grade_id'] ?? $scale['SalaryGradeID'] ?? 0);
                    
                    if ($gradeId > 0) {
                        $gradeStmt->bind_param("ddii", $min, $max, $gradeId, $periodId);
                        $gradeStmt->execute();
                    }
                }
                $gradeStmt->close();
            }
        }

        // 3. Update Compensation Period status
        $periodStmt = $conn->prepare("UPDATE compensation_period SET budget_status = 'Approved/Closed' WHERE period_id = ?");
        $periodStmt->bind_param("i", $periodId);
        $periodStmt->execute();
        $periodStmt->close();

        // 4. Update Simulation Drafts to 'Implemented'
        $draftStmt = $conn->prepare("UPDATE simulation_drafts SET Status = 'Implemented' WHERE period_id = ? AND Status = 'Sent to Finance'");
        $draftStmt->bind_param("i", $periodId);
        $draftStmt->execute();
        $draftStmt->close();

        // 5. Update Simulation Proposals to 'Implemented'
        $propStmt = $conn->prepare("UPDATE simulation_proposals SET Status = 'Implemented' WHERE ProposalID = ?");
        $propStmt->bind_param("i", $proposalId);
        $propStmt->execute();
        $propStmt->close();

        return ['success' => true, 'message' => 'Salaries and grades updated successfully.'];

    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}
