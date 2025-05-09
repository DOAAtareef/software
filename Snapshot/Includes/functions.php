<?php
require_once 'db.php';

function getImages() {
    global $conn;
    $query = "SELECT images.*, users.username FROM images JOIN users ON images.user_id = users.id ORDER BY uploaded_at DESC";
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function getComments($imageId) {
    global $conn;
    $stmt = mysqli_prepare($conn, "SELECT comments.*, users.username FROM comments JOIN users ON comments.user_id = users.id WHERE image_id = ? ORDER BY created_at DESC");
    mysqli_stmt_bind_param($stmt, "i", $imageId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function getLikesCount($imageId) {
    global $conn;
    $stmt = mysqli_prepare($conn, "SELECT COUNT(*) as count FROM likes WHERE image_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $imageId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result)['count'];
}

function hasLiked($userId, $imageId) {
    global $conn;
    $stmt = mysqli_prepare($conn, "SELECT id FROM likes WHERE user_id = ? AND image_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $userId, $imageId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    return mysqli_stmt_num_rows($stmt) > 0;
}
?>