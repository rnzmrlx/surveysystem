<?php
include('../../app/middleware/admin.php');
include('./includes/header.php');
include('./includes/topbar.php');
include('./includes/sidebar.php');
include('../../app/config/config.php');

// Auto-close expired surveys
$today = date('Y-m-d');
$now = date('Y-m-d H:i:s');
mysqli_query($conn, "UPDATE surveys SET status = 'closed' WHERE status = 'published' AND end_date IS NOT NULL AND end_date < '$now'");// Fetch all surveys with response count
$sql = "SELECT s.id, s.title, s.description, s.status, s.created_at, s.category_id,
               COUNT(DISTINCT r.user_id) as response_count
        FROM surveys s
        LEFT JOIN responses r ON r.survey_id = s.id
        GROUP BY s.id
        ORDER BY s.created_at DESC";
$result = mysqli_query($conn, $sql);

$surveys       = [];
$statTotal     = 0;
$statPublished = 0;
$statPending   = 0;
$statClosed    = 0;

while ($row = mysqli_fetch_assoc($result)) {
  $surveys[] = $row;
  $statTotal++;
  if ($row['status'] === 'published') $statPublished++;
  elseif ($row['status'] === 'pending') $statPending++;
  elseif ($row['status'] === 'closed')  $statClosed++;
}

// Total registered students (for response rate denominator)
$usersRes      = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM users WHERE role = 'user'");
$totalStudents = (int)mysqli_fetch_assoc($usersRes)['cnt'];
?>

