<?php 
include 'db.php';
$showCard = false;
$contestantData = [];

function destroy($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

$parties = [
    "APC" => "All Progressives Congress (APC)",
    "PDP" => "People's Democratic Party (PDP)",
    "LP" => "Labour Party (LP)",
    "NNPP" => "New Nigeria People's Party (NNPP)",
    "APGA" => "All Progressives Grand Alliance (APGA)",
    "ADC" => "African Democratic Congress (ADC)",
    "SDP" => "Social Democratic Party (SDP)",
    "YPP" => "Young Progressives Party (YPP)",
    "AAC" => "African Action Congress (AAC)",
    "ADP" => "Action Democratic Party (ADP)",
    "PRP" => "People’s Redemption Party (PRP)",
    "ZLP" => "Zenith Labour Party (ZLP)"
];

$states = [
    "Abia", "Adamawa", "Akwa Ibom", "Anambra", "Bauchi", "Bayelsa", "Benue", "Borno",
    "Cross River", "Delta", "Ebonyi", "Edo", "Ekiti", "Enugu", "Gombe", "Imo", "Jigawa",
    "Kaduna", "Kano", "Katsina", "Kebbi", "Kogi", "Kwara", "Lagos", "Nasarawa",
    "Niger", "Ogun", "Ondo", "Osun", "Oyo", "Plateau", "Rivers", "Sokoto",
    "Taraba", "Yobe", "Zamfara", "FCT (Abuja)"
];

$registeredParties = [];
$result = mysqli_query($conn, "SELECT party FROM contestants");
while ($row = mysqli_fetch_assoc($result)) {
    $registeredParties[] = $row['party'];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = destroy($_POST['name']);
    $username = destroy($_POST['username']);
    $dob = destroy($_POST['dob']);
    $phone = destroy($_POST['phone']);
    $email = destroy($_POST['email']);
    $state = destroy($_POST['state']);
    $password = $_POST['password'];
    $rpassword = $_POST['rpassword'];
    $party = destroy($_POST['party']);

    //Passport upload
    $passport = '';
    if (!empty($_FILES['passport']['name'])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir);
        $passport = $target_dir . basename($_FILES["passport"]["name"]);
        move_uploaded_file($_FILES["passport"]["tmp_name"], $passport);
    }

    $uppercase = preg_match('@[A-Z]@', $password);
    $lowercase = preg_match('@[a-z]@', $password);
    $number = preg_match('@[0-9]@', $password);
    $special = preg_match('@[^\w]@', $password);

    if ($password == $rpassword) {
        if (strlen($password) >= 8 && $uppercase && $lowercase && $number && $special) {
            $pass = password_hash($password, PASSWORD_DEFAULT);

            $checkUser = mysqli_query($conn, "SELECT id FROM contestants WHERE username = '$username'");
            if (mysqli_num_rows($checkUser) > 0) {
                $error = "Username already exists!";
            } elseif (in_array($party, $registeredParties)) {
                $error = "This political party already has a contestant registered!";
            } else {
                $insert = mysqli_query($conn, "
                    INSERT INTO contestants (name, username, dob, phone, email, state, password, party, passport)
                    VALUES ('$name','$username','$dob','$phone','$email','$state','$pass','$party','$passport')
                ") or die('Cannot insert: ' . mysqli_error($conn));

                if ($insert) {
                    $success = 'Registration Successful';
                    $showCard = true;
                    $contestantData = [
                        "name" => $name,
                        "dob" => $dob,
                        "phone" => $phone,
                        "state" => $state,
                        "party" => $party,
                        "passport" => $passport
                    ];
                    $registeredParties[] = $party;
                }
            }
        } else {
            $error = "Password must be at least 8 characters and include uppercase, lowercase, number, and special character.";
        }
    } else {
        $error = "Passwords do not match.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register Contestant | Online Voting System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css?v=5">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
.password-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}
.password-wrapper .password-field {
  flex: 1;
  padding-right: 40px;
  height: 45px;
}
.password-wrapper .toggle-password {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  color: #6c757d;
  font-size: 1.1rem;
  cursor: pointer;
}
.password-wrapper .toggle-password:hover {
  color: #0d6efd;
}
.password-wrapper .toggle-password.active i {
  color: #198754;
}
</style>
</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="index.php">🗳 Online Voting System</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link active" href="register_contestant.php">Register Contestant</a></li>
        <li class="nav-item"><a class="nav-link" href="register_voter.php">Register Voter</a></li>
        <li class="nav-item"><a class="nav-link" href="vote.php">Cast Vote</a></li>
        <li class="nav-item"><a class="nav-link" href="results.php">View Results</a></li>
      </ul>
    </div>
  </div>
</nav>
<!-- Registration Form -->
<div class="container form-container mt-5 pt-5">
  <div class="card shadow-lg border-0">
    <div class="card-header text-center bg-light">
      <h3 class="text-primary mb-0">Contestant Registration</h3>
    </div>
    <div class="card-body bg-white">
      <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
      <?php elseif (isset($success)): ?>
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

        <!--State Dropdown -->
        <div class="col-md-6">
          <label class="form-label fw-semibold text-primary">State</label>
          <select name="state" class="form-select" required>
            <option value="">-- Select State --</option>
            <?php foreach ($states as $s): ?>
              <option value="<?= $s ?>"><?= $s ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <!-- Party Dropdown -->
        <div class="col-md-6">
          <label class="form-label fw-semibold text-primary">Party</label>
          <select name="party" class="form-select" required>
            <option value="">-- Select Political Party --</option>
            <?php foreach ($parties as $key => $partyName): ?>
              <option value="<?= $key ?>" <?= in_array($key, $registeredParties) ? 'disabled class="taken-party"' : '' ?>>
                <?= $partyName ?> <?= in_array($key, $registeredParties) ? '(Taken)' : '' ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <!-- Password -->
        <div class="col-md-6">
          <label class="form-label fw-semibold text-primary">Password</label>
          <div class="password-wrapper">
            <input type="password" name="password" id="password" class="form-control password-field" placeholder="Enter Password" required>
            <button type="button" class="toggle-password"><i class="fa-solid fa-eye-slash"></i></button>
          </div>
        </div>
        <!-- Repeat Password -->
        <div class="col-md-6">
          <label class="form-label fw-semibold text-primary">Repeat Password</label>
          <div class="password-wrapper">
            <input type="password" name="rpassword" id="rpassword" class="form-control password-field" placeholder="Repeat Password" required>
            <button type="button" class="toggle-password"><i class="fa-solid fa-eye-slash"></i></button>
          </div>
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold text-primary">Passport Photo</label>
          <input type="file" name="passport" class="form-control" accept="image/*" required>
        </div>
        <div class="text-center mt-3">
          <button type="submit" class="btn btn-primary px-5">Register</button>
        </div>
      </form>
    </div>
  </div>
  <?php if ($showCard): ?>
  <div class="card mt-5 text-center id-card shadow">
    <div class="card-body">
      <img src="<?= $contestantData['passport']; ?>" class="rounded-circle mb-3" width="100" height="100" alt="Passport">
      <h4><?= $contestantData['name']; ?></h4>
      <p><strong>State:</strong> <?= $contestantData['state']; ?></p>
      <p><strong>DOB:</strong> <?= $contestantData['dob']; ?></p>
      <p><strong>Phone:</strong> <?= $contestantData['phone']; ?></p>
      <p><strong>Party:</strong> <?= $contestantData['party']; ?></p>
      <button onclick="window.print()" class="btn btn-outline-primary mt-2">🖨 Print Card</button>
    </div>
  </div>
  <?php endif; ?>
</div>
<footer class="footer text-light text-center py-3 mt-5">
  <p class="mb-0">&copy; <?= date("Y") ?> Online Voting System</p>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelectorAll('.password-wrapper').forEach(wrapper => {
  const input = wrapper.querySelector('.password-field');
  const btn   = wrapper.querySelector('.toggle-password');
  const icon  = btn.querySelector('i');
  btn.addEventListener('click', () => {
    if (input.type === 'password') {
      input.type = 'text';
      icon.classList.replace('fa-eye-slash', 'fa-eye');
    } else {
      input.type = 'password';
      icon.classList.replace('fa-eye', 'fa-eye-slash');
    }
  });
});
</script>
</body>
</html>
