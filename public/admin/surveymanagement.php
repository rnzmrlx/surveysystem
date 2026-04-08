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
    --pass:      #2a6b4a;
    --pass-bg:   #d8efe3;
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
    flex-wrap: wrap;
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

  .btn-gold {
    display: flex; align-items: center; gap: 6px;
    background: var(--gold); color: #fff;
    border: none; border-radius: var(--radius);
    padding: 8.5px 16px;
    font-family: 'DM Sans', sans-serif;
    font-size: 13.5px; font-weight: 500;
    cursor: pointer; text-decoration: none;
    transition: background 0.15s, transform 0.1s;
    white-space: nowrap;
  }
  .btn-gold:hover { background: var(--gold-dark); color: #fff; }
  .btn-gold:active { transform: scale(0.98); }
  .btn-gold svg { width: 14px; height: 14px; stroke: currentColor; fill: none; stroke-width: 2; flex-shrink: 0; }

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
  .stat-card.teal::before    { background: var(--teal); }
  .stat-card.gold-acc::before { background: var(--gold); }
  .stat-card.rose::before    { background: var(--rose); }

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

  /* ── Table with sticky actions column ── */
  .table-wrap {
    background: #fff;
    border: 1.5px solid var(--paper-3);
    border-radius: var(--radius);
    overflow: hidden;
    box-shadow: var(--shadow);
  }

  /* Allow horizontal scroll on narrow viewports but keep actions pinned */
  .table-scroll {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }

  table { width: 100%; border-collapse: collapse; font-size: 14px; min-width: 600px; }
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

  /* Sticky actions column */
  th.col-actions,
  td.col-actions {
    position: sticky;
    right: 0;
    background: inherit;          /* inherits thead/tbody row bg */
    z-index: 2;
    box-shadow: -4px 0 10px rgba(15,14,13,0.06);
    white-space: nowrap;
  }
  thead th.col-actions { background: var(--paper-2); }
  tbody tr:hover td.col-actions { background: var(--paper); }

  /* Survey title cell */
  .survey-cell { display: flex; flex-direction: column; gap: 2px; }
  .survey-title { font-weight: 500; color: var(--ink); font-size: 14px; }
  .survey-desc  { font-size: 12px; color: var(--ink-3); }

  /* Progress bar */
  .prog-wrap { display: flex; align-items: center; gap: 10px; min-width: 130px; }
  .prog-track { flex: 1; height: 5px; background: var(--paper-2); border-radius: 3px; overflow: hidden; }
  .prog-fill  { height: 100%; border-radius: 3px; background: var(--gold); transition: width 0.4s; }
  .prog-num   { font-weight: 600; font-size: 13px; min-width: 36px; text-align: right; color: var(--ink); }

  /* Badges */
  .badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 10px; border-radius: 20px;
    font-size: 12px; font-weight: 600; white-space: nowrap;
  }
  .badge::before { content: ''; width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }

  .badge-active  { background: var(--teal-lt); color: var(--teal); }
  .badge-active::before  { background: var(--teal); animation: pulse 1.5s infinite; }
  .badge-closed  { background: var(--rose-lt); color: var(--rose); }
  .badge-closed::before  { background: var(--rose); }
  .badge-pending { background: var(--gold-light); color: var(--gold); }
  .badge-pending::before { background: var(--gold); }

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
    white-space: nowrap;
  }
  .action-btn:hover { border-color: var(--ink-2); color: var(--ink); background: var(--paper); }
  .action-btn.danger:hover { border-color: var(--rose); color: var(--rose); background: var(--rose-lt); }
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
  }

  /* ─────────────────────────────────────────
     MULTI-STEP MODAL
  ───────────────────────────────────────── */
  .modal-backdrop {
    display: none;
    position: fixed; inset: 0;
    background: rgba(15,14,13,0.5);
    backdrop-filter: blur(4px);
    z-index: 900;
    align-items: center; justify-content: center;
    padding: 1rem;
  }
  .modal-backdrop.open { display: flex; }

  .modal {
    background: #fff;
    border: 1.5px solid var(--paper-3);
    border-radius: 16px;
    box-shadow: 0 12px 48px rgba(15,14,13,0.2);
    width: 100%; max-width: 640px;
    max-height: 90vh;
    overflow-y: auto;
    position: relative;
    animation: modalIn 0.22s ease;
  }
  @keyframes modalIn { from{opacity:0;transform:translateY(14px);}to{opacity:1;transform:translateY(0);} }

  /* Step indicator at top */
  .modal-steps {
    display: flex;
    align-items: center;
    padding: 1.5rem 2.25rem 0;
    gap: 0;
    position: relative;
  }
  .step-item {
    display: flex; align-items: center; gap: 8px;
    flex: 1;
  }
  .step-item:last-child { flex: none; }
  .step-dot {
    width: 28px; height: 28px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; font-weight: 600;
    border: 2px solid var(--paper-3);
    color: var(--ink-3);
    background: var(--paper-2);
    flex-shrink: 0;
    transition: all 0.2s;
    position: relative; z-index: 1;
  }
  .step-dot.active { border-color: var(--gold); background: var(--gold); color: #fff; }
  .step-dot.done   { border-color: var(--teal); background: var(--teal); color: #fff; }
  .step-label {
    font-size: 12px; font-weight: 600; color: var(--ink-3);
    text-transform: uppercase; letter-spacing: 0.05em;
    transition: color 0.2s;
  }
  .step-label.active { color: var(--ink); }
  .step-line {
    flex: 1; height: 2px;
    background: var(--paper-3);
    margin: 0 10px;
    transition: background 0.3s;
  }
  .step-line.done { background: var(--teal); }

  .modal-body {
    padding: 1.75rem 2.25rem 2.25rem;
  }

  .modal-head {
    display: flex; justify-content: space-between; align-items: flex-start;
    margin-bottom: 1.5rem;
  }
  .modal-head h2 {
    font-family: 'DM Serif Display', serif;
    font-size: 1.5rem; font-weight: 400;
  }
  .modal-head h2 em { font-style: italic; color: var(--gold); }
  .modal-close {
    background: none; border: none; cursor: pointer;
    color: var(--ink-3); padding: 2px; line-height: 1;
    transition: color 0.15s;
  }
  .modal-close:hover { color: var(--ink); }

  /* Step panels */
  .step-panel { display: none; }
  .step-panel.active { display: block; }

  /* Form fields */
  .form-grid { display: flex; flex-direction: column; gap: 1.1rem; }
  .form-row  { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }

  .field { display: flex; flex-direction: column; gap: 5px; }
  .field label {
    font-size: 11px; font-weight: 600;
    text-transform: uppercase; letter-spacing: 0.06em;
    color: var(--ink-3);
  }
  .field input,
  .field select,
  .field textarea {
    background: var(--paper-2);
    border: 1.5px solid var(--paper-3);
    border-radius: var(--radius);
    padding: 9px 13px;
    font-family: 'DM Sans', sans-serif;
    font-size: 13.5px; color: var(--ink);
    outline: none; transition: border-color 0.15s;
    width: 100%;
  }
  .field input:focus,
  .field select:focus,
  .field textarea:focus { border-color: var(--gold); background: #fff; }
  .field textarea { resize: vertical; min-height: 72px; }

  /* ── Question builder (Step 2) ── */
  .questions-list { display: flex; flex-direction: column; gap: 12px; margin-bottom: 14px; }

  .question-card {
    background: var(--paper-2);
    border: 1.5px solid var(--paper-3);
    border-radius: var(--radius);
    padding: 1rem 1.1rem;
    position: relative;
  }
  .q-row { display: flex; gap: 10px; align-items: flex-start; }
  .q-num {
    width: 24px; height: 24px; border-radius: 50%;
    background: var(--gold-light); color: var(--gold-dark);
    font-size: 11px; font-weight: 700;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; margin-top: 2px;
  }
  .q-body { flex: 1; display: flex; flex-direction: column; gap: 8px; }
  .q-body input[type="text"],
  .q-body select {
    background: #fff;
    border: 1.5px solid var(--paper-3);
    border-radius: 8px;
    padding: 8px 12px;
    font-family: 'DM Sans', sans-serif;
    font-size: 13.5px; color: var(--ink);
    outline: none; transition: border-color 0.15s;
    width: 100%;
  }
  .q-body input[type="text"]:focus,
  .q-body select:focus { border-color: var(--gold); }

  .q-type-row { display: flex; align-items: center; gap: 10px; }
  .q-type-row select { flex: 1; }

  .q-options { display: flex; flex-direction: column; gap: 6px; margin-top: 4px; }
  .q-option-row { display: flex; align-items: center; gap: 6px; }
  .q-option-row input {
    flex: 1; background: #fff;
    border: 1.5px solid var(--paper-3);
    border-radius: 7px; padding: 6px 10px;
    font-family: 'DM Sans', sans-serif; font-size: 13px; color: var(--ink);
    outline: none; transition: border-color 0.15s;
  }
  .q-option-row input:focus { border-color: var(--gold); }
  .btn-icon {
    background: none; border: 1.5px solid var(--paper-3);
    border-radius: 6px; width: 28px; height: 28px;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; color: var(--ink-3);
    transition: all 0.12s; flex-shrink: 0;
  }
  .btn-icon:hover { border-color: var(--rose); color: var(--rose); background: var(--rose-lt); }
  .btn-icon svg { width: 13px; height: 13px; stroke: currentColor; fill: none; stroke-width: 2; }

  .btn-add-opt {
    background: none; border: 1.5px dashed var(--paper-3);
    border-radius: 7px; padding: 5px 12px;
    font-family: 'DM Sans', sans-serif; font-size: 12.5px; color: var(--ink-3);
    cursor: pointer; text-align: left; transition: all 0.12s;
    display: flex; align-items: center; gap: 5px; width: 100%;
  }
  .btn-add-opt:hover { border-color: var(--gold); color: var(--gold); }

  .q-delete {
    background: none; border: none; cursor: pointer;
    color: var(--ink-3); padding: 2px; transition: color 0.15s;
    flex-shrink: 0; margin-top: 2px;
  }
  .q-delete:hover { color: var(--rose); }
  .q-delete svg { width: 15px; height: 15px; stroke: currentColor; fill: none; stroke-width: 2; }

  .btn-add-q {
    display: flex; align-items: center; gap: 7px;
    width: 100%; padding: 10px 14px;
    background: none; border: 1.5px dashed var(--paper-3);
    border-radius: var(--radius);
    font-family: 'DM Sans', sans-serif; font-size: 13.5px; color: var(--ink-3);
    cursor: pointer; transition: all 0.15s;
  }
  .btn-add-q:hover { border-color: var(--gold); color: var(--gold); background: var(--gold-light); }
  .btn-add-q svg { width: 14px; height: 14px; stroke: currentColor; fill: none; stroke-width: 2; }

  /* Modal footer */
  .modal-actions {
    display: flex; justify-content: space-between; align-items: center;
    gap: 10px; margin-top: 1.75rem;
    padding-top: 1.25rem;
    border-top: 1.5px solid var(--paper-2);
  }
  .modal-actions-right { display: flex; gap: 10px; }
  .btn-ghost {
    background: none;
    border: 1.5px solid var(--paper-3);
    border-radius: var(--radius);
    padding: 8.5px 16px;
    font-family: 'DM Sans', sans-serif;
    font-size: 13.5px; font-weight: 500;
    color: var(--ink-2); cursor: pointer;
    transition: border-color 0.15s;
    display: flex; align-items: center; gap: 6px;
  }
  .btn-ghost:hover { border-color: var(--ink-2); }
  .btn-ghost svg { width: 14px; height: 14px; stroke: currentColor; fill: none; stroke-width: 2; }

  /* Toast */
  .toast {
    position: fixed; bottom: 2rem; right: 2rem;
    background: var(--ink); color: var(--paper);
    border-radius: 10px; padding: 12px 20px;
    font-size: 13.5px; font-weight: 500;
    box-shadow: 0 8px 32px rgba(15,14,13,0.25);
    transform: translateY(80px); opacity: 0;
    transition: all 0.3s cubic-bezier(0.34,1.56,0.64,1);
    z-index: 9999; pointer-events: none;
    display: flex; align-items: center; gap: 9px;
  }
  .toast.show { transform: translateY(0); opacity: 1; }
  .toast svg { width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 2; flex-shrink: 0; }
  .toast.success { background: var(--teal); }
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
      <button onclick="openModal()" class="btn-gold">
        <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        New Survey
      </button>
    </div>
  </header>

  <div class="stat-strip">
    <div class="stat-card gold">
      <div class="stat-label">Total Surveys</div>
      <div class="stat-value" id="statTotal">3</div>
      <div class="stat-sub">All time</div>
    </div>
    <div class="stat-card teal">
      <div class="stat-label">Active</div>
      <div class="stat-value" id="statActive">1</div>
      <div class="stat-sub">Currently running</div>
    </div>
    <div class="stat-card gold-acc">
      <div class="stat-label">Pending</div>
      <div class="stat-value" id="statPending">1</div>
      <div class="stat-sub">Awaiting launch</div>
    </div>
    <div class="stat-card rose">
      <div class="stat-label">Closed</div>
      <div class="stat-value" id="statClosed">1</div>
      <div class="stat-sub">Completed</div>
    </div>
  </div>

  <section style="width:100%;">
    <div class="table-wrap">
      <div class="table-scroll">
        <table>
          <thead>
            <tr>
              <th class="sortable">Survey Title ↕</th>
              <th>Category</th>
              <th class="sortable">Responses ↕</th>
              <th>Response Rate</th>
              <th>Date Created</th>
              <th>Status</th>
              <th class="col-actions">Actions</th>
            </tr>
          </thead>
          <tbody id="surveyTable">

            <?php
            $surveys = [
              ['title'=>'Mid-Term Faculty Evaluation',  'desc'=>'Rate your professors this semester',   'cat'=>'Academic',       'responses'=>507, 'total'=>1200, 'date'=>'March 28, 2026',  'status'=>'closed'],
              ['title'=>'Online Learning Experience',   'desc'=>'Feedback on e-learning platforms',     'cat'=>'Wellness',       'responses'=>461, 'total'=>1200, 'date'=>'April 15, 2026',  'status'=>'active'],
              ['title'=>'Facility & Safety Assessment', 'desc'=>'Semester wrap-up campus evaluation',  'cat'=>'Administration', 'responses'=>0,   'total'=>1100, 'date'=>'May 30, 2026',    'status'=>'pending'],
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
                  <div class="prog-track"><div class="prog-fill" style="width:<?= $pct ?>%;"></div></div>
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
              <td class="col-actions">
                <div class="action-btns">
                  <a href="survey_questions.php?id=<?= urlencode($s['title']) ?>&action=view" class="action-btn">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" fill="none" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    View
                  </a>
                  <a href="survey_questions.php?id=<?= urlencode($s['title']) ?>&action=edit" class="action-btn">
                    <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" fill="none" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    Edit
                  </a>
                  <button class="action-btn danger" onclick="confirmDelete(this)">
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

<!-- ═══════════════════════════════════════
     MULTI-STEP SURVEY MODAL
═══════════════════════════════════════ -->
<div class="modal-backdrop" id="surveyModal" onclick="handleBackdropClick(event)">
  <div class="modal">

    <!-- Step indicator -->
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
        <div class="step-dot" id="dot3">✓</div>
        <span class="step-label" id="lbl3">Launch</span>
      </div>
    </div>

    <div class="modal-body">

      <!-- ── STEP 1: Survey Details ── -->
      <div class="step-panel active" id="step1">
        <div class="modal-head">
          <h2>Survey <em>Details</em></h2>
          <button class="modal-close" onclick="closeModal()">
            <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" fill="none" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
          </button>
        </div>
        <div class="form-grid">
          <div class="field">
            <label>Survey Title <span style="color:var(--rose)">*</span></label>
            <input type="text" id="s1Title" placeholder="e.g. End-of-Term Faculty Evaluation" required>
          </div>
          <div class="field">
            <label>Description</label>
            <textarea id="s1Desc" placeholder="Brief description of what this survey covers…"></textarea>
          </div>
          <div class="form-row">
            <div class="field">
              <label>Category <span style="color:var(--rose)">*</span></label>
              <select id="s1Cat" required>
                <option value="" disabled selected>Select category</option>
                <option value="Academic">Academic</option>
                <option value="Facilities">Facilities</option>
                <option value="Student Life">Student Life</option>
                <option value="Administration">Administration</option>
                <option value="Wellness">Wellness</option>
              </select>
            </div>
            <div class="field">
              <label>Launch Status</label>
              <select id="s1Status">
                <option value="active" selected>Active (live immediately)</option>
                <option value="pending">Pending (save for later)</option>
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
        <div class="modal-actions">
          <button class="btn-ghost" onclick="closeModal()">Cancel</button>
          <div class="modal-actions-right">
            <button class="btn-gold" onclick="goStep(2)">
              Next: Add Questions
              <svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
            </button>
          </div>
        </div>
      </div>

      <!-- ── STEP 2: Question Builder ── -->
      <div class="step-panel" id="step2">
        <div class="modal-head">
          <h2>Add <em>Questions</em></h2>
          <button class="modal-close" onclick="closeModal()">
            <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" fill="none" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
          </button>
        </div>
        <div class="questions-list" id="questionsList"></div>
        <button class="btn-add-q" onclick="addQuestion()">
          <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          Add Question
        </button>
        <div class="modal-actions">
          <button class="btn-ghost" onclick="goStep(1)">
            <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
            Back
          </button>
          <div class="modal-actions-right">
            <button class="btn-gold" onclick="goStep(3)">
              Review &amp; Launch
              <svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
            </button>
          </div>
        </div>
      </div>

      <!-- ── STEP 3: Confirm & Launch ── -->
      <div class="step-panel" id="step3">
        <div class="modal-head">
          <h2>Review &amp; <em>Launch</em></h2>
          <button class="modal-close" onclick="closeModal()">
            <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" fill="none" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
          </button>
        </div>
        <div id="reviewBlock" style="display:flex;flex-direction:column;gap:12px;"></div>
        <div class="modal-actions">
          <button class="btn-ghost" onclick="goStep(2)">
            <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
            Back
          </button>
          <div class="modal-actions-right">
            <button class="btn-gold" onclick="launchSurvey()">
              <svg viewBox="0 0 24 24"><polyline points="22 2 15 22 11 13 2 9 22 2"/></svg>
              Launch Survey
            </button>
          </div>
        </div>
      </div>

    </div><!-- /modal-body -->
  </div><!-- /modal -->
</div><!-- /backdrop -->

<!-- Toast notification -->
<div class="toast success" id="toast">
  <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
  <span id="toastMsg">Survey launched successfully!</span>
</div>

<script>
  /* ─── Modal open/close ─── */
  function openModal() {
    goStep(1);
    document.getElementById('questionsList').innerHTML = '';
    qCount = 0;
    addQuestion(); // start with one blank question
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

  /* ─── Step navigation ─── */
  function goStep(n) {
    // Validate step 1 before advancing
    if (n === 2) {
      const title = document.getElementById('s1Title').value.trim();
      const cat   = document.getElementById('s1Cat').value;
      if (!title || !cat) {
        showToast('Please fill in Title and Category.', false);
        return;
      }
    }
    if (n === 3) buildReview();

    [1,2,3].forEach(i => {
      document.getElementById('step'+i).classList.toggle('active', i === n);
      const dot = document.getElementById('dot'+i);
      const lbl = document.getElementById('lbl'+i);
      dot.classList.toggle('active', i === n);
      dot.classList.toggle('done',   i < n);
      lbl.classList.toggle('active', i >= n);
    });
    if (document.getElementById('line1'))
      document.getElementById('line1').classList.toggle('done', n > 1);
    if (document.getElementById('line2'))
      document.getElementById('line2').classList.toggle('done', n > 2);

    // Set done checkmark
    [1,2,3].forEach(i => {
      const dot = document.getElementById('dot'+i);
      if (i < n) dot.textContent = '✓';
      else if (i === n) dot.textContent = i;
      else dot.textContent = i;
    });
  }

  /* ─── Question builder ─── */
  let qCount = 0;
  function addQuestion() {
    qCount++;
    const id = 'q' + qCount;
    const li = document.createElement('div');
    li.className = 'question-card';
    li.id = id;
    li.innerHTML = `
      <div class="q-row">
        <span class="q-num">${qCount}</span>
        <div class="q-body">
          <input type="text" placeholder="Enter your question…" class="q-text">
          <div class="q-type-row">
            <select class="q-type" onchange="renderOptions(this, '${id}')">
              <option value="text">Short Answer</option>
              <option value="textarea">Long Answer</option>
              <option value="radio">Multiple Choice</option>
              <option value="checkbox">Checkboxes</option>
              <option value="scale">Rating Scale (1–5)</option>
            </select>
          </div>
          <div class="q-options-wrap"></div>
        </div>
        <button class="q-delete" onclick="removeQuestion('${id}')" title="Remove">
          <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
      </div>`;
    document.getElementById('questionsList').appendChild(li);
    renumberQuestions();
  }

  function removeQuestion(id) {
    const el = document.getElementById(id);
    if (el) el.remove();
    renumberQuestions();
  }

  function renumberQuestions() {
    document.querySelectorAll('.question-card').forEach((card, i) => {
      const num = card.querySelector('.q-num');
      if (num) num.textContent = i + 1;
    });
  }

  function renderOptions(sel, cardId) {
    const wrap = sel.closest('.q-body').querySelector('.q-options-wrap');
    wrap.innerHTML = '';
    const type = sel.value;
    if (type === 'radio' || type === 'checkbox') {
      const opts = document.createElement('div');
      opts.className = 'q-options';
      opts.innerHTML = `
        <div class="q-option-row">
          <input type="text" placeholder="Option 1">
          <button class="btn-icon" onclick="removeOpt(this)" title="Remove"><svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
        </div>
        <div class="q-option-row">
          <input type="text" placeholder="Option 2">
          <button class="btn-icon" onclick="removeOpt(this)" title="Remove"><svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
        </div>`;
      const addBtn = document.createElement('button');
      addBtn.className = 'btn-add-opt';
      addBtn.type = 'button';
      addBtn.innerHTML = `<svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" fill="none" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Add option`;
      addBtn.onclick = () => addOption(opts);
      wrap.appendChild(opts);
      wrap.appendChild(addBtn);
    } else if (type === 'scale') {
      wrap.innerHTML = `<p style="font-size:12.5px;color:var(--ink-3);margin-top:4px;">Respondents will pick a score from <strong>1</strong> to <strong>5</strong>.</p>`;
    }
  }

  function addOption(container) {
    const n = container.querySelectorAll('.q-option-row').length + 1;
    const row = document.createElement('div');
    row.className = 'q-option-row';
    row.innerHTML = `<input type="text" placeholder="Option ${n}">
      <button class="btn-icon" onclick="removeOpt(this)" title="Remove"><svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>`;
    container.appendChild(row);
  }

  function removeOpt(btn) {
    const row = btn.closest('.q-option-row');
    const container = row.closest('.q-options');
    if (container && container.querySelectorAll('.q-option-row').length > 1) row.remove();
  }

  /* ─── Step 3: build review summary ─── */
  function buildReview() {
    const title  = document.getElementById('s1Title').value.trim();
    const desc   = document.getElementById('s1Desc').value.trim();
    const cat    = document.getElementById('s1Cat').value;
    const status = document.getElementById('s1Status').value;
    const start  = document.getElementById('s1Start').value;
    const end    = document.getElementById('s1End').value;

    const questions = [];
    document.querySelectorAll('.question-card').forEach((card, i) => {
      const text = card.querySelector('.q-text')?.value.trim() || '(untitled question)';
      const type = card.querySelector('.q-type')?.value || 'text';
      const opts = [...card.querySelectorAll('.q-option-row input')].map(o => o.value.trim()).filter(Boolean);
      questions.push({ n: i+1, text, type, opts });
    });

    const rb = document.getElementById('reviewBlock');
    const typeLabel = { text:'Short Answer', textarea:'Long Answer', radio:'Multiple Choice', checkbox:'Checkboxes', scale:'Rating Scale' };
    const statusBadge = status === 'active'
      ? `<span class="badge badge-active" style="font-size:11px;padding:2px 8px;">Active</span>`
      : `<span class="badge badge-pending" style="font-size:11px;padding:2px 8px;">Pending</span>`;

    rb.innerHTML = `
      <div style="background:var(--paper-2);border:1.5px solid var(--paper-3);border-radius:var(--radius);padding:1rem 1.1rem;display:flex;flex-direction:column;gap:6px;">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:8px;">
          <span style="font-weight:600;font-size:14px;color:var(--ink)">${escHtml(title)}</span>
          ${statusBadge}
        </div>
        ${desc ? `<p style="font-size:13px;color:var(--ink-3)">${escHtml(desc)}</p>` : ''}
        <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:2px;">
          <span class="cat-pill">${escHtml(cat)}</span>
          ${start ? `<span style="font-size:12px;color:var(--ink-3)">From ${start}</span>` : ''}
          ${end   ? `<span style="font-size:12px;color:var(--ink-3)">To ${end}</span>`   : ''}
        </div>
      </div>
      <p style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--ink-3);margin-top:4px;">${questions.length} Question${questions.length !== 1 ? 's' : ''}</p>
      ${questions.map(q => `
        <div style="background:var(--paper-2);border:1.5px solid var(--paper-3);border-radius:var(--radius);padding:.85rem 1rem;display:flex;gap:10px;align-items:flex-start;">
          <span class="q-num" style="flex-shrink:0">${q.n}</span>
          <div style="flex:1;">
            <p style="font-size:13.5px;font-weight:500;color:var(--ink);margin-bottom:3px">${escHtml(q.text)}</p>
            <p style="font-size:12px;color:var(--ink-3)">${typeLabel[q.type] || q.type}${q.opts.length ? ' · ' + q.opts.map(o=>`<em>${escHtml(o)}</em>`).join(', ') : ''}</p>
          </div>
        </div>`).join('')}`;
  }

  /* ─── Launch: add row to table ─── */
  function launchSurvey() {
    const title  = document.getElementById('s1Title').value.trim();
    const desc   = document.getElementById('s1Desc').value.trim();
    const cat    = document.getElementById('s1Cat').value;
    const status = document.getElementById('s1Status').value;
    const start  = document.getElementById('s1Start').value;

    const dateLabel = start
      ? new Date(start).toLocaleDateString('en-US', {month:'long', day:'numeric', year:'numeric'})
      : new Date().toLocaleDateString('en-US', {month:'long', day:'numeric', year:'numeric'});

    const badge = status === 'active'
      ? `<span class="badge badge-active">Active</span>`
      : `<span class="badge badge-pending">Pending</span>`;

    const tr = document.createElement('tr');
    tr.dataset.status = status;
    tr.dataset.cat    = cat;
    tr.innerHTML = `
      <td>
        <div class="survey-cell">
          <span class="survey-title">${escHtml(title)}</span>
          <span class="survey-desc">${escHtml(desc)}</span>
        </div>
      </td>
      <td><span class="cat-pill">${escHtml(cat)}</span></td>
      <td style="font-weight:600;color:var(--ink);">0</td>
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
          <a href="#" class="action-btn">
            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" fill="none" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            View
          </a>
          <a href="#" class="action-btn">
            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" fill="none" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            Edit
          </a>
          <button class="action-btn danger" onclick="confirmDelete(this)">
            <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" fill="none" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/></svg>
            Delete
          </button>
        </div>
      </td>`;

    document.getElementById('surveyTable').prepend(tr);
    updateStats();
    closeModal();
    showToast(status === 'active' ? 'Survey launched and is now live!' : 'Survey saved as Pending.');
    filterSurveys();
  }

  /* ─── Delete ─── */
  function confirmDelete(btn) {
    if (confirm('Delete this survey? This cannot be undone.')) {
      btn.closest('tr').remove();
      updateStats();
      filterSurveys();
      showToast('Survey deleted.');
    }
  }

  /* ─── Stats refresh ─── */
  function updateStats() {
    const rows = [...document.querySelectorAll('#surveyTable tr')];
    const total   = rows.length;
    const active  = rows.filter(r => r.dataset.status === 'active').length;
    const pending = rows.filter(r => r.dataset.status === 'pending').length;
    const closed  = rows.filter(r => r.dataset.status === 'closed').length;
    document.getElementById('statTotal').textContent   = total;
    document.getElementById('statActive').textContent  = active;
    document.getElementById('statPending').textContent = pending;
    document.getElementById('statClosed').textContent  = closed;
  }

  /* ─── Filter ─── */
  function filterSurveys() {
    const q      = document.getElementById('surveySearch').value.toLowerCase();
    const cat    = document.getElementById('catFilter').value;
    const status = document.getElementById('statusFilter').value;
    const rows   = document.querySelectorAll('#surveyTable tr');
    let visible  = 0;

    rows.forEach(row => {
      const title   = row.querySelector('.survey-title')?.textContent.toLowerCase() || '';
      const rcat    = row.dataset.cat;
      const rstatus = row.dataset.status;
      const show    = (!q || title.includes(q)) && (!cat || rcat === cat) && (!status || rstatus === status);
      row.style.display = show ? '' : 'none';
      if (show) visible++;
    });

    document.getElementById('emptyState').style.display = visible === 0 ? 'block' : 'none';
    document.getElementById('pagInfo').textContent = `Showing 1–${visible} of ${visible} survey${visible !== 1 ? 's' : ''}`;
  }

  /* ─── Toast ─── */
  function showToast(msg, success = true) {
    const t = document.getElementById('toast');
    document.getElementById('toastMsg').textContent = msg;
    t.className = 'toast' + (success ? ' success' : '');
    requestAnimationFrame(() => { t.classList.add('show'); });
    setTimeout(() => t.classList.remove('show'), 3200);
  }

  /* ─── Util ─── */
  function escHtml(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }
</script>

<?php include('./includes/footer.php'); ?>