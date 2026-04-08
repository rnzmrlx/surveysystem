<?php
include('../../app/middleware/admin.php');
include('./includes/header.php');
include('./includes/topbar.php');
include('./includes/sidebar.php');
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
    --teal:       #1b6b6b;
    --teal-lt:    #d0eaea;
    --rose:       #a02c2c;
    --rose-lt:    #f5dede;
    --pass:       #2a6b4a;
    --pass-bg:    #d8efe3;
    --fail:       #a02c2c;
    --fail-bg:    #f5dede;
    --radius:     10px;
    --shadow:     0 2px 16px rgba(15,14,13,0.07);
  }

  * { box-sizing: border-box; margin: 0; padding: 0; }

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

  /* Breadcrumbs */
  .breadcrumbs {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12.5px;
    font-weight: 500;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    color: var(--ink-3);
    margin-bottom: 2rem;
  }
  .breadcrumbs a { color: var(--ink-3); text-decoration: none; transition: color 0.15s; }
  .breadcrumbs a:hover { color: var(--gold); }
  .breadcrumbs .current-page { color: var(--ink-2); }

  /* Header */
  header {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    margin-bottom: 2.25rem;
    gap: 1rem;
  }
  header h1 {
    font-family: 'DM Serif Display', serif;
    font-size: clamp(2rem, 4vw, 3rem);
    font-weight: 400;
    line-height: 1;
    color: var(--ink);
  }
  header h1 em { font-style: italic; color: var(--gold); }

  /* Toolbar */
  .toolbar {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
  }
  .search-wrap { position: relative; }
  .search-wrap svg {
    position: absolute; left: 11px; top: 50%;
    transform: translateY(-50%);
    width: 15px; height: 15px;
    stroke: var(--ink-3); fill: none; stroke-width: 2;
  }
  .search-wrap input {
    background: var(--paper-2);
    border: 1.5px solid var(--paper-3);
    border-radius: var(--radius);
    padding: 8px 14px 8px 34px;
    font-family: 'DM Sans', sans-serif;
    font-size: 13.5px;
    color: var(--ink);
    width: 240px;
    outline: none;
  }
  .search-wrap input:focus {
    border-color: var(--gold);
    background: #fff;
  }

  /* Stat strip — 3 cards */
  .stat-strip {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 14px;
    margin-bottom: 2rem;
  }
  .stat-card {
    background: var(--paper-2);
    border: 1.5px solid var(--paper-3);
    border-radius: var(--radius);
    padding: 1.1rem 1.25rem;
    position: relative; overflow: hidden;
  }
  .stat-card::before {
    content: ''; position: absolute;
    top: 0; left: 0; right: 0; height: 3px;
    background: var(--paper-3);
  }
  .stat-card.gold::before { background: var(--gold); }
  .stat-card.pass::before { background: var(--pass); }
  .stat-card.teal::before { background: var(--teal); }
  .stat-card.rose::before { background: var(--rose); }

  .stat-label { font-size: 11px; font-weight: 600; text-transform: uppercase; color: var(--ink-3); margin-bottom: 8px; }
  .stat-value { font-family: 'DM Serif Display', serif; font-size: 2rem; line-height: 1; color: var(--ink); }
  .stat-sub { font-size: 12px; color: var(--ink-3); margin-top: 5px; }

  /* Table */
  .table-wrap {
    background: #fff;
    border: 1.5px solid var(--paper-3);
    border-radius: var(--radius);
    overflow: hidden;
    box-shadow: var(--shadow);
  }

  table { width: 100%; border-collapse: collapse; font-size: 14px; }
  thead tr { background: var(--paper-2); border-bottom: 1.5px solid var(--paper-3); }
  th { padding: 12px 18px; text-align: left; font-size: 11px; font-weight: 600; text-transform: uppercase; color: var(--ink-3); letter-spacing: 0.05em; }
  tbody tr { border-bottom: 1px solid var(--paper-2); transition: background 0.15s; }
  tbody tr:last-child { border-bottom: none; }
  tbody tr:hover td { background: var(--paper); }
  td { padding: 13px 18px; color: var(--ink-2); vertical-align: middle; }

  .survey-title { font-weight: 600; color: var(--ink); }
  .survey-id { font-size: 12px; color: var(--ink-3); margin-top: 2px; }

  .score-bar-wrap { display: flex; align-items: center; gap: 10px; min-width: 130px; }
  .score-track { flex: 1; height: 5px; background: var(--paper-2); border-radius: 3px; overflow: hidden; }
  .score-fill { height: 100%; border-radius: 3px; }

  /* Badges — correct color mapping */
  .badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 10px; border-radius: 20px;
    font-size: 11.5px; font-weight: 600;
  }
  .badge::before { content: ''; width: 6px; height: 6px; border-radius: 50%; }

  .badge-closed  { background: var(--rose-lt);  color: var(--rose);  }
  .badge-closed::before  { background: var(--rose); }

  .badge-active  { background: var(--teal-lt);  color: var(--teal);  }
  .badge-active::before  { background: var(--teal); }

  .badge-pending { background: var(--gold-light); color: var(--gold); }
  .badge-pending::before { background: var(--gold); }

  .action-btn {
    display: inline-flex; align-items: center; gap: 5px;
    background: none; border: 1.5px solid var(--paper-3);
    border-radius: 7px; padding: 5px 13px; font-size: 12.5px;
    font-family: 'DM Sans', sans-serif;
    color: var(--ink-2); cursor: pointer; text-decoration: none;
    transition: border-color 0.15s, color 0.15s;
  }
  .action-btn:hover { border-color: var(--gold); color: var(--gold); }

  @media (max-width: 900px) {
    .stat-strip { grid-template-columns: repeat(2, 1fr); }
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

  <!-- 3-card stat strip -->
  <div class="stat-strip">
    <div class="stat-card gold">
      <div class="stat-label">Total Submissions</div>
      <div class="stat-value">1,241</div>
      <div class="stat-sub">+12% from last month</div>
    </div>
    <div class="stat-card teal">
      <div class="stat-label">Open Surveys</div>
      <div class="stat-value">1</div>
      <div class="stat-sub">Currently collecting data</div>
    </div>
    <div class="stat-card rose">
      <div class="stat-label">Questions Asked</div>
      <div class="stat-value">156</div>
      <div class="stat-sub">Across all templates</div>
    </div>
  </div>

  <section style="width: 100%;">
    <div class="table-wrap">
      <table id="surveyTable">
        <thead>
          <tr>
            <th>Survey Campaign</th>
            <th>Category</th>
            <th>Responses</th>
            <th>Last Submission</th>
            <th>Completion</th>
            <th>Status</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php
          $surveys = [
            [
              'title'     => 'Mid-Term Faculty Evaluation',
              'id'        => 'SRV-2297',
              'survey_id' => 1,
              'cat'       => 'Academic',
              'res'       => 507,
              'date'      => 'Mar 28, 2026',
              'score'     => 100,
              'status'    => 'Closed',
            ],
            [
              'title'     => 'Online Learning Experience',
              'id'        => 'SRV-8842',
              'survey_id' => 2,
              'cat'       => 'Wellness',
              'res'       => 461,
              'date'      => 'April 15, 2026',
              'score'     => 91,
              'status'    => 'Active',
            ],
            [
              'title'     => 'Facility & Safety Assessment',
              'id'        => 'SRV-5510',
              'survey_id' => 3,
              'cat'       => 'Administration',
              'res'       => 0,
              'date'      => 'May 30, 2026',
              'score'     => 0,
              'status'    => 'Pending',
            ],
          ];

          foreach ($surveys as $s):
            $pct       = $s['score'];
            $bar_color = ($s['status'] === 'Closed') ? 'var(--rose)' : (($s['status'] === 'Active') ? 'var(--teal)' : 'transparent');
            $badge_class = match($s['status']) {
              'Closed'  => 'badge-closed',
              'Active'  => 'badge-active',
              'Pending' => 'badge-pending',
              default   => 'badge-pending'
            };
          ?>
          <tr>
            <td>
              <div class="survey-title"><?= htmlspecialchars($s['title']) ?></div>
              <div class="survey-id"><?= $s['id'] ?></div>
            </td>
            <td><?= $s['cat'] ?></td>
            <td style="font-weight:500;"><?= number_format($s['res']) ?></td>
            <td style="color:var(--ink-3); font-size:13px;"><?= $s['date'] ?></td>
            <td>
              <div class="score-bar-wrap">
                <div class="score-track">
                  <div class="score-fill" style="width:<?= $pct ?>%; background:<?= $bar_color ?>;"></div>
                </div>
                <span style="font-weight:600; min-width:35px; font-size:13px;"><?= $pct ?>%</span>
              </div>
            </td>
            <td><span class="badge <?= $badge_class ?>"><?= $s['status'] ?></span></td>
            <td>
              <a href="surveymanagement.php?id=<?= $s['survey_id'] ?>" class="action-btn">
                View Survey
                <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
              </a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
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