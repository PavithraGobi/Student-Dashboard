<?php
session_start();
$admin_profile_pic = $_SESSION['admin_profile_pic'] ?? 'profile.png';
$conn = new mysqli("localhost", "root", "", "registration_db");
$conn->set_charset("utf8mb4");

// Get dept id from GET parameter (must be set)
$selectedDeptId = isset($_GET['dept']) ? (int)$_GET['dept'] : 0;
if (!$selectedDeptId) {
    header("Location: manage_exams.php?msg=Please+select+a+department+first");
    exit();
}

// Fetch department name
$stmt = $conn->prepare("SELECT dept_name FROM departments WHERE id = ?");
$stmt->bind_param("i", $selectedDeptId);
$stmt->execute();
$stmt->bind_result($selectedDeptName);
if (!$stmt->fetch()) {
    $stmt->close();
    header("Location: manage_exams.php?msg=Invalid+department+selected");
    exit();
}
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $subject_code = $_POST["subject_code"] ?? '';
    $course_name  = $_POST["course_name"] ?? '';
    $exam_date    = $_POST["exam_date"] ?? '';
    $day          = $_POST["day"] ?? '';
    $dept         = (int)($_POST["dept"] ?? 0);
    $session      = $_POST["session"] ?? '';
    $session_time = $_POST["session_time"] ?? '';

    if ($subject_code && $course_name && $exam_date && $day && $dept && $session && $session_time) {
        $stmt = $conn->prepare("INSERT INTO exams (subject_code, course_name, date, day, dept_id, session, session_time) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssiss", $subject_code, $course_name, $exam_date, $day, $dept, $session, $session_time);

        if ($stmt->execute()) {
            // ✅ Insert notification after successful exam creation
            $title = "New Exam Scheduled";
            $message = "Exam '{$course_name}' on {$exam_date} ({$session} session)";

            $notif_stmt = $conn->prepare("INSERT INTO notifications (title, message) VALUES (?, ?)");
            $notif_stmt->bind_param("ss", $title, $message);
            $notif_stmt->execute();
            $notif_stmt->close();

            header("Location: manage_exams.php?dept=" . urlencode($dept));
            exit();
        } else {
            echo "❌ Error: " . $stmt->error;
        }
    } else {
        echo "⚠️ Please fill all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Add Exam</title>
  <link rel="stylesheet" href="css/admin_dashboards.css" />
  <script src="js/admin_dashboard.js"></script>
  <style>
    .content { margin-left: 240px; padding: 100px 40px 40px; }
    form {
      max-width: 500px;
      margin: 0 auto;
      padding: 25px;
      border-radius: 8px;
      background-color: white;
      box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    }
    h2 { color: #0f3460; text-align: center; }
    label { display: block; font-weight: 500; margin-top: 16px; }
    input, select {
      width: 100%; padding: 10px; border: 1px solid #ccc;
      border-radius: 6px; margin-top: 6px;
    }
    button {
      margin-top: 20px; padding: 10px; width: 100%;
      background: #0f3460; color: white; border: none; border-radius: 6px; font-weight: bold;
    }
    .back-link {
      display: inline-block;
      margin-top: 20px;
      color: #0f3460;
      text-decoration: none;
      font-weight: bold;
    }
    .back-link:hover {
      text-decoration: underline;
    }
    input[readonly] {
      background-color: #f0f0f0;
      cursor: not-allowed;
    }
  </style>
</head>
<body>
<div class="container">
  <?php include 'sidebar.php'; ?>
  <div class="header">
    <div><h1>Admin Panel</h1></div>
    <div class="profile-menu">
      <img src="uploads/admin_profile_pics/<?php echo htmlspecialchars($admin_profile_pic); ?>" class="avatar" onclick="toggleProfileDropdown()" />
      <div id="profileDropdown" class="dropdown-content">
        <a href="admin_logout.php">Logout</a>
      </div>
    </div>
  </div>

  <div class="content">
    <h2>➕ Add New Exam</h2>
    <form method="POST" action="add_exam.php?dept=<?= $selectedDeptId ?>">
      <label>Subject Code:</label>
      <input type="text" name="subject_code" required />

      <label>Course Name:</label>
      <input type="text" name="course_name" required />

      <label>Exam Date:</label>
      <input type="date" id="exam_date" name="exam_date" required onchange="updateDay()" />

      <label>Day:</label>
      <input type="text" id="day" name="day" readonly required />

      <label>Department:</label>
      <input type="text" value="<?= htmlspecialchars($selectedDeptName) ?>" readonly />

      <input type="hidden" name="dept" value="<?= $selectedDeptId ?>" />

      <label>Session:</label>
      <select name="session" required>
        <option value="">-- Select Session --</option>
        <option value="FN">FN (Forenoon)</option>
        <option value="AN">AN (Afternoon)</option>
      </select>

      <label>Session Time:</label>
      <input type="text" name="session_time" placeholder="e.g. 09:00 AM - 12:00 PM" required />

      <button type="submit">Add Exam</button>
    </form>

    <div style="text-align: center;">
      <a href="manage_exams.php?dept=<?= $selectedDeptId ?>" class="back-link">← Cancel</a>
    </div>
  </div>
</div>

<script>
  function updateDay() {
    const dateInput = document.getElementById("exam_date").value;
    const dayInput = document.getElementById("day");

    if (dateInput) {
      const date = new Date(dateInput);
      const days = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
      dayInput.value = days[date.getDay()];
    } else {
      dayInput.value = "";
    }
  }
</script>

<div class="footer">
  © 2025 Student Dashboard System. All rights reserved.
</div>
</body>
</html>