<style>
  @import url('https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@300;400;500;600&display=swap');

  /* ── Neutralise AdminLTE/NiceAdmin transforms that break position:fixed ── */
  body {
    transform: none !important;
  }

  #main,
  #content,
  .wrapper,
  .content-wrapper,
  .page-wrapper {
    transform: none !important;
    filter: none !important;
    perspective: none !important;
  }

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

  /* ── Page lock when modal open ── */
  body.modal-open {
    overflow: hidden;
  }

  /* ════════════════════════════════════════
   PAGE LAYOUT
   ════════════════════════════════════════ */
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

  .breadcrumbs span {
    color: var(--paper-3);
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

  /* ── Toolbar ── */
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
    width: 220px;
    outline: none;
    transition: border-color .15s;
  }

  .search-wrap input::placeholder {
    color: var(--ink-3);
  }

  .search-wrap input:focus {
    border-color: var(--gold);
  }

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
    transition: border-color .15s;
  }

  .filter-select:focus {
    border-color: var(--gold);
  }

  .btn-gold {
    display: flex;
    align-items: center;
    gap: 6px;
    background: var(--gold);
    color: #fff;
    border: none;
    border-radius: var(--radius);
    padding: 8.5px 16px;
    font-family: 'DM Sans', sans-serif;
    font-size: 13.5px;
    font-weight: 500;
    cursor: pointer;
    text-decoration: none;
    transition: background .15s, transform .1s;
    white-space: nowrap;
  }

  .btn-gold:hover {
    background: var(--gold-dark);
    color: #fff;
  }

  .btn-gold:active {
    transform: scale(.98);
  }

  .btn-gold svg {
    width: 14px;
    height: 14px;
    stroke: currentColor;
    fill: none;
    stroke-width: 2;
  }

  /* ── Stat Strip ── */
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
    border-radius: 2px 2px 0 0;
  }

  .stat-card.gold::before {
    background: var(--gold);
  }

  .stat-card.teal::before {
    background: var(--teal);
  }

  .stat-card.gold-acc::before {
    background: var(--gold);
  }

  .stat-card.rose::before {
    background: var(--rose);
  }

  .stat-label {
    font-size: 11px;
    font-weight: 600;
    letter-spacing: .08em;
    text-transform: uppercase;
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

  /* ── Table ── */
.table-wrap {
    background: #fff;
    border: 1.5px solid var(--paper-3);
    border-radius: var(--radius);
    overflow: visible;
    box-shadow: var(--shadow);
  }

.table-scroll {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    border-radius: var(--radius);
  }

table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
    min-width: 800px;
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
    letter-spacing: .08em;
    text-transform: uppercase;
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

th.col-actions,
  td.col-actions {
    white-space: nowrap;
  }

  .survey-cell {
    display: flex;
    flex-direction: column;
    gap: 2px;
  }

  .survey-title {
    font-weight: 500;
    color: var(--ink);
    font-size: 14px;
  }

  .survey-desc {
    font-size: 12px;
    color: var(--ink-3);
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
    transition: width .4s;
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
    background: none;
    border: 1.5px solid var(--paper-3);
    border-radius: 7px;
    padding: 5px 11px;
    font-family: 'DM Sans', sans-serif;
    font-size: 12.5px;
    color: var(--ink-2);
    cursor: pointer;
    text-decoration: none;
    transition: border-color .15s, color .15s, background .15s;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    white-space: nowrap;
  }

  .action-btn:hover {
    border-color: var(--ink-2);
    color: var(--ink);
    background: var(--paper);
  }

  .action-btn.danger:hover {
    border-color: var(--rose);
    color: var(--rose);
    background: var(--rose-lt);
  }

  .action-btns {
    display: flex;
    align-items: center;
    gap: 5px;
  }

  /* ── Pagination ── */
  .pagination {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 18px;
    border-top: 1.5px solid var(--paper-2);
    font-size: 13px;
    color: var(--ink-3);
    background: var(--paper);
  }

  .page-btns {
    display: flex;
    gap: 6px;
  }

  .page-btn {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 7px;
    border: 1.5px solid var(--paper-3);
    background: none;
    font-family: 'DM Sans', sans-serif;
    font-size: 13px;
    color: var(--ink-2);
    cursor: pointer;
    transition: all .12s;
  }

  .page-btn:hover {
    border-color: var(--ink-2);
    color: var(--ink);
  }

  .page-btn.active {
    background: var(--ink);
    color: var(--paper);
    border-color: var(--ink);
  }

  .page-btn:disabled {
    opacity: .35;
    cursor: not-allowed;
  }

  /* ════════════════════════════════════════
   MODAL  — THE FULLY FIXED VERSION
   Key: .sm-backdrop is teleported to <body>
   at runtime so no transformed ancestor
   can break position:fixed.
   ════════════════════════════════════════ */

  /* Backdrop / Overlay */
  .sm-backdrop {
    display: none;
    position: fixed;
    /* works correctly once on <body> */
    inset: 0;
    z-index: 99999;
    background: rgba(15, 14, 13, .65);
    backdrop-filter: blur(2px);
    -webkit-backdrop-filter: blur(2px);
    align-items: center;
    justify-content: center;
    padding: 1rem;
  }

  .sm-backdrop.sm-open {
    display: flex;
  }

  /* Modal box */
  .sm-box {
    background: #fff;
    border: 1.5px solid var(--paper-3);
    border-radius: 16px;
    box-shadow: 0 24px 64px rgba(15, 14, 13, .28);
    width: 100%;
    max-width: 660px;
    max-height: 90vh;
    overflow-y: auto;
    position: relative;
    z-index: 100000;
    animation: smIn .22s cubic-bezier(.34, 1.56, .64, 1) both;
    scrollbar-width: thin;
    scrollbar-color: var(--paper-3) transparent;
  }

  .sm-box::-webkit-scrollbar {
    width: 6px;
  }

  .sm-box::-webkit-scrollbar-thumb {
    background: var(--paper-3);
    border-radius: 3px;
  }

  @keyframes smIn {
    from {
      opacity: 0;
      transform: translateY(18px) scale(.97);
    }

    to {
      opacity: 1;
      transform: translateY(0) scale(1);
    }
  }

  /* Step indicator */
  .sm-steps {
    display: flex;
    align-items: center;
    padding: 1.5rem 2rem 0;
    gap: 0;
  }

  .sm-step {
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .sm-step__dot {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    border: 2px solid var(--paper-3);
    background: var(--paper-2);
    color: var(--ink-3);
    font-size: 12px;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: all .2s;
  }

  .sm-step__label {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .05em;
    color: var(--ink-3);
    transition: color .2s;
  }

  .sm-step--active .sm-step__dot {
    border-color: var(--gold);
    background: var(--gold);
    color: #fff;
  }

  .sm-step--active .sm-step__label {
    color: var(--ink);
  }

  .sm-step--done .sm-step__dot {
    border-color: var(--teal);
    background: var(--teal);
    color: #fff;
  }

  .sm-step__line {
    flex: 1;
    height: 2px;
    background: var(--paper-3);
    margin: 0 10px;
    min-width: 24px;
    transition: background .3s;
  }

  .sm-step__line--done {
    background: var(--teal);
  }

  /* Panels */
  .sm-panel {
    display: none;
    padding: 1.75rem 2rem 2rem;
  }

  .sm-panel--active {
    display: block;
  }

  .sm-panel__head {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1.5rem;
  }

  .sm-panel__head h2 {
    font-family: 'DM Serif Display', serif;
    font-size: 1.5rem;
    font-weight: 400;
    color: var(--ink);
  }

  .sm-panel__head h2 em {
    font-style: italic;
    color: var(--gold);
  }

  .sm-close {
    background: none;
    border: none;
    font-size: 24px;
    line-height: 1;
    color: var(--ink-3);
    cursor: pointer;
    padding: 0;
    transition: color .15s;
    flex-shrink: 0;
  }

  .sm-close:hover {
    color: var(--ink);
  }

  /* Form */
  .sm-form {
    display: flex;
    flex-direction: column;
    gap: 1rem;
  }

  .sm-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
  }

  .sm-field {
    display: flex;
    flex-direction: column;
    gap: 5px;
  }

  .sm-field label {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: var(--ink-3);
  }

  .sm-field .req {
    color: var(--rose);
  }

  .sm-field input,
  .sm-field select,
  .sm-field textarea {
    background: var(--paper-2);
    border: 1.5px solid var(--paper-3);
    border-radius: var(--radius);
    padding: 9px 13px;
    font-family: 'DM Sans', sans-serif;
    font-size: 13.5px;
    color: var(--ink);
    outline: none;
    transition: border-color .15s, background .15s;
    width: 100%;
  }

  .sm-field input:focus,
  .sm-field select:focus,
  .sm-field textarea:focus {
    border-color: var(--gold);
    background: #fff;
  }

  .sm-field textarea {
    resize: vertical;
    min-height: 72px;
  }

  /* Error banner */
  .sm-error {
    display: none;
    font-size: 12.5px;
    color: var(--rose);
    background: var(--rose-lt);
    border-radius: 8px;
    padding: 8px 14px;
    margin-top: 6px;
  }

  .sm-error--show {
    display: block;
  }

  /* Questions */
  .sm-qlist {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-bottom: 12px;
  }

  .sm-qcard {
    background: var(--paper-2);
    border: 1.5px solid var(--paper-3);
    border-radius: var(--radius);
    padding: 1rem;
    transition: border-color .15s;
  }

  .sm-qcard:focus-within {
    border-color: var(--gold);
  }

  .sm-qrow {
    display: flex;
    gap: 10px;
    align-items: flex-start;
  }

  .sm-qnum {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: var(--gold-light);
    color: var(--gold-dark);
    font-size: 11px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    margin-top: 3px;
  }

  .sm-qbody {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 8px;
  }

  .sm-qbody input[type="text"],
  .sm-qbody select {
    background: #fff;
    border: 1.5px solid var(--paper-3);
    border-radius: 8px;
    padding: 8px 12px;
    font-family: 'DM Sans', sans-serif;
    font-size: 13.5px;
    color: var(--ink);
    outline: none;
    transition: border-color .15s;
    width: 100%;
  }

  .sm-qbody input[type="text"]:focus,
  .sm-qbody select:focus {
    border-color: var(--gold);
  }

  .sm-opts {
    display: flex;
    flex-direction: column;
    gap: 6px;
    margin-top: 4px;
  }

  .sm-opt-row {
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .sm-opt-row input {
    flex: 1;
    background: #fff;
    border: 1.5px solid var(--paper-3);
    border-radius: 7px;
    padding: 6px 10px;
    font-family: 'DM Sans', sans-serif;
    font-size: 13px;
    color: var(--ink);
    outline: none;
    transition: border-color .15s;
  }

  .sm-opt-row input:focus {
    border-color: var(--gold);
  }

  .sm-opt-del {
    background: none;
    border: 1.5px solid var(--paper-3);
    border-radius: 6px;
    width: 26px;
    height: 26px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: var(--ink-3);
    font-size: 16px;
    line-height: 1;
    transition: all .12s;
    flex-shrink: 0;
  }

  .sm-opt-del:hover {
    border-color: var(--rose);
    color: var(--rose);
    background: var(--rose-lt);
  }

  .sm-btn-addopt {
    background: none;
    border: 1.5px dashed var(--paper-3);
    border-radius: 7px;
    padding: 5px 12px;
    font-family: 'DM Sans', sans-serif;
    font-size: 12.5px;
    color: var(--ink-3);
    cursor: pointer;
    text-align: left;
    transition: all .12s;
    width: 100%;
  }

  .sm-btn-addopt:hover {
    border-color: var(--gold);
    color: var(--gold);
  }

  .sm-qdel {
    background: none;
    border: none;
    font-size: 22px;
    line-height: 1;
    color: var(--ink-3);
    cursor: pointer;
    padding: 0;
    margin-top: 2px;
    transition: color .15s;
    flex-shrink: 0;
  }

  .sm-qdel:hover {
    color: var(--rose);
  }

  .sm-btn-addq {
    display: flex;
    align-items: center;
    gap: 7px;
    width: 100%;
    padding: 10px 14px;
    background: none;
    border: 1.5px dashed var(--paper-3);
    border-radius: var(--radius);
    font-family: 'DM Sans', sans-serif;
    font-size: 13.5px;
    color: var(--ink-3);
    cursor: pointer;
    transition: all .15s;
  }

  .sm-btn-addq:hover {
    border-color: var(--gold);
    color: var(--gold);
    background: var(--gold-light);
  }

  .sm-btn-addq svg {
    width: 14px;
    height: 14px;
    stroke: currentColor;
    fill: none;
    stroke-width: 2;
  }

  /* Review */
  .sm-review-card {
    background: var(--paper-2);
    border: 1.5px solid var(--paper-3);
    border-radius: var(--radius);
    padding: 1rem;
    margin-bottom: 10px;
  }

  .sm-review-card strong {
    display: block;
    font-size: 15px;
    margin-bottom: 4px;
    color: var(--ink);
  }

  .sm-review-card p {
    font-size: 13px;
    color: var(--ink-2);
  }

  .sm-review-qlabel {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: var(--ink-3);
    margin: 12px 0 6px;
  }

  .sm-review-qcard {
    background: var(--paper-2);
    border: 1.5px solid var(--paper-3);
    border-radius: var(--radius);
    padding: .8rem 1rem;
    margin-bottom: 6px;
    display: flex;
    gap: 10px;
    align-items: flex-start;
  }

  .sm-review-qnum {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: var(--gold-light);
    color: var(--gold-dark);
    font-size: 10px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    margin-top: 1px;
  }

  .sm-review-qbody b {
    font-size: 13.5px;
    color: var(--ink);
    display: block;
  }

  .sm-review-qbody small {
    font-size: 11.5px;
    color: var(--ink-3);
  }

  /* Footer / Actions */
  .sm-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
    margin-top: 1.75rem;
    padding-top: 1.25rem;
    border-top: 1.5px solid var(--paper-2);
    flex-wrap: wrap;
  }

  /* Buttons */
  .sm-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    border-radius: var(--radius);
    padding: 9px 18px;
    font-family: 'DM Sans', sans-serif;
    font-size: 13.5px;
    font-weight: 500;
    cursor: pointer;
    transition: background .15s, transform .1s;
    white-space: nowrap;
    border: none;
  }

  .sm-btn:active {
    transform: scale(.98);
  }

  .sm-btn svg {
    width: 14px;
    height: 14px;
    stroke: currentColor;
    fill: none;
    stroke-width: 2;
  }

  .sm-btn--ghost {
    background: none;
    border: 1.5px solid var(--paper-3);
    color: var(--ink-2);
  }

  .sm-btn--ghost:hover {
    border-color: var(--ink-2);
    color: var(--ink);
  }

  .sm-btn--gold {
    background: var(--gold);
    color: #fff;
    border: 1.5px solid transparent;
  }

  .sm-btn--gold:hover {
    background: var(--gold-dark);
  }

  .sm-btn--launch {
    background: var(--teal);
    color: #fff;
    border: 1.5px solid transparent;
  }

  .sm-btn--launch:hover {
    background: #0f4a4a;
  }

  .sm-btn--launch:disabled {
    opacity: .6;
    cursor: not-allowed;
  }

  /* Toast */
  .sm-toast {
    position: fixed;
    bottom: 2rem;
    right: 2rem;
    z-index: 999999;
    background: var(--ink);
    color: var(--paper);
    border-radius: 10px;
    padding: 12px 20px;
    font-family: 'DM Sans', sans-serif;
    font-size: 13.5px;
    font-weight: 500;
    box-shadow: 0 8px 32px rgba(15, 14, 13, .28);
    display: flex;
    align-items: center;
    gap: 9px;
    pointer-events: none;
    transform: translateY(80px);
    opacity: 0;
    transition: transform .3s cubic-bezier(.34, 1.56, .64, 1), opacity .3s ease;
  }

  .sm-toast svg {
    width: 16px;
    height: 16px;
    stroke: currentColor;
    fill: none;
    stroke-width: 2;
    flex-shrink: 0;
  }

  .sm-toast--show {
    transform: translateY(0);
    opacity: 1;
  }

  .sm-toast--success {
    background: var(--teal);
  }

  .sm-toast--error {
    background: var(--rose);
  }

  /* ── Responsive ── */
