<?php
include('../../app/middleware/user.php');
include('./includes/header.php');
include('./includes/topbar.php');
include('./includes/sidebar.php');
include('../../app/config/config.php');

$user_id = $_SESSION['authUser']['user_id'];

// Total surveys (published only)
$surveyQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM surveys WHERE status='published'");
$totalSurveys = mysqli_fetch_assoc($surveyQuery)['total'];

// Answered surveys
$answeredQuery = mysqli_query($conn, "SELECT COUNT(DISTINCT survey_id) as total FROM responses WHERE user_id = $user_id");
$answeredSurveys = mysqli_fetch_assoc($answeredQuery)['total'];

// Not answered
$notAnswered = $totalSurveys - $answeredSurveys;

// Total responses made
$responseQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM responses WHERE user_id = $user_id");
$totalResponses = mysqli_fetch_assoc($responseQuery)['total'];
?>

<style>
  @import url('https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=DM+Sans:wght@300;400;500;600&display=swap');

  :root {
    --ink:#0f0e0d;
    --ink-3:#7a776f;
    --paper:#f7f5f0;
    --paper-2:#eceae3;
    --paper-3:#e0ddd4;
    --gold:#c9972b;
    --teal:#1b6b6b;
    --rose:#a02c2c;
    --radius:10px;
    --shadow:0 2px 16px rgba(15,14,13,.07);
  }

  * { box-sizing:border-box; margin:0; padding:0; }
  body { font-family:'DM Sans',sans-serif; background:var(--paper); color:var(--ink); }

  .main-wrapper { padding:2.5rem; max-width:1440px; margin:auto; }

  header h1 {
    font-family:'DM Serif Display';
    font-size:2.5rem;
  }

  .stat-strip-3 {
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:14px;
    margin:1.5rem 0;
  }

  .stat-card {
    background:var(--paper-2);
    border:1.5px solid var(--paper-3);
    border-radius:var(--radius);
    padding:1.2rem;
    box-shadow:var(--shadow);
  }

  .stat-label {
    font-size:11px;
    text-transform:uppercase;
    letter-spacing:.08em;
    color:var(--ink-3);
  }

  .stat-value {
    font-family:'DM Serif Display';
    font-size:2rem;
    margin-top:5px;
  }

  .stat-sub { font-size:12px; color:var(--ink-3); }

  .section {
    margin-top:2rem;
  }

  .section h2 {
    font-family:'DM Serif Display';
    margin-bottom:10px;
  }

  .card-row {
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:14px;
  }

  .card {
    background:white;
    border:1.5px solid var(--paper-3);
    border-radius:var(--radius);
    padding:1.5rem;
    box-shadow:var(--shadow);
  }

  .btn {
    display:inline-block;
    padding:10px 14px;
    border-radius:8px;
    text-decoration:none;
    font-weight:600;
    margin-top:10px;
  }

  .btn-primary { background:var(--teal); color:white; }
  .btn-success { background:var(--gold); color:white; }
</style>

<div class="main-wrapper">

  <header>
    <h1>User <em>Dashboard</em></h1>
    <p style="color:var(--ink-3)">Your survey activity overview</p>
  </header>

  <!-- STATS (like admin design) -->
  <div class="stat-strip-3">

    <div class="stat-card">
      <div class="stat-label">Available Surveys</div>
      <div class="stat-value"><?= $totalSurveys ?></div>
      <div class="stat-sub">Published surveys</div>
    </div>

    <div class="stat-card">
      <div class="stat-label">Answered</div>
      <div class="stat-value"><?= $answeredSurveys ?></div>
      <div class="stat-sub">Completed surveys</div>
    </div>

    <div class="stat-card">
      <div class="stat-label">Pending</div>
      <div class="stat-value"><?= $notAnswered ?></div>
      <div class="stat-sub">Not answered yet</div>
    </div>

  </div>

  <div class="stat-strip-3">

    <div class="stat-card">
      <div class="stat-label">My Responses</div>
      <div class="stat-value"><?= $totalResponses ?></div>
      <div class="stat-sub">Total answers submitted</div>
    </div>

  </div>

  <!-- ACTION SECTION -->
  <div class="section">
    <h2>Quick Actions</h2>

    <div class="card-row">

      <div class="card">
        <h3>Take Surveys</h3>
        <p style="color:var(--ink-3)">Answer available surveys assigned to you.</p>
        <a href="mysurvey.php" class="btn btn-primary">Go to My Surveys</a>
      </div>

      <div class="card">
        <h3>View Results</h3>
        <p style="color:var(--ink-3)">Check your submitted answers and history.</p>
        <a href="results.php" class="btn btn-success">View My Results</a>
      </div>

    </div>
  </div>

</div>

<?php include('./includes/footer.php'); ?>