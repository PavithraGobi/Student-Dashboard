<?php
session_start();
if (!isset($_SESSION['admin_email'])) {
    header("Location: admin_login.html");
    exit();
}

$conn = new mysqli("localhost", "root", "", "registration_db");
$conn->set_charset("utf8mb4");

// Fetch all departments
$departments = $conn->query("SELECT id, dept_name FROM departments");

// Selected department and assignment
$selectedDept = $_GET['dept'] ?? '';
$selectedAssignment = $_GET['assignment'] ?? '';

// Get assignments for selected department
$assignments = [];
if ($selectedDept) {
    $assignments = $conn->query("
        SELECT a.id, a.assignment_name 
        FROM assignments a 
        JOIN courses c ON a.course_id = c.id 
        WHERE c.dept_id = $selectedDept
    ");
}

// Fetch students and their status
$studentStatus = [];
if ($selectedDept && $selectedAssignment) {
    $stmt = $conn->prepare("
        SELECT s.id AS student_id, s.username, s.email,
               sub.status, sub.submitted_at
        FROM regform s
        LEFT JOIN assignment_submissions sub 
            ON sub.student_id = s.id AND sub.assignment_id = ?
        WHERE s.dept_id = ?
    ");
    $stmt->bind_param("ii", $selectedAssignment, $selectedDept);
    $stmt->execute();
    $studentStatus = $stmt->get_result();
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Assignment Submission Status</title>
  <link rel="stylesheet" href="css/admin_dashboards.css">
  <style>
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { padding: 10px; border: 1px solid #ccc; text-align: center; }
    .badge-completed { background: green; color: white; padding: 5px 10px; border-radius: 5px; }
    .badge-pending { background: red; color: white; padding: 5px 10px; border-radius: 5px; }
    select { padding: 5px 10px; }
  </style>
</head>
<body>
<?php include 'sidebar.php'; ?>

<div class="header">
  <h1>📝 Assignment Submission Status</h1>
</div>

<div class="container">
  <form method="GET" style="margin: 20px 0;">
    <label>Select Department:</label>
    <select name="dept" onchange="this.form.submit()">
      <option value="">-- Select Department --</option>
      <?php while ($d = $departments->fetch_assoc()): ?>
        <option value="<?= $d['id'] ?>" <?= $selectedDept == $d['id'] ? 'selected' : '' ?>>
          <?= $d['dept_name'] ?>
        </option>
      <?php endwhile; ?>
    </select>

    <?php if ($selectedDept): ?>
      <label style="margin-left: 20px;">Select Assignment:</label>
      <select name="assignment" onchange="this.form.submit()">
        <option value="">-- Select Assignment --</option>
        <?php foreach ($assignments as $a): ?>
          <option value="<?= $a['id'] ?>" <?= $selectedAssignment == $a['id'] ? 'selected' : '' ?>>
            <?= $a['assignment_name'] ?>
          </option>
        <?php endforeach; ?>
      </select>
    <?php endif; ?>
  </form>

  <?php if ($selectedAssignment && $studentStatus): ?>
    <table>
      <thead>
        <tr>
          <th>S.No</th>
          <th>Student Name</th>
          <th>Email</th>
          <th>Status</th>
          <th>Submitted At</th>
        </tr>
      </thead>
      <tbody>
        <?php $i = 1; while ($row = $studentStatus->fetch_assoc()): ?>
          <tr>
            <td><?= $i++ ?></td>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td>
              <?php if ($row['status'] === 'Completed'): ?>
                <span class="badge-completed">Completed</span>
              <?php else: ?>
                <span class="badge-pending">Pending</span>
              <?php endif; ?>
            </td>
            <td><?= $row['submitted_at'] ? date('d-m-Y H:i', strtotime($row['submitted_at'])) : '-' ?></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  <?php elseif ($selectedAssignment): ?>
    <p>No students found for this department or assignment.</p>
  <?php endif; ?>
</div>

</body>
</html>
