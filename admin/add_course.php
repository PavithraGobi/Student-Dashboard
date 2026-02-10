<?php
session_start();
if (!isset($_SESSION['admin_email'])) {
    header("Location: admin_login.html");
    exit();
}

$dept = $_GET['dept'] ?? '';

$admin_profile_pic = $_SESSION['admin_profile_pic'] ?? 'profile.png';
$conn = new mysqli("localhost", "root", "", "registration_db");
$conn->set_charset("utf8mb4");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_name = trim($_POST['course_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $dept_id = $_POST['dept_id'] ?? '';

    if ($course_name && $dept_id) {
        $stmt = $conn->prepare("INSERT INTO courses (course_name, description, dept_id) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $course_name, $description, $dept_id);
        if ($stmt->execute()) {
            header("Location: manage_courses.php?dept=" . urlencode($dept_id) . "&msg=Course+added+successfully");
            exit();
        } else {
            $error = "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error = "Course Name and Department are required.";
    }
}

// Fetch department name for display (optional)
$dept_name = '';
if ($dept) {
    $stmt = $conn->prepare("SELECT dept_name FROM departments WHERE id = ?");
    $stmt->bind_param("i", $dept);
    $stmt->execute();
    $stmt->bind_result($dept_name);
    $stmt->fetch();
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Add Course</title>
<link rel="stylesheet" href="css/admin_dashboards.css" />
<style>
  .content {
    margin-left: 240px;
    padding: 100px 40px 40px;
  }
  form {
    max-width: 600px;
    background: #fff;
    padding: 25px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
  }
  label {
    display: block;
    margin-bottom: 6px;
    font-weight: 500;
  }
  input, textarea, select {
    width: 100%;
    padding: 10px;
    margin-bottom: 16px;
    font-size: 15px;
    border: 1px solid #ccc;
    border-radius: 6px;
  }
  button {
    background-color: #0f3460;
    color: white;
    font-weight: bold;
    padding: 10px 18px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 16px;
  }
  button:hover {
    background-color: #1f4c87;
  }
  .error-msg {
    color: red;
    margin-bottom: 15px;
    font-weight: bold;
  }
  .back-link {
    display: inline-block;
    margin-top: 15px;
    text-decoration: none;
    color: #0f3460;
    font-weight: bold;
  }
</style>
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="header">
  <div><h1>Admin Panel</h1></div>
  <div class="profile-menu">
    <img src="uploads/admin_profile_pics/<?php echo htmlspecialchars($admin_profile_pic); ?>" class="avatar" />
  </div>
</div>

<div class="content">
  <h2>➕ Add New Course <?php if($dept_name) echo "for " . htmlspecialchars($dept_name); ?></h2>

  <?php if (!empty($error)): ?>
    <div class="error-msg"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST" action="add_course.php?dept=<?= urlencode($dept) ?>">
    <label>Course Name:</label>
    <input type="text" name="course_name" required />

    <label>Description:</label>
    <textarea name="description" rows="4"></textarea>

    <input type="hidden" name="dept_id" value="<?= htmlspecialchars($dept) ?>" />

    <button type="submit">Add Course</button>
  </form>

  <a href="manage_courses.php?dept=<?= urlencode($dept) ?>" class="back-link">← Back to Courses</a>
</div>
<div class="footer">© 2025 Student Dashboard System. All rights reserved.</div>
</body>
</html>
