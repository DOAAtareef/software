<?php
session_start();
require "C:\Program Files\Ampps\www\Snapshot\Includes\db.php";

if ($_SESSION["reg"] == 1) {
    require_once(__DIR__ . '/vendor/autoload.php');
    $google2fa = new PragmaRX\Google2FA\Google2FA();

    $secretKey = $google2fa->generateSecretKey();
    $username = $_SESSION['username'];
    $query = "UPDATE users SET secret_key=? WHERE username=?";

    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ss", $secretKey, $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    $_SESSION['reg'] = 0;

    $displayMessage = "Please enter the following secret into your phone's 2FA app to complete the setup.";

    // استخدمي Google Charts لتوليد QR
    $qrCodeData = $google2fa->getQRCodeUrl('My Snapshot Platform', $username, $secretKey);
    $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=' . urlencode($qrCodeData);

} else {
    header("location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2FA Setup - My Snapshot Platform</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
    <style>
html, body {
    height: 100%;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center; /* محاذاة المحتوى في المنتصف */
    align-items: center; /* محاذاة المحتوى عموديًا */
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: url('https://i.etsystatic.com/12346933/r/il/597c3d/1293789843/il_1080xN.1293789843_2ybp.jpg') no-repeat center center fixed;
    background-size: cover;
    color: #5c2a4a;
}

header {
    background-color: rgba(255, 192, 203, 0.8);
    color: #5c2a4a;
    padding: 1rem;
    text-align: center;
    border-bottom: 2px solid #e0a3b0;
}

header h1 {
    font-size: 2.5rem;
    margin: 0;
    color: #5c2a4a;
}
h2 {
     text-align: center;
     margin-bottom: 2rem;
     color: #e0a3b0;
        }
header p {
    font-size: 1.2rem;
}

.content {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
}

.container {
    background:  #5c2a4a;
    padding: 2rem;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    border-radius: 20px;
    backdrop-filter: blur(5px);
    width: 100%;
    text-align: center;
}

.qr-code img {
    width: 250px;
    height: 250px;
}

footer {
    background-color: rgba(255, 192, 203, 0.8);
    color: #5c2a4a;
    text-align: center;
    padding: 1rem;
    margin-top: 2rem;
    border-top: 1px solid #e0a3b0;
}
    </style>
</head>
<body>

<header>
    <h1>📸 My Snapshot Platform</h1>
    <p>Set up Two-Factor Authentication</p>
</header>

<div class="content">
    <div class="container">
        <h2>2FA Setup</h2>
        <p><?= $displayMessage ?></p>

        <div class="qr-code">
            <p>Scan this QR code with your 2FA app:</p>
            <img src="<?= $qrCodeUrl ?>" alt="QR Code for 2FA">
        </div>

        <p>Once you've added the secret to your 2FA app, <a href="login.php">login to your account</a>.</p>
    </div>
</div>

<footer>
    &copy; 2025 My Snapshot Platform. All rights reserved.
</footer>

</body>
</html>
