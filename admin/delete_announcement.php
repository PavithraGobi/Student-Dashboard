<?php
session_start();
if (!isset($_SESSION['admin_email'])) {
    header("Location: admin_login.html");
    exit();
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: announcements.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "registration_db");
$conn->set_charset("utf8mb4");

$stmt = $conn->prepare("DELETE FROM announcements WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();
$conn->close();

$_SESSION['flash_message'] = "Announcement deleted successfully!";
header("Location: announcements.php");
exit();
