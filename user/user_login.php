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

            // ✅ Only allow normal registered users (not added_by someone)
            if ($user['role'] === 'user' && empty($user['added_by'])) {
                $_SESSION['user_logged_in'] = true;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_username'] = $user['username'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_profile_pic'] = $user['profile_pic'] ?? 'profile.png';
                  $_SESSION['user_dept_id'] = $user['dept_id']; 

                header("Location: user_home.php");
                exit();
            } else {
                echo "<script>alert('Only registered users can log in here.'); window.history.back();</script>";
                exit();
            }

        } else {
            echo "<script>alert('Incorrect password.'); window.history.back();</script>";
            exit();
        }

    } else {
        echo "<script>
            if (confirm('No user found with this email. Do you want to register?')) {
                window.location.href = 'register.html';
            } else {
                window.history.back();
            }
        </script>";
        exit();
    }

    $stmt->close();
    $conn->close();

} else {
    header("Location: user_login.html");
    exit();
}
?>
