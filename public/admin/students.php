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
  .breadcrumbs span { color: var(--paper-3); }
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
    pointer-events: none;
  }
  .search-wrap input {
    background: var(--paper-2);
    border: 1.5px solid var(--paper-3);
    border-radius: var(--radius);
    padding: 8px 14px 8px 34px;
    font-family: 'DM Sans', sans-serif;
    font-size: 13.5px;
    color: var(--ink);
    width: 220px;
    outline: none;
    transition: border-color 0.15s;
  }
  .search-wrap input:focus { border-color: var(--gold); }

  .filter-select {
    background: var(--paper-2);
    border: 1.5px solid var(--paper-3);
    border-radius: var(--radius);
    padding: 8px 14px;
    font-family: 'DM Sans', sans-serif;
    font-size: 13.5px;
    color: var(--ink);
    outline: none;
    cursor: pointer;
  }

  .btn-export {
    display: flex; align-items: center; gap: 6px;
    background: var(--ink); color: var(--paper);
    border: none; border-radius: var(--radius);
    padding: 8.5px 16px;
    font-family: 'DM Sans', sans-serif;
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
  .stat-card.fail::before { background: var(--fail); }

  .stat-label { font-size: 11px; font-weight: 600; letter-spacing: 0.08em; text-transform: uppercase; color: var(--ink-3); margin-bottom: 8px; }
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
  tbody tr { border-bottom: 1px solid var(--paper-2); transition: background 0.1s; }
  td { padding: 13px 18px; color: var(--ink-2); vertical-align: middle; }

  .student-cell { display: flex; align-items: center; gap: 10px; }
  .avatar {
    width: 34px; height: 34px; border-radius: 50%;
    background: var(--gold-light); border: 1.5px solid var(--paper-3);
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; font-weight: 600; color: var(--gold);
  }
  .student-name { font-weight: 500; color: var(--ink); }
  .student-id { font-size: 12px; color: var(--ink-3); }

  .score-bar-wrap { display: flex; align-items: center; gap: 10px; min-width: 130px; }
  .score-track { flex: 1; height: 5px; background: var(--paper-2); border-radius: 3px; overflow: hidden; }
  .score-fill { height: 100%; border-radius: 3px; transition: width 0.4s ease; }
  .score-num { font-weight: 600; font-size: 13.5px; min-width: 38px; text-align: right; color: var(--ink); }

  .badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 500; }
  .badge::before { content: ''; width: 6px; height: 6px; border-radius: 50%; }
  .badge-pass { background: var(--pass-bg); color: var(--pass); }
  .badge-pass::before { background: var(--pass); }
  .badge-fail { background: var(--fail-bg); color: var(--fail); }
  .badge-fail::before { background: var(--fail); }
  .badge-pending { background: var(--pending-bg); color: var(--pending); }
  .badge-pending::before { background: var(--pending); }

  .action-btn {
    background: none; border: 1.5px solid var(--paper-3);
    border-radius: 7px; padding: 5px 11px;
    font-family: 'DM Sans', sans-serif; font-size: 12.5px;
    color: var(--ink-2); cursor: pointer;
  }

  .pagination { display: flex; align-items: center; justify-content: space-between; padding: 14px 18px; border-top: 1.5px solid var(--paper-2); font-size: 13px; color: var(--ink-3); background: var(--paper); }
</style>

