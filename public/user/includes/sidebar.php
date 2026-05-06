<?php $page = basename($_SERVER['PHP_SELF']); ?>

<style>
  .sidebar {
    position: fixed;
    top: 60px;
    left: 0;
    bottom: 0;
    width: 260px;
    z-index: 996;
    transition: all 0.3s;
    padding: 20px;
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: var(--paper-3) transparent;
    background: var(--paper-2);
    border-right: 1.5px solid var(--paper-3);
  }

  .sidebar-nav {
    padding: 0;
    margin: 0;
    list-style: none;
  }

  .sidebar-nav .nav-heading {
    font-size: 11px;
    text-transform: uppercase;
    color: #7a776f;
    font-weight: 700;
    margin: 15px 0 10px 15px;
    letter-spacing: 0.1em;
    font-family: 'DM Sans', sans-serif;
  }

  .sidebar-nav .nav-item {
    margin-bottom: 5px;
  }

  .sidebar-nav .nav-link.collapsed {
    display: flex;
    align-items: center;
    font-size: 14px;
    font-weight: 600;
    color: #000000;
    transition: all 0.3s ease;
    background: transparent;
    padding: 12px 15px;
    border-radius: var(--radius);
    font-family: 'DM Sans', sans-serif;
    text-decoration: none;
  }

  .sidebar-nav .nav-link.collapsed i {
    font-size: 18px;
    margin-right: 12px;
    color: #000000;
    transition: all 0.3s ease;
  }

  .sidebar-nav .nav-link:not(.collapsed) {
    display: flex;
    align-items: center;
    font-size: 14px;
    font-weight: 600;
    color: var(--gold);
    background: var(--paper-3);
    padding: 12px 15px;
    border-radius: var(--radius);
    font-family: 'DM Sans', sans-serif;
    text-decoration: none;
    transition: all 0.3s ease;
  }

  .sidebar-nav .nav-link:not(.collapsed) i {
    font-size: 18px;
    margin-right: 12px;
    color: var(--gold);
  }

  .sidebar-nav .nav-link.collapsed:hover,
  .sidebar-nav .nav-link.collapsed:hover i {
    color: var(--gold);
    background: var(--paper);
  }

  #main {
    margin-left: 260px;
    transition: all 0.3s;
    padding: 20px;
    background: var(--paper);
    min-height: 100vh;
  }
body.sidebar-hidden .sidebar {
  left: -260px;
}

body.sidebar-hidden .main-wrapper {
  margin-left: 0;
  width: 100%;
}

@media (max-width: 1199px) {
    .sidebar { left: 0; }
    #main { margin-left: 260px; }
  }
</style>

<aside id="sidebar" class="sidebar">

  <ul class="sidebar-nav" id="sidebar-nav">

   <li class="nav-heading">Main</li>

    <li class="nav-item">
      <a class="nav-link <?= ($page == 'index.php') ? '' : 'collapsed' ?>" href="index.php">
        <i class="bi bi-grid"></i>
        <span>Dashboard</span>
      </a>
    </li>

    <li class="nav-heading">Activity</li>

    <li class="nav-item">
      <a class="nav-link <?= ($page == 'mysurvey.php') ? '' : 'collapsed' ?>" href="mysurvey.php">
        <i class="bi bi-clipboard-data"></i>
        <span>My Surveys</span>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link <?= ($page == 'results.php') ? '' : 'collapsed' ?>" href="results.php">
        <i class="bi bi-bar-chart"></i>
        <span>My Results</span>
      </a>
    </li>

  </ul>

</aside>

<main id="main" class="main">