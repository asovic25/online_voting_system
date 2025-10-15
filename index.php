<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A secure and user-friendly Online Voting System that allows registered voters to cast their votes electronically for their preferred contestants. Designed for transparency, efficiency, and real-time result tracking, ensuring a fair and reliable election process.">
    <meta name="keywords" content="online voting system, digital voting, secure election platform, electronic voting, online election, voting software, contestant voting, web voting system, secure online vote">
    <title>Online Voting System | Secure Digital Election Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=4">
    <link rel="canonical" href="http://localhost/online_voting_system/">
</head>
<body>
    <!-- ===== Navbar ===== -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="index.php">🗳 Online Voting System</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="register_contestant.php">Register Contestant</a></li>
                    <li class="nav-item"><a class="nav-link" href="register_voter.php">Register Voter</a></li>
                    <li class="nav-item"><a class="nav-link" href="vote.php">Cast Vote</a></li>
                    <li class="nav-item"><a class="nav-link" href="results.php">View Results</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- ===== Hero Section ===== -->
    <section class="hero-section text-center text-white d-flex flex-column justify-content-center align-items-center">
        <div class="overlay"></div>
        <div class="hero-content">
            <h1 class="fw-bold mb-3">Empowering Transparent Elections</h1>
            <p class="lead"><i>A secure platform for free and fair online voting.</i></p>
            <a href="register_voter.php" class="btn btn-light mt-3 fw-semibold">Get Started</a>
        </div>
    </section>
    <!-- ===== Main Content ===== -->
    <div class="container-fluid py-5 text-center">
        <h2 class="fw-bold mb-4">Welcome to the Online Voting System</h2>
        <p class="text-primary mb-5">Choose an action below to get started.</p>
        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="id-card p-4">
                    <h3>Register Contestant</h3>
                    <p>Contestants can register and get a unique contestant ID.</p>
                    <a href="register_contestant.php"><button>Register</button></a>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="id-card p-4">
                    <h3>Register Voter</h3>
                    <p>Voters can sign up and receive their voter ID to participate.</p>
                    <a href="register_voter.php"><button>Register</button></a>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="id-card p-4">
                    <h3>Cast Your Vote</h3>
                    <p>Submit your vote securely for your preferred contestant.</p>
                    <a href="vote.php"><button>Vote Now</button></a>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="id-card p-4">
                    <h3>View Results</h3>
                    <p>Check live election results after voting is concluded.</p>
                    <a href="results.php"><button>View Results</button></a>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== Footer ===== -->
    <footer class="text-center text-white py-3">
        <p>&copy; <?php echo date("Y"); ?> Online Voting System. All Rights Reserved.</p>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>