<?php
include 'db.php';

// Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Require the PHPMailer files from the folder you just set up
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);

    // 1. Check if the email exists in your user table
    $stmt = $conn->prepare("SELECT user_id FROM user WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        // 2. Generate a secure random token and set expiration (1 hour)
        $token = bin2hex(random_bytes(32));
        date_default_timezone_set('Asia/Kolkata'); 
        $expire_time = date("Y-m-d H:i:s", strtotime('+1 hour'));

        // 3. Save the token to the database
        $update_stmt = $conn->prepare("UPDATE user SET reset_token=?, reset_token_expire=? WHERE email=?");
        $update_stmt->bind_param("sss", $token, $expire_time, $email);
        
        if ($update_stmt->execute()) {
            
            // 4. Construct the reset link (UPDATED TO CLEAN URL)
            $reset_link = "http://localhost/Student-Achievement-System/reset_password?token=" . $token;

            // 5. Initialize PHPMailer
            $mail = new PHPMailer(true);

            try {
                // Server settings for Gmail SMTP
                $mail->isSMTP();                                            
                $mail->Host       = 'smtp.gmail.com';                     
                $mail->SMTPAuth   = true;                                   
                $mail->Username   = 'niharsakhreliya4@gmail.com';         
                $mail->Password   = 'mfmfxgrablrbtgep';             
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            
                $mail->Port       = 465;                                    

                // Recipients
                $mail->setFrom('niharsakhreliya4@gmail.com', 'Student Achievement System'); 
                $mail->addAddress($email);     

                // Content
                $mail->isHTML(true);                                  
                $mail->Subject = 'Password Reset Request';
                $mail->Body    = "<h3>Password Reset</h3>
                                  <p>We received a request to reset your password.</p>
                                  <p><a href='" . $reset_link . "'>Click here to set a new password</a></p>
                                  <p>If you did not request this, please ignore this email.</p>
                                  <br>
                                  <p><i>This link will expire in 1 hour.</i></p>";
                $mail->AltBody = "Click the link below to set a new password:\n" . $reset_link;

                // Send the email
                $mail->send();
                // UPDATED TO CLEAN URL
                echo "<script>alert('A password reset link has been sent to your email.'); window.location.href='index';</script>";
                
            } catch (Exception $e) {
                echo "<script>alert('Message could not be sent. Mailer Error: {$mail->ErrorInfo}'); window.history.back();</script>";
            }
        }
        $update_stmt->close();
    } else {
        // UPDATED TO CLEAN URL
        echo "<script>alert('If that email is registered, a reset link has been sent.'); window.location.href='index';</script>";
    }
    
    $stmt->close();
}
$conn->close();
?>