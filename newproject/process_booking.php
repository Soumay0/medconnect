<?php
session_start();
require 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input
    $user_id = $_SESSION['user_id'];
    $doctor_id = mysqli_real_escape_string($conn, $_POST['doctor_id']);
    $service_type = "Consultation"; // Default or get from form
    $date = mysqli_real_escape_string($conn, $_POST['date']);
    $time = mysqli_real_escape_string($conn, $_POST['time']);
    $notes = isset($_POST['notes']) ? mysqli_real_escape_string($conn, $_POST['notes']) : '';
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $status = 'scheduled'; // Default status

    // Validate required fields
    if (empty($doctor_id) || empty($date) || empty($time) || empty($phone)) {
        $_SESSION['error'] = "Please fill all required fields";
        header("Location: index.php");
        exit();
    }

    // Check doctor availability (optional)
    $day_of_week = date('l', strtotime($date));
    $availability_check = mysqli_query($conn,
        "SELECT 1 FROM doctor_availability 
         WHERE doctor_id = '$doctor_id'
         AND day_of_week = '$day_of_week'
         AND is_available = 1
         AND '$time' BETWEEN start_time AND end_time");
    
    if (mysqli_num_rows($availability_check) == 0) {
        $_SESSION['error'] = "Doctor not available at selected time";
        header("Location: index.php");
        exit();
    }

    // Check for existing booking
    $existing_booking = mysqli_query($conn,
        "SELECT 1 FROM appointments
         WHERE doctor_id = '$doctor_id'
         AND appointment_date = '$date'
         AND appointment_time = '$time'");
    
    if (mysqli_num_rows($existing_booking) > 0) {
        $_SESSION['error'] = "This time slot is already booked";
        header("Location: index.php");
        exit();
    }

    // Insert booking
    $stmt = $conn->prepare("INSERT INTO appointments 
                          (user_id, doctor_id, service_type, appointment_date, appointment_time, notes, phone, status) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iissssss", $user_id, $doctor_id, $service_type, $date, $time, $notes, $phone, $status);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Appointment booked successfully!";
        header("Location: dashboard.php");
    } else {
        $_SESSION['error'] = "Booking failed: " . $conn->error;
        header("Location: index.php");
    }
    
    $stmt->close();
    $conn->close();
    exit();
} else {
    header("Location: index.php");
}
?>