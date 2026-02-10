<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Welcome to Student Dashboard System</title>
  <style>
    body {
      margin: 0;
      font-family: "Segoe UI", sans-serif;
      background: linear-gradient(to right, #e3f2fd, #f5f7fa);
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      height: 100vh;
      color: #0f3460;
    }

    h1 {
      font-size: 42px;
      margin-bottom: 10px;
    }

    p {
      font-size: 20px;
      margin-bottom: 30px;
    }

    .login-options {
      display: flex;
      gap: 30px;
    }

    .login-options a {
      text-decoration: none;
      padding: 14px 28px;
      background-color: #0f3460;
      color: white;
      border-radius: 8px;
      font-size: 18px;
      font-weight: 500;
      transition: 0.3s ease;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .login-options a:hover {
      background-color: #1f4c87;
      transform: translateY(-3px);
    }

    .footer {
      position: fixed;
      bottom: 15px;
      width: 100%;
      text-align: center;
      font-size: 14px;
      color: #666;
    }

    @media (max-width: 600px) {
      .login-options {
        flex-direction: column;
        gap: 20px;
      }
    }
  </style>
</head>
<body>

  <h1>📘 Student Dashboard System</h1>
  <p>Please choose your login type:</p>

  <div class="login-options">
    <a href="admin/admin_login.html">Admin Login</a>
    <a href="user/user_login.html">Student Login</a>
  </div>

  <div class="footer">
    &copy; 2025 Student Dashboard System. All rights reserved.
  </div>

</body>
</html>
