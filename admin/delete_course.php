<?php
session_start();
if (!isset($_SESSION['admin_email'])) {
    header("Location: admin_login.html");
    exit();
}

$dept = $_GET['dept'] ?? '';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: manage_courses.php" . ($dept ? "?dept=" . urlencode($dept) : ""));
    exit();
}

$id = (int)$_GET['id'];

$conn = new mysqli("localhost", "root", "", "registration_db");
$conn->set_charset("utf8mb4");

$stmt = $conn->prepare("DELETE FROM courses WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $msg = "✅ Course deleted successfully";
} else {
    $msg = "❌ Error deleting course: " . $stmt->error;
}

$stmt->close();
$conn->close();

header("Location: manage_courses.php" . ($dept ? "?dept=" . urlencode($dept) : "") . "&msg=" . urlencode($msg));
exit();
?>
