<?php
session_start();
require 'config.php';

$error = '';
$success = '';

if (isset($_GET['token'])) {
    $token = mysqli_real_escape_string($conn, $_GET['token']);
    
    // Check if token exists and is not expired
    $result = mysqli_query($conn, "SELECT * FROM password_resets WHERE token='$token' AND expires_at > NOW()");
    if (mysqli_num_rows($result) == 0) {
        $error = "Invalid or expired token";
    } else {
        $reset = mysqli_fetch_assoc($result);
        $email = $reset['email'];
    }
} else {
    $error = "No token provided";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($email)) {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($password !== $confirm_password) {
        $error = "Passwords don't match";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Try to update in users table
        $updated = mysqli_query($conn, "UPDATE users SET password='$hashed_password' WHERE email='$email'");
        
        if (mysqli_affected_rows($conn) == 0) {
            // If not in users table, try admins table
            $updated = mysqli_query($conn, "UPDATE admins SET password_hash='$hashed_password' WHERE email='$email'");
        }
        
        if (mysqli_affected_rows($conn) > 0) {
            // Delete the token
            mysqli_query($conn, "DELETE FROM password_resets WHERE token='$token'");
            $success = "Password updated successfully! You can now <a href='login.php'>login</a>.";
        } else {
            $error = "Failed to update password";
        }
    }
}
?>