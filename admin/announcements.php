<?php
session_start();
if (!isset($_SESSION['admin_email'])) {
    header("Location: admin_login.html");
    exit();
}
$admin_profile_pic = $_SESSION['admin_profile_pic'] ?? 'profile.png';  // ← Here: use session variable with fallback

$conn = new mysqli("localhost", "root", "", "registration_db");
$conn->set_charset("utf8mb4");

// Fetch announcements from the database
$result = $conn->query("SELECT id, title, message, created_at FROM announcements ORDER BY created_at DESC");

$flash_message = '';
if (isset($_SESSION['flash_message'])) {
    $flash_message = $_SESSION['flash_message'];
    unset($_SESSION['flash_message']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Announcements</title>
  <link rel="stylesheet" href="css/admin_dashboards.css" />
  <style>
    .content {
      margin-left: 240px;
      padding: 80px 40px 40px;
    }
    .announcements-container {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: 20px;
    }
    .announcement-card {
      background-color: white;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
      padding: 20px;
      transition: 0.3s ease;
      border-left: 6px solid #0f3460;
    }
    .announcement-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
    }
    .announcement-title {
      font-size: 18px;
      color: #0f3460;
      margin-bottom: 10px;
      font-weight: bold;
    }
    .announcement-message {
      font-size: 15px;
      color: #333;
      margin-bottom: 12px;
      white-space: pre-wrap;
    }
    .announcement-date {
      font-size: 13px;
      color: #777;
      text-align: right;
    }
    .add-btn {
      background-color: #0f3460;
      color: white;
      padding: 10px 20px;
      border-radius: 6px;
      text-decoration: none;
      font-weight: bold;
      display: inline-block;
      margin-bottom: 20px;
    }
    .add-btn:hover {
      background-color: #1f4c87;
    }
    .flash-message {
      background:#d4edda;
      color:#155724;
      border:1px solid #c3e6cb;
      padding: 12px;
      border-radius: 5px;
      margin-bottom: 20px;
      opacity: 1;
      transition: opacity 1s ease-out;
    }
    .action-links a {
      font-weight: bold;
      text-decoration: none;
      margin-left: 15px;
      cursor: pointer;
    }
    .action-links a.edit {
      color: #0f3460;
    }
    .action-links a.delete {
      color: #b00020;
    }
  </style>
  <script>
    function toggleSettings(el) {
      document.querySelectorAll('.settings-toggle').forEach(item => {
        if (item !== el) {
          item.classList.remove('open');
          const submenu = item.nextElementSibling;
          if (submenu && submenu.classList.contains('submenu')) submenu.style.display = 'none';
          const arrow = item.querySelector('.arrow');
          if (arrow) arrow.textContent = '▼';
        }
      });

      const isOpening = !el.classList.contains('open');
      el.classList.toggle('open');
      const submenu = el.nextElementSibling;
      if (submenu && submenu.classList.contains('submenu')) submenu.style.display = isOpening ? 'block' : 'none';
      const arrow = el.querySelector('.arrow');
      if (arrow) arrow.textContent = isOpening ? '▲' : '▼';
    }

    function toggleProfileDropdown() {
      document.getElementById('profileMenu').classList.toggle('show');
    }

    window.onclick = function(e) {
      if (!e.target.matches('.avatar')) {
        const dropdown = document.getElementById("profileDropdown");
        if (dropdown && dropdown.parentElement.classList.contains("show")) {
          dropdown.parentElement.classList.remove("show");
        }
      }
    };

    window.onload = function() {
      const flash = document.getElementById('flashMessage');
      if (flash) {
        setTimeout(() => { flash.style.opacity = '0'; }, 3000);
        setTimeout(() => { if (flash.parentNode) flash.parentNode.removeChild(flash); }, 4000);
      }
    }
  </script>
</head>
<body>

<div class="container">
  <aside class="sidebar">
    <div class="sidebar-title">👑 Admin Panel</div>
    <a href="admin_dashboard_home.php">🏠 Dashboard</a>
    <li class="settings-toggle" onclick="toggleSettings(this)">
      🎓 Users Management <span class="arrow">▼</span>
    </li>
    <ul class="submenu">
      <li><a href="admin_users_management.php">👥 All Users</a></li>
      <li><a href="add_user.php">➕ Add User</a></li>
      <li><a href="manage_roles.php">🛡️ Manage Roles</a></li>
    </ul>
    <a href="admin_manage_students.php">👥 Registered Students</a>
    <a href="manage_courses.php">📚 Manage Courses</a>
    <a href="manage_assignments.php">📝 Manage Assignments</a>
    <a href="manage_exams.php">📅 Manage Exams</a>
    <a href="announcements.php">📢 Announcements</a>
    <a href="admin_media.php">🎬Media</a>
    <a href="admin_reminders.php">⏰Reminders</a>
    <li class="settings-toggle" onclick="toggleSettings(this)">
      ⚙️ Settings <span class="arrow">▼</span>
    </li>
    <ul class="submenu">
      <li><a href="admin_profile.php">🙍 My Profile</a></li>
      <li><a href="admin_change_password.php">🔑 Change Password</a></li>
      <li><a href="admin_privacy.php">🔒 Privacy</a></li>
    </ul>
  </aside>
  <div class="header">
  <div><h1>Admin Panel</h1></div>
  <div class="profile-menu" id="profileMenu">
    <img src="uploads/admin_profile_pics/<?php echo htmlspecialchars($admin_profile_pic); ?>" class="avatar" onclick="toggleProfileDropdown()" />
    <div id="profileDropdown" class="dropdown-content">
      <a href="admin_logout.php" onclick="localStorage.clear()">Log Out</a>
    </div>
  </div>
</div>
  <div class="content">
    <?php if (!empty($flash_message)): ?>
      <div class="flash-message" id="flashMessage"><?= htmlspecialchars($flash_message) ?></div>
    <?php endif; ?>

    <a href="add_announcement.php" class="add-btn">➕ Add Announcement</a>

    <div class="announcements-container">
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="announcement-card">
          <div class="announcement-title"><?= htmlspecialchars($row['title']) ?></div>
          <div class="announcement-message"><?= nl2br(htmlspecialchars($row['message'])) ?></div>
          <div class="announcement-date">🗓 <?= date('m/d/Y', strtotime($row['created_at'])) ?></div>
          <div class="action-links" style="text-align: right; margin-top: 10px;">
            <a href="edit_announcement.php?id=<?= $row['id'] ?>" class="edit">✏️ Edit</a>
            <a href="delete_announcement.php?id=<?= $row['id'] ?>" class="delete" onclick="return confirm('Are you sure you want to delete this announcement?');">🗑️ Delete</a>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  </div>
</div>
<!-- Footer -->
<div class="footer">
  © 2025 Student Dashboard System. All rights reserved.
</div>
</body>
</html>
<?php $conn->close(); ?>
