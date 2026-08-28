<?php
include 'db.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $rpassword = $_POST['rpassword'] ?? '';
    $voter_id = trim($_POST['voter_id'] ?? '');
    $contestant_id = (int)($_POST['contestant_id'] ?? 0);

    // 1. Validate password confirmation
    if ($password !== $rpassword) {

        $message = "❌ Passwords do not match!";

    } else {

        // 2. Find the registered voter using the unique voter ID
        $stmt = $conn->prepare("
            SELECT id, username, email, password, unique_id, has_voted
            FROM voters
            WHERE unique_id = ?
            LIMIT 1
        ");

        if (!$stmt) {

            $message = "❌ A system error occurred. Please try again.";

        } else {

            $stmt->bind_param("s", $voter_id);
            $stmt->execute();

            $result = $stmt->get_result();

            if ($voter = $result->fetch_assoc()) {

                // 3. Verify username
                if ($voter['username'] !== $username) {

                    $message = "❌ Invalid voter details.";

                // 4. Verify email
                } elseif (strcasecmp($voter['email'], $email) !== 0) {

                    $message = "❌ Invalid voter details.";

                // 5. Verify password
                } elseif (!password_verify($password, $voter['password'])) {

                    $message = "❌ Invalid voter details.";

                // 6. Check whether this voter has already voted
                } elseif ((int)$voter['has_voted'] === 1) {

                    $message = "⚠️ You have already voted! Each voter can vote only once.";

                } else {

                    // 7. Verify that the selected contestant exists
                    $contestantStmt = $conn->prepare("
                        SELECT id
                        FROM contestants
                        WHERE id = ?
                        LIMIT 1
                    ");

                    if (!$contestantStmt) {

                        $message = "❌ A system error occurred. Please try again.";

                    } else {

                        $contestantStmt->bind_param("i", $contestant_id);
                        $contestantStmt->execute();

                        $contestantResult = $contestantStmt->get_result();

                        if (!$contestantResult->fetch_assoc()) {

                            $message = "❌ Invalid contestant selected.";

                        } else {

                            /*
                             * 8. Start database transaction
                             *
                             * The vote insertion and voter status update
                             * will now be treated as one operation.
                             */
                            $conn->begin_transaction();

                            try {

                                /*
                                 * 9. Lock the voter record while processing
                                 *
                                 * This prevents two simultaneous requests
                                 * from successfully voting with the same voter.
                                 */
                                $lockVoter = $conn->prepare("
                                    SELECT id, has_voted
                                    FROM voters
                                    WHERE unique_id = ?
                                    LIMIT 1
                                    FOR UPDATE
                                ");

                                if (!$lockVoter) {
                                    throw new Exception("Unable to verify voter.");
                                }

                                $lockVoter->bind_param("s", $voter_id);
                                $lockVoter->execute();

                                $lockedResult = $lockVoter->get_result();
                                $lockedVoter = $lockedResult->fetch_assoc();

                                if (!$lockedVoter) {

                                    throw new Exception("Voter ID not found.");

                                }

                                // Check again after locking the voter record
                                if ((int)$lockedVoter['has_voted'] === 1) {

                                    $conn->rollback();

                                    $message = "⚠️ You have already voted! Each voter can vote only once.";

                                } else {

                                    /*
                                     * 10. Additional safeguard:
                                     * Check the votes table.
                                     */
                                    $voteCheck = $conn->prepare("
                                        SELECT id
                                        FROM votes
                                        WHERE voter_id = ?
                                        LIMIT 1
                                    ");

                                    if (!$voteCheck) {
                                        throw new Exception("Unable to verify voting status.");
                                    }

                                    $voteCheck->bind_param("s", $voter_id);
                                    $voteCheck->execute();

                                    $voteResult = $voteCheck->get_result();

                                    if ($voteResult->num_rows > 0) {

                                        $conn->rollback();

                                        $message = "⚠️ You have already voted! Each voter can vote only once.";

                                    } else {

                                        /*
                                         * 11. Insert the vote
                                         */
                                        $insertVote = $conn->prepare("
                                            INSERT INTO votes (voter_id, contestant_id)
                                            VALUES (?, ?)
                                        ");

                                        if (!$insertVote) {
                                            throw new Exception("Unable to prepare vote.");
                                        }

                                        $insertVote->bind_param(
                                            "si",
                                            $voter_id,
                                            $contestant_id
                                        );

                                        if (!$insertVote->execute()) {

                                            throw new Exception("Unable to record vote.");

                                        }

                                        /*
                                         * 12. Mark voter as having voted
                                         */
                                        $updateVoter = $conn->prepare("
                                            UPDATE voters
                                            SET has_voted = 1
                                            WHERE unique_id = ?
                                        ");

                                        if (!$updateVoter) {
                                            throw new Exception("Unable to update voter status.");
                                        }

                                        $updateVoter->bind_param("s", $voter_id);

                                        if (!$updateVoter->execute()) {

                                            throw new Exception("Unable to update voter status.");

                                        }

                                        /*
                                         * 13. Confirm both operations permanently
                                         */
                                        $conn->commit();

                                        $message = "✅ Your vote has been cast successfully!";

                                        $updateVoter->close();
                                        $insertVote->close();
                                    }

                                    $voteCheck->close();
                                }

                                $lockVoter->close();

                            } catch (Exception $e) {

                                /*
                                 * If anything fails, undo everything.
                                 */
                                $conn->rollback();

                                $message = "❌ Error casting vote. Please try again.";
                            }
                        }

                        $contestantStmt->close();
                    }
                }
            } else {

                $message = "❌ Voter ID not found. Please make sure you entered your registered Voter ID correctly.";
            }

            $stmt->close();
        }
    }
}


// Fetch contestants
$contestants = $conn->query("
    SELECT id, name, party
    FROM contestants
    ORDER BY name ASC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>

  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Cast Your Vote | Online Voting System</title>

  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
  >

  <link rel="stylesheet" href="style.css?v=4">

</head>

<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top custom-navbar">

  <div class="container-fluid">

    <a class="navbar-brand fw-bold text-light" href="#">
      🗳 Online Voting System
    </a>

    <button
      class="navbar-toggler border-0"
      type="button"
      data-bs-toggle="collapse"
      data-bs-target="#navbarNav"
    >
      <span class="navbar-toggler-icon"></span>
    </button>

    <div
      class="collapse navbar-collapse justify-content-center"
      id="navbarNav"
    >

      <ul class="navbar-nav text-center">

        <li class="nav-item">
          <a class="nav-link" href="index.php">Home</a>
        </li>

        <li class="nav-item">
          <a class="nav-link" href="register_contestant.php">
            Register Contestant
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link" href="register_voter.php">
            Register Voter
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link active" href="vote.php">
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


<!-- Voting Form -->

<div class="container form-container mt-5 pt-5">

  <div class="card shadow-lg border-0">

    <div class="card-header text-center bg-light">

      <h3 class="text-primary mb-0">
        Cast Your Vote
      </h3>

    </div>


    <div class="card-body bg-white">

      <?php if ($message != ""): ?>

        <div class="alert alert-info text-center fw-semibold">
          <?= htmlspecialchars($message) ?>
        </div>

      <?php endif; ?>


      <form method="POST" class="row g-3">

        <div class="col-md-6">

          <label class="form-label fw-semibold text-primary">
            Username
          </label>

          <input
            type="text"
            name="username"
            class="form-control"
            placeholder="Enter Username"
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
            required
          >

        </div>


        <div class="col-md-6">

          <label class="form-label fw-semibold text-primary">
            Password
          </label>

          <input
            type="password"
            name="password"
            class="form-control"
            placeholder="Enter Password"
            required
          >

        </div>


        <div class="col-md-6">

          <label class="form-label fw-semibold text-primary">
            Repeat Password
          </label>

          <input
            type="password"
            name="rpassword"
            class="form-control"
            placeholder="Repeat Password"
            required
          >

        </div>


        <div class="col-md-6">

          <label class="form-label fw-semibold text-primary">
            Voter ID
          </label>

          <input
            type="text"
            name="voter_id"
            class="form-control"
            placeholder="Enter Your Voter ID"
            required
          >

        </div>


        <div class="col-md-6">

          <label class="form-label fw-semibold text-primary">
            Select Contestant
          </label>

          <select
            name="contestant_id"
            class="form-select"
            required
          >

            <option value="">
              -- Select Contestant --
            </option>

            <?php while ($row = $contestants->fetch_assoc()): ?>

              <option value="<?= (int)$row['id']; ?>">

                <?= htmlspecialchars($row['name']) ?>
                (<?= htmlspecialchars($row['party']) ?>)

              </option>

            <?php endwhile; ?>

          </select>

        </div>


        <div class="text-center mt-4">

          <button
            type="submit"
            class="btn btn-primary px-5"
          >
            Submit Vote
          </button>

        </div>

      </form>

    </div>

  </div>

</div>


<!-- Footer -->

<footer class="text-center py-3 text-white mt-auto">

  <p class="mb-0">
    &copy; <?= date('Y'); ?> Online Voting System
  </p>

</footer>


<script
  src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>

</body>

</html>