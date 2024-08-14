<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "attendance_system";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Validate and sanitize input
if (!isset($_POST['studentID']) || !isset($_POST['email']) || empty($_POST['email'])) {
    die("Error: Missing or empty email.");
}

$studentID = $_POST['studentID'];
$email = $_POST['email'];

// Generate QR Code
try {
    $qrCode = new QrCode($email);
    $qrCode->setSize(300);
    $qrCode->setMargin(10);

    $writer = new PngWriter();

    // Define QR Code Path
    $barcodePath = 'barcodes/' . $email . '.png';
    $fullPath = __DIR__ . '/../' . $barcodePath;

    // Ensure the directory exists
    if (!is_dir(dirname($fullPath))) {
        if (!mkdir(dirname($fullPath), 0755, true)) {
            die("Failed to create directory: " . dirname($fullPath));
        }
    }

    // Save QR Code as PNG File
    $result = $writer->write($qrCode);
    file_put_contents($fullPath, $result->getString());

    // Save Student Information
    $sql = "INSERT INTO students (studentID, email, barcode) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("sss", $studentID, $email, $barcodePath);

    if ($stmt->execute()) {
        $webPath = '/AMS/barcodes/' . $email . '.png';
        echo "<h1>Student Registered Successfully!</h1>";
        echo "<img src='$webPath' alt='QR Code' />";
        
        // Notify the teacher's dashboard
        file_put_contents('new_registration.txt', "New registration: $email\n", FILE_APPEND);
    } else {
        echo "Error: " . $stmt->error;
    }

} catch (Exception $e) {
    die("Failed to generate QR code: " . $e->getMessage());
}

$conn->close();
?>
