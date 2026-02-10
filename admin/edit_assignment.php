<?php
session_start();
if (!isset($_SESSION['admin_email'])) {
    header("Location: admin_login.html");
    exit();
}

$conn = new mysqli("localhost", "root", "", "registration_db");
$conn->set_charset("utf8mb4");

$assignment_id = $_GET['id'] ?? '';
$dept_id = $_GET['dept'] ?? '';
$assignment = null;
$courses = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $assignment_name = trim($_POST['assignment_name']);
    $course_id = intval($_POST['course_id']);
    $due_date = $_POST['due_date'];
    $description = trim($_POST['description']);
    $dept_id = $_POST['dept_id'];

    $stmt = $conn->prepare("UPDATE assignments SET assignment_name=?, course_id=?, due_date=?, description=? WHERE id=?");
    $stmt->bind_param("sissi", $assignment_name, $course_id, $due_date, $description, $id);
    if ($stmt->execute()) {
        header("Location: manage_assignments.php?dept=$dept_id");
        exit();
    } else {
        $error = "Update failed: " . $stmt->error;
    }
} else {
    if ($assignment_id) {
        $stmt = $conn->prepare("SELECT * FROM assignments WHERE id=?");
        $stmt->bind_param("i", $assignment_id);
        $stmt->execute();
        $assignment = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($assignment) {
            $course_id = $assignment['course_id'];
            $result = $conn->query("SELECT dept_id FROM courses WHERE id = $course_id");
            $row = $result->fetch_assoc();
            if ($row) $dept_id = $row['dept_id'];

            $stmt = $conn->prepare("SELECT id, course_name FROM courses WHERE dept_id=?");
            $stmt->bind_param("i", $dept_id);
            $stmt->execute();
            $courses = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Edit Assignment</title>
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
    select,
    textarea {
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

<?php include 'sidebar.php'; ?>

<div class="header">
  <div><h1>Admin Panel</h1></div>
  <div class="profile-menu" id="profileMenu">
    <img src="uploads/admin_profile_pics/<?php echo htmlspecialchars($_SESSION['admin_profile_pic'] ?? 'profile.png'); ?>" class="avatar" onclick="toggleProfileDropdown()" />
    <div id="profileDropdown" class="dropdown-content">
      <a href="admin_logout.php">Log Out</a>
    </div>
  </div>
</div>

<div class="content">
  <h2>✏️ Edit Assignment</h2>

  <?php if ($assignment): ?>
    <form method="POST" action="">
      <input type="hidden" name="id" value="<?= $assignment['id'] ?>">
      <input type="hidden" name="dept_id" value="<?= $dept_id ?>">

      <label>Assignment Name:</label>
      <input type="text" name="assignment_name" value="<?= htmlspecialchars($assignment['assignment_name']) ?>" required>

      <label>Subject:</label>
      <select name="course_id" required>
        <option value="">-- Select Course --</option>
        <?php foreach ($courses as $course): ?>
          <option value="<?= $course['id'] ?>" <?= $assignment['course_id'] == $course['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($course['course_name']) ?>
          </option>
        <?php endforeach; ?>
      </select>

      <label>Due Date:</label>
      <input type="date" name="due_date" value="<?= $assignment['due_date'] ?>" required>

      <label>Description:</label>
      <textarea name="description" rows="4"><?= htmlspecialchars($assignment['description']) ?></textarea>

      <button type="submit">Update Assignment</button>
    </form>
    <a href="manage_assignments.php?dept=<?= $dept_id ?>" class="back-link">← Back to Assignments</a>
  <?php else: ?>
    <p style="text-align:center; color:red;">Assignment not found.</p>
  <?php endif; ?>
</div>

<div class="footer">
  © 2025 Student Dashboard System. All rights reserved.
</div>
</body>
</html>