@media (max-width: 900px) {
    .main-wrapper {
      padding: 1.5rem 1.25rem 3rem;
    }
    .stat-strip {
      grid-template-columns: repeat(2, 1fr);
    }
  }

  @media (max-width: 600px) {
    .sm-row { grid-template-columns: 1fr; }
    .sm-steps { padding: 1rem 1rem 0; }
    .sm-panel { padding: 1.25rem 1rem 1.5rem; }
    .sm-step__label { display: none; }
    .sm-box { max-height: 95vh; }
    .sm-toast { bottom: 1rem; right: 1rem; left: 1rem; }
    .stat-strip { grid-template-columns: 1fr 1fr; }
    header { flex-direction: column; align-items: flex-start; }
    .toolbar { flex-direction: column; align-items: stretch; }
    .search-wrap, .search-wrap input, .filter-select, .btn-gold { width: 100%; }
  }
</style>

<!-- ══════════════════════════════════════════════════
     PAGE CONTENT
     ══════════════════════════════════════════════════ -->
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
        <svg viewBox="0 0 24 24">
          <circle cx="11" cy="11" r="8" />
          <line x1="21" y1="21" x2="16.65" y2="16.65" />
        </svg>
        <input type="text" id="surveySearch" placeholder="Search surveys…" oninput="filterSurveys()">
      </div>
      <select class="filter-select" id="statusFilter" onchange="filterSurveys()">
        <option value="">All Statuses</option>
        <option value="published">Published</option>
        <option value="pending">Pending</option>
        <option value="closed">Closed</option>
      </select>
      <!-- ▼ calls smOpen() — the fixed modal function -->
      <button onclick="smOpen()" class="btn-gold">
        <svg viewBox="0 0 24 24">
          <line x1="12" y1="5" x2="12" y2="19" />
          <line x1="5" y1="12" x2="19" y2="12" />
        </svg>
        New Survey
      </button>
    </div>
  </header>

  <!-- Stat Strip -->
  <div class="stat-strip">
    <div class="stat-card gold">
      <div class="stat-label">Total Surveys</div>
      <div class="stat-value"><?= $statTotal ?></div>
      <div class="stat-sub">All time</div>
    </div>
    <div class="stat-card teal">
      <div class="stat-label">Published</div>
      <div class="stat-value"><?= $statPublished ?></div>
      <div class="stat-sub">Currently running</div>
    </div>
    <div class="stat-card gold-acc">
      <div class="stat-label">Pending</div>
      <div class="stat-value"><?= $statPending ?></div>
      <div class="stat-sub">Awaiting launch</div>
    </div>
    <div class="stat-card rose">
      <div class="stat-label">Closed</div>
      <div class="stat-value"><?= $statClosed ?></div>
      <div class="stat-sub">Completed</div>
    </div>
  </div>

  <!-- Table -->
  <section>
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
              $responses  = (int)$s['response_count'];
              $pct        = $totalStudents > 0 ? round(($responses / $totalStudents) * 100) : 0;
              $dateLabel  = date('M d, Y', strtotime($s['created_at']));
            ?>
              <tr data-status="<?= htmlspecialchars($s['status']) ?>" data-id="<?= (int)$s['id'] ?>">
                <td>
                  <div class="survey-cell">
                    <span class="survey-title"><?= htmlspecialchars($s['title']) ?></span>
                    <span class="survey-desc"><?= htmlspecialchars($s['description']) ?></span>
                  </div>
                </td>
                <td style="font-weight:600;color:var(--ink);"><?= number_format($responses) ?> / <?= $totalStudents ?></td>
                <td>
                  <div class="prog-wrap">
                    <div class="prog-track">
                      <div class="prog-fill" style="width:<?= $pct ?>%;"></div>
                    </div>
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
                    <a href="survey_questions.php?id=<?= (int)$s['id'] ?>&action=view" class="action-btn">
                      <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" fill="none" stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                        <circle cx="12" cy="12" r="3" />
                      </svg>
                      View
                    </a>
                    <a href="survey_questions.php?id=<?= (int)$s['id'] ?>&action=edit" class="action-btn">
                      <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" fill="none" stroke-width="2">
                        <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" />
                        <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                      </svg>
                      Edit
                    </a>
                    <?php if ($s['status'] === 'pending' || $s['status'] === 'closed'): ?>
                    <button class="action-btn" onclick="changeSurveyStatus(<?= (int)$s['id'] ?>, 'publish')">
                      <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" fill="none" stroke-width="2">
                        <polyline points="22 2 15 22 11 13 2 9 22 2"/>
                      </svg>
                      Publish
                    </button>
                    <?php elseif ($s['status'] === 'published'): ?>
                    <button class="action-btn danger" onclick="changeSurveyStatus(<?= (int)$s['id'] ?>, 'close')">
                      Close
                    </button>
                    <?php endif; ?>
                    <button class="action-btn danger" onclick="confirmDelete(this, <?= (int)$s['id'] ?>)">
                      <svg viewBox="0 0 24 24" width="13" height="13" stroke="currentColor" fill="none" stroke-width="2">
                        <polyline points="3 6 5 6 21 6" />
                        <path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6" />
                        <path d="M10 11v6M14 11v6" />
                      </svg>
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
        <svg viewBox="0 0 24 24" style="width:32px;height:32px;stroke:var(--ink-3);fill:none;stroke-width:1.5;margin:0 auto 12px;display:block;">
          <rect x="3" y="3" width="18" height="18" rx="2" />
          <path d="M3 9h18M9 21V9" />
        </svg>
        <p style="color:var(--ink-3);font-size:14px;">No surveys match your filters.</p>
      </div>

      <div class="pagination">
        <span id="pagInfo"></span>
        <div class="page-btns" id="pageButtons"></div>
      </div>
    </div>
  </section>

