<?php
session_start();

if (!isset($_SESSION['admin_email'])) {
    header("Location: admin_login.html");
    exit();
}
$admin_profile_pic = $_SESSION['admin_profile_pic'] ?? 'profile.png';

$conn = new mysqli("localhost", "root", "", "registration_db");
$conn->set_charset("utf8mb4");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $phone = trim($_POST['phone_number'] ?? '');
    $dob = $_POST['date_of_birth'] ?? '';
    $role = $_POST['role'] ?? '';

    if (!$username) $errors[] = "Username is required.";
    if (!$email) $errors[] = "Email is required.";
    if (!$password) $errors[] = "Password is required.";
    if ($password !== $confirm_password) $errors[] = "Passwords do not match.";
    if (!$dob) $errors[] = "Date of Birth is required.";
    if (!$role) $errors[] = "Role is required.";

    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $avatar = '';
        $profile_pic = 'profile.png';
        $added_by = $_SESSION['admin_email'];

        $stmt = $conn->prepare("INSERT INTO regform 
            (username, email, date_of_birth, phone_number, password, avatar, profile_pic, role, added_by)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param("sssssssss", $username, $email, $dob, $phone, $hashedPassword, $avatar, $profile_pic, $role, $added_by);

        if ($stmt->execute()) {
            header("Location: admin_users_management.php?msg=User added successfully");
            exit();
        } else {
            $errors[] = "Database error: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Add User</title>
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
    .password-wrapper {
      position: relative;
    }
    .toggle-password {
      position: absolute;
      top: 50%;
      right: 10px;
      transform: translateY(-50%);
      cursor: pointer;
      font-size: 18px;
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
    .error {
      color: red;
      margin-bottom: 15px;
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
        <a href="admin_logout.php" onclick="localStorage.clear()">Log Out</a>
      </div>
    </div>
  </div>

  <div class="content">
    <h1>➕ Add New User</h1>

    <?php if (!empty($errors)): ?>
      <div class="error">
        <?php foreach ($errors as $error): ?>
          <div><?= htmlspecialchars($error) ?></div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <form method="POST">
      <label>Username:</label>
      <input type="text" name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>

      <label>Email:</label>
      <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>

      <label>Password:</label>
      <div class="password-wrapper">
        <input type="password" id="password" name="password" required>
        <span class="toggle-password" onclick="togglePassword('password')">👁️</span>
      </div>

      <label>Confirm Password:</label>
      <div class="password-wrapper">
        <input type="password" id="confirm_password" name="confirm_password" required>
        <span class="toggle-password" onclick="togglePassword('confirm_password')">👁️</span>
      </div>

      <label>Phone Number:</label>
      <input type="text" name="phone_number" value="<?= htmlspecialchars($_POST['phone_number'] ?? '') ?>">

      <label>Date of Birth:</label>
      <input type="date" name="date_of_birth" value="<?= htmlspecialchars($_POST['date_of_birth'] ?? '') ?>" required>

      <label>Role:</label>
      <select name="role" required>
        <option value="">-- Select Role --</option>
        <option value="user" <?= (($_POST['role'] ?? '') === 'user') ? 'selected' : '' ?>>User</option>
        <option value="admin" <?= (($_POST['role'] ?? '') === 'admin') ? 'selected' : '' ?>>Admin</option>
      </select>

      <button type="submit">Add User</button>
    </form>
    <a href="admin_users_management.php" class="back-link">← Cancel</a>
  </div>
</div>

<script>
function togglePassword(id) {
  const input = document.getElementById(id);
  input.type = input.type === "password" ? "text" : "password";
}
</script>

<!-- Footer -->
<div class="footer">
  © 2025 Student Dashboard System. All rights reserved.
</div>
</body>
</html>
