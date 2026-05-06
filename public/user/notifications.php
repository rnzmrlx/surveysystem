<?php
include '../../app/middleware/user.php';
include './includes/header.php';
include './includes/topbar.php';
include './includes/sidebar.php';
?>

<style>
  .notif-page-wrapper {
    padding: 32px 40px;
    transition: all 0.3s;
  }

  .notif-page-title {
    font-family: 'DM Serif Display', serif;
    font-size: 26px;
    color: var(--ink);
    letter-spacing: -0.02em;
    margin-bottom: 2px;
  }

  .notif-breadcrumb {
    font-family: 'DM Sans', sans-serif;
    font-size: 12px;
    color: var(--ink-3);
    margin-bottom: 28px;
  }
  .notif-breadcrumb a { color: var(--gold); text-decoration: none; }
  .notif-breadcrumb a:hover { text-decoration: underline; }

  .notif-card {
    background: #fff;
    border: 1.5px solid var(--paper-3);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    overflow: hidden;
    width: 100%;
    max-width: 1200px;
    margin: 0 auto;
    transition: all 0.3s;
  }

  .notif-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 28px;
    border-bottom: 1.5px solid var(--paper-3);
    background: var(--paper);
    flex-wrap: wrap;
    gap: 12px;
  }
  .notif-card-header h2 {
    font-family: 'DM Serif Display', serif;
    font-size: 18px;
    color: var(--ink);
    margin: 0 0 2px;
  }
  .notif-card-header p {
    font-family: 'DM Sans', sans-serif;
    font-size: 12px;
    color: var(--ink-3);
    margin: 0;
  }

  .btn-mark-read {
    font-family: 'DM Sans', sans-serif;
    font-size: 12px;
    font-weight: 600;
    padding: 7px 18px;
    border-radius: 20px;
    border: 1.5px solid var(--gold);
    background: transparent;
    color: var(--gold);
    cursor: pointer;
    transition: all .2s;
    white-space: nowrap;
  }
  .btn-mark-read:hover { background: var(--gold); color: #fff; }

  .notif-filters {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    padding: 16px 28px;
    border-bottom: 1.5px solid var(--paper-3);
    background: #fff;
  }

  .filter-btn {
    font-family: 'DM Sans', sans-serif;
    font-size: 12px;
    font-weight: 600;
    padding: 5px 16px;
    border-radius: 20px;
    border: 1.5px solid var(--paper-3);
    background: transparent;
    color: var(--ink-3);
    cursor: pointer;
    transition: all .2s;
  }
  .filter-btn:hover { border-color: var(--gold); color: var(--gold); }
  .filter-btn.active { background: var(--gold); border-color: var(--gold); color: #fff; }

  .notif-list { list-style: none; margin: 0; padding: 0; }

  .notif-row {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    padding: 16px 28px;
    border-bottom: 1.5px solid var(--paper-3);
    background: #fff;
    transition: background .15s;
  }
  .notif-row:last-child { border-bottom: none; }
  .notif-row:hover { background: var(--paper); }
  .notif-row.unread {
    background: #fdfaf3;
    border-left: 3px solid var(--gold);
    padding-left: 25px;
  }

  .notif-icon-wrap {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    margin-top: 2px;
  }
  .notif-icon-wrap i { font-size: 16px; }

  .notif-label {
    font-family: 'DM Sans', sans-serif;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: var(--ink-3);
    margin-bottom: 3px;
  }
  .notif-row.unread .notif-label { color: var(--gold); }

  .notif-msg {
    font-family: 'DM Sans', sans-serif;
    font-size: 13px;
    color: var(--ink);
    line-height: 1.5;
    word-break: break-word;
    font-weight: 400;
  }
  .notif-row.unread .notif-msg { font-weight: 500; }

  .notif-time {
    font-family: 'DM Sans', sans-serif;
    font-size: 11px;
    color: var(--ink-3);
    margin-top: 4px;
  }

  .notif-badge {
    font-family: 'DM Sans', sans-serif;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .04em;
    background: var(--gold);
    color: #fff;
    border-radius: 20px;
    padding: 2px 10px;
    align-self: center;
    flex-shrink: 0;
  }

  .notif-survey-chip {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    margin-top: 5px;
    background: var(--gold-light, #f5e9cc);
    color: var(--gold-dark, #8a6318);
    font-size: 11px;
    font-weight: 600;
    padding: 2px 9px;
    border-radius: 20px;
  }

  .notif-state {
    text-align: center;
    padding: 60px 20px;
    font-family: 'DM Sans', sans-serif;
    font-size: 13px;
    color: var(--ink-3);
  }
  .notif-state i {
    font-size: 2.5rem;
    color: var(--paper-3);
    display: block;
    margin-bottom: 12px;
  }

  @media (max-width: 600px) {
    .notif-page-wrapper { padding: 20px 16px; }
    .notif-card-header,
    .notif-filters,
    .notif-row { padding-left: 16px; padding-right: 16px; }
    .notif-row.unread { padding-left: 13px; }
  }
</style>

<div class="notif-page-wrapper">

  <h1 class="notif-page-title">Notifications</h1>
  <div class="notif-breadcrumb">
    <a href="index.php">Home</a> &rsaquo; Notifications
  </div>

  <div class="notif-card">

    <div class="notif-card-header">
      <div>
        <h2 id="notifPageHeader">All Notifications</h2>
        <p id="notifPageSubtitle">Loading…</p>
      </div>
      <button class="btn-mark-read" id="markAllReadPage">
        <i class="bi bi-check2-all me-1"></i> Mark all read
      </button>
    </div>

    <div class="notif-filters" id="filterTabs">
      <button class="filter-btn active" data-type="all">All</button>
      <button class="filter-btn" data-type="survey_published">New Surveys</button>
      <button class="filter-btn" data-type="closing_soon">Closing Soon</button>
      <button class="filter-btn" data-type="survey_closed">Closed</button>
      <button class="filter-btn" data-type="response_recorded">Responses</button>
    </div>

    <ul class="notif-list" id="notifPageList">
      <li class="notif-state">
        <div class="spinner-border spinner-border-sm" style="color:var(--gold);" role="status"></div>
        <span style="margin-left:8px;">Loading notifications…</span>
      </li>
    </ul>

    <div id="notifPageEmpty" class="notif-state" style="display:none;">
      <i class="bi bi-bell-slash"></i>
      No notifications found.
    </div>

  </div>

</div>

<?php include './includes/footer.php'; ?>

<script>
(function () {
  const ENDPOINT = '/surveysystem/app/controllers/userNotificationController.php';

  const iconMap = {
    survey_published:  { icon: 'bi-megaphone',    bg: '#3498db18', color: '#3498db' },
    closing_soon:      { icon: 'bi-clock',         bg: '#e67e2218', color: '#e67e22' },
    survey_closed:     { icon: 'bi-lock',          bg: '#a02c2c18', color: '#a02c2c' },
    response_recorded: { icon: 'bi-check2-circle', bg: '#2ecc7118', color: '#2ecc71' },
  };
  const defaultIcon = { icon: 'bi-bell', bg: '#88888818', color: '#888' };

  const labelMap = {
    survey_published:  'New Survey',
    closing_soon:      'Closing Soon',
    survey_closed:     'Survey Closed',
    response_recorded: 'Response Recorded',
  };

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

  function escHtml(str) {
    return String(str ?? '')
      .replace(/&/g, '&amp;').replace(/</g, '&lt;')
      .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
  }

  let allNotifications = [];
  let activeFilter     = 'all';

  function renderList() {
    const filtered = activeFilter === 'all'
      ? allNotifications
      : allNotifications.filter(n => n.type === activeFilter);

    const list   = document.getElementById('notifPageList');
    const empty  = document.getElementById('notifPageEmpty');
    const header = document.getElementById('notifPageHeader');
    const sub    = document.getElementById('notifPageSubtitle');
    const unread = allNotifications.filter(n => n.is_read == 0).length;

    header.textContent = activeFilter === 'all'
      ? 'All Notifications'
      : (labelMap[activeFilter] ?? 'Notifications');

    sub.textContent = unread > 0
      ? `${unread} unread · ${allNotifications.length} total`
      : `${allNotifications.length} total · all caught up ✓`;

    if (!filtered.length) {
      list.innerHTML = '';
      empty.style.display = 'block';
      return;
    }
    empty.style.display = 'none';

    list.innerHTML = filtered.map(n => {
      const ic    = iconMap[n.type] ?? defaultIcon;
      const label = labelMap[n.type] ?? 'Notification';
      const chip  = n.survey_title
        ? `<div class="notif-survey-chip">
             <i class="bi bi-journal-text" style="font-size:10px;"></i>
             ${escHtml(n.survey_title)}
           </div>`
        : '';
      return `
        <li class="notif-row ${n.is_read == 0 ? 'unread' : ''}" data-id="${n.id}">
          <div class="notif-icon-wrap" style="background:${ic.bg};">
            <i class="bi ${ic.icon}" style="color:${ic.color};"></i>
          </div>
          <div style="min-width:0; flex:1;">
            <div class="notif-label">${label}</div>
            <div class="notif-msg">${escHtml(n.message)}</div>
            ${chip}
            <div class="notif-time">
              ${timeAgo(n.created_at)} &nbsp;·&nbsp; ${fullDate(n.created_at)}
            </div>
          </div>
          ${n.is_read == 0 ? '<span class="notif-badge">New</span>' : ''}
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
        document.getElementById('notifPageList').innerHTML =
          `<li class="notif-state" style="color:#c00;">
             Could not load notifications. Please try again.
           </li>`;
      });
  }

  document.getElementById('filterTabs').addEventListener('click', function (e) {
    const btn = e.target.closest('.filter-btn');
    if (!btn) return;
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    activeFilter = btn.dataset.type;
    renderList();
  });

  document.getElementById('markAllReadPage').addEventListener('click', function () {
    allNotifications.forEach(n => n.is_read = 1);
    renderList();
    fetch(ENDPOINT + '?action=mark_read', { method: 'POST' })
      .then(() => loadAll());
  });

  loadAll();

  // ── Respond to sidebar toggle in real time ────────────────────────────
  var toggleBtn = document.querySelector('.toggle-sidebar-btn') ||
                  document.getElementById('toggleSidebar');
  if (toggleBtn) {
    toggleBtn.addEventListener('click', function () {
      setTimeout(function () {
        var wrapper = document.querySelector('.notif-page-wrapper');
        if (!wrapper) return;
        var hidden = document.body.classList.contains('toggle-sidebar');
        wrapper.style.padding = hidden ? '32px 80px' : '32px 40px';
      }, 50);
    });
  }

})();
</script>