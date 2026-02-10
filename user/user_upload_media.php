<?php
session_start();

if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    http_response_code(403);
    echo "Unauthorized";
    exit;
}

$user_email = $_SESSION['user_email'] ?? '';
if (!$user_email) {
    http_response_code(403);
    echo "Unauthorized";
    exit;
}

// Sanitize email to use in directory name
$safeEmail = preg_replace("/[^a-zA-Z0-9]/", "_", $user_email);
$directory = "uploads/" . $safeEmail . "/";
$allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'mp3', 'wav', 'ogg'];
$maxFileSize = 10 * 1024 * 1024; // 10MB

if (!isset($_FILES['media_file']) || $_FILES['media_file']['error'] !== UPLOAD_ERR_OK) {
    echo "Error: No file uploaded or upload error.";
    exit;
}

$file = $_FILES['media_file'];
$filename = basename($file['name']);
$fileExt = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

if (!in_array($fileExt, $allowedTypes)) {
    echo "Error: Invalid file type. Allowed types: " . implode(", ", $allowedTypes);
    exit;
}

if ($file['size'] > $maxFileSize) {
    echo "Error: File too large. Maximum size is 10MB.";
    exit;
}

if (!is_dir($directory)) {
    if (!mkdir($directory, 0755, true)) {
        echo "Error: Failed to create upload directory.";
        exit;
    }
}

// Sanitize file name
$cleanFilename = time() . "_" . preg_replace("/[^a-zA-Z0-9_\-\.]/", "_", $filename);
$targetPath = $directory . $cleanFilename;

if (move_uploaded_file($file['tmp_name'], $targetPath)) {
    echo "✅ File uploaded successfully!";
} else {
    echo "Error: Failed to save file.";
}
