<?php
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

$teacher_email = $_SESSION['teacher'];
$teacher_sql = "SELECT id FROM teachers WHERE email = '$teacher_email'";
$teacher_result = $conn->query($teacher_sql);
$teacher = $teacher_result->fetch_assoc();
$teacher_id = $teacher['id'];

$attendance_sql = "SELECT students.email, attendance.attended_at 
                    FROM attendance
                    JOIN students ON attendance.studentID = students.studentID
                    WHERE attendance.teacherID = '$teacher_id'
                    ORDER BY attendance.attended_at DESC";
$attendance_result = $conn->query($attendance_sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            function checkForNotifications() {
                $.ajax({
                    url: "includes/check_notifications.php", // The file that checks for new notifications
                    method: "GET",
                    success: function(data) {
                        if (data.new_registration) {
                            alert("New student registration: " + data.student_email);
                        }
                    }
                });
            }

            // Check for notifications every 5 seconds
            setInterval(checkForNotifications, 5000);
        });
    </script>
</head>
<body>
    <h1>Welcome, Teacher</h1>
    <p><a href="logout.php">Logout</a></p>

    <h2>Scan Student QR Code</h2>
    <form action="scanner.php" method="POST" enctype="multipart/form-data">
        <label for="qr_code">Upload QR Code Image:</label>
        <input type="file" id="qr_code" name="qr_code" accept="image/png, image/jpeg" required><br>
        <button type="submit">Scan</button>
    </form>

    <h2>Attendance Records</h2>
    <table>
        <thead>
            <tr>
                <th>Student Email</th>
                <th>Attended At</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($attendance_result->num_rows > 0) {
                while ($row = $attendance_result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['attended_at']) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='2'>No records found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>

<?php
$conn->close();
?>
