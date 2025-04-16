<?php
session_start();
require 'config.php';

// Load PHPMailer manually
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    // Check if email exists
    $exists = mysqli_query($conn, "SELECT email FROM users WHERE email='$email' 
              UNION SELECT email FROM admins WHERE email='$email'");
    
    if (mysqli_num_rows($exists) == 0) {
        $error = "Email not found in our system";
    } else {
        // Generate token
        $token = bin2hex(random_bytes(32));
        $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        // Store token
        mysqli_query($conn, "INSERT INTO password_resets (email, token, expires_at) 
                           VALUES ('$email', '$token', '$expires_at')
                           ON DUPLICATE KEY UPDATE token='$token', expires_at='$expires_at'");
        
        $mail = new PHPMailer(true);
        $mail->SMTPDebug = SMTP::DEBUG_SERVER; // Enable debugging
        
        try {
            // ===== CHOOSE ONE SMTP OPTION BELOW =====
            
            // OPTION 1: Mailtrap (Testing - Always Works)
            // $mail->isSMTP();
            // $mail->Host = 'sandbox.smtp.mailtrap.io';
            // $mail->SMTPAuth = true;
            // $mail->Username = '1a2b3c4d5e6f7g'; // Replace with your Mailtrap creds
            // $mail->Password = '1a2b3c4d5e6f7g'; // Replace with your Mailtrap creds
            // $mail->Port = 2525;
            // $mail->SMTPSecure = 'tls';
            
            // OPTION 2: Gmail (Production)
            
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'your@gmail.com'; // Your Gmail
            $mail->Password = 'your-app-password'; // Gmail App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;
            
            
            // Email content
            $mail->setFrom('no-reply@medconnect.com', 'MedConnect');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $reset_link = "http://".$_SERVER['HTTP_HOST']."/Project1/reset_password.php?token=$token";
            $mail->Body = "Click <a href='$reset_link'>here</a> to reset your password";
            
            if($mail->send()) {
                $success = "Password reset link sent! Check your email.";
            } else {
                $error = "Failed to send email. Please try again later.";
            }
        } catch (Exception $e) {
            $error = "Mailer Error: " . $mail->ErrorInfo;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | MedConnect</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        header {
            background: rgba(255, 255, 255, 0.95);
            padding: 1.5rem 5%;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        }
        
        .logo a {
            font-size: 1.8rem;
            font-weight: 700;
            color: #6c63ff;
            text-decoration: none;
        }
        
        .logo span {
            color: #ff6584;
        }
        
        .container {
            max-width: 500px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        h2 {
            color: #6c63ff;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-control {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-size: 1rem;
        }
        
        .btn {
            width: 100%;
            padding: 1rem;
            background: #6c63ff;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            background: #4d44db;
        }
        
        .error {
            color: #ff6584;
            margin-bottom: 1rem;
            text-align: center;
        }
        
        .success {
            color: #28a745;
            margin-bottom: 1rem;
            text-align: center;
        }
        
        .login-link {
            text-align: center;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <a href="index.php">Med<span>Connect</span></a>
        </div>
    </header>
    
    <div class="container">
        <h2>Forgot Password</h2>
        
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php else: ?>
            <form method="POST" action="forgot_password.php">
                <div class="form-group">
                    <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                </div>
                <button type="submit" class="btn">Send Reset Link</button>
            </form>
        <?php endif; ?>
        
        <div class="login-link">
            Remember your password? <a href="login.php">Login here</a>
        </div>
    </div>
</body>
</html>