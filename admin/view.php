<?php
session_start();
if (!isset($_SESSION['admin_email'])) {
    header("Location: login.html");
    exit();
}
$conn = new mysqli("localhost", "root", "", "registration_db");
$conn->set_charset("utf8mb4");

$id = $_GET['id'] ?? '';
$stmt = $conn->prepare("SELECT username, email, phone_number FROM regform WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($username, $email, $phone);
if ($stmt->fetch()):
?>
<!DOCTYPE html>
<html>
<head><title>View Student</title></head>
<body>
<style>
  body {
    margin: 0;
    padding: 40px;
    font-family: 'Segoe UI', sans-serif;
    background-color: #f4f6f9;
    color: #333;
  }

  h2 {
    color: #0f3460;
    font-size: 26px;
    margin-bottom: 20px;
  }

  p {
    font-size: 18px;
    margin: 10px 0;
    line-height: 1.6;
  }

  strong {
    color: #0f3460;
  }

  a {
    display: inline-block;
    margin-top: 30px;
    padding: 10px 18px;
    background-color: #0f3460;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    transition: background-color 0.3s ease;
  }

  a:hover {
    background-color: #1f4c87;
  }
</style>

  <h2>👁️ View Student</h2>
  <p><strong>Name:</strong> <?= htmlspecialchars($username) ?></p>
  <p><strong>Email:</strong> <?= htmlspecialchars($email) ?></p>
  <p><strong>Phone:</strong> <?= htmlspecialchars($phone) ?></p>
  <a href="admin_dashboard.php">← Back</a>
</body>
</html>
<?php else: echo "Student not found."; endif; $stmt->close(); $conn->close(); ?>
