<?php
session_start();
if (!isset($_SESSION['user_email'])) {
    header("Location: user_login.html");
    exit();
}

$conn = new mysqli("localhost", "root", "", "registration_db");
$conn->set_charset("utf8mb4");

$student_id = $_SESSION['user_id'];
$assignment_id = $_GET['id'] ?? null;

if (!$assignment_id) {
    die("Invalid assignment.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_FILES['assignment_file']) || $_FILES['assignment_file']['error'] !== UPLOAD_ERR_OK) {
        echo "<script>alert('File upload failed.'); window.history.back();</script>";
        exit();
    }

    $fileName = $_FILES['assignment_file']['name'];
    $fileTmp = $_FILES['assignment_file']['tmp_name'];
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowed = ['pdf', 'doc', 'docx', 'zip'];

    if (!in_array($fileExt, $allowed)) {
        echo "<script>alert('Only PDF, DOC, DOCX, ZIP allowed.'); window.history.back();</script>";
        exit();
    }

    $uploadDir = "uploads/assignments/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $newFileName = uniqid() . '_' . basename($fileName);
    $destination = $uploadDir . $newFileName;

    if (!move_uploaded_file($fileTmp, $destination)) {
        echo "<script>alert('File save error.'); window.history.back();</script>";
        exit();
    }

    // Insert or Update submission record with lowercase 'completed' status
    $check = $conn->prepare("SELECT id FROM assignment_submissions WHERE student_id = ? AND assignment_id = ?");
    $check->bind_param("ii", $student_id, $assignment_id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $check->close();
        $stmt = $conn->prepare("UPDATE assignment_submissions SET file_path = ?, status = 'completed', submitted_at = NOW() WHERE student_id = ? AND assignment_id = ?");
        $stmt->bind_param("sii", $newFileName, $student_id, $assignment_id);
    } else {
        $check->close();
        $stmt = $conn->prepare("INSERT INTO assignment_submissions (student_id, assignment_id, file_path, status, submitted_at) VALUES (?, ?, ?, 'completed', NOW())");
        $stmt->bind_param("iis", $student_id, $assignment_id, $newFileName);
    }

    if ($stmt->execute()) {
        echo "<script>alert('Assignment submitted successfully.'); window.location.href='user_assignment.php';</script>";
    } else {
        echo "<script>alert('Database error.'); window.history.back();</script>";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Submit Assignment</title>
  <link rel="stylesheet" href="css/dashboard.css" />
  <script src="js/user_dashboard.js"></script>
  <style>
    .submit-form-container {
      background: white;
      max-width: 600px;
      margin: 50px auto;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    label, input {
      display: block;
      margin-bottom: 10px;
      width: 100%;
    }
    .submit-btn {
      background: green;
      color: white;
      padding: 10px 20px;
      border: none;
      cursor: pointer;
      border-radius: 5px;
    }
    .submit-btn:hover {
      background: darkgreen;
    }
  </style>
</head>
<body>

<div class="container">
  <aside class="sidebar">
    <div class="sidebar-title">👥 Student Panel</div>
    <a href="user_home.php">🏠 Dashboard</a>
    <a href="user_course.php">📚 Courses</a>
    <a href="user_exam.php">📅 Exams</a>
    <a href="user_assignment.php">📝 Assignments</a>
    <a href="announcement.php">📢 Announcements</a>
    <a href="notifications.php">🔔 Notifications</a>
    <a href="user_media.php">🎬 Media</a>
    <li class="settings-toggle" onclick="toggleSettings(this)">⚙️ Settings <span class="arrow">▼</span></li>
    <ul class="submenu">
      <li><a href="user_profile.php">🙍 My Profile</a></li>
      <li><a href="user_change_password.php">🔑 Change Password</a></li>
      <li><a href="user_privacy.php">🔒 Privacy</a></li>
    </ul>
  </aside>

  <div class="header">
    <div><h1>Student Dashboard</h1></div>
    <div class="profile-menu" id="profileMenu">
      <img src="uploads/profile_pics/<?php echo htmlspecialchars($_SESSION['user_profile_pic']); ?>" class="avatar" onclick="toggleProfileDropdown()" />
      <div id="profileDropdown" class="dropdown-content">
        <a href="user_logout.php" onclick="localStorage.clear()">Log Out</a>
      </div>
    </div>
  </div>

  <div class="main-content">
    <div class="submit-form-container">
      <h2>Submit Your Assignment</h2>
      <form method="POST" enctype="multipart/form-data">
        <label for="assignment_file">Select file (pdf, doc, docx, zip):</label>
        <input type="file" name="assignment_file" id="assignment_file" required />
        <button type="submit" class="submit-btn">Submit Assignment</button>
      </form>
      <br />
      <a href="user_assignment.php">⬅ Back to My Assignments</a>
    </div>
  </div>
</div>

<script>
  function toggleProfileDropdown() {
    const dropdown = document.getElementById("profileDropdown");
    dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
  }
  window.onclick = function(event) {
    if (!event.target.matches('.avatar')) {
      const dropdown = document.getElementById("profileDropdown");
      if (dropdown && dropdown.style.display === "block") {
        dropdown.style.display = "none";
      }
    }
  };
</script>

<div class="footer">
  © 2025 Student Dashboard System. All rights reserved.
</div>
</body>
</html>
