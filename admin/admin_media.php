<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.html");
    exit();
}

$admin_profile_pic = $_SESSION['admin_profile_pic'] ?? 'profile.png';  // ← Here: use session variable with fallback

$email = $_SESSION['admin_email'];
$safeEmail = preg_replace("/[^a-zA-Z0-9]/", "_", $email);
$directory = "uploads/" . $safeEmail . "/";
$allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'mp3', 'wav', 'ogg'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Panel - Media Manager</title>
  <link rel="stylesheet" href="css/admin_dashboards.css" />
  <style>
    .media-gallery-container {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      margin-top: 10px;
    }
    .media-card {
      text-align: center;
      border: 1px solid #ddd;
      padding: 10px;
      border-radius: 8px;
      background-color: #fff;
      max-width: 200px;
    }
    .media-card img, .media-card video {
      width: 100%;
      border-radius: 6px;
      cursor: pointer;
    }
    /* Modal container */
    #imageModal {
      display: none;
      position: fixed;
      top: 0; left: 0;
      width: 100vw; height: 100vh;
      background: rgba(0,0,0,0.8);
      justify-content: center;
      align-items: center;
      z-index: 1000;
      user-select: none;
      /* flex layout for arrows + image */
      flex-direction: row;
      gap: 20px;
    }
    /* Modal image */
    #imageModal img {
      max-width: 80%;
      max-height: 90%;
      border-radius: 10px;
      user-select: none;
    }
    /* Navigation arrows */
    .nav-arrow {
      background: transparent;
      border: none;
      color: white;
      font-size: 48px;
      cursor: pointer;
      user-select: none;
      transition: color 0.2s;
      align-self: center;
      padding: 0 10px;
    }
    .nav-arrow:hover {
      color: #7B43CC;
    }
    /* Close button */
    .close-btn {
      position: absolute;
      top: 20px;
      right: 30px;
      font-size: 30px;
      color: white;
      cursor: pointer;
      user-select: none;
      z-index: 1001;
    }

    /* Custom message box */
    #messageBox {
      display: none;
      position: fixed;
      top: 20px;
      right: 20px;
      background: #0f3460;
      color: white;
      padding: 12px 20px;
      border-radius: 5px;
      z-index: 1100;
      box-shadow: 0 4px 12px rgba(0,0,0,0.3);
      font-weight: bold;
      user-select: none;
    }

    /* Custom confirmation modal */
    #confirmModal {
      display: none;
      position: fixed;
      top: 0; left: 0;
      width: 100vw; height: 100vh;
      background: rgba(0,0,0,0.6);
      justify-content: center;
      align-items: center;
      z-index: 1200;
    }
    #confirmBox {
      background: white;
      padding: 20px 30px;
      border-radius: 8px;
      max-width: 320px;
      text-align: center;
      box-shadow: 0 0 15px rgba(0,0,0,0.3);
      font-size: 16px;
    }
    #confirmBox button {
      margin: 15px 10px 0 10px;
      padding: 8px 16px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-weight: bold;
    }
    #confirmYes {
      background-color: #0f3460;
      color: white;
    }
    #confirmNo {
      background-color: #ddd;
      color: #333;
    }
  </style>
</head>
<body>

<div class="header">
  <div><h1>Admin Panel</h1></div>
  <div class="profile-menu" id="profileMenu">
   <img src="uploads/admin_profile_pics/<?php echo htmlspecialchars($admin_profile_pic); ?>" class="avatar" onclick="toggleProfileDropdown()" />
 
    <div id="profileDropdown" class="dropdown-content">
      <a href="admin_logout.php">Log Out</a>
    </div>
  </div>
</div>

<?php include 'sidebar.php'; ?>

