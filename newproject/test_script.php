<?php
$conn = mysqli_connect("localhost", "root", "", "medconnect");
$sql = "INSERT INTO appointments 
        (user_id, doctor_id, service_type, appointment_date, appointment_time, phone) 
        VALUES (1, 1, 'Test', '2023-12-31', '10:00:00', '1234567890')";

if (mysqli_query($conn, $sql)) {
    echo "Test insertion worked! Last ID: " . mysqli_insert_id($conn);
} else {
    echo "Error: " . mysqli_error($conn);
}
?>