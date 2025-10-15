<?php
include 'db.php';
// Fetch results
$results = $conn->query("
    SELECT c.name, c.party, COUNT(v.id) as votes
    FROM contestants c
    LEFT JOIN votes v ON c.id = v.contestant_id
    GROUP BY c.id
    ORDER BY votes DESC
");
$contestants = [];
$votes = [];
while ($row = $results->fetch_assoc()) {
    $contestants[] = $row['name'] . " (" . $row['party'] . ")";
    $votes[] = $row['votes'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View Results | Online Voting System</title>
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
        <li class="nav-item"><a class="nav-link" href="register_voter.php">Register Voter</a></li>
        <li class="nav-item"><a class="nav-link" href="vote.php">Cast Vote</a></li>
        <li class="nav-item"><a class="nav-link active" href="results.php">View Results</a></li>
      </ul>
    </div>
  </div>
</nav>

<!--Results Table -->
<div class="container results-container">
  <h2 class="page-title">Election Results</h2>

  <div class="table-responsive mt-4">
    <table class="table table-bordered table-striped align-middle">
      <thead>
        <tr>
          <th>Contestant</th>
          <th>Party</th>
          <th>Votes</th>
        </tr>
      </thead>
      <tbody>
        <?php
        include 'db.php';
        $results = $conn->query("
            SELECT c.name, c.party, COUNT(v.id) as votes
            FROM contestants c
            LEFT JOIN votes v ON c.id = v.contestant_id
            GROUP BY c.id
            ORDER BY votes DESC
        ");
        while($row = $results->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['name']); ?></td>
          <td><?= htmlspecialchars($row['party']); ?></td>
          <td><?= $row['votes']; ?></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <!--Chart Section -->
  <div class="card shadow-sm mt-5">
    <div class="card-body text-center">
      <h4 class="text-primary mb-3">Vote Distribution Chart</h4>
      <div class="chart-toggle mb-3">
        <button id="barChartBtn" class="btn btn-primary me-2">Bar Chart</button>
        <button id="pieChartBtn" class="btn btn-outline-primary">Pie Chart</button>
      </div>
      <div class="chart-container">
        <canvas id="resultsChart"></canvas>
      </div>
    </div>
  </div>
</div>

<!--Footer -->
<footer class="text-center py-3 text-white mt-5 footer-custom">
  <p class="mb-0">&copy; <?= date('Y'); ?> Online Voting System</p>
</footer>

<!--Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const ctx = document.getElementById('resultsChart');
let chartType = 'bar';
const chartData = {
  labels: <?= json_encode($contestants); ?>,
  datasets: [{
    label: 'Number of Votes',
    data: <?= json_encode($votes); ?>,
    backgroundColor: [
      'red', 'green', 'darkblue', 'yellow', 'cyan', 'violet'
    ],
    borderColor: '#003366',
    borderWidth: 2,
    borderRadius: 5
  }]
};
let resultsChart = new Chart(ctx, {
  type: chartType,
  data: chartData,
  options: {
    responsive: true,
    maintainAspectRatio: false,
    scales: {
      y: { beginAtZero: true, ticks: { stepSize: 1 } }
    },
    plugins: {
      legend: { display: true, position: 'top' },
      tooltip: { enabled: true }
    }
  }
});
document.getElementById('barChartBtn').addEventListener('click', () => switchChart('bar'));
document.getElementById('pieChartBtn').addEventListener('click', () => switchChart('pie'));

function switchChart(type) {
  if (chartType !== type) {
    chartType = type;
    resultsChart.destroy();
    resultsChart = new Chart(ctx, {
      type: chartType,
      data: chartData,
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: true, position: 'top' } }
      }
    });

    // Button active state
    document.getElementById('barChartBtn').classList.toggle('btn-primary', type === 'bar');
    document.getElementById('barChartBtn').classList.toggle('btn-outline-primary', type !== 'bar');
    document.getElementById('pieChartBtn').classList.toggle('btn-primary', type === 'pie');
    document.getElementById('pieChartBtn').classList.toggle('btn-outline-primary', type !== 'pie');
  }
}
</script>
</body>
</html>