<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.html");
    exit();
}
$admin_profile_pic = $_SESSION['admin_profile_pic'] ?? 'profile.png';  // ← Here: use session variable with fallback

$conn = new mysqli("localhost", "root", "", "registration_db");
$conn->set_charset("utf8mb4");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Add Reminder
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_reminder'])) {
    $title = trim($_POST['title'] ?? '');
    $due_date = $_POST['due_date'] ?? '';
   

    if ($title && $due_date) {
        $stmt = $conn->prepare("INSERT INTO reminders (title, due_date) VALUES (?, ?)");
        $stmt->bind_param("ss", $title, $due_date);
        $stmt->execute();
        $stmt->close();

           }
    header("Location: admin_reminders.php");
    exit();
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM reminders WHERE id = $id");
    header("Location: admin_reminders.php");
    exit();
}

// Handle Edit Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_reminder'])) {
    $id = intval($_POST['id']);
    $title = trim($_POST['title'] ?? '');
    $due_date = $_POST['due_date'] ?? '';
   
    if ($title && $due_date) {
        $stmt = $conn->prepare("UPDATE reminders SET title=?, due_date=? WHERE id=?");
        $stmt->bind_param("ssi", $title, $due_date, $id);
        $stmt->execute();
        $stmt->close();

            }
    header("Location: admin_reminders.php");
    exit();
}

$reminders = $conn->query("SELECT * FROM reminders ORDER BY due_date ASC");
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Admin Reminders</title>
  <link rel="stylesheet" href="css/admin_dashboards.css">
  <script src="js/admin_dashboard.js"></script>
  <script>
    function fillEditForm(id, title, date) {
      document.getElementById('edit_id').value = id;
      document.getElementById('edit_title').value = title;
      document.getElementById('edit_due_date').value = date;
      document.getElementById('editForm').style.display = 'block';
      window.scrollTo(0, 0);
    }
  </script>
  <style>
    .reminder-card { background: #fff; border-radius: 8px; padding: 20px; margin: 40px 0; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
    .reminder-alert { color: red; font-weight: bold; }
    #editForm { display: none; margin-bottom: 30px; background: #eef; padding: 15px; border-radius: 8px; }
    .content { margin-left: 240px; padding: 50px; } 
input[type="text"] {
      width: 50%;
      padding: 8px;
      margin-bottom: 10px;
      font-size: 1em;
    }

 </style>
</head>
<body>
<div class="container">
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

  <main class="content">
    <!-- Edit Form -->
    <form id="editForm" method="POST">
      <h3>✏️ Edit Reminder</h3>
      <input type="hidden" name="id" id="edit_id">
      <input type="text" name="title" id="edit_title" placeholder="Reminder Title" required>
      <input type="date" name="due_date" id="edit_due_date" required>
      <button type="submit" name="update_reminder">✅ Update Reminder</button>
    </form>

    <!-- Add Form -->
    <form method="POST">
      <h3>➕ Add Reminder</h3>
      <input type="text" name="title" placeholder="Reminder Title" required>
      <input type="date" name="due_date" required>

      <button type="submit" name="add_reminder">➕ Add Reminder</button>
    </form>

    <hr>

    <!-- Reminder List -->
    <?php while ($row = $reminders->fetch_assoc()): ?>
      <div class="reminder-card">
        <strong><?= htmlspecialchars($row['title']) ?></strong><br>
        🗕️ Due: <?= $row['due_date'] ?>
        <?php
          $daysLeft = (strtotime($row['due_date']) - strtotime(date('Y-m-d'))) / 86400;
          if ($daysLeft <= 2 && $daysLeft >= 0) {
            echo "<div class='reminder-alert'>⚠️ Due in $daysLeft day(s)!</div>";
          }
        ?>
        <div style="margin-top:10px;">
          <button onclick="fillEditForm(<?= $row['id'] ?>, '<?= htmlspecialchars($row['title'], ENT_QUOTES) ?>', '<?= $row['due_date'] ?>')">✏️ Edit</button>
          <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this reminder?')">🗑️ Delete</a>
        </div>
      </div>
    <?php endwhile; ?>
  </main>
</div>
<!-- Footer -->
<div class="footer">
  © 2025 Student Dashboard System. All rights reserved.
</div>
</body>
</html>