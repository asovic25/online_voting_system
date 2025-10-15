<?php
include 'db.php';
$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $rpassword = $_POST['rpassword'];
    $voter_id = trim($_POST['voter_id']);
    $contestant_id = $_POST['contestant_id'];

    // Validate inputs
    if ($password !== $rpassword) {
        $message = "❌ Passwords do not match!";
    } else {
        // Check if voter already exists
        $checkVoter = $conn->query("SELECT * FROM voters WHERE voter_id='$voter_id' OR username='$username'");
        if ($checkVoter->num_rows == 0) {
            // Hash password and insert new voter
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $insertVoter = $conn->query("INSERT INTO voters (username, email, password, voter_id) VALUES ('$username','$email','$hashed','$voter_id')");
            if (!$insertVoter) {
                $message = "❌ Failed to register voter.";
            }
        }

        // Check if voter already voted
        $checkVote = $conn->query("SELECT * FROM votes WHERE voter_id='$voter_id'");
        if ($checkVote->num_rows > 0) {
            $message = "⚠️ You have already voted!";
        } else {
            // Insert vote
            $insertVote = $conn->query("INSERT INTO votes (voter_id, contestant_id) VALUES ('$voter_id','$contestant_id')");
            if ($insertVote) {
                $message = "✅ Your vote has been cast successfully!";
            } else {
                $message = "❌ Error casting vote.";
            }
        }
    }
}

//Fetch contestants
$contestants = $conn->query("SELECT * FROM contestants");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cast Your Vote | Online Voting System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css?v=4">
</head>
<body>
<!--Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top custom-navbar">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold text-light" href="#">🗳 Online Voting System</a>
    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
      <ul class="navbar-nav text-center">
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="register_contestant.php">Register Contestant</a></li>
        <li class="nav-item"><a class="nav-link" href="register_voter.php">Register Voter</a></li>
        <li class="nav-item"><a class="nav-link active" href="vote.php">Cast Vote</a></li>
        <li class="nav-item"><a class="nav-link" href="results.php">View Results</a></li>
      </ul>
    </div>
  </div>
</nav>
<!--Voting Form -->
<div class="container form-container mt-5 pt-5">
  <div class="card shadow-lg border-0">
    <div class="card-header text-center bg-light">
      <h3 class="text-primary mb-0">Cast Your Vote</h3>
    </div>
    <div class="card-body bg-white">
      <?php if ($message != ""): ?>
        <div class="alert alert-info text-center fw-semibold"><?= $message ?></div>
      <?php endif; ?>
      <form method="POST" class="row g-3">
        <div class="col-md-6">
          <label class="form-label fw-semibold text-primary">Username</label>
          <input type="text" name="username" class="form-control" placeholder="Enter Username" required>
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold text-primary">Email</label>
          <input type="email" name="email" class="form-control" placeholder="Enter Email Address" required>
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold text-primary">Password</label>
          <input type="password" name="password" class="form-control" placeholder="Enter Password" required>
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold text-primary">Repeat Password</label>
          <input type="password" name="rpassword" class="form-control" placeholder="Repeat Password" required>
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold text-primary">Voter ID</label>
          <input type="text" name="voter_id" class="form-control" placeholder="Enter Your Voter ID" required>
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold text-primary">Select Contestant</label>
          <select name="contestant_id" class="form-select" required>
            <option value="">-- Select Contestant --</option>
            <?php while($row = $contestants->fetch_assoc()): ?>
              <option value="<?= $row['id']; ?>">
                <?= htmlspecialchars($row['name']) . " (" . htmlspecialchars($row['party']) . ")"; ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="text-center mt-4">
          <button type="submit" class="btn btn-primary px-5">Submit Vote</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!--Footer -->
<footer class="text-center py-3 text-white mt-auto">
  <p class="mb-0">&copy; <?= date('Y'); ?> Online Voting System</p>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>