<?php if (!isset($_SESSION)) session_start();
$user     = $_SESSION['authUser'] ?? [];
$userName     = htmlspecialchars($user['username'] ?? $user['user_name'] ?? 'User');
$userFullName = htmlspecialchars($user['fullName'] ?? $user['name'] ?? '');
$userRole     = htmlspecialchars($user['role'] ?? 'User');

$profileUrl = '/surveysystem/public/user/profile.php';

// ── Use absolute URL — works from any page depth ──
$_topbarAvatar = '/surveysystem/assets/img/profile-img.jpg'; // default

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
    font-size: 22px;
    cursor: pointer;
    color: var(--ink);
    margin-left: 16px;
    line-height: 1;
  }

  .header-nav {
    display: flex;
    align-items: center;
    margin-left: auto;
    list-style: none;
    padding: 0;
    margin-bottom: 0;
    gap: 4px;
  }

  .header-nav .nav-icon {
    color: var(--gold);
    font-size: 20px;
    position: relative;
    padding: 4px 10px;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    text-decoration: none;
  }

  .header-nav .badge-number {
    background: transparent !important;
    color: #000 !important;
    font-size: 11px;
    font-weight: 800;
    position: absolute;
    top: -2px;
    right: 2px;
  }

  .header-nav .nav-icon:hover { color: #000; }

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

  /* Fix: dropdown must escape the fixed header stacking context */
.header-nav .dropdown-menu {
    background: #fff !important;
    border: 1.5px solid var(--paper-3) !important;
    box-shadow: 0 8px 24px rgba(15,14,13,0.12) !important;
    border-radius: var(--radius) !important;
    padding: 6px 0 !important;
    min-width: 200px;
    z-index: 9999 !important;
  }

  .header-nav .dropdown-header {
    padding: 8px 16px 4px;
  }

  .header-nav .dropdown-header h6 {
    font-size: 14px;
    font-weight: 600;
    color: var(--ink);
    margin: 0;
  }

  .header-nav .dropdown-header span {
    font-size: 12px;
    color: var(--ink-3);
  }

  .dropdown-item {
    display: flex !important;
    align-items: center;
    gap: 8px;
    padding: 8px 16px !important;
    font-size: 13.5px !important;
    color: var(--ink-2) !important;
    font-weight: 500;
    background: none !important;
    border: none;
    width: 100%;
    text-decoration: none;
    transition: background 0.15s;
    cursor: pointer;
    font-family: 'DM Sans', sans-serif;
  }

  .dropdown-item:hover {
    background: var(--paper) !important;
    color: var(--gold) !important;
  }
.dropdown-menu.profile .dropdown-header {
  text-align: center;
  padding: 10px 15px;
}

.dropdown-menu.profile .dropdown-header h6 {
  font-size: 15px;
  font-weight: 600;
  margin-bottom: 2px;
  color: var(--ink);
}

.dropdown-menu.profile .dropdown-header span {
  font-size: 12px;
  color: #6c757d;
}
  .dropdown-divider { border-color: var(--paper-3) !important; }
</style>

<header class="header">

  <a href="index.php" class="logo">
    <span>Quick<em>Query</em></span>
  </a>
  <i class="bi bi-list toggle-sidebar-btn" id="toggleSidebar"></i>

  <ul class="header-nav">

<!-- Notifications -->
    <li class="nav-item dropdown">
      <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown" id="userNotifBell">
        <i class="bi bi-bell"></i>
        <span class="badge-number" id="userNotifCount" style="display:none;"></span>
      </a>

      <ul class="dropdown-menu dropdown-menu-end notifications"
          style="min-width:340px; max-height:420px; overflow-y:auto;">

        <li class="dropdown-header d-flex justify-content-between align-items-center px-3 py-2">
          <span id="userNotifHeader" style="font-weight:600;">Notifications</span>
          <a href="#" id="userMarkAllRead"
             class="badge rounded-pill bg-primary p-2 ms-2"
             style="font-size:11px; text-decoration:none;">Mark all read</a>
        </li>

        <li><hr class="dropdown-divider m-0"></li>

        <div id="userNotifList">
          <li class="text-center text-muted py-3" style="font-size:13px; list-style:none;">
            Loading…
          </li>
        </div>

        <li><hr class="dropdown-divider m-0"></li>
        <li class="dropdown-footer text-center py-2">
          <a href="notifications.php" style="font-size:13px;">Show all notifications</a>
        </li>

      </ul>
    </li>

<script>
(function () {
  const ENDPOINT = '/surveysystem/app/controllers/userNotificationController.php';

  const icons = {
    survey_published:  'bi-megaphone text-primary',
    survey_closed:     'bi-lock text-danger',
    response_recorded: 'bi-check2-circle text-success'
  };

  function timeAgo(dateStr) {
    const diff = Math.floor((Date.now() - new Date(dateStr)) / 1000);
    if (diff <    60) return diff + 's ago';
    if (diff <  3600) return Math.floor(diff / 60)   + 'm ago';
    if (diff < 86400) return Math.floor(diff / 3600) + 'h ago';
    return Math.floor(diff / 86400) + 'd ago';
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
          data.unread > 0
            ? `You have ${data.unread} new notification${data.unread > 1 ? 's' : ''}`
            : 'Notifications';

        const list = document.getElementById('userNotifList');
        if (!data.notifications || !data.notifications.length) {
          list.innerHTML = `
            <li style="list-style:none; text-align:center;
                        padding:1.5rem; font-size:13px; color:#999;">
              No notifications yet
            </li>`;
          return;
        }

        list.innerHTML = data.notifications.map(n => `
          <li class="notification-item d-flex align-items-start px-3 py-2
                     ${n.is_read == 0 ? 'bg-light' : ''}"
              style="list-style:none; gap:.75rem;">
            <i class="bi ${icons[n.type] ?? 'bi-bell text-secondary'}"
               style="font-size:20px; margin-top:2px; flex-shrink:0;"></i>
            <div style="min-width:0;">
              <div style="font-size:13px; font-weight:600; margin-bottom:2px;">
                ${n.type === 'survey_published'  ? '📋 New Survey Available' :
                  n.type === 'survey_closed'     ? '🔒 Survey Closed'        :
                  n.type === 'response_recorded' ? '✅ Response Recorded'    : '🔔 Notification'}
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
      })
      .catch(() => {
        document.getElementById('userNotifList').innerHTML = `
          <li style="list-style:none; text-align:center;
                      padding:1rem; font-size:12px; color:#c00;">
            Could not load notifications.
          </li>`;
      });
  }

  document.getElementById('userNotifBell').addEventListener('click', function () {
    fetch(ENDPOINT + '?action=mark_read', { method: 'POST' })
      .then(() => setTimeout(loadNotifications, 250));
  });

  document.getElementById('userMarkAllRead').addEventListener('click', function (e) {
    e.preventDefault();
    e.stopPropagation();
    fetch(ENDPOINT + '?action=mark_read', { method: 'POST' })
      .then(() => loadNotifications());
  });

  loadNotifications();
  setInterval(loadNotifications, 30000);

})();
</script>
    <!-- Profile -->
    <li class="nav-item dropdown pe-3">
      <a class="nav-link nav-profile dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <!-- ── Dynamic avatar with DB fallback ── -->
        <img id="topbarAvatarImg" src="<?= $_topbarAvatar ?>" alt="Profile">
<span><?= $userName ?></span>
      </a>
<ul class="dropdown-menu dropdown-menu-end profile">
        <li class="dropdown-header">
          <h6><?= htmlspecialchars($_SESSION['authUser']['username']) ?></h6>
  <span><?= htmlspecialchars($_SESSION['authUser']['fullName']) ?> · User</span>
</li>
        <li><hr class="dropdown-divider"></li>
        <li>
          <a class="dropdown-item" href="<?= $profileUrl ?>">
            <i class="bi bi-person"></i>
            My Profile
          </a>
        </li>
        <li><hr class="dropdown-divider"></li>
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