</div><!-- /.main-wrapper -->


<!-- ══════════════════════════════════════════════════
     MODAL HTML
     NOTE: This sits anywhere in the DOM — the JS
     teleport below moves it to <body> at runtime,
     which is the critical fix for position:fixed.
     ══════════════════════════════════════════════════ -->
<div id="surveyModal" class="sm-backdrop" role="dialog" aria-modal="true" aria-labelledby="smModalTitle">
  <div class="sm-box">

    <!-- Step indicator -->
    <div class="sm-steps">
      <div class="sm-step sm-step--active" id="smDot1">
        <div class="sm-step__dot">1</div>
        <span class="sm-step__label">Details</span>
      </div>
      <div class="sm-step__line" id="smLine1"></div>
      <div class="sm-step" id="smDot2">
        <div class="sm-step__dot">2</div>
        <span class="sm-step__label">Questions</span>
      </div>
      <div class="sm-step__line" id="smLine2"></div>
      <div class="sm-step" id="smDot3">
        <div class="sm-step__dot">3</div>
        <span class="sm-step__label">Launch</span>
      </div>
    </div>

    <!-- ── Step 1: Details ── -->
    <div class="sm-panel sm-panel--active" id="smStep1">
      <div class="sm-panel__head">
        <h2 id="smModalTitle">Survey <em>Details</em></h2>
        <button class="sm-close" onclick="smClose()" aria-label="Close modal">×</button>
      </div>
      <div class="sm-form">
        <div class="sm-field">
          <label>Survey Title <span class="req">*</span></label>
          <input type="text" id="smTitle" placeholder="e.g. End-of-Term Faculty Evaluation" autocomplete="off">
        </div>
        <div class="sm-field">
          <label>Description</label>
          <textarea id="smDesc" placeholder="Brief description of this survey…" rows="3"></textarea>
        </div>
        <div class="sm-row">
          <div class="sm-field">
            <label>Publish Status</label>
            <select id="smStatus">
              <option value="published">Published — live immediately</option>
              <option value="pending">Pending — save for later</option>
            </select>
          </div>
        </div>
