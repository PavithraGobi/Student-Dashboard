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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject_code = $_POST['subject_code'] ?? '';
    $course_name = $_POST['course_name'] ?? '';
    $date = $_POST['date'] ?? '';
    $day = $_POST['day'] ?? '';
    $dept = $_POST['dept'] ?? '';
    $session = $_POST['session'] ?? '';
    $session_time = $_POST['session_time'] ?? '';

   $stmt = $conn->prepare("UPDATE exams SET subject_code=?, course_name=?, date=?, day=?, dept_id=?, session=?, session_time=? WHERE id=?");
$stmt->bind_param("sssssssi", $subject_code, $course_name, $date, $day, $dept, $session, $session_time, $id);
   
    if ($stmt->execute()) {
       header("Location: manage_exams.php?dept=" . urlencode($dept));
exit();

    } else {
        echo "Update failed: " . $conn->error;
    }
}

// Fetch current exam values
$stmt = $conn->prepare("SELECT subject_code, course_name, date, day, dept_id, session, session_time FROM exams WHERE id=?");

$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($subject_code, $course_name, $date, $day, $dept, $session, $session_time);
$stmt->fetch();
$stmt->close();

// Fetch departments for dropdown
$departments = $conn->query("SELECT id, dept_name FROM departments ORDER BY dept_name ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Edit Exam</title>
  <link rel="stylesheet" href="css/admin_dashboards.css" />
  <script src="js/admin_dashboard.js"></script>
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
    input, select {
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
    .back-link {
      display: inline-block;
      margin-top: 15px;
      text-decoration: none;
      color: #0f3460;
      font-weight: bold;
    }
    input[readonly] {
      background-color: #f0f0f0;
      cursor: not-allowed;
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

<div class="content">
  <h1>✏️ Edit Exam</h1>

  <form method="POST" oninput="updateDay()">
    <label>Subject Code:</label>
    <input type="text" name="subject_code" value="<?= htmlspecialchars($subject_code) ?>" required>

    <label>Subject Name:</label>
    <input type="text" name="course_name" value="<?= htmlspecialchars($course_name) ?>" required>

    <label>Date:</label>
    <input type="date" name="date" id="date" value="<?= htmlspecialchars($date) ?>" required>

    <label>Day:</label>
    <input type="text" name="day" id="day" value="<?= htmlspecialchars($day) ?>" readonly required>

    <label>Department:</label>
    <select name="dept" required>
      <option value="">-- Select Department --</option>
      <?php while ($d = $departments->fetch_assoc()): ?>
        <option value="<?= $d['id'] ?>" <?= $d['id'] == $dept ? 'selected' : '' ?>>
          <?= htmlspecialchars($d['dept_name']) ?>
        </option>
      <?php endwhile; ?>
    </select>

    <label>Session:</label>
    <select name="session" required>
      <option value="">-- Select Session --</option>
      <option value="FN" <?= $session == 'FN' ? 'selected' : '' ?>>FN (Forenoon)</option>
      <option value="AN" <?= $session == 'AN' ? 'selected' : '' ?>>AN (Afternoon)</option>
    </select>

    <label>Session Time:</label>
    <input type="text" name="session_time" value="<?= htmlspecialchars($session_time) ?>" required>

    <button type="submit">Update Exam</button>
  </form>

  <a href="manage_exams.php" class="back-link">← Cancel</a>
</div>

<!-- Footer -->
<div class="footer">
  © 2025 Student Dashboard System. All rights reserved.
</div>

<script>
  function updateDay() {
    const dateInput = document.getElementById("date").value;
    const dayInput = document.getElementById("day");

    if (dateInput) {
      const date = new Date(dateInput);
      const days = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
      dayInput.value = days[date.getDay()];
    } else {
      dayInput.value = "";
    }
  }

  // Initialize day on page load
  updateDay();
</script>
</body>
</html>

<?php
$conn->close();
?>
