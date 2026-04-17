<?php
include('../../app/middleware/admin.php');
include('./includes/header.php');
include('./includes/topbar.php');
include('./includes/sidebar.php');
include('../../app/config/config.php');

// ── EXACT same query as surveymanagement.php ──────────────────────────────
$sql = "SELECT s.id, s.title, s.description, s.status, s.created_at, s.category_id,
               COUNT(DISTINCT r.user_id) as response_count
        FROM surveys s
        LEFT JOIN responses r ON r.survey_id = s.id
        GROUP BY s.id
        ORDER BY s.created_at DESC";
$result = mysqli_query($conn, $sql);

$surveys        = [];
$totalResponses = 0;
$openCount      = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $surveys[]       = $row;
    $totalResponses += (int)$row['response_count'];
    if ($row['status'] === 'published') $openCount++;
}

// ── EXACT same student count as surveymanagement.php ─────────────────────
$usersRes      = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM users WHERE role = 'user'");
$totalStudents = (int)mysqli_fetch_assoc($usersRes)['cnt'];

// ── Total questions across all surveys ────────────────────────────────────
$qRes           = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM questions");
$totalQuestions = (int)mysqli_fetch_assoc($qRes)['cnt'];
?>

<style>
  @import url('https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@300;400;500;600&display=swap');

  :root {
    --ink:        #0f0e0d;
    --ink-2:      #3a3835;
    --ink-3:      #7a776f;
    --paper:      #f7f5f0;
    --paper-2:    #eceae3;
    --paper-3:    #e0ddd4;
    --gold:       #c9972b;
    --gold-light: #f5e9cc;
    --gold-dark:  #8a6318;
    --teal:       #1b6b6b;
    --teal-lt:    #d0eaea;
    --rose:       #a02c2c;
    --rose-lt:    #f5dede;
    --radius:     10px;
    --shadow:     0 2px 16px rgba(15,14,13,0.07);
  }

  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: 'DM Sans', sans-serif; background: var(--paper); color: var(--ink); }

  .main-wrapper { padding: 2.5rem 2.75rem 4rem; max-width: 1440px; margin: 0 auto; width: 100%; }

  .breadcrumbs {
    display: flex; align-items: center; gap: 8px;
    font-size: 12.5px; font-weight: 500; letter-spacing: .04em;
    text-transform: uppercase; color: var(--ink-3); margin-bottom: 2rem;
  }
  .breadcrumbs a { color: var(--ink-3); text-decoration: none; transition: color .15s; }
  .breadcrumbs a:hover { color: var(--gold); }
  .breadcrumbs .current-page { color: var(--ink-2); }

  header {
    display: flex; align-items: flex-end; justify-content: space-between;
    margin-bottom: 2.25rem; gap: 1rem; flex-wrap: wrap;
  }
  header h1 {
    font-family: 'DM Serif Display', serif;
    font-size: clamp(2rem, 4vw, 3rem);
    font-weight: 400; line-height: 1; color: var(--ink);
  }
  header h1 em { font-style: italic; color: var(--gold); }

  .toolbar { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
  .search-wrap { position: relative; }
  .search-wrap svg {
    position: absolute; left: 11px; top: 50%; transform: translateY(-50%);
    width: 15px; height: 15px; stroke: var(--ink-3); fill: none; stroke-width: 2; pointer-events: none;
  }
  .search-wrap input {
    background: var(--paper-2); border: 1.5px solid var(--paper-3);
    border-radius: var(--radius); padding: 8px 14px 8px 34px;
    font-family: 'DM Sans', sans-serif; font-size: 13.5px; color: var(--ink);
    width: 240px; outline: none; transition: border-color .15s;
  }
  .search-wrap input::placeholder { color: var(--ink-3); }
  .search-wrap input:focus { border-color: var(--gold); background: #fff; }

  .stat-strip { display: grid; grid-template-columns: repeat(3,1fr); gap: 14px; margin-bottom: 2rem; }
  .stat-card {
    background: var(--paper-2); border: 1.5px solid var(--paper-3);
    border-radius: var(--radius); padding: 1.1rem 1.25rem;
    position: relative; overflow: hidden;
  }
  .stat-card::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: var(--paper-3);
  }
  .stat-card.gold::before { background: var(--gold); }
  .stat-card.teal::before { background: var(--teal); }
  .stat-card.rose::before { background: var(--rose); }
  .stat-label { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .08em; color: var(--ink-3); margin-bottom: 8px; }
  .stat-value { font-family: 'DM Serif Display', serif; font-size: 2rem; line-height: 1; color: var(--ink); }
  .stat-sub   { font-size: 12px; color: var(--ink-3); margin-top: 5px; }

  .table-wrap { background: #fff; border: 1.5px solid var(--paper-3); border-radius: var(--radius); overflow: hidden; box-shadow: var(--shadow); }
  .table-scroll { overflow-x: auto; -webkit-overflow-scrolling: touch; }
  table { width: 100%; border-collapse: collapse; font-size: 14px; min-width: 640px; }
  thead tr { background: var(--paper-2); border-bottom: 1.5px solid var(--paper-3); }
  th { padding: 12px 18px; text-align: left; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; color: var(--ink-3); white-space: nowrap; }
  tbody tr { border-bottom: 1px solid var(--paper-2); transition: background .1s; }
  tbody tr:last-child { border-bottom: none; }
  tbody tr:hover { background: var(--paper); }
  td { padding: 13px 18px; color: var(--ink-2); vertical-align: middle; }

  .survey-title { font-weight: 600; color: var(--ink); font-size: 14px; }
  .survey-desc  { font-size: 12px; color: var(--ink-3); margin-top: 2px; }

  /* Progress bar — same formula as surveymanagement */
  .prog-wrap  { display: flex; align-items: center; gap: 10px; min-width: 130px; }
  .prog-track { flex: 1; height: 5px; background: var(--paper-2); border-radius: 3px; overflow: hidden; }
  .prog-fill  { height: 100%; border-radius: 3px; background: var(--gold); }
  .prog-num   { font-weight: 600; font-size: 13px; min-width: 36px; text-align: right; color: var(--ink); }

  /* Badges — identical to surveymanagement */
  .badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; white-space: nowrap; }
  .badge::before { content: ''; width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
  .badge-published { background: var(--teal-lt); color: var(--teal); }
  .badge-published::before { background: var(--teal); animation: pulse 1.5s infinite; }
  .badge-closed    { background: var(--rose-lt);  color: var(--rose); }
  .badge-closed::before  { background: var(--rose); }
  .badge-pending   { background: var(--gold-light); color: var(--gold-dark); }
  .badge-pending::before { background: var(--gold); }
  @keyframes pulse { 0%,100%{opacity:1;transform:scale(1);}50%{opacity:.4;transform:scale(.8);} }

  .action-btn {
    display: inline-flex; align-items: center; gap: 5px;
    background: none; border: 1.5px solid var(--paper-3);
    border-radius: 7px; padding: 5px 13px;
    font-size: 12.5px; font-family: 'DM Sans', sans-serif;
    color: var(--ink-2); cursor: pointer; text-decoration: none;
    transition: border-color .15s, color .15s;
  }
  .action-btn:hover { border-color: var(--gold); color: var(--gold); }

  .empty-state { text-align: center; padding: 3rem 1rem; color: var(--ink-3); font-size: 14px; }

  @media (max-width: 900px) {
    .stat-strip { grid-template-columns: repeat(2,1fr); }
    .main-wrapper { padding: 1.5rem 1.25rem 3rem; }
  }
</style>

<div class="main-wrapper">

  <div class="breadcrumbs">
    <a href="index">Home</a>
    <span>/</span>
    <span class="current-page">Survey Results</span>
  </div>

  <header>
    <h1>Data <em>Analytics</em></h1>
    <div class="toolbar">
      <div class="search-wrap">
        <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" id="surveySearch" placeholder="Search surveys…" oninput="filterTable()">
      </div>
    </div>
  </header>

  <!-- Stat strip — values sourced from the same queries as surveymanagement.php -->
  <div class="stat-strip">
    <div class="stat-card gold">
      <div class="stat-label">Total Submissions</div>
      <!-- Sum of COUNT(DISTINCT r.user_id) across all surveys -->
      <div class="stat-value"><?= number_format($totalResponses) ?></div>
      <div class="stat-sub">Out of <?= number_format($totalStudents) ?> registered students</div>
    </div>
    <div class="stat-card teal">
      <div class="stat-label">Open Surveys</div>
      <!-- Count of surveys where status = 'published' -->
      <div class="stat-value"><?= $openCount ?></div>
      <div class="stat-sub">Currently collecting data</div>
    </div>
    <div class="stat-card rose">
      <div class="stat-label">Questions Asked</div>
      <div class="stat-value"><?= number_format($totalQuestions) ?></div>
      <div class="stat-sub">Across all templates</div>
    </div>
  </div>

  <section style="width:100%;">
    <div class="table-wrap">
      <div class="table-scroll">
        <table id="surveyTable">
          <thead>
            <tr>
              <th>Survey Campaign</th>
              <th>Responses</th>
              <th>Response Rate</th>
              <th>Date Created</th>
              <th>Status</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($surveys)): ?>
              <tr>
                <td colspan="6"><div class="empty-state">No surveys found.</div></td>
              </tr>
            <?php else: ?>
              <?php foreach ($surveys as $s):
                // Identical to surveymanagement.php — no custom formulas
                $responses = (int)$s['response_count'];
                $pct       = $totalStudents > 0 ? round(($responses / $totalStudents) * 100) : 0;
                $dateLabel = date('M d, Y', strtotime($s['created_at']));
              ?>
              <tr>
                <td>
                  <div class="survey-title"><?= htmlspecialchars($s['title']) ?></div>
                  <?php if (!empty($s['description'])): ?>
                    <div class="survey-desc"><?= htmlspecialchars($s['description']) ?></div>
                  <?php endif; ?>
                </td>
                <!-- "X / totalStudents" — same display as surveymanagement -->
                <td style="font-weight:600; color:var(--ink);">
                  <?= number_format($responses) ?> / <?= $totalStudents ?>
                </td>
                <!-- Bar + % — same formula: (responses / totalStudents) × 100 -->
                <td>
                  <div class="prog-wrap">
                    <div class="prog-track">
                      <div class="prog-fill" style="width:<?= $pct ?>%;"></div>
                    </div>
                    <span class="prog-num"><?= $pct ?>%</span>
                  </div>
                </td>
                <td style="color:var(--ink-3); font-size:13px;"><?= $dateLabel ?></td>
                <td>
                  <?php if ($s['status'] === 'published'): ?>
                    <span class="badge badge-published">Published</span>
                  <?php elseif ($s['status'] === 'pending'): ?>
                    <span class="badge badge-pending">Pending</span>
                  <?php else: ?>
                    <span class="badge badge-closed">Closed</span>
                  <?php endif; ?>
                </td>
                <td>
                  <a href="surveymanagement.php?id=<?= (int)$s['id'] ?>" class="action-btn">
                    View Survey
                    <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2">
                      <polyline points="9 18 15 12 9 6"/>
                    </svg>
                  </a>
                </td>
              </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </section>
</div>

<script>
function filterTable() {
  const q = document.getElementById('surveySearch').value.toLowerCase();
  document.querySelectorAll('#surveyTable tbody tr').forEach(row => {
    row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
  });
}
</script>

<?php include('./includes/footer.php'); ?>