<?php
session_start();
require './vendor/autoload.php';
require 'C:\Program Files\Ampps\www\Snapshot\Includes\db.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = ''; // ستُستخدم لعرض الرسائل للمستخدم

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["username"])) {
    $username = trim($_POST["username"]);

    // التحقق من وجود المستخدم
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        $record = mysqli_fetch_assoc($result);
        $email = $record["email"];

       
        $token = bin2hex(random_bytes(32));
        $tokenExpiry = date("Y-m-d H:i:s", strtotime("+1 hour"));

        
        $updateSql = "UPDATE users SET reset_token = ?, token_status = 1, token_expiry = ? WHERE username = ?";
        $updateStmt = mysqli_prepare($conn, $updateSql);
        mysqli_stmt_bind_param($updateStmt, "sss", $token, $tokenExpiry, $username);
        mysqli_stmt_execute($updateStmt);

        // إرسال البريد
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'ware43193@gmail.com';       // ✏️ ضع إيميلك هنا
            $mail->Password = 'ykcm edrt kgxi xbie';       // ✏️ استخدم "كلمة مرور التطبيقات" هنا
            $mail->SMTPSecure = 'tls';                     // أو 'ssl' إذا استخدمت المنفذ 465
            $mail->Port = 587;

            $mail->setFrom('ware43193@gmail.com', 'Support');
            $mail->addAddress($email);

            $mail->Subject = 'Reset Your Password';
            $mail->Body = "Hi $username,\n\nClick the link below to reset your password:\n\nhttp://localhost/SS_modified/Snapshot/reset.php?token=$token\n\nIf you didn’t request this, you can ignore the message.";

            $mail->send();
            $message = "A password reset link has been sent to your email."; // رسالة النجاح
        } catch (Exception $e) {
            $message = "Failed to send email. Error: {$mail->ErrorInfo}"; // رسالة الخطأ
        }

    } else {
        $message = "Username not found."; // رسالة عند عدم وجود المستخدم
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Forget Password</title>
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

    .login-container input[type="text"] {
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
    <h2>Forget Password</h2>
    
    <!-- عرض الرسائل بناءً على الحالة -->
    <?php if (!empty($message)): ?>
      <div class="<?= strpos($message, 'error') !== false ? 'error' : 'success' ?>">
        <?= htmlspecialchars($message) ?>
      </div>
    <?php endif; ?>

    <form action="forget.php" method="POST">
      <input type="text" name="username" placeholder="Enter your username" required>
      <button type="submit">Send Reset Link</button>
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
