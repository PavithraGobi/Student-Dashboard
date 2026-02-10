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

$safeEmail = preg_replace("/[^a-zA-Z0-9]/", "_", $admin_email);
$directory = "uploads/" . $safeEmail . "/";

if (!isset($_POST['filename']) || empty($_POST['filename'])) {
    echo "Error: No file specified.";
    exit;
}

$filename = basename($_POST['filename']);
$filePath = $directory . $filename;

if (!file_exists($filePath)) {
    echo "Error: File does not exist.";
    exit;
}

if (unlink($filePath)) {
    echo "File deleted successfully!";
} else {
    echo "Error: Could not delete file.";
}
