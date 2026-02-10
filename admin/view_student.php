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

// Updated query to join departments and get dept_name
$stmt = $conn->prepare("
    SELECT r.username, r.email, r.phone_number, d.dept_name 
    FROM regform r 
    LEFT JOIN departments d ON r.dept_id = d.id
    WHERE r.id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($username, $email, $phone, $dept_name);
$found = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View Student</title>
  <link rel="stylesheet" href="css/admin_dashboards.css" />
  <script src="js/admin_dashboard.js"></script>
  <style>
    .content {
      margin-left: 240px;
      padding: 100px 40px 40px;
    }

    .view-box {
      max-width: 600px;
      background-color: #fff;
      padding: 25px;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    }

    h1 {
      color: #0f3460;
      font-size: 26px;
      margin-bottom: 24px;
    }

    p {
      font-size: 17px;
      margin-bottom: 12px;
      line-height: 1.5;
    }

    strong {
      color: #0f3460;
    }

    .back-link {
      display: inline-block;
      margin-top: 25px;
      padding: 10px 18px;
      background-color: #0f3460;
      color: white;
      text-decoration: none;
      border-radius: 6px;
      font-weight: bold;
    }

    .back-link:hover {
      background-color: #1f4c87;
    }
  </style>
</head>
<body>

<div class="container">

  <!-- Sidebar -->
  <?php include 'sidebar.php'; ?>

  <!-- Header -->
  <div class="header">
    <div><h1>Admin Panel</h1></div>
    <div class="profile-menu" id="profileMenu">
     <img src="uploads/admin_profile_pics/<?php echo htmlspecialchars($admin_profile_pic); ?>" class="avatar" onclick="toggleProfileDropdown()" />
      <div id="profileDropdown" class="dropdown-content">
        <a href="admin_logout.php">Log Out</a>
      </div>
    </div>
  </div>

  <!-- Main Content -->
  <div class="content">
    <?php if ($found): ?>
      <div class="view-box">
        <h1>👁️ View Student</h1>
        <p><strong>Name:</strong> <?= htmlspecialchars($username) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($email) ?></p>
   <p><strong>Department:</strong> <?= htmlspecialchars($dept_name ?: 'N/A') ?></p>
        <p><strong>Phone:</strong> <?= htmlspecialchars($phone ?: 'N/A') ?></p>
       
        <a href="admin_manage_students.php" class="back-link">← Back to List</a>
      </div>
    <?php else: ?>
      <div class="view-box">
        <h1>⚠️ Student Not Found</h1>
        <p>No record found for ID <strong><?= htmlspecialchars($id) ?></strong>.</p>
        <a href="admin_manage_students.php" class="back-link">← Back</a>
      </div>
    <?php endif; ?>
  </div>

</div>
<!-- Footer -->
<div class="footer">
  © 2025 Student Dashboard System. All rights reserved.
</div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
