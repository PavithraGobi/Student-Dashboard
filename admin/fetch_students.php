<?php
session_start();
$conn = new mysqli("localhost", "root", "", "registration_db");
$conn->set_charset("utf8mb4");

$limit = 8;
$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
$search = trim($_POST['search'] ?? '');
$offset = ($page - 1) * $limit;

$where = "WHERE r.role = 'user' AND r.added_by IS NULL";
if (!empty($search)) {
    $escaped_search = $conn->real_escape_string($search);
    $where .= " AND (r.username LIKE '%$escaped_search%' OR r.email LIKE '%$escaped_search%' OR r.phone_number LIKE '%$escaped_search%')";
}

// Total record count
$totalResult = $conn->query("SELECT COUNT(*) AS total FROM regform r $where");
$total = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($total / $limit);

// Fetch paginated student data with department name via JOIN
$sql = "SELECT r.id, r.username, r.email, r.phone_number, r.date_of_birth, d.dept_name
        FROM regform r
        LEFT JOIN departments d ON r.dept_id = d.id
        $where ORDER BY r.id DESC LIMIT $limit OFFSET $offset";

$result = $conn->query($sql);

// Output starts
$html = '<div class="table-wrapper"><table id="userTable">
<thead>
  <tr>
    <th>S.No.</th><th>Username</th><th>Email</th><th>DOB</th><th>Phone</th><th>Department</th><th>Actions</th>
  </tr>
</thead>
<tbody>';

if ($result && $result->num_rows > 0) {
    $sno = $offset + 1;
    while ($row = $result->fetch_assoc()) {
        $dept = htmlspecialchars($row['dept_name'] ?? 'N/A');
        $html .= '<tr>
          <td>' . $sno++ . '</td>
          <td>' . htmlspecialchars($row['username']) . '</td>
          <td>' . htmlspecialchars($row['email']) . '</td>
          <td>' . htmlspecialchars($row['date_of_birth']) . '</td>
          <td>' . htmlspecialchars($row['phone_number']) . '</td>
          <td>' . $dept . '</td>
          <td>
            <a class="action-btn" href="view_student.php?id=' . $row['id'] . '">👁 View</a>
            <a class="action-btn" href="edit_student.php?id=' . $row['id'] . '">✏️ Edit</a>
            <a class="action-btn" href="delete_student.php?id=' . $row['id'] . '" onclick="return confirm(\'Are you sure you want to delete this student?\')">🗑️ Delete</a>
          </td>
        </tr>';
    }
} else {
    $html .= '<tr><td colspan="7" style="text-align:center;">No students found.</td></tr>';
}

$html .= '</tbody></table></div>';

// Pagination controls
$html .= '<div class="pagination">';
if ($page > 1) {
    $html .= '<a href="#" class="page-link" data-page="' . ($page - 1) . '">« Prev</a>';
}
for ($i = 1; $i <= $totalPages; $i++) {
    $active = ($i == $page) ? 'active' : '';
    $html .= '<a href="#" class="page-link ' . $active . '" data-page="' . $i . '">' . $i . '</a>';
}
if ($page < $totalPages) {
    $html .= '<a href="#" class="page-link" data-page="' . ($page + 1) . '">Next »</a>';
}
$html .= '</div>';

echo $html;
?>
