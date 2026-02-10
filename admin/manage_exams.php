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

// Fetch departments
$departments = [];
$dResult = $conn->query("SELECT id, dept_name FROM departments ORDER BY dept_name ASC");
while ($row = $dResult->fetch_assoc()) {
    $departments[] = $row;
}

$selectedDeptId = $_GET['dept'] ?? '';
$exams = [];

if ($selectedDeptId !== '') {
    $stmt = $conn->prepare("
        SELECT e.id, e.subject_code, e.course_name, e.date, e.day, d.dept_name, e.session, e.session_time
        FROM exams e
        JOIN departments d ON e.dept_id = d.id
        WHERE e.dept_id = ?
        ORDER BY e.date ASC
    ");
    $stmt->bind_param("i", $selectedDeptId);
    $stmt->execute();
    $exams = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Manage Exams by Department</title>
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
      margin-bottom: 20px;
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
  </style>
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
    <h2 style="text-align:center;">📝 Manage Exams by Department</h2>

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

    <?php if ($selectedDeptId): ?>
      <div class="button-container">
        <a href="add_exam.php?dept=<?= $selectedDeptId ?>" class="add-btn">➕ Add Exam</a>
      </div>

      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>S.No</th>
              <th>Subject Code</th>
              <th>Subject</th>
              <th>Date</th>
              <th>Day</th>
              <th>Session</th>
              <th>Time</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($exams)): $sno = 1; ?>
              <?php foreach ($exams as $exam): ?>
                <tr>
                  <td><?= $sno++ ?></td>
                  <td><?= htmlspecialchars($exam['subject_code']) ?></td>
                  <td><?= htmlspecialchars($exam['course_name']) ?></td>
                  <td><?= date('d-m-Y', strtotime($exam['date'])) ?></td>
                  <td><?= htmlspecialchars($exam['day']) ?></td>
                  <td><?= htmlspecialchars($exam['session']) ?></td>
                  <td><?= htmlspecialchars($exam['session_time']) ?></td>
                  <td>
                    <a href="edit_exam.php?id=<?= $exam['id'] ?>&dept=<?= $selectedDeptId ?>" class="action-btn">✏️ Edit</a>
                    <a href="delete_exam.php?id=<?= $exam['id'] ?>&dept=<?= $selectedDeptId ?>" class="action-btn" onclick="return confirm('Are you sure you want to delete this exam?')">🗑️ Delete</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="8" style="text-align:center;">No exams found for this department.</td></tr>
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
