<?php
include('../../app/middleware/admin.php');
include('./includes/header.php');
include('./includes/topbar.php');
include('./includes/sidebar.php');
include('../../app/config/config.php');

$user_id = $_SESSION['authUser']['user_id'];

function fetchUser($conn, $user_id)
{
  $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
  $stmt->bind_param('i', $user_id);
  $stmt->execute();
  $user = $stmt->get_result()->fetch_assoc();
  $stmt->close();
  return $user;
}

$user = fetchUser($conn, $user_id);

$success = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

  if ($_POST['action'] === 'update_profile') {
    $firstName  = trim($_POST['firstName'] ?? '');
    $middleName = trim($_POST['middleName'] ?? '');
    $lastName   = trim($_POST['lastName'] ?? '');
    $email      = trim($_POST['emailAddress'] ?? '');
    $username   = trim($_POST['username'] ?? '');

    if (!$firstName || !$lastName || !$email || !$username) {
      $error = 'First name, last name, email, and username are required.';
    } else {
      $chk = $conn->prepare("SELECT id FROM users WHERE (username = ? OR emailAddress = ?) AND id != ?");
      $chk->bind_param('ssi', $username, $email, $user_id);
      $chk->execute();
      $chk->store_result();
      if ($chk->num_rows > 0) {
        $error = 'Username or email is already taken by another account.';
      } else {
        $avatarPath = $user['avatar'] ?? '';

        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
          $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
          $fileType = mime_content_type($_FILES['avatar']['tmp_name']);
          if (!in_array($fileType, $allowedTypes)) {
            $error = 'Invalid image type. Only JPG, PNG, GIF, WEBP allowed.';
          } elseif ($_FILES['avatar']['size'] > 2 * 1024 * 1024) {
            $error = 'Image must be under 2MB.';
          } else {
            $role      = $user['role'] ?? 'user';
            $uploadDir = dirname(__DIR__, 2) . '/app/uploads/avatars/' . $role . '/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $ext     = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
            $newName = 'avatar_' . $user_id . '_' . time() . '.' . $ext;
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadDir . $newName)) {
              if ($avatarPath) {
                $oldFile = dirname(__DIR__, 2) . '/app/uploads/avatars/' . $role . '/' . basename($avatarPath);
                if (file_exists($oldFile)) unlink($oldFile);
              }
              $avatarPath = 'app/uploads/avatars/' . $role . '/' . $newName;
            } else {
              $error = 'Failed to upload image. Check folder permissions on app/uploads/avatars/' . $role . '/';
            }
          }
        }

        if (!$error) {
          $upd = $conn->prepare("UPDATE users SET firstName=?, middleName=?, lastName=?, emailAddress=?, username=?, avatar=? WHERE id=?");
          $upd->bind_param('ssssssi', $firstName, $middleName, $lastName, $email, $username, $avatarPath, $user_id);
          if ($upd->execute()) {
            $_SESSION['authUser']['fullName']  = $firstName . ' ' . $lastName;
            $_SESSION['authUser']['username']  = $username;
            $_SESSION['authUser']['avatar']    = $avatarPath;
            $_SESSION['authUser']['firstName'] = $firstName;
            $_SESSION['authUser']['lastName']  = $lastName;
            $user = fetchUser($conn, $user_id);
            $success = 'Profile updated successfully.';
          } else {
            $error = 'Failed to update profile. Please try again.';
          }
          $upd->close();
        }
      }
      $chk->close();
    }
  }

  if ($_POST['action'] === 'change_password') {
    $freshUser = fetchUser($conn, $user_id);
    $current = $_POST['current_password'] ?? '';
    $new     = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (!$current || !$new || !$confirm) {
      $error = 'All password fields are required.';
    } elseif ($new !== $confirm) {
      $error = 'New passwords do not match.';
    } elseif (strlen($new) < 8) {
      $error = 'New password must be at least 8 characters.';
    } elseif (!password_verify($current, $freshUser['password'])) {
      $error = 'Current password is incorrect.';
    } else {
      $hashed = password_hash($new, PASSWORD_DEFAULT);
      $upd = $conn->prepare("UPDATE users SET password=? WHERE id=?");
      $upd->bind_param('si', $hashed, $user_id);
      if ($upd->execute()) {
        $success = 'Password changed successfully.';
      } else {
        $error = 'Failed to change password.';
      }
      $upd->close();
    }
  }
}

