<?php
include('../../app/middleware/user.php');
include('./includes/header.php');
include('./includes/topbar.php');
include('./includes/sidebar.php');
include('../../app/config/config.php');

$user_id = $_SESSION['authUser']['user_id'];

// Total surveys (published only)
$surveyQuery   = mysqli_query($conn, "SELECT COUNT(*) as total FROM surveys WHERE status='published'");
$totalSurveys  = (int)mysqli_fetch_assoc($surveyQuery)['total'];

// Answered surveys
$answeredQuery   = mysqli_query($conn, "SELECT COUNT(DISTINCT survey_id) as total FROM responses WHERE user_id = $user_id");
$answeredSurveys = (int)mysqli_fetch_assoc($answeredQuery)['total'];

// Not answered
// Not answered (published surveys this user hasn't responded to yet)
$notAnsweredQuery = mysqli_query($conn, "
    SELECT COUNT(*) as total
    FROM surveys s
    WHERE s.status = 'published'
      AND s.id NOT IN (
          SELECT DISTINCT survey_id FROM responses WHERE user_id = $user_id
      )
");
$notAnswered = (int)mysqli_fetch_assoc($notAnsweredQuery)['total'];
// Total responses made
$responseQuery  = mysqli_query($conn, "SELECT COUNT(*) as total FROM responses WHERE user_id = $user_id");
$totalResponses = (int)mysqli_fetch_assoc($responseQuery)['total'];

// Recent surveys the user has answered
$recentQuery = mysqli_query($conn, "
    SELECT s.id, s.title, s.description, s.status, s.created_at,
           COUNT(r.id) as response_count
    FROM surveys s
    INNER JOIN responses r ON r.survey_id = s.id AND r.user_id = $user_id
    GROUP BY s.id
    ORDER BY MAX(r.submitted_at) DESC
    LIMIT 5
");
$recentSurveys = [];
while ($row = mysqli_fetch_assoc($recentQuery)) {
  $recentSurveys[] = $row;
}

// Pending surveys (published, not yet answered)
$pendingQuery = mysqli_query($conn, "
    SELECT s.id, s.title, s.description, s.created_at
    FROM surveys s
    WHERE s.status = 'published'
      AND s.id NOT IN (
          SELECT DISTINCT survey_id FROM responses WHERE user_id = $user_id
      )
    ORDER BY s.created_at DESC
    LIMIT 5
");
$pendingSurveys = [];
while ($row = mysqli_fetch_assoc($pendingQuery)) {
  $pendingSurveys[] = $row;
}
?>

<style>
  @import url('https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@300;400;500;600&display=swap');

  :root {
    --ink: #0f0e0d;
    --ink-2: #3a3835;
    --ink-3: #7a776f;
    --paper: #f7f5f0;
    --paper-2: #eceae3;
    --paper-3: #e0ddd4;
    --gold: #c9972b;
    --gold-light: #f5e9cc;
    --gold-dark: #8a6318;
    --teal: #1b6b6b;
    --teal-lt: #d0eaea;
    --rose: #a02c2c;
    --rose-lt: #f5dede;
    --radius: 10px;
    --shadow: 0 2px 16px rgba(15, 14, 13, 0.07);
  }

  * {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
  }

  body {
    font-family: 'DM Sans', sans-serif;
    background: var(--paper);
    color: var(--ink);
  }

  #main {
    margin-left: 260px;
    padding: 2.5rem 2.75rem 4rem;
    min-height: 100vh;
    background: var(--paper);
    transition: margin-left 0.3s;
  }

  body.sidebar-hidden #main {
    margin-left: 0;
  }

  .breadcrumbs {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12.5px;
    font-weight: 500;
    letter-spacing: .04em;
    text-transform: uppercase;
    color: var(--ink-3);
    margin-bottom: 2rem;
  }

  .breadcrumbs .current-page {
    color: var(--ink-2);
  }

  header {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    margin-bottom: 2.25rem;
    gap: 1rem;
    flex-wrap: wrap;
  }

  header h1 {
    font-family: 'DM Serif Display', serif;
    font-size: clamp(2rem, 4vw, 3rem);
    font-weight: 400;
    line-height: 1;
    color: var(--ink);
  }

  header h1 em {
    font-style: italic;
    color: var(--gold);
  }

  .header-sub {
    font-size: 13.5px;
    color: var(--ink-3);
    margin-top: 6px;
  }

  .section-label {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 12px;
  }

  .section-label h2 {
    font-family: 'DM Serif Display', serif;
    font-size: 1.2rem;
    font-weight: 400;
    color: var(--ink);
  }

  .section-label h2 em {
    font-style: italic;
    color: var(--gold);
  }

  .section-link {
    font-size: 12.5px;
    font-weight: 600;
    color: var(--gold-dark);
    text-decoration: none;
    letter-spacing: .04em;
    display: flex;
    align-items: center;
    gap: 4px;
    transition: color .15s;
  }

  .section-link:hover {
    color: var(--gold);
  }

  .section-link svg {
    width: 12px;
    height: 12px;
    stroke: currentColor;
    fill: none;
    stroke-width: 2.5;
  }

  .stat-strip-4 {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-bottom: 2.25rem;
  }

  .stat-strip-3 {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 14px;
    margin-bottom: 2.25rem;
  }

  .stat-strip-2 {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 14px;
    margin-bottom: 2.25rem;
  }

  .stat-card {
    background: var(--paper-2);
    border: 1.5px solid var(--paper-3);
    border-radius: var(--radius);
    padding: 1.1rem 1.25rem;
    position: relative;
    overflow: hidden;
    transition: border-color .15s, box-shadow .15s;
    text-decoration: none;
    display: block;
    color: inherit;
  }

  .stat-card:hover {
    border-color: var(--gold);
    box-shadow: var(--shadow);
  }

  .stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: var(--paper-3);
    border-radius: 2px 2px 0 0;
  }

  .stat-card.gold::before {
    background: var(--gold);
  }

  .stat-card.teal::before {
    background: var(--teal);
  }

  .stat-card.rose::before {
    background: var(--rose);
  }

  .stat-label {
    font-size: 11px;
    font-weight: 600;
    letter-spacing: .08em;
    text-transform: uppercase;
    color: var(--ink-3);
    margin-bottom: 8px;
  }

  .stat-value {
    font-family: 'DM Serif Display', serif;
    font-size: 2rem;
    line-height: 1;
    color: var(--ink);
  }

  .stat-sub {
    font-size: 12px;
    color: var(--ink-3);
    margin-top: 5px;
  }

  .nav-cards {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
    margin-bottom: 2.25rem;
  }

  .nav-card {
    background: #fff;
    border: 1.5px solid var(--paper-3);
    border-radius: var(--radius);
    padding: 1.25rem 1.4rem;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 14px;
    transition: border-color .15s, box-shadow .15s, transform .1s;
    box-shadow: var(--shadow);
  }

  .nav-card:hover {
    border-color: var(--gold);
    box-shadow: 0 4px 24px rgba(15, 14, 13, .1);
    transform: translateY(-1px);
  }

  .nav-icon-box {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .nav-icon-box.gold {
    background: var(--gold-light);
  }

  .nav-icon-box.teal {
    background: var(--teal-lt);
  }

  .nav-icon-box svg {
    width: 20px;
    height: 20px;
    fill: none;
    stroke-width: 1.8;
  }

  .nav-icon-box.gold svg {
    stroke: var(--gold-dark);
  }

  .nav-icon-box.teal svg {
    stroke: var(--teal);
  }

  .nav-card-body {
    flex: 1;
  }

  .nav-card-title {
    font-weight: 600;
    font-size: 14px;
    color: var(--ink);
  }

  .nav-card-sub {
    font-size: 12px;
    color: var(--ink-3);
    margin-top: 2px;
  }

  .nav-card-arrow svg {
    width: 14px;
    height: 14px;
    stroke: var(--ink-3);
    fill: none;
    stroke-width: 2;
  }

  .table-wrap {
    background: #fff;
    border: 1.5px solid var(--paper-3);
    border-radius: var(--radius);
    overflow: hidden;
    box-shadow: var(--shadow);
  }

  .table-scroll {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }

  table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13.5px;
  }

  thead tr {
    background: var(--paper-2);
    border-bottom: 1.5px solid var(--paper-3);
  }

  th {
    padding: 10px 16px;
    text-align: left;
    font-size: 10.5px;
    font-weight: 600;
    letter-spacing: .08em;
    text-transform: uppercase;
    color: var(--ink-3);
    white-space: nowrap;
  }

  tbody tr {
    border-bottom: 1px solid var(--paper-2);
    transition: background .1s;
  }

  tbody tr:last-child {
    border-bottom: none;
  }

  tbody tr:hover {
    background: var(--paper);
  }

  td {
    padding: 11px 16px;
    color: var(--ink-2);
    vertical-align: middle;
  }

  .survey-title {
    font-weight: 500;
    color: var(--ink);
    font-size: 13.5px;
  }

  .survey-desc {
    font-size: 12px;
    color: var(--ink-3);
  }

  .badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 9px;
    border-radius: 20px;
    font-size: 11.5px;
    font-weight: 600;
    white-space: nowrap;
  }

  .badge::before {
    content: '';
    width: 5px;
    height: 5px;
    border-radius: 50%;
    flex-shrink: 0;
  }

  .badge-published {
    background: var(--teal-lt);
    color: var(--teal);
  }

  .badge-published::before {
    background: var(--teal);
    animation: pulse 1.5s infinite;
  }

  .badge-pending {
    background: var(--gold-light);
    color: var(--gold-dark);
  }

  .badge-pending::before {
    background: var(--gold);
  }

  .badge-closed {
    background: var(--rose-lt);
    color: var(--rose);
  }

  .badge-closed::before {
    background: var(--rose);
  }

  .badge-answered {
    background: var(--teal-lt);
    color: var(--teal);
  }

  .badge-answered::before {
    background: var(--teal);
  }

  @keyframes pulse {

    0%,
    100% {
      opacity: 1;
      transform: scale(1);
    }

    50% {
      opacity: .4;
      transform: scale(.8);
    }
  }

  .two-col {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
    margin-bottom: 2.25rem;
  }

  .divider {
    border: none;
    border-top: 1.5px solid var(--paper-3);
    margin: 2.25rem 0;
  }

  @media (max-width: 900px) {
    .stat-strip-4 {
      grid-template-columns: repeat(2, 1fr);
    }

    .stat-strip-3 {
      grid-template-columns: repeat(2, 1fr);
    }

    .nav-cards {
      grid-template-columns: 1fr;
    }

    .two-col {
      grid-template-columns: 1fr;
    }
  }
