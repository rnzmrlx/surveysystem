<?php session_start() ?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Register · QuickQuery</title>
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

    .register-wrap {
      width: 100%;
      max-width: 560px;
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

    /* Section divider */
    .form-section {
      font-size: 10.5px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      color: var(--ink-3);
      padding-bottom: 8px;
      border-bottom: 1.5px solid var(--paper-2);
      margin-bottom: 1rem;
      margin-top: 1.5rem;
    }

    .form-section:first-of-type {
      margin-top: 0;
    }

    /* Grid rows */
    .form-row {
      display: grid;
      gap: 1rem;
      margin-bottom: 1rem;
    }

    .form-row.col-2 {
      grid-template-columns: 1fr 1fr;
    }

    .form-row.col-1 {
      grid-template-columns: 1fr;
    }

    /* Fields */
    .field {
      display: flex;
      flex-direction: column;
      gap: 5px;
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

    .input-wrap input.no-left-icon {
      padding-left: 14px;
    }

    .input-wrap input::placeholder {
      color: var(--ink-3);
    }

    .input-wrap input:focus {
      border-color: var(--gold);
      background: #fff;
    }

    .input-wrap input.invalid {
      border-color: var(--rose);
    }

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

    .field-error {
      font-size: 11.5px;
      color: var(--rose);
      display: none;
    }

    .field-error.show {
      display: block;
    }

    /* Terms */
    .terms-row {
      display: flex;
      align-items: flex-start;
      gap: 9px;
      margin: 1.25rem 0;
    }

    .terms-row input[type="checkbox"] {
      width: 15px;
      height: 15px;
      margin-top: 2px;
      accent-color: var(--gold);
      cursor: pointer;
      flex-shrink: 0;
    }

    .terms-row label {
      font-size: 13px;
      color: var(--ink-3);
      cursor: pointer;
      line-height: 1.5;
    }

    .terms-row label a {
      color: var(--gold);
      text-decoration: none;
      font-weight: 600;
    }

    .terms-row label a:hover {
      color: var(--gold-dark);
    }

    /* Submit */
    .btn-register {
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

    .btn-register:hover {
      background: var(--ink-2);
    }

    .btn-register:active {
      transform: scale(0.99);
    }

    .login-link {
      text-align: center;
      font-size: 13px;
      color: var(--ink-3);
    }

    .login-link a {
      color: var(--gold);
      font-weight: 600;
      text-decoration: none;
      transition: color 0.15s;
    }

    .login-link a:hover {
      color: var(--gold-dark);
    }

    .register-footer {
      text-align: center;
      margin-top: 1.5rem;
      font-size: 12px;
      color: var(--ink-3);
    }

    @media (max-width: 520px) {
      .card {
        padding: 1.75rem 1.25rem 2rem;
      }

      .form-row.col-2 {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>

<body>
  <div class="register-wrap">

    <div class="brand">
      <h1>Quick<em>Query</em></h1>
      <p>University Survey System</p>
    </div>

    <!-- Card -->
    <div class="card">
      <div class="card-title">Create an <em>Account</em></div>
      <div class="card-sub">Enter your details to get started</div>

      <form id="registerForm" action="/surveysystem/app/controllers/loginController.php" method="post" enctype="multipart/form-data" autocomplete="off" novalidate>

        <!-- Personal Info -->
        <div class="form-section">Personal Information</div>

        <div class="form-row col-2">
          <div class="field">
            <label>First Name <span class="req">*</span></label>
            <div class="input-wrap">
              <input type="text" name="firstName" placeholder="Abigail" class="no-left-icon" required>
            </div>
            <span class="field-error" id="err-firstName">Please enter your first name.</span>
          </div>
          <div class="field">
            <label>Middle Name</label>
            <div class="input-wrap">
              <input type="text" name="middleName" placeholder="Lapinid" class="no-left-icon">
            </div>
          </div>
        </div>

        <div class="form-row col-1">
          <div class="field">
            <label>Last Name <span class="req">*</span></label>
            <div class="input-wrap">
              <input type="text" name="lastName" placeholder="Rabosa" class="no-left-icon" required>
            </div>
            <span class="field-error" id="err-lastName">Please enter your last name.</span>
          </div>
        </div>

        <!-- Account Info -->
        <div class="form-section">Account Details</div>

        <div class="form-row col-1">
          <div class="field">
            <label>Email Address <span class="req">*</span></label>
            <div class="input-wrap">
              <svg class="icon" viewBox="0 0 24 24">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                <polyline points="22,6 12,13 2,6" />
              </svg>
              <input type="email" name="emailAddress" placeholder="abilex@ustp.edu.ph" required>
            </div>
            <span class="field-error" id="err-email">Please enter a valid email.</span>
          </div>
        </div>

        <div class="form-row col-1">
          <div class="field">
            <label>Username <span class="req">*</span></label>
            <div class="input-wrap">
              <svg class="icon" viewBox="0 0 24 24">
                <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2" />
                <circle cx="12" cy="7" r="4" />
              </svg>
              <input type="text" name="username" placeholder="abilex" required>
            </div>
            <span class="field-error" id="err-username">Please choose a username.</span>
          </div>
        </div>

        <div class="form-row col-2">
          <div class="field">
            <label>Password <span class="req">*</span></label>
            <div class="input-wrap">
              <svg class="icon" viewBox="0 0 24 24">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                <path d="M7 11V7a5 5 0 0110 0v4" />
              </svg>
              <input type="password" name="password" id="password" placeholder="••••••••••" required>
              <button type="button" class="toggle-pw" onclick="togglePw('password','eyeIcon1')">
                <svg id="eyeIcon1" viewBox="0 0 24 24">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                  <circle cx="12" cy="12" r="3" />
                </svg>
              </button>
            </div>
            <span class="field-error" id="err-password">Please enter a password.</span>
          </div>
          <div class="field">
            <label>Confirm Password <span class="req">*</span></label>
            <div class="input-wrap">
              <svg class="icon" viewBox="0 0 24 24">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                <path d="M7 11V7a5 5 0 0110 0v4" />
              </svg>
              <input type="password" name="confirmPassword" id="confirmPassword" placeholder="••••••••••" required>
              <button type="button" class="toggle-pw" onclick="togglePw('confirmPassword','eyeIcon2')">
                <svg id="eyeIcon2" viewBox="0 0 24 24">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                  <circle cx="12" cy="12" r="3" />
                </svg>
              </button>
            </div>
            <span class="field-error" id="err-confirm">Passwords do not match.</span>
          </div>
        </div>

        <!-- Address -->
        <div class="form-section">Address</div>

        <div class="form-row col-2">
          <div class="field">
            <label>Street <span class="req">*</span></label>
            <div class="input-wrap">
              <input type="text" name="street" placeholder="Rose St." class="no-left-icon" required>
            </div>
            <span class="field-error" id="err-street">Please enter your street.</span>
          </div>
          <div class="field">
            <label>Barangay <span class="req">*</span></label>
            <div class="input-wrap">
              <input type="text" name="barangay" placeholder="Cugman" class="no-left-icon" required>
            </div>
            <span class="field-error" id="err-barangay">Please enter your barangay.</span>
          </div>
        </div>

        <div class="form-row col-1">
          <div class="field">
            <label>City <span class="req">*</span></label>
            <div class="input-wrap">
              <svg class="icon" viewBox="0 0 24 24">
                <path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0118 0z" />
                <circle cx="12" cy="10" r="3" />
              </svg>
              <input type="text" name="city" placeholder="Cagayan de Oro City" required>
            </div>
            <span class="field-error" id="err-city">Please enter your city.</span>
          </div>
        </div>

        <!-- Terms -->
        <div class="terms-row">
          <input type="checkbox" name="terms" id="acceptTerms" required>
          <label for="acceptTerms">I agree and accept the <a href="#">terms and conditions</a></label>
        </div>
        <span class="field-error" id="err-terms">You must agree before submitting.</span>

        <button type="submit" name="registerButton" class="btn-register">Create Account</button>

        <div class="login-link">
          Already have an account? <a href="login">Sign in</a>
        </div>

      </form>
    </div>

    <div class="register-footer">Academic Term · A.Y. 2025–2026</div>

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

    document.getElementById('registerForm').addEventListener('submit', function(e) {
      let valid = true;

      const required = [{
          name: 'firstName',
          errId: 'err-firstName'
        },
        {
          name: 'lastName',
          errId: 'err-lastName'
        },
        {
          name: 'emailAddress',
          errId: 'err-email'
        },
        {
          name: 'username',
          errId: 'err-username'
        },
        {
          name: 'password',
          errId: 'err-password'
        },
        {
          name: 'street',
          errId: 'err-street'
        },
        {
          name: 'barangay',
          errId: 'err-barangay'
        },
        {
          name: 'city',
          errId: 'err-city'
        },
      ];

      required.forEach(({
        name,
        errId
      }) => {
        const input = this.elements[name];
        const err = document.getElementById(errId);
        if (!input.value.trim()) {
          input.classList.add('invalid');
          err.classList.add('show');
          valid = false;
        } else {
          input.classList.remove('invalid');
          err.classList.remove('show');
        }
      });

      // Password match
      const pw = document.getElementById('password');
      const cpw = document.getElementById('confirmPassword');
      const errConfirm = document.getElementById('err-confirm');
      if (pw.value !== cpw.value || !cpw.value) {
        cpw.classList.add('invalid');
        errConfirm.classList.add('show');
        valid = false;
      } else {
        cpw.classList.remove('invalid');
        errConfirm.classList.remove('show');
      }

      // Terms
      const terms = document.getElementById('acceptTerms');
      const errTerms = document.getElementById('err-terms');
      if (!terms.checked) {
        errTerms.classList.add('show');
        valid = false;
      } else {
        errTerms.classList.remove('show');
      }

      if (!valid) e.preventDefault();
    });
  </script>

  <?php if (isset($_SESSION['message']) && $_SESSION['code'] != ''): ?>
    <script>
      Swal.fire({
        icon: "<?php echo $_SESSION['code']; ?>",
        title: "<?php echo $_SESSION['message']; ?>",
        toast: true,
        position: 'top-end',
        timer: 3000,
        showConfirmButton: false,
        timerProgressBar: true
      });
    </script>
    <?php unset($_SESSION['message']);
    unset($_SESSION['code']); ?>
  <?php endif; ?>

</body>

</html>