$initials  = strtoupper(substr($user['firstName'] ?? 'U', 0, 1) . substr($user['lastName'] ?? 'S', 0, 1));
$fullName  = htmlspecialchars(($user['firstName'] ?? '') . ' ' . ($user['middleName'] ? $user['middleName'] . ' ' : '') . ($user['lastName'] ?? ''));
$avatarSrc = !empty($user['avatar']) ? '/surveysystem/' . htmlspecialchars($user['avatar']) . '?v=' . time() : '';
?>

<style>
  @import url('https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@300;400;500;600&display=swap');

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
    --teal-lt: #d0eaea;
    --rose: #a02c2c;
    --rose-lt: #f5dede;
    --radius: 10px;
    --shadow: 0 2px 16px rgba(15, 14, 13, .07);
  }

  * {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
  }

  body {
    font-family: 'DM Sans', sans-serif;
    background: var(--paper);
    color: var(--ink);
  }

  .main-wrapper {
    padding: 2.5rem 2.75rem 4rem;
    max-width: 1440px;
    margin: 0 auto;
    width: 100%;
    transition: all .3s ease;
  }

  .breadcrumbs {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12.5px;
    font-weight: 500;
    letter-spacing: .04em;
    text-transform: uppercase;
    color: var(--ink-3);
    margin-bottom: 2rem;
  }

  .breadcrumbs a {
    color: var(--ink-3);
    text-decoration: none;
    transition: color .15s;
  }

  .breadcrumbs a:hover {
    color: var(--gold);
  }

  .breadcrumbs span {
    color: var(--paper-3);
  }

  .breadcrumbs .cur {
    color: var(--ink-2);
  }

.page-header {
    margin-bottom: 2rem;
    display: block;
}

