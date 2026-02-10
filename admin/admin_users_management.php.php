<?php
session_start();
if (!isset($_SESSION['admin_email'])) {
    header("Location: login.html");
    exit();
}

$admin_username = $_SESSION['admin_username'] ?? 'Admin';
$admin_profile_pic = $_SESSION['admin_profile_pic'] ?? 'default.png';

// DB Connection
$conn = new mysqli("localhost", "root", "", "registration_db");
$conn->set_charset("utf8mb4");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch students
$students = [];
$query = $conn->query("SELECT id, username, email, phone_number FROM regform WHERE role = 'user'");
if ($query && $query->num_rows > 0) {
    while ($row = $query->fetch_assoc()) {
        $students[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Manage Students</title>
  <link rel="stylesheet" href="css/admin_dashboards.css" />
  <script src="js/admin_dashboard.js"> </script>
</head>
<body>

<?php include 'sidebar.php'; ?>

<!-- Header -->
<div class="header">
  <div><h1>Admin Panel</h1></div>
  <div class="profile-menu" id="profileMenu">
    <img src="uploads/<?php echo htmlspecialchars($admin_profile_pic); ?>" class="avatar" onclick="toggleProfileDropdown()" />
    <div id="profileDropdown" class="dropdown-content">
      <a href="admin_logout.php">Log Out</a>
    </div>
  </div>
</div>

<!-- Main Content -->
<div class="container">
  <main id="content">
  <div class="search-container">
  <input type="text" id="userSearchInput" placeholder="🔍 Search by name, email, or phone..." onkeyup="searchUserTable()" />
</div>


    <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>ID</th><th>Username</th><th>Email</th><th>Phone</th><th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($students)): ?>
          <?php foreach ($students as $student): ?>
            <tr>
              <td><?= $student['id'] ?></td>
              <td><?= htmlspecialchars($student['username']) ?></td>
              <td><?= htmlspecialchars($student['email']) ?></td>
              <td><?= htmlspecialchars($student['phone_number']) ?></td>
              <td>
                <a class="action-btn" href="view_user.php?id=<?= $student['id'] ?>">👁 View</a>
                <a class="action-btn" href="edit_user.php?id=<?= $student['id'] ?>">✏️ Edit</a>
                <a class="action-btn" href="delete_user.php?id=<?= $student['id'] ?>" onclick="return confirm('Are you sure you want to delete this user?')">🗑️ Delete</a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="5">No students found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
   </div>
  </main>
</div>



</body>
</html>
