<?php
session_start();
require_once '../../config/config.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? $_POST['action'] ?? $_GET['action'] ?? 'fetch';

if ($action === 'fetch') {
    // Fetch Verified simulations for Manager Review
    $sql = "SELECT DraftID, CycleName, TotalCost, Status, CreatedAt, ProposedBy FROM simulation_drafts WHERE Status IN ('Verified', 'Reviewed') ORDER BY CreatedAt DESC";
    $result = $conn->query($sql);
    $sims = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $sims[] = $row;
        }
    }
    echo json_encode(['success' => true, 'data' => $sims]);
} elseif ($action === 'details') {
    $id = (int)($input['id'] ?? $_GET['id'] ?? 0);
    $stmt = $conn->prepare("SELECT * FROM simulation_drafts WHERE DraftID = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        echo json_encode(['success' => true, 'data' => $row]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Simulation not found.']);
    }
} elseif ($action === 'save') {
    $id = (int)($input['id'] ?? 0);
    $data = $input['employee_data'] ?? '';
    $totalCost = (float)($input['total_cost'] ?? 0);

    $stmt = $conn->prepare("UPDATE simulation_drafts SET EmployeeData = ?, TotalCost = ?, UpdatedAt = NOW() WHERE DraftID = ?");
    $stmt->bind_param("sdi", $data, $totalCost, $id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Progress saved successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save progress.']);
    }
} elseif ($action === 'approve') {
    $id = (int)($input['id'] ?? 0);
    // Forward to Finance
    $stmt = $conn->prepare("UPDATE simulation_drafts SET Status = 'Reviewed', LastSaved = NOW() WHERE DraftID = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        // Notify Finance
        $notifMsg = "A compensation simulation has been reviewed by the Manager and awaits final Finance approval.";
        $notifStmt = $conn->prepare("INSERT INTO system_notifications (module_target, message, role_target) VALUES ('finance_approval', ?, 'finance manager')");
        $notifStmt->bind_param("s", $notifMsg);
        $notifStmt->execute();

        echo json_encode(['success' => true, 'message' => 'Simulation approved and forwarded to Finance.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to approve simulation.']);
    }
} elseif ($action === 'reject') {
    $id = (int)($input['id'] ?? 0);
    $stmt = $conn->prepare("UPDATE simulation_drafts SET Status = 'Rejected', UpdatedAt = NOW() WHERE DraftID = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Simulation rejected.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to reject simulation.']);
    }
}
?>
