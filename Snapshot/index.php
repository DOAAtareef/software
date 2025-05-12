<?php
session_start();
require 'C:\Program Files\Ampps\www\Snapshot\Includes\db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

?>
