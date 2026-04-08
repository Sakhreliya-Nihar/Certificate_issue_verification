<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8">
    <title>Forgot Password | Student Achievement System</title>
    <link rel="stylesheet" href="login_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    <div class="container" style="max-width: 500px;"> 
        <div class="forms">
            <div class="form-content">
                <div class="login-form" style="width: 100%;">
                    <div class="title">Reset Password</div>
                    <p style="margin-top: 10px; font-size: 14px; color: #666;">Enter your email address and we'll send you a link to reset your password.</p>
                    
                    <form action="send_reset_link" method="POST">
                        <div class="input-boxes">
                            <div class="input-box">
                                <i class="fas fa-envelope"></i>
                                <input type="email" name="email"
                                    pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$"
                                    placeholder="Enter your email" required>
                            </div>
                            
                            <div class="button input-box">
                                <input type="submit" value="Send Reset Link">
                            </div>
                            
                            <div class="text sign-up-text">Remember your password? <a href="index.html">Login here</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>