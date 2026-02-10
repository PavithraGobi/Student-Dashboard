<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(403);
    echo "Unauthorized";
    exit;
}

$admin_email = $_SESSION['admin_email'] ?? '';
if (!$admin_email) {
    http_response_code(403);
    echo "Unauthorized";
    exit;
}

// Sanitize folder name
$safeEmail = preg_replace("/[^a-zA-Z0-9]/", "_", $admin_email);
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

$cleanFilename = time() . "_" . preg_replace("/[^a-zA-Z0-9_\-\.]/", "_", $filename);
$targetPath = $directory . $cleanFilename;

if (move_uploaded_file($file['tmp_name'], $targetPath)) {
    echo "File uploaded successfully!";
} else {
    echo "Error: Failed to save file.";
}
