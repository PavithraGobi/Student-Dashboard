<?php
session_start();
if (!isset($_SESSION['user_email'])) {
    header("Location: user_login.html");
    exit();
}

$conn = new mysqli("localhost", "root", "", "registration_db");
$conn->set_charset("utf8mb4");

$student_id = $_SESSION['user_id'];
$dept_id = $_SESSION['user_dept_id'];

// Fetch assignments with submission status
$stmt = $conn->prepare("
  SELECT 
    a.id AS assignment_id,
    a.assignment_name,
    a.description,
    a.due_date,
    c.course_name,
    sub.status
  FROM assignments a
  JOIN courses c ON a.course_id = c.id
  LEFT JOIN assignment_submissions sub 
    ON sub.assignment_id = a.id AND sub.student_id = ?
  WHERE c.dept_id = ?
  ORDER BY a.due_date ASC
");
$stmt->bind_param("ii", $student_id, $dept_id);
$stmt->execute();
$assignments = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>My Assignments</title>
  <link rel="stylesheet" href="css/dashboard.css" />
  <style>
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

    .submit-btn {
      background: #28a745;
      color: white;
      padding: 6px 12px;
      text-decoration: none;
      border-radius: 4px;
    }
    .submit-btn:hover {
      background: #218838;
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
    <h2>📝 My Assignments</h2>
    <table>
      <thead>
        <tr>
          <th>S.No</th>
          <th>Assignment</th>
          <th>Course</th>
          <th>Due Date</th>
          <th>Description</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php $i = 1; ?>
        <?php while ($row = $assignments->fetch_assoc()): ?>
          <tr>
            <td><?= $i++ ?></td>
            <td><?= htmlspecialchars($row['assignment_name']) ?></td>
            <td><?= htmlspecialchars($row['course_name']) ?></td>
            <td><?= date('d-m-Y', strtotime($row['due_date'])) ?></td>
            <td><?= htmlspecialchars($row['description']) ?></td>
            <td><?= ($row['status'] === 'completed') ? '✅ Completed' : '⏳ Pending' ?></td>
            <td>
              <?php if ($row['status'] !== 'completed'): ?>
                <a href="submit_assignment.php?id=<?= $row['assignment_id'] ?>" class="submit-btn">Submit</a>
              <?php else: ?>
                <span>Submitted</span>
              <?php endif; ?>
            </td>
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
