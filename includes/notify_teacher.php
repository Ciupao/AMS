<?php

$studentID = $_POST['studentID'];
$email = $_POST['email'];

// Here, you would add code to update the teacher's dashboard or database
// For example, insert a notification record into a "notifications" table

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "attendance_system";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "INSERT INTO notifications (studentID, email, messenge) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo "Prepare failed: " . $conn->error;
    exit;
}

$message = "New student registered: $studentID ($email)";
$stmt->bind_param("sss", $studentID, $email, $messenge);

if ($stmt->execute()) {
    echo "Notification sent.";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
