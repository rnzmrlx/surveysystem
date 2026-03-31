<?php session_start() ?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Pages / Register - NiceAdmin Bootstrap Template</title>

  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700|Nunito:300,400,600,700|Poppins:300,400,500,600,700" rel="stylesheet">

  <style>
    /* Ensures the card doesn't look too cramped with side-by-side inputs */
    .register .card {
      border: none;
      border-radius: 20px;
    }
  </style>
</head>

<body>

  <main>
    <div class="container">

      <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8 d-flex flex-column align-items-center justify-content-center">

              <div class="card mb-3">
                <div class="card-body">

                  <div class="pt-4 pb-2">
                    <h5 class="card-title text-center pb-0 fs-4">Create an Account</h5>
                    <p class="text-center small">Enter your personal details to create account</p>
                  </div>

                  <form class="row g-3 needs-validation" action="/surveysystem/app/controllers/loginController.php" method="post" enctype="multipart/form-data" autocomplete="off" novalidate>
                    
                    <div class="col-md-6">
                      <label for="firstName" class="form-label">First Name <span class="text-danger">*</span></label>
                      <input type="text" name="firstName" class="form-control" id="firstName" placeholder="Juan" required>
                      <div class="invalid-feedback">Please, enter your first name!</div>
                    </div>
                    
                    <div class="col-md-6">
                      <label for="middleName" class="form-label">Middle Name</label>
                      <input type="text" name="middleName" class="form-control" id="middleName" placeholder="Santos">
                    </div>

                    <div class="col-12">
                      <label for="lastName" class="form-label">Last Name <span class="text-danger">*</span></label>
                      <input type="text" name="lastName" class="form-control" id="lastName" placeholder="Dela Cruz" required>
                      <div class="invalid-feedback">Please, enter your last name!</div>
                    </div>

                    <div class="col-12">
                      <label for="yourEmail" class="form-label">Email Address <span class="text-danger">*</span></label>
                      <input type="email" name="emailAddress" class="form-control" id="yourEmail" placeholder="juan.delacruz@example.com" required>
                      <div class="invalid-feedback">Please enter a valid email!</div>
                    </div>

                    <div class="col-12">
                      <label for="yourUsername" class="form-label">Username <span class="text-danger">*</span></label>
                      <div class="input-group has-validation">
                        <span class="input-group-text">@</span>
                        <input type="text" name="username" class="form-control" id="yourUsername" placeholder="juan_delacruz" required>
                        <div class="invalid-feedback">Please choose a username.</div>
                      </div>
                    </div>

                    <div class="col-md-6">
                      <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                      <input type="password" name="password" class="form-control" id="password" placeholder="**********" required>
                      <div class="invalid-feedback">Please, enter your password!</div>
                    </div>

                    <div class="col-md-6">
                      <label for="confirmPassword" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                      <input type="password" name="confirmPassword" class="form-control" id="confirmPassword" placeholder="**********" required>
                      <div class="invalid-feedback">Please, confirm your password!</div>
                    </div>

                    <div class="col-md-6">
                      <label for="street" class="form-label">Street <span class="text-danger">*</span></label>
                      <input type="text" name="street" class="form-control" id="street" placeholder="Zone 5" required>
                      <div class="invalid-feedback">Please, enter your street!</div>
                    </div>

                    <div class="col-md-6">
                      <label for="barangay" class="form-label">Barangay <span class="text-danger">*</span></label>
                      <input type="text" name="barangay" class="form-control" id="barangay" placeholder="Cugman" required>
                      <div class="invalid-feedback">Please, enter your barangay!</div>
                    </div>

                    <div class="col-12">
                      <label for="city" class="form-label">City <span class="text-danger">*</span></label>
                      <input type="text" name="city" class="form-control" id="city" placeholder="Cagayan de Oro City" required>
                      <div class="invalid-feedback">Please, enter your city!</div>
                    </div>

                    <div class="col-12">
                      <div class="form-check">
                        <input class="form-check-input" name="terms" type="checkbox" value="" id="acceptTerms" required>
                        <label class="form-check-label" for="acceptTerms">I agree and accept the <a href="#">terms and conditions</a></label>
                        <div class="invalid-feedback">You must agree before submitting.</div>
                      </div>
                    </div>

                    <div class="col-12">
                      <button class="btn btn-primary w-100" name="registerButton" type="submit">Create Account</button>
                    </div>
                    
                    <div class="col-12 text-center">
                      <p class="small mb-0">Already have an account? <a href="login">Log in</a></p>
                    </div>
                  </form>

                </div>
              </div>

            </div>
          </div>
        </div>
      </section>

    </div>
  </main>

  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    (() => {
      'use strict'
      const form = document.querySelector('.needs-validation')
      const password = document.getElementById('password')
      const confirmPassword = document.getElementById('confirmPassword')

      form.addEventListener('submit', event => {
        if (password.value !== confirmPassword.value) {
          confirmPassword.setCustomValidity('Passwords do not match');
        } else {
          confirmPassword.setCustomValidity('');
        }

        if (!form.checkValidity()) {
          event.preventDefault()
          event.stopPropagation()
        }
        form.classList.add('was-validated')
      }, false)
    })()
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
    <?php unset($_SESSION['message']); unset($_SESSION['code']); ?>
  <?php endif; ?>

</body>
</html>