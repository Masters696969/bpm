<?php
header('Content-Type: application/json');
session_start();
require_once '../../../config/config.php';

// Import PHPMailer (assuming it's in vendor as per config.php)
require_once __DIR__ . '/../../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit();
}

$applicantId = $_POST['applicant_id'] ?? null;
$interviewerId = $_POST['interviewer_id'] ?? null;
$date = $_POST['interview_date'] ?? null;
$time = $_POST['interview_time'] ?? null;
$mode = $_POST['interview_mode'] ?? null;
$location = $_POST['location_link'] ?? null;
$notes = $_POST['notes'] ?? '';

if (!$applicantId || !$interviewerId || !$date || !$time || !$mode || !$location) {
    echo json_encode(['success' => false, 'error' => 'All mandatory fields are required']);
    exit();
}

// 1. Fetch Applicant Email and Name
$stmt = $conn->prepare("SELECT FirstName, LastName, Email FROM applicants WHERE ApplicantID = ?");
$stmt->bind_param("i", $applicantId);
$stmt->execute();
$applicant = $stmt->get_result()->fetch_assoc();

if (!$applicant) {
    echo json_encode(['success' => false, 'error' => 'Applicant not found']);
    exit();
}

// 2. Start Transaction
$conn->begin_transaction();

try {
    // 3. Save Schedule
    $stmt = $conn->prepare("INSERT INTO interview_schedules (ApplicantID, InterviewerID, InterviewDate, InterviewTime, InterviewMode, LocationOrLink, Notes) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iisssss", $applicantId, $interviewerId, $date, $time, $mode, $location, $notes);
    $stmt->execute();

    // 4. Update Applicant Status to 'Interview' and set Resume Score to 100%
    $stmt = $conn->prepare("UPDATE applicants SET Status = 'Interview', ResumeScore = 100 WHERE ApplicantID = ?");
    $stmt->bind_param("i", $applicantId);
    $stmt->execute();

    // 5. Send Email
    $mail = new PHPMailer(true);
    
    // Server settings
    $mail->isSMTP();
    $mail->Host = $mail_config['host'];
    $mail->SMTPAuth = $mail_config['smtp_auth'];
    $mail->Username = $mail_config['username'];
    $mail->Password = $mail_config['password'];
    $mail->SMTPSecure = $mail_config['smtp_secure'];
    $mail->Port = $mail_config['port'];
    
    // Recipients
    $mail->setFrom($mail_config['from_email'], $mail_config['from_name']);
    $mail->addAddress($applicant['Email'], "{$applicant['FirstName']} {$applicant['LastName']}");
    $mail->addReplyTo($mail_config['reply_to']);
    
    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Interview Invitation: Microfinance System';
    
    $formattedDate = date('F d, Y', strtotime($date));
    $formattedTime = date('h:i A', strtotime($time));
    
    $emailBody = "
    <div style='font-family: \"Segoe UI\", Roboto, Helvetica, Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 0; background-color: #f9fafb; border-radius: 16px; overflow: hidden; border: 1px solid #e5e7eb;'>
        <!-- Header -->
        <div style='background: linear-gradient(135deg, #2ca078, #14532d); padding: 40px 20px; text-align: center; color: white;'>
            <div style='background: rgba(255,255,255,0.15); width: 64px; height: 64px; border-radius: 16px; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 20px;'>
                 <div style='font-size: 32px;'>📅</div>
            </div>
            <h1 style='margin: 0; font-size: 26px; font-weight: 800; letter-spacing: -0.02em;'>Interview Invitation</h1>
            <p style='margin: 10px 0 0; font-size: 16px; opacity: 0.9;'>Microfinance Recruitment Team</p>
        </div>
        
        <!-- Content -->
        <div style='padding: 40px; background-color: white;'>
            <p style='font-size: 16px; color: #374151; line-height: 1.6;'>Dear <strong>{$applicant['FirstName']}</strong>,</p>
            
            <p style='font-size: 15px; color: #4b5563; line-height: 1.6;'>We are impressed by your background and would like to invite you for an interview to further explore your fit for the position. We've scheduled a session for you with our recruitment team.</p>
            
            <!-- Schedule Box -->
            <div style='background-color: #f0fdf4; border: 1px solid #dcfce7; border-radius: 16px; padding: 30px; margin: 30px 0;'>
                <h3 style='margin: 0 0 20px; font-size: 14px; font-weight: 700; color: #166534; text-transform: uppercase; letter-spacing: 0.05em;'>Schedule Details</h3>
                
                <div style='display: flex; flex-direction: column; gap: 16px;'>
                    <div style='display: flex; align-items: center; margin-bottom: 15px;'>
                        <div style='width: 32px; color: #2ca078;'>📅</div>
                        <div style='font-size: 16px; color: #111827;'><strong>Date:</strong> $formattedDate</div>
                    </div>
                    <div style='display: flex; align-items: center; margin-bottom: 15px;'>
                        <div style='width: 32px; color: #2ca078;'>⏰</div>
                        <div style='font-size: 16px; color: #111827;'><strong>Time:</strong> $formattedTime</div>
                    </div>
                    <div style='display: flex; align-items: center; margin-bottom: 15px;'>
                        <div style='width: 32px; color: #2ca078;'>📍</div>
                        <div style='font-size: 16px; color: #111827;'><strong>Mode:</strong> $mode</div>
                    </div>
                    <div style='display: flex; align-items: center; padding-top: 15px; border-top: 1px solid #dcfce7;'>
                        <div style='width: 32px; color: #2ca078;'>🔗</div>
                        <div style='font-size: 16px; color: #111827;'><strong>Location/Link:</strong> <span style='color: #2ca078; font-weight: 600;'>$location</span></div>
                    </div>
                </div>
            </div>";

    if (!empty($notes)) {
        $emailBody .= "
            <div style='margin-bottom: 30px;'>
                <p style='font-size: 14px; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 10px;'>Interviewer Notes</p>
                <div style='background: #fff; padding: 20px; border-radius: 12px; border: 1px solid #e5e7eb; color: #4b5563; font-size: 14px; line-height: 1.6;'>
                    " . nl2br(htmlspecialchars($notes)) . "
                </div>
            </div>";
    }

    $emailBody .= "
            <p style='font-size: 14px; color: #6b7280; text-align: center; margin-top: 40px;'>
                If you have any questions or need to reschedule, please reach out to us at <a href='mailto:" . $mail_config['from_email'] . "' style='color: #2ca078; text-decoration: none; font-weight: 600;'>" . $mail_config['from_email'] . "</a>.
            </p>
            
            <div style='text-align: center; margin-top: 30px; padding-top: 30px; border-top: 1px solid #f3f4f6;'>
                <p style='margin: 0; font-size: 15px; color: #111827;'><strong>Microfinance HR Team</strong></p>
                <p style='margin: 5px 0 0; font-size: 13px; color: #9ca3af;'>Building a better financial future together.</p>
            </div>
        </div>
        
        <!-- Footer -->
        <div style='background-color: #f9fafb; padding: 20px; text-align: center; border-top: 1px solid #f3f4f6;'>
            <p style='margin: 0; font-size: 11px; color: #9ca3af; line-height: 1.5;'>
                This is an automated recruitment message from the Microfinance System.<br>
                Please do not reply directly to this email.
            </p>
        </div>
    </div>";
    
    $mail->Body = $emailBody;
    $mail->AltBody = "Hello {$applicant['FirstName']},\n\nYou are invited for an interview on $formattedDate at $formattedTime ($mode).\nLocation/Link: $location\n\nBest Regards,\nMicrofinance HR Team";
    
    $mail->send();

    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Interview scheduled and invitation sent.']);

} catch (Exception $e) {
    $conn->rollback();
    error_log("Scheduling Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Failed to process scheduling: ' . $e->getMessage()]);
} catch (mysqli_sql_exception $e) {
    $conn->rollback();
    error_log("DB Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Database error occurred.']);
}
?>
