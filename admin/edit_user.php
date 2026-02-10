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
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $dob = trim($_POST['dob'] ?? '');

    if ($username) {
        $stmt = $conn->prepare("UPDATE regform SET username=?, phone_number=?, date_of_birth=? WHERE id=?");
        $stmt->bind_param("sssi", $username, $phone, $dob, $id);

        if ($stmt->execute()) {
            header("Location: admin_users_management.php");
            exit();
        } else {
            $error = "❌ Update failed: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error = "⚠️ Username is required.";
    }
}

$stmt = $conn->prepare("SELECT username, email, phone_number, date_of_birth FROM regform WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($username, $email, $phone, $dob);
$stmt->fetch();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit User</title>
  <link rel="stylesheet" href="css/admin_dashboards.css" />
  <script src="js/admin_dashboard.js"></script>
  <style>
    .content {
      margin-left: 240px;
      padding: 100px 40px 40px;
    }
    form {
      max-width: 500px;
      background: #fff;
      padding: 25px;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    }
    label {
      display: block;
      margin-bottom: 6px;
      font-weight: 500;
    }
    input {
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
    .back-link:hover {
      text-decoration: underline;
    }
    .error {
      margin-bottom: 15px;
      color: crimson;
      font-weight: 500;
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

  <!-- Content -->
  <div class="content">
    <h1>✏️ Edit User</h1>

    <?php if (!empty($error)): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
      <label>Username:</label>
      <input type="text" name="username" value="<?= htmlspecialchars($username) ?>" required>

      <label>Date of Birth:</label>
      <input type="date" name="dob" value="<?= htmlspecialchars($dob) ?>">

      <label>Phone Number:</label>
      <input type="text" name="phone" value="<?= htmlspecialchars($phone) ?>">

      <button type="submit">Update</button>
    </form>

    <a href="admin_users_management.php" class="back-link">← Cancel</a>
  </div>

</div>
<!-- Footer -->
<div class="footer">
  © 2025 Student Dashboard System. All rights reserved.
</div>
</body>
</html>
