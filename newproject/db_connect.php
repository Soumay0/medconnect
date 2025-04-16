<?php
$host = "localhost";
$user = "root"; // Default XAMPP MySQL username
$password = ""; // Default is empty for XAMPP
$database = "medconnect"; // Make sure this database exists

$conn = mysqli_connect($host, $user, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
