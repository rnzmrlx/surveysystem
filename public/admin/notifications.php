<?php
// ── notifications.php ─────────────────────────────────────────────────────
// Location: surveysystem/public/admin/notifications.php
// ─────────────────────────────────────────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
if (empty($_SESSION['authUser'])) {
  header('Location: ../login.php');
  exit;
}

include __DIR__ . '/../../app/config/config.php';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/topbar.php';
include __DIR__ . '/includes/sidebar.php';
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
    --shadow: 0 2px 16px rgba(15, 14, 13, .07);
    --sidebar-w: 260px;
    --sidebar-w-mini: 72px;
  }

  *,
  *::before,
  *::after {
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

  /* ── Breadcrumb ── */
  .notif-breadcrumb {
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

  .notif-breadcrumb a {
    color: var(--ink-3);
    text-decoration: none;
    transition: color .15s;
  }

  .notif-breadcrumb a:hover {
    color: var(--gold);
  }

  .notif-breadcrumb .sep {
    color: var(--paper-3);
  }

  .notif-breadcrumb .cur {
    color: var(--ink-2);
  }

  /* ── Page title ── */
  .notif-page-header {
    margin-bottom: 1.5rem;
  }

.notif-page-title {
  font-family: 'DM Serif Display', serif;
  font-size: clamp(1.6rem, 5vw, 3rem);
  font-weight: 400;
  color: var(--ink);
}

  .notif-page-title em {
    font-style: italic;
    color: var(--gold);
  }

  .notif-page-subtitle {
    font-size: 13.5px;
    color: var(--ink-3);
    margin-top: 6px;
  }

  /* ── Split layout ── */
  .notif-split {
    display: grid;
    grid-template-columns: 320px 1fr;
    gap: 0;
    background: #fff;
    border: 1.5px solid var(--paper-3);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    overflow: hidden;
    min-height: 600px;
    position: relative;
  }

  .notif-split::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: var(--gold);
    z-index: 2;
  }

  /* ── Left panel ── */
  .notif-panel-left {
    border-right: 1.5px solid var(--paper-3);
    display: flex;
    flex-direction: column;
    overflow: hidden;
  }

  .notif-panel-header {
    padding: 20px 20px 14px;
    border-bottom: 1.5px solid var(--paper-3);
    background: var(--paper);
    flex-shrink: 0;
  }

  .notif-panel-header h2 {
    font-family: 'DM Serif Display', serif;
    font-size: 1rem;
    font-weight: 400;
    color: var(--ink);
    margin-bottom: 4px;
  }

  .notif-panel-header p {
    font-size: 11.5px;
    color: var(--ink-3);
  }

  /* ── Filter tabs ── */
  .notif-filters {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
    padding: 12px 14px;
    border-bottom: 1.5px solid var(--paper-3);
    background: #fff;
    flex-shrink: 0;
  }

  .filter-btn {
    font-family: 'DM Sans', sans-serif;
    font-size: 11px;
    font-weight: 600;
    padding: 4px 12px;
    border-radius: 20px;
    border: 1.5px solid var(--paper-3);
    background: transparent;
    color: var(--ink-3);
    cursor: pointer;
    transition: all .2s ease;
  }

  .filter-btn:hover {
    border-color: var(--gold);
    color: var(--gold);
  }

  .filter-btn.active {
    background: var(--gold);
    border-color: var(--gold);
    color: #fff;
  }

  /* ── Mark all read ── */
  .btn-mark-read {
    font-family: 'DM Sans', sans-serif;
    font-size: 11px;
    font-weight: 600;
    padding: 5px 14px;
    border-radius: 20px;
    border: 1.5px solid var(--gold);
    background: transparent;
    color: var(--gold);
    cursor: pointer;
    transition: all .2s ease;
    white-space: nowrap;
  }

  .btn-mark-read:hover {
    background: var(--gold);
    color: #fff;
  }

  .notif-panel-actions {
    display: flex;
    justify-content: flex-end;
    padding: 8px 14px;
    border-bottom: 1.5px solid var(--paper-3);
    flex-shrink: 0;
  }

  /* ── Notification list ── */
  .notif-list {
    list-style: none;
    overflow-y: auto;
    flex: 1;
  }

  .notif-row {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 14px 18px;
    border-bottom: 1.5px solid var(--paper-3);
    background: #fff;
    transition: background .15s ease;
    cursor: pointer;
    position: relative;
  }

  .notif-row:last-child {
    border-bottom: none;
  }

  .notif-row:hover {
    background: var(--paper);
  }

  .notif-row.active {
    background: var(--gold-light);
  }

  .notif-row.unread {
    background: #fdfaf3;
    border-left: 3px solid var(--gold);
    padding-left: 15px;
  }

  .notif-row.unread.active {
    background: var(--gold-light);
  }

  /* ── Icon bubble ── */
  .notif-icon-wrap {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    margin-top: 1px;
  }

  .notif-icon-wrap i {
    font-size: 15px;
  }

  /* ── Row text ── */
  .notif-row-body {
    flex: 1;
    min-width: 0;
  }

  .notif-label {
    font-size: 10.5px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: var(--ink-3);
    margin-bottom: 3px;
  }

  .notif-row.unread .notif-label {
    color: var(--gold);
  }

  .notif-msg {
    font-size: 12.5px;
    color: var(--ink);
    line-height: 1.5;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    font-weight: 400;
  }

  .notif-row.unread .notif-msg {
    font-weight: 600;
  }

  .notif-time {
    font-size: 11px;
    color: var(--ink-3);
    margin-top: 3px;
  }

  .notif-badge {
    font-size: 9px;
    font-weight: 700;
    letter-spacing: 0.04em;
    background: var(--gold);
    color: #fff;
    border-radius: 20px;
    padding: 2px 8px;
    align-self: center;
    flex-shrink: 0;
  }

  /* ── Right panel (detail) ── */
  .notif-panel-right {
    display: flex;
    flex-direction: column;
    overflow: hidden;
    background: #fff;
  }

  .notif-detail-empty {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: var(--ink-3);
    font-size: 13px;
    text-align: center;
    padding: 40px;
    gap: 12px;
  }

  .notif-detail-empty i {
    font-size: 2.4rem;
    color: var(--paper-3);
  }

  .notif-detail-empty p {
    max-width: 220px;
    line-height: 1.6;
  }

  .notif-detail-view {
    display: none;
    flex-direction: column;
    height: 100%;
    overflow-y: auto;
  }

  .notif-detail-view.visible {
    display: flex;
  }

  .notif-detail-head {
    padding: 28px 32px 20px;
    border-bottom: 1.5px solid var(--paper-3);
    background: var(--paper);
  }

  .notif-detail-type {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.07em;
    color: var(--gold);
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 7px;
  }

  .notif-detail-type i {
    font-size: 14px;
  }

  .notif-detail-title {
    font-family: 'DM Serif Display', serif;
    font-size: 1.35rem;
    font-weight: 400;
    color: var(--ink);
    line-height: 1.4;
    margin-bottom: 10px;
  }

  .notif-detail-meta {
    font-size: 12px;
    color: var(--ink-3);
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
  }

  .notif-detail-meta span {
    display: flex;
    align-items: center;
    gap: 5px;
  }

  .notif-detail-body {
    padding: 28px 32px;
    flex: 1;
  }

  .notif-detail-section-label {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.07em;
    color: var(--ink-3);
    margin-bottom: 10px;
  }

  .notif-detail-message {
    font-size: 14px;
    color: var(--ink);
    line-height: 1.7;
    background: var(--paper);
    border: 1.5px solid var(--paper-3);
    border-radius: var(--radius);
    padding: 16px 20px;
  }

  .notif-detail-actions {
    padding: 18px 32px;
    border-top: 1.5px solid var(--paper-3);
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
  }

  .btn-secondary {
    font-family: 'DM Sans', sans-serif;
    font-size: 12px;
    font-weight: 600;
    padding: 7px 18px;
    border-radius: 20px;
    border: 1.5px solid var(--paper-3);
    background: transparent;
    color: var(--ink-2);
    cursor: pointer;
    transition: all .2s;
  }

  .btn-secondary:hover {
    border-color: var(--ink-3);
  }

  .btn-primary-sm {
    font-family: 'DM Sans', sans-serif;
    font-size: 12px;
    font-weight: 600;
    padding: 7px 18px;
    border-radius: 20px;
    border: 1.5px solid var(--gold);
    background: var(--gold);
    color: #fff;
    cursor: pointer;
    transition: all .2s;
  }

  .btn-primary-sm:hover {
    background: var(--gold-dark);
    border-color: var(--gold-dark);
  }

  /* ── Loading / empty state ── */
  .notif-state {
    text-align: center;
    padding: 60px 20px;
    font-size: 13px;
    color: var(--ink-3);
  }

  .notif-state i {
    font-size: 2.5rem;
    color: var(--paper-3);
    display: block;
    margin-bottom: 12px;
  }

  /* ── Responsive ── */
  @media (max-width: 900px) {
    .notif-split {
      grid-template-columns: 1fr;
    }

    .notif-panel-left {
      border-right: none;
      border-bottom: 1.5px solid var(--paper-3);
      max-height: 420px;
    }

    .notif-panel-right {
      min-height: 300px;
    }
  }

  @media (max-width: 768px) {
    .main-wrapper {
      padding: 1.5rem 1.25rem 3rem;
    }

    .notif-detail-head {
      padding: 20px 18px 16px;
    }

    .notif-detail-body {
      padding: 20px 18px;
    }

    .notif-detail-actions {
      padding: 14px 18px;
    }
  }

  @media (max-width: 480px) {
    .filter-btn {
      font-size: 10px;
      padding: 3px 10px;
    }

    .notif-msg {
      font-size: 12px;
    }
  }
</style>

<div class="main-wrapper">

  <!-- Breadcrumb -->
  <nav class="notif-breadcrumb" aria-label="breadcrumb">
    <a href="index.php">Home</a>
    <span class="sep" aria-hidden="true">/</span>
    <span class="cur" aria-current="page">Notifications</span>
  </nav>

  <!-- Page title -->
  <div class="notif-page-header">
    <h1 class="notif-page-title">All <em>Notifications</em></h1>
    <p class="notif-page-subtitle">View and manage your recent activity alerts.</p>
  </div>

  <!-- Split panel -->
  <div class="notif-split">

    <!-- LEFT: list -->
    <aside class="notif-panel-left">

      <div class="notif-panel-header">
        <h2 id="historyHeader">Notifications</h2>
        <p id="historySubtitle">Loading…</p>
      </div>

      <div class="notif-filters" id="filterTabs" role="tablist" aria-label="Notification filters">
        <button class="filter-btn active" data-type="all" role="tab" aria-selected="true">All</button>
        <button class="filter-btn" data-type="answered" role="tab" aria-selected="false">Answered</button>
        <button class="filter-btn" data-type="accessed" role="tab" aria-selected="false">Accessed</button>
        <button class="filter-btn" data-type="auto_closed" role="tab" aria-selected="false">Closed</button>
        <button class="filter-btn" data-type="user_registered" role="tab" aria-selected="false">New Users</button>
      </div>

      <div class="notif-panel-actions">
        <button class="btn-mark-read" id="markAllReadPage" aria-label="Mark all notifications as read">
          <i class="bi bi-check2-all me-1"></i> Mark all read
        </button>
      </div>

      <ul class="notif-list" id="historyList" role="list">
        <li class="notif-state">
          <div class="spinner-border spinner-border-sm me-2"
            style="color:var(--gold);" role="status">
            <span class="visually-hidden">Loading…</span>
          </div>
          Loading…
        </li>
      </ul>

    </aside>

    <!-- RIGHT: detail -->
    <section class="notif-panel-right" aria-live="polite" aria-label="Notification detail">

      <div class="notif-detail-empty" id="detailEmpty">
        <i class="bi bi-arrow-left-circle" aria-hidden="true"></i>
        <p>Select a notification from the list to view more details.</p>
      </div>

      <div class="notif-detail-view" id="detailView">

        <div class="notif-detail-head" id="detailHead">
          <div class="notif-detail-type" id="detailType"></div>
          <div class="notif-detail-title" id="detailTitle"></div>
          <div class="notif-detail-meta" id="detailMeta"></div>
        </div>

        <div class="notif-detail-body">
          <p class="notif-detail-section-label">Message</p>
          <div class="notif-detail-message" id="detailMessage"></div>
        </div>

        <div class="notif-detail-actions">
          <button class="btn-primary-sm" id="detailMarkRead">
            <i class="bi bi-check2 me-1"></i> Mark as read
          </button>
          <button class="btn-secondary" id="detailClose">
            <i class="bi bi-x me-1"></i> Dismiss
          </button>
        </div>

      </div>

    </section>

  </div><!-- /.notif-split -->

</div><!-- /.main-wrapper -->

<?php include __DIR__ . '/includes/footer.php'; ?>

<script>
  (function() {
    'use strict';

    const ENDPOINT = '../../app/controllers/notificationController.php';

    const iconMap = {
      accessed: {
        icon: 'bi-eye',
        bg: '#c9a84c18',
        color: 'var(--gold)'
      },
      answered: {
        icon: 'bi-check2-circle',
        bg: '#2ecc7118',
        color: '#2ecc71'
      },
      auto_closed: {
        icon: 'bi-clock-history',
        bg: '#e67e2218',
        color: '#e67e22'
      },
      user_registered: {
        icon: 'bi-person-plus',
        bg: '#3498db18',
        color: '#3498db'
      },
    };
    const defaultIcon = {
      icon: 'bi-bell',
      bg: '#88888818',
      color: '#888'
    };

    function notifLabel(type) {
      const labels = {
        answered: 'Survey Answered',
        accessed: 'Survey Accessed',
        auto_closed: 'Survey Auto-Closed',
        user_registered: 'New User Registered',
      };
      return labels[type] ?? 'Notification';
    }

    function timeAgo(dateStr) {
      const diff = Math.floor((Date.now() - new Date(dateStr)) / 1000);
      if (diff < 60) return diff + 's ago';
      if (diff < 3600) return Math.floor(diff / 60) + 'm ago';
      if (diff < 86400) return Math.floor(diff / 3600) + 'h ago';
      return Math.floor(diff / 86400) + 'd ago';
    }

    function fullDate(dateStr) {
      return new Date(dateStr).toLocaleString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
    }

    let allNotifications = [];
    let activeFilter = 'all';
    let activeId = null;

    /* ── Render list ── */
    function renderList() {
      const filtered = activeFilter === 'all' ?
        allNotifications :
        allNotifications.filter(n => n.type === activeFilter);

      const list = document.getElementById('historyList');
      const subtitle = document.getElementById('historySubtitle');

      const unread = allNotifications.filter(n => n.is_read == 0).length;
      subtitle.textContent = unread > 0 ?
        `${unread} unread · ${allNotifications.length} total` :
        `${allNotifications.length} total · all caught up ✓`;

      if (!filtered.length) {
        list.innerHTML = `<li class="notif-state">
        <i class="bi bi-bell-slash" aria-hidden="true"></i>
        No notifications found.
      </li>`;
        return;
      }

      list.innerHTML = filtered.map(n => {
        const ic = iconMap[n.type] ?? defaultIcon;
        const isActive = n.id == activeId ? ' active' : '';
        return `
        <li class="notif-row${n.is_read == 0 ? ' unread' : ''}${isActive}" data-id="${n.id}" role="button" tabindex="0" aria-label="${notifLabel(n.type)}: ${n.message}">
          <div class="notif-icon-wrap" style="background:${ic.bg};" aria-hidden="true">
            <i class="bi ${ic.icon}" style="color:${ic.color};"></i>
          </div>
          <div class="notif-row-body">
            <div class="notif-label">${notifLabel(n.type)}</div>
            <div class="notif-msg">${n.message}</div>
            <div class="notif-time">${timeAgo(n.created_at)}</div>
          </div>
          ${n.is_read == 0 ? '<span class="notif-badge" aria-label="Unread">New</span>' : ''}
        </li>`;
      }).join('');

      list.querySelectorAll('.notif-row').forEach(row => {
        row.addEventListener('click', () => openDetail(row.dataset.id));
        row.addEventListener('keydown', e => {
          if (e.key === 'Enter' || e.key === ' ') openDetail(row.dataset.id);
        });
      });
    }

    /* ── Open detail ── */
    function openDetail(id) {
      const notif = allNotifications.find(n => n.id == id);
      if (!notif) return;

      activeId = id;

      // Mark active in list
      document.querySelectorAll('.notif-row').forEach(r => r.classList.remove('active'));
      const row = document.querySelector(`.notif-row[data-id="${id}"]`);
      if (row) row.classList.add('active');

      const ic = iconMap[notif.type] ?? defaultIcon;

      document.getElementById('detailType').innerHTML =
        `<i class="bi ${ic.icon}" style="color:${ic.color};" aria-hidden="true"></i> ${notifLabel(notif.type)}`;

      document.getElementById('detailTitle').textContent = notif.message;

      document.getElementById('detailMeta').innerHTML = `
      <span><i class="bi bi-clock" aria-hidden="true"></i> ${timeAgo(notif.created_at)}</span>
      <span><i class="bi bi-calendar3" aria-hidden="true"></i> ${fullDate(notif.created_at)}</span>
      ${notif.is_read == 0 ? '<span style="color:var(--gold);font-weight:600;"><i class="bi bi-circle-fill" style="font-size:7px;vertical-align:2px;" aria-hidden="true"></i> Unread</span>' : '<span style="color:var(--ink-3);"><i class="bi bi-check2-all" aria-hidden="true"></i> Read</span>'}
    `;

      document.getElementById('detailMessage').textContent = notif.message;

      // Show / hide mark-read button
      const markBtn = document.getElementById('detailMarkRead');
      markBtn.style.display = notif.is_read == 0 ? '' : 'none';

      // Show detail panel
      document.getElementById('detailEmpty').style.display = 'none';
      document.getElementById('detailView').classList.add('visible');

      // Auto mark as read
      if (notif.is_read == 0) {
        notif.is_read = 1;
        renderList();
        fetch(ENDPOINT + '?action=mark_one&id=' + id, {
          method: 'POST'
        });
      }
    }

    /* ── Load all ── */
    function loadAll() {
      fetch(ENDPOINT + '?action=fetch_all')
        .then(r => r.json())
        .then(data => {
          allNotifications = data.notifications ?? [];
          renderList();
        })
        .catch(() => {
          document.getElementById('historyList').innerHTML =
            `<li class="notif-state" style="color:var(--rose);">
             <i class="bi bi-exclamation-circle"></i>
             Could not load notifications.
           </li>`;
        });
    }

    /* ── Filter tabs ── */
    document.getElementById('filterTabs').addEventListener('click', e => {
      const btn = e.target.closest('.filter-btn');
      if (!btn) return;
      document.querySelectorAll('.filter-btn').forEach(b => {
        b.classList.remove('active');
        b.setAttribute('aria-selected', 'false');
      });
      btn.classList.add('active');
      btn.setAttribute('aria-selected', 'true');
      activeFilter = btn.dataset.type;
      renderList();
    });

    /* ── Mark all read ── */
    document.getElementById('markAllReadPage').addEventListener('click', () => {
      allNotifications.forEach(n => n.is_read = 1);
      renderList();
      // Update detail panel if open
      if (activeId) {
        document.getElementById('detailMarkRead').style.display = 'none';
        const metaSpans = document.getElementById('detailMeta').querySelectorAll('span');
        if (metaSpans.length >= 3) {
          metaSpans[2].outerHTML = '<span style="color:var(--ink-3);"><i class="bi bi-check2-all"></i> Read</span>';
        }
      }
      fetch(ENDPOINT + '?action=mark_read', {
        method: 'POST'
      });
    });

    /* ── Detail: mark single read ── */
    document.getElementById('detailMarkRead').addEventListener('click', () => {
      if (!activeId) return;
      const notif = allNotifications.find(n => n.id == activeId);
      if (notif) notif.is_read = 1;
      renderList();
      document.getElementById('detailMarkRead').style.display = 'none';
      fetch(ENDPOINT + '?action=mark_one&id=' + activeId, {
        method: 'POST'
      });
    });

    /* ── Detail: dismiss / close ── */
    document.getElementById('detailClose').addEventListener('click', () => {
      activeId = null;
      document.querySelectorAll('.notif-row').forEach(r => r.classList.remove('active'));
      document.getElementById('detailView').classList.remove('visible');
      document.getElementById('detailEmpty').style.display = '';
    });

    loadAll();
  })();
</script>