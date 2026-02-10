<?php
session_start();
if (!isset($_SESSION['admin_email'])) {
    header("Location: admin_login.html");
    exit();
}

$admin_username = $_SESSION['admin_username'] ?? 'Admin';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Dashboard Overview</title>
  <link rel="stylesheet" href="css/admin_dashboards.css" />
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background:  linear-gradient(to right, #e3eafc, #f5f7fa);
      color: #333;
      padding: 40px;
    }

    h2 {
      margin-bottom: 8px;
      color: #0f3460;
      font-size: 26px;
    }

    p {
      margin-bottom: 30px;
      font-size: 16px;
    }

    .dashboard-overview {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      gap: 24px;
    }

    .overview-card {
      background: white;
      color: #0f3460;
      padding: 25px;
      border-radius: 12px;
      text-align: center;
      box-shadow: 0 4px 10px rgba(0,0,0,0.08);
    }

    .overview-card h3 {
      margin-bottom: 8px;
      font-size: 20px;
    }

    .overview-card p {
      font-size: 14px;
      color: #555;
      margin: 0;
    }

    .login-link {
      text-align: center;
      margin-top: 40px;
    }

    .login-link a {
      text-decoration: none;
      color: #0f3460;
      font-weight: bold;
      font-size: 16px;
    }
  </style>
</head>
<body>

  <main>
    <h2>👋 Welcome, <?= htmlspecialchars($admin_username) ?>!</h2>
    <p>This is a quick overview of your dashboard sections:</p>

    <div class="dashboard-overview">
      <div class="overview-card">
        <h3>📚 Courses</h3>
        <p>View and manage course offerings</p>
      </div>

      <div class="overview-card">
        <h3>👨‍🎓 Students</h3>
        <p>Add and manage student records</p>
      </div>

      <div class="overview-card">
        <h3>📝 Assignments</h3>
        <p>Upload and track assignment status</p>
      </div>

      <div class="overview-card">
        <h3>📅 Exams</h3>
        <p>Schedule and update exam details</p>
      </div>

      <div class="overview-card">
        <h3>📢 Announcements</h3>
        <p>Publish important news and updates</p>
      </div>

      <div class="overview-card">
        <h3>📁 Media Files</h3>
        <p>Store course-related documents</p>
      </div>

      <div class="overview-card">
        <h3>🔔 Reminders</h3>
        <p>Create alerts for key events</p>
      </div>
    </div>

    <div class="login-link">
      🔐 <a href="admin_login.html">Login Here</a>
    </div>
  </main>

</body>
</html>
