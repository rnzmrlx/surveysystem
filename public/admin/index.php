<?php
include('../../app/middleware/admin.php');
include('./includes/header.php');
include('./includes/topbar.php');
include('./includes/sidebar.php');
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&display=swap" rel="stylesheet">

<style>
:root {
    --ink:       #0f0e0d;
    --ink-2:     #3a3835;
    --ink-3:     #7a776f;
    --paper:     #f7f5f0;
    --paper-2:   #eceae3;
    --paper-3:   #dedad0;
    --gold:      #c9972b;
    --gold-lt:   #f5e9cc;
    --teal:      #1b6b6b;
    --teal-lt:   #d0eaea;
    --rose:      #a02c2c;
    --rose-lt:   #f2d8d8;
    --amber:     #8a5e00;
    --amber-lt:  #fff0cc;
    --radius:    14px;
    --shadow:    0 2px 16px rgba(15,14,13,0.07);
  }

  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: 'DM Sans', sans-serif; background: var(--paper); color: var(--ink); }

  /* ── Layout ── */
  .main-wrapper { padding: 2rem 2.5rem 3rem; max-width: 1440px; margin: 0 auto; }

  /* ── Page Header ── */
  .page-header {
    display: flex; justify-content: space-between; align-items: flex-end;
    margin-bottom: 2rem; padding-bottom: 1.5rem;
    border-bottom: 1.5px solid var(--paper-3);
  }
  .page-header-left h1 {
    font-family: 'DM Serif Display', serif;
    font-size: 2.4rem; font-weight: 400; line-height: 1.1;
  }
  .page-header-left h1 em { color: var(--gold); font-style: italic; }
  .page-header-left p { font-size: 13px; color: var(--ink-3); margin-top: 4px; }
  .page-header-right { font-size: 12.5px; color: var(--ink-3); text-align: right; line-height: 1.6; }
  .page-header-right strong { color: var(--ink-2); }

  /* ── Stat Strip (3 cards now) ── */
  .stat-strip { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 1.5rem; }
  .stat-card {
    background: #fff; border: 1.5px solid var(--paper-3);
    border-radius: var(--radius); padding: 1.25rem 1.5rem;
    box-shadow: var(--shadow); display: flex; align-items: center; gap: 1rem;
    position: relative; overflow: hidden;
  }
  .stat-card::before {
    content: ''; position: absolute; top: 0; left: 0;
    width: 4px; height: 100%; border-radius: 4px 0 0 4px;
  }
  .stat-card.gold::before  { background: var(--gold); }
  .stat-card.teal::before  { background: var(--teal); }
  .stat-card.rose::before  { background: var(--rose); }

  .stat-icon {
    width: 44px; height: 44px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;
  }
  .stat-card.gold  .stat-icon { background: var(--gold-lt);  }
  .stat-card.teal  .stat-icon { background: var(--teal-lt);  }
  .stat-card.rose  .stat-icon { background: var(--rose-lt);  }

  .stat-body {}
  .stat-label { font-size: 11px; text-transform: uppercase; letter-spacing: 0.06em; color: var(--ink-3); font-weight: 600; }
  .stat-val   { font-family: 'DM Serif Display', serif; font-size: 2rem; line-height: 1.1; margin: 2px 0; }
  .stat-sub   { font-size: 11px; color: var(--ink-3); }

  /* ── Main Grid ── */
  .dash-grid { display: grid; grid-template-columns: 1fr 380px; gap: 1.5rem; }

  /* ── Cards ── */
  .card {
    background: #fff; border: 1.5px solid var(--paper-3);
    border-radius: var(--radius); padding: 1.5rem;
    box-shadow: var(--shadow); margin-bottom: 1.5rem;
  }
  .card:last-child { margin-bottom: 0; }
  .card-head {
    display: flex; justify-content: space-between; align-items: center;
    margin-bottom: 1.25rem;
  }
  .card-head h2 { font-family: 'DM Serif Display', serif; font-size: 1.2rem; font-weight: 400; }
  .card-head span { font-size: 11.5px; color: var(--ink-3); }

  /* ── Survey Table ── */
  .survey-table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
  .survey-table th {
    text-align: left; padding: 9px 12px;
    font-size: 10.5px; color: var(--ink-3); font-weight: 600;
    text-transform: uppercase; letter-spacing: 0.05em;
    border-bottom: 1.5px solid var(--paper-2);
  }
  .survey-table td { padding: 13px 12px; border-bottom: 1px solid var(--paper-2); vertical-align: middle; }
  .survey-table tbody tr:last-child td { border-bottom: none; }
  .survey-table tbody tr:hover td { background: var(--paper); transition: background 0.15s; }

  .survey-name { font-weight: 500; color: var(--ink-2); }
  .survey-cat  { font-size: 12px; color: var(--ink-3); margin-top: 2px; }

  /* Progress bar */
  .prog-wrap { display: flex; align-items: center; gap: 8px; }
  .prog-bar  { flex: 1; height: 6px; background: var(--paper-2); border-radius: 99px; overflow: hidden; }
  .prog-fill { height: 100%; border-radius: 99px; }
  .prog-fill.teal  { background: var(--teal); }
  .prog-fill.gold  { background: var(--gold); }
  .prog-fill.amber { background: #d4a017; }
  .prog-pct  { font-size: 12px; color: var(--ink-3); width: 32px; text-align: right; }

  /* Badges — updated color mapping */
  .badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: 99px; font-size: 11px; font-weight: 600; }
  .badge::before { content: ''; width: 6px; height: 6px; border-radius: 50%; }

  /* Closed → rose */
  .badge-closed  { background: var(--rose-lt); color: var(--rose); }
  .badge-closed::before  { background: var(--rose); }

  /* Active → teal */
  .badge-active { background: var(--teal-lt); color: var(--teal); }
  .badge-active::before { background: var(--teal); }

  /* Pending → gold */
  .badge-pending { background: var(--gold-lt); color: var(--gold); }
  .badge-pending::before { background: var(--gold); }

  /* ── Pie Chart Card ── */
  .pie-wrap { position: relative; height: 200px; display: flex; align-items: center; justify-content: center; }
  .pie-center-label {
    position: absolute; text-align: center; pointer-events: none;
  }
  .pie-center-label .big { font-family: 'DM Serif Display', serif; font-size: 1.8rem; }
  .pie-center-label .sm  { font-size: 11px; color: var(--ink-3); }

  .legend-list { list-style: none; display: flex; flex-direction: column; gap: 10px; margin-top: 1.25rem; }
  .legend-list li { display: flex; align-items: center; justify-content: space-between; font-size: 13px; }
  .legend-dot { display: inline-flex; align-items: center; gap: 8px; }
  .legend-dot span { width: 10px; height: 10px; border-radius: 3px; display: inline-block; }
  .legend-count { font-family: 'DM Serif Display', serif; font-size: 1.05rem; }

  /* ── Response Rate Card ── */
  .resp-bar-list { display: flex; flex-direction: column; gap: 14px; }
  .resp-item { display: flex; flex-direction: column; gap: 4px; }
  .resp-label { display: flex; justify-content: space-between; font-size: 12.5px; }
  .resp-label span:first-child { color: var(--ink-2); font-weight: 500; }
  .resp-label span:last-child  { color: var(--ink-3); }
  .resp-track { height: 8px; background: var(--paper-2); border-radius: 99px; overflow: hidden; }
  .resp-fill  { height: 100%; border-radius: 99px; transition: width 0.6s ease; }

  /* ── Quick Stats row at bottom of right col ── */
  .mini-stat-row { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
  .mini-stat { background: var(--paper-2); border-radius: 10px; padding: 12px 14px; }
  .mini-stat-label { font-size: 10.5px; color: var(--ink-3); text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600; }
  .mini-stat-val   { font-family: 'DM Serif Display', serif; font-size: 1.4rem; margin-top: 2px; }

  @media (max-width: 1100px) {
    .dash-grid { grid-template-columns: 1fr; }
    .stat-strip { grid-template-columns: repeat(2, 1fr); }
  }
  @media (max-width: 640px) {
    .stat-strip { grid-template-columns: 1fr; }
  }
</style>

<div class="main-wrapper">

  <!-- Page Header -->
  <div class="page-header">
    <div class="page-header-left">
      <h1>QuickQuery <em>Dashboard</em></h1>
      <p>Overview</p>
    </div>
    <div class="page-header-right">
      <strong><?= date('l, F d Y') ?></strong><br>
      Academic Term · A.Y. 2025–2026
    </div>
  </div>

  <!-- Stat Strip (3 cards) -->
  <div class="stat-strip">
    <div class="stat-card gold">
      <div class="stat-icon">🎓</div>
      <div class="stat-body">
        <div class="stat-label">Total Students</div>
        <div class="stat-val">507</div>
        <div class="stat-sub">Enrolled this term</div>
      </div>
    </div>
    <div class="stat-card teal">
      <div class="stat-icon">📋</div>
      <div class="stat-body">
        <div class="stat-label">Active Surveys</div>
        <div class="stat-val">1</div>
        <div class="stat-sub">Currently running</div>
      </div>
    </div>
    <div class="stat-card rose">
      <div class="stat-icon">📊</div>
      <div class="stat-body">
        <div class="stat-label">Total Responses</div>
        <div class="stat-val">2,420</div>
        <div class="stat-sub">Across all surveys</div>
      </div>
    </div>
  </div>

  <!-- Main Grid -->
  <div class="dash-grid">

    <!-- LEFT COLUMN -->
    <div class="left-col">

      <!-- Survey Management Table -->
      <div class="card">
        <div class="card-head">
          <h2>Survey Management</h2>
          <span>3 surveys · 507 students</span>
        </div>
        <table class="survey-table">
          <thead>
            <tr>
              <th>Survey</th>
              <th>Category</th>
              <th>Responses</th>
              <th>Completion</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>
                <div class="survey-name">Mid-Term Faculty Evaluation</div>
                <div class="survey-cat">Academic Performance</div>
              </td>
              <td>Academic</td>
              <td>507 / 507</td>
              <td>
                <div class="prog-wrap">
                  <div class="prog-bar"><div class="prog-fill" style="width:100%; background:var(--rose);"></div></div>
                  <span class="prog-pct">100%</span>
                </div>
              </td>
              <td><span class="badge badge-closed">Closed</span></td>
            </tr>
            <tr>
              <td>
                <div class="survey-name">Online Learning Experience</div>
                <div class="survey-cat">Digital Education</div>
              </td>
              <td>Wellness</td>
              <td>461 / 507</td>
              <td>
                <div class="prog-wrap">
                  <div class="prog-bar"><div class="prog-fill teal" style="width:91%"></div></div>
                  <span class="prog-pct">91%</span>
                </div>
              </td>
              <td><span class="badge badge-active">Active</span></td>
            </tr>
            <tr>
              <td>
                <div class="survey-name">Facility &amp; Safety Assessment</div>
                <div class="survey-cat">Campus Services</div>
              </td>
              <td>Facilities</td>
              <td>0 / 507</td>
              <td>
                <div class="prog-wrap">
                  <div class="prog-bar"><div style="width:0%"></div></div>
                  <span class="prog-pct">0%</span>
                </div>
              </td>
              <td><span class="badge badge-pending">Pending</span></td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Response Rate per Survey -->
      <div class="card">
        <div class="card-head">
          <h2>Response Rate by Survey</h2>
          <span>Out of 507 students</span>
        </div>
        <div class="resp-bar-list">
          <div class="resp-item">
            <div class="resp-label">
              <span>Mid-Term Faculty Evaluation</span>
              <span>507 responses · 100%</span>
            </div>
            <div class="resp-track"><div class="resp-fill" style="width:100%; background:var(--rose);"></div></div>
          </div>
          <div class="resp-item">
            <div class="resp-label">
              <span>Online Learning Experience</span>
              <span>461 responses · 91%</span>
            </div>
            <div class="resp-track"><div class="resp-fill" style="width:91%; background:var(--teal);"></div></div>
          </div>
          <div class="resp-item">
            <div class="resp-label">
              <span>Facility &amp; Safety Assessment</span>
              <span>0 responses · 0%</span>
            </div>
            <div class="resp-track"><div style="width:89%; background:#d4a017;"></div></div>
          </div>
        </div>
      </div>

    </div>

    <!-- RIGHT COLUMN -->
    <div class="right-col">

      <!-- Pie Chart: Survey Status Breakdown -->
      <div class="card">
        <div class="card-head">
          <h2>Survey Status</h2>
          <span>3 total surveys</span>
        </div>
        <div class="pie-wrap">
          <canvas id="chartPie" style="max-height:200px;"></canvas>
          <div class="pie-center-label">
            <div class="big">3</div>
            <div class="sm">Surveys</div>
          </div>
        </div>
        <ul class="legend-list">
          <li>
            <div class="legend-dot"><span style="background:var(--rose)"></span> Closed</div>
            <span class="legend-count" style="color:var(--rose);">1</span>
          </li>
          <li>
            <div class="legend-dot"><span style="background:var(--teal)"></span> Active</div>
            <span class="legend-count" style="color:var(--teal);">1</span>
          </li>
          <li>
            <div class="legend-dot"><span style="background:var(--gold)"></span> Pending</div>
            <span class="legend-count" style="color:var(--gold);">1</span>
          </li>
        </ul>
      </div>

      <!-- Student Completion Quick Stats -->
      <div class="card">
        <div class="card-head">
          <h2>Student Overview</h2>
        </div>
        <div class="mini-stat-row" style="margin-bottom: 1rem;">
          <div class="mini-stat">
            <div class="mini-stat-label">Completed All</div>
            <div class="mini-stat-val" style="color:var(--teal);">441</div>
          </div>
          <div class="mini-stat">
            <div class="mini-stat-label">Partial</div>
            <div class="mini-stat-val" style="color:var(--gold);">54</div>
          </div>
          <div class="mini-stat">
            <div class="mini-stat-label">Not Started</div>
            <div class="mini-stat-val" style="color:var(--rose);">12</div>
          </div>
          <div class="mini-stat">
            <div class="mini-stat-label">Total Students</div>
            <div class="mini-stat-val">507</div>
          </div>
        </div>
        <a href="results.php" style="display: block; text-align: center; font-size: 12.5px; font-weight: 600; color: var(--gold); text-decoration: none; padding: 10px; border: 1.5px solid var(--gold-lt); border-radius: 8px; transition: background 0.15s;">
          View Full Results →
        </a>
      </div>

    </div>
  </div>

</div>

<script>
const rose  = '#a02c2c';
const teal  = '#1b6b6b';
const gold  = '#c9972b';

// Pie Chart — Survey Status Distribution
// Order: Closed (rose), Active (teal), Pending (gold)
const ctxPie = document.getElementById('chartPie');
new Chart(ctxPie, {
  type: 'doughnut',
  data: {
    labels: ['Closed', 'Active', 'Pending'],
    datasets: [{
      data: [1, 1, 1],
      backgroundColor: [rose, teal, gold],
      borderWidth: 3,
      borderColor: '#ffffff',
      hoverOffset: 8
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    cutout: '68%',
    plugins: {
      legend: { display: false },
      tooltip: {
        callbacks: {
          label: ctx => ` ${ctx.label}: ${ctx.raw} survey`
        }
      }
    }
  }
});
</script>

<?php include('./includes/footer.php'); ?>