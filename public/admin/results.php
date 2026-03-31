<?php
include('../../app/middleware/admin.php');
include('./includes/header.php');
include('./includes/topbar.php');
include('./includes/sidebar.php');
?>

<style>
  @import url('https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@300;400;500;600&display=swap');

  :root {
    --ink:       #0f0e0d;
    --ink-2:     #3a3835;
    --ink-3:     #7a776f;
    --paper:     #f7f5f0;
    --paper-2:   #eceae3;
    --paper-3:   #e0ddd4;
    --gold:      #c9972b;
    --gold-light:#f5e9cc;
    --pass:      #2a6b4a;
    --pass-bg:   #d8efe3;
    --fail:      #a02c2c;
    --fail-bg:   #f5dede;
    --pending:   #5a5200;
    --pending-bg:#f0eccc;
    --radius:    10px;
    --shadow:    0 2px 16px rgba(15,14,13,0.07);
  }

  * { box-sizing: border-box; margin: 0; padding: 0; }

  body {
    font-family: 'DM Sans', sans-serif;
    background: var(--paper);
    color: var(--ink);
  }

  .main-wrapper {
    padding: 2.5rem 2.75rem 4rem;
    max-width: 1200px;
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

  .btn-export {
    display: flex; align-items: center; gap: 6px;
    background: var(--ink); color: var(--paper);
    border: none; border-radius: var(--radius);
    padding: 8.5px 16px;
    font-size: 13.5px; font-weight: 500;
    cursor: pointer; text-decoration: none;
  }

  /* Stat strip */
  .stat-strip {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
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
  th { padding: 12px 18px; text-align: left; font-size: 11px; font-weight: 600; text-transform: uppercase; color: var(--ink-3); }
  tbody tr { border-bottom: 1px solid var(--paper-2); }
  td { padding: 13px 18px; color: var(--ink-2); vertical-align: middle; }

  /* Survey Specifics */
  .survey-title { font-weight: 600; color: var(--ink); }
  .survey-id { font-size: 12px; color: var(--ink-3); }

  .score-bar-wrap { display: flex; align-items: center; gap: 10px; min-width: 130px; }
  .score-track { flex: 1; height: 5px; background: var(--paper-2); border-radius: 3px; overflow: hidden; }
  .score-fill { height: 100%; border-radius: 3px; }

  .badge { display: inline-flex; align-items: center; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 500; }
  .badge-pass { background: var(--pass-bg); color: var(--pass); }
  .badge-pending { background: var(--pending-bg); color: var(--pending); }

  .action-btn {
    background: none; border: 1.5px solid var(--paper-3);
    border-radius: 7px; padding: 5px 11px; font-size: 12.5px;
    color: var(--ink-2); cursor: pointer;
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
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" placeholder="Search surveys…">
      </div>
      <a href="#" class="btn-export">
        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" fill="none"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        Download All
      </a>
    </div>
  </header>

  <div class="stat-strip">
    <div class="stat-card gold">
      <div class="stat-label">Total Submissions</div>
      <div class="stat-value">1,241</div>
      <div class="stat-sub">+12% from last month</div>
    </div>
    <div class="stat-card pass">
      <div class="stat-label">Avg. Satisfaction</div>
      <div class="stat-value">4.8/5</div>
      <div class="stat-sub">Based on latest feedback</div>
    </div>
    <div class="stat-card">
      <div class="stat-label">Open Surveys</div>
      <div class="stat-value">8</div>
      <div class="stat-sub">Currently collecting data</div>
    </div>
    <div class="stat-card">
      <div class="stat-label">Questions Asked</div>
      <div class="stat-value">156</div>
      <div class="stat-sub">Across all templates</div>
    </div>
  </div>

  <section style="width: 100%;">
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Survey Campaign</th>
            <th>Category</th>
            <th>Responses</th>
            <th>Last Submission</th>
            <th>Avg. Sentiment</th>
            <th>Status</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php
          $surveys = [
            ['title'=>'Mid-Term Faculty Evaluation',    'id'=>'USR-2026-09', 'cat'=>'Academic',        'res'=>507,  'date'=>'Mar 28, 2026', 'score'=>100, 'status'=>'Closed'],
            ['title'=>'Online Learning Experience',   'id'=>'SVY-2026-12', 'cat'=>'Wellness',      'res'=>461,  'date'=>'April 15, 2026', 'score'=>91, 'status'=>'Active'],
            ['title'=>'Facility & Safety Assessment',  'id'=>'SVY-2025-99', 'cat'=>'Administration',         'res'=>0,  'date'=>'May 30, 2026', 'score'=>0, 'status'=>'Pending'],
          ];

          foreach ($surveys as $s):
            $pct = $s['score'];
            $bar_color = ($pct >= 85) ? 'var(--pass)' : (($pct >= 70) ? 'var(--gold)' : 'var(--fail)');
          ?>
          <tr>
            <td>
              <div>
                <div class="survey-title"><?= htmlspecialchars($s['title']) ?></div>
                <div class="survey-id"><?= $s['id'] ?></div>
              </div>
            </td>
            <td><?= $s['cat'] ?></td>
            <td style="font-weight: 500;"><?= number_format($s['res']) ?></td>
            <td style="color:var(--ink-3); font-size:13px;"><?= $s['date'] ?></td>
            <td>
              <div class="score-bar-wrap">
                <div class="score-track"><div class="score-fill" style="width:<?= $pct ?>%; background:<?= $bar_color ?>;"></div></div>
                <span class="score-num" style="font-weight:600; min-width:35px;"><?= $pct ?>%</span>
              </div>
            </td>
            <td>
              <span class="badge <?= ($s['status'] == 'Active') ? 'badge-pass' : 'badge-pending' ?>">
                <?= $s['status'] ?>
              </span>
            </td>
            <td><button class="action-btn">View Report</button></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>
</div>

<?php include('./includes/footer.php'); ?>