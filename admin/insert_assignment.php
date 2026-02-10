<?php
session_start();
if (!isset($_SESSION['admin_email'])) {
    header("Location: admin_login.html");
    exit();
}

$conn = new mysqli("localhost", "root", "", "registration_db");
$conn->set_charset("utf8mb4");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $assignment_name = trim($_POST['assignment_name']);
    $course_id = intval($_POST['course_id']);
    $due_date = $_POST['due_date'];
    $description = trim($_POST['description']);
    $dept_id = $_POST['dept_id'] ?? '';

    if ($assignment_name && $course_id && $due_date) {
        $stmt = $conn->prepare("INSERT INTO assignments (assignment_name, course_id, due_date, description) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("siss", $assignment_name, $course_id, $due_date, $description);

        if ($stmt->execute()) {
            // ✅ INSERT NOTIFICATION after successful assignment insert
            $title = "New Assignment Added";
            $message = "Assignment '{$assignment_name}' is available. Due date: {$due_date}";

            $notif_stmt = $conn->prepare("INSERT INTO notifications (title, message) VALUES (?, ?)");
            $notif_stmt->bind_param("ss", $title, $message);
            $notif_stmt->execute();
            $notif_stmt->close();

            // Redirect
            header("Location: manage_assignments.php?dept=" . urlencode($dept_id));
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Missing required fields.";
    }
}
?>
