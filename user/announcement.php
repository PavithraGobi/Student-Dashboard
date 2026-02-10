<?php
session_start();
if (!isset($_SESSION['user_email'])) {
    header("Location: user_login.html");
    exit();
}

$username = $_SESSION['username'] ?? 'User';
$profile_pic = $_SESSION['user_profile_pic'] ?? 'default.png';

$conn = new mysqli("localhost", "root", "", "registration_db");
$conn->set_charset("utf8mb4");

// Fetch all announcements
$result = $conn->query("SELECT title, message, created_at FROM announcements ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Announcements</title>
  <link rel="stylesheet" href="css/dashboard.css" />
  <style>
    .content {
      margin-left: 240px;
      padding: 80px 40px;
    }
    .announcement-card {
      background: #fff;
      border-left: 5px solid #0f3460;
      border-radius: 10px;
      padding: 20px;
      margin-bottom: 20px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .announcement-title {
      font-size: 18px;
      font-weight: bold;
      color: #0f3460;
      margin-bottom: 8px;
    }
    .announcement-message {
      font-size: 15px;
      color: #333;
      margin-bottom: 10px;
      white-space: pre-wrap;
    }
    .announcement-date {
      font-size: 13px;
      color: #777;
      text-align: right;
    }
  </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="header">
  <div><h1>Student Dashboard</h1></div>
  <div class="profile-menu" id="profileMenu">
    <img src="uploads/profile_pics/<?php echo htmlspecialchars($_SESSION['user_profile_pic']); ?>" class="avatar" onclick="toggleProfileDropdown()" />
    <div id="profileDropdown" class="dropdown-content">
      <a href="user_logout.php" onclick="localStorage.clear()">Log Out</a>
    </div>
  </div>
</div>


<div class="content">
  <h2>📢 Announcements</h2>

  <?php if ($result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
      <div class="announcement-card">
        <div class="announcement-title"><?= htmlspecialchars($row['title']) ?></div>
        <div class="announcement-message"><?= nl2br(htmlspecialchars($row['message'])) ?></div>
        <div class="announcement-date">🗓 <?= date('d-m-Y', strtotime($row['created_at'])) ?></div>
      </div>
    <?php endwhile; ?>
  <?php else: ?>
    <p>No announcements available.</p>
  <?php endif; ?>
</div>

<!-- Footer -->
<div class="footer">
  © 2025 Student Dashboard System. All rights reserved.
</div>



<div class="footer">
  © 2025 Student Dashboard System. All rights reserved.
</div>
<script src="js/user_dashboard.js"></script>
</body>
</html>

<?php $conn->close(); ?>
