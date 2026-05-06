<?php
include('../../app/middleware/user.php');
include('./includes/header.php');
include('./includes/topbar.php');
include('./includes/sidebar.php');
include('../../app/config/config.php');

$user_id = $_SESSION['authUser']['user_id'];

// Get all published surveys with answered status for this user
$sql = "
    SELECT s.id, s.title, s.description, s.created_at,
           COUNT(DISTINCT q.id) as question_count,
           CASE WHEN r.survey_id IS NOT NULL THEN 1 ELSE 0 END as is_answered
    FROM surveys s
    LEFT JOIN questions q ON q.survey_id = s.id
    LEFT JOIN (
        SELECT DISTINCT survey_id FROM responses WHERE user_id = $user_id
    ) r ON r.survey_id = s.id
    WHERE s.status = 'published'
    GROUP BY s.id, r.survey_id
    ORDER BY s.created_at DESC
";
$result = mysqli_query($conn, $sql);

$surveys       = [];
$totalSurveys  = 0;
$answeredCount = 0;
$pendingCount  = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $surveys[] = $row;
    $totalSurveys++;
    if ($row['is_answered']) $answeredCount++;
    else $pendingCount++;
}
?>

<style>
  @import url('https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@300;400;500;600&display=swap');

  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: 'DM Sans', sans-serif; background: var(--paper); color: var(--ink); }

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
    display: flex; align-items: center; gap: 8px;
    font-size: 12.5px; font-weight: 500; letter-spacing: .04em;
    text-transform: uppercase; color: var(--ink-3); margin-bottom: 2rem;
  }
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
  .header-sub { font-size: 13.5px; color: var(--ink-3); margin-top: 6px; }

  .stat-strip-3 { display: grid; grid-template-columns: repeat(3,1fr); gap: 14px; margin-bottom: 2.25rem; }

  .stat-card {
    background: var(--paper-2); border: 1.5px solid var(--paper-3);
    border-radius: var(--radius); padding: 1.1rem 1.25rem;
    position: relative; overflow: hidden;
    transition: border-color .15s, box-shadow .15s;
  }
  .stat-card::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: var(--paper-3); border-radius: 2px 2px 0 0;
  }
  .stat-card.gold::before { background: var(--gold); }
  .stat-card.teal::before { background: var(--teal); }
  .stat-card.rose::before { background: var(--rose); }
  .stat-label { font-size: 11px; font-weight: 600; letter-spacing: .08em; text-transform: uppercase; color: var(--ink-3); margin-bottom: 8px; }
  .stat-value { font-family: 'DM Serif Display', serif; font-size: 2rem; line-height: 1; color: var(--ink); }
  .stat-sub   { font-size: 12px; color: var(--ink-3); margin-top: 5px; }

  .section-label {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 12px;
  }
  .section-label h2 {
    font-family: 'DM Serif Display', serif;
    font-size: 1.2rem; font-weight: 400; color: var(--ink);
  }
  .section-label h2 em { font-style: italic; color: var(--gold); }

  .filter-bar {
    display: flex; align-items: center; gap: 10px;
    margin-bottom: 1.25rem; flex-wrap: wrap;
  }
  .filter-btn {
    padding: 6px 14px; border-radius: 20px; font-size: 12.5px;
    font-weight: 600; border: 1.5px solid var(--paper-3);
    background: var(--paper-2); color: var(--ink-3);
    cursor: pointer; transition: all 0.15s; font-family: 'DM Sans', sans-serif;
  }
  .filter-btn:hover { border-color: var(--gold); color: var(--gold); }
  .filter-btn.active { background: var(--gold); border-color: var(--gold); color: #fff; }

  .survey-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 16px;
    margin-bottom: 2.25rem;
  }

  .survey-card {
    background: #fff;
    border: 1.5px solid var(--paper-3);
    border-radius: var(--radius);
    padding: 1.4rem;
    box-shadow: var(--shadow);
    transition: border-color .15s, box-shadow .15s, transform .1s;
    display: flex;
    flex-direction: column;
    gap: 12px;
    position: relative;
    overflow: hidden;
  }
  .survey-card::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: var(--paper-3);
  }
  .survey-card.pending::before  { background: var(--gold); }
  .survey-card.answered::before { background: var(--teal); }
  .survey-card:hover { border-color: var(--gold); box-shadow: 0 4px 24px rgba(15,14,13,.1); transform: translateY(-1px); }

  .survey-card-header {
    display: flex; align-items: flex-start; justify-content: space-between; gap: 10px;
  }
  .survey-card-title {
    font-family: 'DM Serif Display', serif;
    font-size: 1rem; font-weight: 400; color: var(--ink); line-height: 1.3;
  }
  .survey-card-desc { font-size: 13px; color: var(--ink-3); line-height: 1.5; }
  .survey-card-meta { display: flex; align-items: center; gap: 14px; font-size: 12px; color: var(--ink-3); }
  .survey-card-meta span { display: flex; align-items: center; gap: 4px; }
  .survey-card-footer {
    display: flex; align-items: center; justify-content: space-between;
    margin-top: auto; padding-top: 10px;
    border-top: 1px solid var(--paper-2);
  }

  .badge { display: inline-flex; align-items: center; gap: 5px; padding: 3px 9px; border-radius: 20px; font-size: 11.5px; font-weight: 600; white-space: nowrap; }
  .badge::before { content: ''; width: 5px; height: 5px; border-radius: 50%; flex-shrink: 0; }
  .badge-open     { background: var(--gold-light); color: var(--gold-dark); }
  .badge-open::before     { background: var(--gold); }
  .badge-answered { background: var(--teal-lt); color: var(--teal); }
  .badge-answered::before { background: var(--teal); animation: pulse 1.5s infinite; }
  @keyframes pulse { 0%,100%{opacity:1;transform:scale(1);}50%{opacity:.4;transform:scale(.8);} }

  .btn-take {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 7px 16px; border-radius: 8px;
    background: var(--gold); color: #fff;
    font-size: 12.5px; font-weight: 600;
    text-decoration: none; border: none; cursor: pointer;
    transition: background 0.15s; font-family: 'DM Sans', sans-serif;
  }
  .btn-take:hover { background: var(--gold-dark); color: #fff; }

  .btn-done {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 7px 16px; border-radius: 8px;
    background: var(--teal-lt); color: var(--teal);
    font-size: 12.5px; font-weight: 600;
    text-decoration: none; border: none; cursor: default;
    font-family: 'DM Sans', sans-serif;
  }

  .empty-state {
    text-align: center; padding: 4rem 2rem;
    background: #fff; border: 1.5px solid var(--paper-3);
    border-radius: var(--radius); box-shadow: var(--shadow);
  }
  .empty-state i { font-size: 2.5rem; color: var(--paper-3); margin-bottom: 1rem; display: block; }
  .empty-state h3 { font-family: 'DM Serif Display', serif; font-size: 1.3rem; color: var(--ink); margin-bottom: 6px; }
  .empty-state p  { font-size: 13.5px; color: var(--ink-3); }

  .divider { border: none; border-top: 1.5px solid var(--paper-3); margin: 2.25rem 0; }

  @media (max-width: 900px) {
    .stat-strip-3 { grid-template-columns: repeat(2,1fr); }
    .survey-grid  { grid-template-columns: 1fr; }
  }
</style>

 <div class="breadcrumbs">
    <a href="index" style="color:var(--ink-3);text-decoration:none;transition:color .15s;" onmouseover="this.style.color='var(--gold)'" onmouseout="this.style.color='var(--ink-3)'">Home</a>
    <span style="color:var(--paper-3);">/</span>
    <span class="current-page">My Surveys</span>
  </div>

  <header>
    <div>
      <h1>My <em>Surveys</em></h1>
      <p class="header-sub">Browse and take available surveys assigned to you.</p>
    </div>
  </header>

  <!-- Stats -->
  <div class="stat-strip-3">
    <div class="stat-card gold">
      <div class="stat-label">Total Available</div>
      <div class="stat-value"><?= $totalSurveys ?></div>
      <div class="stat-sub">Published surveys</div>
    </div>
    <div class="stat-card teal">
      <div class="stat-label">Answered</div>
      <div class="stat-value"><?= $answeredCount ?></div>
      <div class="stat-sub">Completed by you</div>
    </div>
    <div class="stat-card rose">
      <div class="stat-label">Pending</div>
      <div class="stat-value"><?= $pendingCount ?></div>
      <div class="stat-sub">Not answered yet</div>
    </div>
  </div>

  <hr class="divider">

  <!-- Survey List -->
  <div class="section-label">
    <h2>Available <em>Surveys</em></h2>
  </div>

  <!-- Filter Buttons -->
  <div class="filter-bar">
    <button class="filter-btn active" onclick="filterSurveys('all', this)">All (<?= $totalSurveys ?>)</button>
    <button class="filter-btn" onclick="filterSurveys('pending', this)">Pending (<?= $pendingCount ?>)</button>
    <button class="filter-btn" onclick="filterSurveys('answered', this)">Answered (<?= $answeredCount ?>)</button>
  </div>

  <!-- Survey Cards -->
  <?php if (empty($surveys)): ?>
    <div class="empty-state">
      <i class="bi bi-clipboard-x"></i>
      <h3>No Surveys Available</h3>
      <p>There are no published surveys at the moment. Check back later.</p>
    </div>
  <?php else: ?>
    <div class="survey-grid" id="surveyGrid">
      <?php foreach ($surveys as $s):
        $status    = $s['is_answered'] ? 'answered' : 'pending';
        $dateLabel = date('M d, Y', strtotime($s['created_at']));
        $qCount    = (int)$s['question_count'];
      ?>
      <div class="survey-card <?= $status ?>" data-status="<?= $status ?>">
        <div class="survey-card-header">
          <div class="survey-card-title"><?= htmlspecialchars($s['title']) ?></div>
          <?php if ($status === 'answered'): ?>
            <span class="badge badge-answered">Answered</span>
          <?php else: ?>
            <span class="badge badge-open">Open</span>
          <?php endif; ?>
        </div>

        <?php if (!empty($s['description'])): ?>
          <div class="survey-card-desc"><?= htmlspecialchars($s['description']) ?></div>
        <?php endif; ?>

        <div class="survey-card-meta">
          <span><i class="bi bi-question-circle"></i> <?= $qCount ?> question<?= $qCount !== 1 ? 's' : '' ?></span>
          <span><i class="bi bi-calendar3"></i> <?= $dateLabel ?></span>
        </div>

        <div class="survey-card-footer">
          <?php if ($status === 'answered'): ?>
            <span class="btn-done"><i class="bi bi-check-circle"></i> Completed</span>
          <?php else: ?>
<a href="/surveysystem/app/controllers/responseController.php?id=<?= $s['id'] ?>" class="btn-take"><i class="bi bi-pencil"></i> Take Survey</a>          <?php endif; ?>
          <span style="font-size:12px;color:var(--ink-3);"><?= $dateLabel ?></span>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

<script>
  function filterSurveys(status, btn) {
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('.survey-card').forEach(card => {
      card.style.display = (status === 'all' || card.dataset.status === status) ? 'flex' : 'none';
    });
  }
</script>

<?php include('./includes/footer.php'); ?>
