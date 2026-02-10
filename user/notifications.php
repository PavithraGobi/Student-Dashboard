<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$conn = new mysqli("localhost", "root", "", "registration_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, title, message, seen FROM notifications 
        WHERE user_id IS NULL OR user_id = ? 
        ORDER BY id DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Notifications</title>
  <link rel="stylesheet" href="css/dashboard.css">
  <script src="js/user_dashboard.js"></script>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background-color: #f4f6f9;
    }

    .main-content {
    
      margin-left: 250px;
      padding: 100px 40px 40px;
    }

    h2 {
      text-align: center;
      color: #0f3460;
      margin-bottom: 30px;
    }

    .notification {
      background: #fff;
      padding: 15px 20px;
      margin-bottom: 15px;
      border-radius: 8px;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
      transition: all 0.3s ease;
    }

    .notification.unseen {
      border-left: 5px solid #007BFF;
      background: #eef5ff;
    }

    .notification h4 {
      margin: 0 0 6px;
      color: #0f3460;
    }

    .notification p {
      margin: 0;
      color: #333;
      white-space: pre-wrap;
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

<div class="main-content">
  <h2>🔔 Notifications</h2>

  <?php if ($result->num_rows === 0): ?>
    <p style="text-align: center; color: #777;">No notifications available.</p>
  <?php else: ?>
    <?php while($row = $result->fetch_assoc()): ?>
      <div class="notification <?php echo $row['seen'] == 0 ? 'unseen' : ''; ?>">
        <h4><?php echo htmlspecialchars($row['title']); ?></h4>
        <p><?php echo htmlspecialchars($row['message']); ?></p>
      </div>
    <?php endwhile; ?>
  <?php endif; ?>
</div>



</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
