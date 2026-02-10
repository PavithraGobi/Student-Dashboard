<?php
session_start();
if (!isset($_SESSION['admin_email'])) {
    header("Location: admin_login.html");
    exit();
}

$admin_profile_pic = $_SESSION['admin_profile_pic'] ?? 'profile.png';

$conn = new mysqli("localhost", "root", "", "registration_db");
$conn->set_charset("utf8mb4");

$id = $_GET['id'] ?? '';
$dept = $_GET['dept'] ?? '';

if (!$id || !is_numeric($id)) {
    header("Location: manage_courses.php" . ($dept ? "?dept=" . urlencode($dept) : ''));
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject_name = trim($_POST['subject_name'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($subject_name) {
        $stmt = $conn->prepare("UPDATE courses SET course_name=?, description=? WHERE id=?");
        $stmt->bind_param("ssi", $subject_name, $description, $id);
        if ($stmt->execute()) {
            header("Location: manage_courses.php?dept=" . urlencode($dept) . "&msg=Subject+updated+successfully");
            exit();
        } else {
            $error = "Error updating subject: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error = "Subject Name is required.";
    }
}

// Fetch current subject details
$stmt = $conn->prepare("SELECT course_name, description FROM courses WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($subject_name, $description);
$stmt->fetch();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Edit Subject</title>
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
  input, textarea {
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
  <h2>✏️ Edit Subject</h2>

  <?php if ($error): ?>
    <div class="error-msg"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST" action="edit_course.php?id=<?= urlencode($id) ?>&dept=<?= urlencode($dept) ?>">
    <label>Subject Name:</label>
    <input type="text" name="subject_name" value="<?= htmlspecialchars($subject_name) ?>" required />

    <label>Description:</label>
    <textarea name="description" rows="4"><?= htmlspecialchars($description) ?></textarea>

    <button type="submit">Update Subject</button>
  </form>

  <a href="manage_courses.php?dept=<?= urlencode($dept) ?>" class="back-link">← Back to Subjects</a>
</div>
<div class="footer">© 2025 Student Dashboard System. All rights reserved.</div>
</body>
</html>
