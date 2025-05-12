<?php
session_start();

//echo "<pre>"; print_r($_SESSION); echo "</pre>";

if (isset($_POST['code'])) {
    require_once(__DIR__ . '/vendor/autoload.php');

    
    if (isset($_SESSION["verified"]) && $_SESSION["verified"] === true && !isset($_SESSION["2Floggedin"])) {
        $google2fa = new PragmaRX\Google2FA\Google2FA();
        $code = $_POST['code'];
        $userSecret = $_SESSION['SecretCode'];

        if (!empty($userSecret)) {
            $valid = $google2fa->verifyKey($userSecret, $code);

            if ($valid) {
                require 'C:\Program Files\Ampps\www\Snapshot\Includes\db.php';

                try {
                    $stmt = $pdo->prepare("SELECT role FROM users WHERE username = ?");
                    $stmt->execute([$_SESSION["username"]]);
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($user) {
                        $_SESSION['role'] = $user['role'];
                        $_SESSION["loggedin"] = true;
                        $_SESSION["2Floggedin"] = true;

                        if ($_SESSION['role'] === 'admin') {
                            header("Location: admin/dashboard.php");
                        } else {
                            header("Location: user/dashboard.php");
                        }
                        exit();
                    } else {
                        $login_err = "User role not found in database.";
                    }
                } catch (PDOException $e) {
                    $login_err = "Database error: " . $e->getMessage();
                }
            } else {
                $login_err = "Authentication code is incorrect.";
            }
        } else {
            $login_err = "Authentication secret key is missing.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Two-Factor Authentication</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url('https://i.etsystatic.com/12346933/r/il/597c3d/1293789843/il_1080xN.1293789843_2ybp.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #5c2a4a;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        header {
            background-color: rgba(255, 192, 203, 0.8);
            color: #5c2a4a;
            padding: 1rem;
            text-align: center;
            border-bottom: 2px solid #e0a3b0;
        }

        .container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 1rem;
        }

        .wrapper {
            background: rgba(255, 255, 255, 0.95);
            padding: 2rem;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            border-radius: 20px;
            width: 100%;
            max-width: 400px;
            backdrop-filter: blur(5px);
        }

        h2 {
            text-align: center;
            margin-bottom: 1.5rem;
            color: #c2185b;
        }

        p {
            text-align: center;
            font-size: 0.95rem;
            margin-bottom: 1.5rem;
        }

        .alert {
            background-color: #ffe0e0;
            color: #b30000;
            padding: 1rem;
            border-radius: 12px;
            font-weight: 500;
            margin-bottom: 1rem;
            text-align: center;
        }

        label {
            font-weight: 600;
            display: block;
            margin-bottom: 0.5rem;
        }

        input[type="text"] {
            width: 100%;
            padding: 0.8rem;
            margin-bottom: 1rem;
            border: 1px solid #f8bbd0;
            border-radius: 12px;
            background-color: #fff0f6;
        }

        button {
            width: 100%;
            padding: 0.8rem;
            background-color: #ec407a;
            color: white;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #d81b60;
        }

        footer {
            background-color: rgba(255, 192, 203, 0.8);
            color: #5c2a4a;
            text-align: center;
            padding: 1rem;
            border-top: 2px solid #e0a3b0;
        }
    </style>
</head>
<body>
<header>
  <h2>📸 My Snapshot Platform</h2>
  <p>Share your best moments with others</p>
</header>

<div class="container">
    <div class="wrapper">
        <h2>2FA Verification</h2>
        <p>Enter the code from your Google Authenticator app to continue.</p>

        <?php if (!empty($login_err)) : ?>
            <div class="alert"><?= htmlspecialchars($login_err) ?></div>
        <?php endif; ?>

        <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post">
            <label for="code">Authentication Code</label>
            <input type="text" name="code" id="code" required>

            <button type="submit">Verify</button>
        </form>
    </div>
</div>

<footer>
    &copy; 2025 My Snapshot Platform. All rights reserved.
</footer>
</body>
</html>
