<?php
session_start();
if (!isset($_SESSION['admin_email'])) {
    header("Location: admin_login.html");
    exit();
}

$conn = new mysqli("localhost", "root", "", "registration_db");
$conn->set_charset("utf8mb4");

// Validate inputs
$course_id = $_POST['course_id'] ?? '';
$dept_id = $_POST['dept'] ?? '';
$file_title = trim($_POST['file_title'] ?? '');

if (!$course_id || !$file_title || !isset($_FILES['file'])) {
    die("Missing required fields.");
}

// IMPORTANT: Adjust upload directory to point outside admin folder
$uploadDir = "../uploads/subject_files/";

// Create directory if not exists
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$fileName = basename($_FILES['file']['name']);
$safeFileName = time() . "_" . preg_replace("/[^a-zA-Z0-9_.]/", "_", $fileName);

$targetFile = $uploadDir . $safeFileName;
$fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

// Allowed file types
$allowedTypes = ['pdf', 'docx', 'pptx', 'xlsx', 'txt'];
if (!in_array($fileType, $allowedTypes)) {
    die("Invalid file type. Only PDF, DOCX, PPTX, XLSX, TXT are allowed.");
}

// Move uploaded file to new location
if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
    // Save relative path in DB (relative to task folder)
    $relativePath = "uploads/subject_files/" . $safeFileName;

    $stmt = $conn->prepare("INSERT INTO subject_files (course_id, file_title, file_path) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $course_id, $file_title, $relativePath);
    if ($stmt->execute()) {
        // Redirect back to manage courses page with department filter
        header("Location: manage_courses.php?dept=" . urlencode($dept_id));
        exit();
    } else {
        echo "Error inserting file info into database: " . $stmt->error;
    }
    $stmt->close();
} else {
    echo "File upload failed.";
}
?>
