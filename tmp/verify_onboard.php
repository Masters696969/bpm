<?php
session_start();
$_SESSION['username'] = 'AdminTest';
$_SESSION['user_id'] = 1;
$_SESSION['user_role'] = 'Administrator';

// Mocking POST data
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST['applicant_id'] = 6;
$_POST['position_id'] = 1; // Example position
$_POST['salary_grade_id'] = 1; // Example grade
$_POST['hiring_date'] = date('Y-m-d');
$_POST['employment_status'] = 'Probationary';
$_POST['salary_type'] = 'Monthly';

// Capture output
ob_start();
require_once 'c:/xamppp/htdocs/microfinance-backup/modules/admin/backend/onboard_action.php';
$output = ob_get_clean();

echo "Response: " . $output . "\n";

// Verify Database
require_once 'c:/xamppp/htdocs/microfinance-backup/config/config.php';

$res = $conn->query("SELECT * FROM employee WHERE PersonalEmail = (SELECT Email FROM applicants WHERE ApplicantID = 6)");
if ($res && $res->num_rows > 0) {
    $emp = $res->fetch_assoc();
    echo "SUCCESS: Employee record created. ID: " . $emp['EmployeeID'] . "\n";
    
    $accRes = $conn->query("SELECT * FROM useraccounts WHERE EmployeeID = " . $emp['EmployeeID']);
    if ($accRes && $accRes->num_rows > 0) {
        $acc = $accRes->fetch_assoc();
        echo "SUCCESS: User account created. Username: " . $acc['Username'] . "\n";
        
        $roleRes = $conn->query("SELECT * FROM useraccountroles WHERE AccountID = " . $acc['AccountID']);
        if ($roleRes && $roleRes->num_rows > 0) {
             echo "SUCCESS: Role assigned.\n";
        } else {
             echo "FAILURE: Role NOT assigned.\n";
        }
    } else {
        echo "FAILURE: User account NOT created.\n";
    }
} else {
    echo "FAILURE: Employee record NOT created.\n";
}
?>
