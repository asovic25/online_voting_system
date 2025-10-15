<?php
include '../db.php';
include 'admin_auth.php'; // Optional: include admin auth check

function safeGet($row, $key) {
    return isset($row[$key]) ? htmlspecialchars($row[$key], ENT_QUOTES, 'UTF-8') : '';
}

$voters = $conn->query("SELECT * FROM voters ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>View Voters | Admin Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="admin_style.css?v=6">
</head>
<body>
<?php include 'admin_nav.php'; ?>
<div class="container mt-5 pt-5">
    <h2 class="mb-4">Registered Voters</h2>
    <table class="table table-hover table-bordered align-middle">
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
            <?php while($row = $voters->fetch_assoc()): ?>
            <tr>
                <td><?= safeGet($row,'id'); ?></td>
                <td><?= safeGet($row,'name'); ?></td>
                <td><?= safeGet($row,'email'); ?></td>
                <td><?= safeGet($row,'phone'); ?></td>
                <td><?= safeGet($row,'unique_id'); ?></td>
                <td>
                    <a href="edit_voter.php?id=<?= safeGet($row,'id'); ?>" class="btn btn-sm btn-warning">Edit</a>
                    <a href="delete_voter.php?id=<?= safeGet($row,'id'); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?');">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
