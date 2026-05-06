<?php
// ── Resolve topbar avatar path ─────────────────────────────────────────────
$_sessionAvatar = $_SESSION['authUser']['avatar'] ?? '';
// avatar is stored as "uploads/avatars/file.jpg" (relative to web root)
// topbar lives at /admin/ so we need ../../ to reach root
$_topbarAvatarSrc = !empty($_sessionAvatar)
    ? '../../' . htmlspecialchars($_sessionAvatar)
    : 'assets/img/profile-img.jpg'; // fallback to default

$_topbarInitials = strtoupper(
    substr($_SESSION['authUser']['firstName'] ?? 'U', 0, 1) .
    substr($_SESSION['authUser']['lastName']  ?? 'S', 0, 1)
);
?>

<style>
  /* Header Container */
  .header {
    background: var(--paper) !important;
    border-bottom: 1.5px solid var(--paper-3);
    height: 60px;
    padding-left: 20px;
  }

  /* Logo & Brand */
  .header .logo span {
    font-family: 'DM Serif Display', serif;
    font-size: 22px;
    color: var(--ink);
    letter-spacing: -0.02em;
  }
  .header .logo span em {
    font-style: normal;
    color: var(--gold);
  }

  /* Nav Icon - Gold by default */
  .header-nav .nav-icon {
    color: var(--gold);
    font-size: 20px;
    position: relative;
    padding: 4px 8px;
    transition: all 0.2s ease;
  }

  /* NOTIFICATION NUMBER - Black */
  .header-nav .badge-number {
    background: transparent !important;
    color: #000000 !important;
    font-family: 'DM Sans', sans-serif;
    font-size: 11px;
    font-weight: 800;
    position: absolute;
    top: -2px;
    right: 2px;
    letter-spacing: -0.05em;
    text-shadow: 0.5px 0.5px 0px var(--paper), -0.5px -0.5px 0px var(--paper);
  }

  /* Hover effect */
  .header-nav .nav-icon:hover {
    color: #000000;
  }

  .header-nav .nav-icon:hover .badge-number {
    color: var(--gold) !important;
  }

  /* Profile Styling */
  .header-nav .nav-profile span {
    font-family: 'DM Sans', sans-serif;
    font-weight: 600;
    color: var(--ink-2);
    font-size: 14px;
  }

  /* Dropdown Styling */
  .dropdown-menu {
    background: #fff;
    border: 1.5px solid var(--paper-3) !important;
    box-shadow: var(--shadow) !important;
    border-radius: var(--radius) !important;
  }

  /* Topbar avatar */
  .topbar-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    object-fit: cover;
    border: 1.5px solid var(--paper-3);
  }

  /* Unread notification item */
  #notifList .notification-item.unread {
    background-color: #f8f9fa;
  }
  #notifList .notification-item.unread .notif-title {
    font-weight: 700;
  }
  #notifList .notification-item .notif-title {
    font-weight: 400;
  }
</style>

<header id="header" class="header fixed-top d-flex align-items-center">

  <div class="d-flex align-items-center justify-content-between">
    <a href="index.php" class="logo d-flex align-items-center">
      <img src="assets/img/logo.png" alt="">
      <span class="d-none d-lg-block">Quick<em>Query</em></span>
    </a>
    <i class="bi bi-list toggle-sidebar-btn"></i>
  </div>

  <nav class="header-nav ms-auto">
    <ul class="d-flex align-items-center">

      <!-- ── Notifications ── -->
      <li class="nav-item dropdown">
        <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown" id="notifBell">
          <i class="bi bi-bell"></i>
          <span class="badge badge-number" id="notifCount" style="display:none;"></span>
        </a>

        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications"
            style="min-width:340px; max-height:420px; overflow-y:auto;">

          <li class="dropdown-header d-flex justify-content-between align-items-center px-3 py-2">
            <span id="notifHeader" style="font-weight:600;">Notifications</span>
            <a href="#" id="markAllRead"
               class="badge rounded-pill bg-primary p-2 ms-2"
               style="font-size:11px; text-decoration:none;">Mark all read</a>
          </li>

          <li><hr class="dropdown-divider m-0"></li>

          <div id="notifList">
            <li class="text-center text-muted py-3" style="font-size:13px; list-style:none;">
              Loading…
            </li>
          </div>

          <li><hr class="dropdown-divider m-0"></li>
          <li class="dropdown-footer text-center py-2">
            <a href="notifications.php" style="font-size:13px;">Show all notifications</a>
          </li>

        </ul>
      </li><!-- /.nav-item notifications -->

      <!-- ── Profile ── -->
      <li class="nav-item dropdown pe-3">
        <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
          <img id="topbarAvatarImg"
               src="<?= $_topbarAvatarSrc ?>"
               alt="Profile"
               class="topbar-avatar rounded-circle">
          <span class="d-none d-md-block dropdown-toggle ps-2">
            <?= htmlspecialchars($_SESSION['authUser']['username']) ?>
          </span>
        </a>

        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
          <li class="dropdown-header">
            <h6><?= htmlspecialchars($_SESSION['authUser']['username']) ?></h6>
            <span><?= htmlspecialchars($_SESSION['authUser']['fullName']) ?> · Admin</span>
          </li>
          <li><hr class="dropdown-divider"></li>
          <li>
            <a class="dropdown-item d-flex align-items-center" href="/surveysystem/public/admin/profile.php">
              <i class="bi bi-person"></i>
              <span>My Profile</span>
            </a>
          </li>
          <li><hr class="dropdown-divider"></li>
          <li>
            <form action="../../app/controllers/adminController.php" method="post">
              <button type="submit" name="logoutButton" class="dropdown-item d-flex align-items-center">
                <i class="bi bi-box-arrow-right"></i>
                <span>Sign Out</span>
              </button>
            </form>
          </li>
        </ul>
      </li><!-- /.nav-item profile -->

    </ul>
  </nav>

