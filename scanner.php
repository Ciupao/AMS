<?php
require __DIR__ . '/vendor/autoload.php'; // Adjusted path

use Zxing\QrReader; // Using the Zxing library for QR code scanning

session_start();

if (!isset($_SESSION['teacher'])) {
    header("Location: teachersLogin.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "attendance_system";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['qr_code']) && $_FILES['qr_code']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['qr_code']['tmp_name'];

        // Decode QR Code
        $qrCode = new QrReader($fileTmpPath);
        $text = $qrCode->text();

        if ($text) {
            // Mark Attendance
            $teacher_email = $_SESSION['teacher'];
            $teacher_sql = "SELECT id FROM teachers WHERE email = '$teacher_email'";
            $teacher_result = $conn->query($teacher_sql);
            $teacher = $teacher_result->fetch_assoc();

            $student_sql = "SELECT * FROM students WHERE email = '$text'";
            $student_result = $conn->query($student_sql);

            if ($student_result->num_rows > 0) {
                $student = $student_result->fetch_assoc();
                $student_id = $student['studentID'];

                $attendance_sql = "INSERT INTO attendance (studentID, teacherID, attended_at) VALUES (?, ?, NOW())";
                $stmt = $conn->prepare($attendance_sql);
                $stmt->bind_param("ii", $student_id, $teacher['id']);

                if ($stmt->execute()) {
                    echo "<script>alert('Student " . htmlspecialchars($student['email']) . " has been marked present.');</script>";
                    header("Location: /AMS/dashboard.php");
                } else {
                    echo "Error marking attendance: " . $conn->error;
                }
            } else {
                echo "Invalid QR code.";
            }
        } else {
            echo "Failed to decode QR code.";
        }
    } else {
        echo "No QR code uploaded or there was an error with the upload.";
    }
} else {
    echo "Invalid request.";
}

$conn->close();
?>
