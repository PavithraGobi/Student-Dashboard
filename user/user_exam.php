<?php
session_start();
if (!isset($_SESSION['user_email'])) {
    header("Location: user_login.html");
    exit();
}

$conn = new mysqli("localhost", "root", "", "registration_db");
$conn->set_charset("utf8mb4");

$dept_id = $_SESSION['user_dept_id'];
$stmt = $conn->prepare("SELECT subject_code, course_name, date, day, session, session_time FROM exams WHERE dept_id = ? ORDER BY date ASC");
$stmt->bind_param("i", $dept_id);
$stmt->execute();
$exams = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
  <title>My Exams</title>
  <link rel="stylesheet" href="css/dashboard.css" />
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
<div class="container">
  <main id="content">
<h2> 📅My Exams</h2>
    <table>
      <thead>
        <tr><th>S.No</th><th>Subject Code</th><th>Course</th><th>Date</th><th>Day</th><th>Session</th><th>Time</th></tr>
      </thead>
      <tbody>
        <?php $i = 1; while ($exam = $exams->fetch_assoc()): ?>
          <tr>
            <td><?= $i++ ?></td>
            <td><?= htmlspecialchars($exam['subject_code']) ?></td>
            <td><?= htmlspecialchars($exam['course_name']) ?></td>
            <td><?= date('d-m-Y', strtotime($exam['date'])) ?></td>
            <td><?= htmlspecialchars($exam['day']) ?></td>
            <td><?= htmlspecialchars($exam['session']) ?></td>
            <td><?= htmlspecialchars($exam['session_time']) ?></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </main>
</div>
<script src="js/user_dashboard.js"></script>
<div class="footer">
  © 2025 Student Dashboard System. All rights reserved.
</div>
</body>
</html>
