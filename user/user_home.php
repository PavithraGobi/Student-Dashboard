<?php
session_start();
if (!isset($_SESSION['user_logged_in'])) {
    header("Location: user_login.html");
    exit();
}

$conn = new mysqli("localhost", "root", "", "registration_db");
$conn->set_charset("utf8mb4");

$user_id = $_SESSION['user_id'] ?? 0;
$dept_id = $_SESSION['user_dept_id'] ?? 0;
$user_profile_pic = $_SESSION['user_profile_pic'] ?? 'profile.png';
$user_name = $_SESSION['user_username'] ?? 'Student';

// Fetch assignment count
$assignmentCount = 0;
$stmt1 = $conn->prepare("SELECT COUNT(*) AS total FROM assignments WHERE course_id IN (SELECT id FROM courses WHERE dept_id = ?)");
$stmt1->bind_param("i", $dept_id);
$stmt1->execute();
$res1 = $stmt1->get_result()->fetch_assoc();
$assignmentCount = $res1['total'] ?? 0;
$stmt1->close();

// Fetch upcoming exam count
// ✅ Updated Exam Count Logic
$examCount = 0;
$stmt2 = $conn->prepare("SELECT COUNT(*) AS total FROM exams WHERE date >= CURDATE() AND dept_id = ?");
$stmt2->bind_param("i", $dept_id);
$stmt2->execute();
$res2 = $stmt2->get_result()->fetch_assoc();
$examCount = $res2['total'] ?? 0;
$stmt2->close();


// Fetch announcements count
$announcementCount = 0;
$res3 = $conn->query("SELECT COUNT(*) AS total FROM announcements");
if ($res3) {
    $announcementCount = $res3->fetch_assoc()['total'] ?? 0;
}

// Fetch notification count
$notificationCount = 0;
$res4 = $conn->query("SELECT COUNT(*) AS total FROM notifications");
if ($res4) {
    $notificationCount = $res4->fetch_assoc()['total'] ?? 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Student Dashboard</title>
  <link rel="stylesheet" href="css/dashboard.css" />
  
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background-color: #f4f6f9;
    }

    .main-content {
      margin-left: 250px;
      padding: 100px 40px 40px;
    }

    .dashboard-boxes {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 20px;
      margin-top: 30px;
    }

    .box {
      background-color: white;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
      text-align: center;
    }

    .box h3 {
      margin: 0;
      font-size: 18px;
      color: #0f3460;
    }

    .box p {
      margin: 10px 0 0;
      font-size: 14px;
      color: #666;
    }

     </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="header">
  <div><h1>Student Dashboard</h1></div>
  <div class="profile-menu" id="profileMenu">
    <img src="uploads/profile_pics/<?php echo htmlspecialchars($user_profile_pic); ?>" class="avatar" onclick="toggleProfileDropdown()" />
    <div id="profileDropdown" class="dropdown-content">
      <a href="user_logout.php" onclick="localStorage.clear()">Log Out</a>
    </div>
  </div>
</div>

<div class="main-content">
  <h2>Welcome, <?php echo htmlspecialchars($user_name); ?> 👋</h2>
  <p>Here’s what’s happening today in your student panel:</p>

  <div class="dashboard-boxes">
    <div class="box">
      <h3>📚 Assignments</h3>
      <p>You have <strong><?= $assignmentCount ?></strong> assignments</p>
    </div>
    <div class="box">
      <h3>📝 Exams</h3>
      <p><strong><?= $examCount ?></strong> upcoming exams</p>
    </div>
    <div class="box">
      <h3>🔔 Notifications</h3>
      <p><strong><?= $notificationCount ?></strong> recent updates</p>
    </div>
    <div class="box">
      <h3>📢 Announcements</h3>
      <p><strong><?= $announcementCount ?></strong> new announcements</p>
    </div>
  </div>
</div>



<div class="footer">
  © 2025 Student Dashboard System. All rights reserved.
</div>
<script src="js/user_dashboard.js"></script>
</body>
</html>
