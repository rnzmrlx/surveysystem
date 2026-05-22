<?php if (!isset($_SESSION)) session_start();
$user     = $_SESSION['authUser'] ?? [];
$userName     = htmlspecialchars($user['username'] ?? $user['user_name'] ?? 'User');
$userFullName = htmlspecialchars($user['fullName'] ?? $user['name'] ?? '');
$userRole     = htmlspecialchars($user['role'] ?? 'User');

$profileUrl = '/surveysystem/public/user/profile.php';

$_topbarAvatar = '/surveysystem/assets/img/profile-img.jpg';

if (!empty($user['avatar'])) {
  $_topbarAvatar = '/surveysystem/' . htmlspecialchars($user['avatar']);
} elseif (!empty($user['user_id'])) {
  if (!isset($conn)) {
    include_once dirname(__DIR__, 3) . '/app/config/config.php';
  }
  $stmt = $conn->prepare("SELECT avatar FROM users WHERE id = ? LIMIT 1");
  $stmt->bind_param('i', $user['user_id']);
  $stmt->execute();
  $stmt->bind_result($_dbAvatar);
  $stmt->fetch();
  $stmt->close();
  if (!empty($_dbAvatar)) {
    $_SESSION['authUser']['avatar'] = $_dbAvatar;
    $_topbarAvatar = '/surveysystem/' . htmlspecialchars($_dbAvatar);
  }
}
?>

<style>
  .header {
    background: var(--paper);
    border-bottom: 1.5px solid var(--paper-3);
    height: 60px;
    padding: 0 20px;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1030;
    display: flex;
    align-items: center;
    transition: all 0.3s;
  }

  body.sidebar-hidden .header {
    padding-left: 20px;
  }

  @media (max-width: 1199px) {
    .header {
      padding-left: 20px;
    }
  }

  .header .logo {
    display: flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
  }

  .header .logo span {
    font-family: 'DM Serif Display', serif;
    font-size: 22px;
    color: var(--ink);
    letter-spacing: -0.02em;
  }

  .header .logo span em {
    font-style: italic;
    color: var(--gold);
  }

  .toggle-sidebar-btn {
    font-size: 30px;
    cursor: pointer;
    color: var(--ink);
    margin-left: 16px;
    line-height: 1;
  }

  .header-nav {
    display: flex;
    align-items: center;
    margin-left: auto;
    margin-right: 0.5rem;
    list-style: none;
    padding: 0;
    margin-bottom: 0;
    gap: 4px;
  }
.header-nav li {
    list-style: none !important;
}
 .header-nav .nav-icon {
    position: relative;   /* already there — confirm it's present */
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 50%;
}

  .header-nav .nav-icon:hover {
    color: #000;
  }

.header-nav .badge-number {
    position: absolute;
    top: 2px;
    right: 6px;
    background: transparent !important;
    color: #000000 !important;
    font-size: 11px;
    font-weight: 800;
    font-family: 'DM Sans', sans-serif;
    padding: 0;
    line-height: 1;
}

  .header-nav .nav-profile {
    display: flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    padding: 4px 10px;
    color: var(--ink-2);
  }

  .header-nav .nav-profile img {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    object-fit: cover;
    border: 1px solid var(--paper-3);
  }

  .header-nav .nav-profile span {
    font-weight: 600;
    font-size: 14px;
    color: var(--ink-2);
  }

  .header-nav .dropdown-menu {
    background: #fff !important;
    border: 1.5px solid var(--paper-3) !important;
    box-shadow: 0 8px 24px rgba(15, 14, 13, 0.12) !important;
    border-radius: var(--radius) !important;
    padding: 6px 0 !important;
    min-width: 240px;
    z-index: 9999 !important;
  }

  .header-nav .dropdown-header {
    padding: 8px 16px 4px;
  }

  .header-nav .dropdown-header h6 {
    font-size: 16px;
    font-weight: 600;
    color: var(--ink);
    margin: 0;
  }

  .header-nav .dropdown-header span {
  font-size: 14px;   /* ← matches admin */
  color: #6c757d;
}

  .dropdown-menu.profile {
    min-width: 200px;
}
.dropdown-menu.profile .dropdown-header h6 {
    font-size: 14px;
    font-weight: 600;
    color: var(--ink);
    margin: 0;
}

.dropdown-menu.profile .dropdown-header span {
    font-size: 12px;
    color: #6c757d;
}

.dropdown-menu.profile .dropdown-header {
    text-align: center;
}

.dropdown-item {
    font-size: 13.5px !important;
}

