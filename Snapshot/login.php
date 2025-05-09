<?php
session_start();
require 'C:\Program Files\Ampps\www\Snapshot\Includes\db.php';

// Redirect already logged-in and verified users
if (
    isset($_SESSION['verified']) && $_SESSION['verified'] === true &&
    isset($_SESSION['2Floggedin']) && $_SESSION['2Floggedin'] === true
) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: user/dashboard.php");
    }
    exit();
}

// Initialize failed attempts count
if (!isset($_SESSION['failed_attempts'])) {
    $_SESSION['failed_attempts'] = 0;
}

// Lockout after 3 failed attempts
if ($_SESSION['failed_attempts'] >= 3) {
    if (!isset($_SESSION['lockout_time'])) {
        $_SESSION['lockout_time'] = time() + 180; // lock for 3 minutes
    }

    if (time() < $_SESSION['lockout_time']) {
        $remaining = $_SESSION['lockout_time'] - time();
        $minutes = floor($remaining / 60);
        $seconds = $remaining % 60;
        $error = "Too many failed login attempts. Please try again in {$minutes} minutes and {$seconds} seconds.";
    } else {
        $_SESSION['failed_attempts'] = 0;
        unset($_SESSION['lockout_time']);
    }
}

if (
    isset($_POST["username"], $_POST["password"], $_POST['g-recaptcha-response']) &&
    $_SESSION['failed_attempts'] < 3
) {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $recaptchaSecret = "6Lfd0Z4pAAAAAAYveHV58d0aaBGBmOQQruKXPKgP";
    $recaptchaResponse = $_POST['g-recaptcha-response'];

    // Verify reCAPTCHA
    $verifyResponse = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$recaptchaSecret&response=$recaptchaResponse");
    $responseData = json_decode($verifyResponse);

    if ($responseData->success) {
        $sql = "SELECT id, username, password, email, role, secret_key FROM users WHERE username = ?";

        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);

            if (mysqli_stmt_num_rows($stmt) == 1) {
                mysqli_stmt_bind_result($stmt, $id, $username_db, $hash_password, $email, $role, $secret_key);

                if (mysqli_stmt_fetch($stmt)) {
                    if (password_verify($password, $hash_password)) {
                        session_regenerate_id(true); // Protect from session fixation
                        $_SESSION['verified'] = true;
                        $_SESSION['username'] = $username_db;
                        $_SESSION['email'] = $email;
                        $_SESSION['role'] = $role;
                        $_SESSION['SecretCode'] = $secret_key;
                        $_SESSION['user_id'] = $id;
                        $_SESSION['failed_attempts'] = 0;
                        unset($_SESSION['lockout_time']);
                        header("Location: verify2fa.php");
                        exit();
                    } else {
                        $_SESSION['failed_attempts']++;
                        $error = "Incorrect password.";
                    }
                }
            } else {
                $_SESSION['failed_attempts']++;
                $error = "Invalid username or password.";
            }
            mysqli_stmt_close($stmt);
        }
    } else {
        $error = "Please complete the reCAPTCHA.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login - My Snapshot Platform</title>
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
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

  .login-container input[type="text"],
  .login-container input[type="password"] {
    width: 100%;
    padding: 0.8rem;
    margin-bottom: 1rem;
    border: 1px solid #f8bbd0;
    border-radius: 12px;
    background-color: #fff0f6;
  }

  .g-recaptcha {
    margin: 1rem 0;
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

  .error {
    color: red;
    text-align: center;
    margin-bottom: 1rem;
  }

  footer {
    background-color: rgba(255, 192, 203, 0.8);
    color: #5c2a4a;
    text-align: center;
    padding: 1rem;
    margin-top: 0rem;
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
    <h2>Login to Your Account</h2>

    <?php if (isset($error)): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form action="login.php" method="POST">
      <input type="text" name="username" placeholder="Username" required>
      <input type="password" name="password" placeholder="Password" required>
      <div class="g-recaptcha" data-sitekey="6Lfd0Z4pAAAAAIaohtH-z-QLQRA3oYPsNyI0MS1y"></div>
      <button type="submit">Login</button>
    </form>

    <div class="signup-link">
      <p>Don't have an account? <a href="signup.php">Sign up</a></p>
      <p>Forgot password? <a href="forget.php">Reset it</a></p>
    </div>
  </div>

  <footer>
    &copy; 2025 My Snapshot Platform. All rights reserved.
  </footer>
</body>
</html>
