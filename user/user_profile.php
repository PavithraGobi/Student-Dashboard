<?php
session_start();

if (!isset($_SESSION['user_email'])) {
    header("Location: user_login.html");
    exit();
}

$conn = new mysqli("localhost", "root", "", "registration_db");
$conn->set_charset("utf8mb4");

$user_email = $_SESSION['user_email'];
$message = "";

// Fetch current user details
$stmt = $conn->prepare("SELECT username, email, phone_number, date_of_birth, profile_pic FROM regform WHERE email = ? AND role = 'user'");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$stmt->bind_result($username, $email, $phone, $dob, $profile_pic);
$stmt->fetch();
$stmt->close();

if (!$profile_pic) {
    $profile_pic = 'profile.png';
}
$_SESSION['user_profile_pic'] = $profile_pic;

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newUsername = trim($_POST['editUsername'] ?? '');
    $newEmail = trim($_POST['editEmail'] ?? '');
    $newPhone = trim($_POST['editPhoneNumber'] ?? '');
    $newDob = $_POST['editDateOfBirth'] ?? '';

    // Handle file upload
    if (!empty($_FILES['profile_pic']['name'])) {
        $targetDir = "uploads/profile_pics/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $fileName = basename($_FILES['profile_pic']['name']);
        $targetFile = $targetDir . time() . '_' . $fileName;

        if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $targetFile)) {
            $profile_pic = basename($targetFile);
        }
    }

    // Update the user record
    $update = $conn->prepare("UPDATE regform SET username=?, email=?, phone_number=?, date_of_birth=?, profile_pic=? WHERE email=? AND role='user'");
    $update->bind_param("ssssss", $newUsername, $newEmail, $newPhone, $newDob, $profile_pic, $user_email);

    if ($update->execute()) {
        $_SESSION['user_username'] = $newUsername;
        $_SESSION['user_email'] = $newEmail;
        $_SESSION['user_profile_pic'] = $profile_pic;
        $_SESSION['msg'] = "✅ Profile updated successfully!";
        header("Location: user_profile.php");
        exit();
    } else {
        $message = "❌ Failed to update profile.";
    }
    $update->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>User Profile</title>
  <link rel="stylesheet" href="css/dashboard.css" />
  <script src="js/user_dashboard.js"></script>
  <style>
    .content {
      margin-left: 240px;
      padding: 100px 40px 40px;
    }
    .profile-pic {
      width: 120px;
      height: 120px;
      object-fit: cover;
      border-radius: 50%;
      margin-bottom: 10px;
    }
    form {
      max-width: 500px;
      background: #fff;
      padding: 25px;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    label {
      display: block;
      margin-bottom: 6px;
      font-weight: 500;
    }
    input {
      width: 100%;
      padding: 10px;
      margin-bottom: 16px;
      font-size: 15px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }
    button {
      background-color: #0f3460;
      color: white;
      font-weight: bold;
      padding: 10px 18px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-size: 16px;
    }
    button:hover {
      background-color: #1f4c87;
    }
    .message {
      margin-bottom: 20px;
      font-weight: bold;
      color: green;
    }
  </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="header">
  <div><h1>Student Dashboard</h1></div>
  <div class="profile-menu" id="profileMenu">
    <img src="uploads/profile_pics/<?php echo htmlspecialchars($_SESSION['user_profile_pic']); ?>" class="avatar" onclick="toggleProfileDropdown()" />
    <div id="profileDropdown" class="dropdown-content">
      <a href="user_logout.php" onclick="localStorage.clear()">Log Out</a>
    </div>
  </div>
</div>

<div class="content">
  <h2>👤 User Profile</h2>

  <?php if (!empty($_SESSION['msg'])): ?>
    <div class="message"><?= $_SESSION['msg']; unset($_SESSION['msg']); ?></div>
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data">
    <img src="uploads/profile_pics/<?php echo htmlspecialchars($profile_pic); ?>" class="profile-pic" alt="Profile Picture" />

    <label>Username:</label>
    <input type="text" name="editUsername" value="<?= htmlspecialchars($username) ?>" required>

    <label>Email:</label>
    <input type="email" name="editEmail" value="<?= htmlspecialchars($email) ?>" required>

    <label>Phone Number:</label>
    <input type="text" name="editPhoneNumber" value="<?= htmlspecialchars($phone) ?>">

    <label>Date of Birth:</label>
    <input type="date" name="editDateOfBirth" value="<?= htmlspecialchars($dob) ?>">

    <label>Change Profile Picture:</label>
    <input type="file" name="profile_pic" accept="image/*">

    <button type="submit">Update Profile</button>
  </form>
</div>

<!-- Footer -->
<div class="footer">
  © 2025 Student Dashboard System. All rights reserved.
</div>

</body>
</html>
