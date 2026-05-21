<?php
include('../../app/middleware/user.php');
include('./includes/header.php');
include('./includes/topbar.php');
include('./includes/sidebar.php');
include('../../app/config/config.php');

$user_id = $_SESSION['authUser']['user_id'];

// Get all surveys this user has responded to
$sql = "
    SELECT s.id, s.title, s.description, s.created_at,
           COUNT(DISTINCT q.id) as question_count,
           MAX(r.submitted_at) as last_submitted
    FROM surveys s
    INNER JOIN responses r ON r.survey_id = s.id AND r.user_id = $user_id
    LEFT JOIN questions q ON q.survey_id = s.id
    GROUP BY s.id
    ORDER BY MAX(r.submitted_at) DESC
";
$result = mysqli_query($conn, $sql);

$answeredSurveys = [];
while ($row = mysqli_fetch_assoc($result)) {
  $answeredSurveys[] = $row;
}

$totalAnswered  = count($answeredSurveys);

// Total responses submitted
$rQuery        = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM responses WHERE user_id = $user_id");
$totalResponses = (int)mysqli_fetch_assoc($rQuery)['cnt'];

// Total available surveys
$sQuery        = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM surveys WHERE status = 'published'");
$totalSurveys  = (int)mysqli_fetch_assoc($sQuery)['cnt'];

$pendingCount  = $totalSurveys - $totalAnswered;
?>

<style>
  @import url('https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@300;400;500;600&display=swap');

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

  /* Stat strip */
  .stat-strip-4 {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
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
    transition: border-color .15s, box-shadow .15s, transform .1s;
  }

  .stat-card:hover {
    border-color: var(--gold);
    box-shadow: var(--shadow);
    transform: translateY(-2px);
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

  /* Section label */
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

  /* Table */
  .table-wrap {
    background: #fff;
    border: 1.5px solid var(--paper-3);
    border-radius: var(--radius);
    overflow: hidden;
    box-shadow: var(--shadow);
    margin-bottom: 2.25rem;
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

  /* Progress bar */
  .prog-wrap {
    display: flex;
    align-items: center;
    gap: 8px;
    min-width: 110px;
  }

  .prog-track {
    flex: 1;
    height: 4px;
    background: var(--paper-2);
    border-radius: 3px;
    overflow: hidden;
  }

  .prog-fill {
    height: 100%;
    border-radius: 3px;
    background: var(--gold);
  }

  .prog-num {
    font-weight: 600;
    font-size: 12.5px;
    min-width: 34px;
    text-align: right;
    color: var(--ink);
  }

  /* Badge */
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

  .badge-answered {
    background: var(--teal-lt);
    color: var(--teal);
  }

  .badge-answered::before {
    background: var(--teal);
    animation: pulse 1.5s infinite;
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

  /* Empty state */
  .empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: #fff;
    border: 1.5px solid var(--paper-3);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
  }

  .empty-state i {
    font-size: 2.5rem;
    color: var(--paper-3);
    margin-bottom: 1rem;
    display: block;
  }

  .empty-state h3 {
    font-family: 'DM Serif Display', serif;
    font-size: 1.3rem;
    color: var(--ink);
    margin-bottom: 6px;
  }

  .empty-state p {
    font-size: 13.5px;
    color: var(--ink-3);
  }

  .btn-view {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 8px;
    background: var(--gold-light);
    color: var(--gold-dark);
    font-size: 12.5px;
    font-weight: 600;
    text-decoration: none;
    transition: background 0.15s;
    font-family: 'DM Sans', sans-serif;
  }

  .btn-view:hover {
    background: var(--gold);
    color: #fff;
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
  }
</style>

<div class="breadcrumbs">
  <a href="index" style="color:var(--ink-3);text-decoration:none;transition:color .15s;" onmouseover="this.style.color='var(--gold)'" onmouseout="this.style.color='var(--ink-3)'">Home</a>
  <span style="color:var(--paper-3);">/</span>
  <span class="current-page">My Results</span>
</div>

<header>
  <div>
    <h1>My <em>Results</em></h1>
    <p class="header-sub">Your survey submission history and activity.</p>
  </div>
</header>

<!-- Stats -->
<div class="stat-strip-4">
  <div class="stat-card gold">
    <div class="stat-label">Surveys Answered</div>
    <div class="stat-value"><?= $totalAnswered ?></div>
    <div class="stat-sub">Completed surveys</div>
  </div>
  <div class="stat-card teal">
    <div class="stat-label">Total Responses</div>
    <div class="stat-value"><?= $totalResponses ?></div>
    <div class="stat-sub">Answers submitted</div>
  </div>
  <div class="stat-card rose">
    <div class="stat-label">Pending</div>
    <div class="stat-value"><?= $pendingCount < 0 ? 0 : $pendingCount ?></div>
    <div class="stat-sub">Not answered yet</div>
  </div>
  <div class="stat-card gold">
    <div class="stat-label">Total Available</div>
    <div class="stat-value"><?= $totalSurveys ?></div>
    <div class="stat-sub">Published surveys</div>
  </div>
</div>

<hr class="divider">

<!-- Results Table -->
<div class="section-label">
  <h2>Answered <em>Surveys</em></h2>
  <a href="mysurvey.php" class="section-link">
    Take more
    <svg viewBox="0 0 24 24">
      <polyline points="9 18 15 12 9 6" />
    </svg>
  </a>
</div>

<?php if (empty($answeredSurveys)): ?>
  <div class="empty-state">
    <i class="bi bi-bar-chart"></i>
    <h3>No Results Yet</h3>
    <p>You haven't answered any surveys yet. Go take one!</p>
  </div>
<?php else: ?>
  <div class="table-wrap">
    <div class="table-scroll">
      <table>
        <thead>
          <tr>
            <th>Survey</th>
            <th>Questions</th>
            <th>Completion</th>
            <th>Last Submitted</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($answeredSurveys as $s):
            $qCount    = (int)$s['question_count'];
            $rCount    = (int)$totalResponses;
            $pct       = $qCount > 0 ? min(100, round(($totalResponses / $qCount) * 100)) : 100;
            $dateLabel = date('M d, Y h:i A', strtotime($s['last_submitted']));
          ?>
            <tr>
              <td>
                <div class="survey-title"><?= htmlspecialchars($s['title']) ?></div>
                <?php if (!empty($s['description'])): ?>
                  <div class="survey-desc"><?= htmlspecialchars($s['description']) ?></div>
                <?php endif; ?>
              </td>
              <td style="font-weight:600;color:var(--ink);"><?= $qCount ?></td>
              <td>
                <div class="prog-wrap">
                  <div class="prog-track">
                    <div class="prog-fill" style="width:100%;"></div>
                  </div>
                  <span class="prog-num">100%</span>
                </div>
              </td>
              <td style="color:var(--ink-3);font-size:13px;"><?= $dateLabel ?></td>
              <td><span class="badge badge-answered">Answered</span></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
<?php endif; ?>

<?php include('./includes/footer.php'); ?>