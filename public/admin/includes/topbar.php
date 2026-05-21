<?php
// ── Resolve topbar avatar path ─────────────────────────────────────────────
$_sessionAvatar = $_SESSION['authUser']['avatar'] ?? '';
$_topbarAvatarSrc = !empty($_sessionAvatar)
  ? '../../' . htmlspecialchars($_sessionAvatar)
  : 'assets/img/profile-img.jpg';

$_topbarInitials = strtoupper(
  substr($_SESSION['authUser']['firstName'] ?? 'U', 0, 1) .
    substr($_SESSION['authUser']['lastName']  ?? 'S', 0, 1)
);
?>

<style>
  .header {
    background: var(--paper) !important;
    border-bottom: 1.5px solid var(--paper-3);
    height: 60px;
    padding-left: 20px;
  }
@media (max-width: 991px) {
  .header > div:first-child {
    flex: 1;
  }
  .header-nav {
    flex: 0;
    margin-right: 20px !important;
  }
}
@media (min-width: 1200px) {
    .header-nav {
        margin-right: 2rem !important;
    }
}
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

  .header-nav .nav-icon {
    color: var(--gold);
    font-size: 20px;
    position: relative;
    padding: 4px 8px;
    transition: all 0.2s ease;
  }

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

  .header-nav .nav-icon:hover {
    color: #000000;
  }

  .header-nav .nav-icon:hover .badge-number {
    color: var(--gold) !important;
  }

  .header-nav .nav-profile span {
    font-family: 'DM Sans', sans-serif;
    font-weight: 600;
    color: var(--ink-2);
    font-size: 14px;
}
  .dropdown-menu {
    background: #fff;
    border: 1.5px solid var(--paper-3) !important;
    box-shadow: var(--shadow) !important;
    border-radius: var(--radius) !important;
  }
.dropdown-item:hover {
  background: var(--paper) !important;
  color: var(--gold) !important;
}
  .topbar-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    object-fit: cover;
    border: 1.5px solid var(--paper-3);
  }

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

<div class="d-flex align-items-center w-100-sm">
  <a href="index.php" class="logo d-flex align-items-center">
  <img src="assets/img/logo.png" alt="Logo" style="height: 32px; width: auto; margin-right: 6px;">
  <span class="d-none d-lg-block">Quick<em>Query</em></span>
</a>
<i class="bi bi-list toggle-sidebar-btn" style="margin-left: 0px;"></i>  </div>

<nav class="header-nav ms-auto" style="margin-right: 6px;">    <ul class="d-flex align-items-center">

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
              style="font-size:11px; text-decoration:none; background:var(--gold); color:#fff;
                      padding:3px 10px; border-radius:20px; font-weight:600;">
              Mark all read
            </a>
          </li>

          <li>
            <hr class="dropdown-divider m-0">
          </li>

          <div id="notifList">
            <li class="text-center text-muted py-3" style="font-size:13px; list-style:none;">
              Loading…
            </li>
          </div>

          <li>
            <hr class="dropdown-divider m-0">
          </li>
          <li class="dropdown-footer text-center py-2">
<a href="notifications.php" style="font-size:13px; color:var(--ink-3); text-decoration:underline;">Show all notifications</a>          
</li>

        </ul>
      </li>

      <!-- ── Profile ── -->
      <li class="nav-item dropdown pe-3">
        <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
          <img id="topbarAvatarImg"
            src="<?= $_topbarAvatarSrc ?>"
            alt="Profile"
            class="topbar-avatar rounded-circle">
          <span class="dropdown-toggle ps-2"><?= htmlspecialchars($_SESSION['authUser']['username']) ?></span>
        </a>

        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
          <li class="dropdown-header">
            <h6><?= htmlspecialchars($_SESSION['authUser']['username']) ?></h6>
            <span><?= htmlspecialchars($_SESSION['authUser']['fullName']) ?> · Admin</span>
          </li>
          <li>
            <hr class="dropdown-divider">
          </li>
          <li>
            <a class="dropdown-item d-flex align-items-center" href="/surveysystem/public/admin/profile.php">
              <i class="bi bi-person"></i>
              <span>My Profile</span>
            </a>
          </li>
          <li>
            <hr class="dropdown-divider">
          </li>
          <li>
            <form action="../../app/controllers/adminController.php" method="post">
              <button type="submit" name="logoutButton" class="dropdown-item d-flex align-items-center">
                <i class="bi bi-box-arrow-right"></i>
                <span>Sign Out</span>
              </button>
            </form>
          </li>
        </ul>
      </li>

    </ul>
  </nav>

