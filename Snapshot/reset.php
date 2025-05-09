<?php
session_start();
require './vendor/autoload.php';
require 'C:\Program Files\Ampps\www\Snapshot\Includes\db.php';

$token = "";

if (isset($_GET['token'])) {
    $token = $_GET['token'];
} elseif (isset($_POST['token'])) {
    $token = $_POST['token'];
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($token) && isset($_POST["password"])) {
    $newPassword = $_POST["password"];

    $sql = "SELECT * FROM users WHERE reset_token = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $token);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        $record = mysqli_fetch_assoc($result);
        $tokenExpiry = $record["token_expiry"];
        $tokenStatus = $record["token_status"];

        if ($tokenStatus == 1 && strtotime($tokenExpiry) > time()) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            $updateSql = "UPDATE users SET password = ?, reset_token = NULL, token_expiry = NULL, token_status = 0 WHERE reset_token = ?";
            $updateStmt = mysqli_prepare($conn, $updateSql);
            mysqli_stmt_bind_param($updateStmt, "ss", $hashedPassword, $token);
            mysqli_stmt_execute($updateStmt);

            $message = "✔ Your password has been successfully reset.";
        } else {
            $message = "✘ The token is either expired or invalid.";
        }
    } else {
        $message = "✘ Invalid token.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reset Password</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: url('https://i.etsystatic.com/12346933/r/il/597c3d/1293789843/il_1080xN.1293789843_2ybp.jpg') no-repeat center center fixed;
      background-size: cover;
      color: #5c2a4a;
      margin: 0;
      padding: 0;
    }

    header {
      background-color: rgba(255, 192, 203, 0.8);
      color: #5c2a4a;
      padding: 1rem;
      text-align: center;
      border-bottom: 2px solid #e0a3b0;
    }

    .login-container {
      max-width: 400px;
      margin: 3rem auto;
      background: rgba(255, 255, 255, 0.95);
      padding: 2rem;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
      border-radius: 20px;
      backdrop-filter: blur(5px);
    }

    .login-container h2 {
      text-align: center;
      margin-bottom: 1.5rem;
      color: #c2185b;
    }

    .login-container input[type="password"] {
      width: 100%;
      padding: 0.8rem;
      margin-bottom: 1rem;
      border: 1px solid #f8bbd0;
      border-radius: 12px;
      background-color: #fff0f6;
    }

    .login-container button {
      width: 100%;
      padding: 0.8rem;
      background-color: #ec407a;
      color: white;
      border: none;
      border-radius: 12px;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    .login-container button:hover {
      background-color: #d81b60;
    }

    .signup-link {
      text-align: center;
      margin-top: 1rem;
    }

    .signup-link a {
      color: #ad1457;
      text-decoration: none;
      font-weight: bold;
    }

    .signup-link a:hover {
      text-decoration: underline;
    }

    .error, .success {
      padding: 1rem;
      margin-bottom: 1rem;
      border-radius: 12px;
      text-align: center;
      font-weight: bold;
    }

    .error {
      background-color: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }

    .success {
      background-color: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }

    footer {
      background-color: rgba(255, 192, 203, 0.8);
      color: #5c2a4a;
      text-align: center;
      padding: 1rem;
      margin-top: 11rem;
      border-top: 1px solid #e0a3b0;
    }
  </style>
</head>
<body>
  <header>
    <h1>📸 My Snapshot Platform</h1>
    <p>Share your best moments with others</p>
  </header>

  <div class="login-container">
    <h2>Reset Password</h2>

    <?php if (!empty($message)): ?>
      <div class="<?= strpos($message, '✔') !== false ? 'success' : 'error' ?>">
        <?= htmlspecialchars($message) ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="reset.php">
      <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
      <input type="password" name="password" required placeholder="New Password">
      <button type="submit">Reset Password</button>
    </form>

    <div class="signup-link">
      <p>Remembered your password? <a href="login.php">Login</a></p>
    </div>
  </div>

  <footer>
    &copy; 2025 My Snapshot Platform. All rights reserved.
  </footer>
</body>
</html>
