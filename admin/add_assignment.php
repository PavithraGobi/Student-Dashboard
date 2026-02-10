<?php
session_start();
$admin_profile_pic = $_SESSION['admin_profile_pic'] ?? 'profile.png';
if (!isset($_SESSION['admin_email'])) {
    header("Location: admin_login.html");
    exit();
}

$conn = new mysqli("localhost", "root", "", "registration_db");
$conn->set_charset("utf8mb4");

$dept_id = $_GET['dept'] ?? '';
$courses = [];

// Fetch only department-specific courses
if ($dept_id !== '') {
    $stmt = $conn->prepare("SELECT id, course_name FROM courses WHERE dept_id = ?");
    $stmt->bind_param("i", $dept_id);
    $stmt->execute();
    $courses = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Add Assignment</title>
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
    input[type="date"],
    textarea,
    select {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 16px;
      margin-top: 5px;
      margin-bottom: 20px;
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
        <a href="admin_logout.php">Log Out</a>
      </div>
    </div>
  </div>

  <div class="content">
    <h2>➕ Add New Assignment</h2>

    <?php if ($dept_id && count($courses) > 0): ?>
    <form method="POST" action="insert_assignment.php">
      <input type="hidden" name="dept_id" value="<?= htmlspecialchars($dept_id) ?>" />

      <label>Assignment Name:</label>
      <input type="text" name="assignment_name" required>

      <label>Course:</label>
      <select name="course_id" required>
        <option value="">-- Select Course --</option>
        <?php foreach ($courses as $course): ?>
          <option value="<?= $course['id'] ?>"><?= htmlspecialchars($course['course_name']) ?></option>
        <?php endforeach; ?>
      </select>

      <label>Due Date:</label>
      <input type="date" name="due_date" required>

      <label>Description:</label>
      <textarea name="description" rows="4"></textarea>

      <button type="submit">Add Assignment</button>
    </form>
    <?php else: ?>
      <p style="color: red; text-align: center;">Invalid department or no courses available.</p>
    <?php endif; ?>

    <a href="manage_assignments.php?dept=<?= htmlspecialchars($dept_id) ?>" class="back-link">← Back</a>
  </div>
</div>

<div class="footer">
  © 2025 Student Dashboard System. All rights reserved.
</div>
</body>
</html>
