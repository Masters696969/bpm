<?php
require_once 'config/config.php';

// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'login') {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($email) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Please enter email and password']);
            exit;
        }
        
        // Get user account by email
        $sql = "SELECT * FROM useraccounts WHERE Email = ? AND AccountStatus = 'Active'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $user['PasswordHash'])) {
                // Generate OTP
                $otp = sprintf("%06d", mt_rand(0, 999999));
                $otpExpiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
                
                // Store OTP in database
                $updateSql = "UPDATE useraccounts SET OTP_Code = ?, OTP_Expiry = ? WHERE AccountID = ?";
                $updateStmt = $conn->prepare($updateSql);
                $updateStmt->bind_param("ssi", $otp, $otpExpiry, $user['AccountID']);
                $updateStmt->execute();
                
                // Store in session for verification
                $_SESSION['pending_login'] = [
                    'account_id' => $user['AccountID'],
                    'email' => $user['Email']
                ];
                
                // Store portal preference
                if (isset($_POST['login_portal'])) {
                    $_SESSION['login_portal'] = $_POST['login_portal'];
                }
                
                // Send OTP email
                $emailSent = sendOtpEmail($user['Email'], $otp, $user['Username']);
                
                echo json_encode([
                    'success' => true, 
                    'message' => 'OTP sent to your email',
                    'requires_otp' => true
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid password']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'User not found or account inactive']);
        }
        exit;
    }
    
    if ($action === 'verify_otp') {
        $otp = trim($_POST['otp'] ?? '');
        
        if (empty($otp)) {
            echo json_encode(['success' => false, 'message' => 'Please enter OTP']);
            exit;
        }
        
        if (!isset($_SESSION['pending_login'])) {
            echo json_encode(['success' => false, 'message' => 'Session expired. Please login again.']);
            exit;
        }
        
        $pending = $_SESSION['pending_login'];
        
        // Verify OTP - check matching code first, then check expiry in PHP
        $sql = "SELECT AccountID, Username, Email, OTP_Code, OTP_Expiry FROM useraccounts WHERE AccountID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $pending['account_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Check if OTP code matches (loose comparison)
            if ($user['OTP_Code'] == $otp) {
                // Check if OTP is expired (using strtotime for timezone-aware comparison)
                $expiryTime = strtotime($user['OTP_Expiry']);
                $currentTime = time();
                
                if ($currentTime <= $expiryTime) {
                    // OTP is valid! Clear it and log in
                    $updateSql = "UPDATE useraccounts SET OTP_Code = NULL, OTP_Expiry = NULL WHERE AccountID = ?";
                    $updateStmt = $conn->prepare($updateSql);
                    $updateStmt->bind_param("i", $pending['account_id']);
                    $updateStmt->execute();
                    
                    // Get user roles
                    $rolesSql = "SELECT r.RoleName FROM roles r 
                                JOIN useraccountroles ur ON r.RoleID = ur.RoleID 
                                WHERE ur.AccountID = ?";
                    $rolesStmt = $conn->prepare($rolesSql);
                    $rolesStmt->bind_param("i", $pending['account_id']);
                    $rolesStmt->execute();
                    $rolesResult = $rolesStmt->get_result();
                    
                    $roles = [];
                    while ($roleRow = $rolesResult->fetch_assoc()) {
                        $roles[] = $roleRow['RoleName'];
                    }
                    
                    $primaryRole = $roles[0] ?? 'employee';
                    
                    // Set session variables
                    $_SESSION['user_id'] = $user['AccountID'];
                    $_SESSION['username'] = $user['Username'];
                    $_SESSION['user_email'] = $user['Email'];
                    $_SESSION['user_name'] = $user['Username'];
                    $_SESSION['user_role'] = $primaryRole;
                    $_SESSION['user_roles'] = $roles;
                    
                    // Clear pending login
                    unset($_SESSION['pending_login']);
                    
                    // Redirect based on role and portal preference
                    $roleKey = strtolower($primaryRole);
                    $portalInfo = $_SESSION['login_portal'] ?? 'workforce';
                    
                    if ($portalInfo === 'ess') {
                        $redirectUrl = 'modules/ess/dashboard.php';
                    } else {
                        if ($roleKey === 'administrator') {
                            $redirectUrl = 'modules/admin/dashboard.php';
                        } elseif ($roleKey === 'hr data specialist' || $roleKey === 'hr manager') {
                            $redirectUrl = 'modules/corehumancapital/dashboard.php';
                        } elseif ($roleKey === 'hr staff') {
                            $redirectUrl = 'modules/hr1staff/dashboard.php';
                        } else {
                            $redirectUrl = 'dashboard.php';
                        }
                    }
                    
                    // Cleanup portal session
                    unset($_SESSION['login_portal']);
                    
                    echo json_encode([
                        'success' => true, 
                        'message' => 'Login successful',
                        'redirect' => $redirectUrl,
                        'user_role' => $primaryRole
                    ]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'OTP has expired. Please login again.']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid OTP code']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'No OTP found. Please login again.']);
        }
        exit;
    }
    
    if ($action === 'resend_otp') {
        if (!isset($_SESSION['pending_login'])) {
            echo json_encode(['success' => false, 'message' => 'Session expired. Please login again.']);
            exit;
        }
        
        $pending = $_SESSION['pending_login'];
        
        // Generate new OTP
        $otp = sprintf("%06d", mt_rand(0, 999999));
        $otpExpiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        // Update OTP in database
        $updateSql = "UPDATE useraccounts SET OTP_Code = ?, OTP_Expiry = ? WHERE AccountID = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("ssi", $otp, $otpExpiry, $pending['account_id']);
        $updateStmt->execute();
        
        // Send new OTP email
        $emailSent = sendOtpEmail($pending['email'], $otp);
        
        echo json_encode([
            'success' => true, 
            'message' => 'New OTP sent to your email',
            'email_sent' => $emailSent
        ]);
        exit;
    }
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
?>
