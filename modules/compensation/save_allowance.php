<?php
require_once '../../config/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $grade_id = intval($_POST['grade_id'] ?? 0);
    $type_id = intval($_POST['type_id'] ?? 0);
    $amount = floatval($_POST['amount'] ?? 0);

    if ($grade_id > 0 && $type_id > 0) {
        // Check if record exists
        $check = $conn->prepare("SELECT GradeAllowanceID FROM grade_allowances WHERE SalaryGradeID = ? AND AllowanceTypeID = ?");
        $check->bind_param("ii", $grade_id, $type_id);
        $check->execute();
        $res = $check->get_result();

        if ($res->num_rows > 0) {
            $stmt = $conn->prepare("UPDATE grade_allowances SET Amount = ? WHERE SalaryGradeID = ? AND AllowanceTypeID = ?");
            $stmt->bind_param("dii", $amount, $grade_id, $type_id);
        } else {
            $stmt = $conn->prepare("INSERT INTO grade_allowances (Amount, SalaryGradeID, AllowanceTypeID) VALUES (?, ?, ?)");
            $stmt->bind_param("dii", $amount, $grade_id, $type_id);
        }

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $conn->error]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid parameters']);
    }
}
?>
