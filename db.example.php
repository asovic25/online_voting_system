<?php

$conn = mysqli_connect(
    "localhost",
    "YOUR_DATABASE_USERNAME",
    "YOUR_DATABASE_PASSWORD",
    "online_voting_system"
);

if (!$conn) {
    die("Database connection failed.");
}
?>