
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration</title>
</head>
<body>
    <h1>Student Registration</h1>
    <form action="includes/registration.php" method="POST">
        <label for="studentID">student ID</label>
        <input type="text" id="studentID" name="studentID" required><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br>

        <button type="submit">Register</button>
    </form>
</body>
</html>
