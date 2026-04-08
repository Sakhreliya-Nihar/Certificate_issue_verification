<?php
// Note: No session_start() or admin check here! This page must be public so anyone can verify.
include 'db.php'; 

$verification_result = null;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['verify_code'])) {
    $code = trim($_POST['verify_code']);

    // Securely query the database and JOIN with the student table to get their actual name
    $sql = "SELECT c.code, c.issue_date, c.status, c.certificate_file, s.first_name, s.last_name, s.register_no 
            FROM certificates c 
            JOIN student s ON c.student_id = s.student_id 
            WHERE c.code = ?";
            
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("s", $code);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            // Certificate found!
            $verification_result = $result->fetch_assoc();
        } else {
            // No match found in the database
            $verification_result = "fake";
        }
        $stmt->close();
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8">
    <title>Verify Certificate | Student Achievement System</title>
    <link rel="stylesheet" href="login_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    <div class="container" style="max-width: 500px;"> 
        <div class="forms">
            <div class="form-content">
                <div class="login-form" style="width: 100%;">
                    
                    <div class="title" style="display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-shield-alt text-indigo-600"></i> Verify Certificate
                    </div>
                    
                    <p style="margin-top: 10px; font-size: 14px; color: #666;">
                        Enter the unique 10-character code found on the certificate to verify its authenticity.
                    </p>
                    
                    <form action="verify_certificate" method="POST">
                        <div class="input-boxes">
                            <div class="input-box">
                                <i class="fas fa-certificate"></i>
                                <input type="text" name="verify_code" placeholder="e.g. AB02CDEFGH" style="text-transform: uppercase; letter-spacing: 2px; font-weight: bold;" required>
                            </div>
                            
                            <div class="button input-box">
                                <input type="submit" value="Verify Now">
                            </div>
                            
                            <div class="text sign-up-text">
                                Go back to the <a href="index">Dashboard</a>
                            </div>
                        </div>
                    </form>

                    <?php if ($verification_result === "fake"): ?>
                        
                        <div style="margin-top: 25px; padding: 15px; background: #ffebee; border-left: 4px solid #f44336; border-radius: 4px;">
                            <h3 style="color: #c62828; margin-bottom: 5px; font-size: 16px; display: flex; align-items: center;">
                                <i class="fas fa-times-circle" style="margin-right: 8px; font-size: 20px;"></i> Invalid Certificate
                            </h3>
                            <p style="color: #d32f2f; font-size: 13px; margin-left: 28px; line-height: 1.4;">
                                This code does not exist in our system. This certificate may be forged or tampered with.
                            </p>
                        </div>

                    <?php elseif (is_array($verification_result)): ?>
                        
                        <div style="margin-top: 25px; padding: 20px; background: #f1f8e9; border-top: 4px solid #4caf50; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                            
                            <div style="display: flex; flex-direction: column; align-items: center; margin-bottom: 15px;">
                                <i class="fas fa-check-circle" style="color: #4caf50; font-size: 40px; margin-bottom: 10px;"></i>
                                <h3 style="color: #2e7d32; font-size: 18px; font-weight: bold; margin-bottom: 5px;">Verified & Authentic</h3>
                                <span style="background: #c8e6c9; color: #2e7d32; font-size: 11px; padding: 3px 10px; border-radius: 20px; font-weight: bold; text-transform: uppercase;">
                                    <?php echo htmlspecialchars($verification_result['status']); ?>
                                </span>
                            </div>
                            
                            <div style="font-size: 13px; color: #444; background: #fff; padding: 15px; border-radius: 4px; border: 1px solid #c8e6c9;">
                                <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #eee; padding-bottom: 8px; margin-bottom: 8px;">
                                    <span style="color: #777; font-weight: 600;">Issued To:</span>
                                    <span style="font-weight: bold; color: #111;"><?php echo htmlspecialchars($verification_result['first_name'] . ' ' . $verification_result['last_name']); ?></span>
                                </div>
                                <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #eee; padding-bottom: 8px; margin-bottom: 8px;">
                                    <span style="color: #777; font-weight: 600;">Register No:</span>
                                    <span style="color: #222;"><?php echo htmlspecialchars($verification_result['register_no']); ?></span>
                                </div>
                                <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #eee; padding-bottom: 8px; margin-bottom: 8px;">
                                    <span style="color: #777; font-weight: 600;">Issue Date:</span>
                                    <span style="color: #222;"><?php echo date('M d, Y', strtotime($verification_result['issue_date'])); ?></span>
                                </div>
                                <div style="display: flex; justify-content: space-between;">
                                    <span style="color: #777; font-weight: 600;">Cert Code:</span>
                                    <span style="color: #5c6bc0; font-family: monospace; font-weight: bold;"><?php echo htmlspecialchars($verification_result['code']); ?></span>
                                </div>
                            </div>
                            
                            <?php if (!empty($verification_result['certificate_file'])): ?>
                                <div style="margin-top: 15px; text-align: center;">
                                    <a href="<?php echo str_replace('../../', '', htmlspecialchars($verification_result['certificate_file'])); ?>" target="_blank" style="display: inline-block; font-size: 13px; background: #e0e0e0; color: #333; padding: 8px 15px; text-decoration: none; border-radius: 4px; transition: 0.3s;">
                                        <i class="fas fa-file-pdf" style="margin-right: 5px;"></i> View Original File
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</body>

</html>