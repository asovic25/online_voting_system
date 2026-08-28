<?php
include 'db.php';

$showCard = false;
$voterData = [];
$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Collect and sanitize form inputs
    $name = trim($_POST['name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $dob = trim($_POST['dob'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $rpassword = $_POST['rpassword'] ?? '';

    /*
    |--------------------------------------------------------------------------
    | 1. Basic validation
    |--------------------------------------------------------------------------
    */
    if (
        $name === '' ||
        $username === '' ||
        $phone === '' ||
        $dob === '' ||
        $email === '' ||
        $password === '' ||
        $rpassword === ''
    ) {

        $error = "❌ Please complete all required fields.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "❌ Please enter a valid email address.";

    } elseif ($password !== $rpassword) {

        $error = "❌ Passwords do not match!";

    } else {

        /*
        |--------------------------------------------------------------------------
        | 2. Password strength validation
        |--------------------------------------------------------------------------
        */
        $uppercase = preg_match('/[A-Z]/', $password);
        $lowercase = preg_match('/[a-z]/', $password);
        $number = preg_match('/[0-9]/', $password);
        $specialChars = preg_match('/[^\w]/', $password);

        if (
            strlen($password) < 8 ||
            !$uppercase ||
            !$lowercase ||
            !$number ||
            !$specialChars
        ) {

            $error = "Password must be at least 8 characters and include uppercase, lowercase, number, and special character.";

        } else {

            /*
            |--------------------------------------------------------------------------
            | 3. Validate passport upload
            |--------------------------------------------------------------------------
            */
            if (
                !isset($_FILES['passport']) ||
                $_FILES['passport']['error'] !== UPLOAD_ERR_OK
            ) {

                $error = "❌ Please upload a valid passport photograph.";

            } elseif ($_FILES['passport']['size'] > 5 * 1024 * 1024) {

                $error = "❌ Passport image must not be larger than 5MB.";

            } else {

                $tmpFile = $_FILES['passport']['tmp_name'];

                // Detect actual MIME type instead of trusting file extension
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mimeType = $finfo->file($tmpFile);

                $allowedTypes = [
                    'image/jpeg' => 'jpg',
                    'image/png'  => 'png',
                    'image/gif'  => 'gif',
                    'image/webp' => 'webp'
                ];

                if (!isset($allowedTypes[$mimeType])) {

                    $error = "❌ Invalid passport format. Please upload JPG, PNG, GIF, or WEBP.";

                } else {

                    /*
                    |--------------------------------------------------------------------------
                    | 4. Validate date of birth
                    |--------------------------------------------------------------------------
                    */
                    $dobDate = DateTime::createFromFormat('Y-m-d', $dob);

                    if (
                        !$dobDate ||
                        $dobDate->format('Y-m-d') !== $dob
                    ) {

                        $error = "❌ Please enter a valid date of birth.";

                    } elseif ($dobDate > new DateTime('today')) {

                        $error = "❌ Date of birth cannot be in the future.";

                    } else {

                        /*
                        |--------------------------------------------------------------------------
                        | 5. Check duplicate username/email
                        |--------------------------------------------------------------------------
                        */
                        $check = $conn->prepare("
                            SELECT id
                            FROM voters
                            WHERE username = ? OR email = ?
                            LIMIT 1
                        ");

                        if (!$check) {

                            $error = "❌ Unable to process registration.";

                        } else {

                            $check->bind_param("ss", $username, $email);
                            $check->execute();
                            $check->store_result();

                            if ($check->num_rows > 0) {

                                $error = "❌ Username or Email already exists!";

                            } else {

                                /*
                                |--------------------------------------------------------------------------
                                | 6. Generate secure unique Voter ID
                                |--------------------------------------------------------------------------
                                */
                                $unique_id = '';

                                for ($attempt = 0; $attempt < 10; $attempt++) {

                                    try {
                                        $randomPart = strtoupper(
                                            bin2hex(random_bytes(4))
                                        );
                                    } catch (Exception $e) {
                                        $randomPart = strtoupper(
                                            substr(hash('sha256', uniqid((string)mt_rand(), true)), 0, 8)
                                        );
                                    }

                                    $candidateId = 'VOT' . $randomPart;

                                    $idCheck = $conn->prepare("
                                        SELECT id
                                        FROM voters
                                        WHERE unique_id = ?
                                        LIMIT 1
                                    ");

                                    if (!$idCheck) {
                                        break;
                                    }

                                    $idCheck->bind_param("s", $candidateId);
                                    $idCheck->execute();
                                    $idCheck->store_result();

                                    if ($idCheck->num_rows === 0) {
                                        $unique_id = $candidateId;
                                        $idCheck->close();
                                        break;
                                    }

                                    $idCheck->close();
                                }

                                if ($unique_id === '') {

                                    $error = "❌ Unable to generate a unique Voter ID. Please try again.";

                                } else {

                                    /*
                                    |--------------------------------------------------------------------------
                                    | 7. Hash password
                                    |--------------------------------------------------------------------------
                                    */
                                    $hashedPassword = password_hash(
                                        $password,
                                        PASSWORD_DEFAULT
                                    );

                                    if ($hashedPassword === false) {

                                        $error = "❌ Unable to secure your password.";

                                    } else {

                                        /*
                                        |--------------------------------------------------------------------------
                                        | 8. Create upload directory
                                        |--------------------------------------------------------------------------
                                        */
                                        $target_dir = "uploads/";

                                        if (!is_dir($target_dir)) {

                                            if (!mkdir($target_dir, 0755, true)) {
                                                $error = "❌ Unable to create upload directory.";
                                            }
                                        }

                                        if ($error === "") {

                                            /*
                                            |--------------------------------------------------------------------------
                                            | 9. Generate secure filename
                                            |--------------------------------------------------------------------------
                                            */
                                            try {
                                                $randomFileName = bin2hex(random_bytes(16));
                                            } catch (Exception $e) {
                                                $randomFileName = hash(
                                                    'sha256',
                                                    uniqid((string)mt_rand(), true)
                                                );
                                            }

                                            $extension = $allowedTypes[$mimeType];

                                            $filename = $randomFileName . '.' . $extension;

                                            $passport = $target_dir . $filename;

                                            /*
                                            |--------------------------------------------------------------------------
                                            | 10. Move uploaded passport
                                            |--------------------------------------------------------------------------
                                            */
                                            if (!move_uploaded_file($tmpFile, $passport)) {

                                                $error = "❌ Unable to save passport photograph.";

                                            } else {

                                                /*
                                                |--------------------------------------------------------------------------
                                                | 11. Database transaction
                                                |--------------------------------------------------------------------------
                                                */
                                                $conn->begin_transaction();

                                                try {

                                                    $insert = $conn->prepare("
                                                        INSERT INTO voters
                                                        (
                                                            name,
                                                            username,
                                                            phone,
                                                            dob,
                                                            email,
                                                            password,
                                                            passport,
                                                            unique_id
                                                        )
                                                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                                                    ");

                                                    if (!$insert) {
                                                        throw new Exception("Unable to prepare registration.");
                                                    }

                                                    $insert->bind_param(
                                                        "ssssssss",
                                                        $name,
                                                        $username,
                                                        $phone,
                                                        $dob,
                                                        $email,
                                                        $hashedPassword,
                                                        $passport,
                                                        $unique_id
                                                    );

                                                    if (!$insert->execute()) {
                                                        throw new Exception("Unable to complete registration.");
                                                    }

                                                    $insert->close();

                                                    /*
                                                    |--------------------------------------------------------------------------
                                                    | 12. Commit transaction
                                                    |--------------------------------------------------------------------------
                                                    */
                                                    $conn->commit();

                                                    $success = "✅ Registration Successful!";
                                                    $showCard = true;

                                                    $voterData = [
                                                        "name" => $name,
                                                        "email" => $email,
                                                        "phone" => $phone,
                                                        "passport" => $passport,
                                                        "unique_id" => $unique_id
                                                    ];

                                                } catch (Throwable $e) {

                                                    /*
                                                    |--------------------------------------------------------------------------
                                                    | Roll back database and remove uploaded file
                                                    |--------------------------------------------------------------------------
                                                    */
                                                    $conn->rollback();

                                                    if (
                                                        isset($passport) &&
                                                        file_exists($passport)
                                                    ) {
                                                        unlink($passport);
                                                    }

                                                    /*
                                                    | Do not expose database errors to users.
                                                    */
                                                    $error = "❌ Registration could not be completed. Please try again.";
                                                }
                                            }
                                        }
                                    }
                                }
                            }

                            $check->close();
                        }
                    }
                }
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

    <title>Register Voter | Online Voting System</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
    >

    <link rel="stylesheet" href="style.css?v=4">

</head>

<body>

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


<!-- Navbar -->

<nav class="navbar navbar-expand-lg navbar-dark fixed-top custom-navbar">

    <div class="container-fluid">

        <a
            class="navbar-brand fw-bold text-light"
            href="index.php"
        >
            🗳 Online Voting System
        </a>

        <button
            class="navbar-toggler border-0"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#mainNavbar"
            aria-controls="mainNavbar"
            aria-expanded="false"
            aria-label="Toggle navigation"
        >

            <span class="navbar-toggler-icon"></span>

        </button>

        <div
            class="collapse navbar-collapse justify-content-center"
            id="mainNavbar"
        >

            <ul class="navbar-nav text-center">

                <li class="nav-item">
                    <a class="nav-link" href="index.php">
                        Home
                    </a>
                </li>

                <li class="nav-item">
                    <a
                        class="nav-link"
                        href="register_contestant.php"
                    >
                        Register Contestant
                    </a>
                </li>

                <li class="nav-item">
                    <a
                        class="nav-link active"
                        href="register_voter.php"
                    >
                        Register Voter
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="vote.php">
                        Cast Vote
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="results.php">
                        View Results
                    </a>
                </li>

            </ul>

        </div>

    </div>

</nav>


<!-- Registration Form -->

<div class="container form-container mt-5 pt-5">

    <div class="card shadow-lg border-0">

        <div class="card-header text-center bg-light">

            <h3 class="text-primary mb-0">
                Voter Registration
            </h3>

        </div>

        <div class="card-body bg-white">

            <?php if ($error): ?>

                <div class="alert alert-danger">
                    <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
                </div>

            <?php elseif ($success): ?>

                <div class="alert alert-success">
                    <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?>
                </div>

            <?php endif; ?>


            <form
                method="POST"
                enctype="multipart/form-data"
                class="row g-3"
            >

                <div class="col-md-6">

                    <label class="form-label fw-semibold text-primary">
                        Full Name
                    </label>

                    <input
                        type="text"
                        name="name"
                        class="form-control"
                        placeholder="Enter Full Name"
                        value="<?= htmlspecialchars($_POST['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                        required
                    >

                </div>


                <div class="col-md-6">

                    <label class="form-label fw-semibold text-primary">
                        Username
                    </label>

                    <input
                        type="text"
                        name="username"
                        class="form-control"
                        placeholder="Enter Username"
                        value="<?= htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                        required
                    >

                </div>


                <div class="col-md-6">

                    <label class="form-label fw-semibold text-primary">
                        Date of Birth
                    </label>

                    <input
                        type="date"
                        name="dob"
                        class="form-control"
                        value="<?= htmlspecialchars($_POST['dob'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                        required
                    >

                </div>


                <div class="col-md-6">

                    <label class="form-label fw-semibold text-primary">
                        Phone Number
                    </label>

                    <input
                        type="number"
                        name="phone"
                        class="form-control"
                        placeholder="Enter Phone Number"
                        value="<?= htmlspecialchars($_POST['phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                        required
                    >

                </div>


                <div class="col-md-6">

                    <label class="form-label fw-semibold text-primary">
                        Email
                    </label>

                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        placeholder="Enter Email Address"
                        value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                        required
                    >

                </div>


                <!-- Password -->

                <div class="col-md-6">

                    <label class="form-label fw-semibold text-primary">
                        Password
                    </label>

                    <div class="password-wrapper">

                        <input
                            type="password"
                            name="password"
                            id="password"
                            class="form-control password-field"
                            placeholder="Enter Password"
                            required
                        >

                        <button
                            type="button"
                            class="toggle-password"
                            aria-label="Show password"
                        >

                            <i class="fa-solid fa-eye-slash"></i>

                        </button>

                    </div>

                </div>


                <!-- Repeat Password -->

                <div class="col-md-6">

                    <label class="form-label fw-semibold text-primary">
                        Repeat Password
                    </label>

                    <div class="password-wrapper">

                        <input
                            type="password"
                            name="rpassword"
                            id="rpassword"
                            class="form-control password-field"
                            placeholder="Repeat Password"
                            required
                        >

                        <button
                            type="button"
                            class="toggle-password"
                            aria-label="Show password"
                        >

                            <i class="fa-solid fa-eye-slash"></i>

                        </button>

                    </div>

                </div>


                <!-- Passport -->

                <div class="col-6">

                    <label class="form-label fw-semibold text-primary">
                        Upload Passport
                    </label>

                    <input
                        type="file"
                        name="passport"
                        class="form-control"
                        accept="image/jpeg,image/png,image/gif,image/webp"
                        required
                    >

                    <small class="text-muted">
                        JPG, PNG, GIF or WEBP — Maximum 5MB
                    </small>

                </div>


                <div class="text-center mt-3">

                    <button
                        type="submit"
                        class="btn btn-primary px-5"
                    >
                        Register
                    </button>

                </div>

            </form>

        </div>

    </div>


    <!-- Display Voter Card -->

    <?php if ($showCard): ?>

        <div class="card mt-5 shadow text-center id-card">

            <img
                src="<?= htmlspecialchars($voterData['passport'], ENT_QUOTES, 'UTF-8'); ?>"
                alt="Passport"
                class="rounded-circle mx-auto"
                width="120"
                height="120"
            >

            <h4 class="mt-3">
                <?= htmlspecialchars($voterData['name'], ENT_QUOTES, 'UTF-8'); ?>
            </h4>

            <p>
                <strong>Email:</strong>
                <?= htmlspecialchars($voterData['email'], ENT_QUOTES, 'UTF-8'); ?>
            </p>

            <p>
                <strong>Phone:</strong>
                <?= htmlspecialchars($voterData['phone'], ENT_QUOTES, 'UTF-8'); ?>
            </p>

            <p>
                <strong>Voter ID:</strong>
                <?= htmlspecialchars($voterData['unique_id'], ENT_QUOTES, 'UTF-8'); ?>
            </p>

            <p>
                <span class="badge bg-primary">
                    Registered Voter ✅
                </span>
            </p>

            <button
                class="btn btn-outline-primary"
                onclick="printCard()"
            >
                🖨 Print Card
            </button>

        </div>

    <?php endif; ?>

</div>


<!-- Footer -->

<footer class="text-center py-3 text-white mt-5">

    <p class="mb-0">
        &copy; <?= date('Y'); ?> Online Voting System
    </p>

</footer>


<!-- Print Card Script -->

<script>

function printCard() {

    var card = document.querySelector('.id-card');

    if (!card) {
        return;
    }

    var printContents = card.outerHTML;

    var newWin = window.open(
        '',
        '',
        'width=700,height=500'
    );

    if (!newWin) {
        alert('Please allow pop-ups to print your voter card.');
        return;
    }

    newWin.document.write(
        '<html><head><title>Print Voter Card</title>'
    );

    newWin.document.write(
        '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">'
    );

    newWin.document.write(
        '</head><body class="p-4 bg-light">'
    );

    newWin.document.write(printContents);

    newWin.document.write(
        '</body></html>'
    );

    newWin.document.close();

    newWin.focus();

    newWin.print();

}

</script>


<!-- Bootstrap -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


<!-- Navbar fallback -->

<script>

document.addEventListener('DOMContentLoaded', function () {

    var toggler = document.querySelector('.navbar-toggler');

    var collapse = document.getElementById('mainNavbar');

    if (toggler && collapse) {

        toggler.addEventListener('click', function () {

            collapse.classList.toggle('show');

            var expanded =
                toggler.getAttribute('aria-expanded') === 'true';

            toggler.setAttribute(
                'aria-expanded',
                (!expanded).toString()
            );

        });

    }

});

</script>


<!-- Password Show/Hide -->

<script>

document.querySelectorAll('.password-wrapper').forEach(wrapper => {

    const input = wrapper.querySelector('.password-field');

    const btn = wrapper.querySelector('.toggle-password');

    const icon = btn.querySelector('i');

    btn.addEventListener('click', () => {

        if (input.type === 'password') {

            input.type = 'text';

            icon.classList.replace(
                'fa-eye-slash',
                'fa-eye'
            );

            btn.classList.add('active');

            btn.setAttribute(
                'aria-label',
                'Hide password'
            );

        } else {

            input.type = 'password';

            icon.classList.replace(
                'fa-eye',
                'fa-eye-slash'
            );

            btn.classList.remove('active');

            btn.setAttribute(
                'aria-label',
                'Show password'
            );

        }

    });

});

</script>


</body>
</html>