</style>

<div class="breadcrumbs">
  <span class="current-page">My Dashboard</span>
</div>

<header>
  <div>
    <h1>My <em>Dashboard</em></h1>
    <p class="header-sub">A snapshot of your survey activity and progress.</p>
  </div>
</header>

<!-- Quick Navigation -->
<div class="nav-cards">
  <a href="mysurvey.php" class="nav-card">
    <div class="nav-icon-box gold">
      <svg viewBox="0 0 24 24">
        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2" />
        <rect x="9" y="3" width="6" height="4" rx="1" />
        <line x1="9" y1="12" x2="15" y2="12" />
        <line x1="9" y1="16" x2="13" y2="16" />
      </svg>
    </div>
    <div class="nav-card-body">
      <div class="nav-card-title">My Surveys</div>
      <div class="nav-card-sub"><?= $totalSurveys ?> available · <?= $notAnswered ?> pending</div>
    </div>
    <div class="nav-card-arrow"><svg viewBox="0 0 24 24">
        <polyline points="9 18 15 12 9 6" />
      </svg></div>
  </a>
  <a href="results.php" class="nav-card">
    <div class="nav-icon-box teal">
      <svg viewBox="0 0 24 24">
        <line x1="18" y1="20" x2="18" y2="10" />
        <line x1="12" y1="20" x2="12" y2="4" />
        <line x1="6" y1="20" x2="6" y2="14" />
      </svg>
    </div>
    <div class="nav-card-body">
      <div class="nav-card-title">My Results</div>
      <div class="nav-card-sub"><?= $totalResponses ?> responses submitted</div>
    </div>
    <div class="nav-card-arrow"><svg viewBox="0 0 24 24">
        <polyline points="9 18 15 12 9 6" />
      </svg></div>
  </a>
