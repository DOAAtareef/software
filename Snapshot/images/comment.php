<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['image_id'], $_POST['content'])) {
    $stmt = mysqli_prepare($conn, "INSERT INTO comments (user_id, image_id, content) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "iis", $_SESSION['user_id'], $_POST['image_id'], $_POST['content']);
    mysqli_stmt_execute($stmt);
}

header("Location: " . $_SERVER['HTTP_REFERER']);
?>