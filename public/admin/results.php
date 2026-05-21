<?php
include('../../app/middleware/admin.php');
include('./includes/header.php');
include('./includes/topbar.php');
include('./includes/sidebar.php');
include('../../app/config/config.php');

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

$usersRes      = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM users WHERE role = 'user'");
$totalStudents = (int)mysqli_fetch_assoc($usersRes)['cnt'];

$qRes           = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM questions");
$totalQuestions = (int)mysqli_fetch_assoc($qRes)['cnt'];
?>

<style>
  @import url('https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@300;400;500;600&display=swap');

  :root {
    --ink: #0f0e0d;
    --ink-2: #3a3835;
    --ink-3: #7a776f;
    --paper: #f7f5f0;
    --paper-2: #eceae3;
    --paper-3: #e0ddd4;
    --gold: #c9972b;
    --gold-light: #f5e9cc;
    --gold-dark: #8a6318;
    --teal: #1b6b6b;
    --teal-lt: #d0eaea;
    --rose: #a02c2c;
    --rose-lt: #f5dede;
    --radius: 10px;
    --shadow: 0 2px 16px rgba(15, 14, 13, 0.07);
  }

  * {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
  }

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

  .breadcrumbs {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12.5px;
    font-weight: 500;
    letter-spacing: .04em;
    text-transform: uppercase;
    color: var(--ink-3);
    margin-bottom: 2rem;
  }

  .breadcrumbs a {
    color: var(--ink-3);
    text-decoration: none;
    transition: color .15s;
  }

  .breadcrumbs a:hover {
    color: var(--gold);
  }

  .breadcrumbs .current-page {
    color: var(--ink-2);
  }

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

  header h1 em {
    font-style: italic;
    color: var(--gold);
  }

  .toolbar {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
  }

  .search-wrap {
    position: relative;
  }

  .search-wrap svg {
    position: absolute;
    left: 11px;
    top: 50%;
    transform: translateY(-50%);
    width: 15px;
    height: 15px;
    stroke: var(--ink-3);
    fill: none;
    stroke-width: 2;
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
    width: 240px;
    outline: none;
    transition: border-color .15s;
  }

  .search-wrap input::placeholder {
    color: var(--ink-3);
  }

  .search-wrap input:focus {
    border-color: var(--gold);
    background: #fff;
  }

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
    position: relative;
    overflow: hidden;
    transition: border-color .15s, box-shadow .15s;
  }

  .stat-card:hover {
    border-color: var(--gold);
    box-shadow: var(--shadow);
  }

  .stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: var(--paper-3);
  }

  .stat-card.gold::before {
    background: var(--gold);
  }

  .stat-card.teal::before {
    background: var(--teal);
  }

  .stat-card.rose::before {
    background: var(--rose);
  }

  .stat-label {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: var(--ink-3);
    margin-bottom: 8px;
  }

  .stat-value {
    font-family: 'DM Serif Display', serif;
    font-size: 2rem;
    line-height: 1;
    color: var(--ink);
  }

  .stat-sub {
    font-size: 12px;
    color: var(--ink-3);
    margin-top: 5px;
  }

  .table-wrap {
    background: #fff;
    border: 1.5px solid var(--paper-3);
    border-radius: var(--radius);
    overflow: hidden;
    box-shadow: var(--shadow);
  }

  .table-scroll {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }

  table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
    min-width: 640px;
  }

  thead tr {
    background: var(--paper-2);
    border-bottom: 1.5px solid var(--paper-3);
  }

  th {
    padding: 12px 18px;
    text-align: left;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .05em;
    color: var(--ink-3);
    white-space: nowrap;
  }

  tbody tr {
    border-bottom: 1px solid var(--paper-2);
    transition: background .1s;
  }

  tbody tr:last-child {
    border-bottom: none;
  }

  tbody tr:hover {
    background: var(--paper);
  }

  td {
    padding: 13px 18px;
    color: var(--ink-2);
    vertical-align: middle;
  }

  .survey-title {
    font-weight: 600;
    color: var(--ink);
    font-size: 14px;
  }

  .survey-desc {
    font-size: 12px;
    color: var(--ink-3);
    margin-top: 2px;
  }

  .prog-wrap {
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 130px;
  }

  .prog-track {
    flex: 1;
    height: 5px;
    background: var(--paper-2);
    border-radius: 3px;
    overflow: hidden;
  }

  .prog-fill {
    height: 100%;
    border-radius: 3px;
    background: var(--gold);
  }

  .prog-num {
    font-weight: 600;
    font-size: 13px;
    min-width: 36px;
    text-align: right;
    color: var(--ink);
  }

  .badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    white-space: nowrap;
  }

  .badge::before {
    content: '';
    width: 6px;
    height: 6px;
    border-radius: 50%;
    flex-shrink: 0;
  }

  .badge-published {
    background: var(--teal-lt);
    color: var(--teal);
  }

  .badge-published::before {
    background: var(--teal);
    animation: pulse 1.5s infinite;
  }

  .badge-closed {
    background: var(--rose-lt);
    color: var(--rose);
  }

  .badge-closed::before {
    background: var(--rose);
  }

  .badge-pending {
    background: var(--gold-light);
    color: var(--gold-dark);
  }

  .badge-pending::before {
    background: var(--gold);
  }

  @keyframes pulse {

    0%,
    100% {
      opacity: 1;
      transform: scale(1);
    }

    50% {
      opacity: .4;
      transform: scale(.8);
    }
  }

  .action-btn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: none;
    border: 1.5px solid var(--paper-3);
    border-radius: 7px;
    padding: 5px 13px;
    font-size: 12.5px;
    font-family: 'DM Sans', sans-serif;
    color: var(--ink-2);
    cursor: pointer;
    transition: border-color .15s, color .15s;
  }

  .action-btn:hover {
    border-color: var(--gold);
    color: var(--gold);
  }

  .empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: var(--ink-3);
    font-size: 14px;
  }

  /* ── Modal ── */
  .modal-backdrop {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 1000;
    background: rgba(15, 14, 13, .48);
    backdrop-filter: blur(3px);
    align-items: flex-start;
    justify-content: center;
    padding: 2rem 1rem;
    overflow-y: auto;
  }

  .modal-backdrop.open {
    display: flex;
  }

  .modal-box {
    background: #fff;
    border-radius: 14px;
    width: min(860px, 100%);
    margin: auto;
    display: flex;
    flex-direction: column;
    box-shadow: 0 12px 56px rgba(15, 14, 13, .22);
    animation: slideUp .2s ease;
  }

  @keyframes slideUp {
    from {
      transform: translateY(18px);
      opacity: 0;
    }

    to {
      transform: translateY(0);
      opacity: 1;
    }
  }

  .modal-header {
    padding: 1.75rem 2rem 1.25rem;
    border-bottom: 1.5px solid var(--paper-2);
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1rem;
    position: sticky;
    top: 0;
    background: #fff;
    z-index: 2;
    border-radius: 14px 14px 0 0;
  }

  .modal-close {
    background: var(--paper-2);
    border: none;
    border-radius: 8px;
    width: 34px;
    height: 34px;
    cursor: pointer;
    font-size: 18px;
    color: var(--ink-3);
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background .12s, color .12s;
  }

  .modal-close:hover {
    background: var(--paper-3);
    color: var(--ink);
  }

  .modal-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    border-bottom: 1.5px solid var(--paper-2);
  }

  .modal-stat {
    padding: 1rem 1.25rem;
    border-right: 1.5px solid var(--paper-2);
  }

  .modal-stat:last-child {
    border-right: none;
  }

  .modal-stat-label {
    font-size: 10.5px;
    font-weight: 600;
    letter-spacing: .08em;
    text-transform: uppercase;
    color: var(--ink-3);
    margin-bottom: 4px;
  }

  .modal-stat-value {
    font-family: 'DM Serif Display', serif;
    font-size: 1.5rem;
    color: var(--ink);
    line-height: 1.1;
  }

  .modal-stat-value span {
    font-size: .9rem;
    color: var(--ink-3);
    font-family: 'DM Sans', sans-serif;
  }

  .modal-tabs {
    display: flex;
    gap: 0;
    border-bottom: 1.5px solid var(--paper-2);
    background: var(--paper);
  }

  .modal-tab {
    padding: 11px 22px;
    font-size: 13px;
    font-weight: 600;
    font-family: 'DM Sans', sans-serif;
    color: var(--ink-3);
    cursor: pointer;
    border: none;
    background: none;
    border-bottom: 2.5px solid transparent;
    transition: color .12s, border-color .12s;
    margin-bottom: -1.5px;
  }

  .modal-tab.active {
    color: var(--gold);
    border-bottom-color: var(--gold);
  }

  .modal-tab:hover:not(.active) {
    color: var(--ink-2);
  }

  .modal-body {
    padding: 1.5rem 2rem 2rem;
  }

  /* ── Questions tab ── */
  .question-block {
    background: var(--paper);
    border: 1.5px solid var(--paper-3);
    border-radius: var(--radius);
    margin-bottom: 12px;
    overflow: hidden;
  }

  .question-block-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1rem;
    padding: 1rem 1.25rem;
  }

  .question-num {
    font-family: 'DM Serif Display', serif;
    font-size: 1.05rem;
    color: var(--gold);
    flex-shrink: 0;
  }

  .question-text {
    font-weight: 600;
    font-size: 14px;
    color: var(--ink);
    flex: 1;
  }

  .type-pill {
    font-size: 11px;
    font-weight: 600;
    letter-spacing: .05em;
    text-transform: uppercase;
    color: var(--ink-3);
    padding: 3px 8px;
    background: var(--paper-2);
    border-radius: 6px;
    white-space: nowrap;
    flex-shrink: 0;
  }

  .answers-list {
    border-top: 1.5px solid var(--paper-3);
  }

  .answer-row {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 10px 1.25rem;
    border-bottom: 1px solid var(--paper-2);
    font-size: 13.5px;
  }

  .answer-row:last-child {
    border-bottom: none;
  }

  .answer-avatar {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: var(--gold-light);
    border: 1.5px solid var(--paper-3);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: 700;
    color: var(--gold-dark);
    flex-shrink: 0;
  }

  .answer-meta {
    font-size: 12px;
    color: var(--ink-3);
    margin-top: 1px;
  }

  .answer-text {
    color: var(--ink);
    margin-top: 3px;
    line-height: 1.5;
  }

  .no-answer {
    color: var(--ink-3);
    font-style: italic;
    margin-top: 3px;
    font-size: 13px;
  }

  /* ── Respondents tab ── */
  .respondent-card {
    background: var(--paper);
    border: 1.5px solid var(--paper-3);
    border-radius: var(--radius);
    margin-bottom: 10px;
    overflow: hidden;
  }

  .respondent-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: .85rem 1.25rem;
    cursor: pointer;
    gap: 1rem;
    transition: background .12s;
  }

  .respondent-header:hover {
    background: var(--paper-2);
  }

  .respondent-info {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .resp-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: var(--gold-light);
    border: 1.5px solid var(--paper-3);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 700;
    color: var(--gold-dark);
    flex-shrink: 0;
  }

  .resp-name {
    font-weight: 600;
    font-size: 14px;
    color: var(--ink);
  }

  .resp-sub {
    font-size: 12px;
    color: var(--ink-3);
    margin-top: 1px;
  }

  .resp-date {
    font-size: 12px;
    color: var(--ink-3);
    white-space: nowrap;
  }

  .resp-chevron {
    transition: transform .2s;
    color: var(--ink-3);
    flex-shrink: 0;
  }

  .resp-chevron.open {
    transform: rotate(90deg);
  }

  .respondent-answers {
    display: none;
    border-top: 1.5px solid var(--paper-3);
  }

  .respondent-answers.open {
    display: block;
  }

  .resp-answer-row {
    display: grid;
    grid-template-columns: 1fr 1.4fr;
    gap: 1rem;
    padding: 10px 1.25rem;
    border-bottom: 1px solid var(--paper-2);
    font-size: 13.5px;
  }

  .resp-answer-row:last-child {
    border-bottom: none;
  }

  .resp-q-text {
    color: var(--ink-3);
    font-size: 12.5px;
    font-weight: 500;
  }

  .resp-a-text {
    color: var(--ink);
  }

  .resp-a-empty {
    color: var(--ink-3);
    font-style: italic;
    font-size: 13px;
  }

  /* Spinner */
  .modal-loading {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 3rem;
    gap: 12px;
    color: var(--ink-3);
    font-size: 14px;
  }

  .spinner {
    width: 20px;
    height: 20px;
    border: 2.5px solid var(--paper-3);
    border-top-color: var(--gold);
    border-radius: 50%;
    animation: spin .7s linear infinite;
    flex-shrink: 0;
  }

  @keyframes spin {
    to {
      transform: rotate(360deg);
    }
  }

  .modal-empty {
    text-align: center;
    padding: 2rem;
    color: var(--ink-3);
    font-size: 14px;
  }

  /* ── Summary chart bar ── */
  .chart-bar-wrap {
    margin-bottom: 8px;
  }

  .chart-bar-label {
    display: flex;
    justify-content: space-between;
    font-size: 12.5px;
    color: var(--ink-2);
    margin-bottom: 3px;
  }

  .chart-bar-track {
    height: 8px;
    background: var(--paper-2);
    border-radius: 4px;
    overflow: hidden;
  }

  .chart-bar-fill {
    height: 100%;
    border-radius: 4px;
    background: var(--gold);
    transition: width .4s ease;
  }

  @media (max-width: 900px) {
    .stat-strip {
      grid-template-columns: repeat(2, 1fr);
    }

    .main-wrapper {
      padding: 1.5rem 1.25rem 3rem;
    }

    .modal-stats {
      grid-template-columns: 1fr 1fr;
    }

    .modal-stats .modal-stat:nth-child(3) {
      grid-column: span 2;
      border-right: none;
      border-top: 1.5px solid var(--paper-2);
    }

    .modal-header,
    .modal-body {
      padding-left: 1.25rem;
      padding-right: 1.25rem;
    }

    .resp-answer-row {
      grid-template-columns: 1fr;
      gap: .4rem;
    }
  }
