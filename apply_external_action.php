<?php
// apply_external_action.php
require_once 'config/config.php';

header('Content-Type: application/json');

// Global error handler to catch any unexpected issues and return JSON
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    if (!(error_reporting() & $errno)) return;
    echo json_encode(['success' => false, 'message' => "PHP Error: $errstr in $errfile on line $errline"]);
    exit;
});

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$postId = $_POST['post_id'] ?? null;
$firstName = trim($_POST['first_name'] ?? '');
$middleName = trim($_POST['middle_name'] ?? '');
$lastName = trim($_POST['last_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');

// New Fields - Make them NULL if empty
$gender = !empty($_POST['gender']) ? $_POST['gender'] : null;
$dob = !empty($_POST['dob']) ? $_POST['dob'] : null;
$address = !empty(trim($_POST['address'] ?? '')) ? trim($_POST['address']) : null;
$emergencyName = !empty(trim($_POST['emergency_name'] ?? '')) ? trim($_POST['emergency_name']) : null;
$emergencyRelationship = !empty(trim($_POST['emergency_relationship'] ?? '')) ? trim($_POST['emergency_relationship']) : null;
$emergencyPhone = !empty(trim($_POST['emergency_phone'] ?? '')) ? trim($_POST['emergency_phone']) : null;


if (!$postId || empty($firstName) || empty($lastName) || empty($email)) {
    echo json_encode(['success' => false, 'message' => 'Missing required basic fields (Name, Email, etc).']);
    exit;
}

// Check for existing application by email for the same post
$checkStmt = $conn->prepare("SELECT ApplicantID FROM applicants WHERE PostID = ? AND Email = ?");
if (!$checkStmt) {
    echo json_encode(['success' => false, 'message' => 'Database preparation error (101).']);
    exit;
}

$checkStmt->bind_param("is", $postId, $email);
$checkStmt->execute();
if ($checkStmt->get_result()->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'You have already submitted an application for this position using this email.']);
    exit;
}

$uploadDir = 'uploads/applications/';
if (!is_dir($uploadDir)) {
    if (!mkdir($uploadDir, 0777, true)) {
        echo json_encode(['success' => false, 'message' => 'Failed to create upload directory.']);
        exit;
    }
}

$files = [
    'resume' => 'Resume',
    'gov_id' => 'GovID',
    'clearance' => 'Clearance',
    'tor' => 'TOR',
    'id_picture' => 'IDPic'
];

$paths = [];
foreach ($files as $key => $label) {
    if (isset($_FILES[$key]) && $_FILES[$key]['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES[$key]['name'], PATHINFO_EXTENSION);
        $filename = $label . "_" . preg_replace('/[^a-zA-Z0-9]/', '', $lastName) . "_" . time() . "." . $ext;
        $targetPath = $uploadDir . $filename;
        
        if (move_uploaded_file($_FILES[$key]['tmp_name'], $targetPath)) {
            $paths[$key] = $targetPath;
        } else {
            echo json_encode(['success' => false, 'message' => "Failed to upload $label. Please check file sizes or permissions."]);
            exit;
        }
    } else {
        $paths[$key] = null;
    }
}

$sql = "INSERT INTO applicants (
    PostID, FirstName, MiddleName, LastName, Email, Phone, 
    Gender, DateOfBirth, PermanentAddress, 
    EmergencyContactName, EmergencyRelationship, EmergencyPhone, 
    ResumePath, GovIDPath, ClearancePath, TORPath, IDPicturePath,
    Status, AppliedAt
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'New', CURRENT_TIMESTAMP)";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database error during preparation (Insert): ' . $conn->error]);
    exit;
}

$stmt->bind_param("issssssssssssssss", 
    $postId, $firstName, $middleName, $lastName, $email, $phone,
    $gender, $dob, $address,
    $emergencyName, $emergencyRelationship, $emergencyPhone,
    $paths['resume'], $paths['gov_id'], $paths['clearance'], $paths['tor'], $paths['id_picture']
);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Your application has been submitted successfully! We will review your profile and get back to you soon.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error during execution: ' . $stmt->error]);
}
?>
