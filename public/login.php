<?php session_start() ?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Login · QuickQuery</title>
<link rel="icon" type="image/png" href="/surveysystem/public/user/assets/img/logo.png">
  <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
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
      --rose: #a02c2c;
      --radius: 12px;
      --shadow: 0 4px 24px rgba(15, 14, 13, 0.09);
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
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2rem 1rem;
    }

    .login-wrap {
      position: relative;
      z-index: 1;
      width: 100%;
      max-width: 440px;
    }

    /* Brand */
    .brand {
      text-align: center;
      margin-bottom: 2rem;
    }

    .brand h1 {
      font-family: 'DM Serif Display', serif;
      font-size: 2rem;
      font-weight: 400;
      line-height: 1;
    }

    .brand h1 em {
      font-style: italic;
      color: var(--gold);
    }

    .brand p {
      font-size: 13px;
      color: var(--ink-3);
      margin-top: 6px;
    }

    /* Card */
    .card {
      background: #fff;
      border: 1.5px solid var(--paper-3);
      border-radius: 16px;
      padding: 2.25rem 2.5rem 2.5rem;
      box-shadow: var(--shadow);
      position: relative;
      overflow: hidden;
    }

    .card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 3px;
      background: var(--gold);
      border-radius: 2px 2px 0 0;
    }

    .card-title {
      font-family: 'DM Serif Display', serif;
      font-size: 1.4rem;
      font-weight: 400;
      margin-bottom: 4px;
    }

    .card-title em {
      font-style: italic;
      color: var(--gold);
    }

    .card-sub {
      font-size: 13px;
      color: var(--ink-3);
      margin-bottom: 2rem;
    }

    /* Form */
    .field {
      display: flex;
      flex-direction: column;
      gap: 5px;
      margin-bottom: 1.1rem;
    }

    .field:last-of-type {
      margin-bottom: 0;
    }

    .field label {
      font-size: 11px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      color: var(--ink-3);
    }

    .field label .req {
      color: var(--rose);
      margin-left: 2px;
    }

    .input-wrap {
      position: relative;
    }

    .input-wrap svg.icon {
      position: absolute;
      left: 12px;
      top: 50%;
      transform: translateY(-50%);
      width: 15px;
      height: 15px;
      stroke: var(--ink-3);
      fill: none;
      stroke-width: 2;
      pointer-events: none;
    }

    .input-wrap input {
      width: 100%;
      background: var(--paper-2);
      border: 1.5px solid var(--paper-3);
      border-radius: var(--radius);
      padding: 10px 40px 10px 36px;
      font-family: 'DM Sans', sans-serif;
      font-size: 13.5px;
      color: var(--ink);
      outline: none;
      transition: border-color 0.15s, background 0.15s;
    }

    .input-wrap input::placeholder {
      color: var(--ink-3);
    }

    .input-wrap input:focus {
      border-color: var(--gold);
      background: #fff;
    }

    /* Toggle password visibility */
    .toggle-pw {
      position: absolute;
      right: 12px;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      cursor: pointer;
      padding: 0;
      color: var(--ink-3);
      transition: color 0.15s;
      display: flex;
      align-items: center;
    }

    .toggle-pw:hover {
      color: var(--ink);
    }

    .toggle-pw svg {
      width: 15px;
      height: 15px;
      stroke: currentColor;
      fill: none;
      stroke-width: 2;
    }

    /* Remember row */
    .remember-row {
      display: flex;
      align-items: center;
      gap: 8px;
      margin: 1.25rem 0;
    }

    .remember-row input[type="checkbox"] {
      width: 15px;
      height: 15px;
      accent-color: var(--gold);
      cursor: pointer;
    }

    .remember-row label {
      font-size: 13px;
      color: var(--ink-3);
      cursor: pointer;
    }

    /* Submit button */
    .btn-login {
      width: 100%;
      background: var(--ink);
      color: var(--paper);
      border: none;
      border-radius: var(--radius);
      padding: 11px 16px;
      font-family: 'DM Sans', sans-serif;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.15s, transform 0.1s;
      margin-bottom: 1.25rem;
    }

    .btn-login:hover {
      background: var(--ink-2);
    }

    .btn-login:active {
      transform: scale(0.99);
    }

    /* Register link */
    .register-link {
      text-align: center;
      font-size: 13px;
      color: var(--ink-3);
    }

    .register-link a {
      color: var(--gold);
      font-weight: 600;
      text-decoration: none;
      transition: color 0.15s;
    }

    .register-link a:hover {
      color: var(--gold-dark);
    }

    /* Footer note */
    .login-footer {
      text-align: center;
      margin-top: 1.5rem;
      font-size: 12px;
      color: var(--ink-3);
    }
  </style>
</head>

<body>
  <div class="login-wrap">

    <!-- Brand -->
    <div class="brand">
      <h1>Quick<em>Query</em></h1>
      <p>University Survey System</p>
    </div>

    <!-- Card -->
    <div class="card">
      <div class="card-title">Welcome <em>back</em></div>
      <div class="card-sub">Enter your credentials to access the dashboard</div>

      <form action="/surveysystem/app/controllers/loginController.php" method="post" enctype="multipart/form-data" autocomplete="off" novalidate>

        <div class="field">
          <label>Username <span class="req">*</span></label>
          <div class="input-wrap">
            <svg class="icon" viewBox="0 0 24 24">
              <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" />
              <circle cx="12" cy="7" r="4" />
            </svg>
            <input type="text" name="username" placeholder="Enter your username" required>
          </div>
        </div>

        <div class="field">
          <label>Password <span class="req">*</span></label>
          <div class="input-wrap">
            <svg class="icon" viewBox="0 0 24 24">
              <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
              <path d="M7 11V7a5 5 0 0110 0v4" />
            </svg>
            <input type="password" name="password" id="passwordInput" placeholder="Enter your password" required>
            <button type="button" class="toggle-pw" onclick="togglePw('passwordInput', 'eyeIcon')">
              <svg id="eyeIcon" viewBox="0 0 24 24">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                <circle cx="12" cy="12" r="3" />
              </svg>
            </button>
          </div>
        </div>

        <div class="remember-row">
          <input type="checkbox" name="remember" value="true" id="rememberMe">
          <label for="rememberMe">Remember me</label>
        </div>

        <button type="submit" name="loginButton" class="btn-login">Sign In</button>

        <div class="register-link">
          Don't have an account? <a href="registration.php">Create one</a>
        </div>

      </form>
    </div>

    <div class="login-footer">Academic Term · A.Y. 2025–2026</div>

  </div>

  <script>
    function togglePw(inputId, iconId) {
      const input = document.getElementById(inputId);
      const isHidden = input.type === 'password';
      input.type = isHidden ? 'text' : 'password';
      document.getElementById(iconId).innerHTML = isHidden ?
        '<path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/>' :
        '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
    }
  </script>

  <?php
  if (isset($_SESSION['message']) && $_SESSION['code'] != '') {
  ?>
    <script>
      const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
          toast.onmouseenter = Swal.stopTimer;
          toast.onmouseleave = Swal.resumeTimer;
        }
      });
      Toast.fire({
        icon: "<?php echo $_SESSION['code']; ?>",
        title: "<?php echo $_SESSION['message']; ?>"
      });
    </script>
  <?php
    unset($_SESSION['message']);
    unset($_SESSION['code']);
  }
  ?>

</body>

</html>