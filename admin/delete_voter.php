<?php
session_start();
include('../db.php');

if(!isset($_SESSION['admin_id'])){
    header("Location: admin_login.php");
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])){
    $id = intval($_POST['id']);

    // Optional: Delete associated files if needed
    $fileQuery = $conn->prepare("SELECT passport FROM voters WHERE id = ?");
    $fileQuery->bind_param("i",$id);
    $fileQuery->execute();
    $result = $fileQuery->get_result();
    if($row = $result->fetch_assoc()){
        if(file_exists('../'.$row['passport'])){
            unlink('../'.$row['passport']);
        }
    }

    // Delete voter
    $stmt = $conn->prepare("DELETE FROM voters WHERE id=?");
    $stmt->bind_param("i",$id);
    if($stmt->execute()){
        $_SESSION['success'] = "Voter deleted successfully.";
    } else {
        $_SESSION['error'] = "Error deleting voter: ".$conn->error;
    }
}
header("Location: admin_dashboard.php");
exit();
