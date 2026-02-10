<?php
session_start();
if (!isset($_SESSION['admin_email'])) {
    header("Location: login.html");
    exit();
}
$conn = new mysqli("localhost", "root", "", "registration_db");
$conn->set_charset("utf8mb4");

$id = $_GET['id'] ?? '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $stmt = $conn->prepare("UPDATE regform SET username=?, email=?, phone_number=? WHERE id=?");
    $stmt->bind_param("sssi", $username, $email, $phone, $id);
    if ($stmt->execute()) {
        header("Location: admin_dashboard.php");
        exit();
    } else {
        echo "Update failed.";
    }
}

$stmt = $conn->prepare("SELECT username, email, phone_number FROM regform WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($username, $email, $phone);
$stmt->fetch();
?>
<!DOCTYPE html>
<html>
<head><style>
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
    margin-bottom: 24px;
  }

  form {
    max-width: 400px;
    background-color: none;
    padding: 25px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
  }

  label {
    display: block;
    margin-bottom: 15px;
    font-weight: 500;
  }

  input[type="text"],
  input[type="email"] {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 16px;
    margin-top: 5px;
  }

  button[type="submit"] {
    padding: 10px 20px;
    background-color: #0f3460;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: bold;
    font-size: 16px;
    margin-top: 15px;
  }

  button[type="submit"]:hover {
    background-color: #1f4c87;
  }

  a {
    display: inline-block;
    margin-top: 20px;
    color: #0f3460;
    text-decoration: none;
    font-weight: 500;
  }

  a:hover {
    text-decoration: underline;
  }
</style>
<title>Edit Student</title></head>
<body>

  <h2>✏️ Edit Student</h2>
  <form method="POST">
    <label>Name: <input type="text" name="username" value="<?= htmlspecialchars($username) ?>"></label><br><br>
    <label>Email: <input type="email" name="email" value="<?= htmlspecialchars($email) ?>"></label><br><br>
    <label>Phone: <input type="text" name="phone" value="<?= htmlspecialchars($phone) ?>"></label><br><br>
    <button type="submit">Update</button>
  </form>
  <a href="admin_dashboard.php">← Cancel</a>
</body>
</html>
<?php $stmt->close(); $conn->close(); ?>
