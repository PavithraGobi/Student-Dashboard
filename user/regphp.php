<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$database = "registration_db";

// Connect to database
$conn = new mysqli($servername, $username, $password, $database);
$conn->set_charset("utf8mb4");

// Check connection
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// Get form data
$username = $_POST['username'];
$email = $_POST['email'];
$date_of_birth = $_POST['date_of_birth'];
$phone_number = $_POST['phone_number'];
$password = $_POST['password'];
$confirm_Password = $_POST['confirm_Password'];
$dept_id = $_POST['dept_id'];  // ✅ added

// Check if passwords match
if ($password !== $confirm_Password) {
    echo "❌ Error: Passwords do not match.";
} else {
    // Check if email already exists
    $check = "SELECT email FROM regform WHERE email = '$email'";
    $result = $conn->query($check);

    if ($result->num_rows > 0) {
        echo " Error: Email address already exists.";
    } else {
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert data with dept_id
        $insert = "INSERT INTO regform (username, email, date_of_birth, phone_number, password, dept_id)
                   VALUES ('$username', '$email', '$date_of_birth', '$phone_number', '$hashedPassword', '$dept_id')";

        if ($conn->query($insert) === TRUE) {
            echo "Registration successful!";
            header("Location: user_login.html");
            exit;
        } else {
            echo "Error: " . $conn->error;
        }
    }
}

// Close connection
$conn->close();
?>
