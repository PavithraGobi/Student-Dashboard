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
    </ul>

    <a href="admin_manage_students.php">👥 Registered Students</a>
    <a href="manage_courses.php">📚 Manage Courses</a>

    <li class="settings-toggle" onclick="toggleSettings(this)">
      📝 Assignments <span class="arrow">▼</span>
    </li>
    <ul class="submenu">
      <li><a href="manage_assignments.php">Manage Assignments</a></li>
      <li><a href="assignment_status.php">View Submission Status</a></li>
    </ul>

    <a href="manage_exams.php">📅Exam schedule</a>
    <a href="announcements.php">📢 Announcements</a>
    <a href="admin_media.php">🎬 Media</a>
    <a href="admin_reminders.php">⏰ Reminders</a>

    <li class="settings-toggle" onclick="toggleSettings(this)">
      ⚙️ Settings <span class="arrow">▼</span>
    </li>
    <ul class="submenu">
      <li><a href="admin_profile.php">🙍 My Profile</a></li>
      <li><a href="admin_change_password.php">🔑 Change Password</a></li>
      <li><a href="admin_privacy.php">🔒 Privacy</a></li>
    </ul>
  </aside>
</div>
