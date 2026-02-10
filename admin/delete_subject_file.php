<?php
session_start();
if (!isset($_SESSION['admin_email'])) {
    header("Location: admin_login.html");
    exit();
}

$conn = new mysqli("localhost", "root", "", "registration_db");
$conn->set_charset("utf8mb4");

$file_id = $_GET['id'] ?? null;
$dept = $_GET['dept'] ?? '';

if ($file_id) {
    // Get file path before deleting from DB
    $stmt = $conn->prepare("SELECT file_path FROM subject_files WHERE id = ?");
    $stmt->bind_param("i", $file_id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $file = $res->fetch_assoc();
        $file_path = $file['file_path'];

        // Delete from DB
        $delStmt = $conn->prepare("DELETE FROM subject_files WHERE id = ?");
        $delStmt->bind_param("i", $file_id);
        $delStmt->execute();

        // Delete actual file from filesystem
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }
}

// Redirect back to department
header("Location: /task/admin/manage_courses.php?dept=" . urlencode($dept));
exit();

?>
