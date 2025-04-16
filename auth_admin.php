<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

// Optional: Check if admin still exists in database
require 'config.php';
$check = mysqli_query($conn, "SELECT id FROM admins WHERE id=".$_SESSION['admin_id']);
if (mysqli_num_rows($check) == 0) {
    session_destroy();
    header("Location: ../login.php?error=Admin account no longer exists");
    exit();
}
?>