</style>

<div class="main-wrapper">

  <div class="breadcrumbs">
    <a href="index">Home</a>
    <span>/</span>
    <span class="current-page">Survey Results</span>
  </div>

<header>
  <div>
    <h1>Data <em>Analytics</em></h1>
    <p style="font-size:13.5px;color:var(--ink-3);margin-top:6px;">Track survey performance and view detailed response data.</p>
  </div>
  <div class="toolbar">
      <div class="search-wrap">
        <svg viewBox="0 0 24 24">
          <circle cx="11" cy="11" r="8" />
          <line x1="21" y1="21" x2="16.65" y2="16.65" />
        </svg>
        <input type="text" id="surveySearch" placeholder="Search surveys…" oninput="filterTable()">
      </div>
    </div>
  </header>

  <div class="stat-strip">
    <div class="stat-card gold">
      <div class="stat-label">Total Submissions</div>
      <div class="stat-value"><?= number_format($totalResponses) ?></div>
      <div class="stat-sub">Out of <?= number_format($totalStudents) ?> registered students</div>
    </div>
    <div class="stat-card teal">
      <div class="stat-label">Open Surveys</div>
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
                <td colspan="6">
                  <div class="empty-state">No surveys found.</div>
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($surveys as $s):
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
                  <td style="font-weight:600; color:var(--ink);">
                    <?= number_format($responses) ?> / <?= $totalStudents ?>
                  </td>
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
                    <button class="action-btn" onclick="openSurvey(<?= (int)$s['id'] ?>)">
                      View Results
                      <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="9 18 15 12 9 6" />
                      </svg>
                    </button>
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

