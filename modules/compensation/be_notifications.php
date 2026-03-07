<?php
session_start();
header('Content-Type: application/json');
require_once '../../config/config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

if ($action === 'fetch') {
    $module_target = $_GET['module_target'] ?? '';
    $user_role = $_SESSION['user_role'] ?? '';
    
    // Role mapping for targeting
    $role_map = [
        'Administrator' => 'admin',
        'HR Manager' => 'hr manager',
        'Supervisor' => 'supervisor',
        'Compensation Analyst' => 'processor',
        'Payroll Processor' => 'processor',
        'Processor' => 'processor'
    ];
    $target_role = $role_map[$user_role] ?? strtolower($user_role);
    
    // Strict Filtering:
    // Supervisors only see 'supervisor' target.
    // Managers only see 'hr manager' target.
    // Admin sees everything if they want, but filtered by role here for dashboard tidiness.
    $where_clause = "(module_target = ? OR module_target = 'global')";
    $params = [$module_target];
    $types = "s";
    
    // Special logic:
    // 1. Administrators see everything (no role filter).
    // 2. Processors/Analysts in 'compensation_cycle' see everything for that module (to track status).
    // 3. Others (Supervisor, HR Manager) see only their targeted role or broad/global alerts.
    
    $is_processor_cycle = in_array($user_role, ['Compensation Analyst', 'Payroll Processor', 'Processor']) 
                          && $module_target === 'compensation_cycle';

    if ($user_role === 'Administrator' || $is_processor_cycle || $module_target === 'compensation_cycle') {
        // No additional role filter
    } else {
        $where_clause .= " AND (role_target = ? OR role_target = 'all' OR role_target IS NULL OR role_target = '')";
        $params[] = $target_role;
        $types .= "s";
    }
    
    $query = "SELECT * FROM system_notifications WHERE $where_clause ORDER BY created_at DESC LIMIT 50";
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $notifications = [];
    $unread_count = 0;
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row;
        if ($row['is_read'] == 0) {
            $unread_count++;
        }
    }
    
    echo json_encode(['success' => true, 'notifications' => $notifications, 'unread_count' => $unread_count]);
    exit();
}

if ($action === 'mark_read') {
    $module_target = $_POST['module_target'] ?? json_decode(file_get_contents('php://input'), true)['module_target'] ?? '';
    $user_role = $_SESSION['user_role'] ?? '';
    
    if ($user_role === 'Administrator' || (in_array($user_role, ['Compensation Analyst', 'Payroll Processor', 'Processor']) && $module_target === 'compensation_cycle')) {
        $stmt = $conn->prepare("UPDATE system_notifications SET is_read = 1 WHERE module_target = ? AND is_read = 0");
        $stmt->bind_param("s", $module_target);
    } else {
        $stmt = $conn->prepare("UPDATE system_notifications SET is_read = 1 WHERE module_target = ? AND (role_target = ? OR role_target IS NULL OR role_target = '') AND is_read = 0");
        $stmt->bind_param("ss", $module_target, $target_role);
    }
    
    $stmt->execute();
    echo json_encode(['success' => true]);
    exit();
}

echo json_encode(['success' => false, 'message' => 'Invalid action']);
?>
