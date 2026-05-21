<?php
include('../../app/middleware/admin.php');
include('./includes/header.php');
include('./includes/topbar.php');
include('./includes/sidebar.php');
include('../../app/config/config.php');

$sql = "SELECT s.id, s.title, s.description, s.status, s.created_at,
               COUNT(DISTINCT r.user_id) as response_count
        FROM surveys s
        LEFT JOIN responses r ON r.survey_id = s.id
        GROUP BY s.id
        ORDER BY s.created_at DESC";
$result = mysqli_query($conn, $sql);

$surveys = [];
$statTotal = 0;
$statPublished = 0;
$statPending = 0;
$statClosed = 0;
$totalResponses = 0;
while ($row = mysqli_fetch_assoc($result)) {
  $surveys[] = $row;
  $statTotal++;
  $totalResponses += (int)$row['response_count'];
  if ($row['status'] === 'published') $statPublished++;
  elseif ($row['status'] === 'pending') $statPending++;
  elseif ($row['status'] === 'closed') $statClosed++;
}

$usersRes = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM users WHERE role = 'user'");
$totalStudents = (int)mysqli_fetch_assoc($usersRes)['cnt'];
$activeCount = 0;
$inactiveCount = 0;
$studentResult = mysqli_query($conn, "SELECT id FROM users WHERE role = 'user'");
while ($s = mysqli_fetch_assoc($studentResult)) {
  $uid = (int)$s['id'];
  $check = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM responses WHERE user_id = $uid");
  $row = mysqli_fetch_assoc($check);
  if ($row['cnt'] > 0) $activeCount++;
  else $inactiveCount++;
}