</header>

<script>
(function () {
  const ENDPOINT = '../../app/controllers/notificationController.php';

  const icons = {
    accessed:         'bi-eye text-info',
    answered:         'bi-check2-circle text-success',
    auto_closed:      'bi-clock-history text-warning',
    user_registered:  'bi-person-plus text-primary',
  };

  function timeAgo(dateStr) {
    const diff = Math.floor((Date.now() - new Date(dateStr)) / 1000);
    if (diff <    60) return diff + 's ago';
    if (diff <  3600) return Math.floor(diff / 60)   + 'm ago';
    if (diff < 86400) return Math.floor(diff / 3600) + 'h ago';
    return Math.floor(diff / 86400) + 'd ago';
  }

  function notifLabel(type) {
    switch (type) {
      case 'answered':        return '📋 Survey Answered';
      case 'accessed':        return '👁 Survey Accessed';
      case 'auto_closed':     return '⏰ Survey Auto-Closed';
      case 'user_registered': return '👤 New User Registered';
      default:                return '🔔 Notification';
    }
  }

  function renderItems(notifications) {
    if (!notifications || !notifications.length) {
      return `<li style="list-style:none; text-align:center;
                          padding:1.5rem; font-size:13px; color:#999;">
                No notifications yet
              </li>`;
    }

    return notifications.map(n => `
      <li class="notification-item d-flex align-items-start px-3 py-2
                 ${n.is_read == 0 ? 'unread' : ''}"
          style="list-style:none; gap:.75rem;">
        <i class="bi ${icons[n.type] ?? 'bi-bell text-secondary'}"
           style="font-size:20px; margin-top:2px; flex-shrink:0;"></i>
        <div style="min-width:0;">
          <div class="notif-title" style="font-size:13px; margin-bottom:2px;">
            ${notifLabel(n.type)}
          </div>
          <div style="font-size:12px; color:#555; white-space:normal; word-break:break-word;">
            ${n.message}
          </div>
          <div style="font-size:11px; color:#aaa; margin-top:3px;">
            ${timeAgo(n.created_at)}
          </div>
        </div>
      </li>
      <li style="list-style:none;"><hr class="dropdown-divider m-0"></li>
    `).join('');
  }

  function loadNotifications() {
    fetch(ENDPOINT + '?action=fetch')
      .then(r => r.json())
      .then(data => {
        // ── Badge ──
        const badge = document.getElementById('notifCount');
        if (data.unread > 0) {
          badge.textContent = data.unread > 99 ? '99+' : data.unread;
          badge.style.display = 'block';
        } else {
          badge.style.display = 'none';
        }

        // ── Header text ──
        document.getElementById('notifHeader').textContent =
          data.unread > 0
            ? `You have ${data.unread} new notification${data.unread > 1 ? 's' : ''}`
            : 'Notifications';

        // ── List ──
        document.getElementById('notifList').innerHTML =
          renderItems(data.notifications);
      })
      .catch(() => {
        document.getElementById('notifList').innerHTML = `
          <li style="list-style:none; text-align:center;
                      padding:1rem; font-size:12px; color:#c00;">
            Could not load notifications.
          </li>`;
      });
  }

  // ── Bell click → mark read then reload ──
  document.getElementById('notifBell').addEventListener('click', function () {
    fetch(ENDPOINT + '?action=mark_read', { method: 'POST' })
      .then(() => setTimeout(loadNotifications, 250));
  });

  // ── Mark all read button ──
  document.getElementById('markAllRead').addEventListener('click', function (e) {
    e.preventDefault();
    e.stopPropagation();

    // Optimistic UI update — no waiting for server
    document.querySelectorAll('#notifList .notification-item').forEach(el => {
      el.classList.remove('unread');
    });
    document.getElementById('notifCount').style.display = 'none';
    document.getElementById('notifHeader').textContent = 'Notifications';

    // Persist to server then re-sync
    fetch(ENDPOINT + '?action=mark_read', { method: 'POST' })
      .then(() => loadNotifications());
  });

  // ── Initial load + 30 s polling ──
  loadNotifications();
  setInterval(loadNotifications, 30000);

})();
</script>