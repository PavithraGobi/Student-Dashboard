<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // redirect if not logged in
    exit();
}

$user_id = $_SESSION['user_id'];

$conn = new mysqli("localhost", "root", "", "registration_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch notifications for all users (user_id IS NULL) or specific to logged-in user
$sql = "SELECT id, title, message, seen FROM notifications 
        WHERE user_id IS NULL OR user_id = ? 
        ORDER BY id DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Your Notifications</title>
    <style>
      body { font-family: Arial, sans-serif; margin: 20px; background: #f9f9f9; }
      .notification { background: #fff; padding: 15px; margin-bottom: 10px; border-radius: 5px; box-shadow: 0 0 5px rgba(0,0,0,0.1);}
      .notification.unseen { border-left: 4px solid #007BFF; }
      .notification h4 { margin: 0 0 5px; }
    </style>
</head>
<body>
    <h2>Your Notifications</h2>

    <?php if ($result->num_rows === 0): ?>
        <p>No notifications yet.</p>
    <?php else: ?>
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="notification <?php echo $row['seen'] == 0 ? 'unseen' : ''; ?>">
                <h4><?php echo htmlspecialchars($row['title']); ?></h4>
                <p><?php echo nl2br(htmlspecialchars($row['message'])); ?></p>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
