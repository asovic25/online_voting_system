<?php
session_start();
include('../db.php');

function safeGet($key) {
    return isset($_POST[$key]) ? htmlspecialchars(trim($_POST[$key])) : '';
}

$error = "";
$success = "";

if($_SERVER['REQUEST_METHOD'] == "POST") {
    $fullname = safeGet('fullname');
    $username = safeGet('username');
    $email = safeGet('email');
    $password = safeGet('password');
    $confirm_password = safeGet('confirm_password');

    if($password !== $confirm_password){
        $error = "Passwords do not match!";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // Check existing username or email
        $stmt = $conn->prepare("SELECT id FROM admins WHERE username=? OR email=?");
        $stmt->bind_param("ss",$username,$email);
        $stmt->execute();
        $stmt->store_result();
        if($stmt->num_rows > 0){
            $error = "Username or Email already exists!";
        } else {
            $insert = $conn->prepare("INSERT INTO admins (fullname, username, email, password) VALUES (?,?,?,?)");
            $insert->bind_param("ssss",$fullname,$username,$email,$hash);
            if($insert->execute()){
                $success = "Admin account created successfully! <a href='admin_login.php'>Login Now</a>";
            } else {
                $error = "Error: ".$conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Signup</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="style.css?v=1">
</head>
<body>
<div class="container mt-5 pt-5">
  <div class="card shadow-lg mx-auto" style="max-width: 500px;">
    <div class="card-header text-center bg-primary text-white">
      <h3>Admin Signup</h3>
    </div>
    <div class="card-body">
      <?php if($error) echo "<div class='alert alert-danger'>$error</div>"; ?>
      <?php if($success) echo "<div class='alert alert-success'>$success</div>"; ?>
      <form method="POST">
        <div class="mb-3">
          <label>Full Name</label>
          <input type="text" name="fullname" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Username</label>
          <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Email</label>
          <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Password</label>
          <input type="password" name="password" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Confirm Password</label>
          <input type="password" name="confirm_password" class="form-control" required>
        </div>
        <div class="text-center">
          <button type="submit" class="btn btn-primary px-5">Signup</button>
        </div>
      </form>
    </div>
  </div>
</div>
</body>
</html>