</div>

<hr class="divider">

<!-- Activity Stats -->
<div class="section-label">
  <h2>My <em>Activity</em></h2>
</div>
<div class="stat-strip-4">
  <div class="stat-card gold">
    <div class="stat-label">Available Surveys</div>
    <div class="stat-value"><?= $totalSurveys ?></div>
    <div class="stat-sub">Published surveys</div>
  </div>
  <div class="stat-card teal">
    <div class="stat-label">Answered</div>
    <div class="stat-value"><?= $answeredSurveys ?></div>
    <div class="stat-sub">Completed surveys</div>
  </div>
  <div class="stat-card rose">
    <div class="stat-label">Pending</div>
    <div class="stat-value"><?= $notAnswered ?></div>
    <div class="stat-sub">Not answered yet</div>
  </div>
  <div class="stat-card gold">
    <div class="stat-label">My Responses</div>
    <div class="stat-value"><?= $totalResponses ?></div>
    <div class="stat-sub">Total answers submitted</div>
  </div>
</div>

<hr class="divider">

<!-- Two column: Recently Answered + Pending -->
<div class="two-col">

  <!-- Recently Answered -->
  <div>
    <div class="section-label">
      <h2>Recently <em>Answered</em></h2>
      <a href="results.php" class="section-link">
        View all
        <svg viewBox="0 0 24 24">
          <polyline points="9 18 15 12 9 6" />
        </svg>
      </a>
    </div>
    <div class="table-wrap">
      <div class="table-scroll">
        <table>
          <thead>
            <tr>
              <th>Survey</th>
              <th>Responses</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($recentSurveys)): ?>
              <tr>
                <td colspan="3" style="text-align:center;padding:2rem;color:var(--ink-3);">No answered surveys yet.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($recentSurveys as $s): ?>
                <tr>
                  <td>
                    <div class="survey-title"><?= htmlspecialchars($s['title']) ?></div>
                    <?php if (!empty($s['description'])): ?>
                      <div class="survey-desc"><?= htmlspecialchars($s['description']) ?></div>
                    <?php endif; ?>
                  </td>
                  <td style="font-weight:600;color:var(--ink);"><?= (int)$s['response_count'] ?></td>
                  <td><span class="badge badge-answered">Answered</span></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Pending Surveys -->
  <div>
    <div class="section-label">
      <h2>Pending <em>Surveys</em></h2>
      <a href="mysurvey.php" class="section-link">
        Take a survey
        <svg viewBox="0 0 24 24">
          <polyline points="9 18 15 12 9 6" />
        </svg>
      </a>
    </div>
    <div class="table-wrap">
      <div class="table-scroll">
        <table>
          <thead>
            <tr>
              <th>Survey</th>
              <th>Date Added</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($pendingSurveys)): ?>
              <tr>
                <td colspan="3" style="text-align:center;padding:2rem;color:var(--ink-3);">All surveys answered!</td>
              </tr>
            <?php else: ?>
              <?php foreach ($pendingSurveys as $s): ?>
                <tr>
                  <td>
                    <div class="survey-title"><?= htmlspecialchars($s['title']) ?></div>
                    <?php if (!empty($s['description'])): ?>
                      <div class="survey-desc"><?= htmlspecialchars($s['description']) ?></div>
                    <?php endif; ?>
                  </td>
                  <td style="color:var(--ink-3);font-size:13px;"><?= date('M d, Y', strtotime($s['created_at'])) ?></td>
                  <td><span class="badge badge-published">Open</span></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>

<?php include('./includes/footer.php'); ?>