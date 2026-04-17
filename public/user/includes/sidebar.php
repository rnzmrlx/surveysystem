<?php $page = basename($_SERVER['PHP_SELF']); ?>

<style>
.sidebar {
  position: fixed;
  top: 60px;
  left: 0;
  width: 260px;
  height: calc(100vh - 60px);
  background: var(--paper-2);
  border-right: 1.5px solid var(--paper-3);
  padding: 20px;
}

.sidebar a {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 14px;
  border-radius: var(--radius);
  text-decoration: none;
  font-weight: 600;
  color: var(--ink);
  transition: 0.2s;
}

.sidebar a i {
  color: var(--ink);
}

.sidebar a:hover {
  background: var(--paper);
  color: var(--gold);
}

.sidebar a.active {
  background: var(--paper);
  color: var(--gold);
}

.sidebar a.active i {
  color: var(--gold);
}

.sidebar-heading {
  margin: 15px 0 8px;
  font-size: 11px;
  text-transform: uppercase;
  color: var(--ink-3);
  font-weight: 700;
}
</style>

<aside class="sidebar">

  <div class="sidebar-heading">MAIN</div>

  <a href="index.php" class="<?= $page == 'index.php' ? 'active' : '' ?>">
    <i class="bi bi-grid"></i> Dashboard
  </a>

  <a href="mysurvey.php" class="<?= $page == 'mysurvey.php' ? 'active' : '' ?>">
    <i class="bi bi-clipboard-data"></i> My Surveys
  </a>

  <a href="results.php" class="<?= $page == 'results.php' ? 'active' : '' ?>">
    <i class="bi bi-bar-chart"></i> My Results
  </a>

</aside>