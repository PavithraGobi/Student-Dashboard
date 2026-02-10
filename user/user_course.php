<?php
session_start();
if (!isset($_SESSION['user_email'])) {
    header("Location: user_login.html");
    exit();
}

$conn = new mysqli("localhost", "root", "", "registration_db");
$conn->set_charset("utf8mb4");

$dept_id = $_SESSION['user_dept_id'] ?? 0;
if (!$dept_id) {
    die("Department not set for user.");
}

// Get subjects
$stmt = $conn->prepare("SELECT id, course_name, description FROM courses WHERE dept_id = ?");
$stmt->bind_param("i", $dept_id);
$stmt->execute();
$subjects = $stmt->get_result();

$base_url = "http://localhost/task/"; // Base URL without admin/
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>My Courses</title>
<link rel="stylesheet" href="css/dashboard.css" />
<script src="js/user_dashboard.js"></script>
<style>
  .subject-section {
    border: 1px solid #ddd;
    margin: 15px 0;
    padding: 15px;
    border-radius: 8px;
    background-color: #fafafa;
  }
  .subject-title {
    font-weight: bold;
    font-size: 20px;
    margin-bottom: 5px;
  }
  .subject-description {
    margin-bottom: 10px;
  }
  .file-list ul {
    list-style: none;
    padding-left: 0;
  }
  .file-list li {
    margin-bottom: 5px;
  }
main#content {
    position: absolute;
    top: 60px;
    left: 250px;
    right: 0;
    bottom: 40px;
    padding: 30px;
    overflow-y: auto;
    background-color: #f4f6f9;
    box-sizing: border-box;
  }

</style>
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
    <h2>📚 My Courses</h2>
    <?php while ($subject = $subjects->fetch_assoc()): ?>
      <div class="subject-section">
        <div class="subject-title"><?= htmlspecialchars($subject['course_name']) ?></div>
        <div class="subject-description"><?= nl2br(htmlspecialchars($subject['description'])) ?></div>

        <div class="file-list">
          <strong>Materials:</strong>
          <ul>
           <?php
  $stmtFiles = $conn->prepare("SELECT file_title, file_path, uploaded_at FROM subject_files WHERE course_id = ? ORDER BY uploaded_at DESC");
  $stmtFiles->bind_param("i", $subject['id']);
  $stmtFiles->execute();
  $files = $stmtFiles->get_result();

  if ($files->num_rows > 0):
    while ($file = $files->fetch_assoc()):
      // Strip only the beginning 'admin/' for public access
      $file_url = $base_url . preg_replace('/^admin\//', '', $file['file_path']);
?>
  <li>
    📄 <strong><?= htmlspecialchars($file['file_title']) ?></strong> – 
    <a href="<?= htmlspecialchars($file_url) ?>" target="_blank" rel="noopener noreferrer">View</a>
    <small>(<?= date("d M Y", strtotime($file['uploaded_at'])) ?>)</small>
  </li>
<?php
    endwhile;
  else:
    echo "<li>No materials uploaded yet.</li>";
  endif;
  $stmtFiles->close();
?>

          </ul>
        </div>
      </div>
    <?php endwhile; ?>
  </main>
</div>

<div class="footer">
  © 2025 Student Dashboard System. All rights reserved.
</div>
</body>
</html>
