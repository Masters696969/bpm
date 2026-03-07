<?php
session_start();
header('Content-Type: application/json');
require_once '../../config/config.php';

if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Check for file upload
if (!isset($_FILES['gov_proof']) || $_FILES['gov_proof']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'Proof file is required']);
    exit();
}

$reason = $_POST['proposal_reason'] ?? '';
if (empty($reason)) {
    echo json_encode(['success' => false, 'message' => 'Reason is required']);
    exit();
}

$proposedBy = $_SESSION['user_id'] ?? null;
$batchRef = uniqid('stat_', true);

// Fetch current values
$period_id = 1; // Assume period 1 as in cycle.php
$sss = $conn->query("SELECT max_msc_monthly, employee_share_pct, employer_share_pct, wisp_threshold FROM sss_settings WHERE period_id = $period_id")->fetch_assoc();
$ph = $conn->query("SELECT salary_ceiling, employee_share_pct, employer_share_pct FROM philhealth_settings WHERE period_id = $period_id")->fetch_assoc();
$pi = $conn->query("SELECT monthly_cap_ee, monthly_cap_er, employee_rate_pct FROM pagibig_settings WHERE period_id = $period_id")->fetch_assoc();
$bir = $conn->query("SELECT tax_exempt_limit, de_minimis_cap, thirteenth_month_cap FROM bir_tax_settings WHERE period_id = $period_id")->fetch_assoc();

$proposals = [
    ['Category' => 'SSS', 'FieldName' => 'max_msc_monthly', 'OldValue' => $sss['max_msc_monthly'], 'ProposedValue' => $_POST['proposed_sss_msc']],
    ['Category' => 'SSS', 'FieldName' => 'employee_share_pct', 'OldValue' => $sss['employee_share_pct'], 'ProposedValue' => $_POST['proposed_sss_ee_pct']],
    ['Category' => 'SSS', 'FieldName' => 'employer_share_pct', 'OldValue' => $sss['employer_share_pct'], 'ProposedValue' => $_POST['proposed_sss_er_pct']],
    ['Category' => 'SSS', 'FieldName' => 'wisp_threshold', 'OldValue' => $sss['wisp_threshold'], 'ProposedValue' => $_POST['proposed_sss_wisp']],
    
    ['Category' => 'PhilHealth', 'FieldName' => 'salary_ceiling', 'OldValue' => $ph['salary_ceiling'], 'ProposedValue' => $_POST['proposed_ph_ceiling']],
    ['Category' => 'PhilHealth', 'FieldName' => 'employee_share_pct', 'OldValue' => $ph['employee_share_pct'], 'ProposedValue' => $_POST['proposed_ph_ee_pct']],
    ['Category' => 'PhilHealth', 'FieldName' => 'employer_share_pct', 'OldValue' => $ph['employer_share_pct'], 'ProposedValue' => $_POST['proposed_ph_er_pct']],

    ['Category' => 'Pag-IBIG', 'FieldName' => 'monthly_cap_ee', 'OldValue' => $pi['monthly_cap_ee'], 'ProposedValue' => $_POST['proposed_pi_cap_ee']],
    ['Category' => 'Pag-IBIG', 'FieldName' => 'monthly_cap_er', 'OldValue' => $pi['monthly_cap_er'], 'ProposedValue' => $_POST['proposed_pi_cap_er']],
    ['Category' => 'Pag-IBIG', 'FieldName' => 'employee_rate_pct', 'OldValue' => $pi['employee_rate_pct'], 'ProposedValue' => $_POST['proposed_pi_ee_rate_pct']],

    ['Category' => 'BIR Tax', 'FieldName' => 'tax_exempt_limit', 'OldValue' => $bir['tax_exempt_limit'], 'ProposedValue' => $_POST['proposed_bir_limit']],
    ['Category' => 'BIR Tax', 'FieldName' => 'de_minimis_cap', 'OldValue' => $bir['de_minimis_cap'], 'ProposedValue' => $_POST['proposed_bir_de_minimis']],
    ['Category' => 'BIR Tax', 'FieldName' => 'thirteenth_month_cap', 'OldValue' => $bir['thirteenth_month_cap'], 'ProposedValue' => $_POST['proposed_bir_13th_month']]
];

// Filter only changed fields (use rounding to avoid precision issues with DECIMAL types)
$proposals = array_filter($proposals, function($p) {
    $old = round((float)$p['OldValue'], 4);
    $new = round((float)$p['ProposedValue'], 4);
    return $old !== $new;
});

if (empty($proposals)) {
    echo json_encode(['success' => false, 'message' => 'No changes detected. Please modify at least one value.']);
    exit();
}

// Handle File Upload
$uploadDir = '../../uploads/statutory_proofs/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

$fileExt = pathinfo($_FILES['gov_proof']['name'], PATHINFO_EXTENSION);
$fileName = $batchRef . '.' . $fileExt;
$uploadPath = $uploadDir . $fileName;
$dbPath = 'uploads/statutory_proofs/' . $fileName;

if (!move_uploaded_file($_FILES['gov_proof']['tmp_name'], $uploadPath)) {
    echo json_encode(['success' => false, 'message' => 'Failed to upload proof file']);
    exit();
}

$conn->begin_transaction();
try {
    $stmt = $conn->prepare("INSERT INTO statutory_proposals (BatchReference, Category, FieldName, OldValue, ProposedValue, Reason, ProofPath, ProposedBy, Status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Pending')");
    
    foreach ($proposals as $prop) {
        $stmt->bind_param("sssddssi", 
            $batchRef, 
            $prop['Category'], 
            $prop['FieldName'], 
            $prop['OldValue'], 
            $prop['ProposedValue'], 
            $reason, 
            $dbPath, 
            $proposedBy
        );
        if (!$stmt->execute()) {
            throw new Exception("Error saving proposal: " . $stmt->error);
        }
    }
    
    // Notify through system notifications
    $notifMsg = "New statutory change proposal submitted by {$_SESSION['username']}. Batch: {$batchRef}";
    $notifStmt = $conn->prepare("INSERT INTO system_notifications (module_target, message, role_target) VALUES ('compensation_cycle', ?, 'supervisor')");
    $notifStmt->bind_param("s", $notifMsg);
    $notifStmt->execute();

    $conn->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    if ($conn) $conn->rollback();
    if (file_exists($uploadPath)) unlink($uploadPath);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?>
