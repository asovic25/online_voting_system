<?php
session_start();
include('../db.php');

if(!isset($_SESSION['admin_id'])){
    header("Location: admin_login.php");
    exit();
}

if($_SERVER['REQUEST_METHOD'] == "POST"){
    $id = $_POST['id'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $party = trim($_POST['party']);

    $stmt = $conn->prepare("UPDATE contestants SET name=?, email=?, phone=?, party=? WHERE id=?");
    $stmt->bind_param("ssssi", $name, $email, $phone, $party, $id);
    if($stmt->execute()){
        header("Location: admin_dashboard.php");
    } else {
        echo "Error updating contestant: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Voter | Admin Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../style.css?v=12">
</head>
<body>
<div class="container mt-5 pt-5">
    <h3 class="mb-4 text-primary">Edit Voter</h3>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST" class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Full Name</label>
            <input type="text" name="name" class="form-control" value="<?= safeGet($voter, ['fullname','name','full_name','username']) ?>" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?= safeGet($voter, ['email','mail']) ?>" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control" value="<?= safeGet($voter, ['phone','phone_number','tel']) ?>" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">State</label>
            <input type="text" name="state" class="form-control" value="<?= safeGet($voter, ['state','residence_state','state_of_residence']) ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label">LGA</label>
            <input type="text" name="lga" class="form-control" value="<?= safeGet($voter, ['lga','local_government','localgov']) ?>">
        </div>
        <div class="col-12 text-center mt-3">
            <button type="submit" class="btn btn-primary">Update Voter</button>
            <a href="admin_dashboard.php" class="btn btn-secondary">Back</a>
        </div>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
