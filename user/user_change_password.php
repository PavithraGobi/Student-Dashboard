<?php
session_start();
if (!isset($_SESSION['user_email'])) {
    header("Location: user_login.html");
    exit();
}
$user_profile_pic = $_SESSION['user_profile_pic'] ?? 'profile.png';

$conn = new mysqli("localhost", "root", "", "registration_db");
$conn->set_charset("utf8mb4");

$message = '';
$message_type = '';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['change_password'])) {
    $current = $_POST['currentPassword'] ?? '';
    $new = $_POST['newPassword'] ?? '';
    $confirm = $_POST['confirmPassword'] ?? '';

    if (empty($current) || empty($new) || empty($confirm)) {
        $message = "All fields are required.";
        $message_type = "error";
    } elseif ($new !== $confirm) {
        $message = "New passwords do not match.";
        $message_type = "error";
    } elseif (strlen($new) < 6) {
        $message = "Password must be at least 6 characters.";
        $message_type = "error";
    } else {
        $stmt = $conn->prepare("SELECT password FROM regform WHERE email = ?");
        $stmt->bind_param("s", $_SESSION['user_email']);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res->num_rows === 1) {
            $row = $res->fetch_assoc();
            if (!password_verify($current, $row['password'])) {
                $message = "Current password is incorrect.";
                $message_type = "error";
            } else {
                $newHash = password_hash($new, PASSWORD_DEFAULT);
                $stmt2 = $conn->prepare("UPDATE regform SET password = ? WHERE email = ?");
                $stmt2->bind_param("ss", $newHash, $_SESSION['user_email']);
                if ($stmt2->execute()) {
                    $message = "Password changed successfully.";
                    $message_type = "success";
                } else {
                    $message = "Error updating password.";
                    $message_type = "error";
                }
                $stmt2->close();
            }
        }
        $stmt->close();
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Change Password</title>
  <link rel="stylesheet" href="css/dashboard.css" />
  <script src="js/user_dashboard.js"></script>
<style>
  .content {
    margin-left: 240px;
    padding: 100px 40px 40px;
  }
  form {
    max-width: 500px;
    margin: 0 auto;
    padding: 25px;
    border-radius: 8px;
    background-color: white;
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
  }
  label {
    display: block;
    margin-bottom: 10px;
    font-weight: 500;
  }
  input[type="password"] {
    width: 100%;
    padding: 10px;
    padding-right: 40px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 16px;
    margin-top: 5px;
    margin-bottom: 20px;
  }
  .password-wrapper {
  position: relative;
  margin-bottom: 20px;
}

.password-wrapper input[type="password"],
.password-wrapper input[type="text"] {
  width: 100%;
  padding: 10px 40px 10px 10px; /* Leave space for eye icon */
  font-size: 16px;
  border-radius: 6px;
  border: 1px solid #ccc;
  box-sizing: border-box;
}

.toggle-password {
  position: absolute;
  top: 50%;
  right: 12px;
  transform: translateY(-50%);
  cursor: pointer;
  font-size: 18px;
  color: #555;
  user-select: none;
}
  button[type="submit"] {
    width: 100%;
    padding: 10px;
    background-color: #0f3460;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: bold;
    font-size: 16px;
  }
  button[type="submit"]:hover {
    background-color: #1f4c87;
  }
  .message {
    max-width: 500px;
    margin: 0 auto 20px;
    padding: 12px;
    border-radius: 6px;
    font-weight: 600;
    text-align: center;
  }
  .success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
  }
  .error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
  }
</style>

  <script>
    function toggleVisibility(id, el) {
      const input = document.getElementById(id);
      if (input.type === "password") {
        input.type = "text";
        el.textContent = "👁️";
      } else {
        input.type = "password";
        el.textContent = "👁️";
      }
    }

    window.onload = function () {
      const msg = document.getElementById('msg-box');
      if (msg) {
        setTimeout(() => {
          msg.style.transition = 'opacity 0.5s ease';
          msg.style.opacity = 0;
          setTimeout(() => { msg.style.display = 'none'; }, 500);
        }, 3000);
      }
    };
  </script>
</head>
<body>

<div class="container">
  <?php include 'sidebar.php'; ?>

  <div class="header">
    <div><h1>Student Dashboard</h1></div>
    <div class="profile-menu" id="profileMenu">
      <img src="uploads/profile_pics/<?php echo htmlspecialchars($user_profile_pic); ?>" class="avatar" onclick="toggleProfileDropdown()" />
      <div id="profileDropdown" class="dropdown-content">
        <a href="user_logout.php" onclick="localStorage.clear()">Log Out</a>
      </div>
    </div>
  </div>

  <div class="content">
    <h2 style="text-align:center;">🔐 Change Password</h2>

    <?php if ($message): ?>
      <div id="msg-box" class="message <?= $message_type === 'success' ? 'success' : 'error' ?>">
        <?= htmlspecialchars($message) ?>
      </div>
    <?php endif; ?>

    <form method="POST">
      <div class="password-wrapper">
        <label for="currentPassword">Current Password</label>
        <input type="password" id="currentPassword" name="currentPassword" required />
        <span class="toggle-password" onclick="toggleVisibility('currentPassword', this)">👁️</span>
      </div>

      <div class="password-wrapper">
        <label for="newPassword">New Password</label>
        <input type="password" id="newPassword" name="newPassword" required />
        <span class="toggle-password" onclick="toggleVisibility('newPassword', this)">👁️</span>
      </div>

      <div class="password-wrapper">
        <label for="confirmPassword">Confirm New Password</label>
        <input type="password" id="confirmPassword" name="confirmPassword" required />
        <span class="toggle-password" onclick="toggleVisibility('confirmPassword', this)">👁️</span>
      </div>

      <button type="submit" name="change_password">Update Password</button>
    </form>

    <div style="text-align: center;">
      <a href="user_profile.php" class="back-link">← Cancel</a>
    </div>
  </div>
</div>

<!-- Footer -->
<div class="footer">
  © 2025 Student Dashboard System. All rights reserved.
</div>
</body>
</html>