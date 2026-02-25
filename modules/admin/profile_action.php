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

$action = $_POST['action'] ?? '';

if ($action === 'update_profile') {
    $userId = $_SESSION['user_id'];
    
    // Get EmployeeID linked to this account
    $stmt = $conn->prepare("SELECT EmployeeID FROM useraccounts WHERE AccountID = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $res = $stmt->get_result();
    $user = $res->fetch_assoc();
    
    if (!$user || !$user['EmployeeID']) {
        echo json_encode(['success' => false, 'message' => 'No employee record linked to this account.']);
        exit;
    }
    
    $employeeId = $user['EmployeeID'];
    
    $firstName = $_POST['FirstName'] ?? '';
    $lastName = $_POST['LastName'] ?? '';
    $phone = $_POST['PhoneNumber'] ?? '';
    $email = $_POST['PersonalEmail'] ?? '';
    $address = $_POST['PermanentAddress'] ?? '';
    
    if (empty($firstName) || empty($lastName)) {
        echo json_encode(['success' => false, 'message' => 'First Name and Last Name are required.']);
        exit;
    }

    $profilePhotoPath = null;
    if (isset($_FILES['ProfilePhoto']) && $_FILES['ProfilePhoto']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../img/profiles/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileExtension = strtolower(pathinfo($_FILES['ProfilePhoto']['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($fileExtension, $allowedExtensions)) {
            echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, and GIF allowed.']);
            exit;
        }

        $newFileName = 'profile_' . $employeeId . '_' . time() . '.' . $fileExtension;
        $targetPath = $uploadDir . $newFileName;

        if (move_uploaded_file($_FILES['ProfilePhoto']['tmp_name'], $targetPath)) {
            $profilePhotoPath = 'img/profiles/' . $newFileName;
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to move uploaded file.']);
            exit;
        }
    }
    
    if ($profilePhotoPath) {
        $sql = "UPDATE employee SET FirstName = ?, LastName = ?, PhoneNumber = ?, PersonalEmail = ?, PermanentAddress = ?, ProfilePhoto = ? WHERE EmployeeID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssi", $firstName, $lastName, $phone, $email, $address, $profilePhotoPath, $employeeId);
    } else {
        $sql = "UPDATE employee SET FirstName = ?, LastName = ?, PhoneNumber = ?, PersonalEmail = ?, PermanentAddress = ? WHERE EmployeeID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $firstName, $lastName, $phone, $email, $address, $employeeId);
    }
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Profile updated successfully', 'photo_path' => $profilePhotoPath]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update profile: ' . $conn->error]);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid action']);
