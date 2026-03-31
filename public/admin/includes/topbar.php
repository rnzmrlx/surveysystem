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
    color: var(--gold); /* Icon is Gold */
    font-size: 20px;
    position: relative;
    padding: 4px 8px;
    transition: all 0.2s ease;
  }

  /* NOTIFICATION NUMBER - Black */
  .header-nav .badge-number {
    background: transparent !important;
    color: #000000 !important; /* Number is solid Black */
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

      <li class="nav-item dropdown">
        <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
          <i class="bi bi-bell"></i>
          <span class="badge badge-number">4</span>
        </a>

        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications">
          <li class="dropdown-header">
            You have 4 new notifications
            <a href="#"><span class="badge rounded-pill bg-primary p-2 ms-2">View all</span></a>
          </li>
          <li><hr class="dropdown-divider"></li>
          <li class="notification-item">
            <i class="bi bi-exclamation-circle text-warning"></i>
            <div>
              <h4>System Update</h4>
              <p>Maintenance scheduled for tonight</p>
              <p>30 min. ago</p>
            </div>
          </li>
          <li><hr class="dropdown-divider"></li>
          <li class="dropdown-footer">
            <a href="#">Show all notifications</a>
          </li>
        </ul>
      </li>

      <li class="nav-item dropdown pe-3">
        <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
          <img src="assets/img/profile-img.jpg" alt="Profile" class="rounded-circle" style="border: 1px solid var(--paper-3)">
          <span class="d-none d-md-block dropdown-toggle ps-2"><?php echo htmlspecialchars($_SESSION['authUser']['fullName']); ?></span>
        </a>

        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
          <li class="dropdown-header">
            <h6><?php echo htmlspecialchars($_SESSION['authUser']['fullName']); ?></h6>
            <span>Admin</span>
          </li>
          <li><hr class="dropdown-divider"></li>
          <li>
            <a class="dropdown-item d-flex align-items-center" href="users-profile.html">
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
      </li>

    </ul>
  </nav>

</header>