<div class="sm-row">
          <div class="sm-field">
            <label>Start Date</label>
            <input type="date" id="smStart">
          </div>
          <div class="sm-field">
            <label>Start Time</label>
            <div style="display:flex;gap:6px;">
              <select id="smStartHour" style="flex:1;background:var(--paper-2);border:1.5px solid var(--paper-3);border-radius:var(--radius);padding:9px 8px;font-family:'DM Sans',sans-serif;font-size:13.5px;color:var(--ink);outline:none;">
                <?php for($h=1;$h<=12;$h++) echo "<option value='".str_pad($h,2,'0',STR_PAD_LEFT)."'>".str_pad($h,2,'0',STR_PAD_LEFT)."</option>"; ?>
              </select>
              <select id="smStartMin" style="flex:1;background:var(--paper-2);border:1.5px solid var(--paper-3);border-radius:var(--radius);padding:9px 8px;font-family:'DM Sans',sans-serif;font-size:13.5px;color:var(--ink);outline:none;">
                <option value="00">00</option>
                <option value="15">15</option>
                <option value="30">30</option>
                <option value="45">45</option>
              </select>
              <select id="smStartAmPm" style="flex:1;background:var(--paper-2);border:1.5px solid var(--paper-3);border-radius:var(--radius);padding:9px 8px;font-family:'DM Sans',sans-serif;font-size:13.5px;color:var(--ink);outline:none;">
                <option value="AM">AM</option>
                <option value="PM">PM</option>
              </select>
            </div>
          </div>
        </div>
        <div class="sm-row">
          <div class="sm-field">
            <label>End Date <span class="req">*</span></label>
            <input type="date" id="smEnd">
          </div>
          <div class="sm-field">
            <label>End Time <span class="req">*</span></label>
            <div style="display:flex;gap:6px;">
              <select id="smEndHour" style="flex:1;background:var(--paper-2);border:1.5px solid var(--paper-3);border-radius:var(--radius);padding:9px 8px;font-family:'DM Sans',sans-serif;font-size:13.5px;color:var(--ink);outline:none;">
                <?php for($h=1;$h<=12;$h++) echo "<option value='".str_pad($h,2,'0',STR_PAD_LEFT)."'>".str_pad($h,2,'0',STR_PAD_LEFT)."</option>"; ?>
              </select>
              <select id="smEndMin" style="flex:1;background:var(--paper-2);border:1.5px solid var(--paper-3);border-radius:var(--radius);padding:9px 8px;font-family:'DM Sans',sans-serif;font-size:13.5px;color:var(--ink);outline:none;">
                <option value="00">00</option>
                <option value="15">15</option>
                <option value="30">30</option>
                <option value="45">45</option>
              </select>
              <select id="smEndAmPm" style="flex:1;background:var(--paper-2);border:1.5px solid var(--paper-3);border-radius:var(--radius);padding:9px 8px;font-family:'DM Sans',sans-serif;font-size:13.5px;color:var(--ink);outline:none;">
                <option value="AM">AM</option>
                <option value="PM" selected>PM</option>
              </select>
            </div>
          </div>
        </div>
      </div>
      <div class="sm-error" id="smErr1"></div>
      <div class="sm-footer">
        <button class="sm-btn sm-btn--ghost" onclick="smClose()">Cancel</button>
        <button class="sm-btn sm-btn--gold" onclick="smGoStep2()">
          Next: Add Questions
          <svg viewBox="0 0 24 24">
            <polyline points="9 18 15 12 9 6" />
          </svg>
        </button>
      </div>
    </div>

    <!-- ── Step 2: Questions ── -->
    <div class="sm-panel" id="smStep2">
      <div class="sm-panel__head">
        <h2>Add <em>Questions</em></h2>
        <button class="sm-close" onclick="smClose()" aria-label="Close modal">×</button>
      </div>
      <div id="smQList" class="sm-qlist"></div>
      <button class="sm-btn-addq" onclick="smAddQuestion()">
        <svg viewBox="0 0 24 24">
          <line x1="12" y1="5" x2="12" y2="19" />
          <line x1="5" y1="12" x2="19" y2="12" />
        </svg>
        Add Question
      </button>
      <div class="sm-error" id="smErr2"></div>
      <div class="sm-footer">
        <button class="sm-btn sm-btn--ghost" onclick="smSetStep(1)">
          <svg viewBox="0 0 24 24">
            <polyline points="15 18 9 12 15 6" />
          </svg>
          Back
        </button>
        <button class="sm-btn sm-btn--gold" onclick="smGoStep3()">
          Review &amp; Launch
          <svg viewBox="0 0 24 24">
            <polyline points="9 18 15 12 9 6" />
          </svg>
        </button>
      </div>
    </div>

    <!-- ── Step 3: Review & Launch ── -->
    <div class="sm-panel" id="smStep3">
      <div class="sm-panel__head">
        <h2>Review &amp; <em>Launch</em></h2>
        <button class="sm-close" onclick="smClose()" aria-label="Close modal">×</button>
      </div>
      <div id="smReview"></div>
      <div class="sm-footer">
        <button class="sm-btn sm-btn--ghost" onclick="smSetStep(2)">
          <svg viewBox="0 0 24 24">
            <polyline points="15 18 9 12 15 6" />
          </svg>
          Back
        </button>
        <button class="sm-btn sm-btn--launch" id="smLaunchBtn" onclick="smLaunch()">
          <svg viewBox="0 0 24 24">
            <polyline points="22 2 15 22 11 13 2 9 22 2" />
          </svg>
          Publish Survey
        </button>
      </div>
    </div>

  </div><!-- /.sm-box -->
</div><!-- /#surveyModal -->

<!-- Toast -->
<div id="smToast" class="sm-toast" role="alert" aria-live="polite">
  <svg viewBox="0 0 24 24">
    <polyline points="20 6 9 17 4 12" />
  </svg>
  <span id="smToastMsg">Done!</span>
</div>


<!-- ══════════════════════════════════════════════════
     JAVASCRIPT — ALL-IN-ONE
     ══════════════════════════════════════════════════ -->