.page-header h1 {
  font-family: 'DM Serif Display', serif;
  font-size: clamp(1.6rem, 5vw, 3rem);
  font-weight: 400;
  color: var(--ink);
}

  .page-header h1 em {
    font-style: italic;
    color: var(--gold);
  }

  .page-header p {
    font-size: 13.5px;
    color: var(--ink-3);
    margin-top: 6px;
  }

  .profile-avatar-wrap {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: #fff;
    border: 1.5px solid var(--paper-3);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
  }

  .avatar-upload-zone {
    position: relative;
    flex-shrink: 0;
  }

  .avatar-circle {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: var(--gold-light);
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'DM Serif Display', serif;
    font-size: 2rem;
    color: var(--gold-dark);
    border: 2px solid var(--paper-3);
    overflow: hidden;
  }

  .avatar-circle img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .avatar-edit-btn {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 26px;
    height: 26px;
    border-radius: 50%;
    background: var(--gold);
    border: 2px solid #fff;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 11px;
    transition: background .15s;
  }

  .avatar-edit-btn:hover {
    background: var(--gold-dark);
  }

  .avatar-info h3 {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--ink);
    word-break: break-word;
    overflow-wrap: break-word;
  }

  .avatar-info {
    min-width: 0;
    flex: 1;
  }

  .avatar-info p {
    font-size: 13px;
    color: var(--ink-3);
    margin-top: 3px;
  }

  .avatar-meta {
    display: flex;
    gap: 8px;
    margin-top: 8px;
    flex-wrap: wrap;
  }

  .role-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 11.5px;
    font-weight: 600;
    background: var(--gold-light);
    color: var(--gold-dark);
  }

  .avatar-hint {
    font-size: 11.5px;
    color: var(--ink-3);
    margin-top: 6px;
  }

  .avatar-hint.preview-active {
    color: var(--teal);
    font-weight: 500;
  }

  .profile-section {
    background: #fff;
    border: 1.5px solid var(--paper-3);
    border-radius: var(--radius);
    padding: 1.75rem;
    box-shadow: var(--shadow);
    margin-bottom: 1.5rem;
    position: relative;
    overflow: hidden;
  }

  .profile-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: var(--gold);
  }

  .profile-section.teal::before {
    background: var(--teal);
  }

  .profile-section h3 {
    font-family: 'DM Serif Display', serif;
    font-size: 1.1rem;
    font-weight: 400;
    color: var(--ink);
    margin-bottom: 1.25rem;
    padding-bottom: 10px;
    border-bottom: 1.5px solid var(--paper-2);
  }

  .pf-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
    margin-bottom: 14px;
  }

  .pf-field {
    display: flex;
    flex-direction: column;
    gap: 5px;
    margin-bottom: 14px;
  }

  .pf-field:last-child {
    margin-bottom: 0;
  }

  .pf-field label {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: var(--ink-3);
  }

  .pf-field input {
    background: var(--paper-2);
    border: 1.5px solid var(--paper-3);
    border-radius: var(--radius);
    padding: 9px 13px;
    font-family: 'DM Sans', sans-serif;
    font-size: 13.5px;
    color: var(--ink);
    outline: none;
    width: 100%;
    transition: border-color .15s, background .15s;
  }

  .pf-field input:focus {
    border-color: var(--gold);
    background: #fff;
  }

  .pf-field input[readonly] {
    background: var(--paper-3);
    color: var(--ink-3);
    cursor: not-allowed;
  }

  .pw-strength {
    height: 4px;
    border-radius: 2px;
    margin-top: 6px;
    background: var(--paper-3);
    overflow: hidden;
  }

  .pw-strength-bar {
    height: 100%;
    border-radius: 2px;
    width: 0%;
    transition: width .3s, background .3s;
  }

  .pw-show-toggle {
    position: relative;
  }

  .pw-show-toggle input {
    padding-right: 38px;
  }

  .pw-eye {
    position: absolute;
    right: 11px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: var(--ink-3);
    cursor: pointer;
    font-size: 14px;
    padding: 2px;
  }

  .btn-save {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: var(--gold);
    color: #fff;
    border: none;
    border-radius: var(--radius);
    padding: 9px 20px;
    font-family: 'DM Sans', sans-serif;
    font-size: 13.5px;
    font-weight: 500;
    cursor: pointer;
    transition: background .2s, opacity .2s;
  }

  .btn-save:hover {
    background: var(--gold-dark);
  }

  .btn-save.teal {
    background: var(--teal);
  }

  .btn-save.teal:hover {
    background: #0f4a4a;
  }

  .btn-save.saved {
    background: var(--teal) !important;
    pointer-events: none;
    opacity: 1;
  }

  .btn-save:disabled {
    opacity: .8;
    cursor: not-allowed;
    pointer-events: none;
  }

  .alert {
    padding: 10px 16px;
    border-radius: var(--radius);
    font-size: 13.5px;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .alert-success {
    background: var(--teal-lt);
    color: var(--teal);
    border: 1.5px solid var(--teal);
  }

  .alert-error {
    background: var(--rose-lt);
    color: var(--rose);
    border: 1.5px solid var(--rose);
  }

@media(max-width:640px) {
  .main-wrapper {
    padding: 0.5rem 1rem 3rem;
    margin-top: 0.5rem;
  }
    .avatar-info h3 {
      font-size: 0.95rem;
    }

    .avatar-info p {
      font-size: 12px;
    }

    .avatar-info {
      text-align: center;
      width: 100%;
    }

    .pf-row {
      grid-template-columns: 1fr;
    }

    .profile-avatar-wrap {
      flex-direction: column;
      text-align: center;
    }

    .avatar-meta {
      justify-content: center;
    }

  }
</style>

<div class="main-wrapper">

  <div class="breadcrumbs">
    <a href="index">Home</a>
    <span>/</span>
    <span class="cur">My Profile</span>
  </div>

<div class="page-header">
    <div>
        <h1>My <em>Profile</em></h1>
        <p>Manage your account information and security settings.</p>
    </div>
</div>
  <?php if ($success): ?>
    <div class="alert alert-success"><i class="bi bi-check-circle"></i> <?= htmlspecialchars($success) ?></div>
  <?php endif; ?>
  <?php if ($error): ?>
    <div class="alert alert-error"><i class="bi bi-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <div class="profile-avatar-wrap">
    <div class="avatar-upload-zone">
      <div class="avatar-circle" id="avatarPreview">
        <?php if ($avatarSrc): ?>
          <img src="<?= $avatarSrc ?>" alt="Avatar">
        <?php else: ?>
          <span><?= $initials ?></span>
        <?php endif; ?>
      </div>
      <label class="avatar-edit-btn" for="avatarInput" title="Change photo">
        <i class="bi bi-camera"></i>
      </label>
    </div>
    <div class="avatar-info">
      <h3><?= $fullName ?></h3>
      <p><?= htmlspecialchars($user['emailAddress']) ?></p>
      <div class="avatar-meta">
        <span class="role-badge"><i class="bi bi-shield-check"></i> <?= ucfirst($user['role']) ?></span>
      </div>
      <p class="avatar-hint" id="avatarHint">Click the camera icon to change your photo.</p>
    </div>
  </div>

  <div class="profile-section">
    <h3><i class="bi bi-person" style="color:var(--gold);margin-right:6px;"></i> Personal Information</h3>
    <form method="POST" enctype="multipart/form-data" id="profileForm">
      <input type="hidden" name="action" value="update_profile">
      <input type="file" name="avatar" id="avatarInput" accept="image/*" style="display:none;">

      <div class="pf-row">
        <div class="pf-field">
          <label>First Name <span style="color:var(--rose)">*</span></label>
          <input type="text" name="firstName" value="<?= htmlspecialchars($user['firstName'] ?? '') ?>" required>
        </div>
        <div class="pf-field">
          <label>Middle Name</label>
          <input type="text" name="middleName" value="<?= htmlspecialchars($user['middleName'] ?? '') ?>">
        </div>
      </div>

      <div class="pf-row">
        <div class="pf-field">
          <label>Last Name <span style="color:var(--rose)">*</span></label>
          <input type="text" name="lastName" value="<?= htmlspecialchars($user['lastName'] ?? '') ?>" required>
        </div>
        <div class="pf-field">
          <label>Username <span style="color:var(--rose)">*</span></label>
          <input type="text" name="username" value="<?= htmlspecialchars($user['username'] ?? '') ?>" required>
        </div>
      </div>

      <div class="pf-row">
        <div class="pf-field">
          <label>Email Address <span style="color:var(--rose)">*</span></label>
          <input type="email" name="emailAddress" value="<?= htmlspecialchars($user['emailAddress'] ?? '') ?>" required>
        </div>
        <div class="pf-field">
          <label>Role</label>
          <input type="text" value="<?= ucfirst($user['role']) ?>" readonly>
        </div>
      </div>

      <div style="margin-top:1.25rem;">
        <button type="submit" class="btn-save" id="profileSaveBtn">
          <i class="bi bi-check-lg"></i> Save Changes
        </button>
      </div>
    </form>
  </div>

  <div class="profile-section teal">
    <h3><i class="bi bi-shield-lock" style="color:var(--teal);margin-right:6px;"></i> Change Password</h3>
    <form method="POST" id="passwordForm">
      <input type="hidden" name="action" value="change_password">

      <div class="pf-field">
        <label>Current Password <span style="color:var(--rose)">*</span></label>
        <div class="pw-show-toggle">
          <input type="password" name="current_password" id="curPw" placeholder="Enter your current password">
          <button type="button" class="pw-eye" onclick="togglePw('curPw',this)"><i class="bi bi-eye"></i></button>
        </div>
      </div>

      <div class="pf-row">
        <div class="pf-field">
          <label>New Password <span style="color:var(--rose)">*</span></label>
          <div class="pw-show-toggle">
            <input type="password" name="new_password" id="newPw" placeholder="At least 8 characters" oninput="checkStrength(this.value)">
            <button type="button" class="pw-eye" onclick="togglePw('newPw',this)"><i class="bi bi-eye"></i></button>
          </div>
          <div class="pw-strength">
            <div class="pw-strength-bar" id="pwBar"></div>
          </div>
          <span id="pwLabel" style="font-size:11.5px;color:var(--ink-3);margin-top:4px;display:block;"></span>
        </div>
        <div class="pf-field">
          <label>Confirm New Password <span style="color:var(--rose)">*</span></label>
          <div class="pw-show-toggle">
            <input type="password" name="confirm_password" id="confPw" placeholder="Repeat new password">
            <button type="button" class="pw-eye" onclick="togglePw('confPw',this)"><i class="bi bi-eye-slash"></i></button>
          </div>
        </div>
      </div>

      <div class="pf-field" style="margin-top:4px;">
        <label>Password Requirements</label>
        <div style="background:var(--paper-2);border-radius:8px;padding:10px 14px;font-size:12.5px;color:var(--ink-3);display:grid;grid-template-columns:1fr 1fr;gap:4px 12px;margin-top:2px;">
          <span id="req-len">○ Minimum 8 characters</span>
          <span id="req-upper">○ One uppercase letter</span>
          <span id="req-num">○ One number</span>
          <span id="req-special">○ One special character</span>
        </div>
      </div>

      <div style="margin-top:1.25rem;">
        <button type="submit" class="btn-save teal" id="passwordSaveBtn">
          <i class="bi bi-shield-lock"></i> Change Password
        </button>
      </div>
    </form>
  </div>

</div>

<script>
  document.getElementById('avatarInput').addEventListener('change', function() {
    if (!this.files[0]) return;
    var hint = document.getElementById('avatarHint');
    var reader = new FileReader();
    reader.onload = function(e) {
      document.getElementById('avatarPreview').innerHTML =
        '<img src="' + e.target.result + '" alt="Avatar" style="width:100%;height:100%;object-fit:cover;">';
      hint.textContent = '✓ Photo selected — click Save Changes to apply.';
      hint.classList.add('preview-active');
    };
    reader.readAsDataURL(this.files[0]);
  });

  <?php if ($success && ($_POST['action'] ?? '') === 'update_profile'): ?>
      (function() {
        var firstName = <?= json_encode($user['firstName'] ?? '') ?>;
        var lastName = <?= json_encode($user['lastName'] ?? '') ?>;
        var username = <?= json_encode($user['username'] ?? '') ?>;
        var fullName = firstName + ' ' + lastName;

        <?php if (!empty($user['avatar'])): ?>
          var freshSrc = '/surveysystem/<?= htmlspecialchars($user['avatar']) ?>?v=<?= time() ?>';
          var topbarImg = document.getElementById('topbarAvatarImg');
          if (topbarImg) topbarImg.src = freshSrc;
          document.getElementById('avatarPreview').innerHTML =
            '<img src="' + freshSrc + '" alt="Avatar" style="width:100%;height:100%;object-fit:cover;">';
        <?php endif; ?>

var topbarSpan = document.querySelector('.nav-profile span');
if (topbarSpan) topbarSpan.textContent = username;

var dropdownH6 = document.querySelector('.dropdown-menu.profile .dropdown-header h6');
if (dropdownH6) dropdownH6.textContent = username;

var dropdownSpan = document.querySelector('.dropdown-menu.profile .dropdown-header span');
if (dropdownSpan) dropdownSpan.textContent = fullName + ' · Admin';

        var profileDropdown = document.querySelector('.dropdown-menu.profile');
        if (profileDropdown) {
          var h6 = profileDropdown.querySelector('.dropdown-header h6');
          if (h6) h6.textContent = username;

          var subSpan = profileDropdown.querySelector('.dropdown-header span');
          if (subSpan) subSpan.textContent = fullName + ' · Admin';
        }
      })();
  <?php endif; ?>

  function checkStrength(val) {
    var bar = document.getElementById('pwBar');
    var lbl = document.getElementById('pwLabel');
    var reqs = {
      len: val.length >= 8,
      upper: /[A-Z]/.test(val),
      num: /[0-9]/.test(val),
      special: /[^A-Za-z0-9]/.test(val)
    };
    var score = Object.values(reqs).filter(Boolean).length;
    var colors = ['', '#a02c2c', '#c9972b', '#1b6b6b', '#1b6b6b'];
    var labels = ['', 'Weak', 'Fair', 'Strong', 'Very Strong'];
    var widths = ['0%', '25%', '50%', '75%', '100%'];
    bar.style.width = widths[score] || '0%';
    bar.style.background = colors[score] || 'transparent';
    lbl.textContent = score > 0 ? labels[score] : '';
    var map = {
      len: 'req-len',
      upper: 'req-upper',
      num: 'req-num',
      special: 'req-special'
    };
    for (var k in map) {
      var el = document.getElementById(map[k]);
      if (reqs[k]) {
        el.style.color = 'var(--teal)';
        el.textContent = el.textContent.replace('○', '✓');
      } else {
        el.style.color = '';
        el.textContent = el.textContent.replace('✓', '○');
      }
    }
  }

  function togglePw(id, btn) {
    var inp = document.getElementById(id);
    var showing = inp.type === 'text';
    inp.type = showing ? 'password' : 'text';
    btn.innerHTML = showing ?
      '<i class="bi bi-eye"></i>' :
      '<i class="bi bi-eye-slash"></i>';
  }

  function markSaved(btn, originalHTML) {
    btn.classList.add('saved');
    btn.innerHTML = '<i class="bi bi-check-circle-fill"></i> Saved!';
    btn.disabled = true;
    setTimeout(function() {
      btn.classList.remove('saved');
      btn.innerHTML = originalHTML;
      btn.disabled = false;
    }, 3000);
  }

  (function() {
    <?php if ($success): ?>
        (function() {
          var action = '<?= htmlspecialchars($_POST['action'] ?? '') ?>';
          var btnId = action === 'change_password' ? 'passwordSaveBtn' : 'profileSaveBtn';
          var btn = document.getElementById(btnId);
          if (btn) markSaved(btn, btn.innerHTML);
        })();
    <?php endif; ?>

      ['profileForm', 'passwordForm'].forEach(function(formId) {
        var form = document.getElementById(formId);
        if (!form) return;
        form.addEventListener('submit', function(e) {
          if (e.defaultPrevented) return;
          var btn = form.querySelector('.btn-save');
          if (!btn) return;
          btn.disabled = true;
          btn.innerHTML = '<i class="bi bi-arrow-repeat"></i> Saving…';
        });
      });
  })();
</script>

<?php include('./includes/footer.php'); ?>