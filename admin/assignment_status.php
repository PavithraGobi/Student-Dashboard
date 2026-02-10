<?php
session_start();
if (!isset($_SESSION['admin_email'])) {
    header("Location: admin_login.html");
    exit();
}

$admin_profile_pic = $_SESSION['admin_profile_pic'] ?? 'profile.png';

$conn = new mysqli("localhost", "root", "", "registration_db");
$conn->set_charset("utf8mb4");

// Fetch all departments for dropdown
$departments = $conn->query("SELECT id, dept_name FROM departments ORDER BY dept_name ASC");

// Get selected department and assignment from GET params
$selectedDept = $_GET['dept'] ?? '';
$selectedAssignment = $_GET['assignment'] ?? '';

// Get assignments for selected department
$assignments = [];
if ($selectedDept) {
    $stmtAssign = $conn->prepare("
        SELECT a.id, a.assignment_name 
        FROM assignments a 
        JOIN courses c ON a.course_id = c.id 
        WHERE c.dept_id = ?
        ORDER BY a.assignment_name ASC
    ");
    $stmtAssign->bind_param("i", $selectedDept);
    $stmtAssign->execute();
    $resultAssign = $stmtAssign->get_result();
    $assignments = $resultAssign->fetch_all(MYSQLI_ASSOC);
    $stmtAssign->close();
}

// Fetch students and their status for the selected assignment
$studentStatus = null;
if ($selectedDept && $selectedAssignment) {
    $stmt = $conn->prepare("
        SELECT s.id AS student_id, s.username, s.email,
               sub.status, sub.submitted_at
        FROM regform s
        LEFT JOIN assignment_submissions sub 
            ON sub.student_id = s.id AND sub.assignment_id = ?
        WHERE s.dept_id = ?
        ORDER BY sub.status = 'Completed' DESC, sub.submitted_at DESC, s.username ASC
    ");
    $stmt->bind_param("ii", $selectedAssignment, $selectedDept);
    $stmt->execute();
    $studentStatus = $stmt->get_result();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>View Assignment Submission Status</title>
  <link rel="stylesheet" href="css/admin_dashboards.css" />
  <script src="js/admin_dashboard.js"></script>
  <style>
    .content-wrapper {
      margin-left: 250px;
      padding: 20px;
    }
    label {
      font-weight: bold;
      margin-right: 10px;
    }
    select {
      padding: 5px;
      margin-right: 20px;
      min-width: 200px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }
    th, td {
      border: 1px solid #ddd;
      padding: 8px;
      text-align: left;
    }
    th {
      background-color: #0f3460;
      color: white;
    }
    .badge-completed {
      color: #155724;
      background-color: #d4edda;
      padding: 3px 8px;
      border-radius: 4px;
      font-weight: bold;
    }
    .badge-pending {
      color: #856404;
      background-color: #fff3cd;
      padding: 3px 8px;
      border-radius: 4px;
      font-weight: bold;
    }
    .stats {
      margin-top: 20px;
      font-weight: bold;
    }
    .header {
      position: fixed;
      top: 0;
      left: 250px;
      right: 0;
      height: 60px;
      background-color: #0f3460;
      color: white;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0 20px;
      z-index: 100;
    }
    .profile-menu {
      position: relative;
    }
    .avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      cursor: pointer;
    }
    .dropdown-content {
      display: none;
      position: absolute;
      right: 0;
      background-color: white;
      min-width: 120px;
      box-shadow: 0 8px 16px rgba(0,0,0,0.2);
      border-radius: 4px;
      z-index: 101;
    }
    .dropdown-content a {
      color: #0f3460;
      padding: 10px 16px;
      text-decoration: none;
      display: block;
    }
    .dropdown-content a:hover {
      background-color: #f1f1f1;
    }
  </style>
  <script>
    function toggleProfileDropdown() {
      const dropdown = document.getElementById('profileDropdown');
      dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
    }
    // Close dropdown if clicked outside
    window.onclick = function(event) {
      if (!event.target.matches('.avatar')) {
        const dropdown = document.getElementById('profileDropdown');
        if (dropdown.style.display === 'block') {
          dropdown.style.display = 'none';
        }
      }
    }
  </script>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="header">
  <div><h1>Admin Dashboard</h1></div>
  <div class="profile-menu" id="profileMenu">
    <img src="uploads/admin_profile_pics/<?php echo htmlspecialchars($admin_profile_pic); ?>" class="avatar" onclick="toggleProfileDropdown()" />
    <div id="profileDropdown" class="dropdown-content">
      <a href="admin_logout.php">Log Out</a>
    </div>
  </div>
</div>

<div class="container" style="margin-top: 70px;">
 <div class="content-wrapper">
  <h2 style="color:#0f3460; margin-bottom: 20px;">📋 Assignment Submission Status</h2>
    <form method="GET" action="">
      <label for="dept">Select Department:</label>
      <select name="dept" id="dept" onchange="this.form.submit()">
        <option value="">-- Select Department --</option>
        <?php while ($d = $departments->fetch_assoc()): ?>
          <option value="<?= htmlspecialchars($d['id']) ?>" <?= $selectedDept == $d['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($d['dept_name']) ?>
          </option>
        <?php endwhile; ?>
      </select>

      <?php if ($selectedDept): ?>
        <label for="assignment">Select Assignment:</label>
        <select name="assignment" id="assignment" onchange="this.form.submit()">
          <option value="">-- Select Assignment --</option>
          <?php foreach ($assignments as $a): ?>
            <option value="<?= htmlspecialchars($a['id']) ?>" <?= $selectedAssignment == $a['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($a['assignment_name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      <?php endif; ?>
    </form>

    <?php if ($selectedAssignment && $studentStatus && $studentStatus->num_rows > 0): ?>
      <table>
        <thead>
          <tr>
            <th>S.No</th>
            <th>Student Name</th>
            <th>Email</th>
            <th>Status</th>
            <th>Submitted On</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          $i = 1;
          $total = 0;
          $completed = 0;
          $studentStatus->data_seek(0); // reset pointer
          while ($row = $studentStatus->fetch_assoc()): 
            $total++;
            if (strtolower($row['status']) === 'completed') $completed++;
          ?>
            <tr>
              <td><?= $i++ ?></td>
              <td><?= htmlspecialchars($row['username']) ?></td>
              <td><?= htmlspecialchars($row['email']) ?></td>
              <td>
                <?php if (strtolower($row['status']) === 'completed'): ?>
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

      <div class="stats">
        <p>Total Students: <?= $total ?></p>
        <p>Completed: <?= $completed ?></p>
        <p>Pending: <?= $total - $completed ?></p>
      </div>
    <?php elseif ($selectedAssignment): ?>
      <p>No students found for this department or assignment.</p>
    <?php endif; ?>
  </div>
</div>

</body>
</html>
