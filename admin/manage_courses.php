<?php
session_start();
if (!isset($_SESSION['admin_email'])) {
    header("Location: admin_login.html");
    exit();
}

$admin_username = $_SESSION['admin_username'] ?? 'Admin';
$admin_profile_pic = $_SESSION['admin_profile_pic'] ?? 'profile.png';

$conn = new mysqli("localhost", "root", "", "registration_db");
$conn->set_charset("utf8mb4");

// Fetch departments for dropdown
$departments = [];
$dResult = $conn->query("SELECT id, dept_name FROM departments ORDER BY dept_name ASC");
while ($row = $dResult->fetch_assoc()) {
    $departments[] = $row;
}

$selectedDeptId = $_GET['dept'] ?? '';
$subjects = [];

if ($selectedDeptId !== '') {
    $stmt = $conn->prepare("
        SELECT id, course_name, description
        FROM courses
        WHERE dept_id = ?
        ORDER BY id DESC
    ");
    $stmt->bind_param("i", $selectedDeptId);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($subject = $result->fetch_assoc()) {
        $subjects[] = $subject;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Manage Subjects by Department</title>
<link rel="stylesheet" href="css/admin_dashboards.css" />
<script src="js/admin_dashboard.js"></script>
<style>
  .filter-container {
    margin: 20px 0;
    text-align: center;
  }
  select {
    padding: 8px;
    font-size: 16px;
  }
  .add-btn {
    display: inline-block;
    margin-bottom: 15px;
    padding: 8px 16px;
    background-color: #0f3460;
    color: white;
    font-weight: bold;
    text-decoration: none;
    border-radius: 6px;
  }
  .add-btn:hover {
    background-color: #1f4c87;
  }
  .action-btn {
    margin-right: 8px;
    text-decoration: none;
    font-size: 16px;
  }
  .file-section {
    background: #f4f6f8;
    padding: 12px;
    margin-top: 5px;
    border-radius: 6px;
  }
  .file-section form {
    margin-bottom: 12px;
  }
  .file-list ul {
    list-style: none;
    padding-left: 0;
  }
  .file-list li {
    margin-bottom: 6px;
  }
  .toggle-btn {
    cursor: pointer;
    background-color: #0f3460;
    color: white;
    border: none;
    padding: 6px 12px;
    border-radius: 5px;
    font-size: 14px;
  }
</style>
<script>
  function toggleFileSection(id) {
    const el = document.getElementById('files-' + id);
    if (el.style.display === 'none' || el.style.display === '') {
      el.style.display = 'block';
    } else {
      el.style.display = 'none';
    }
  }
</script>
</head>
<body>

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

<div class="container">
  <main id="content">
    <h2 style="text-align:center;">📚 Manage Subjects by Department</h2>

    <div class="filter-container">
      <form method="GET" action="">
        <label for="dept">Choose Department:</label>
        <select name="dept" id="dept" onchange="this.form.submit()">
          <option value="">-- Select Department --</option>
          <?php foreach ($departments as $dept): ?>
            <option value="<?= $dept['id'] ?>" <?= $selectedDeptId == $dept['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($dept['dept_name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </form>
    </div>

    <?php if ($selectedDeptId !== ''): ?>
      <div>
        <a href="add_course.php?dept=<?= urlencode($selectedDeptId) ?>" class="add-btn">➕ Add New Subject</a>
      </div>

      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>S.No</th>
              <th>Subject Name</th>
              <th>Description</th>
              <th>Actions</th>
              <th>Files</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($subjects)): ?>
              <?php $serial = 1; ?>
              <?php foreach ($subjects as $subject): ?>
                <tr>
                  <td><?= $serial++ ?></td>
                  <td><?= htmlspecialchars($subject['course_name']) ?></td>
                  <td><?= htmlspecialchars($subject['description']) ?></td>
                  <td>
                    <a href="edit_course.php?id=<?= $subject['id'] ?>&dept=<?= urlencode($selectedDeptId) ?>" class="action-btn">✏️ Edit</a>
                    <a href="delete_course.php?id=<?= $subject['id'] ?>&dept=<?= urlencode($selectedDeptId) ?>" class="action-btn" onclick="return confirm('Are you sure you want to delete this subject?')">🗑️ Delete</a>
                  </td>
                  <td>
                    <button class="toggle-btn" onclick="toggleFileSection(<?= $subject['id'] ?>)">Manage Files</button>
                  </td>
                </tr>
                <tr>
                  <td colspan="5" style="padding: 0;">
                    <div id="files-<?= $subject['id'] ?>" class="file-section" style="display:none;">
                      <!-- Upload Form -->
                      <form method="POST" action="upload_subject_file.php" enctype="multipart/form-data">

                        <input type="hidden" name="course_id" value="<?= $subject['id'] ?>">
                        <input type="hidden" name="dept" value="<?= urlencode($selectedDeptId) ?>">
                        <input type="text" name="file_title" placeholder="File title" required style="width:180px;" />
                        <input type="file" name="file" required />

                        <button type="submit" style="background:#0f3460;color:white;border:none;padding:6px 10px;">Upload</button>

                      </form>
<!-- Files List -->
<div class="file-list">
  <ul>
  <?php
    $stmtFiles = $conn->prepare("SELECT * FROM subject_files WHERE course_id = ? ORDER BY uploaded_at DESC");
    $stmtFiles->bind_param("i", $subject['id']);
    $stmtFiles->execute();
    $resFiles = $stmtFiles->get_result();
    if ($resFiles->num_rows > 0):
      while ($f = $resFiles->fetch_assoc()):
  ?>
    <li>
      📄 <strong><?= htmlspecialchars($f['file_title']) ?></strong> –
      <a href="<?= htmlspecialchars($f['file_path']) ?>" target="_blank">View</a>
      <small>(<?= date("d M Y", strtotime($f['uploaded_at'])) ?>)</small>
      <a href="delete_subject_file.php?id=<?= $f['id'] ?>&dept=<?= urlencode($selectedDeptId) ?>"
         onclick="return confirm('Are you sure you want to delete this file?')"
         style="color: red; font-weight: bold; margin-left: 10px;"
         title="Delete File">❌</a>
    </li>
  <?php
      endwhile;
    else:
      echo '<li>No files uploaded yet.</li>';
    endif;
    $stmtFiles->close();
  ?>
  </ul>
</div>

                  
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="5" style="text-align:center;">No subjects found for this department.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </main>
</div>

<div class="footer">
  © 2025 Student Dashboard System. All rights reserved.
</div>
</body>
</html>
