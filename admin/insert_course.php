<?php
session_start();

if (!isset($_SESSION['admin_email'])) {
    header("Location: admin_login.html");
    exit();
}

$conn = new mysqli("localhost", "root", "", "registration_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_name = $conn->real_escape_string($_POST['course_name']);
    $description = $conn->real_escape_string($_POST['description']);

    if (!empty($_POST['id'])) {
        // UPDATE existing course
        $id = intval($_POST['id']);
        $sql = "UPDATE courses SET course_name = '$course_name', description = '$description' WHERE id = $id";

        if ($conn->query($sql) === TRUE) {
            header("Location: manage_courses.php?msg=updated");
            exit();
        } else {
            echo "Error updating record: " . $conn->error;
        }
    } else {
        // INSERT new course
        $sql = "INSERT INTO courses (course_name, description, created_at) VALUES ('$course_name', '$description', NOW())";

        if ($conn->query($sql) === TRUE) {
            header("Location: manage_courses.php?msg=added");
            exit();
        } else {
            echo "Error inserting record: " . $conn->error;
        }
    }
} else {
    echo "Invalid request.";
}

$conn->close();
?>