<script>
  (function() {
      'use strict';

      /* ────────────────────────────────────────────────
         1. TELEPORT  ← THE PRIMARY BUG FIX
         Move #surveyModal and #smToast to be direct
         children of <body>. AdminLTE / NiceAdmin apply
         CSS transform to wrapper divs; any transformed
         ancestor breaks position:fixed by creating a
         new stacking context. On <body> (which has no
         transform) position:fixed targets the viewport.
      ──────────────────────────────────────────────── */
      function teleport() {
        ['surveyModal', 'smToast'].forEach(function(id) {
          var el = document.getElementById(id);
          if (el && el.parentNode !== document.body) {
            document.body.appendChild(el);
          }
        });
      }
      /* Run early, on DOMContentLoaded, and on load — belt-and-suspenders */
      teleport();
      document.addEventListener('DOMContentLoaded', function() {
    teleport();
    visibleRows = getAllRows();
    renderPage();
});
window.addEventListener('load', teleport);


      /* ────────────────────────────────────────────────
         2. MODAL STATE
      ──────────────────────────────────────────────── */
      var smQCount = 0;
      var smToastTimer = null;


      /* ────────────────────────────────────────────────
         3. OPEN / CLOSE
      ──────────────────────────────────────────────── */
      window.smOpen = function() {
        teleport(); /* ensure placement even if called before DOMContentLoaded */

        var modal = document.getElementById('surveyModal');
        modal.classList.add('sm-open');
        document.body.classList.add('modal-open');

        /* Reset */
        document.getElementById('smTitle').value = '';
        document.getElementById('smDesc').value = '';
        document.getElementById('smStatus').value = 'published';
        document.getElementById('smStart').value = '';
        document.getElementById('smEnd').value = '';
        document.getElementById('smQList').innerHTML = '';
        document.getElementById('smErr1').classList.remove('sm-error--show');
        document.getElementById('smErr2').classList.remove('sm-error--show');

        smQCount = 0;
        smSetStep(1);
        smAddQuestion(); /* start with one blank question */

        setTimeout(function() {
          var t = document.getElementById('smTitle');
          if (t) t.focus();
        }, 60);
      };

      window.smClose = function() {
    var prev = window.onbeforeunload;
    window.onbeforeunload = null;
    document.getElementById('surveyModal').classList.remove('sm-open');
    document.body.classList.remove('modal-open');
    window.onbeforeunload = prev;
};

      /* Backdrop click — close only when clicking the dark overlay, not the box */
      document.addEventListener('click', function(e) {
        var modal = document.getElementById('surveyModal');
        if (modal && e.target === modal) smClose();
      });

      /* Escape key */
      document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') smClose();
      });


      /* ────────────────────────────────────────────────
         4. STEP NAVIGATION
      ──────────────────────────────────────────────── */
      window.smSetStep = function(step) {
        /* Panels */
        for (var i = 1; i <= 3; i++) {
          var p = document.getElementById('smStep' + i);
          if (p) p.classList.toggle('sm-panel--active', i === step);
        }
        /* Dots */
        for (var j = 1; j <= 3; j++) {
          var d = document.getElementById('smDot' + j);
          if (!d) continue;
          d.classList.remove('sm-step--active', 'sm-step--done');
          if (j === step) d.classList.add('sm-step--active');
          if (j < step) d.classList.add('sm-step--done');
        }
        /* Lines */
        var l1 = document.getElementById('smLine1');
        var l2 = document.getElementById('smLine2');
        if (l1) l1.classList.toggle('sm-step__line--done', step > 1);
        if (l2) l2.classList.toggle('sm-step__line--done', step > 2);

        /* Scroll box to top */
        var box = document.querySelector('#surveyModal .sm-box');
        if (box) box.scrollTop = 0;
      };

