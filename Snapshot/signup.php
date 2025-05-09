<?php
session_start();
require 'C:\Program Files\Ampps\www\Snapshot\Includes\db.php';
require_once(__DIR__ . '/vendor/autoload.php');

$error = "";
if (isset($_POST["username"])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if username or email already exists
    $check_query = "SELECT * FROM users WHERE username = ? OR email = ?";
    $check_stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($check_stmt, "ss", $username, $email);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_store_result($check_stmt);

    if (mysqli_stmt_num_rows($check_stmt) > 0) {
        $error = "Username or email is already taken. Please choose another.";
    } else {
        // Password validation
        if (strlen($password) < 8) {
            $error = "Please make the password at least 8 characters long.";
        } elseif (!preg_match('/[A-Z]+/', $password)) {
            $error = "Please include at least one uppercase letter in the password.";
        } elseif (!preg_match('/[a-z]+/', $password)) {
            $error = "Please include at least one lowercase letter in the password.";
        } elseif (!preg_match('/[0-9]+/', $password)) {
            $error = "Please include at least one digit in the password.";
        } elseif (!preg_match('/[^A-Za-z0-9]+/', $password)) {
            $error = "Please include at least one special character in the password.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $query = "INSERT INTO users (username, password, email, role, token_status) VALUES (?, ?, ?, '1', 0)";
            $stmt = mysqli_prepare($conn, $query);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "sss", $username, $hashed_password, $email);
                if (mysqli_stmt_execute($stmt)) {
                    $_SESSION['reg'] = 1;
                    $_SESSION['username'] = $username;
                    header("Location: 2FA.php");
                    exit();
                } else {
                    $error = "Cannot create user: " . mysqli_error($conn);
                }
                mysqli_stmt_close($stmt);
            }
        }
    }
    mysqli_stmt_close($check_stmt);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Signup - My Snapshot Platform</title>
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

    .signup-container {
      max-width: 400px;
      margin: 3rem auto;
      background: rgba(255, 255, 255, 0.95);
      padding: 2rem;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
      border-radius: 20px;
      backdrop-filter: blur(5px);
    }

    .signup-container h2 {
      text-align: center;
      margin-bottom: 1.5rem;
      color: #c2185b;
    }

    .signup-container input[type="text"],
    .signup-container input[type="password"] {
      width: 100%;
      padding: 0.8rem;
      margin-bottom: 1rem;
      border: 1px solid #f8bbd0;
      border-radius: 12px;
      background-color: #fff0f6;
    }

    .signup-container button {
      width: 100%;
      padding: 0.8rem;
      background-color: #ec407a;
      color: white;
      border: none;
      border-radius: 12px;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    .signup-container button:hover {
      background-color: #d81b60;
    }

    .login-link {
      text-align: center;
      margin-top: 1rem;
    }

    .login-link a {
      color: #ad1457;
      text-decoration: none;
      font-weight: bold;
    }

    .login-link a:hover {
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
      margin-top: 4rem;
      border-top: 1px solid #e0a3b0;
    }
  </style>
</head>
<body>
  <header>
    <h1>📸 My Snapshot Platform</h1>
    <p>Share your best moments with others</p>
  </header>

  <div class="signup-container">
    <h2>Create Your Account</h2>

    <?php if (!empty($error)): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" action="signup.php">
      <input type="text" name="username" placeholder="Username" required>
      <input type="text" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Sign Up</button>
    </form>

    <div class="login-link">
      <p>Already have an account? <a href="login.php">Log in</a></p>
    </div>
  </div>

  <footer>
    &copy; 2025 My Snapshot Platform. All rights reserved.
  </footer>
</body>
</html>