<!-- ── Modal ── -->
<div class="modal-backdrop" id="surveyModal">
  <div class="modal-box">

    <div class="modal-header">
      <div>
        <div id="modalStatus" style="margin-bottom:8px;"></div>
        <h2 id="modalTitle" style="font-family:'DM Serif Display',serif; font-size:1.65rem; font-weight:400; color:var(--ink); line-height:1.2;"></h2>
        <p id="modalDesc" style="font-size:13.5px; color:var(--ink-3); margin-top:6px; line-height:1.5;"></p>
      </div>
      <button class="modal-close" onclick="closeModal()">✕</button>
    </div>

    <div class="modal-stats" id="modalStats"></div>

    <div class="modal-tabs">
      <button class="modal-tab active" id="tabQBtn" onclick="switchTab('questions')">Questions &amp; Answers</button>
      <button class="modal-tab" id="tabRBtn" onclick="switchTab('respondents')">By Respondent</button>
      <button class="modal-tab" id="tabSBtn" onclick="switchTab('summary')">Summary</button>
    </div>

    <div class="modal-body">
      <div id="modalLoading" class="modal-loading">
        <div class="spinner"></div> Loading results…
      </div>
      <div id="tabQuestions" style="display:none;"></div>
      <div id="tabRespondents" style="display:none;"></div>
      <div id="tabSummary" style="display:none;"></div>
    </div>

  </div>