window.smGoStep2 = function() {
        var err = document.getElementById('smErr1');
        err.classList.remove('sm-error--show');
        if (!document.getElementById('smTitle').value.trim()) {
          err.textContent = 'Please enter a survey title.';
          err.classList.add('sm-error--show');
          document.getElementById('smTitle').focus();
          return;
        }
        if (!document.getElementById('smEnd').value) {
          err.textContent = 'Please set an end date for the survey.';
          err.classList.add('sm-error--show');
          document.getElementById('smEnd').focus();
          return;
        }
        if (document.getElementById('smStart').value && document.getElementById('smEnd').value < document.getElementById('smStart').value) {
          err.textContent = 'End date cannot be before start date.';
          err.classList.add('sm-error--show');
          document.getElementById('smEnd').focus();
          return;
        }
        smSetStep(2);
      };

      window.smGoStep3 = function() {
        var err = document.getElementById('smErr2');
        err.classList.remove('sm-error--show');
        if (document.querySelectorAll('.sm-qcard').length === 0) {
          err.textContent = 'Add at least one question before continuing.';
          err.classList.add('sm-error--show');
          return;
        }
        smBuildReview();
        smSetStep(3);
      };


      /* ────────────────────────────────────────────────
         5. QUESTIONS
      ──────────────────────────────────────────────── */
      window.smAddQuestion = function() {
        smQCount++;
        var n = smQCount;
        var card = document.createElement('div');
        card.className = 'sm-qcard';
        card.dataset.qid = n;
        card.innerHTML =
  '<div class="sm-qrow">' +
  '<span class="sm-qnum">' + n + '</span>' +
  '<div class="sm-qbody">' +
  '<input type="text" class="sm-qtext" placeholder="Type your question here…" autocomplete="off">' +
  '<div style="display:flex;gap:10px;align-items:center;margin-top:4px;">' +
  '<select class="sm-qtype" onchange="smRenderOpts(this)">' +
  '<option value="text">Short Answer</option>' +
  '<option value="textarea">Long Answer</option>' +
  '<option value="radio">Multiple Choice (single)</option>' +
  '<option value="checkbox">Checkboxes (multiple)</option>' +
  '<option value="scale">Rating Scale 1–5</option>' +
  '</select>' +
  '<label style="display:flex;align-items:center;gap:5px;font-size:12.5px;color:var(--ink-2);cursor:pointer;white-space:nowrap;">' +
  '<input type="checkbox" class="sm-qrequired" checked style="accent-color:var(--teal);width:14px;height:14px;"> Required' +
  '</label>' +
  '</div>' +
  '<div class="sm-opts-wrap"></div>' +
  '</div>' +
  '<button type="button" class="sm-qdel" onclick="smDeleteQuestion(this)" aria-label="Delete question">×</button>' +
  '</div>';
        document.getElementById('smQList').appendChild(card);
        card.querySelector('.sm-qtext').focus();
      };

      window.smDeleteQuestion = function(btn) {
        btn.closest('.sm-qcard').remove();
        /* Re-number remaining */
        document.querySelectorAll('#smQList .sm-qcard').forEach(function(c, i) {
          var num = c.querySelector('.sm-qnum');
          if (num) num.textContent = i + 1;
        });
      };

      window.smRenderOpts = function(select) {
        var type = select.value;
        var wrap = select.closest('.sm-qbody').querySelector('.sm-opts-wrap');
        wrap.innerHTML = '';

        if (type === 'radio' || type === 'checkbox') {
          var div = document.createElement('div');
          div.className = 'sm-opts';
          div.innerHTML =
            '<div class="sm-opt-row"><input type="text" placeholder="Option 1"><button type="button" class="sm-opt-del" onclick="smDelOpt(this)">×</button></div>' +
            '<div class="sm-opt-row"><input type="text" placeholder="Option 2"><button type="button" class="sm-opt-del" onclick="smDelOpt(this)">×</button></div>' +
            '<button type="button" class="sm-btn-addopt" onclick="smAddOpt(this)">+ Add option</button>';
          wrap.appendChild(div);
        }

        if (type === 'scale') {
          wrap.innerHTML =
            '<div style="display:flex;gap:6px;margin-top:6px;">' + [1, 2, 3, 4, 5].map(function(n) {
              return '<div style="width:32px;height:32px;border-radius:8px;border:1.5px solid var(--paper-3);' +
                'display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:600;' +
                'background:#fff;color:var(--ink-3);">' + n + '</div>';
            }).join('') +
            '</div>' +
            '<p style="font-size:12px;color:var(--ink-3);margin-top:6px;">Respondents choose a value from 1 to 5.</p>';
        }
      };

      window.smAddOpt = function(btn) {
        var opts = btn.closest('.sm-opts');
        var count = opts.querySelectorAll('.sm-opt-row').length + 1;
        var row = document.createElement('div');
        row.className = 'sm-opt-row';
        row.innerHTML = '<input type="text" placeholder="Option ' + count + '"><button type="button" class="sm-opt-del" onclick="smDelOpt(this)">×</button>';
        opts.insertBefore(row, btn);
      };

      window.smDelOpt = function(btn) {
        var opts = btn.closest('.sm-opts');
        if (opts.querySelectorAll('.sm-opt-row').length > 1) {
          btn.closest('.sm-opt-row').remove();
        }
      };


      /* ────────────────────────────────────────────────
         6. REVIEW BUILD
      ──────────────────────────────────────────────── */
      function smBuildReview() {
        var title = document.getElementById('smTitle').value;
        var desc = document.getElementById('smDesc').value;
        var status = document.getElementById('smStatus').value;
        var start = document.getElementById('smStart').value;
        var end = document.getElementById('smEnd').value;

        var meta = [(status.charAt(0).toUpperCase() + status.slice(1))];
        if (start) meta.push('From ' + start);
        if (end) meta.push('To ' + end);

        var html =
          '<div class="sm-review-card">' +
          '<strong>' + esc(title) + '</strong>' +
          '<p>' + esc(desc || '—') + '</p>' +
          '<p style="font-size:12px;color:var(--ink-3);margin-top:6px;">' + meta.join(' · ') + '</p>' +
          '</div>' +
          '<div class="sm-review-qlabel">Questions (' + document.querySelectorAll('.sm-qcard').length + ')</div>';

        document.querySelectorAll('.sm-qcard').forEach(function(card, i) {
          var text = card.querySelector('.sm-qtext').value || '(untitled)';
          var typeEl = card.querySelector('.sm-qtype');
          var typeName = typeEl ? typeEl.options[typeEl.selectedIndex].text : '';
          html +=
            '<div class="sm-review-qcard">' +
            '<div class="sm-review-qnum">' + (i + 1) + '</div>' +
            '<div class="sm-review-qbody">' +
            '<b>' + esc(text) + '</b>' +
'<small>' + esc(typeName) + ' · ' + (card.querySelector('.sm-qrequired') && card.querySelector('.sm-qrequired').checked ? '✱ Required' : 'Optional') + '</small>' +            '</div>' +
            '</div>';
        });

        document.getElementById('smReview').innerHTML = html;
      }

      function esc(str) {
        return String(str)
          .replace(/&/g, '&amp;').replace(/</g, '&lt;')
          .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
      }


      /* ────────────────────────────────────────────────
         7. SUBMIT / LAUNCH
      ──────────────────────────────────────────────── */
      window.smLaunch = function() {
        var title = document.getElementById('smTitle').value.trim();
        var desc = document.getElementById('smDesc').value.trim();
        var status = document.getElementById('smStatus').value;

        if (!title) {
          smToastMsg('Title is required.', false);
          smSetStep(1);
          return;
        }

        var questions = [];
        document.querySelectorAll('.sm-qcard').forEach(function(card) {
          var text = card.querySelector('.sm-qtext').value.trim();
          var typeEl = card.querySelector('.sm-qtype');
          var type = typeEl ? typeEl.value : 'text';
          if (!text) return;
          var opts = [];
          card.querySelectorAll('.sm-opt-row input').forEach(function(inp) {
            if (inp.value.trim()) opts.push(inp.value.trim());
          });
          questions.push({
  text: text,
  type: type,
  opts: opts,
  required: card.querySelector('.sm-qrequired') ? card.querySelector('.sm-qrequired').checked : true  // ← add this
});
        });

        if (questions.length === 0) {
          smToastMsg('Add at least one question.', false);
          smSetStep(2);
          return;
        }

        var btn = document.getElementById('smLaunchBtn');
        btn.disabled = true;
        btn.textContent = 'Saving…';

        var fd = new FormData();
        fd.append('action', 'create');
        fd.append('title', title);
        fd.append('description', desc);
        fd.append('status', status);
function to24(h, m, ap) {
          var hour = parseInt(h);
          if (ap === 'AM' && hour === 12) hour = 0;
          if (ap === 'PM' && hour !== 12) hour += 12;
          return String(hour).padStart(2,'0') + ':' + m + ':00';
        }
        var startTime = to24(document.getElementById('smStartHour').value, document.getElementById('smStartMin').value, document.getElementById('smStartAmPm').value);
        var endTime   = to24(document.getElementById('smEndHour').value,   document.getElementById('smEndMin').value,   document.getElementById('smEndAmPm').value);
        fd.append('start_date', document.getElementById('smStart').value + ' ' + startTime);
        fd.append('end_date',   document.getElementById('smEnd').value   + ' ' + endTime);
        fd.append('questions', JSON.stringify(questions));

fetch('/surveysystem/app/controllers/surveyController.php', {    method: 'POST',
    body: fd
})
.then(function(res) {
    return res.text().then(function(txt) {
        console.log('RAW RESPONSE:', txt);
        try {
            return JSON.parse(txt);
        } catch (e) {
            throw new Error('Invalid JSON: ' + txt.slice(0, 200));
        }
    });
})
.then(function(data) {
    if (!data.success) throw new Error(data.message || 'Server returned failure.');
    window.onbeforeunload = null;
    smClose();
    location.reload();
})
.catch(function(err) {
    console.error(err);
    smToastMsg(err.message || 'Server error. See console.', false);
});
  };


  /* ────────────────────────────────────────────────
     8. TOAST
  ──────────────────────────────────────────────── */
  window.smToastMsg = function(msg, success) {
    var t = document.getElementById('smToast');
    document.getElementById('smToastMsg').textContent = msg;
    t.className = 'sm-toast sm-toast--show ' + (success ? 'sm-toast--success' : 'sm-toast--error');
    clearTimeout(smToastTimer);
    smToastTimer = setTimeout(function() {
      t.classList.remove('sm-toast--show');
    }, 3500);
  };


  /* ────────────────────────────────────────────────
     9. DELETE CONFIRMATION
  ──────────────────────────────────────────────── */
 window.confirmDelete = function(btn, surveyId) {
    var row = btn.closest('tr');
    var existing = row.querySelector('.delete-confirm-inline');
    if (existing) { existing.remove(); return; }

    var bar = document.createElement('div');
    bar.className = 'delete-confirm-inline';
    bar.style.cssText = 'display:flex;align-items:center;gap:8px;padding:8px 18px;background:var(--rose-lt);border-top:1px solid var(--paper-2);';
    bar.innerHTML =
        '<span style="font-size:13px;color:var(--rose);flex:1;">Delete this survey? This cannot be undone.</span>' +
        '<button style="background:var(--rose);color:#fff;border:none;border-radius:7px;padding:5px 14px;font-size:12.5px;font-family:DM Sans,sans-serif;cursor:pointer;" onclick="doDelete(' + surveyId + ',this)">Yes, delete</button>' +
        '<button style="background:none;border:1.5px solid var(--paper-3);border-radius:7px;padding:5px 14px;font-size:12.5px;font-family:DM Sans,sans-serif;cursor:pointer;color:var(--ink-2);" onclick="this.closest(\'.delete-confirm-inline\').remove()">Cancel</button>';

    var td = document.createElement('td');
    td.colSpan = 6;
    td.style.padding = '0';
    td.appendChild(bar);

    var confirmRow = document.createElement('tr');
    confirmRow.appendChild(td);
    row.after(confirmRow);
    bar._confirmRow = confirmRow;
};

