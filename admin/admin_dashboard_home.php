<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) && !isset($_SESSION['user_logged_in']))  {
    header("Location: admin_login.html");
    exit();
}

$admin_username = $_SESSION['admin_username'] ?? 'Admin';
$admin_profile_pic = $_SESSION['admin_profile_pic'] ?? 'profile.png';

$conn = new mysqli("localhost", "root", "", "registration_db");
$conn->set_charset("utf8mb4");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$result = $conn->query("SELECT COUNT(*) AS total_students FROM regform WHERE role='user'");
$total_students = $result ? $result->fetch_assoc()['total_students'] : 0;

$total_courses = 0;
$table_check = $conn->query("SHOW TABLES LIKE 'courses'");
if ($table_check && $table_check->num_rows > 0) {
    $result = $conn->query("SELECT COUNT(*) AS total_courses FROM courses");
    $total_courses = $result ? $result->fetch_assoc()['total_courses'] : 0;
}

include 'sidebar.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="css/admin_dashboards.css">
  <script src="js/admin_dashboard.js"></script>
  <style>
    .content-wrapper {
      margin-left: 240px;
    position:fixed;
      background-color: #F5F7FA;
      min-height: calc(100vh - 100px);
    }

    .card {
      background-color: white;
      padding: 24px;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .card h3 {
      font-size: 1.4em;
      margin-bottom: 10px;
    }

    .card p.count {
      font-size: 2em;
      font-weight: bold;
      margin: 0 0 6px 0;
    }

    .card p.description {
      font-size: 0.9em;
      color: #666;
    }

    .dashboard-grid {
      margin-top: 10px;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 20px;
    }

    #adminHomeContent h1 {
      font-size: 2em;
      margin-bottom: 10px;
    }

    #adminHomeContent p.intro {
      font-size: 1.1em;
      margin-bottom: 30px;
    }
  </style>
</head>
<body>

<!-- Header -->
<div class="header">
  <div><h1>Admin Panel</h1></div>
  <div class="profile-menu" id="profileMenu">
    <img src="uploads/admin_profile_pics/<?php echo htmlspecialchars($admin_profile_pic); ?>" class="avatar" onclick="toggleProfileDropdown()" />
    <div id="profileDropdown" class="dropdown-content">
      <a href="admin_logout.php" onclick="localStorage.clear()">Log Out</a>
    </div>
  </div>
</div>

<!-- Main Content -->
<div class="content-wrapper">
  <div id="adminHomeContent">
    <h1>👋 Welcome <?php echo htmlspecialchars($admin_username); ?>!</h1>
    <p class="intro">This is your central control panel for managing the student dashboard system.</p>

    <div class="dashboard-grid">
      <div class="card">
        <h3>Total Students</h3>
        <p class="count" style="color: purple;"><?php echo $total_students; ?></p>
        <p class="description">Active users in the system</p>
      </div>

      <div class="card">
        <h3>Total Courses</h3>
        <p class="count" style="color: #007bff;"><?php echo $total_courses; ?></p>
        <p class="description">Available courses for students</p>
      </div>
    </div>
  </div>
</div>

<!-- Footer -->
<div class="footer">
  © <?php echo date("Y"); ?> Student Dashboard System. All rights reserved.
</div>

</body>
</html>  