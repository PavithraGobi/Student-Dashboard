<?php
session_start();
if (!isset($_SESSION['admin_email'])) {
    exit("Unauthorized");
}

$conn = new mysqli("localhost", "root", "", "registration_db");
$conn->set_charset("utf8mb4");
if ($conn->connect_error) {
    die("DB connection error");
}

$addedBy = $_SESSION['admin_email'];

$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
$search = trim($_POST['search'] ?? '');

$limit = 8;
$offset = ($page - 1) * $limit;

// Prepare search SQL snippet
$search_sql = "";
$params = [];
$types = "s";  // added_by param type

if ($search !== "") {
    $search_sql = " AND (username LIKE ? OR email LIKE ? OR phone_number LIKE ?) ";
    $search_param = "%$search%";
    $types .= "sss";
    $params = [$addedBy, $search_param, $search_param, $search_param];
} else {
    $params = [$addedBy];
}

// Get total count
$count_sql = "SELECT COUNT(*) as total FROM regform WHERE added_by = ? AND role = 'user' $search_sql";
$stmt = $conn->prepare($count_sql);

if ($search !== "") {
    $stmt->bind_param($types, ...$params);
} else {
    $stmt->bind_param("s", $addedBy);
}

$stmt->execute();
$total_result = $stmt->get_result();
$total_row = $total_result->fetch_assoc();
$total_records = $total_row['total'];

$total_pages = ceil($total_records / $limit);

// Fetch paginated data
$data_sql = "SELECT id, username, email, phone_number,date_of_birth FROM regform WHERE added_by = ? AND role = 'user' $search_sql ORDER BY id DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($data_sql);

if ($search !== "") {
    $bind_types = $types . "ii"; // e.g. ssssii
    $bind_values = array_merge($params, [$limit, $offset]);
    $stmt->bind_param($bind_types, ...$bind_values);
} else {
    $stmt->bind_param("sii", $addedBy, $limit, $offset);
}
$stmt->execute();
$result = $stmt->get_result();

?>

<table id="userTable">
  <thead>
    <tr>
      <th>S.No.</th><th>Username</th><th>Email</th><th>DOB</th><th>Phone</th><th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php if ($result->num_rows === 0): ?>
      <tr><td colspan="5" style="text-align:center;">No users found</td></tr>
    <?php else: ?>
      <?php $sno = $offset + 1; ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= $sno++ ?></td>
          <td><?= htmlspecialchars($row['username']) ?></td>
          <td><?= htmlspecialchars($row['email']) ?></td>
               <td><?= htmlspecialchars($row['date_of_birth']) ?></td>
          <td><?= htmlspecialchars($row['phone_number']) ?></td>
     
          <td>
            <a class="action-btn" href="view_user.php?id=<?= $row['id'] ?>">👁 View</a>
            <a class="action-btn" href="edit_user.php?id=<?= $row['id'] ?>">✏️ Edit</a>
            <a class="action-btn" href="delete_user.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this user?')">🗑️ Delete</a>
          </td>
        </tr>
      <?php endwhile; ?>
    <?php endif; ?>
  </tbody>
</table>

<div class="pagination">
  <?php for ($p = 1; $p <= $total_pages; $p++): ?>
    <a href="#" class="page-link <?= $p == $page ? 'active' : '' ?>" data-page="<?= $p ?>"><?= $p ?></a>
  <?php endfor; ?>
</div>