.dropdown-item:hover {
    color: var(--gold) !important;
}
  /* ── Notification dropdown (manual, no Bootstrap) ── */
  #notifWrapper {
    position: relative;
  }

  .notif-dropdown {
    position: absolute;
    top: calc(100% + 6px);
    right: 0;
    min-width: 360px;
    max-height: 520px;
    display: none;
    flex-direction: column;
    background: #fff;
    border: 1.5px solid var(--paper-3);
    box-shadow: 0 8px 24px rgba(15, 14, 13, 0.12);
    border-radius: var(--radius, 8px);
    z-index: 9999;
    overflow: hidden;
  }

  .notif-dropdown.show {
    display: flex;
  }

  .notif-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 16px;
    border-bottom: 1.5px solid var(--paper-3);
    flex-shrink: 0;
  }

  .notif-header-title {
    font-size: 14px;
    font-weight: 700;
    color: var(--ink);
  }

  .btn-mark-all {
    background: var(--gold) !important;
    color: #fff !important;
    border: none;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    padding: 4px 12px;
    cursor: pointer;
    transition: background 0.2s;
    font-family: 'DM Sans', sans-serif;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
  }

  .btn-mark-all:hover {
    background: var(--gold-dark) !important;
    color: #fff !important;
  }

  .notif-list {
    overflow-y: auto;
    flex: 1;
    scrollbar-width: thin;
    scrollbar-color: var(--paper-3) transparent;
  }

  .notif-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 11px 16px;
    border-bottom: 1px solid var(--paper-2);
    cursor: pointer;
    transition: background 0.15s;
  }

  .notif-item:hover {
    background: var(--paper);
  }

  .notif-item.unread {
    background: var(--gold-light);
  }

  .notif-item.unread:hover {
    background: #f0e0b0;
  }

  .notif-icon {
    font-size: 20px;
    flex-shrink: 0;
    margin-top: 2px;
  }

  .notif-body {
    flex: 1;
    min-width: 0;
  }

  .notif-title {
    font-size: 13px;
    font-weight: 700;
    color: var(--ink);
    margin-bottom: 2px;
  }

  .notif-item.read .notif-title {
    font-weight: 500;
    color: var(--ink-2);
  }

  .notif-msg {
    font-size: 12px;
    color: var(--ink-2);
    white-space: normal;
    word-break: break-word;
  }

  .notif-time {
    font-size: 11px;
    color: var(--ink-3);
    margin-top: 3px;
  }

  .notif-footer {
    border-top: 1.5px solid var(--paper-3);
    padding: 10px 16px;
    text-align: center;
    flex-shrink: 0;
  }

  .notif-footer a {
    font-size: 13px;
    font-weight: 700;
    color: var(--gold);
    text-decoration: none;
    transition: color 0.15s;
  }

  .notif-footer a:hover {
    color: var(--gold-dark);
  }
</style>

<header class="header" style="padding: 0 10px;">
  <a href="index.php" class="logo d-flex align-items-center">
    <img src="/surveysystem/public/user/assets/img/logo.png" alt="Logo" style="height: 32px; width: auto; margin-right: 6px;">
    <span class="d-none d-lg-block">Quick<em>Query</em></span>
  </a>
  <i class="bi bi-list toggle-sidebar-btn" id="toggleSidebar" style="margin-left: 96px;"></i>
<ul class="header-nav">  <!-- remove the style attribute entirely -->
    <!-- ── Notifications ── -->
    <li class="nav-item dropdown">
      <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown" id="userNotifBell">
<i class="bi bi-bell" style="color: var(--gold);"></i>        <span class="badge badge-number" id="userNotifCount" style="display:none;"></span>
      </a>

      <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications"
        style="min-width:340px; max-height:420px; overflow-y:auto;">

        <li class="dropdown-header d-flex justify-content-between align-items-center px-3 py-2">
          <span id="userNotifHeader" style="font-weight:600;">Notifications</span>
          <a href="#" id="userMarkAllRead"
            style="font-size:11px; text-decoration:none; background:var(--gold); color:#fff;
                    padding:3px 10px; border-radius:20px; font-weight:600;">
            Mark all read
          </a>
        </li>

        <li>
          <hr class="dropdown-divider m-0">
        </li>

        <div id="userNotifList">
          <li class="text-center text-muted py-3" style="font-size:13px; list-style:none;">
            Loading…
          </li>
        </div>

        <li>
          <hr class="dropdown-divider m-0">
        </li>
        <li class="dropdown-footer text-center py-2">
