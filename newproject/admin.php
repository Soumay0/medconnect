<?php
require 'config.php';

$name = "Main Admin";
$email = "admin@medconnect.com";
$password = "your_secure_password"; // Change this!
$role = "superadmin";

$hashed_password = password_hash($password, PASSWORD_BCRYPT);

mysqli_query($conn, "INSERT INTO admins (name, email, password_hash, role) 
                    VALUES ('$name', '$email', '$hashed_password', '$role')");

echo "Admin account created successfully!";
?>