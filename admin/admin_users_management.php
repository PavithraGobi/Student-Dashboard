<?php
session_start();
if (!isset($_SESSION['admin_email'])) {
    header("Location: admin_login.html");
    exit();
}

$admin_profile_pic = $_SESSION['admin_profile_pic'] ?? 'profile.png';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Manage Users</title>
  <link rel="stylesheet" href="css/admin_dashboards.css" />
  <style>
    /* Include pagination and table styles same as students page */
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

<div class="header">
  <div><h1>Admin Panel</h1></div>
  <div class="profile-menu" id="profileMenu">
    <img src="uploads/admin_profile_pics/<?php echo htmlspecialchars($admin_profile_pic); ?>" class="avatar" onclick="toggleProfileDropdown()" />
    <div id="profileDropdown" class="dropdown-content">
      <a href="admin_logout.php" onclick="localStorage.clear()">Log Out</a>
    </div>
  </div>
</div>

<div class="container" style="margin-left: 240px; padding: 20px;">
  <div class="search-container">
    <input type="text" id="userSearchInput" placeholder="🔍 Search by name, email, or phone..." />
  </div>

  <div id="usersTableContainer">
    <!-- User table + pagination loads here -->
  </div>
</div>

<div class="footer">
  © 2025 Student Dashboard System. All rights reserved.
</div>

<script>
function loadUsers(page = 1, search = '') {
  const formData = new FormData();
  formData.append('page', page);
  formData.append('search', search);

  fetch('fetch_users.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.text())
  .then(data => {
    document.getElementById('usersTableContainer').innerHTML = data;

    // Add pagination event listeners
    document.querySelectorAll('.page-link').forEach(btn => {
      btn.addEventListener('click', e => {
        e.preventDefault();
        const newPage = e.target.dataset.page;
        loadUsers(newPage, document.getElementById('userSearchInput').value);
      });
    });
  });
}

document.addEventListener('DOMContentLoaded', () => {
  loadUsers();

  document.getElementById('userSearchInput').addEventListener('input', () => {
    loadUsers(1, document.getElementById('userSearchInput').value);
  });
});
</script>

</body>
</html>
