<?php
session_start();
if (!isset($_SESSION['admin_email'])) {
    header("Location: login.html");
    exit();
}
$conn = new mysqli("localhost", "root", "", "registration_db");

$id = $_GET['id'];
$assignment_name = $_POST['assignment_name'];
$course = $_POST['course'];
$due_date = $_POST['due_date'];
$description = $_POST['description'];

$stmt = $conn->prepare("UPDATE assignments SET assignment_name=?, course=?, due_date=?, description=? WHERE id=?");
$stmt->bind_param("ssssi", $assignment_name, $course, $due_date, $description, $id);

if ($stmt->execute()) {
    header("Location: manage_assignments.php");
    exit();
} else {
    echo "Update failed: " . $stmt->error;
}
?>