</div>

<script>
  const CONTROLLER_URL = '../../app/controllers/surveyController.php';

  const typeLabels = {
    text: 'Short Answer',
    textarea: 'Long Answer',
    radio: 'Multiple Choice',
    checkbox: 'Checkboxes',
    select: 'Dropdown',
    rating: 'Rating',
  };

  let _currentTab = 'questions';

  /* ── Tab switching ─────────────────────────────────────────── */
  function switchTab(tab) {
    _currentTab = tab;
    ['questions', 'respondents', 'summary'].forEach(t => {
      const btn = document.getElementById('tab' + t.charAt(0).toUpperCase() + t.slice(1).replace('summary', 'S').replace('questions', 'Q').replace('respondents', 'R') + 'Btn');
    });
    document.getElementById('tabQBtn').classList.toggle('active', tab === 'questions');
    document.getElementById('tabRBtn').classList.toggle('active', tab === 'respondents');
    document.getElementById('tabSBtn').classList.toggle('active', tab === 'summary');
    document.getElementById('tabQuestions').style.display = tab === 'questions' ? '' : 'none';
    document.getElementById('tabRespondents').style.display = tab === 'respondents' ? '' : 'none';
    document.getElementById('tabSummary').style.display = tab === 'summary' ? '' : 'none';
  }

  /* ── Open modal ────────────────────────────────────────────── */
  function openSurvey(id) {
    const modal = document.getElementById('surveyModal');
    modal.classList.add('open');
    document.body.style.overflow = 'hidden';

    // Reset state
    document.getElementById('modalStatus').innerHTML = '';
    document.getElementById('modalTitle').textContent = '';
    document.getElementById('modalDesc').textContent = '';
    document.getElementById('modalStats').innerHTML = '';
    document.getElementById('tabQuestions').innerHTML = '';
    document.getElementById('tabRespondents').innerHTML = '';
    document.getElementById('tabSummary').innerHTML = '';
    document.getElementById('modalLoading').style.display = 'flex';
    document.getElementById('tabQuestions').style.display = 'none';
    document.getElementById('tabRespondents').style.display = 'none';
    document.getElementById('tabSummary').style.display = 'none';
    _currentTab = 'questions';
    document.getElementById('tabQBtn').classList.add('active');
    document.getElementById('tabRBtn').classList.remove('active');
    document.getElementById('tabSBtn').classList.remove('active');

    fetch(`${CONTROLLER_URL}?id=${id}`)
      .then(r => r.json())
      .then(data => {
        document.getElementById('modalLoading').style.display = 'none';

        if (!data.success) {
          document.getElementById('tabQuestions').innerHTML =
            `<div class="modal-empty">⚠️ Could not load survey data.</div>`;
          switchTab('questions');
          return;
        }

        const s = data.survey;
        const questions = data.questions; // [{id, question_text, question_type}]
        const responses = data.responses; // [{response_id, student_name, email, submitted_at, answers:{qid:text}}]

        /* ── Header ── */
        const badgeMap = {
          published: ['badge-published', 'Published'],
          pending: ['badge-pending', 'Pending'],
          closed: ['badge-closed', 'Closed'],
        };
        const [cls, lbl] = badgeMap[s.status] ?? ['badge-closed', s.status];
        document.getElementById('modalStatus').innerHTML =
          `<span class="badge ${cls}">${lbl}</span>`;
        document.getElementById('modalTitle').textContent = s.title;
        document.getElementById('modalDesc').textContent = s.description ?? '';

        /* ── Stats strip ── */
        document.getElementById('modalStats').innerHTML = `
          <div class="modal-stat">
            <div class="modal-stat-label">Responses</div>
            <div class="modal-stat-value">${data.responseCount} <span>/ ${data.totalStudents}</span></div>
          </div>
          <div class="modal-stat">
            <div class="modal-stat-label">Response Rate</div>
            <div class="modal-stat-value">${data.pct}%</div>
          </div>
          <div class="modal-stat">
            <div class="modal-stat-label">Created</div>
            <div style="font-size:13.5px;font-weight:500;color:var(--ink);margin-top:6px;">${fmtDate(s.created_at)}</div>
          </div>`;

        /* ────────────────────────────────────────────────────────
           TAB 1 — Questions & Answers
           For every question, list every respondent's answer
        ──────────────────────────────────────────────────────── */
        const qPanel = document.getElementById('tabQuestions');

        if (!questions.length) {
          qPanel.innerHTML = `<div class="modal-empty">No questions found for this survey.</div>`;
        } else {
          qPanel.innerHTML = questions.map((q, i) => {
            const typeLabel = typeLabels[q.question_type] ?? q.question_type;

            let answerRows;
            if (!responses.length) {
              answerRows = `<div class="answer-row">
                <div style="color:var(--ink-3);font-size:13px;padding:4px 0;">No responses yet.</div>
              </div>`;
            } else {
              answerRows = responses.map(resp => {
                // Coerce key — PHP JSON-encodes numeric keys as integers, JS object keys are strings
                const answerTxt = resp.answers[String(q.id)] ?? resp.answers[q.id] ?? null;
                const date = fmtDateTime(resp.submitted_at);
                const initials = initials2(resp.student_name);
                return `
                  <div class="answer-row">
                    <div class="answer-avatar">${initials}</div>
                    <div style="flex:1;min-width:0;">
                      <div style="font-weight:600;font-size:13px;color:var(--ink);">${escHtml(resp.student_name)}</div>
                      <div class="answer-meta">${escHtml(resp.email)} &middot; ${date}</div>
                      ${answerTxt !== null && String(answerTxt).trim() !== ''
                        ? `<div class="answer-text">${escHtml(String(answerTxt))}</div>`
                        : `<div class="no-answer">No answer provided</div>`}
                    </div>
                  </div>`;
              }).join('');
            }

            return `
              <div class="question-block">
                <div class="question-block-header">
                  <div style="display:flex;gap:8px;align-items:flex-start;flex:1;min-width:0;">
                    <span class="question-num">${i + 1}.</span>
                    <span class="question-text">${escHtml(q.question_text)}</span>
                  </div>
                  <span class="type-pill">${typeLabel}</span>
                </div>
                <div class="answers-list">${answerRows}</div>
              </div>`;
          }).join('');
        }

        /* ────────────────────────────────────────────────────────
           TAB 2 — By Respondent
           Collapsible card per student showing all their answers
        ──────────────────────────────────────────────────────── */
        const rPanel = document.getElementById('tabRespondents');

        if (!responses.length) {
          rPanel.innerHTML = `<div class="modal-empty">No responses yet.</div>`;
        } else {
          rPanel.innerHTML = responses.map((resp, ri) => {
            const date = fmtDateTime(resp.submitted_at);
            const initials = initials2(resp.student_name);

            const answerRows = questions.length ?
              questions.map(q => {
                const answerTxt = resp.answers[String(q.id)] ?? resp.answers[q.id] ?? null;
                return `
                    <div class="resp-answer-row">
                      <div class="resp-q-text">${escHtml(q.question_text)}</div>
                      ${answerTxt !== null && String(answerTxt).trim() !== ''
                        ? `<div class="resp-a-text">${escHtml(String(answerTxt))}</div>`
                        : `<div class="resp-a-empty">No answer</div>`}
                    </div>`;
              }).join('') :
              `<div class="resp-answer-row"><div class="resp-a-empty">No questions found.</div></div>`;

            return `
              <div class="respondent-card">
                <div class="respondent-header" onclick="toggleRespondent(${ri})">
                  <div class="respondent-info">
                    <div class="resp-avatar">${initials}</div>
                    <div>
                      <div class="resp-name">${escHtml(resp.student_name)}</div>
                      <div class="resp-sub">${escHtml(resp.email)}</div>
                    </div>
                  </div>
                  <div style="display:flex;align-items:center;gap:12px;">
                    <span class="resp-date">${date}</span>
                    <svg class="resp-chevron" id="chev-${ri}" viewBox="0 0 24 24" width="14" height="14"
                         fill="none" stroke="currentColor" stroke-width="2.5">
                      <polyline points="9 18 15 12 9 6"/>
                    </svg>
                  </div>
                </div>
                <div class="respondent-answers" id="resp-answers-${ri}">
                  ${answerRows}
                </div>
              </div>`;
          }).join('');
        }

        /* ────────────────────────────────────────────────────────
           TAB 3 — Summary
           Per-question answer frequency / bar chart
        ──────────────────────────────────────────────────────── */
        const sPanel = document.getElementById('tabSummary');

        if (!questions.length || !responses.length) {
          sPanel.innerHTML = `<div class="modal-empty">Not enough data to summarise.</div>`;
        } else {
          sPanel.innerHTML = questions.map((q, i) => {
            const typeLabel = typeLabels[q.question_type] ?? q.question_type;

            // Tally answers
            const tally = {};
            let answered = 0;
            responses.forEach(resp => {
              const raw = resp.answers[String(q.id)] ?? resp.answers[q.id] ?? null;
              if (raw === null || String(raw).trim() === '') return;
              answered++;
              // For checkbox answers that may be comma-separated
              const parts = q.question_type === 'checkbox' ?
                String(raw).split(',').map(s => s.trim()).filter(Boolean) :
                [String(raw).trim()];
              parts.forEach(p => {
                tally[p] = (tally[p] ?? 0) + 1;
              });
            });

            const skipped = responses.length - answered;
            const sorted = Object.entries(tally).sort((a, b) => b[1] - a[1]);
            const maxVal = sorted[0]?.[1] ?? 1;

            let chartHtml;
            if (!sorted.length) {
              chartHtml = `<p style="color:var(--ink-3);font-size:13px;padding:4px 0;">No answers recorded.</p>`;
            } else if (['text', 'textarea'].includes(q.question_type)) {
              // Free-text: just list distinct answers with counts
              chartHtml = sorted.slice(0, 20).map(([val, cnt]) => `
                <div class="chart-bar-wrap">
                  <div class="chart-bar-label">
                    <span style="flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${escHtml(val)}</span>
                    <span style="margin-left:12px;font-weight:600;flex-shrink:0;">${cnt}</span>
                  </div>
                  <div class="chart-bar-track">
                    <div class="chart-bar-fill" style="width:${Math.round((cnt/maxVal)*100)}%;"></div>
                  </div>
                </div>`).join('') +
                (sorted.length > 20 ? `<p style="font-size:12px;color:var(--ink-3);margin-top:8px;">…and ${sorted.length - 20} more unique answers</p>` : '');
            } else {
              chartHtml = sorted.map(([val, cnt]) => `
                <div class="chart-bar-wrap">
                  <div class="chart-bar-label">
                    <span>${escHtml(val)}</span>
                    <span style="font-weight:600;">${cnt} <span style="font-weight:400;color:var(--ink-3);">(${Math.round((cnt/responses.length)*100)}%)</span></span>
                  </div>
                  <div class="chart-bar-track">
                    <div class="chart-bar-fill" style="width:${Math.round((cnt/maxVal)*100)}%;"></div>
                  </div>
                </div>`).join('');
            }

            return `
              <div class="question-block" style="margin-bottom:14px;">
                <div class="question-block-header">
                  <div style="display:flex;gap:8px;align-items:flex-start;flex:1;min-width:0;">
                    <span class="question-num">${i + 1}.</span>
                    <span class="question-text">${escHtml(q.question_text)}</span>
                  </div>
                  <span class="type-pill">${typeLabel}</span>
                </div>
                <div style="padding:1rem 1.25rem; border-top:1.5px solid var(--paper-3);">
                  ${chartHtml}
                  <div style="margin-top:10px;font-size:12px;color:var(--ink-3);">
                    ${answered} answered &nbsp;·&nbsp; ${skipped} skipped
                  </div>
                </div>
              </div>`;
          }).join('');
        }

        /* ── Reveal the correct tab ── */
        switchTab(_currentTab);
      })
      .catch(err => {
        console.error(err);
        document.getElementById('modalLoading').style.display = 'none';
        document.getElementById('tabQuestions').innerHTML =
          `<div class="modal-empty">⚠️ Network error. Please try again.</div>`;
        switchTab('questions');
      });
  }

  /* ── Respondent accordion ────────────────────────────────── */
  function toggleRespondent(ri) {
    const panel = document.getElementById(`resp-answers-${ri}`);
    const chevron = document.getElementById(`chev-${ri}`);
    const isOpen = panel.classList.toggle('open');
    chevron.classList.toggle('open', isOpen);
  }

  /* ── Close modal ─────────────────────────────────────────── */
  function closeModal() {
    document.getElementById('surveyModal').classList.remove('open');
    document.body.style.overflow = '';
  }
  document.getElementById('surveyModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
  });
  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeModal();
  });

  /* ── Table search ────────────────────────────────────────── */
  function filterTable() {
    const q = document.getElementById('surveySearch').value.toLowerCase();
    document.querySelectorAll('#surveyTable tbody tr').forEach(row => {
      row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
  }

  /* ── Utilities ───────────────────────────────────────────── */
  function escHtml(str) {
    return String(str ?? '')
      .replace(/&/g, '&amp;').replace(/</g, '&lt;')
      .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
  }

  function fmtDate(dateStr) {
    return new Date(dateStr).toLocaleDateString('en-US', {
      month: 'short',
      day: 'numeric',
      year: 'numeric'
    });
  }

  function fmtDateTime(dateStr) {
    return new Date(dateStr).toLocaleDateString('en-US', {
      month: 'short',
      day: 'numeric',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    });
  }

  function initials2(name) {
    const parts = String(name ?? '').trim().split(/\s+/);
    if (parts.length >= 2) return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
    return (parts[0]?.[0] ?? '?').toUpperCase();
  }
</script>

<?php include('./includes/footer.php'); ?>