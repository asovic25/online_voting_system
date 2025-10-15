<?php
$conn = mysqli_connect("localhost", "root", "", "online_voting_system");

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>