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
    --teal:      #1b6b6b;
    --teal-lt:   #d0eaea;
    --rose:      #a02c2c;
    --rose-lt:   #f5dede;
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
    width: 240px; outline: none;
    transition: border-color 0.15s;
  }
  .search-wrap input::placeholder { color: var(--ink-3); }
  .search-wrap input:focus { border-color: var(--gold); background: #fff; }

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
    transition: border-color 0.15s, box-shadow 0.15s;
  }
  .stat-card:hover { border-color: var(--gold); box-shadow: var(--shadow); }
  .stat-card::before {
    content: ''; position: absolute;
    top: 0; left: 0; right: 0; height: 3px;
    background: var(--paper-3);
    border-radius: 2px 2px 0 0;
  }
  .stat-card.gold::before { background: var(--gold); }
  .stat-card.teal::before { background: var(--teal); }
  .stat-card.rose::before { background: var(--rose); }

  .stat-label { font-size: 11px; font-weight: 600; letter-spacing: 0.08em; text-transform: uppercase; color: var(--ink-3); margin-bottom: 8px; }
  .stat-value { font-family: 'DM Serif Display', serif; font-size: 2rem; line-height: 1; color: var(--ink); }
  .stat-sub   { font-size: 12px; color: var(--ink-3); margin-top: 5px; }

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
  tbody tr { border-bottom: 1px solid var(--paper-2); transition: background 0.1s; }
  tbody tr:last-child { border-bottom: none; }
  tbody tr:hover td { background: var(--paper); }
  td { padding: 13px 18px; color: var(--ink-2); vertical-align: middle; }

  /* Student cell */
  .student-cell { display: flex; align-items: center; gap: 10px; }
  .avatar {
    width: 36px; height: 36px; border-radius: 50%;
    background: var(--gold-light); border: 1.5px solid var(--paper-3);
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; font-weight: 700; color: var(--gold-dark);
    flex-shrink: 0;
  }
  .student-name { font-weight: 500; color: var(--ink); font-size: 14px; }
  .student-id   { font-size: 12px; color: var(--ink-3); margin-top: 1px; }

  /* Badges — Active=teal, Inactive=rose */
  .badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 10px; border-radius: 20px;
    font-size: 11.5px; font-weight: 600; white-space: nowrap;
  }
  .badge::before { content: ''; width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }

  .badge-active   { background: var(--teal-lt); color: var(--teal); }
  .badge-active::before   { background: var(--teal); animation: pulse 1.5s infinite; }

  .badge-inactive { background: var(--rose-lt); color: var(--rose); }
  .badge-inactive::before { background: var(--rose); }

  @keyframes pulse { 0%,100%{opacity:1;transform:scale(1);}50%{opacity:0.4;transform:scale(0.8);} }

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
  }
</style>

