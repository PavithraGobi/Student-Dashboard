<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.html");
    exit();
}

$admin_profile_pic = $_SESSION['admin_profile_pic'] ?? 'profile.png';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Panel</title>
  <link rel="stylesheet" href="css/admin_dashboards.css" />
</head>
<body>

<div class="header">
  <div><h1>Admin panel</h1></div>
  <div class="profile-menu" id="profileMenu">
<img src="uploads/admin_profile_pics/<?php echo htmlspecialchars($admin_profile_pic); ?>" class="avatar" onclick="toggleProfileDropdown()" />

       <div id="profileDropdown" class="dropdown-content">
      <a href="admin_logout.php">Log Out</a>
    </div>
  </div>
</div>
	<?php include'sidebar.php'; ?>
  <main id="content">
    <h2>Welcome, Admin 👋</h2>
    <p>Select an item from the Admin Panel to continue.</p>
  </main>
</div>


<script src="js/admin_dashboard.js"></script>
</body>
</html>
