<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hr4";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// PHPMailer configuration
$mail_config = [
    'host' => 'smtp.gmail.com',
    'port' => 587,
    'smtp_secure' => 'tls',
    'smtp_auth' => true,
    'username' => 'suruiz.joshuabcp@gmail.com',
    'password' => 'aovb dqcb sqve rbsa',
    'from_email' => 'suruiz.joshuabcp@gmail.com',
    'from_name' => 'Microfinance System',
    'reply_to' => 'suruiz.joshuabcp@gmail.com',
];

// Xendit configuration
$xendit_config = [
    'secret_key' => 'xnd_development_E2yXH8Yvvha2Yw8NkBZF3JXPOeKvFyY8qELYF0E8zvs3zgpAYQE3ZZ51h42kmx',
    'payout_endpoint' => 'https://api.xendit.co/v2/payouts'
];

// Function to send OTP email using PHPMailer
function sendOtpEmail($toEmail, $otp, $userName = '')
{
    global $mail_config;

    // Import PHPMailer
    require_once __DIR__ . '/../vendor/autoload.php';

    try {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);

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
        $mail->addAddress($toEmail);
        $mail->addReplyTo($mail_config['reply_to']);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Microfinance Login OTP Code';

        $emailBody = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f4f4f4;'>
            <div style='background-color: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);'>
                <div style='text-align: center; margin-bottom: 30px;'>
                    <h2 style='color: #2ca078; margin: 0;'>Microfinance System</h2>
                    <p style='color: #666; margin: 5px 0 0 0;'>Secure Login Verification</p>
                </div>
                
                <div style='background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; text-align: center;'>
                    <h3 style='color: #333; margin: 0 0 10px 0;'>Your OTP Code</h3>
                    <div style='font-size: 32px; font-weight: bold; color: #2ca078; letter-spacing: 5px; margin: 15px 0;'>
                        $otp
                    </div>
                    <p style='color: #666; margin: 10px 0 0 0; font-size: 14px;'>This code will expire in 10 minutes</p>
                </div>
                
                <div style='margin: 30px 0;'>
                    <h4 style='color: #333; margin: 0 0 10px 0;'>Instructions:</h4>
                    <ol style='color: #666; margin: 0; padding-left: 20px;'>
                        <li>Enter the 6-digit code above in the login verification page</li>
                        <li>Do not share this code with anyone</li>
                        <li>If you didn't request this code, please ignore this email</li>
                    </ol>
                </div>
                
                <div style='border-top: 1px solid #eee; padding-top: 20px; margin-top: 30px; text-align: center;'>
                    <p style='color: #999; font-size: 12px; margin: 0;'>
                        This is an automated message from Microfinance System.<br>
                        Please do not reply to this email.
                    </p>
                </div>
            </div>
        </div>";

        $mail->Body = $emailBody;
        $mail->AltBody = "Your OTP code is: $otp\n\nThis code will expire in 10 minutes.\n\nIf you didn't request this code, please ignore this email.";

        $mail->send();
        return true;

    }
    catch (Exception $e) {
        // Log error for debugging
        error_log("PHPMailer Error: " . $e->getMessage());
        return false;
    }
}

