<?php
require "db.php";

$query = "SELECT * FROM users";
$result = mysqli_query($conn, $query);

if ($result) {
    echo "Database connection is working!";
} else {
    echo "Error: " . mysqli_error($conn);
}
?>