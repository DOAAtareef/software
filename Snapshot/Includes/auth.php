<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: ../login.php");
        exit();
    }
}

function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header("Location: ../user/dashboard.php");
        exit();
    }
}
?>