window.doDelete = function(surveyId, btn) {
    var confirmRow = btn.closest('.delete-confirm-inline')._confirmRow;
    btn.disabled = true;
    btn.textContent = 'Deleting…';

    var fd = new FormData();
    fd.append('action', 'delete');
    fd.append('id', surveyId);

    fetch('/surveysystem/app/controllers/surveyController.php', {
        method: 'POST',
        body: fd
    }).then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            var surveyRow = confirmRow.previousElementSibling;
            if (surveyRow) surveyRow.remove();
            if (confirmRow) confirmRow.remove();
            filterSurveys();
        } else {
            smToastMsg(data.message || 'Delete failed.', false);
            if (confirmRow) confirmRow.remove();
        }
    })
    .catch(function() {
        smToastMsg('Server error.', false);
        if (confirmRow) confirmRow.remove();
    });

  };


  /* ────────────────────────────────────────────────
     10. TABLE — FILTER + PAGINATION
  ──────────────────────────────────────────────── */
  var ROWS_PER_PAGE = 10;
  var currentPage = 1;
  var visibleRows = [];

  function getAllRows() {
    return Array.from(document.querySelectorAll('#surveyTable tr'));
  }

  window.filterSurveys = function() {
    var q = document.getElementById('surveySearch').value.toLowerCase();
    var status = document.getElementById('statusFilter').value;

    visibleRows = getAllRows().filter(function(row) {
      var title = (row.querySelector('.survey-title') || {}).textContent || '';
      var rstatus = row.dataset.status || '';
      return (!q || title.toLowerCase().includes(q)) &&
        (!status || rstatus === status);
    });

    currentPage = 1;
    renderPage();
  };

  function renderPage() {
    var all = getAllRows();
    var total = visibleRows.length;
    var start = (currentPage - 1) * ROWS_PER_PAGE;
    var end = start + ROWS_PER_PAGE;

    all.forEach(function(r) {
      r.style.display = 'none';
    });
    visibleRows.slice(start, end).forEach(function(r) {
      r.style.display = '';
    });

    var empty = document.getElementById('emptyState');
    if (empty) empty.style.display = total === 0 ? 'block' : 'none';

    var info = document.getElementById('pagInfo');
    if (info) {
      info.textContent = total === 0 ?
        'No surveys found' :
        'Showing ' + (start + 1) + '–' + Math.min(end, total) + ' of ' + total;
    }
    renderPageButtons(total);
  }

  function renderPageButtons(total) {
    var totalPages = Math.ceil(total / ROWS_PER_PAGE) || 1;
    var container = document.getElementById('pageButtons');
    if (!container) return;
    container.innerHTML = '';

    /* Prev */
    var prev = document.createElement('button');
    prev.className = 'page-btn';
    prev.innerHTML = '‹';
    prev.disabled = currentPage === 1;
    prev.onclick = function() {
      if (currentPage > 1) {
        currentPage--;
        renderPage();
      }
    };
    container.appendChild(prev);

    /* Pages */
    for (var i = 1; i <= totalPages; i++) {
      (function(page) {
        var btn = document.createElement('button');
        btn.className = 'page-btn' + (page === currentPage ? ' active' : '');
        btn.textContent = page;
        btn.onclick = function() {
          currentPage = page;
          renderPage();
        };
        container.appendChild(btn);
      })(i);
    }

    /* Next */
    var next = document.createElement('button');
    next.className = 'page-btn';
    next.innerHTML = '›';
    next.disabled = currentPage >= totalPages;
    next.onclick = function() {
      if (currentPage < totalPages) {
        currentPage++;
        renderPage();
      }
    };
    container.appendChild(next);
  }

  /* Boot the table on load */
  document.addEventListener('DOMContentLoaded', function() {
  visibleRows = getAllRows();
  renderPage();
  });
window.changeSurveyStatus = function(surveyId, action) {
    var fd = new FormData();
    fd.append('action', action);
    fd.append('id', surveyId);

    fetch('/surveysystem/app/controllers/surveyController.php', {
      method: 'POST',
      body: fd
    }).then(function(r) { return r.json(); })
    .then(function(data) {
      if (data.success) {
        smToastMsg(action === 'publish' ? 'Survey published!' : 'Survey closed!', true);
        setTimeout(function() { location.reload(); }, 1000);
      } else {
        smToastMsg(data.message || 'Action failed.', false);
      }
    }).catch(function() {
      smToastMsg('Server error.', false);
    });
  };
  })();
</script>

<?php include('./includes/footer.php'); ?>