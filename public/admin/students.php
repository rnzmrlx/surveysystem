<?php
include('../../app/middleware/admin.php');
include('./includes/header.php');
include('./includes/topbar.php');
include('./includes/sidebar.php');
include('../../app/config/config.php');

// Fetch only registered students (non-admin)
$sql    = "SELECT id, uuid, firstName, middleName, lastName, emailAddress, username 
           FROM users 
           WHERE role = 'user' 
           ORDER BY firstName ASC";
$result = mysqli_query($conn, $sql);

$students = [];
while ($row = mysqli_fetch_assoc($result)) {
  $students[] = $row;
}
$totalStudents = count($students);

// Get total published surveys count (for "X / Y" display)
$totalSurveysRes = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM surveys WHERE status = 'published'");
$totalSurveys    = mysqli_fetch_assoc($totalSurveysRes)['cnt'] ?? 0;

// Active = has at least one response; Inactive = none
$activeCount   = 0;
$inactiveCount = 0;
foreach ($students as $s) {
  $uid   = (int)$s['id'];
  $check = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM responses WHERE user_id = $uid");
  $row   = mysqli_fetch_assoc($check);
  if ($row['cnt'] > 0) $activeCount++;
  else $inactiveCount++;
}
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
    border-radius: 2px 2px 0 0;
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

  .table-wrap {
    background: #fff;
    border: 1.5px solid var(--paper-3);
    border-radius: var(--radius);
    overflow: hidden;
    box-shadow: var(--shadow);
  }

  table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
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

  .student-cell {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .avatar {
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

  .student-name {
    font-weight: 500;
    color: var(--ink);
    font-size: 14px;
  }

  .student-id {
    font-size: 12px;
    color: var(--ink-3);
    margin-top: 1px;
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
    font-size: 11.5px;
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

  .badge-active {
    background: var(--teal-lt);
    color: var(--teal);
  }

  .badge-active::before {
    background: var(--teal);
    animation: pulse 1.5s infinite;
  }

  .badge-inactive {
    background: var(--rose-lt);
    color: var(--rose);
  }

  .badge-inactive::before {
    background: var(--rose);
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

  .page-btn svg {
    width: 14px;
    height: 14px;
    stroke: currentColor;
    fill: none;
    stroke-width: 2;
  }

  .empty-state {
    text-align: center;
    padding: 3rem 1rem;
  }

  .empty-state p {
    color: var(--ink-3);
    font-size: 14px;
  }

  @media (max-width: 900px) {
    .main-wrapper {
      padding: 1.5rem 1.25rem 3rem;
    }

    .stat-strip {
      grid-template-columns: repeat(2, 1fr);
    }
  }
</style>

<div class="main-wrapper">

  <div class="breadcrumbs">
    <a href="index">Home</a>
    <span>/</span>
    <span class="current-page">Students</span>
  </div>

<header>
  <div>
    <h1>Registered <em>Students</em></h1>
    <p style="font-size:13.5px;color:var(--ink-3);margin-top:6px;">Browse and monitor all registered student accounts.</p>
  </div>
  <div class="toolbar">
      <div class="search-wrap">
        <svg viewBox="0 0 24 24">
          <circle cx="11" cy="11" r="8" />
          <line x1="21" y1="21" x2="16.65" y2="16.65" />
        </svg>
        <input type="text" id="studentSearch" placeholder="Search students…" oninput="filterStudents()">
      </div>
      <select class="filter-select" id="statusFilter" onchange="filterStudents()">
        <option value="">All Statuses</option>
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
      </select>
    </div>
  </header>

  <div class="stat-strip">
    <div class="stat-card gold">
      <div class="stat-label">Total Students</div>
      <div class="stat-value"><?= $totalStudents ?></div>
      <div class="stat-sub">Registered users</div>
    </div>
    <div class="stat-card teal">
      <div class="stat-label">Active</div>
      <div class="stat-value"><?= $activeCount ?></div>
      <div class="stat-sub">Answered at least one survey</div>
    </div>
    <div class="stat-card rose">
      <div class="stat-label">Inactive</div>
      <div class="stat-value"><?= $inactiveCount ?></div>
      <div class="stat-sub">No responses yet</div>
    </div>
  </div>

  <section style="width:100%;">
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Student</th>
            <th>Email</th>
            <th>Username</th>
            <th style="text-align:center;">Surveys Answered</th>
            <th>Completion</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody id="studentTable">
          <?php foreach ($students as $s):
            $fullName = trim(
              htmlspecialchars($s['firstName']) . ' ' .
                (!empty($s['middleName']) ? htmlspecialchars($s['middleName'][0]) . '. ' : '') .
                htmlspecialchars($s['lastName'])
            );
            $initials  = strtoupper(substr($s['firstName'], 0, 1) . substr($s['lastName'], 0, 1));
            $uid       = (int)$s['id'];

            // Count distinct surveys this student has responded to
            $surveyRes   = mysqli_query($conn, "SELECT COUNT(DISTINCT survey_id) as cnt FROM responses WHERE user_id = $uid");
            $surveyCount = (int)mysqli_fetch_assoc($surveyRes)['cnt'];

            $status = $surveyCount > 0 ? 'active' : 'inactive';
            $pct    = $totalSurveys > 0 ? round(($surveyCount / $totalSurveys) * 100) : 0;
          ?>
            <tr data-status="<?= $status ?>"
              data-name="<?= strtolower(strip_tags($fullName)) ?>"
              data-id="<?= strtolower(htmlspecialchars($s['uuid'])) ?>">
              <td>
                <div class="student-cell">
                  <div class="avatar"><?= $initials ?></div>
                  <div>
                    <div class="student-name"><?= $fullName ?></div>
                    <div class="student-id"><?= htmlspecialchars($s['uuid']) ?></div>
                  </div>
                </div>
              </td>
              <td style="font-size:13.5px;"><?= htmlspecialchars($s['emailAddress']) ?></td>
              <td style="font-size:13.5px;"><?= htmlspecialchars($s['username']) ?></td>
              <td style="font-weight:600; color:var(--ink); text-align:center;">
                <?= $surveyCount ?> / <?= $totalSurveys ?>
              </td>
              <td>
                <div class="prog-wrap">
                  <div class="prog-track">
                    <div class="prog-fill" style="width:<?= $pct ?>%;"></div>
                  </div>
                  <span class="prog-num"><?= $pct ?>%</span>
                </div>
              </td>
              <td>
                <?php if ($status === 'active'): ?>
                  <span class="badge badge-active">Active</span>
                <?php else: ?>
                  <span class="badge badge-inactive">Inactive</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <div id="emptyState" style="display:none;" class="empty-state">
        <p>No students match your filters.</p>
      </div>

      <div class="pagination">
        <span id="pagInfo"></span>
        <div class="page-btns" id="pageButtons"></div>
      </div>
    </div>
  </section>
</div>

<script>
  const ROWS_PER_PAGE = 10;
  let currentPage = 1;
  let visibleRows = [];

  function getAllRows() {
    return Array.from(document.querySelectorAll('#studentTable tr'));
  }

  function filterStudents() {
    const q = document.getElementById('studentSearch').value.toLowerCase();
    const status = document.getElementById('statusFilter').value;
    const rows = getAllRows();

    visibleRows = rows.filter(row => {
      const name = row.dataset.name || '';
      const id = row.dataset.id || '';
      const rstatus = row.dataset.status;
      const qOk = !q || name.includes(q) || id.includes(q);
      const statusOk = !status || rstatus === status;
      return qOk && statusOk;
    });

    currentPage = 1;
    renderPage();
  }

  function renderPage() {
    const rows = getAllRows();
    const total = visibleRows.length;
    const start = (currentPage - 1) * ROWS_PER_PAGE;
    const end = start + ROWS_PER_PAGE;

    rows.forEach(r => r.style.display = 'none');
    visibleRows.slice(start, end).forEach(r => r.style.display = '');

    document.getElementById('emptyState').style.display = total === 0 ? 'block' : 'none';
    document.getElementById('pagInfo').textContent = total === 0 ?
      'No students found' :
      `Showing ${start + 1}–${Math.min(end, total)} of ${total} students`;

    renderPageButtons(total);
  }

  function renderPageButtons(total) {
    const totalPages = Math.ceil(total / ROWS_PER_PAGE);
    const container = document.getElementById('pageButtons');
    container.innerHTML = '';

    const prev = makeBtn('', true);
    prev.innerHTML = `<svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>`;
    prev.disabled = currentPage === 1;
    prev.onclick = () => {
      if (currentPage > 1) {
        currentPage--;
        renderPage();
      }
    };
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
        btn.onclick = () => {
          currentPage = p;
          renderPage();
        };
        container.appendChild(btn);
      }
    });

    const next = makeBtn('', true);
    next.innerHTML = `<svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>`;
    next.disabled = currentPage === totalPages || totalPages === 0;
    next.onclick = () => {
      if (currentPage < totalPages) {
        currentPage++;
        renderPage();
      }
    };
    container.appendChild(next);
  }

  function makeBtn(label, isIcon) {
    const btn = document.createElement('button');
    btn.className = 'page-btn';
    if (!isIcon) btn.textContent = label;
    return btn;
  }

  function pageRange(current, total) {
    if (total <= 7) return Array.from({
      length: total
    }, (_, i) => i + 1);
    if (current <= 4) return [1, 2, 3, 4, 5, '...', total];
    if (current >= total - 3) return [1, '...', total - 4, total - 3, total - 2, total - 1, total];
    return [1, '...', current - 1, current, current + 1, '...', total];
  }

  document.addEventListener('DOMContentLoaded', () => {
    visibleRows = getAllRows();
    renderPage();
  });
</script>

<?php include('./includes/footer.php'); ?>