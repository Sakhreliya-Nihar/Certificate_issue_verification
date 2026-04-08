<?php
include 'db.php';

// ---------------------------------------------------------
// PART 1: Handle the form submission (Updating the password)
// ---------------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $new_password = $_POST['password'];

    // Validate password strength (Matches your signup page rules)
    if (strlen($new_password) < 8 || 
        !preg_match("/[A-Z]/", $new_password) || 
        !preg_match("/[a-z]/", $new_password) || 
        !preg_match("/[0-9]/", $new_password) || 
        !preg_match("/[\W_]/", $new_password)) {
        
        echo "<script>alert('Weak Password! It must be at least 8 characters long and include an uppercase letter, a lowercase letter, a number, and a special character.'); window.history.back();</script>";
        exit();
    }

    // Hash the new password securely
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Update the password in the database AND delete the used token so it can't be used twice
    $stmt = $conn->prepare("UPDATE user SET password=?, reset_token=NULL, reset_token_expire=NULL WHERE reset_token=?");
    $stmt->bind_param("ss", $hashed_password, $token);
    
    if ($stmt->execute()) {
        echo "<script>alert('Password updated successfully! You can now login.'); window.location.href='index.html';</script>";
        exit();
    } else {
        echo "<script>alert('Error updating password.'); window.location.href='index.html';</script>";
        exit();
    }
}

// ---------------------------------------------------------
// PART 2: Handle the link click (Showing the form)
// ---------------------------------------------------------
if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
    // Check if token exists and has not expired
    date_default_timezone_set('Asia/Kolkata'); 
    $current_time = date("Y-m-d H:i:s");

    $stmt = $conn->prepare("SELECT email FROM user WHERE reset_token=? AND reset_token_expire > ?");
    $stmt->bind_param("ss", $token, $current_time);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        // Token is valid! Show the HTML form.
        ?>
        <!DOCTYPE html>
        <html lang="en" dir="ltr">
        <head>
            <meta charset="UTF-8">
            <title>Create New Password</title>
            <link rel="stylesheet" href="login_style.css">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
        </head>
        <body>
            <div class="container" style="max-width: 500px;">
                <div class="forms">
                    <div class="form-content">
                        <div class="login-form" style="width: 100%;">
                            <div class="title">Create New Password</div>
                            <form action="reset_password.php" method="POST">
                                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                                
                                <div class="input-boxes" style="margin-top: 20px;">
                                    <div class="input-box">
                                        <i class="fas fa-lock"></i>
                                        <input type="password" name="password" placeholder="Enter new password" required>
                                    </div>
                                    <div class="button input-box">
                                        <input type="submit" value="Update Password">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </body>
        </html>
        <?php
    } else {
        // Token is invalid, expired, or already used
        echo "<script>alert('Invalid or expired password reset link. Please request a new one.'); window.location.href='forgot_password.php';</script>";
    }
    $stmt->close();
} else {
    // If someone just types reset_password.php into the URL without a token, send them to login
    header("Location: index.html");
    exit();
}
$conn->close();
?>
