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
    --gold-dark: #8a6318;
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
    font-size: 13.5px; color: var(--ink);
    width: 220px; outline: none;
    transition: border-color 0.15s;
  }
  .search-wrap input::placeholder { color: var(--ink-3); }
  .search-wrap input:focus { border-color: var(--gold); }

  .filter-select {
    background: var(--paper-2);
    border: 1.5px solid var(--paper-3);
    border-radius: var(--radius);
    padding: 8px 14px;
    font-family: 'DM Sans', sans-serif;
    font-size: 13.5px; color: var(--ink);
    outline: none; cursor: pointer;
    transition: border-color 0.15s;
  }
  .filter-select:focus { border-color: var(--gold); }

  .btn-dark {
    display: flex; align-items: center; gap: 6px;
    background: var(--ink); color: var(--paper);
    border: none; border-radius: var(--radius);
    padding: 8.5px 16px;
    font-family: 'DM Sans', sans-serif;
    font-size: 13.5px; font-weight: 500;
    cursor: pointer; text-decoration: none;
    transition: background 0.15s, transform 0.1s;
  }
  .btn-dark:hover { background: var(--ink-2); }
  .btn-dark:active { transform: scale(0.98); }
  .btn-dark svg { width: 14px; height: 14px; stroke: currentColor; fill: none; stroke-width: 2; }

  .btn-gold {
    display: flex; align-items: center; gap: 6px;
    background: var(--gold); color: #fff;
    border: none; border-radius: var(--radius);
    padding: 8.5px 16px;
    font-family: 'DM Sans', sans-serif;
    font-size: 13.5px; font-weight: 500;
    cursor: pointer; text-decoration: none;
    transition: background 0.15s, transform 0.1s;
  }
  .btn-gold:hover { background: var(--gold-dark); color: #fff; }
  .btn-gold:active { transform: scale(0.98); }
  .btn-gold svg { width: 14px; height: 14px; stroke: currentColor; fill: none; stroke-width: 2; }

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
    transition: border-color 0.15s, box-shadow 0.15s;
  }
  .stat-card:hover { border-color: var(--gold); box-shadow: var(--shadow); }
  .stat-card::before {
    content: ''; position: absolute;
    top: 0; left: 0; right: 0; height: 3px;
    background: var(--paper-3);
    border-radius: 2px 2px 0 0;
  }
  .stat-card.gold::before    { background: var(--gold); }
  .stat-card.pass::before    { background: var(--pass); }
  .stat-card.fail::before    { background: var(--fail); }
  .stat-card.pending::before { background: var(--pending); }

  .stat-label {
    font-size: 11px; font-weight: 600;
    letter-spacing: 0.08em; text-transform: uppercase;
    color: var(--ink-3); margin-bottom: 8px;
  }
  .stat-value {
    font-family: 'DM Serif Display', serif;
    font-size: 2rem; line-height: 1; color: var(--ink);
  }
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
  th {
    padding: 12px 18px; text-align: left;
    font-size: 11px; font-weight: 600;
    letter-spacing: 0.08em; text-transform: uppercase;
    color: var(--ink-3); white-space: nowrap;
  }
  th.sortable { cursor: pointer; user-select: none; }
  th.sortable:hover { color: var(--ink); }
  tbody tr { border-bottom: 1px solid var(--paper-2); transition: background 0.1s; }
  tbody tr:last-child { border-bottom: none; }
  tbody tr:hover { background: var(--paper); }
  td { padding: 13px 18px; color: var(--ink-2); vertical-align: middle; }

  /* Survey title cell */
  .survey-cell { display: flex; flex-direction: column; gap: 2px; }
  .survey-title { font-weight: 500; color: var(--ink); font-size: 14px; }
  .survey-desc  { font-size: 12px; color: var(--ink-3); }

  /* Progress bar */
  .prog-wrap { display: flex; align-items: center; gap: 10px; min-width: 130px; }
  .prog-track { flex: 1; height: 5px; background: var(--paper-2); border-radius: 3px; overflow: hidden; }
  .prog-fill  { height: 100%; border-radius: 3px; background: var(--gold); transition: width 0.4s; }
  .prog-num   { font-weight: 600; font-size: 13px; min-width: 36px; text-align: right; color: var(--ink); }

  /* Badge */
  .badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 10px; border-radius: 20px;
    font-size: 12px; font-weight: 500; white-space: nowrap;
  }
  .badge::before { content: ''; width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
  .badge-active  { background: var(--pass-bg);    color: var(--pass);    }
  .badge-active::before  { background: var(--pass); animation: pulse 1.5s infinite; }
  .badge-closed  { background: var(--paper-2);    color: var(--ink-3);   }
  .badge-closed::before  { background: var(--ink-3); }
  .badge-pending { background: var(--pending-bg); color: var(--pending); }
  .badge-pending::before { background: var(--pending); }
  @keyframes pulse { 0%,100%{opacity:1;transform:scale(1);}50%{opacity:0.4;transform:scale(0.8);} }

  /* Category pill */
  .cat-pill {
    background: var(--gold-light); color: var(--gold-dark);
    font-size: 11.5px; font-weight: 600;
    padding: 3px 10px; border-radius: 20px;
    border: 1px solid #e8d49a; white-space: nowrap;
  }

  /* Action buttons */
  .action-btn {
    background: none; border: 1.5px solid var(--paper-3);
    border-radius: 7px; padding: 5px 11px;
    font-family: 'DM Sans', sans-serif;
    font-size: 12.5px; color: var(--ink-2);
    cursor: pointer; text-decoration: none;
    transition: border-color 0.15s, color 0.15s, background 0.15s;
    display: inline-flex; align-items: center; gap: 5px;
  }
  .action-btn:hover { border-color: var(--ink-2); color: var(--ink); background: var(--paper); }
  .action-btn.danger:hover { border-color: var(--fail); color: var(--fail); background: var(--fail-bg); }
  .action-btns { display: flex; align-items: center; gap: 5px; }

  /* Pagination */
  .pagination {
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 18px; border-top: 1.5px solid var(--paper-2);
    font-size: 13px; color: var(--ink-3); background: var(--paper);
  }
  .page-btns { display: flex; gap: 6px; }
  .page-btn {
    width: 32px; height: 32px;
    display: flex; align-items: center; justify-content: center;
    border-radius: 7px; border: 1.5px solid var(--paper-3);
    background: none; font-family: 'DM Sans', sans-serif;
    font-size: 13px; color: var(--ink-2); cursor: pointer;
    transition: all 0.12s;
  }
  .page-btn:hover { border-color: var(--ink-2); color: var(--ink); }
  .page-btn.active { background: var(--ink); color: var(--paper); border-color: var(--ink); }
  .page-btn svg { width: 14px; height: 14px; stroke: currentColor; fill: none; stroke-width: 2; }

  @media (max-width: 900px) {
    .main-wrapper { padding: 1.5rem 1.25rem 3rem; }
    .stat-strip { grid-template-columns: repeat(2, 1fr); }
    th:nth-child(3), td:nth-child(3),
    th:nth-child(5), td:nth-child(5) { display: none; }
  }
</style>

<div class="main-wrapper">

  <div class="breadcrumbs">
    <a href="index">Home</a>
    <span>/</span>
    <span class="current-page">Survey Management</span>
  </div>

  <header>
    <h1>Survey <em>Management</em></h1>
    <div class="toolbar">
      <div class="search-wrap">
        <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" id="surveySearch" placeholder="Search surveys…" oninput="filterSurveys()">
      </div>
      <select class="filter-select" id="catFilter" onchange="filterSurveys()">
        <option value="">All Categories</option>
        <option value="Academic">Academic</option>
        <option value="Facilities">Facilities</option>
        <option value="Student Life">Student Life</option>
        <option value="Administration">Administration</option>
      </select>
      <select class="filter-select" id="statusFilter" onchange="filterSurveys()">
        <option value="">All Statuses</option>
        <option value="active">Active</option>
        <option value="pending">Pending</option>
        <option value="closed">Closed</option>
      </select>
      <a href="#" class="btn-dark">
        <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        Export
      </a>
      <a href="create_survey.php" class="btn-gold">
        <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        New Survey
      </a>
    </div>
  </header>

  <div class="stat-strip">
    <div class="stat-card gold">
      <div class="stat-label">Total Surveys</div>
      <div class="stat-value">12</div>
      <div class="stat-sub">All time</div>
    </div>
    <div class="stat-card pass">
      <div class="stat-label">Active</div>
      <div class="stat-value">4</div>
      <div class="stat-sub">Currently running</div>
    </div>
    <div class="stat-card pending">
      <div class="stat-label">Pending</div>
      <div class="stat-value">5</div>
      <div class="stat-sub">Awaiting launch</div>
    </div>
    <div class="stat-card fail">
      <div class="stat-label">Closed</div>
      <div class="stat-value">3</div>
      <div class="stat-sub">Completed</div>
    </div>
  </div>

  <section style="width:100%;">
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th class="sortable">Survey Title ↕</th>
            <th>Category</th>
            <th class="sortable">Responses ↕</th>
            <th>Response Rate</th>
            <th>Date Created</th>
            <th>Status</th>
            <th></th>
          </tr>
        </thead>
        <tbody id="surveyTable">

          <?php
          $surveys = [
            ['title'=>'Mid-Term Faculty Evaluation',      'desc'=>'Rate your professors this semester',       'cat'=>'Academic',       'responses'=>431, 'total'=>1200, 'date'=>'March 28, 2026', 'status'=>'closed'],
            ['title'=>'Online Learning Experience',   'desc'=>'Feedback on e-learning platforms',          'cat'=>'Wellness',       'responses'=>210,   'total'=>1200, 'date'=>'April 15, 2026', 'status'=>'active'],
            ['title'=>'Facility & Safety Assessment',   'desc'=>'Semester wrap-up faculty evaluation',      'cat'=>'Administration',       'responses'=>0,'total'=>1100, 'date'=>'May 30, 2026', 'status'=>'pending'],
          ];

          foreach ($surveys as $s):
            $pct = ($s['total'] > 0) ? round(($s['responses'] / $s['total']) * 100) : 0;
          ?>
          <tr data-status="<?= $s['status'] ?>" data-cat="<?= $s['cat'] ?>">
            <td>
              <div class="survey-cell">
                <span class="survey-title"><?= htmlspecialchars($s['title']) ?></span>
                <span class="survey-desc"><?= htmlspecialchars($s['desc']) ?></span>
              </div>
            </td>
            <td><span class="cat-pill"><?= $s['cat'] ?></span></td>
            <td style="font-weight:600; color:var(--ink);"><?= number_format($s['responses']) ?></td>
            <td>
              <div class="prog-wrap">
                <div class="prog-track">
                  <div class="prog-fill" style="width:<?= $pct ?>%;"></div>
                </div>
                <span class="prog-num"><?= $pct ?>%</span>
              </div>
            </td>
            <td style="color:var(--ink-3); font-size:13px;"><?= $s['date'] ?></td>
            <td>
              <?php if ($s['status'] === 'active'): ?>
                <span class="badge badge-active">Active</span>
              <?php elseif ($s['status'] === 'pending'): ?>
                <span class="badge badge-pending">Pending</span>
              <?php else: ?>
                <span class="badge badge-closed">Closed</span>
              <?php endif; ?>
            </td>
            <td>
              <div class="action-btns">
                <a href="#" class="action-btn">
                  <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" fill="none" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                  View
                </a>
                <a href="#" class="action-btn">
                  <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" fill="none" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                  Edit
                </a>
                <a href="#" class="action-btn danger">
                  <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" fill="none" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/></svg>
                </a>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>

        </tbody>
      </table>

      <div id="emptyState" style="display:none; text-align:center; padding:3rem 1rem;">
        <p style="color:var(--ink-3); font-size:14px;">No surveys match your filters.</p>
      </div>

      <div class="pagination">
        <span id="pagInfo">Showing 1–3 of 3 surveys</span>
        <div class="page-btns">
          <button class="page-btn">
            <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
          </button>
          <button class="page-btn active">1</button>
          <button class="page-btn">
            <svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
          </button>
        </div>
      </div>
    </div>
  </section>

</div>

<script>
  function filterSurveys() {
    const q      = document.getElementById('surveySearch').value.toLowerCase();
    const cat    = document.getElementById('catFilter').value;
    const status = document.getElementById('statusFilter').value;
    const rows   = document.querySelectorAll('#surveyTable tr');
    let visible  = 0;

    rows.forEach(row => {
      const title  = row.querySelector('.survey-title')?.textContent.toLowerCase() || '';
      const rcat   = row.dataset.cat;
      const rstatus= row.dataset.status;

      const qOk      = !q      || title.includes(q);
      const catOk    = !cat    || rcat    === cat;
      const statusOk = !status || rstatus === status;
      const show     = qOk && catOk && statusOk;

      row.style.display = show ? '' : 'none';
      if (show) visible++;
    });

    document.getElementById('emptyState').style.display = visible === 0 ? 'block' : 'none';
    document.getElementById('pagInfo').textContent =
      'Showing 1–' + visible + ' of ' + visible + ' survey' + (visible !== 1 ? 's' : '');
  }
</script>

<?php include('./includes/footer.php'); ?>