$qRes = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM questions");
$totalQuestions = (int)mysqli_fetch_assoc($qRes)['cnt'];
$recentSurveys = array_slice($surveys, 0, 5);
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

  .main-wrapper {
    padding: 2.5rem 2.75rem 4rem;
    max-width: 1440px;
    margin: 0 auto;
    width: 100%;
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
    gap: 14px;
    margin-bottom: 2.25rem;
  }

  .stat-strip-3 {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
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

  .stat-card.gold-acc::before {
    background: var(--gold);
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

  .two-col {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 2.25rem;
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

  .badge-closed {
    background: var(--rose-lt);
    color: var(--rose);
  }

  .badge-closed::before {
    background: var(--rose);
  }

  .badge-pending {
    background: var(--gold-light);
    color: var(--gold-dark);
  }

  .badge-pending::before {
    background: var(--gold);
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

  .nav-cards {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 14px;
    margin-bottom: 2.5rem;
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

  .nav-icon {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .nav-icon.gold {
    background: var(--gold-light);
  }

  .nav-icon.teal {
    background: var(--teal-lt);
  }

  .nav-icon.rose {
    background: var(--rose-lt);
  }

  .nav-icon svg {
    width: 20px;
    height: 20px;
    fill: none;
    stroke-width: 1.8;
  }

  .nav-icon.gold svg {
    stroke: var(--gold-dark);
  }

  .nav-icon.teal svg {
    stroke: var(--teal);
  }

  .nav-icon.rose svg {
    stroke: var(--rose);
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

  .divider {
    border: none;
    border-top: 1.5px solid var(--paper-3);
    margin: 2.25rem 0;
  }

  @media (max-width:1100px) {
    .two-col {
      grid-template-columns: 1fr;
    }

    .nav-cards {
      grid-template-columns: 1fr 1fr;
    }
  }

  @media (max-width:900px) {
    .main-wrapper {
      padding: 1.5rem 1.25rem 3rem;
    }

    .stat-strip-4 {
      grid-template-columns: repeat(2, 1fr);
    }

    .stat-strip-3 {
      grid-template-columns: repeat(2, 1fr);
    }

    .nav-cards {
      grid-template-columns: 1fr;
    }
  }
</style>

<div class="main-wrapper">
  <div class="breadcrumbs"><span class="current-page">Dashboard Overview</span></div>
  <header>
    <div>
      <h1>Admin <em>Overview</em></h1>
      <p class="header-sub">A snapshot of surveys, students, and response analytics.</p>
    </div>
  </header>

  <div class="nav-cards">
    <a href="surveymanagement.php" class="nav-card">
      <div class="nav-icon gold"><svg viewBox="0 0 24 24">
          <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2" />
          <rect x="9" y="3" width="6" height="4" rx="1" />
          <line x1="9" y1="12" x2="15" y2="12" />
          <line x1="9" y1="16" x2="13" y2="16" />
        </svg></div>
      <div class="nav-card-body">
        <div class="nav-card-title">Survey Management</div>
        <div class="nav-card-sub"><?= $statTotal ?> surveys · <?= $statPublished ?> live</div>
      </div>
      <div class="nav-card-arrow"><svg viewBox="0 0 24 24">
          <polyline points="9 18 15 12 9 6" />
        </svg></div>
    </a>
    <a href="students.php" class="nav-card">
      <div class="nav-icon teal"><svg viewBox="0 0 24 24">
          <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
          <circle cx="9" cy="7" r="4" />
          <path d="M23 21v-2a4 4 0 00-3-3.87" />
          <path d="M16 3.13a4 4 0 010 7.75" />
        </svg></div>
      <div class="nav-card-body">
        <div class="nav-card-title">Registered Students</div>
        <div class="nav-card-sub"><?= $totalStudents ?> students · <?= $activeCount ?> active</div>
      </div>
      <div class="nav-card-arrow"><svg viewBox="0 0 24 24">
          <polyline points="9 18 15 12 9 6" />
        </svg></div>
    </a>
    <a href="results.php" class="nav-card">
      <div class="nav-icon rose"><svg viewBox="0 0 24 24">
          <line x1="18" y1="20" x2="18" y2="10" />
          <line x1="12" y1="20" x2="12" y2="4" />
          <line x1="6" y1="20" x2="6" y2="14" />
        </svg></div>
      <div class="nav-card-body">
        <div class="nav-card-title">Data Analytics</div>
        <div class="nav-card-sub"><?= number_format($totalResponses) ?> submissions · <?= $totalQuestions ?> questions</div>
      </div>
      <div class="nav-card-arrow"><svg viewBox="0 0 24 24">
          <polyline points="9 18 15 12 9 6" />
        </svg></div>
    </a>
  </div>

  <hr class="divider">

  <div class="section-label">
    <h2>Survey <em>Management</em></h2>
    <a href="surveymanagement.php" class="section-link">Manage surveys <svg viewBox="0 0 24 24">
        <polyline points="9 18 15 12 9 6" />
      </svg></a>
  </div>
  <div class="stat-strip-4">
    <div class="stat-card gold">
      <div class="stat-label">Total Surveys</div>
      <div class="stat-value"><?= $statTotal ?></div>
      <div class="stat-sub">All time</div>
    </div>
    <div class="stat-card teal">
      <div class="stat-label">Published</div>
      <div class="stat-value"><?= $statPublished ?></div>
      <div class="stat-sub">Currently running</div>
    </div>
    <div class="stat-card gold-acc">
      <div class="stat-label">Pending</div>
      <div class="stat-value"><?= $statPending ?></div>
      <div class="stat-sub">Awaiting launch</div>
    </div>
    <div class="stat-card rose">
      <div class="stat-label">Closed</div>
      <div class="stat-value"><?= $statClosed ?></div>
      <div class="stat-sub">Completed</div>
    </div>
  </div>

  <hr class="divider">

  <div class="two-col">
    <div>
      <div class="section-label">
        <h2>Registered <em>Students</em></h2>
        <a href="students.php" class="section-link">View all <svg viewBox="0 0 24 24">
            <polyline points="9 18 15 12 9 6" />
          </svg></a>
      </div>
      <div class="stat-strip-3">
        <div class="stat-card gold">
          <div class="stat-label">Total</div>
          <div class="stat-value"><?= $totalStudents ?></div>
          <div class="stat-sub">Registered users</div>
        </div>
        <div class="stat-card teal">
          <div class="stat-label">Active</div>
          <div class="stat-value"><?= $activeCount ?></div>
          <div class="stat-sub">At least one response</div>
        </div>
        <div class="stat-card rose">
          <div class="stat-label">Inactive</div>
          <div class="stat-value"><?= $inactiveCount ?></div>
          <div class="stat-sub">No responses yet</div>
        </div>
      </div>
    </div>
    <div>
      <div class="section-label">
        <h2>Data <em>Analytics</em></h2>
        <a href="results.php" class="section-link">View all <svg viewBox="0 0 24 24">
            <polyline points="9 18 15 12 9 6" />
          </svg></a>
      </div>
      <div class="stat-strip-3">
        <div class="stat-card gold">
          <div class="stat-label">Submissions</div>
          <div class="stat-value"><?= number_format($totalResponses) ?></div>
          <div class="stat-sub">Out of <?= $totalStudents ?> students</div>
        </div>
        <div class="stat-card teal">
          <div class="stat-label">Open Surveys</div>
          <div class="stat-value"><?= $statPublished ?></div>
          <div class="stat-sub">Collecting data</div>
        </div>
        <div class="stat-card rose">
          <div class="stat-label">Questions</div>
          <div class="stat-value"><?= number_format($totalQuestions) ?></div>
          <div class="stat-sub">Across all templates</div>
        </div>
      </div>
    </div>
  </div>

  <hr class="divider">

  <div class="section-label" style="margin-bottom:14px;">
    <h2>Recent <em>Surveys</em></h2>
    <a href="surveymanagement.php" class="section-link">See all <svg viewBox="0 0 24 24">
        <polyline points="9 18 15 12 9 6" />
      </svg></a>
  </div>
  <div class="table-wrap" style="margin-bottom:2.25rem;">
    <div class="table-scroll">
      <table>
        <thead>
          <tr>
            <th>Survey Title</th>
            <th>Responses</th>
            <th>Response Rate</th>
            <th>Date Created</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($recentSurveys)): ?>
            <tr>
              <td colspan="5" style="text-align:center;padding:2rem;color:var(--ink-3);">No surveys yet.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($recentSurveys as $s):
              $responses = (int)$s['response_count'];
              $pct = $totalStudents > 0 ? round(($responses / $totalStudents) * 100) : 0;
              $dateLabel = date('M d, Y', strtotime($s['created_at']));
            ?>
              <tr>
                <td>
                  <div class="survey-title"><?= htmlspecialchars($s['title']) ?></div>
                  <?php if (!empty($s['description'])): ?><div class="survey-desc"><?= htmlspecialchars($s['description']) ?></div><?php endif; ?>
                </td>
                <td style="font-weight:600;color:var(--ink);"><?= number_format($responses) ?> / <?= $totalStudents ?></td>
                <td>
                  <div class="prog-wrap">
                    <div class="prog-track">
                      <div class="prog-fill" style="width:<?= $pct ?>%;"></div>
                    </div>
                    <span class="prog-num"><?= $pct ?>%</span>
                  </div>
                </td>
                <td style="color:var(--ink-3);font-size:13px;"><?= $dateLabel ?></td>
                <td>
                  <?php if ($s['status'] === 'published'): ?>
                    <span class="badge badge-published">Published</span>
                  <?php elseif ($s['status'] === 'pending'): ?>
                    <span class="badge badge-pending">Pending</span>
                  <?php else: ?>
                    <span class="badge badge-closed">Closed</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</div><!-- end .main-wrapper -->

</main><!-- closes <main> opened by sidebar.php -->

<?php include('./includes/footer.php'); ?>