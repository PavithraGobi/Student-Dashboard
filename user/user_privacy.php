<?php
session_start();
$user_profile_pic = $_SESSION['user_profile_pic'] ?? 'profile.png';

if (!isset($_SESSION['user_email'])) {
    header("Location: user_login.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Privacy Policy</title>
  <link rel="stylesheet" href="css/dashboard.css" /> <!-- change if needed -->
<script src="js/user_dashboard.js"></script>
  <style>
    .content {
      margin-left: 240px;
      padding: 100px 40px 40px;
    }

    .privacy-box {
      max-width: 800px;
      margin: 0 auto;
      padding: 30px;
      background-color: white;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }

    h2 {
      text-align: center;
      color: #0f3460;
      margin-bottom: 25px;
    }

    p {
      font-size: 16px;
      line-height: 1.6;
      color: #333;
      margin-bottom: 20px;
    }

    a.btn-back {
      display: block;
      text-align: center;
      margin-top: 30px;
      color: #0f3460;
      text-decoration: none;
      font-weight: bold;
    }

    a.btn-back:hover {
      text-decoration: underline;
    }
  </style>
  <script>
    function toggleProfileDropdown() {
      document.getElementById('profileMenu').classList.toggle('show');
    }

    window.onclick = function(e) {
      if (!e.target.matches('.avatar')) {
        const dropdown = document.getElementById("profileDropdown");
        if (dropdown && dropdown.parentElement.classList.contains("show")) {
          dropdown.parentElement.classList.remove("show");
        }
      }
    };
  </script>
</head>
<body>

<div class="container">

  <?php include 'sidebar.php'; ?> <!-- use appropriate user sidebar -->

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
    <div class="privacy-box">
      <h2>🔒 Privacy Policy</h2>

      <p>
        Your privacy is important to us. As a registered user, your personal information is stored securely and used only for academic and communication purposes within this portal.
      </p>

      <p>
        We collect data such as your name, email address, phone number, date of birth, and department only to personalize your dashboard experience and facilitate communication with faculty or admins.
      </p>

      <p>
        Your password is encrypted and never visible to any admin or system operator. Please keep your login credentials safe and avoid sharing them with others.
      </p>

      <p>
        We do not share your personal data with any third-party services or external organizations.
      </p>

      <p>
        If you suspect any unauthorized access to your account, please report it to the admin immediately for investigation and protection.
      </p>

      <a href="user_profile.php" class="btn-back">← Back to profile</a>
    </div>
  </div>

</div>

<!-- Footer -->
<div class="footer">
  © 2025 Student Dashboard System. All rights reserved.
</div>

</body>
</html>
