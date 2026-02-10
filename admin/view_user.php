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
$stmt = $conn->prepare("SELECT username, email, phone_number FROM regform WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($username, $email, $phone);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>View Student</title>
  <link rel="stylesheet" href="css/admin_dashboards.css" />
  <script src="js/admin_dashboard.js"></script>
  <style>
    .content {
      margin-left: 240px;
      padding: 100px 40px 40px;
    }

    .view-box {
      max-width: 500px;
      background: #fff;
      padding: 25px;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
      margin: 0 auto;
    }

    h2 {
      color: #0f3460;
      font-size: 26px;
      margin-bottom: 20px;
      text-align: center;
    }

    p {
      font-size: 18px;
      margin: 10px 0;
      line-height: 1.6;
    }

    strong {
      color: #0f3460;
    }

    a.back-btn {
      display: block;
      margin-top: 30px;
      padding: 10px 18px;
      background-color: #0f3460;
      color: white;
      text-decoration: none;
      border-radius: 6px;
      text-align: center;
      transition: background-color 0.3s ease;
    }

    a.back-btn:hover {
      background-color: #1f4c87;
    }
  </style>
</head>
<body>

<div class="container">

  <!-- Sidebar include -->
  <?php include 'sidebar.php'; ?>

  <!-- Header -->
  <div class="header">
    <div><h1>Admin Panel</h1></div>
    <div class="profile-menu" id="profileMenu">
   <img src="uploads/admin_profile_pics/<?php echo htmlspecialchars($admin_profile_pic); ?>" class="avatar" onclick="toggleProfileDropdown()" />
      <div id="profileDropdown" class="dropdown-content">
        <a href="admin_logout.php">Log Out</a>
      </div>
    </div>
  </div>

  <!-- Main Content -->
  <div class="content">
    <?php if ($stmt->fetch()): ?>
      <div class="view-box">
        <h2>👁️ View User</h2>
        <p><strong>Name:</strong> <?= htmlspecialchars($username) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($email) ?></p>
        <p><strong>Phone:</strong> <?= htmlspecialchars($phone) ?></p>
        <a href="admin_users_management.php" class="back-btn">← Back</a>
      </div>
    <?php else: ?>
      <p style="color: crimson; font-weight: bold;">❌ User not found.</p>
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
