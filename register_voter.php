<?php 
include 'db.php';
$showCard = false;
$voterData = [];
$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $username = trim($_POST['username']);
    $phone = trim($_POST['phone']);
    $dob = trim($_POST['dob']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $rpassword = $_POST['rpassword'];

    // Upload passport
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) mkdir($target_dir);
    $passport = $target_dir . time() . "_" . basename($_FILES["passport"]["name"]);
    move_uploaded_file($_FILES["passport"]["tmp_name"], $passport);

    // Generate unique voter ID
    $unique_id = 'VOT' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));

    // Validate password
    $uppercasea = preg_match('@[A-Z]@', $password);
    $lowercasea = preg_match('@[a-z]@', $password);
    $numbera = preg_match('@[0-9]@', $password);
    $specialCharsa = preg_match('@[^\w]@', $password);

    if ($password == $rpassword) {
        if(strlen($password) >= 8 && $uppercasea && $lowercasea && $numbera && $specialCharsa){
            $pass = password_hash($password, PASSWORD_DEFAULT);

            // Check duplicate username or email
            $check = mysqli_query($conn, "SELECT username FROM voters WHERE username = '$username' OR email = '$email'");
            if (mysqli_num_rows($check) > 0) {
                $error = "❌ Username or Email already exists!";
            } else {
                $sql = "INSERT INTO voters (name, username, phone, dob, email, password, passport, unique_id)
                        VALUES ('$name', '$username','$phone','$dob','$email','$pass', '$passport','$unique_id')";
                if (mysqli_query($conn, $sql)) {
                    $success = "✅ Registration Successful!";
                    $showCard = true;
                    $voterData = [
                        "name" => $name,
                        "email" => $email,
                        "phone" => $phone,
                        "passport" => $passport,
                        "unique_id" => $unique_id
                    ];
                } else {
                    $error = "❌ Error: " . mysqli_error($conn);
                }
            }
        } else {
            $error = "Password must be at least 8 characters and include uppercase, lowercase, number, and special character.";
        }
    } else {
        $error = "Passwords do not match!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Voter | Online Voting System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=4">
</head>
<body>
<!--Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top custom-navbar">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold text-light" href="index.php">🗳 Online Voting System</a>
    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse"
            data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false"
            aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-center" id="mainNavbar">
      <ul class="navbar-nav text-center">
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="register_contestant.php">Register Contestant</a></li>
        <li class="nav-item"><a class="nav-link active" href="register_voter.php">Register Voter</a></li>
        <li class="nav-item"><a class="nav-link" href="vote.php">Cast Vote</a></li>
        <li class="nav-item"><a class="nav-link" href="results.php">View Results</a></li>
      </ul>
    </div>
  </div>
</nav>
<!--Registration Form -->
<div class="container form-container mt-5 pt-5">
  <div class="card shadow-lg border-0">
    <div class="card-header text-center bg-light">
      <h3 class="text-primary mb-0">Voter Registration</h3>
    </div>
    <div class="card-body bg-white">
      <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
      <?php elseif ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
      <?php endif; ?>
      <form method="POST" enctype="multipart/form-data" class="row g-3">
        <div class="col-md-6">
          <label class="form-label fw-semibold text-primary">Full Name</label>
          <input type="text" name="name" class="form-control" placeholder="Enter Full Name" required>
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold text-primary">Username</label>
          <input type="text" name="username" class="form-control" placeholder="Enter Username" required>
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold text-primary">Date of Birth</label>
          <input type="date" name="dob" class="form-control" required>
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold text-primary">Phone Number</label>
          <input type="number" name="phone" class="form-control" placeholder="Enter Phone Number" required>
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
        <div class="col-6">
          <label class="form-label fw-semibold text-primary">Upload Passport</label>
          <input type="file" name="passport" class="form-control" accept="image/*" required>
        </div>
        <div class="text-center mt-3">
          <button type="submit" class="btn btn-primary px-5">Register</button>
        </div>
      </form>
    </div>
  </div>
  <!--Display Voter Card -->
  <?php if ($showCard): ?>
  <div class="card mt-5 shadow text-center id-card">
    <img src="<?= $voterData['passport']; ?>" alt="Passport" class="rounded-circle mx-auto" width="120" height="120">
    <h4 class="mt-3"><?= $voterData['name']; ?></h4>
    <p><strong>Email:</strong> <?= $voterData['email']; ?></p>
    <p><strong>Phone:</strong> <?= $voterData['phone']; ?></p>
    <p><strong>Voter ID:</strong> <?= $voterData['unique_id']; ?></p>
    <p><span class="badge bg-primary">Registered Voter ✅</span></p>
    <button class="btn btn-outline-primary" onclick="printCard()">🖨 Print Card</button>
  </div>
  <?php endif; ?>
</div>

<!--Footer -->
<footer class="text-center py-3 text-white mt-5">
  <p class="mb-0">&copy; <?= date('Y'); ?> Online Voting System</p>
</footer>

<script>
function printCard() {
  var printContents = document.querySelector('.card.mt-5').outerHTML;
  var newWin = window.open('', '', 'width=700,height=500');
  newWin.document.write('<html><head><title>Print Voter Card</title>');
  newWin.document.write('<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">');
  newWin.document.write('</head><body class="p-4 bg-light">');
  newWin.document.write(printContents);
  newWin.document.write('</body></html>');
  newWin.document.close();
  newWin.print();
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Fallback: simple toggle if Bootstrap cannot initialize for some reason -->
<script>
document.addEventListener('DOMContentLoaded', function () {
  // quick sanity check: console errors will prevent bootstrap from running
  // fallback toggler if collapse doesn't open
  var toggler = document.querySelector('.navbar-toggler');
  var collapse = document.getElementById('mainNavbar');

  if (toggler && collapse) {
    toggler.addEventListener('click', function (e) {
      // If bootstrap works normally, this is redundant but harmless.
      collapse.classList.toggle('show');
      // toggle aria-expanded attribute for accessibility
      var expanded = toggler.getAttribute('aria-expanded') === 'true';
      toggler.setAttribute('aria-expanded', (!expanded).toString());
    });
  }
});
</script>
</body>
</html>