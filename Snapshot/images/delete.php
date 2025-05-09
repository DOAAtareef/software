<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['image_id'])) {
    // احصل على مسار الصورة أولاً
    $stmt = mysqli_prepare($conn, "SELECT path FROM images WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $_POST['image_id']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $image = mysqli_fetch_assoc($result);
    
    if ($image) {
        // احذف الصورة من المجلد
        $filepath = '../uploads/' . $image['path'];
        if (file_exists($filepath)) {
            unlink($filepath);
        }
        
        // احذف السجلات المرتبطة
        mysqli_begin_transaction($conn);
        try {
            mysqli_query($conn, "DELETE FROM likes WHERE image_id = " . $_POST['image_id']);
            mysqli_query($conn, "DELETE FROM comments WHERE image_id = " . $_POST['image_id']);
            mysqli_query($conn, "DELETE FROM images WHERE id = " . $_POST['image_id']);
            mysqli_commit($conn);
        } catch (Exception $e) {
            mysqli_rollback($conn);
        }
    }
}

header("Location: ../admin/dashboard.php");
?>