</header>

<script>
  (function() {
    const ENDPOINT = '../../app/controllers/notificationController.php';

    const icons = {
      accessed: 'bi-eye text-info',
      answered: 'bi-check2-circle text-success',
      auto_closed: 'bi-clock-history text-warning',
      user_registered: 'bi-person-plus text-primary',
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
        case 'answered':
          return '📋 Survey Answered';
        case 'accessed':
          return '👁 Survey Accessed';
        case 'auto_closed':
          return '⏰ Survey Auto-Closed';
        case 'user_registered':
          return '👤 New User Registered';
        default:
          return '🔔 Notification';
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
        <i class="bi ${icons[n.type] ?? 'bi-bell text-secondary'}"
           style="font-size:20px; margin-top:2px; flex-shrink:0;"></i>
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
          const badge = document.getElementById('notifCount');
          if (data.unread > 0) {
            badge.textContent = data.unread > 99 ? '99+' : data.unread;
            badge.style.display = 'block';
          } else {
            badge.style.display = 'none';
          }

          document.getElementById('notifHeader').textContent =
            data.unread > 0 ?
            `You have ${data.unread} new notification${data.unread > 1 ? 's' : ''}` :
            'Notifications';

          const latest5 = (data.notifications ?? []).slice(0, 5);
          document.getElementById('notifList').innerHTML = renderItems(latest5);

          document.querySelectorAll('#notifList .notification-item').forEach(function(item) {
            item.addEventListener('click', function() {
              var id = this.dataset.id;
              var title = this.querySelector('.notif-title');
              var msg = this.querySelector('.notif-msg');
              if (title) title.style.fontWeight = '400';
              if (msg) msg.style.fontWeight = '400';
              this.classList.remove('unread');
              fetch(ENDPOINT + '?action=mark_one&id=' + id, {
                  method: 'POST'
                })
                .then(() => loadNotifications());
            });
          });
        })
        .catch(() => {
          document.getElementById('notifList').innerHTML = `
          <li style="list-style:none; text-align:center;
                      padding:1rem; font-size:12px; color:#c00;">
            Could not load notifications.
          </li>`;
        });
    }

    document.getElementById('notifBell').addEventListener('click', function() {
      loadNotifications();
    });

    document.getElementById('markAllRead').addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();

      document.querySelectorAll('#notifList .notification-item').forEach(function(item) {
        item.classList.remove('unread');
        var title = item.querySelector('.notif-title');
        var msg = item.querySelector('.notif-msg');
        if (title) title.style.fontWeight = '400';
        if (msg) msg.style.fontWeight = '400';
      });
      document.getElementById('notifCount').style.display = 'none';
      document.getElementById('notifHeader').textContent = 'Notifications';

      fetch(ENDPOINT + '?action=mark_read', {
          method: 'POST'
        })
        .then(() => loadNotifications());
    });

    loadNotifications();
    setInterval(loadNotifications, 30000);

  })();

  document.querySelector('.toggle-sidebar-btn').addEventListener('click', function() {
    document.body.classList.toggle('sidebar-hidden');
    localStorage.setItem('sidebar-hidden', document.body.classList.contains('sidebar-hidden'));
  });

  (function() {
    if (localStorage.getItem('sidebar-hidden') === 'true') {
      document.body.classList.add('sidebar-hidden');
    }
  })();
</script>