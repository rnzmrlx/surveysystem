<?php
// ── notifications.php ─────────────────────────────────────────────────────
// Location: surveysystem/public/admin/notifications.php
// ─────────────────────────────────────────────────────────────────────────────
session_start();
if (empty($_SESSION['authUser'])) {
    header('Location: ../login.php');
    exit;
}

include __DIR__ . '/../../app/config/config.php';
include __DIR__ . '/includes/header.php';
?>

<style>
  /* ── Page layout ── */
  .notif-page-wrapper {
    min-height: calc(100vh - 60px);
    padding: 32px 40px;
    background: var(--paper, #faf9f6);
  }

  /* ── Page title ── */
  .notif-page-title {
    font-family: 'DM Serif Display', serif;
    font-size: 26px;
    color: var(--ink, #1a1a1a);
    letter-spacing: -0.02em;
    margin-bottom: 2px;
  }
  .notif-breadcrumb {
    font-family: 'DM Sans', sans-serif;
    font-size: 12px;
    color: var(--ink-2, #888);
    margin-bottom: 28px;
  }
  .notif-breadcrumb a {
    color: var(--gold, #c9a84c);
    text-decoration: none;
  }
  .notif-breadcrumb a:hover { text-decoration: underline; }

  /* ── Card ── */
  .notif-card {
    background: #fff;
    border: 1.5px solid var(--paper-3, #e8e4dc);
    border-radius: var(--radius, 10px);
    box-shadow: var(--shadow, 0 2px 12px rgba(0,0,0,.06));
    overflow: hidden;
    width: 100%;
  }

  /* ── Card header ── */
  .notif-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 28px;
    border-bottom: 1.5px solid var(--paper-3, #e8e4dc);
    background: var(--paper, #faf9f6);
  }
  .notif-card-header h2 {
    font-family: 'DM Serif Display', serif;
    font-size: 18px;
    color: var(--ink, #1a1a1a);
    letter-spacing: -0.02em;
    margin: 0 0 2px 0;
  }
  .notif-card-header p {
    font-family: 'DM Sans', sans-serif;
    font-size: 12px;
    color: var(--ink-2, #888);
    margin: 0;
  }

  /* ── Mark all read button ── */
  .btn-mark-read {
    font-family: 'DM Sans', sans-serif;
    font-size: 12px;
    font-weight: 600;
    padding: 7px 18px;
    border-radius: 20px;
    border: 1.5px solid var(--gold, #c9a84c);
    background: transparent;
    color: var(--gold, #c9a84c);
    cursor: pointer;
    transition: all .2s ease;
    white-space: nowrap;
  }
  .btn-mark-read:hover {
    background: var(--gold, #c9a84c);
    color: #fff;
  }

  /* ── Filter tabs ── */
  .notif-filters {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    padding: 16px 28px;
    border-bottom: 1.5px solid var(--paper-3, #e8e4dc);
    background: #fff;
  }
  .filter-btn {
    font-family: 'DM Sans', sans-serif;
    font-size: 12px;
    font-weight: 600;
    padding: 5px 16px;
    border-radius: 20px;
    border: 1.5px solid var(--paper-3, #e8e4dc);
    background: transparent;
    color: var(--ink-2, #888);
    cursor: pointer;
    transition: all .2s ease;
  }
  .filter-btn:hover {
    border-color: var(--gold, #c9a84c);
    color: var(--gold, #c9a84c);
  }
  .filter-btn.active {
    background: var(--gold, #c9a84c);
    border-color: var(--gold, #c9a84c);
    color: #fff;
  }

  /* ── Notification rows ── */
  .notif-list {
    list-style: none;
    margin: 0;
    padding: 0;
  }
  .notif-row {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    padding: 16px 28px;
    border-bottom: 1.5px solid var(--paper-3, #e8e4dc);
    background: #fff;
    transition: background .15s ease;
  }
  .notif-row:last-child { border-bottom: none; }
  .notif-row:hover { background: var(--paper, #faf9f6); }
  .notif-row.unread {
    background: #fdfaf3;
    border-left: 3px solid var(--gold, #c9a84c);
    padding-left: 25px; /* compensate for border */
  }

  /* ── Icon bubble ── */
  .notif-icon-wrap {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    margin-top: 1px;
  }
  .notif-icon-wrap i { font-size: 16px; }

  /* ── Text ── */
  .notif-label {
    font-family: 'DM Sans', sans-serif;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: var(--ink-2, #888);
    margin-bottom: 3px;
  }
  .notif-row.unread .notif-label { color: var(--gold, #c9a84c); }
  .notif-msg {
    font-family: 'DM Sans', sans-serif;
    font-size: 13px;
    color: var(--ink, #1a1a1a);
    line-height: 1.5;
    word-break: break-word;
    font-weight: 400;
  }
  .notif-row.unread .notif-msg { font-weight: 500; }
  .notif-time {
    font-family: 'DM Sans', sans-serif;
    font-size: 11px;
    color: var(--ink-2, #aaa);
    margin-top: 4px;
  }

  /* ── New badge ── */
  .notif-badge {
    font-family: 'DM Sans', sans-serif;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.04em;
    background: var(--gold, #c9a84c);
    color: #fff;
    border-radius: 20px;
    padding: 2px 10px;
    align-self: center;
    flex-shrink: 0;
  }

  /* ── Loading / empty ── */
  .notif-state {
    text-align: center;
    padding: 60px 20px;
    font-family: 'DM Sans', sans-serif;
    font-size: 13px;
    color: var(--ink-2, #aaa);
  }
  .notif-state i {
    font-size: 2.5rem;
    color: var(--paper-3, #e8e4dc);
    display: block;
    margin-bottom: 12px;
  }
</style>

<main id="main" class="main">
  <div class="notif-page-wrapper">

    <!-- Page title -->
    <h1 class="notif-page-title">Notifications</h1>
    <div class="notif-breadcrumb">
      <a href="index.php">Home</a> &rsaquo; Notifications
    </div>

    <!-- Card -->
    <div class="notif-card">

      <!-- Header -->
      <div class="notif-card-header">
        <div>
          <h2 id="historyHeader">All Notifications</h2>
          <p id="historySubtitle">Loading…</p>
        </div>
        <button class="btn-mark-read" id="markAllReadPage">
          <i class="bi bi-check2-all me-1"></i> Mark all read
        </button>
      </div>

      <!-- Filters -->
      <div class="notif-filters" id="filterTabs">
        <button class="filter-btn active" data-type="all">All</button>
        <button class="filter-btn" data-type="answered">Survey Answered</button>
        <button class="filter-btn" data-type="accessed">Survey Accessed</button>
        <button class="filter-btn" data-type="auto_closed">Auto Closed</button>
        <button class="filter-btn" data-type="user_registered">New Users</button>
      </div>

      <!-- List -->
      <ul class="notif-list" id="historyList">
        <li class="notif-state">
          <div class="spinner-border spinner-border-sm me-2"
               style="color:var(--gold,#c9a84c);" role="status"></div>
          Loading notifications…
        </li>
      </ul>

      <!-- Empty state -->
      <div id="emptyState" class="notif-state d-none">
        <i class="bi bi-bell-slash"></i>
        No notifications found.
      </div>

    </div><!-- /.notif-card -->

  </div><!-- /.notif-page-wrapper -->
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

<script>
(function () {
  const ENDPOINT = '../../app/controllers/notificationController.php';

  const iconMap = {
    accessed:        { icon: 'bi-eye',          bg: '#c9a84c18', color: 'var(--gold,#c9a84c)' },
    answered:        { icon: 'bi-check2-circle', bg: '#2ecc7118', color: '#2ecc71' },
    auto_closed:     { icon: 'bi-clock-history', bg: '#e67e2218', color: '#e67e22' },
    user_registered: { icon: 'bi-person-plus',   bg: '#3498db18', color: '#3498db' },
  };
  const defaultIcon = { icon: 'bi-bell', bg: '#88888818', color: '#888' };

  function notifLabel(type) {
    switch (type) {
      case 'answered':        return 'Survey Answered';
      case 'accessed':        return 'Survey Accessed';
      case 'auto_closed':     return 'Survey Auto-Closed';
      case 'user_registered': return 'New User Registered';
      default:                return 'Notification';
    }
  }

  function timeAgo(dateStr) {
    const diff = Math.floor((Date.now() - new Date(dateStr)) / 1000);
    if (diff <    60) return diff + 's ago';
    if (diff <  3600) return Math.floor(diff / 60)   + 'm ago';
    if (diff < 86400) return Math.floor(diff / 3600) + 'h ago';
    return Math.floor(diff / 86400) + 'd ago';
  }

  function fullDate(dateStr) {
    return new Date(dateStr).toLocaleString('en-US', {
      month: 'short', day: 'numeric', year: 'numeric',
      hour: '2-digit', minute: '2-digit'
    });
  }

  let allNotifications = [];
  let activeFilter     = 'all';

  function renderList() {
    const filtered = activeFilter === 'all'
      ? allNotifications
      : allNotifications.filter(n => n.type === activeFilter);

    const list     = document.getElementById('historyList');
    const empty    = document.getElementById('emptyState');
    const subtitle = document.getElementById('historySubtitle');

    const unread = allNotifications.filter(n => n.is_read == 0).length;
    subtitle.textContent = unread > 0
      ? `${unread} unread · ${allNotifications.length} total`
      : `${allNotifications.length} total · all caught up ✓`;

    if (!filtered.length) {
      list.innerHTML = '';
      empty.classList.remove('d-none');
      return;
    }
    empty.classList.add('d-none');

    list.innerHTML = filtered.map(n => {
      const ic = iconMap[n.type] ?? defaultIcon;
      return `
        <li class="notif-row ${n.is_read == 0 ? 'unread' : ''}" data-id="${n.id}">
          <div class="notif-icon-wrap" style="background:${ic.bg};">
            <i class="bi ${ic.icon}" style="color:${ic.color};"></i>
          </div>
          <div style="min-width:0; flex:1;">
            <div class="notif-label">${notifLabel(n.type)}</div>
            <div class="notif-msg">${n.message}</div>
            <div class="notif-time">
              ${timeAgo(n.created_at)} &nbsp;·&nbsp; ${fullDate(n.created_at)}
            </div>
          </div>
          ${n.is_read == 0 ? `<span class="notif-badge">New</span>` : ''}
        </li>`;
    }).join('');
  }

  function loadAll() {
    fetch(ENDPOINT + '?action=fetch_all')
      .then(r => r.json())
      .then(data => {
        allNotifications = data.notifications ?? [];
        renderList();
      })
      .catch(() => {
        document.getElementById('historyList').innerHTML =
          `<li class="notif-state" style="color:#c00;">
             Could not load notifications. Please try again.
           </li>`;
      });
  }

  // Filter tabs
  document.getElementById('filterTabs').addEventListener('click', function (e) {
    const btn = e.target.closest('.filter-btn');
    if (!btn) return;
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    activeFilter = btn.dataset.type;
    renderList();
  });

  // Mark all read
  document.getElementById('markAllReadPage').addEventListener('click', function () {
    allNotifications.forEach(n => n.is_read = 1);
    renderList();
    fetch(ENDPOINT + '?action=mark_read', { method: 'POST' })
      .then(() => loadAll());
  });

  loadAll();
})();
</script>