<?php
session_start();

if (!isset($_SESSION['admin_email'])) {
    header("Location: admin_login.html");
    exit();
}

// Connect to database
$conn = new mysqli("localhost", "root", "", "registration_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and prepare inputs
    $username = $conn->real_escape_string($_POST['username'] ?? '');
    $email = $conn->real_escape_string($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $phone = $conn->real_escape_string($_POST['phone_number'] ?? '');
    $dob = $conn->real_escape_string($_POST['date_of_birth'] ?? '');

    // Basic validation (you can extend this)
    if (empty($username) || empty($email) || empty($password) || empty($dob)) {
        die("Please fill all required fields.");
    }

    // Hash password securely
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Insert query
    $sql = "INSERT INTO students (username, email, password, phone_number, date_of_birth) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("sssss", $username, $email, $password_hash, $phone, $dob);

    if ($stmt->execute()) {
        // Success - redirect
        header("Location: admin_manage_students.php?msg=Student added successfully");
        exit();
    } else {
        echo "Error inserting student: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