<a href="/surveysystem/public/user/notifications.php" style="font-size:13px; color:var(--ink-3); text-decoration:underline;">Show all notifications</a>        </li>
      </ul>
    </li>

    <!-- ── Profile ── -->
    <li class="nav-item dropdown pe-3">
      <a class="nav-link nav-profile dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <img id="topbarAvatarImg" src="<?= $_topbarAvatar ?>" alt="Profile">
        <span><?= $userName ?></span>
      </a>
      <ul class="dropdown-menu dropdown-menu-end profile">
        <li class="dropdown-header">
          <h6><?= htmlspecialchars($_SESSION['authUser']['username']) ?></h6>
          <span><?= htmlspecialchars($_SESSION['authUser']['fullName']) ?> · User</span>
        </li>
        <li>
          <hr class="dropdown-divider">
        </li>
        <li>
          <a class="dropdown-item" href="<?= $profileUrl ?>">
            <i class="bi bi-person"></i>
            My Profile
          </a>
        </li>
        <li>
          <hr class="dropdown-divider">
        </li>
        <li>
          <form action="../../app/controllers/userController.php" method="post">
            <button type="submit" name="logoutButton" class="dropdown-item">
              <i class="bi bi-box-arrow-right"></i>
              Sign Out
            </button>
          </form>
        </li>
      </ul>
    </li>

  </ul>

</header>

<script>

  (function() {
    const ENDPOINT = '/surveysystem/app/controllers/userNotificationController.php';

const icons = {
    survey_published:  'bi-megaphone',
    closing_soon:      'bi-clock',
    survey_closed:     'bi-lock',
    response_recorded: 'bi-check2-circle',
    pending_survey:    'bi-hourglass-split',  // ✅ ADD
    inactivity:        'bi-person-dash',       // ✅ ADD
};

    function timeAgo(dateStr) {
      const diff = Math.floor((Date.now() - new Date(dateStr)) / 1000);
      if (diff < 60) return diff + 's ago';
      if (diff < 3600) return Math.floor(diff / 60) + 'm ago';
      if (diff < 86400) return Math.floor(diff / 3600) + 'h ago';
      return Math.floor(diff / 86400) + 'd ago';
    }

function notifLabel(type) {
    switch (type) {
        case 'survey_published':  return '📋 New Survey Available';
        case 'closing_soon':      return '⏰ Closing Soon';
        case 'survey_closed':     return '🔒 Survey Closed';
        case 'response_recorded': return '✅ Response Recorded';
        case 'pending_survey':    return '📝 Pending Survey';      // ✅ ADD
        case 'inactivity':        return '💤 Reminder';            // ✅ ADD
        default:                  return '🔔 Notification';
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
          style="list-style:none; gap:.75rem; cursor:pointer;"
          data-id="${n.id}">
        <i class="bi ${icons[n.type] ?? 'bi-bell'}"
           style="font-size:20px; margin-top:2px; flex-shrink:0; color:var(--gold);"></i>
        <div style="min-width:0;">
          <div class="notif-title" style="font-size:13px; margin-bottom:2px;
               font-weight:${n.is_read == 0 ? '700' : '400'};">
            ${notifLabel(n.type)}
          </div>
          <div class="notif-msg" style="font-size:12px; color:#555; white-space:normal;
               word-break:break-word; font-weight:${n.is_read == 0 ? '600' : '400'};">
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
          const badge = document.getElementById('userNotifCount');
          if (data.unread > 0) {
            badge.textContent = data.unread > 99 ? '99+' : data.unread;
            badge.style.display = 'block';
          } else {
            badge.style.display = 'none';
          }

          document.getElementById('userNotifHeader').textContent =
            data.unread > 0 ?
            `You have ${data.unread} new notification${data.unread > 1 ? 's' : ''}` :
            'Notifications';

          const latest5 = (data.notifications ?? []).slice(0, 5);
          document.getElementById('userNotifList').innerHTML = renderItems(latest5);

document.querySelectorAll('#userNotifList .notification-item').forEach(function(item) {
            const clone = item.cloneNode(true);
            item.parentNode.replaceChild(clone, item);
            clone.addEventListener('click', function() {
              const id = this.dataset.id;
              const title = this.querySelector('.notif-title');
              const msg = this.querySelector('.notif-msg');
              if (title) title.style.fontWeight = '400';
              if (msg) msg.style.fontWeight = '400';
              this.classList.remove('unread');
              fetch(ENDPOINT + '?action=mark_one&id=' + id, { method: 'POST' })
                .then(() => loadNotifications());
            });
          });
        })
        .catch(() => {
          document.getElementById('userNotifList').innerHTML = `
          <li style="list-style:none; text-align:center;
                      padding:1rem; font-size:12px; color:#c00;">
            Could not load notifications.
          </li>`;
        });
    }

    document.getElementById('userMarkAllRead').addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();

      document.querySelectorAll('#userNotifList .notification-item').forEach(function(item) {
        item.classList.remove('unread');
        const title = item.querySelector('.notif-title');
        const msg = item.querySelector('.notif-msg');
        if (title) title.style.fontWeight = '400';
        if (msg) msg.style.fontWeight = '400';
      });
      document.getElementById('userNotifCount').style.display = 'none';
      document.getElementById('userNotifHeader').textContent = 'Notifications';

      fetch(ENDPOINT + '?action=mark_read', {
          method: 'POST'
        })
        .then(() => loadNotifications());
    });

    loadNotifications();
    setInterval(loadNotifications, 30000);
  })();
</script>