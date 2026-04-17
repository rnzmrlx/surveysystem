<?php
if (!isset($_SESSION)) session_start();

$user = $_SESSION['authUser'] ?? [];
$userName = $user['user_name'] ?? $user['name'] ?? 'User';
$userRole = $user['role'] ?? '';
?>

<style>
.header {
  height: 60px;
  background: var(--paper);
  border-bottom: 1.5px solid var(--paper-3);
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 20px;
  position: fixed;
  top: 0;
  left: 260px;
  right: 0;
  z-index: 1000;
}

.header .brand {
  font-family: 'DM Serif Display', serif;
  font-size: 20px;
  color: var(--ink);
}

.header .brand em {
  color: var(--gold);
  font-style: normal;
}

.topbar-right {
  display: flex;
  align-items: center;
  gap: 18px;
}

.user-box {
  display: flex;
  flex-direction: column;
  text-align: right;
}

.user-box .name {
  font-weight: 600;
  font-size: 14px;
  color: var(--ink);
}

.user-box .role {
  font-size: 11px;
  color: var(--ink-3);
}

.dropdown-menu {
  border: 1.5px solid var(--paper-3) !important;
  border-radius: var(--radius) !important;
  box-shadow: var(--shadow) !important;
}
</style>

<header class="header">

  <div class="brand">
    Survey<em>System</em>
  </div>

  <div class="topbar-right">

    <div class="dropdown">

      <a class="nav-link" data-bs-toggle="dropdown" style="cursor:pointer;">
        <i class="bi bi-bell" style="color:var(--gold); font-size:18px;"></i>
      </a>

    </div>

    <div class="dropdown">

      <a class="nav-link d-flex align-items-center" data-bs-toggle="dropdown">
        <div class="user-box">
          <span class="name"><?= htmlspecialchars($userName) ?></span>
          <span class="role"><?= htmlspecialchars($userRole) ?></span>
        </div>
      </a>

      <ul class="dropdown-menu dropdown-menu-end">
        <li><a class="dropdown-item" href="profile.php">Profile</a></li>
        <li>
          <form method="POST" action="../../app/controllers/logout.php">
            <button class="dropdown-item" name="logoutButton">Logout</button>
          </form>
        </li>
      </ul>

    </div>

  </div>

</header>