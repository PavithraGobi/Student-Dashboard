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

// Sanitize email to find user folder
$safeEmail = preg_replace("/[^a-zA-Z0-9]/", "_", $user_email);
$directory = "uploads/" . $safeEmail . "/";

if (!isset($_POST['file']) || empty($_POST['file'])) {
    echo "Error: No file specified.";
    exit;
}

$filename = basename($_POST['file']);
$filePath = $directory . $filename;

if (!file_exists($filePath)) {
    echo "Error: File does not exist.";
    exit;
}

if (unlink($filePath)) {
    echo "✅ File deleted successfully!";
} else {
    echo "Error: Could not delete file.";
}
