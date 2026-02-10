<?php
session_start();
if (!isset($_SESSION['admin_email'])) {
    header("Location: admin_login.html");
    exit();
}

$admin_username = $_SESSION['admin_username'] ?? 'Admin';
$admin_profile_pic = $_SESSION['admin_profile_pic'] ?? 'profile.png';
$admin_email = $_SESSION['admin_email'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Manage Students</title>
  <link rel="stylesheet" href="css/admin_dashboards.css" />
  <style>
    .pagination {
      text-align: center;
      margin-top: 15px;
    }
    .pagination a {
      margin: 0 4px;
      padding: 6px 12px;
      background-color: #444;
      color: #fff;
      border-radius: 4px;
      text-decoration: none;
    }
    .pagination a.active {
      background-color: #007bff;
    }
    .pagination a:hover {
      background-color: #555;
    }
    .search-container {
      margin-bottom: 20px;
    }
    .search-container input {
      padding: 8px;
      width: 350px;
      font-size: 16px;
    }
    .table-wrapper {
      overflow-x: auto;
    }
  </style>
  <script src="js/admin_dashboard.js"></script>
</head>
<body>

<?php include 'sidebar.php'; ?>

<!-- Header -->
<div class="header">
  <div><h1>Admin Panel</h1></div>
  <div class="profile-menu" id="profileMenu">
    <img src="uploads/admin_profile_pics/<?php echo htmlspecialchars($admin_profile_pic); ?>" class="avatar" onclick="toggleProfileDropdown()" />
    <div id="profileDropdown" class="dropdown-content">
      <a href="admin_logout.php" onclick="localStorage.clear()">Log Out</a>
    </div>
  </div>
</div>

<!-- Main Content -->
<div class="container" style="margin-left: 240px; padding: 20px;">
  <div class="search-container">
    <input type="text" id="userSearchInput" placeholder="🔍 Search by name, email, or phone..." />
  </div>

  <div id="studentsTableContainer">
    <!-- Student table + pagination loads here -->
  </div>
</div>

<!-- Footer -->
<div class="footer">
  © 2025 Student Dashboard System. All rights reserved.
</div>

<!-- JavaScript -->
<script>
function loadStudents(page = 1, search = '') {
  const formData = new FormData();
  formData.append('page', page);
  formData.append('search', search);

  fetch('fetch_students.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.text())
  .then(data => {
    document.getElementById('studentsTableContainer').innerHTML = data;

    // Bind click events for pagination
    document.querySelectorAll('.page-link').forEach(btn => {
      btn.addEventListener('click', e => {
        e.preventDefault();
        const newPage = e.target.dataset.page;
        loadStudents(newPage, document.getElementById('userSearchInput').value);
      });
    });
  });
}

// Load initial data
document.addEventListener('DOMContentLoaded', () => {
  loadStudents();

  document.getElementById('userSearchInput').addEventListener('input', () => {
    loadStudents(1, document.getElementById('userSearchInput').value);
  });
});
</script>
</body>
</html>
