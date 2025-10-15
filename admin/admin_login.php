<?php
session_start();
include('../db.php');

function safeGet($key){
    return isset($_POST[$key]) ? trim($_POST[$key]) : '';
}

$error = "";

if($_SERVER['REQUEST_METHOD'] == "POST"){
    $username = safeGet('username');
    $password = safeGet('password');

    $stmt = $conn->prepare("SELECT * FROM admins WHERE username=?");
    $stmt->bind_param("s",$username);
    $stmt->execute();
    $result = $stmt->get_result();
    if($row = $result->fetch_assoc()){
        if(password_verify($password,$row['password'])){
            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['admin_name'] = $row['fullname'];
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "Username not found!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="style.css?v=1">
</head>
<body>
<div class="container mt-5 pt-5">
  <div class="card shadow-lg mx-auto" style="max-width: 500px;">
    <div class="card-header text-center bg-primary text-white">
      <h3>Admin Login</h3>
    </div>
    <div class="card-body">
      <?php if($error) echo "<div class='alert alert-danger'>$error</div>"; ?>
      <form method="POST">
        <div class="mb-3">
          <label>Username</label>
          <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Password</label>
          <input type="password" name="password" class="form-control" required>
        </div>
        <div class="text-center">
          <button type="submit" class="btn btn-primary px-5">Login</button>
        </div>
        <p class="mt-3 text-center">Don't have an account? <a href="admin_signup.php">Signup</a></p>
      </form>
    </div>
  </div>
</div>
</body>
</html>
