<?php
session_start();
require_once '../../config/config.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$batchId = (int)($input['batch_id'] ?? 0);

if (!$batchId) {
    echo json_encode(['success' => false, 'message' => 'Invalid Batch ID.']);
    exit();
}

$conn->begin_transaction();

try {
    // 1. Fetch simulation details
    $stmt = $conn->prepare("SELECT EmployeeData, Status FROM simulation_drafts WHERE DraftID = ?");
    $stmt->bind_param("i", $batchId);
    $stmt->execute();
    $res = $stmt->get_result();
    
    if ($res->num_rows === 0) {
        throw new Exception("Simulation draft not found.");
    }

    $draft = $res->fetch_assoc();
    if ($draft['Status'] === 'Finalized') {
        throw new Exception("This simulation has already been finalized.");
    }

    $empData = json_decode($draft['EmployeeData'], true);
    if (!$empData) {
        throw new Exception("Invalid employee data in simulation.");
    }

    // 2. Prepare update statements
    $updateSalaryStmt = $conn->prepare("UPDATE employmentinformation SET BaseSalary = ? WHERE EmployeeID = ?");
    $updatePromoStmt = $conn->prepare("UPDATE employmentinformation SET BaseSalary = ?, SalaryGradeID = ? WHERE EmployeeID = ?");

    // 3. Iterate and Apply
    foreach ($empData as $emp) {
        $empId = $emp['EmployeeID'] ?? $emp['id'] ?? '';
        $newSalary = (float)($emp['new_salary'] ?? 0);
        $promoSG = isset($emp['promo_sg']) && $emp['promo_sg'] !== '' ? (int)$emp['promo_sg'] : null;

        if (!$empId) continue;

        if ($promoSG) {
            $updatePromoStmt->bind_param("dis", $newSalary, $promoSG, $empId);
            $updatePromoStmt->execute();
        } else {
            $updateSalaryStmt->bind_param("ds", $newSalary, $empId);
            $updateSalaryStmt->execute();
        }
    }

    // 4. Mark draft as Finalized
    $finalizeStmt = $conn->prepare("UPDATE simulation_drafts SET Status = 'Finalized', UpdatedAt = NOW() WHERE DraftID = ?");
    $finalizeStmt->bind_param("i", $batchId);
    $finalizeStmt->execute();

    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Salaries updated successfully and simulation finalized.']);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
