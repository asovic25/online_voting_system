<?php
session_start();
include('../db.php');

if(!isset($_SESSION['admin_id'])){
    header("Location: admin_login.php");
    exit();
}

$admin_name = $_SESSION['admin_name'];

// Safe get function
function safeGet($arr,$key){
    return isset($arr[$key]) ? htmlspecialchars($arr[$key]) : '';
}

// Fetch voters
$voters = $conn->query("SELECT * FROM voters ORDER BY id DESC");

// Fetch contestants
$contestants = $conn->query("SELECT * FROM contestants ORDER BY id DESC");

// Handle session messages
$success = isset($_SESSION['success']) ? $_SESSION['success'] : '';
$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
unset($_SESSION['success'], $_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="style.css?v=2">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Admin Dashboard</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navmenu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navmenu">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link text-white" href="../index.php">Visit Main Site</a></li>
        <li class="nav-item"><span class="nav-link text-white">Hello, <?= htmlspecialchars($admin_name) ?></span></li>
        <li class="nav-item"><a class="nav-link text-white" href="admin_logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>
<div class="container mt-5 pt-5">
  <?php if($success) echo "<div class='alert alert-success'>$success</div>"; ?>
  <?php if($error) echo "<div class='alert alert-danger'>$error</div>"; ?>
  <h2 class="mb-3">Voters</h2>
  <div class="table-responsive">
  <table class="table table-striped table-bordered">
    <thead class="table-primary">
      <tr>
        <th>ID</th>
        <th>Full Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Voter ID</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php while($row=$voters->fetch_assoc()): ?>
      <tr>
        <td><?= safeGet($row,'id') ?></td>
        <td><?= safeGet($row,'name') ?></td>
        <td><?= safeGet($row,'email') ?></td>
        <td><?= safeGet($row,'phone') ?></td>
        <td><?= safeGet($row,'unique_id') ?></td>
        <td>
          <a href="edit_voter.php?id=<?= safeGet($row,'id') ?>" class="btn btn-sm btn-warning">Edit</a>
          <form action="delete_voter.php" method="POST" class="d-inline">
            <input type="hidden" name="id" value="<?= safeGet($row,'id') ?>">
            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
          </form>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
  </div>
  <h2 class="mt-5 mb-3">Contestants</h2>
  <div class="table-responsive">
  <table class="table table-striped table-bordered">
    <thead class="table-primary">
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Party</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php while($row=$contestants->fetch_assoc()): ?>
      <tr>
        <td><?= safeGet($row,'id') ?></td>
        <td><?= safeGet($row,'name') ?></td>
        <td><?= safeGet($row,'email') ?></td>
        <td><?= safeGet($row,'phone') ?></td>
        <td><?= safeGet($row,'party') ?></td>
        <td>
          <a href="edit_contestant.php?id=<?= safeGet($row,'id') ?>" class="btn btn-sm btn-warning">Edit</a>
          <form action="delete_contestant.php" method="POST" class="d-inline">
            <input type="hidden" name="id" value="<?= safeGet($row,'id') ?>">
            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
          </form>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
