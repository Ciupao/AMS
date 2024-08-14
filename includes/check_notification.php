<?php
session_start();

if (!isset($_SESSION['teacher'])) {
    header("Location: teachersLogin.php");
    exit();
}

header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "attendance_system";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$teacher_email = $_SESSION['teacher'];
$teacher_sql = "SELECT id FROM teachers WHERE email = '$teacher_email'";
$teacher_result = $conn->query($teacher_sql);
$teacher = $teacher_result->fetch_assoc();
$teacher_id = $teacher['id'];

$last_check_time = $_SESSION['last_check_time'] ?? date('Y-m-d H:i:s', strtotime('-5 minutes'));

$sql = "SELECT email FROM students 
        WHERE created_at > '$last_check_time'";
$result = $conn->query($sql);

$new_registration = $result->num_rows > 0;

if ($new_registration) {
    $row = $result->fetch_assoc();
    $response = [
        'new_registration' => true,
        'student_email' => $row['email']
    ];
} else {
    $response = ['new_registration' => false];
}

$_SESSION['last_check_time'] = date('Y-m-d H:i:s');

echo json_encode($response);

$conn->close();
?>