<div class="main-wrapper">

  <div class="breadcrumbs">
    <a href="index">Home</a>
    <span>/</span>
    <span class="current-page">Survey Participation</span>
  </div>

  <header>
    <h1>User <em>Engagement</em></h1>
    <div class="toolbar">
      <div class="search-wrap">
        <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" placeholder="Search respondents…">
      </div>
      <select class="filter-select">
        <option>All Surveys</option>
        <option>User Satisfaction</option>
        <option>Product Feedback</option>
        <option>System Performance</option>
      </select>
      <a href="#" class="btn-export">
        <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        Export Report
      </a>
    </div>
  </header>

  <div class="stat-strip">
    <div class="stat-card gold">
      <div class="stat-label">Total Respondents</div>
      <div class="stat-value">507</div>
      <div class="stat-sub">Registered users</div>
    </div>
    <div class="stat-card pass">
      <div class="stat-label">Completion Rate</div>
      <div class="stat-value">74%</div>
      <div class="stat-sub">923 completed surveys</div>
    </div>
    <div class="stat-card fail">
      <div class="stat-label">Inactive</div>
      <div class="stat-value">112</div>
      <div class="stat-sub">No recent activity</div>
    </div>
    <div class="stat-card">
      <div class="stat-label">Avg. Time</div>
      <div class="stat-value">4:20</div>
      <div class="stat-sub">Minutes per survey</div>
    </div>
  </div>

  <section style="width: 100%;">
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Rank</th>
            <th>Respondent</th>
            <th>Latest Survey</th>
            <th>Completion Date</th>
            <th>Engagement Score</th>
            <th>Status</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php
          $results = [
            ['rank'=>1,  'name'=>'Sophia Reyes',    'id'=>'USR-4011', 'survey'=>'User Satisfaction', 'date'=>'Mar 28, 2026', 'score'=>98, 'status'=>'active'],
            ['rank'=>2,  'name'=>'James Okafor',    'id'=>'USR-8029', 'survey'=>'Product Feedback',  'date'=>'Mar 27, 2026', 'score'=>92, 'status'=>'active'],
            ['rank'=>3,  'name'=>'Aiko Tanaka',     'id'=>'USR-1013', 'survey'=>'User Satisfaction', 'date'=>'Mar 25, 2026', 'score'=>89, 'status'=>'active'],
            ['rank'=>4,  'name'=>'Luca Bianchi',    'id'=>'USR-5057', 'survey'=>'System Performance', 'date'=>'Mar 24, 2026', 'score'=>65, 'status'=>'pending'],
            ['rank'=>5,  'name'=>'Fatima Hassan',   'id'=>'USR-7077', 'survey'=>'Product Feedback',  'date'=>'Mar 22, 2026', 'score'=>42, 'status'=>'inactive'],
          ];

          foreach ($results as $r):
            $initials = implode('', array_map(fn($w) => strtoupper($w[0]), explode(' ', $r['name'])));
            $score = $r['score'];
            if ($score >= 80)      $bar_color = 'var(--pass)';
            elseif ($score >= 60)  $bar_color = 'var(--gold)';
            else                   $bar_color = 'var(--fail)';
          ?>
          <tr>
            <td><span class="rank <?= $r['rank'] <= 3 ? 'top' : '' ?>">#<?= $r['rank'] ?></span></td>
            <td>
              <div class="student-cell">
                <div class="avatar"><?= $initials ?></div>
                <div>
                  <div class="student-name"><?= htmlspecialchars($r['name']) ?></div>
                  <div class="student-id"><?= $r['id'] ?></div>
                </div>
              </div>
            </td>
            <td><?= $r['survey'] ?></td>
            <td style="color:var(--ink-3); font-size:13px;"><?= $r['date'] ?></td>
            <td>
              <div class="score-bar-wrap">
                <div class="score-track"><div class="score-fill" style="width:<?= $score ?>%; background:<?= $bar_color ?>;"></div></div>
                <span class="score-num"><?= $score ?>%</span>
              </div>
            </td>
            <td>
              <?php if ($r['status'] === 'active'): ?>
                <span class="badge badge-pass">Active</span>
              <?php elseif ($r['status'] === 'inactive'): ?>
                <span class="badge badge-fail">Inactive</span>
              <?php else: ?>
                <span class="badge badge-pending">Pending</span>
              <?php endif; ?>
            </td>
            <td><button class="action-btn">Analyze</button></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      
      <div class="pagination">
        <span>Showing 1–5 of 1,248 respondents</span>
        </div>
    </div>
  </section>
</div>

<?php include('./includes/footer.php'); ?>