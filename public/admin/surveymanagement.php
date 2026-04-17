<?php
include('../../app/middleware/admin.php');
include('./includes/header.php');
include('./includes/topbar.php');
include('./includes/sidebar.php');
include('../../app/config/config.php');

// Fetch all surveys with response count
$sql = "SELECT s.id, s.title, s.description, s.status, s.created_at, s.category_id,
               COUNT(DISTINCT r.user_id) as response_count
        FROM surveys s
        LEFT JOIN responses r ON r.survey_id = s.id
        GROUP BY s.id
        ORDER BY s.created_at DESC";
$result = mysqli_query($conn, $sql);

$surveys      = [];
$statTotal    = 0;
$statPublished = 0;
$statPending  = 0;
$statClosed   = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $surveys[] = $row;
    $statTotal++;
    if ($row['status'] === 'published') $statPublished++;
    elseif ($row['status'] === 'pending') $statPending++;
    elseif ($row['status'] === 'closed')  $statClosed++;
}

// Total registered students (for response rate denominator)
$usersRes     = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM users WHERE role = 'user'");
$totalStudents = (int)mysqli_fetch_assoc($usersRes)['cnt'];
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
  body { font-family: 'DM Sans', sans-serif; background: var(--paper); color: var(--ink); }

  .main-wrapper { padding: 2.5rem 2.75rem 4rem; max-width: 1440px; margin: 0 auto; width: 100%; }

  .breadcrumbs {
    display: flex; align-items: center; gap: 8px;
    font-size: 12.5px; font-weight: 500; letter-spacing: .04em;
    text-transform: uppercase; color: var(--ink-3); margin-bottom: 2rem;
  }
  .breadcrumbs a { color: var(--ink-3); text-decoration: none; transition: color .15s; }
  .breadcrumbs a:hover { color: var(--gold); }
  .breadcrumbs span { color: var(--paper-3); }
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
    width: 220px; outline: none; transition: border-color .15s;
  }
  .search-wrap input::placeholder { color: var(--ink-3); }
  .search-wrap input:focus { border-color: var(--gold); }
  .filter-select {
    background: var(--paper-2); border: 1.5px solid var(--paper-3);
    border-radius: var(--radius); padding: 8px 14px;
    font-family: 'DM Sans', sans-serif; font-size: 13.5px; color: var(--ink);
    outline: none; cursor: pointer; transition: border-color .15s;
  }
  .filter-select:focus { border-color: var(--gold); }
  .btn-gold {
    display: flex; align-items: center; gap: 6px;
    background: var(--gold); color: #fff; border: none;
    border-radius: var(--radius); padding: 8.5px 16px;
    font-family: 'DM Sans', sans-serif; font-size: 13.5px; font-weight: 500;
    cursor: pointer; text-decoration: none;
    transition: background .15s, transform .1s; white-space: nowrap;
  }
  .btn-gold:hover { background: var(--gold-dark); color: #fff; }
  .btn-gold:active { transform: scale(.98); }
  .btn-gold svg { width: 14px; height: 14px; stroke: currentColor; fill: none; stroke-width: 2; flex-shrink: 0; }

  .stat-strip { display: grid; grid-template-columns: repeat(4,1fr); gap: 14px; margin-bottom: 2rem; }
  .stat-card {
    background: var(--paper-2); border: 1.5px solid var(--paper-3);
    border-radius: var(--radius); padding: 1.1rem 1.25rem;
    position: relative; overflow: hidden; transition: border-color .15s, box-shadow .15s;
  }
  .stat-card:hover { border-color: var(--gold); box-shadow: var(--shadow); }
  .stat-card::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: var(--paper-3); border-radius: 2px 2px 0 0;
  }
  .stat-card.gold::before     { background: var(--gold); }
  .stat-card.teal::before     { background: var(--teal); }
  .stat-card.gold-acc::before { background: var(--gold); }
  .stat-card.rose::before     { background: var(--rose); }
  .stat-label { font-size: 11px; font-weight: 600; letter-spacing: .08em; text-transform: uppercase; color: var(--ink-3); margin-bottom: 8px; }
  .stat-value { font-family: 'DM Serif Display', serif; font-size: 2rem; line-height: 1; color: var(--ink); }
  .stat-sub   { font-size: 12px; color: var(--ink-3); margin-top: 5px; }

  .table-wrap { background: #fff; border: 1.5px solid var(--paper-3); border-radius: var(--radius); overflow: hidden; box-shadow: var(--shadow); }
  .table-scroll { overflow-x: auto; -webkit-overflow-scrolling: touch; }
  table { width: 100%; border-collapse: collapse; font-size: 14px; min-width: 640px; }
  thead tr { background: var(--paper-2); border-bottom: 1.5px solid var(--paper-3); }
  th {
    padding: 12px 18px; text-align: left; font-size: 11px; font-weight: 600;
    letter-spacing: .08em; text-transform: uppercase; color: var(--ink-3); white-space: nowrap;
  }
  tbody tr { border-bottom: 1px solid var(--paper-2); transition: background .1s; }
  tbody tr:last-child { border-bottom: none; }
  tbody tr:hover { background: var(--paper); }
  td { padding: 13px 18px; color: var(--ink-2); vertical-align: middle; }

  th.col-actions, td.col-actions {
    position: sticky; right: 0; background: inherit; z-index: 2;
    box-shadow: -4px 0 10px rgba(15,14,13,.06); white-space: nowrap;
  }
  thead th.col-actions { background: var(--paper-2); }
  tbody tr:hover td.col-actions { background: var(--paper); }

  .survey-cell { display: flex; flex-direction: column; gap: 2px; }
  .survey-title { font-weight: 500; color: var(--ink); font-size: 14px; }
  .survey-desc  { font-size: 12px; color: var(--ink-3); }

  .prog-wrap { display: flex; align-items: center; gap: 10px; min-width: 130px; }
  .prog-track { flex: 1; height: 5px; background: var(--paper-2); border-radius: 3px; overflow: hidden; }
  .prog-fill  { height: 100%; border-radius: 3px; background: var(--gold); transition: width .4s; }
  .prog-num   { font-weight: 600; font-size: 13px; min-width: 36px; text-align: right; color: var(--ink); }

  .badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; white-space: nowrap; }
  .badge::before { content: ''; width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
  .badge-published { background: var(--teal-lt); color: var(--teal); }
  .badge-published::before { background: var(--teal); animation: pulse 1.5s infinite; }
  .badge-closed  { background: var(--rose-lt); color: var(--rose); }
  .badge-closed::before  { background: var(--rose); }
  .badge-pending { background: var(--gold-light); color: var(--gold-dark); }
  .badge-pending::before { background: var(--gold); }
  @keyframes pulse { 0%,100%{opacity:1;transform:scale(1);}50%{opacity:.4;transform:scale(.8);} }

  .action-btn {
    background: none; border: 1.5px solid var(--paper-3);
    border-radius: 7px; padding: 5px 11px;
    font-family: 'DM Sans', sans-serif; font-size: 12.5px; color: var(--ink-2);
    cursor: pointer; text-decoration: none;
    transition: border-color .15s, color .15s, background .15s;
    display: inline-flex; align-items: center; gap: 5px; white-space: nowrap;
  }
  .action-btn:hover { border-color: var(--ink-2); color: var(--ink); background: var(--paper); }
  .action-btn.danger:hover { border-color: var(--rose); color: var(--rose); background: var(--rose-lt); }
  .action-btns { display: flex; align-items: center; gap: 5px; }

  .pagination {
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 18px; border-top: 1.5px solid var(--paper-2);
    font-size: 13px; color: var(--ink-3); background: var(--paper);
  }
  .page-btns { display: flex; gap: 6px; }
  .page-btn {
    width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;
    border-radius: 7px; border: 1.5px solid var(--paper-3); background: none;
    font-family: 'DM Sans', sans-serif; font-size: 13px; color: var(--ink-2);
    cursor: pointer; transition: all .12s;
  }
  .page-btn:hover { border-color: var(--ink-2); color: var(--ink); }
  .page-btn.active { background: var(--ink); color: var(--paper); border-color: var(--ink); }
  .page-btn svg { width: 14px; height: 14px; stroke: currentColor; fill: none; stroke-width: 2; }

  @media (max-width: 900px) {
    .main-wrapper { padding: 1.5rem 1.25rem 3rem; }
    .stat-strip { grid-template-columns: repeat(2,1fr); }
  }

  /* ── Modal styles (kept from original) ── */
  .modal-backdrop {
    display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0;
    width: 100vw; height: 100vh; background: rgba(15,14,13,.65);
    z-index: 2147483647; align-items: center; justify-content: center; padding: 1rem;
  }
  .modal-backdrop.open { display: flex; }
  .modal {
    background: #fff; border: 1.5px solid var(--paper-3);
    border-radius: 16px; box-shadow: 0 12px 48px rgba(15,14,13,.2);
    width: 100%; max-width: 660px; max-height: 90vh;
    overflow-y: auto; position: relative; animation: modalIn .22s ease;
  }
  @keyframes modalIn { from{opacity:0;transform:translateY(14px);}to{opacity:1;transform:translateY(0);} }
  .modal-steps { display: flex; align-items: center; padding: 1.5rem 2.25rem 0; gap: 0; }
  .step-item { display: flex; align-items: center; gap: 8px; flex: 1; }
  .step-item:last-child { flex: none; }
  .step-dot {
    width: 28px; height: 28px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; font-weight: 600; border: 2px solid var(--paper-3);
    color: var(--ink-3); background: var(--paper-2); flex-shrink: 0; transition: all .2s;
  }
  .step-dot.active { border-color: var(--gold); background: var(--gold); color: #fff; }
  .step-dot.done   { border-color: var(--teal); background: var(--teal); color: #fff; }
  .step-label { font-size: 12px; font-weight: 600; color: var(--ink-3); text-transform: uppercase; letter-spacing: .05em; transition: color .2s; }
  .step-label.active { color: var(--ink); }
  .step-line { flex: 1; height: 2px; background: var(--paper-3); margin: 0 10px; transition: background .3s; }
  .step-line.done { background: var(--teal); }
  .modal-body { padding: 1.75rem 2.25rem 2.25rem; }
  .modal-head { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem; }
  .modal-head h2 { font-family: 'DM Serif Display', serif; font-size: 1.5rem; font-weight: 400; }
  .modal-head h2 em { font-style: italic; color: var(--gold); }
  .modal-close { background: none; border: none; cursor: pointer; color: var(--ink-3); padding: 2px; line-height: 1; transition: color .15s; font-size: 22px; }
  .modal-close:hover { color: var(--ink); }
  .step-panel { display: none; }
  .step-panel.active { display: block; }
  .form-grid { display: flex; flex-direction: column; gap: 1.1rem; }
  .form-row  { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
  .field { display: flex; flex-direction: column; gap: 5px; }
  .field label { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; color: var(--ink-3); }
  .field input, .field select, .field textarea {
    background: var(--paper-2); border: 1.5px solid var(--paper-3);
    border-radius: var(--radius); padding: 9px 13px;
    font-family: 'DM Sans', sans-serif; font-size: 13.5px; color: var(--ink);
    outline: none; transition: border-color .15s; width: 100%;
  }
  .field input:focus, .field select:focus, .field textarea:focus { border-color: var(--gold); background: #fff; }
  .field textarea { resize: vertical; min-height: 72px; }
  .field-error { font-size: 12px; color: var(--rose); background: var(--rose-lt); border-radius: 7px; padding: 7px 12px; margin-top: .5rem; display: none; }
  .field-error.show { display: block; }
  .questions-list { display: flex; flex-direction: column; gap: 12px; margin-bottom: 14px; }
  .question-card { background: var(--paper-2); border: 1.5px solid var(--paper-3); border-radius: var(--radius); padding: 1rem 1.1rem; transition: border-color .15s; }
  .question-card:focus-within { border-color: var(--gold); }
  .q-row { display: flex; gap: 10px; align-items: flex-start; }
  .q-num { width: 24px; height: 24px; border-radius: 50%; background: var(--gold-light); color: var(--gold-dark); font-size: 11px; font-weight: 700; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px; }
  .q-body { flex: 1; display: flex; flex-direction: column; gap: 8px; }
  .q-body input[type="text"], .q-body select { background: #fff; border: 1.5px solid var(--paper-3); border-radius: 8px; padding: 8px 12px; font-family: 'DM Sans', sans-serif; font-size: 13.5px; color: var(--ink); outline: none; transition: border-color .15s; width: 100%; }
  .q-body input[type="text"]:focus, .q-body select:focus { border-color: var(--gold); }
  .q-type-row { display: flex; align-items: center; gap: 10px; }
  .q-type-row select { flex: 1; }
  .q-required-toggle { display: flex; align-items: center; gap: 6px; font-size: 12px; color: var(--ink-3); cursor: pointer; user-select: none; white-space: nowrap; }
  .q-required-toggle input { accent-color: var(--gold); width: 14px; height: 14px; cursor: pointer; }
  .q-options { display: flex; flex-direction: column; gap: 6px; margin-top: 4px; }
  .q-option-row { display: flex; align-items: center; gap: 6px; }
  .q-option-row input { flex: 1; background: #fff; border: 1.5px solid var(--paper-3); border-radius: 7px; padding: 6px 10px; font-family: 'DM Sans', sans-serif; font-size: 13px; color: var(--ink); outline: none; transition: border-color .15s; }
  .q-option-row input:focus { border-color: var(--gold); }
  .btn-icon { background: none; border: 1.5px solid var(--paper-3); border-radius: 6px; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; cursor: pointer; color: var(--ink-3); transition: all .12s; flex-shrink: 0; }
  .btn-icon:hover { border-color: var(--rose); color: var(--rose); background: var(--rose-lt); }
  .btn-icon svg { width: 13px; height: 13px; stroke: currentColor; fill: none; stroke-width: 2; }
  .btn-add-opt { background: none; border: 1.5px dashed var(--paper-3); border-radius: 7px; padding: 5px 12px; font-family: 'DM Sans', sans-serif; font-size: 12.5px; color: var(--ink-3); cursor: pointer; text-align: left; transition: all .12s; display: flex; align-items: center; gap: 5px; width: 100%; }
  .btn-add-opt:hover { border-color: var(--gold); color: var(--gold); }
  .q-delete { background: none; border: none; cursor: pointer; color: var(--ink-3); padding: 2px; transition: color .15s; flex-shrink: 0; margin-top: 2px; font-size: 20px; line-height: 1; }
  .q-delete:hover { color: var(--rose); }
  .btn-add-q { display: flex; align-items: center; gap: 7px; width: 100%; padding: 10px 14px; background: none; border: 1.5px dashed var(--paper-3); border-radius: var(--radius); font-family: 'DM Sans', sans-serif; font-size: 13.5px; color: var(--ink-3); cursor: pointer; transition: all .15s; }
  .btn-add-q:hover { border-color: var(--gold); color: var(--gold); background: var(--gold-light); }
  .btn-add-q svg { width: 14px; height: 14px; stroke: currentColor; fill: none; stroke-width: 2; }
  .scale-preview { display: flex; gap: 6px; margin-top: 4px; }
  .scale-pip { width: 32px; height: 32px; border-radius: 8px; border: 1.5px solid var(--paper-3); display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600; color: var(--ink-3); background: #fff; }
  .scale-hint { font-size: 12px; color: var(--ink-3); margin-top: 4px; }
  .review-card { background: var(--paper-2); border: 1.5px solid var(--paper-3); border-radius: var(--radius); padding: 1rem 1.1rem; display: flex; flex-direction: column; gap: 6px; margin-bottom: 10px; }
  .review-q-card { background: var(--paper-2); border: 1.5px solid var(--paper-3); border-radius: var(--radius); padding: .85rem 1rem; display: flex; gap: 10px; align-items: flex-start; margin-bottom: 6px; }
  .review-section-label { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; color: var(--ink-3); margin: 12px 0 6px; }
  .modal-actions { display: flex; justify-content: space-between; align-items: center; gap: 10px; margin-top: 1.75rem; padding-top: 1.25rem; border-top: 1.5px solid var(--paper-2); }
  .modal-actions-right { display: flex; gap: 10px; }
  .btn-ghost { background: none; border: 1.5px solid var(--paper-3); border-radius: var(--radius); padding: 8.5px 16px; font-family: 'DM Sans', sans-serif; font-size: 13.5px; font-weight: 500; color: var(--ink-2); cursor: pointer; transition: border-color .15s; display: flex; align-items: center; gap: 6px; }
  .btn-ghost:hover { border-color: var(--ink-2); }
  .btn-ghost svg { width: 14px; height: 14px; stroke: currentColor; fill: none; stroke-width: 2; }
  .btn-launch { display: flex; align-items: center; gap: 6px; background: var(--teal); color: #fff; border: none; border-radius: var(--radius); padding: 8.5px 18px; font-family: 'DM Sans', sans-serif; font-size: 13.5px; font-weight: 500; cursor: pointer; transition: background .15s, transform .1s; }
  .btn-launch:hover { background: #0f4a4a; }
  .btn-launch:active { transform: scale(.98); }
  .btn-launch svg { width: 14px; height: 14px; stroke: currentColor; fill: none; stroke-width: 2; }
  .toast { position: fixed; bottom: 2rem; right: 2rem; background: var(--ink); color: var(--paper); border-radius: 10px; padding: 12px 20px; font-size: 13.5px; font-weight: 500; box-shadow: 0 8px 32px rgba(15,14,13,.25); transform: translateY(80px); opacity: 0; transition: all .3s cubic-bezier(.34,1.56,.64,1); z-index: 2147483647; pointer-events: none; display: flex; align-items: center; gap: 9px; }
  .toast.show { transform: translateY(0); opacity: 1; }
  .toast svg { width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 2; flex-shrink: 0; }
  .toast.success { background: var(--teal); }
  .toast.error   { background: var(--rose); }
  .cat-pill { background: var(--gold-light); color: var(--gold-dark); font-size: 11.5px; font-weight: 600; padding: 3px 10px; border-radius: 20px; border: 1px solid #e8d49a; white-space: nowrap; }
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
      <select class="filter-select" id="statusFilter" onchange="filterSurveys()">
        <option value="">All Statuses</option>
        <option value="published">Published</option>
        <option value="pending">Pending</option>
        <option value="closed">Closed</option>
      </select>
      <button onclick="openModal()" class="btn-gold">
        <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        New Survey
      </button>
    </div>
  </header>

  <div class="stat-strip">
    <div class="stat-card gold">
      <div class="stat-label">Total Surveys</div>
      <div class="stat-value" id="statTotal"><?= $statTotal ?></div>
      <div class="stat-sub">All time</div>
    </div>
    <div class="stat-card teal">
      <div class="stat-label">Published</div>
      <div class="stat-value" id="statPublished"><?= $statPublished ?></div>
      <div class="stat-sub">Currently running</div>
    </div>
    <div class="stat-card gold-acc">
      <div class="stat-label">Pending</div>
      <div class="stat-value" id="statPending"><?= $statPending ?></div>
      <div class="stat-sub">Awaiting launch</div>
    </div>
    <div class="stat-card rose">
      <div class="stat-label">Closed</div>
      <div class="stat-value" id="statClosed"><?= $statClosed ?></div>
      <div class="stat-sub">Completed</div>
    </div>
  </div>

  <section style="width:100%;">
    <div class="table-wrap">
      <div class="table-scroll">
        <table>
          <thead>
            <tr>
              <th>Survey Title</th>
              <th>Responses</th>
              <th>Response Rate</th>
              <th>Date Created</th>
              <th>Status</th>
              <th class="col-actions">Actions</th>
            </tr>
          </thead>
          <tbody id="surveyTable">
            <?php foreach ($surveys as $s):
              $responses = (int)$s['response_count'];
              $pct       = $totalStudents > 0 ? round(($responses / $totalStudents) * 100) : 0;
              $dateLabel = date('M d, Y', strtotime($s['created_at']));
            ?>
            <tr data-status="<?= $s['status'] ?>" data-id="<?= $s['id'] ?>">
              <td>
                <div class="survey-cell">
                  <span class="survey-title"><?= htmlspecialchars($s['title']) ?></span>
                  <span class="survey-desc"><?= htmlspecialchars($s['description']) ?></span>
                </div>
              </td>
              <td style="font-weight:600;color:var(--ink);"><?= number_format($responses) ?> / <?= $totalStudents ?></td>
              <td>
                <div class="prog-wrap">
                  <div class="prog-track"><div class="prog-fill" style="width:<?= $pct ?>%;"></div></div>
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
              <td class="col-actions">
                <div class="action-btns">
                  <a href="survey_questions.php?id=<?= $s['id'] ?>&action=view" class="action-btn">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" fill="none" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    View
                  </a>
                  <a href="survey_questions.php?id=<?= $s['id'] ?>&action=edit" class="action-btn">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" fill="none" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    Edit
                  </a>
                  <button class="action-btn danger" onclick="confirmDelete(this, <?= $s['id'] ?>)">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" fill="none" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/></svg>
                    Delete
                  </button>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <div id="emptyState" style="display:none;text-align:center;padding:3rem 1rem;">
        <p style="color:var(--ink-3);font-size:14px;">No surveys match your filters.</p>
      </div>

      <div class="pagination">
        <span id="pagInfo"></span>
        <div class="page-btns" id="pageButtons"></div>
      </div>
    </div>
  </section>
</div>


<!-- MODAL -->
<div class="modal-backdrop" id="surveyModal" onclick="handleBackdropClick(event)">
  <div class="modal">
    <div class="modal-steps">
      <div class="step-item">
        <div class="step-dot active" id="dot1">1</div>
        <span class="step-label active" id="lbl1">Details</span>
      </div>
      <div class="step-line" id="line1"></div>
      <div class="step-item">
        <div class="step-dot" id="dot2">2</div>
        <span class="step-label" id="lbl2">Questions</span>
      </div>
      <div class="step-line" id="line2"></div>
      <div class="step-item">
        <div class="step-dot" id="dot3">3</div>
        <span class="step-label" id="lbl3">Launch</span>
      </div>
    </div>
    <div class="modal-body">
      <!-- Step 1 -->
      <div class="step-panel active" id="step1">
        <div class="modal-head">
          <h2>Survey <em>Details</em></h2>
          <button class="modal-close" onclick="closeModal()">×</button>
        </div>
        <div class="form-grid">
          <div class="field">
            <label>Survey Title <span style="color:var(--rose)">*</span></label>
            <input type="text" id="s1Title" placeholder="e.g. End-of-Term Faculty Evaluation">
          </div>
          <div class="field">
            <label>Description</label>
            <textarea id="s1Desc" placeholder="Brief description…"></textarea>
          </div>
          <div class="form-row">
            <div class="field">
              <label>Publish Status</label>
              <select id="s1Status">
                <option value="published">Published — live immediately</option>
                <option value="pending">Pending — save for later</option>
              </select>
            </div>
          </div>
          <div class="form-row">
            <div class="field">
              <label>Start Date</label>
              <input type="date" id="s1Start">
            </div>
            <div class="field">
              <label>End Date</label>
              <input type="date" id="s1End">
            </div>
          </div>
        </div>
        <div class="field-error" id="err1"></div>
        <div class="modal-actions">
          <button class="btn-ghost" onclick="closeModal()">Cancel</button>
          <div class="modal-actions-right">
            <button class="btn-gold" onclick="goStep2()">
              Next: Add Questions
              <svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
            </button>
          </div>
        </div>
      </div>
      <!-- Step 2 -->
      <div class="step-panel" id="step2">
        <div class="modal-head">
          <h2>Add <em>Questions</em></h2>
          <button class="modal-close" onclick="closeModal()">×</button>
        </div>
        <div class="questions-list" id="questionsList"></div>
        <button class="btn-add-q" onclick="addQuestion()">
          <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          Add Question
        </button>
        <div class="field-error" id="err2"></div>
        <div class="modal-actions">
          <button class="btn-ghost" onclick="setStep(1)">
            <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
            Back
          </button>
          <div class="modal-actions-right">
            <button class="btn-gold" onclick="goStep3()">
              Review &amp; Launch
              <svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
            </button>
          </div>
        </div>
      </div>
      <!-- Step 3 -->
      <div class="step-panel" id="step3">
        <div class="modal-head">
          <h2>Review &amp; <em>Launch</em></h2>
          <button class="modal-close" onclick="closeModal()">×</button>
        </div>
        <div id="reviewBlock"></div>
        <div class="modal-actions">
          <button class="btn-ghost" onclick="setStep(2)">
            <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
            Back
          </button>
          <div class="modal-actions-right">
            <button class="btn-launch" onclick="launchSurvey()">
              <svg viewBox="0 0 24 24"><polyline points="22 2 15 22 11 13 2 9 22 2"/></svg>
              Publish Survey
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="toast" id="toast">
  <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
  <span id="toastMsg">Done!</span>
</div>

<script>
(function(){
  function teleport(){
    var m=document.getElementById('surveyModal');
    var t=document.getElementById('toast');
    if(m&&m.parentNode!==document.body) document.body.appendChild(m);
    if(t&&t.parentNode!==document.body) document.body.appendChild(t);
  }
  if(document.readyState==='loading') document.addEventListener('DOMContentLoaded',teleport);
  else teleport();
})();

/* ── Pagination ── */
const ROWS_PER_PAGE = 10;
let currentPage = 1;
let visibleRows = [];

function getAllRows() { return Array.from(document.querySelectorAll('#surveyTable tr')); }

function filterSurveys() {
  const q      = document.getElementById('surveySearch').value.toLowerCase();
  const status = document.getElementById('statusFilter').value;
  const rows   = getAllRows();

  visibleRows = rows.filter(row => {
    const title   = row.querySelector('.survey-title')?.textContent.toLowerCase() || '';
    const rstatus = row.dataset.status || '';
    return (!q || title.includes(q)) && (!status || rstatus === status);
  });

  currentPage = 1;
  renderPage();
}

function renderPage() {
  const rows  = getAllRows();
  const total = visibleRows.length;
  const start = (currentPage - 1) * ROWS_PER_PAGE;
  const end   = start + ROWS_PER_PAGE;

  rows.forEach(r => r.style.display = 'none');
  visibleRows.slice(start, end).forEach(r => r.style.display = '');

  document.getElementById('emptyState').style.display = total === 0 ? 'block' : 'none';
  document.getElementById('pagInfo').textContent = total === 0
    ? 'No surveys found'
    : `Showing ${start + 1}–${Math.min(end, total)} of ${total} survey${total !== 1 ? 's' : ''}`;

  renderPageButtons(total);
}

function renderPageButtons(total) {
  const totalPages = Math.ceil(total / ROWS_PER_PAGE);
  const container  = document.getElementById('pageButtons');
  container.innerHTML = '';

  const prev = makeBtn('', true);
  prev.innerHTML = `<svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>`;
  prev.disabled  = currentPage === 1;
  prev.onclick   = () => { if (currentPage > 1) { currentPage--; renderPage(); } };
  container.appendChild(prev);

  pageRange(currentPage, totalPages).forEach(p => {
    if (p === '...') {
      const dots = document.createElement('span');
      dots.textContent = '…';
      dots.style.cssText = 'padding:0 4px;line-height:32px;color:var(--ink-3);';
      container.appendChild(dots);
    } else {
      const btn = makeBtn(p, false);
      if (p === currentPage) btn.classList.add('active');
      btn.onclick = () => { currentPage = p; renderPage(); };
      container.appendChild(btn);
    }
  });

  const next = makeBtn('', true);
  next.innerHTML = `<svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>`;
  next.disabled  = currentPage === totalPages || totalPages === 0;
  next.onclick   = () => { if (currentPage < totalPages) { currentPage++; renderPage(); } };
  container.appendChild(next);
}

function makeBtn(label, isIcon) {
  const btn = document.createElement('button');
  btn.className = 'page-btn';
  if (!isIcon) btn.textContent = label;
  return btn;
}

function pageRange(current, total) {
  if (total <= 7) return Array.from({length: total}, (_, i) => i + 1);
  if (current <= 4) return [1,2,3,4,5,'...',total];
  if (current >= total - 3) return [1,'...',total-4,total-3,total-2,total-1,total];
  return [1,'...',current-1,current,current+1,'...',total];
}

document.addEventListener('DOMContentLoaded', () => {
  visibleRows = getAllRows();
  renderPage();
});

/* ── Modal ── */
let qCount = 0;
let currentStep = 1;

function openModal() {
  document.getElementById('s1Title').value = '';
  document.getElementById('s1Desc').value  = '';
  document.getElementById('s1Status').selectedIndex = 0;
  document.getElementById('s1Start').value = '';
  document.getElementById('s1End').value   = '';
  document.getElementById('questionsList').innerHTML = '';
  document.getElementById('reviewBlock').innerHTML   = '';
  hideErr('err1'); hideErr('err2');
  qCount = 0;
  setStep(1);
  addQuestion();
  document.getElementById('surveyModal').classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closeModal() {
  document.getElementById('surveyModal').classList.remove('open');
  document.body.style.overflow = '';
}

function handleBackdropClick(e) {
  if (e.target === document.getElementById('surveyModal')) closeModal();
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

function setStep(n) {
  currentStep = n;
  [1,2,3].forEach(i => {
    document.getElementById('step'+i).classList.toggle('active', i===n);
    const dot = document.getElementById('dot'+i);
    const lbl = document.getElementById('lbl'+i);
    dot.classList.remove('active','done');
    lbl.classList.remove('active');
    if (i===n)     { dot.classList.add('active'); lbl.classList.add('active'); }
    else if (i<n)  { dot.classList.add('done');   lbl.classList.add('active'); }
    dot.textContent = (i<n) ? '✓' : i;
  });
  document.getElementById('line1').classList.toggle('done', n>1);
  document.getElementById('line2').classList.toggle('done', n>2);
}

function goStep2() {
  const title = document.getElementById('s1Title').value.trim();
  if (!title) { showErr('err1','Please enter a survey title.'); return; }
  hideErr('err1');
  setStep(2);
}

function goStep3() {
  const cards = document.querySelectorAll('.question-card');
  if (cards.length === 0) { showErr('err2','Add at least one question.'); return; }
  let hasEmpty = false;
  cards.forEach(c => { if (!c.querySelector('.q-text')?.value.trim()) hasEmpty = true; });
  if (hasEmpty) { showErr('err2','All questions need text.'); return; }
  hideErr('err2');
  buildReview();
  setStep(3);
}

function addQuestion() {
  qCount++;
  const id   = 'q' + qCount;
  const card = document.createElement('div');
  card.className = 'question-card';
  card.id = id;
  card.innerHTML = `
    <div class="q-row">
      <span class="q-num">${qCount}</span>
      <div class="q-body">
        <input type="text" class="q-text" placeholder="Type your question here…">
        <div class="q-type-row">
          <select class="q-type" onchange="renderOptions(this,'${id}')">
            <option value="text">Short Answer</option>
            <option value="textarea">Long Answer</option>
            <option value="radio">Multiple Choice</option>
            <option value="checkbox">Checkboxes</option>
            <option value="scale">Rating Scale (1–5)</option>
          </select>
          <label class="q-required-toggle">
            <input type="checkbox" class="q-required"> Required
          </label>
        </div>
        <div class="q-options-wrap"></div>
      </div>
      <button class="q-delete" onclick="removeQuestion('${id}')">×</button>
    </div>`;
  document.getElementById('questionsList').appendChild(card);
  renumberQuestions();
  card.querySelector('.q-text').focus();
}

function removeQuestion(id) {
  const el = document.getElementById(id);
  if (el) el.remove();
  renumberQuestions();
}

function renumberQuestions() {
  document.querySelectorAll('.question-card').forEach((c,i) => {
    const n = c.querySelector('.q-num');
    if (n) n.textContent = i+1;
  });
}

function renderOptions(sel, cardId) {
  const wrap = sel.closest('.q-body').querySelector('.q-options-wrap');
  wrap.innerHTML = '';
  const type = sel.value;
  if (type === 'radio' || type === 'checkbox') {
    const opts = document.createElement('div');
    opts.className = 'q-options';
    for (let i=1;i<=2;i++) opts.appendChild(makeOptionRow(`Option ${i}`));
    const addBtn = document.createElement('button');
    addBtn.type = 'button';
    addBtn.className = 'btn-add-opt';
    addBtn.innerHTML = `<svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" fill="none" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Add option`;
    addBtn.onclick = () => addOptionTo(opts);
    wrap.appendChild(opts);
    wrap.appendChild(addBtn);
  } else if (type === 'scale') {
    const preview = document.createElement('div');
    preview.innerHTML = `<div class="scale-preview">${[1,2,3,4,5].map(n=>`<div class="scale-pip">${n}</div>`).join('')}</div><p class="scale-hint">Respondents pick 1 (Poor) to 5 (Excellent).</p>`;
    wrap.appendChild(preview);
  }
}

function makeOptionRow(placeholder) {
  const row = document.createElement('div');
  row.className = 'q-option-row';
  row.innerHTML = `<input type="text" placeholder="${placeholder}"><button class="btn-icon" onclick="removeOption(this)"><svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>`;
  return row;
}

function addOptionTo(container) {
  const n = container.querySelectorAll('.q-option-row').length + 1;
  const row = makeOptionRow(`Option ${n}`);
  container.appendChild(row);
  row.querySelector('input').focus();
}

function removeOption(btn) {
  const row = btn.closest('.q-option-row');
  const container = row.closest('.q-options');
  if (container && container.querySelectorAll('.q-option-row').length > 1) row.remove();
}

function buildReview() {
  const title  = document.getElementById('s1Title').value.trim();
  const desc   = document.getElementById('s1Desc').value.trim();
  const status = document.getElementById('s1Status').value;
  const start  = document.getElementById('s1Start').value;
  const end    = document.getElementById('s1End').value;
  const typeLabel = { text:'Short Answer', textarea:'Long Answer', radio:'Multiple Choice', checkbox:'Checkboxes', scale:'Rating Scale (1–5)' };
  const questions = [];
  document.querySelectorAll('.question-card').forEach((card,i) => {
    const text     = card.querySelector('.q-text')?.value.trim() || '';
    const type     = card.querySelector('.q-type')?.value || 'text';
    const required = card.querySelector('.q-required')?.checked || false;
    const opts     = [...card.querySelectorAll('.q-option-row input')].map(o=>o.value.trim()).filter(Boolean);
    questions.push({n:i+1,text,type,required,opts});
  });
  const statusBadge = status==='published'
    ? `<span class="badge badge-published">Published</span>`
    : `<span class="badge badge-pending">Pending</span>`;
  document.getElementById('reviewBlock').innerHTML = `
    <div class="review-card">
      <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:8px;">
        <span style="font-weight:600;font-size:14.5px;color:var(--ink)">${esc(title)}</span>
        ${statusBadge}
      </div>
      ${desc ? `<p style="font-size:13px;color:var(--ink-3);">${esc(desc)}</p>` : ''}
      <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;margin-top:3px;">
        ${start ? `<span style="font-size:12px;color:var(--ink-3);">From ${start}</span>` : ''}
        ${end   ? `<span style="font-size:12px;color:var(--ink-3);">To ${end}</span>` : ''}
      </div>
    </div>
    <p class="review-section-label">${questions.length} Question${questions.length!==1?'s':''}</p>
    ${questions.map(q=>`
      <div class="review-q-card">
        <span class="q-num">${q.n}</span>
        <div style="flex:1;">
          <p style="font-size:13.5px;font-weight:500;color:var(--ink);margin-bottom:3px;">
            ${esc(q.text)}
            ${q.required?'<span style="color:var(--rose);font-size:11px;margin-left:4px;">Required</span>':''}
          </p>
          <p style="font-size:12px;color:var(--ink-3);">
            ${typeLabel[q.type]||q.type}
            ${q.opts.length?' · '+q.opts.map(o=>`<em>${esc(o)}</em>`).join(', '):''}
          </p>
        </div>
      </div>`).join('')}`;
}

function launchSurvey() {
  const title  = document.getElementById('s1Title').value.trim();
  const desc   = document.getElementById('s1Desc').value.trim();
  const status = document.getElementById('s1Status').value;
  const start  = document.getElementById('s1Start').value;
  const dateLabel = start
    ? new Date(start+'T00:00:00').toLocaleDateString('en-US',{month:'short',day:'numeric',year:'numeric'})
    : new Date().toLocaleDateString('en-US',{month:'short',day:'numeric',year:'numeric'});

 // Collect questions from the modal
const questionsData = [];
document.querySelectorAll('.question-card').forEach((card) => {
    const text     = card.querySelector('.q-text')?.value.trim() || '';
    const type     = card.querySelector('.q-type')?.value || 'text';
    const required = card.querySelector('.q-required')?.checked || false;
    const opts     = [...card.querySelectorAll('.q-option-row input')]
                        .map(o => o.value.trim()).filter(Boolean);
    questionsData.push({ text, type, required, opts });
});

const formData = new FormData();
formData.append('title',       title);
formData.append('description', desc);
formData.append('status',      status);
formData.append('questions',   JSON.stringify(questionsData));

fetch('../../controllers/surveyController.php', { method: 'POST', body: formData })
    .then(r => r.json())
    .then(data => {
      if (!data.success) { showToast('Error saving survey.', false); return; }

      const badge = status === 'published'
        ? `<span class="badge badge-published">Published</span>`
        : `<span class="badge badge-pending">Pending</span>`;
      const qCountVal = document.querySelectorAll('.question-card').length;

      const tr = document.createElement('tr');
      tr.dataset.status = status;
      tr.dataset.id     = data.id || '';
      tr.innerHTML = `
        <td>
          <div class="survey-cell">
            <span class="survey-title">${esc(title)}</span>
            <span class="survey-desc">${esc(desc)} ${qCountVal > 0 ? '· '+qCountVal+' question'+(qCountVal!==1?'s':'') : ''}</span>
          </div>
        </td>
        <td style="font-weight:600;color:var(--ink);">0 / <?= $totalStudents ?></td>
        <td>
          <div class="prog-wrap">
            <div class="prog-track"><div class="prog-fill" style="width:0%;"></div></div>
            <span class="prog-num">0%</span>
          </div>
        </td>
        <td style="color:var(--ink-3);font-size:13px;">${dateLabel}</td>
        <td>${badge}</td>
        <td class="col-actions">
          <div class="action-btns">
            <a href="survey_questions.php?id=${data.id||''}&action=view" class="action-btn">
              <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" fill="none" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              View
            </a>
            <a href="survey_questions.php?id=${data.id||''}&action=edit" class="action-btn">
              <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" fill="none" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
              Edit
            </a>
            <button class="action-btn danger" onclick="confirmDelete(this,${data.id||0})">
              <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" fill="none" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/></svg>
              Delete
            </button>
          </div>
        </td>`;

      document.getElementById('surveyTable').prepend(tr);
      updateStats();
      closeModal();
      filterSurveys();
      showToast(status==='published' ? 'Survey published and is now live!' : 'Survey saved as Pending.', true);
    })
    .catch(() => showToast('Network error. Please try again.', false));
}

function confirmDelete(btn, id) {
  if (!confirm('Delete this survey? This cannot be undone.')) return;

  fetch('delete_survey.php', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded'},
    body: 'id=' + id
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      btn.closest('tr').remove();
      updateStats();
      filterSurveys();
      showToast('Survey deleted.');
    } else {
      showToast('Could not delete survey.', false);
    }
  })
  .catch(() => showToast('Network error.', false));
}

function updateStats() {
  const rows = [...document.querySelectorAll('#surveyTable tr')];
  document.getElementById('statTotal').textContent     = rows.length;
  document.getElementById('statPublished').textContent = rows.filter(r=>r.dataset.status==='published').length;
  document.getElementById('statPending').textContent   = rows.filter(r=>r.dataset.status==='pending').length;
  document.getElementById('statClosed').textContent    = rows.filter(r=>r.dataset.status==='closed').length;
}

function showToast(msg, success=true) {
  const t = document.getElementById('toast');
  document.getElementById('toastMsg').textContent = msg;
  t.className = 'toast' + (success ? ' success' : ' error');
  requestAnimationFrame(() => t.classList.add('show'));
  setTimeout(() => t.classList.remove('show'), 3500);
}

function showErr(id,msg) { const el=document.getElementById(id); el.textContent=msg; el.classList.add('show'); setTimeout(()=>el.classList.remove('show'),4000); }
function hideErr(id) { document.getElementById(id).classList.remove('show'); }
function esc(s) { return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
</script>

<?php include('./includes/footer.php'); ?>