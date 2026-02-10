<?php
session_start();
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header("Location: login.html");
    exit();
}

$user_profile_pic = $_SESSION['user_profile_pic'] ?? 'profile.png';
$email = $_SESSION['user_email'];
$safeEmail = preg_replace("/[^a-zA-Z0-9]/", "_", $email);
$directory = "uploads/" . $safeEmail . "/";
$allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'mp3', 'wav', 'ogg'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>User Panel - Media Manager</title>
  <link rel="stylesheet" href="css/dashboard.css" />
<script src="js/user_dashboard.js"></script>
  <style>
    .media-gallery-container {
      display: flex;
      flex-wrap: wrap;
      gap: 16px;
      margin-top: 20px;
    }
    .media-card {
      width: 180px;
      background: #fff;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 8px;
      text-align: center;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    .media-card img, .media-card video {
      max-width: 100%;
      height: 120px;
      object-fit: cover;
      border-radius: 4px;
    }
    .media-card audio {
      width: 100%;
      margin-top: 10px;
    }
    .media-card button {
      margin-top: 8px;
      background: crimson;
      color: white;
      border: none;
      padding: 6px 10px;
      border-radius: 4px;
      cursor: pointer;
    }
    .media-card button:hover {
      background: darkred;
    }
    form#mediaUploadForm {
      margin-bottom: 20px;
    }
  </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="header">
  <div><h1>Student Dashboard</h1></div>
  <div class="profile-menu" id="profileMenu">
    <img src="uploads/profile_pics/<?php echo htmlspecialchars($user_profile_pic); ?>" class="avatar" onclick="toggleProfileDropdown()" />
    <div id="profileDropdown" class="dropdown-content">
      <a href="user_logout.php" onclick="localStorage.clear()">Log Out</a>
    </div>
  </div>
</div>

<main id="content" style="padding: 30px;">
  <h2>📷 My Media Gallery</h2>

  <!-- Upload Form -->
  <form id="mediaUploadForm" enctype="multipart/form-data">
    <input type="file" name="media_file" required>
    <button type="submit">Upload</button>
  </form>

  <!-- Gallery -->
  <div class="media-gallery-container" id="mediaGallery">
    <?php
    if (is_dir($directory)) {
        $files = array_diff(scandir($directory), ['.', '..']);
        foreach ($files as $file) {
            $filePath = $directory . $file;
            $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            echo "<div class='media-card'>";
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                echo "<img src='$filePath' alt='Image'>";
            } elseif ($ext === 'mp4') {
                echo "<video controls><source src='$filePath'></video>";
            } elseif (in_array($ext, ['mp3', 'wav', 'ogg'])) {
                echo "<audio controls><source src='$filePath'></audio>";
            } else {
                echo "<p>Unsupported</p>";
            }
            echo "<button onclick=\"confirmDelete('$file')\">Delete</button>";
            echo "</div>";
        }
    } else {
        echo "<p>No media uploaded yet.</p>";
    }
    ?>
  </div>
</main>

<div class="footer">© 2025 Student Dashboard System. All rights reserved.</div>

<script>
document.getElementById('mediaUploadForm').addEventListener('submit', function (e) {
  e.preventDefault();
  const formData = new FormData(this);
  fetch('user_upload_media.php', {
    method: 'POST',
    body: formData
  })
    .then(res => res.text())
    .then(msg => {
      alert(msg);
      location.reload();
    })
    .catch(() => alert("❌ Upload failed"));
});

function confirmDelete(file) {
  if (confirm("Delete this file?")) {
    fetch('user_delete_media.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: 'file=' + encodeURIComponent(file)
    })
    .then(res => res.text())
    .then(msg => {
      alert(msg);
      location.reload();
    });
  }
}
</script>

</body>
</html>
