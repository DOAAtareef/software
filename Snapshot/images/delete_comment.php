<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_id'])) {
    $stmt = mysqli_prepare($conn, "DELETE FROM comments WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $_POST['comment_id']);
    mysqli_stmt_execute($stmt);
}

header("Location: " . $_SERVER['HTTP_REFERER']);
?>