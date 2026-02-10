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

// Get department list
$departments = [];
$dResult = $conn->query("SELECT id, dept_name FROM departments ORDER BY dept_name ASC");
while ($row = $dResult->fetch_assoc()) {
    $departments[] = $row;
}

$selectedDeptId = $_GET['dept'] ?? '';
$assignments = [];

if ($selectedDeptId !== '') {
    $stmt = $conn->prepare("
        SELECT a.id, a.assignment_name, a.due_date, a.description, c.course_name
        FROM assignments a
        JOIN courses c ON a.course_id = c.id
        WHERE c.dept_id = ?
        ORDER BY a.due_date ASC
    ");
    $stmt->bind_param("i", $selectedDeptId);
    $stmt->execute();
    $assignments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Manage Assignments by Department</title>
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
    <h2 style="text-align:center;">📘 Manage Assignments by Department</h2>

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
        <a href="add_assignment.php?dept=<?= $selectedDeptId ?>" class="add-btn">➕ Add Assignment</a>
      </div>

      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>S.No</th>
              <th>Assignment Name</th>
              <th>Subject</th>
              <th>Due Date</th>
              <th>Description</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($assignments)): $sno = 1; ?>
              <?php foreach ($assignments as $assignment): ?>
                <tr>
                  <td><?= $sno++ ?></td>
                  <td><?= htmlspecialchars($assignment['assignment_name']) ?></td>
                  <td><?= htmlspecialchars($assignment['course_name']) ?></td>
                  <td><?= date('d-m-Y', strtotime($assignment['due_date'])) ?></td>
                  <td><?= htmlspecialchars($assignment['description']) ?></td>
                  <td>
                  
                    <a href="edit_assignment.php?id=<?= $assignment['id'] ?>&dept=<?= $selectedDeptId ?>" class="action-btn">✏️ Edit</a>
                    <a href="delete_assignment.php?id=<?= $assignment['id'] ?>&dept=<?= $selectedDeptId ?>" class="action-btn" onclick="return confirm('Are you sure?')">🗑️ Delete</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="6" style="text-align:center;">No assignments found for this department.</td></tr>
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
