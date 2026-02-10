<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        echo "<script>alert('Please enter both email and password.'); window.history.back();</script>";
        exit();
    }

    $conn = new mysqli("localhost", "root", "", "registration_db");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT * FROM regform WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {

            // ✅ Admin
            if ($user['role'] === 'admin') {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_username'] = $user['username'];
                $_SESSION['admin_email'] = $user['email'];
                $_SESSION['admin_profile_pic'] = $user['profile_pic'] ?? 'profile.png';

                header("Location: admin_dashboard_home.php");
                exit();

            // ✅ User added by admin
            } elseif ($user['role'] === 'user' && !empty($user['added_by'])) {
                $_SESSION['admin_logged_in'] = true; // treat them like admin
                $_SESSION['admin_username'] = $user['username'];
                $_SESSION['admin_email'] = $user['email'];
                $_SESSION['admin_profile_pic'] = $user['profile_pic'] ?? 'profile.png';

                header("Location: admin_dashboard_home.php");
                exit();

            // ❌ Normal registered user
            } else {
                echo "<script>alert('Access denied for this user.'); window.history.back();</script>";
                exit();
            }

        } else {
            echo "<script>alert('Incorrect password.'); window.history.back();</script>";
            exit();
        }

    } else {
        echo "<script>alert('No user found with this email.'); window.history.back();</script>";
        exit();
    }

    $stmt->close();
    $conn->close();

} else {
    header("Location: login.html");
    exit();
}
?>
