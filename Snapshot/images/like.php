<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['image_id'])) {
    if (hasLiked($_SESSION['user_id'], $_POST['image_id'])) {
        // Unlike
        $stmt = mysqli_prepare($conn, "DELETE FROM likes WHERE user_id = ? AND image_id = ?");
    } else {
        // Like
        $stmt = mysqli_prepare($conn, "INSERT INTO likes (user_id, image_id) VALUES (?, ?)");
    }
    mysqli_stmt_bind_param($stmt, "ii", $_SESSION['user_id'], $_POST['image_id']);
    mysqli_stmt_execute($stmt);
}

header("Location: " . $_SERVER['HTTP_REFERER']);
?>