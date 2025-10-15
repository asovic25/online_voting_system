<?php
session_start();
include('../db.php');

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

function safeGet(array $row, $keys, $default = '') {
    foreach ((array)$keys as $k) {
        if (array_key_exists($k, $row) && $row[$k] !== null && $row[$k] !== '') {
            return $row[$k];
        }
    }
    return $default;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$contestantQuery = $conn->query("SELECT * FROM contestants WHERE id = $id");
if (!$contestantQuery || $contestantQuery->num_rows == 0) {
    die("Contestant not found.");
}

$contestant = $contestantQuery->fetch_assoc();
$error = $success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $party = $_POST['party'];
    $dob = $_POST['dob'];

    $stmt = $conn->prepare("UPDATE contestants SET name=?, email=?, phone=?, party=?, dob=? WHERE id=?");
    $stmt->bind_param("sssssi", $name, $email, $phone, $party, $dob, $id);
    if ($stmt->execute()) {
        $success = "Contestant updated successfully.";
        $contestant = $conn->query("SELECT * FROM contestants WHERE id = $id")->fetch_assoc();
    } else {
        $error = "Error updating contestant: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Contestant | Admin Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../style.css?v=13">
</head>
<body>
<div class="container mt-5 pt-5">
    <h3 class="mb-4 text-primary">Edit Contestant</h3>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST" class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Full Name</label>
            <input type="text" name="name" class="form-control" value="<?= safeGet($contestant, ['name','fullname','username']) ?>" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?= safeGet($contestant, ['email']) ?>" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control" value="<?= safeGet($contestant, ['phone']) ?>" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">Party</label>
            <input type="text" name="party" class="form-control" value="<?= safeGet($contestant, ['party']) ?>" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">Date of Birth</label>
            <input type="date" name="dob" class="form-control" value="<?= safeGet($contestant, ['dob']) ?>" required>
        </div>
        <div class="col-12 text-center mt-3">
            <button type="submit" class="btn btn-primary">Update Contestant</button>
            <a href="admin_dashboard.php" class="btn btn-secondary">Back</a>
        </div>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