// Function to send official hiring email using PHPMailer
function sendHiringEmail($toEmail, $employeeName, $position, $hiringDate, $scores) {
    global $mail_config;
    require_once __DIR__ . '/../vendor/autoload.php';

    try {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = $mail_config['host'];
        $mail->SMTPAuth = $mail_config['smtp_auth'];
        $mail->Username = $mail_config['username'];
        $mail->Password = $mail_config['password'];
        $mail->SMTPSecure = $mail_config['smtp_secure'];
        $mail->Port = $mail_config['port'];

        $mail->setFrom($mail_config['from_email'], $mail_config['from_name']);
        $mail->addAddress($toEmail);
        $mail->isHTML(true);
        $mail->Subject = 'Official Hiring Notification - Microfinance System';
        
        $interviewRating = number_format($scores['InterviewScore'] ?? 0, 1);
        $examScore = $scores['ExamScore'] ?? 0;
        $resumeScore = $scores['ResumeScore'] ?? 0;
        $totalScore = number_format($scores['TotalScore'] ?? 0, 1);
        $decision = $scores['Decision'] ?? 'Approved';

        $emailBody = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f4f4f4;'>
            <div style='background-color: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);'>
                <div style='text-align: center; margin-bottom: 30px;'>
                    <img src='http://localhost/microfinance/img/logo.png' alt='Microfinance System Logo' style='width: 60px; height: 60px; border-radius: 10px; margin-bottom: 10px; display: block; margin-left: auto; margin-right: auto;'>
                    <h2 style='color: #2ca078; margin: 0;'>Microfinance System</h2>
                    <p style='color: #666; margin: 5px 0 0 0;'>Human Resources Department</p>
                </div>
                
                <div style='background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; text-align: center;'>
                    <h3 style='color: #333; margin: 0 0 10px 0;'>Congratulations, $employeeName!</h3>
                    <p style='color: #666; margin: 0 0 12px 0; font-size: 15px;'>You have been officially hired at <strong>Microfinance System</strong> as <strong style='color: #2ca078;'>$position</strong>.</p>
                    <p style='color: #555; margin: 0; font-size: 14px; line-height: 1.7;'>
                        We are thrilled to welcome you to our team! After a thorough review of your application and performance, 
                        we are confident that you will be a great addition to the organization. 
                        Your journey with us starts on <strong>$hiringDate</strong> &mdash; we look forward to working with you.
                    </p>
                </div>

                <div style='margin: 30px 0;'>
                    <h4 style='color: #333; margin: 0 0 10px 0;'>Performance Summary:</h4>
                    <table style='width: 100%; border-collapse: collapse; font-size: 14px;'>
                        <tr style='border-bottom: 1px solid #eee;'>
                            <td style='padding: 10px 0; color: #666;'>Resume Rating</td>
                            <td style='padding: 10px 0; font-weight: bold; color: #333; text-align: right;'>$resumeScore%</td>
                        </tr>
                        <tr style='border-bottom: 1px solid #eee;'>
                            <td style='padding: 10px 0; color: #666;'>Interview Score</td>
                            <td style='padding: 10px 0; font-weight: bold; color: #333; text-align: right;'>$interviewRating / 5.0</td>
                        </tr>
                        <tr style='border-bottom: 1px solid #eee;'>
                            <td style='padding: 10px 0; color: #666;'>Examination Result</td>
                            <td style='padding: 10px 0; font-weight: bold; color: #333; text-align: right;'>$examScore Points</td>
                        </tr>
                        <tr style='border-bottom: 2px solid #2ca078;'>
                            <td style='padding: 12px 0; color: #333; font-weight: bold;'>Final Weighted Result</td>
                            <td style='padding: 12px 0; font-weight: bold; color: #2ca078; text-align: right; font-size: 16px;'>$totalScore%</td>
                        </tr>
                    </table>
                </div>

                <div style='margin: 30px 0;'>
                    <h4 style='color: #333; margin: 0 0 10px 0;'>Employment Details:</h4>
                    <table style='width: 100%; border-collapse: collapse; font-size: 14px;'>
                        <tr style='border-bottom: 1px solid #eee;'>
                            <td style='padding: 10px 0; color: #666;'>Position</td>
                            <td style='padding: 10px 0; font-weight: bold; color: #333; text-align: right;'>$position</td>
                        </tr>
                        <tr style='border-bottom: 1px solid #eee;'>
                            <td style='padding: 10px 0; color: #666;'>Hiring Date</td>
                            <td style='padding: 10px 0; font-weight: bold; color: #333; text-align: right;'>$hiringDate</td>
                        </tr>
                    </table>
                </div>

                <div style='margin: 30px 0;'>
                    <h4 style='color: #333; margin: 0 0 10px 0;'>Next Steps:</h4>
                    <ol style='color: #666; margin: 0; padding-left: 20px; font-size: 14px; line-height: 1.8;'>
                        <li>Our administrative team will set up your work account shortly.</li>
                        <li>You will receive a separate email with your login credentials.</li>
                        <li>Please prepare your original documents for verification on your first day.</li>
                        <li>Contact HR if you have any questions before your start date.</li>
                    </ol>
                </div>

                <div style='text-align: center; margin: 30px 0;'>
                    <a href='http://localhost/microfinance/login.php' style='display: inline-block; padding: 14px 32px; background: #2ca078; color: white; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 15px;'>Access Training Portal &rarr;</a>
                </div>
                
                <div style='border-top: 1px solid #eee; padding-top: 20px; margin-top: 30px; text-align: center;'>
                    <p style='color: #999; font-size: 12px; margin: 0;'>
                        This is an automated message from Microfinance System &mdash; Human Resources Dept.<br>
                        Please do not reply to this email.
                    </p>
                </div>
            </div>
        </div>";

        $mail->Body = $emailBody;
        $mail->send();
        return true;

    }
    catch (Exception $e) {
        error_log("PHPMailer Error: " . $e->getMessage());
        return false;
    }
}
define('GROQ_API_KEY', '');