<?php
session_start();
$admin_profile_pic = $_SESSION['admin_profile_pic'] ?? 'profile.png';  // ← Here: use session variable with fallback

if (!isset($_SESSION['admin_email'])) {
    header("Location: admin_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Privacy Policy</title>
  <link rel="stylesheet" href="css/admin_dashboards.css" />
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
    // Sidebar submenu toggle and state save/load
    function toggleSettings(el) {
      const label = el.textContent.trim();

      document.querySelectorAll('.settings-toggle').forEach(item => {
        if (item !== el) {
          item.classList.remove('open');
          const otherSubmenu = item.nextElementSibling;
          if (otherSubmenu && otherSubmenu.classList.contains('submenu')) {
            otherSubmenu.style.display = 'none';
            const arrow = item.querySelector('.arrow');
            if (arrow) arrow.textContent = '▼';
          }
        }
      });

      const isOpening = !el.classList.contains('open');
      if (isOpening) {
        el.classList.add('open');
        const submenu = el.nextElementSibling;
        if (submenu && submenu.classList.contains('submenu')) submenu.style.display = 'block';
        const arrow = el.querySelector('.arrow');
        if (arrow) arrow.textContent = '▲';
        localStorage.setItem('openSubmenu', label);
      } else {
        el.classList.remove('open');
        const submenu = el.nextElementSibling;
        if (submenu && submenu.classList.contains('submenu')) submenu.style.display = 'none';
        const arrow = el.querySelector('.arrow');
        if (arrow) arrow.textContent = '▼';
        localStorage.removeItem('openSubmenu');
      }
    }

    // Profile dropdown toggle
    function toggleProfileDropdown() {
      document.getElementById('profileMenu').classList.toggle('show');
    }

    // Hide dropdown if clicking outside
    window.onclick = function(e) {
      if (!e.target.matches('.avatar')) {
        const dropdown = document.getElementById("profileDropdown");
        if (dropdown && dropdown.parentElement.classList.contains("show")) {
          dropdown.parentElement.classList.remove("show");
        }
      }
    };

    // Restore submenu open state on page load
    window.addEventListener('DOMContentLoaded', () => {
      const savedLabel = localStorage.getItem('openSubmenu');
      if (savedLabel) {
        const matchedItem = Array.from(document.querySelectorAll('.settings-toggle')).find(item => item.textContent.trim() === savedLabel);
        if (matchedItem) {
          matchedItem.classList.add('open');
          const submenu = matchedItem.nextElementSibling;
          if (submenu && submenu.classList.contains('submenu')) submenu.style.display = 'block';
          const arrow = matchedItem.querySelector('.arrow');
          if (arrow) arrow.textContent = '▲';
        } else {
          localStorage.removeItem('openSubmenu');
        }
      }
    });
  </script>
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
    <div class="privacy-box">
      <h2>🔒 Privacy Policy</h2>

      <p>
        This admin panel is designed to protect and manage user and system data securely.
        As an admin, your activities are monitored and logged to ensure responsible handling of sensitive data.
      </p>

      <p>
        All personal data stored in this system (emails, passwords, phone numbers, etc.) are securely stored and never shared with third parties. Passwords are encrypted using strong hashing algorithms.
      </p>

      <p>
        Admins are advised not to share login credentials. Your access to user data must follow institution or organizational policy.
      </p>

      <p>
        Any misuse or unauthorized access may lead to account deactivation and legal consequences.
      </p>

      <p>
        This privacy policy may be updated periodically. You will be notified of any critical changes.
      </p>

      <a href="admin_profile.php" class="btn-back">← Back to profile</a>
    </div>
  </div>
</div>
<!-- Footer -->
<div class="footer">
  © 2025 Student Dashboard System. All rights reserved.
</div>
</body>
</html>
