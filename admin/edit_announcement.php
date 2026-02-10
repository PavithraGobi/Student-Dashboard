<?php
session_start();
if (!isset($_SESSION['admin_email'])) {
    header("Location: admin_login.html");
    exit();
}
$admin_profile_pic = $_SESSION['admin_profile_pic'] ?? 'profile.png';  
$conn = new mysqli("localhost", "root", "", "registration_db");
$conn->set_charset("utf8mb4");

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: announcements.php");
    exit();
}

$error = '';
$stmt = $conn->prepare("SELECT title, message FROM announcements WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($title, $message);
if (!$stmt->fetch()) {
    $stmt->close();
    $conn->close();
    header("Location: announcements.php");
    exit();
}
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $newTitle = trim($_POST['title'] ?? '');
    $newMessage = trim($_POST['message'] ?? '');

    if ($newTitle && $newMessage) {
        $updateStmt = $conn->prepare("UPDATE announcements SET title = ?, message = ? WHERE id = ?");
        $updateStmt->bind_param("ssi", $newTitle, $newMessage, $id);
        $updateStmt->execute();
        $updateStmt->close();
        $conn->close();

        $_SESSION['flash_message'] = "Announcement updated successfully!";
        header("Location: announcements.php");
        exit();
    } else {
        $error = "Please fill in both Title and Message.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Edit Announcement</title>
  <link rel="stylesheet" href="css/admin_dashboards.css" />
  <script src="js/admin_dashboard.js"></script>
  <style>
    .content {
      margin-left: 240px;
      padding: 100px 40px 40px;
    }

    form {
      max-width: 500px;
      margin: 0 auto;
      padding: 25px;
      border-radius: 8px;
      background-color: white;
      box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    }

    h2 {
      color: #0f3460;
      font-size: 26px;
      margin-bottom: 24px;
      text-align: center;
    }

    label {
      display: block;
      margin-bottom: 10px;
      font-weight: 500;
    }

    input[type="text"],
    textarea {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 16px;
      margin-top: 5px;
      margin-bottom: 20px;
      resize: vertical;
    }

    button[type="submit"] {
      width: 100%;
      padding: 10px;
      background-color: #0f3460;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-weight: bold;
      font-size: 16px;
    }

    button[type="submit"]:hover {
      background-color: #1f4c87;
    }

    a.back-link {
      display: block;
      margin-top: 20px;
      text-align: center;
      color: #0f3460;
      text-decoration: none;
      font-weight: 500;
    }

    a.back-link:hover {
      text-decoration: underline;
    }

    .error-msg {
      color: #b00020;
      font-weight: bold;
      margin-bottom: 20px;
      text-align: center;
    }
  </style>
</head>
<body>

<div class="container">
  <?php include 'sidebar.php'; ?>

  <div class="header">
    <div><h1>Admin Panel</h1></div>
    <div class="profile-menu" id="profileMenu">
      <img src="uploads/admin_profile_pics/<?php echo htmlspecialchars($admin_profile_pic); ?>" class="avatar" onclick="toggleProfileDropdown()" />
      <div id="profileDropdown" class="dropdown-content">
        <p style="padding: 10px;"><?php echo $_SESSION['admin_email']; ?></p>
        <a href="admin_profile.php">View Profile</a>
        <a href="admin_change_password.php">Change Password</a>
        <a href="admin_logout.php">Logout</a>
      </div>
    </div>
  </div>

  <div class="content">
    <form method="post" action="">
      <h2>✏️ Edit Announcement</h2>

      <label for="title">Title:</label>
      <input type="text" name="title" id="title" value="<?= htmlspecialchars($title) ?>" required>

      <label for="message">Message:</label>
      <textarea name="message" id="message" rows="6" required><?= htmlspecialchars($message) ?></textarea>

      <?php if ($error): ?>
        <div class="error-msg"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <button type="submit">Save Changes</button>
    </form>
    <a href="announcements.php" class="back-link">← Back to Announcements</a>
  </div>
</div>

<div class="footer">
  © 2025 Student Dashboard System. All rights reserved.
</div>
</body>
</html>
