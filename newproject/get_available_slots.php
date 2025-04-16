<?php
require 'db_connection.php';

$doctor_id = $_GET['doctor_id'];
$date = $_GET['date'];
$day_of_week = date('l', strtotime($date));

// Get doctor's availability
$availability = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT start_time, end_time 
     FROM doctor_availability 
     WHERE doctor_id = $doctor_id 
     AND day_of_week = '$day_of_week'
     AND is_available = 1"));

if (!$availability) {
    die('<option value="">Doctor not available this day</option>');
}

// Get booked appointments
$booked_slots = [];
$result = mysqli_query($conn,
    "SELECT appointment_time 
     FROM appointments 
     WHERE doctor_id = $doctor_id 
     AND appointment_date = '$date'");
while ($row = mysqli_fetch_assoc($result)) {
    $booked_slots[] = $row['appointment_time'];
}

// Generate time slots (30-minute intervals)
$start = strtotime($availability['start_time']);
$end = strtotime($availability['end_time']);
$options = ['<option value="">Select time</option>'];

for ($time = $start; $time < $end; $time += 1800) { // 30-minute intervals
    $time_value = date('H:i:s', $time);
    $time_display = date('g:i A', $time);
    
    if (!in_array($time_value, $booked_slots)) {
        $options[] = "<option value='$time_value'>$time_display</option>";
    }
}

echo implode('', $options);