<div class="main-wrapper">

  <div class="breadcrumbs">
    <a href="index">Home</a>
    <span>/</span>
    <span class="current-page">Students</span>
  </div>

  <header>
    <h1>Registered <em>Students</em></h1>
    <div class="toolbar">
      <div class="search-wrap">
        <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" id="studentSearch" placeholder="Search students…" oninput="filterStudents()">
      </div>
      <select class="filter-select" id="statusFilter" onchange="filterStudents()">
        <option value="">All Statuses</option>
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
      </select>
      <select class="filter-select" id="surveyFilter" onchange="filterStudents()">
        <option value="">All Surveys</option>
        <option value="Mid-Term Faculty Evaluation">Mid-Term Faculty Evaluation</option>
        <option value="Online Learning Experience">Online Learning Experience</option>
        <option value="Facility &amp; Safety Assessment">Facility &amp; Safety Assessment</option>
      </select>
    </div>
  </header>

  <!-- 3-card stat strip -->
  <div class="stat-strip">
    <div class="stat-card gold">
      <div class="stat-label">Total Students</div>
      <div class="stat-value">507</div>
      <div class="stat-sub">Registered this term</div>
    </div>
    <div class="stat-card teal">
      <div class="stat-label">Active</div>
      <div class="stat-value">395</div>
      <div class="stat-sub">Recently participated</div>
    </div>
    <div class="stat-card rose">
      <div class="stat-label">Inactive</div>
      <div class="stat-value">112</div>
      <div class="stat-sub">No recent activity</div>
    </div>
  </div>

  <section style="width:100%;">
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Student</th>
            <th>Course / Section</th>
            <th>Latest Survey</th>
            <th>Last Active</th>
            <th style="text-align:center;">Surveys Completed</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody id="studentTable">
          <?php
          $students = [
            ['name'=>'Sophia Reyes',    'id'=>'USR-4011', 'course'=>'BSIT 3-A',  'survey'=>'Mid-Term Faculty Evaluation',    'date'=>'Mar 28, 2026', 'completed'=>3, 'status'=>'active'],
            ['name'=>'James Okafor',    'id'=>'USR-8029', 'course'=>'BSCS 2-B',  'survey'=>'Online Learning Experience',     'date'=>'Mar 27, 2026', 'completed'=>2, 'status'=>'active'],
            ['name'=>'Aiko Tanaka',     'id'=>'USR-1013', 'course'=>'BSED 4-A',  'survey'=>'Mid-Term Faculty Evaluation',    'date'=>'Mar 25, 2026', 'completed'=>3, 'status'=>'active'],
            ['name'=>'Luca Bianchi',    'id'=>'USR-5057', 'course'=>'BSBA 1-C',  'survey'=>'Online Learning Experience',     'date'=>'Mar 20, 2026', 'completed'=>1, 'status'=>'inactive'],
            ['name'=>'Fatima Hassan',   'id'=>'USR-7077', 'course'=>'BSIT 2-A',  'survey'=>'Facility & Safety Assessment',   'date'=>'Mar 10, 2026', 'completed'=>1, 'status'=>'inactive'],
            ['name'=>'Carlos Mendoza',  'id'=>'USR-3021', 'course'=>'BSCS 3-C',  'survey'=>'Mid-Term Faculty Evaluation',    'date'=>'Mar 28, 2026', 'completed'=>3, 'status'=>'active'],
            ['name'=>'Priya Nair',      'id'=>'USR-6044', 'course'=>'BSED 2-B',  'survey'=>'Online Learning Experience',     'date'=>'Mar 26, 2026', 'completed'=>2, 'status'=>'active'],
            ['name'=>'Marco Santos',    'id'=>'USR-9012', 'course'=>'BSBA 3-A',  'survey'=>'Mid-Term Faculty Evaluation',    'date'=>'Feb 28, 2026', 'completed'=>1, 'status'=>'inactive'],
          ];

          foreach ($students as $s):
            $initials = implode('', array_map(fn($w) => strtoupper($w[0]), explode(' ', $s['name'])));
          ?>
          <tr data-status="<?= $s['status'] ?>" data-survey="<?= htmlspecialchars($s['survey']) ?>">
            <td>
              <div class="student-cell">
                <div class="avatar"><?= $initials ?></div>
                <div>
                  <div class="student-name"><?= htmlspecialchars($s['name']) ?></div>
                  <div class="student-id"><?= $s['id'] ?></div>
                </div>
              </div>
            </td>
            <td style="font-size:13.5px;"><?= $s['course'] ?></td>
            <td style="font-size:13.5px;"><?= htmlspecialchars($s['survey']) ?></td>
            <td style="color:var(--ink-3); font-size:13px;"><?= $s['date'] ?></td>
            <td style="font-weight:600; color:var(--ink); text-align:center;"><?= $s['completed'] ?> / 3</td>
            <td>
              <?php if ($s['status'] === 'active'): ?>
                <span class="badge badge-active">Active</span>
              <?php else: ?>
                <span class="badge badge-inactive">Inactive</span>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <div id="emptyState" style="display:none; text-align:center; padding:3rem 1rem;">
        <p style="color:var(--ink-3); font-size:14px;">No students match your filters.</p>
      </div>

      <div class="pagination">
        <span id="pagInfo">Showing 1–8 of 507 students</span>
        <div class="page-btns">
          <button class="page-btn">
            <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
          </button>
          <button class="page-btn active">1</button>
          <button class="page-btn">2</button>
          <button class="page-btn">3</button>
          <button class="page-btn">
            <svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
          </button>
        </div>
      </div>
    </div>
  </section>
</div>

<script>
function filterStudents() {
  const q      = document.getElementById('studentSearch').value.toLowerCase();
  const status = document.getElementById('statusFilter').value;
  const survey = document.getElementById('surveyFilter').value;
  const rows   = document.querySelectorAll('#studentTable tr');
  let visible  = 0;

  rows.forEach(row => {
    const name    = row.querySelector('.student-name')?.textContent.toLowerCase() || '';
    const id      = row.querySelector('.student-id')?.textContent.toLowerCase() || '';
    const rstatus = row.dataset.status;
    const rsurvey = row.dataset.survey;

    const qOk      = !q      || name.includes(q) || id.includes(q);
    const statusOk = !status || rstatus === status;
    const surveyOk = !survey || rsurvey === survey;
    const show     = qOk && statusOk && surveyOk;

    row.style.display = show ? '' : 'none';
    if (show) visible++;
  });

  document.getElementById('emptyState').style.display = visible === 0 ? 'block' : 'none';
  document.getElementById('pagInfo').textContent =
    'Showing 1–' + visible + ' of 507 students';
}
</script>

<?php include('./includes/footer.php'); ?>