<main id="content" style="padding: 30px;">
  <h2>📷 Media Manager</h2>

  <!-- Upload Form -->
  <form id="mediaUploadForm" enctype="multipart/form-data">
    <input type="file" id="mediaFileInput" name="media_file" required>
    <button type="submit">Upload</button>
  </form>

  <!-- Media Gallery -->
  <div class="media-gallery-container" id="mediaGallery">
    <?php
    if (is_dir($directory)) {
        $files = array_diff(scandir($directory), ['.', '..']);
        foreach ($files as $file) {
            $filePath = $directory . $file;
            $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            echo "<div class='media-card'>";
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                echo "<img src='$filePath' alt='Image' onclick=\"openImageModal('$filePath')\">";
            } elseif ($ext === 'mp4') {
                echo "<video controls><source src='$filePath'></video>";
            } elseif (in_array($ext, ['mp3', 'wav', 'ogg'])) {
                echo "<audio controls><source src='$filePath'></audio>";
            } else {
                echo "<p>Unsupported file</p>";
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

<!-- Image Modal with arrows -->
<div id="imageModal" onclick="closeModal(event)">
  <span class="close-btn" onclick="closeModal(event)">×</span>
  <button id="prevArrow" class="nav-arrow" onclick="showPrevImage(event)">&#8592;</button>
  <img id="modalImage" alt="Preview">
  <button id="nextArrow" class="nav-arrow" onclick="showNextImage(event)">&#8594;</button>
</div>

<!-- Custom Message Box -->
<div id="messageBox"></div>

<!-- Custom Confirm Modal -->
<div id="confirmModal">
  <div id="confirmBox">
    <p id="confirmText">Are you sure?</p>
    <button id="confirmYes">Yes</button>
    <button id="confirmNo">No</button>
  </div>
</div>

<script>
  function toggleProfileDropdown() {
    document.getElementById("profileMenu").classList.toggle("show");
  }
  window.onclick = function(e) {
    if (!e.target.matches('.avatar')) {
      const dropdown = document.getElementById("profileDropdown");
      if (dropdown && dropdown.parentElement.classList.contains("show")) {
        dropdown.parentElement.classList.remove("show");
      }
    }
  };

  // Array of image URLs and current index
  let galleryImages = [];
  let currentIndex = -1;

  // Initialize gallery images from page
  function initGalleryImages() {
    galleryImages = [];
    const imgs = document.querySelectorAll('.media-gallery-container img');
    imgs.forEach(img => galleryImages.push(img.src));
  }

  // Open modal with image and set current index
  function openImageModal(src) {
    if (galleryImages.length === 0) initGalleryImages();

    currentIndex = galleryImages.indexOf(src);
    if (currentIndex === -1) {
      galleryImages.push(src);
      currentIndex = galleryImages.length - 1;
    }

    updateModalImage();
    document.getElementById("imageModal").style.display = "flex";
  }

  // Update modal image src
  function updateModalImage() {
    const modalImage = document.getElementById("modalImage");
    modalImage.src = galleryImages[currentIndex];
  }

  // Show previous image
  function showPrevImage(event) {
    event.stopPropagation();  // prevent modal close
    if (galleryImages.length === 0) return;
    currentIndex = (currentIndex - 1 + galleryImages.length) % galleryImages.length;
    updateModalImage();
  }

  // Show next image
  function showNextImage(event) {
    event.stopPropagation();  // prevent modal close
    if (galleryImages.length === 0) return;
    currentIndex = (currentIndex + 1) % galleryImages.length;
    updateModalImage();
  }

  // Close modal
  function closeModal(event) {
    if (!event || event.target.id === 'imageModal' || event.target.className === 'close-btn') {
      document.getElementById("imageModal").style.display = "none";
      document.getElementById("modalImage").src = '';
    }
  }

  // Custom message box
  function showMessage(msg, duration = 3000) {
    const box = document.getElementById('messageBox');
    box.textContent = msg;
    box.style.display = 'block';
    setTimeout(() => {
      box.style.display = 'none';
    }, duration);
  }

  // Custom confirm dialog with Promise
  function confirmDelete(filename) {
    const modal = document.getElementById('confirmModal');
    const confirmText = document.getElementById('confirmText');
    confirmText.textContent = `Are you sure you want to delete "${filename}"?`;
    modal.style.display = 'flex';

    return new Promise((resolve) => {
      const yesBtn = document.getElementById('confirmYes');
      const noBtn = document.getElementById('confirmNo');

      function cleanup() {
        yesBtn.removeEventListener('click', onYes);
        noBtn.removeEventListener('click', onNo);
        modal.style.display = 'none';
      }

      function onYes() {
        cleanup();
        deleteMedia(filename);
        resolve(true);
      }
      function onNo() {
        cleanup();
        resolve(false);
      }

      yesBtn.addEventListener('click', onYes);
      noBtn.addEventListener('click', onNo);
    });
  }

  // Upload media
  document.getElementById("mediaUploadForm").addEventListener("submit", function(e) {
    e.preventDefault();
    const fileInput = document.getElementById("mediaFileInput");
    if (fileInput.files.length === 0) {
      showMessage("Please select a file to upload.");
      return;
    }
    const formData = new FormData();
    formData.append("media_file", fileInput.files[0]);

    fetch("upload_media.php", {
      method: "POST",
      body: formData
    })
    .then(res => res.text())
    .then(msg => {
      showMessage(msg);
      if (!msg.toLowerCase().includes("error")) {
        // Reload gallery after short delay
        setTimeout(() => location.reload(), 1200);
      }
    })
    .catch(() => {
      showMessage("Upload failed. Please try again.");
    });
  });

  // Delete media file
  function deleteMedia(filename) {
    const formData = new FormData();
    formData.append("filename", filename);
    fetch("delete_media.php", {
      method: "POST",
      body: formData
    })
    .then(res => res.text())
    .then(msg => {
      showMessage(msg);
      if (!msg.toLowerCase().includes("error")) {
        setTimeout(() => location.reload(), 1200);
      }
    })
    .catch(() => {
      showMessage("Deletion failed. Please try again.");
    });
  }
</script>
<script src="js/admin_dashboard.js"></script>
<!-- Footer -->
<div class="footer">
  © 2025 Student Dashboard System. All rights reserved.
</div>
</body>
</html>
