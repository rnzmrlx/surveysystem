<?php
include('../../app/middleware/user.php');
include('./includes/header.php');
include('./includes/topbar.php');
include('./includes/sidebar.php');
include('../../app/config/config.php');

$user_id = $_SESSION['authUser']['user_id'];

// Fetch full user data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

$success = '';
$error   = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

  if ($_POST['action'] === 'update_profile') {
    $firstName   = trim($_POST['firstName'] ?? '');
    $middleName  = trim($_POST['middleName'] ?? '');
    $lastName    = trim($_POST['lastName'] ?? '');
    $email       = trim($_POST['emailAddress'] ?? '');
    $username    = trim($_POST['username'] ?? '');
    $phone       = trim($_POST['phone'] ?? '');
    $dob         = trim($_POST['dob'] ?? '');
    $gender      = trim($_POST['gender'] ?? '');
    $bio         = trim($_POST['bio'] ?? '');
    $street      = trim($_POST['street'] ?? '');
    $barangay    = trim($_POST['barangay'] ?? '');
    $city        = trim($_POST['city'] ?? '');
    $province    = trim($_POST['province'] ?? '');
    $zipCode     = trim($_POST['zip_code'] ?? '');
    $country     = trim($_POST['country'] ?? 'Philippines');

    if (!$firstName || !$lastName || !$email || !$username) {
      $error = 'First name, last name, email, and username are required.';
    } elseif ($phone !== '' && !preg_match('/^\+?[0-9]{7,15}$/', preg_replace('/[\s\-()]/', '', $phone))) {
      $error = 'Phone number is invalid. Use digits only, 7–15 numbers (e.g. +639171234567).';
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
          $upd = $conn->prepare("UPDATE users SET firstName=?, middleName=?, lastName=?, emailAddress=?, username=?, phone=?, dob=?, gender=?, bio=?, street=?, barangay=?, city=?, province=?, zip_code=?, country=?, avatar=? WHERE id=?");
          $upd->bind_param(
            'ssssssssssssssssi',
            $firstName,
            $middleName,
            $lastName,
            $email,
            $username,
            $phone,
            $dob,
            $gender,
            $bio,
            $street,
            $barangay,
            $city,
            $province,
            $zipCode,
            $country,
            $avatarPath,
            $user_id
          );
          if ($upd->execute()) {
            $_SESSION['authUser']['fullName']  = $firstName . ' ' . $lastName;
            $_SESSION['authUser']['username']  = $username;
            $_SESSION['authUser']['avatar']    = $avatarPath;
            $_SESSION['authUser']['firstName'] = $firstName;
            $_SESSION['authUser']['lastName']  = $lastName;
            foreach (['firstName', 'middleName', 'lastName', 'emailAddress', 'username', 'phone', 'dob', 'gender', 'bio', 'street', 'barangay', 'city', 'province', 'zip_code', 'country'] as $f) {
              $user[$f] = $$f ?? $user[$f];
            }
            $user['avatar'] = $avatarPath;
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
    $current = $_POST['current_password'] ?? '';
    $new     = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (!$current || !$new || !$confirm) {
      $error = 'All password fields are required.';
    } elseif ($new !== $confirm) {
      $error = 'New passwords do not match.';
    } elseif (strlen($new) < 8) {
      $error = 'New password must be at least 8 characters.';
    } elseif (!password_verify($current, $user['password'])) {
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

  if ($_POST['action'] === 'update_notifications') {
    $emailNotif  = isset($_POST['notif_email'])  ? 1 : 0;
    $smsNotif    = isset($_POST['notif_sms'])    ? 1 : 0;
    $systemNotif = isset($_POST['notif_system']) ? 1 : 0;
    $upd = $conn->prepare("UPDATE users SET notif_email=?, notif_sms=?, notif_system=? WHERE id=?");
    $upd->bind_param('iiii', $emailNotif, $smsNotif, $systemNotif, $user_id);
    if ($upd->execute()) {
      $user['notif_email']  = $emailNotif;
      $user['notif_sms']    = $smsNotif;
      $user['notif_system'] = $systemNotif;
      $success = 'Notification preferences updated.';
    } else {
      $error = 'Failed to update preferences.';
    }
    $upd->close();
  }
}

$initials    = strtoupper(substr($user['firstName'] ?? 'U', 0, 1) . substr($user['lastName'] ?? 'S', 0, 1));
$fullName    = htmlspecialchars(($user['firstName'] ?? '') . ' ' . ($user['lastName'] ?? ''));
$memberSince = isset($user['created_at']) ? date('F j, Y', strtotime($user['created_at'])) : 'N/A';
$lastLogin   = isset($user['last_login'])  ? date('M d, Y g:i A', strtotime($user['last_login'])) : 'N/A';
$avatarSrc   = !empty($user['avatar']) ? '/surveysystem/' . htmlspecialchars($user['avatar']) . '?v=' . time() : '';
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
    --sky: #1a5d8f;
    --sky-lt: #d4e8f5;
    --radius: 10px;
    --shadow: 0 2px 16px rgba(15, 14, 13, .07);
  }

  #main {
    margin-left: 260px;
    padding: 2.5rem 2.75rem 4rem;
    min-height: 100vh;
    background: var(--paper);
    transition: margin-left 0.3s;
  }

  body.sidebar-hidden #main {
    margin-left: 0;
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
  }

  .page-header h1 {
    font-family: 'DM Serif Display', serif;
    font-size: clamp(2rem, 4vw, 3rem);
    font-weight: 400;
    line-height: 1;
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

  .tab-nav {
    display: flex;
    gap: 4px;
    margin-bottom: 1.75rem;
    background: var(--paper-2);
    padding: 5px;
    border-radius: var(--radius);
    width: fit-content;
    flex-wrap: wrap;
  }

  .tab-btn {
    padding: 8px 18px;
    border: none;
    background: transparent;
    border-radius: 8px;
    font-family: 'DM Sans', sans-serif;
    font-size: 13px;
    font-weight: 500;
    color: var(--ink-3);
    cursor: pointer;
    transition: all .2s;
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .tab-btn.active {
    background: #fff;
    color: var(--ink);
    box-shadow: 0 1px 6px rgba(15, 14, 13, .1);
  }

  .tab-btn:hover:not(.active) {
    color: var(--ink-2);
  }

  .tab-panel {
    display: none;
  }

  .tab-panel.active {
    display: block;
  }

  .profile-avatar-wrap {
    display: flex;
    align-items: center;
    gap: 1.75rem;
    margin-bottom: 1.75rem;
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

  #avatarInput {
    display: none;
  }

  .avatar-info h3 {
    font-size: 1.15rem;
    font-weight: 600;
    color: var(--ink);
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

  .meta-chip {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 11.5px;
    font-weight: 500;
    background: var(--paper-2);
    color: var(--ink-3);
  }

  .stat-strip {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 14px;
    margin-bottom: 2.25rem;
  }

  .stat-card {
    background: var(--paper-2);
    border: 1.5px solid var(--paper-3);
    border-radius: var(--radius);
    padding: 1.1rem 1.25rem;
    position: relative;
    overflow: hidden;
  }

  .stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: var(--gold);
  }

  .stat-card .s-val {
    font-family: 'DM Serif Display', serif;
    font-size: 2rem;
    line-height: 1;
    color: var(--ink);
  }

  .stat-card .s-lbl {
    font-size: 11px;
    font-weight: 600;
    letter-spacing: .08em;
    text-transform: uppercase;
    color: var(--ink-3);
    margin-bottom: 8px;
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

  .profile-section.sky::before {
    background: var(--sky);
  }

  .profile-section h3 {
    font-family: 'DM Serif Display', serif;
    font-size: 1.1rem;
    font-weight: 400;
    color: var(--ink);
    margin-bottom: 1.25rem;
    padding-bottom: 10px;
    border-bottom: 1.5px solid var(--paper-2);
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .section-icon {
    width: 28px;
    height: 28px;
    border-radius: 7px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
  }

  .section-icon.gold {
    background: var(--gold-light);
    color: var(--gold-dark);
  }

  .section-icon.teal {
    background: var(--teal-lt);
    color: var(--teal);
  }

  .section-icon.sky {
    background: var(--sky-lt);
    color: var(--sky);
  }

  .pf-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
    margin-bottom: 14px;
  }

  .pf-row-3 {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
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

  .pf-field input,
  .pf-field select,
  .pf-field textarea {
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

  .pf-field input:focus,
  .pf-field select:focus,
  .pf-field textarea:focus {
    border-color: var(--gold);
    background: #fff;
  }

  .pf-field input[readonly] {
    background: var(--paper-3);
    color: var(--ink-3);
    cursor: not-allowed;
  }

  .pf-field textarea {
    resize: vertical;
    min-height: 80px;
  }

  .pf-field .hint {
    font-size: 11.5px;
    color: var(--ink-3);
    margin-top: 3px;
  }

  .pf-field input.phone-error {
    border-color: var(--rose) !important;
    background: #fff;
  }

  .pf-field input.phone-ok {
    border-color: var(--teal) !important;
    background: #fff;
  }

  .phone-msg {
    font-size: 11.5px;
    margin-top: 3px;
    display: none;
  }

  .phone-msg.show {
    display: block;
  }

  .phone-msg.error {
    color: var(--rose);
  }

  .phone-msg.ok {
    color: var(--teal);
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

  .toggle-list {
    display: flex;
    flex-direction: column;
    gap: 14px;
  }

  .toggle-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 14px;
    background: var(--paper-2);
    border-radius: 8px;
    border: 1.5px solid var(--paper-3);
  }

  .toggle-item-info h4 {
    font-size: 13.5px;
    font-weight: 500;
    color: var(--ink);
  }

  .toggle-item-info p {
    font-size: 12px;
    color: var(--ink-3);
    margin-top: 2px;
  }

  .toggle-switch {
    position: relative;
    width: 42px;
    height: 24px;
    flex-shrink: 0;
  }

  .toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
  }

  .toggle-slider {
    position: absolute;
    cursor: pointer;
    inset: 0;
    background: var(--paper-3);
    border-radius: 24px;
    transition: .3s;
  }

  .toggle-slider:before {
    position: absolute;
    content: '';
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background: #fff;
    border-radius: 50%;
    transition: .3s;
  }

  .toggle-switch input:checked+.toggle-slider {
    background: var(--teal);
  }

  .toggle-switch input:checked+.toggle-slider:before {
    transform: translateX(18px);
  }

  .info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
  }

  .info-item {
    background: var(--paper-2);
    border-radius: 8px;
    padding: 12px 14px;
  }

  .info-item .i-label {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: var(--ink-3);
    margin-bottom: 4px;
  }

  .info-item .i-val {
    font-size: 13.5px;
    color: var(--ink);
    font-weight: 500;
  }

  .info-item .i-val.mono {
    font-family: monospace;
    font-size: 12.5px;
    letter-spacing: .04em;
  }

  .status-dot {
    display: inline-block;
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #2e8b57;
    margin-right: 5px;
    vertical-align: middle;
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

  .btn-save.sky {
    background: var(--sky);
  }

  .btn-save.sky:hover {
    background: #0f3d63;
  }

  .btn-save.saved {
    background: var(--teal) !important;
    pointer-events: none;
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

  @media(max-width:680px) {

    .pf-row,
    .pf-row-3,
    .info-grid {
      grid-template-columns: 1fr;
    }

    .profile-avatar-wrap {
      flex-direction: column;
      text-align: center;
    }

    .avatar-meta {
      justify-content: center;
    }

    .stat-strip {
      grid-template-columns: 1fr;
    }

    .tab-nav {
      width: 100%;
    }

    .tab-btn {
      flex: 1;
      justify-content: center;
    }
  }
</style>

<div class="main">

  <div class="breadcrumbs">
    <a href="index">Home</a>
    <span>/</span>
    <span class="cur">My Profile</span>
  </div>

  <div class="page-header">
    <h1>My <em>Profile</em></h1>
    <p>Manage your personal information, security, and account preferences.</p>
  </div>

  <?php if ($success): ?>
    <div class="alert alert-success">
      <i class="bi bi-check-circle"></i> <?= htmlspecialchars($success) ?>
    </div>
  <?php endif; ?>
  <?php if ($error): ?>
    <div class="alert alert-error">
      <i class="bi bi-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
    </div>
  <?php endif; ?>

  <!-- Avatar Card -->
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
        <?php if (!empty($user['city'])): ?>
          <span class="meta-chip"><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($user['city']) ?></span>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Tab Navigation -->
  <div class="tab-nav">
    <button class="tab-btn active" onclick="switchTab('personal', this)"><i class="bi bi-person"></i> Personal</button>
    <button class="tab-btn" onclick="switchTab('address', this)"><i class="bi bi-geo-alt"></i> Address</button>
    <button class="tab-btn" onclick="switchTab('security', this)"><i class="bi bi-shield-lock"></i> Security</button>
    <button class="tab-btn" onclick="switchTab('account', this)"><i class="bi bi-info-circle"></i> Account</button>
  </div>

  <!-- TAB: Personal -->
  <div class="tab-panel active" id="tab-personal">
    <div class="profile-section">
      <h3><span class="section-icon gold"><i class="bi bi-person"></i></span> Personal Information</h3>
      <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="update_profile">
        <input type="file" name="avatar" id="avatarInput" accept="image/*" style="display:none;">
        <div class="pf-row">
          <div class="pf-field">
            <label>First Name <span style="color:var(--rose)">*</span></label>
<input type="text" name="firstName" value="<?= htmlspecialchars($user['firstName'] ?? '') ?>" placeholder="Enter first name" required>          </div>
          <div class="pf-field">
            <label>Middle Name</label>
<input type="text" name="middleName" value="<?= htmlspecialchars($user['middleName'] ?? '') ?>" placeholder="Enter middle name">          </div>
        </div>
        <div class="pf-row">
          <div class="pf-field">
            <label>Last Name <span style="color:var(--rose)">*</span></label>
<input type="text" name="lastName" value="<?= htmlspecialchars($user['lastName'] ?? '') ?>" placeholder="Enter last name" required>          </div>
          <div class="pf-field">
            <label>Username <span style="color:var(--rose)">*</span></label>
<input type="text" name="username" value="<?= htmlspecialchars($user['username'] ?? '') ?>" placeholder="Enter username" required>          </div>
        </div>
        <div class="pf-field">
          <label>Email Address <span style="color:var(--rose)">*</span></label>
<input type="email" name="emailAddress" value="<?= htmlspecialchars($user['emailAddress'] ?? '') ?>" placeholder="e.g. juan@email.com" required>        </div>
        <div class="pf-row">
          <div class="pf-field">
            <label>Phone Number</label>
            <input type="tel" name="phone" id="phoneInput" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" placeholder="+63 917 123 4567" inputmode="numeric" autocomplete="tel">
            <span class="phone-msg" id="phoneMsg"></span>
            <span class="hint">Numbers only, 7–15 digits. Include country code (e.g. +63).</span>
          </div>
          <div class="pf-field">
            <label>Date of Birth</label>
<input type="date" name="dob" value="<?= htmlspecialchars($user['dob'] ?? '') ?>" placeholder="YYYY-MM-DD">          </div>
        </div>
        <div class="pf-row">
          <div class="pf-field">
            <label>Gender</label>
            <select name="gender">
              <option value="">— Select —</option>
              <?php foreach (['Male', 'Female', 'Non-binary', 'Prefer not to say'] as $g): ?>
                <option value="<?= $g ?>" <?= ($user['gender'] ?? '') === $g ? 'selected' : '' ?>><?= $g ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="pf-field">
            <label>Role</label>
            <input type="text" value="<?= ucfirst($user['role']) ?>" readonly>
          </div>
        </div>
        <div class="pf-field">
          <label>Bio / About Me</label>
          <textarea name="bio" placeholder="Write a short description about yourself..."><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
          <span class="hint">Max 250 characters. Shown on your public profile.</span>
        </div>
        <div style="margin-top:1.25rem;">
          <button type="submit" class="btn-save" id="personalSaveBtn">
            <i class="bi bi-check-lg"></i> Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- TAB: Address -->
  <div class="tab-panel" id="tab-address">
    <div class="profile-section">
      <h3><span class="section-icon gold"><i class="bi bi-geo-alt"></i></span> Address Information</h3>
      <form method="POST">
        <input type="hidden" name="action" value="update_profile">
        <input type="hidden" name="firstName" value="<?= htmlspecialchars($user['firstName'] ?? '') ?>">
        <input type="hidden" name="middleName" value="<?= htmlspecialchars($user['middleName'] ?? '') ?>">
        <input type="hidden" name="lastName" value="<?= htmlspecialchars($user['lastName'] ?? '') ?>">
        <input type="hidden" name="emailAddress" value="<?= htmlspecialchars($user['emailAddress'] ?? '') ?>">
        <input type="hidden" name="username" value="<?= htmlspecialchars($user['username'] ?? '') ?>">
        <input type="hidden" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
        <input type="hidden" name="dob" value="<?= htmlspecialchars($user['dob'] ?? '') ?>">
        <input type="hidden" name="gender" value="<?= htmlspecialchars($user['gender'] ?? '') ?>">
        <input type="hidden" name="bio" value="<?= htmlspecialchars($user['bio'] ?? '') ?>">
        <div class="pf-field">
          <label>Street / House No. / Building</label>
          <input type="text" name="street" value="<?= htmlspecialchars($user['street'] ?? '') ?>" placeholder="e.g. 123 Rizal St, Apt 4B">
        </div>
        <div class="pf-row">
<div class="pf-field">
  <label>Barangay</label>
  <input type="text" name="barangay" value="<?= htmlspecialchars($user['barangay'] ?? '') ?>" placeholder="e.g. Brgy. San Antonio">
</div>
<div class="pf-field">
  <label>City / Municipality</label>
  <input type="text" name="city" value="<?= htmlspecialchars($user['city'] ?? '') ?>" placeholder="e.g. Davao City">
</div>
        </div>
        <div class="pf-row-3">
<div class="pf-field">
  <label>Province</label>
  <input type="text" name="province" value="<?= htmlspecialchars($user['province'] ?? '') ?>" placeholder="e.g. Davao del Sur">
</div>
          <div class="pf-field">
            <label>ZIP / Postal Code</label>
            <input type="text" name="zip_code" value="<?= htmlspecialchars($user['zip_code'] ?? '') ?>" placeholder="e.g. 1600">
          </div>
          <div class="pf-field">
            <label>Country</label>
            <select name="country">
              <?php foreach (['Philippines', 'United States', 'United Kingdom', 'Canada', 'Australia', 'Japan', 'Singapore', 'Other'] as $c): ?>
                <option value="<?= $c ?>" <?= ($user['country'] ?? 'Philippines') === $c ? 'selected' : '' ?>><?= $c ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div style="margin-top:1.25rem;">
          <button type="submit" class="btn-save"><i class="bi bi-check-lg"></i> Save Address</button>
        </div>
      </form>
    </div>
  </div>

  <!-- TAB: Security -->
  <div class="tab-panel" id="tab-security">
    <div class="profile-section teal">
      <h3><span class="section-icon teal"><i class="bi bi-shield-lock"></i></span> Change Password</h3>
      <form method="POST">
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
          <button type="submit" class="btn-save teal"><i class="bi bi-shield-lock"></i> Change Password</button>
        </div>
      </form>
    </div>
  </div>

  <!-- TAB: Account -->
  <div class="tab-panel" id="tab-account">
    <div class="profile-section">
      <h3><span class="section-icon gold"><i class="bi bi-info-circle"></i></span> Account Information</h3>
      <div class="info-grid">
        <div class="info-item">
          <div class="i-label">User ID</div>
          <div class="i-val mono">#<?= str_pad($user['id'], 6, '0', STR_PAD_LEFT) ?></div>
        </div>
        <div class="info-item">
          <div class="i-label">Account Status</div>
          <div class="i-val"><span class="status-dot"></span> Active</div>
        </div>
        <div class="info-item">
          <div class="i-label">Role</div>
          <div class="i-val"><?= ucfirst($user['role']) ?></div>
        </div>
        <div class="info-item">
          <div class="i-label">Username</div>
          <div class="i-val mono">@<?= htmlspecialchars($user['username']) ?></div>
        </div>
        <div class="info-item">
          <div class="i-label">Email</div>
          <div class="i-val"><?= htmlspecialchars($user['emailAddress']) ?></div>
        </div>
        <div class="info-item">
          <div class="i-label">Phone</div>
          <div class="i-val"><?= !empty($user['phone']) ? htmlspecialchars($user['phone']) : '—' ?></div>
        </div>
        <?php if (!empty($user['dob'])): ?>
          <div class="info-item">
            <div class="i-label">Date of Birth</div>
            <div class="i-val"><?= date('F d, Y', strtotime($user['dob'])) ?></div>
          </div>
        <?php endif; ?>
        <?php if (!empty($user['gender'])): ?>
          <div class="info-item">
            <div class="i-label">Gender</div>
            <div class="i-val"><?= htmlspecialchars($user['gender']) ?></div>
          </div>
        <?php endif; ?>
      </div>
      <?php if (!empty($user['bio'])): ?>
        <div style="margin-top:14px;">
          <div class="info-item">
            <div class="i-label">Bio</div>
            <div class="i-val" style="margin-top:4px;line-height:1.6;"><?= nl2br(htmlspecialchars($user['bio'])) ?></div>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>

</div><!-- .main -->

<script>
  function switchTab(id, btn) {
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + id).classList.add('active');
    btn.classList.add('active');
  }

  document.getElementById('avatarInput').addEventListener('change', function() {
    if (!this.files[0]) return;
    var reader = new FileReader();
    reader.onload = function(e) {
      document.getElementById('avatarPreview').innerHTML =
        '<img src="' + e.target.result + '" alt="Avatar" style="width:100%;height:100%;object-fit:cover;">';
    };
    reader.readAsDataURL(this.files[0]);
  });

  var phoneInput = document.getElementById('phoneInput');
  var phoneMsg = document.getElementById('phoneMsg');

  function validatePhone(value) {
    if (value === '') return null;
    var stripped = value.replace(/[\s\-()]/g, '');
    if (/[a-zA-Z]/.test(stripped)) return false;
    if (!/^\+?[0-9]{7,15}$/.test(stripped)) return false;
    return true;
  }

  function showPhoneState(valid) {
    phoneInput.classList.remove('phone-error', 'phone-ok');
    phoneMsg.classList.remove('show', 'error', 'ok');
    if (valid === null) return;
    if (valid) {
      phoneInput.classList.add('phone-ok');
      phoneMsg.textContent = '✓ Valid phone number';
      phoneMsg.classList.add('show', 'ok');
    } else {
      phoneInput.classList.add('phone-error');
      phoneMsg.textContent = '✗ Enter numbers only, 7–15 digits (e.g. +639171234567)';
      phoneMsg.classList.add('show', 'error');
    }
  }

  phoneInput.addEventListener('keypress', function(e) {
    if (!/[0-9\+\-\(\)\s]/.test(e.key) && !['Backspace', 'Delete', 'Tab', 'ArrowLeft', 'ArrowRight'].includes(e.key)) {
      e.preventDefault();
    }
  });
  phoneInput.addEventListener('input', function() {
    showPhoneState(validatePhone(this.value));
  });
  phoneInput.addEventListener('blur', function() {
    showPhoneState(validatePhone(this.value));
  });
  phoneInput.closest('form').addEventListener('submit', function(e) {
    var valid = validatePhone(phoneInput.value);
    showPhoneState(valid);
    if (valid === false) {
      e.preventDefault();
      phoneInput.focus();
    }
  });

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
    bar.style.width = ['0%', '25%', '50%', '75%', '100%'][score] || '0%';
    bar.style.background = ['', '#a02c2c', '#c9972b', '#1b6b6b', '#1b6b6b'][score] || '';
    lbl.textContent = ['', 'Weak', 'Fair', 'Strong', 'Very Strong'][score] || '';
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
    inp.type = inp.type === 'text' ? 'password' : 'text';
    btn.innerHTML = inp.type === 'text' ? '<i class="bi bi-eye-slash"></i>' : '<i class="bi bi-eye"></i>';
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
    var savedTab = sessionStorage.getItem('profileTab');
    if (savedTab) {
      var tabBtn = document.querySelector('.tab-btn[onclick*="' + savedTab + '"]');
      if (tabBtn) switchTab(savedTab, tabBtn);
      sessionStorage.removeItem('profileTab');
    }

    <?php if ($success): ?>
        (function() {
          var activePanel = document.querySelector('.tab-panel.active');
          if (!activePanel) return;
          var submitBtn = activePanel.querySelector('.btn-save');
          if (!submitBtn) return;
          markSaved(submitBtn, submitBtn.innerHTML);
        })();
    <?php endif; ?>

    document.querySelectorAll('.btn-save').forEach(function(b) {
      var form = b.closest('form');
      if (!form) return;
      form.addEventListener('submit', function(e) {
        if (e.defaultPrevented) return;
        var panel = b.closest('.tab-panel');
        if (panel) sessionStorage.setItem('profileTab', panel.id.replace('tab-', ''));
        b.disabled = true;
        b.innerHTML = '<i class="bi bi-arrow-repeat"></i> Saving…';
      });
    });
  })();

  (function() {
    var personalForm = document.querySelector('#tab-personal form');
    if (!personalForm) return;

    var pendingAvatarDataUrl = null;

    document.getElementById('avatarInput').addEventListener('change', function() {
      if (!this.files[0]) return;
      var reader = new FileReader();
      reader.onload = function(e) {
        pendingAvatarDataUrl = e.target.result;
        document.getElementById('avatarPreview').innerHTML =
          '<img src="' + pendingAvatarDataUrl + '" alt="Avatar" style="width:100%;height:100%;object-fit:cover;">';
      };
      reader.readAsDataURL(this.files[0]);
    });

    personalForm.addEventListener('submit', function(e) {
      e.preventDefault();

      var btn = personalForm.querySelector('.btn-save');
      var originalHTML = btn.innerHTML;
      btn.disabled = true;
      btn.innerHTML = '<i class="bi bi-arrow-repeat"></i> Saving…';

      fetch(window.location.href, {
          method: 'POST',
          body: new FormData(personalForm)
        })
        .then(r => r.text())
        .then(html => {
          var parser = new DOMParser();
          var doc = parser.parseFromString(html, 'text/html');

          var newUsername = doc.querySelector('#tab-personal input[name="username"]')?.value ?? '';
          var newFirstName = doc.querySelector('#tab-personal input[name="firstName"]')?.value ?? '';
          var newLastName = doc.querySelector('#tab-personal input[name="lastName"]')?.value ?? '';
          var newFullName = (newFirstName + ' ' + newLastName).trim();

          var topbarName = document.querySelector('.nav-profile span');
          if (topbarName && newUsername) topbarName.textContent = newUsername;

          var dropHeader = document.querySelector('.dropdown-header h6');
          if (dropHeader && newUsername) dropHeader.textContent = newUsername;

          var dropSub = document.querySelector('.dropdown-header span');
          if (dropSub && newFullName) {
            var role = dropSub.textContent.split('·')[1]?.trim() ?? 'User';
            dropSub.textContent = newFullName + ' · ' + role;
          }

          var topbarImg = document.getElementById('topbarAvatarImg');
          if (topbarImg) {
            if (pendingAvatarDataUrl) {
              topbarImg.src = pendingAvatarDataUrl;
              pendingAvatarDataUrl = null;
            } else {
              var serverSrc = doc.getElementById('topbarAvatarImg')?.getAttribute('src');
              if (serverSrc) topbarImg.src = serverSrc;
            }
          }

          btn.classList.add('saved');
          btn.innerHTML = '<i class="bi bi-check-circle-fill"></i> Saved!';
          setTimeout(function() {
            btn.classList.remove('saved');
            btn.innerHTML = originalHTML;
            btn.disabled = false;
          }, 3000);
        })
        .catch(() => {
          btn.innerHTML = '<i class="bi bi-x-circle"></i> Error — try again';
          setTimeout(function() {
            btn.innerHTML = originalHTML;
            btn.disabled = false;
          }, 2500);
        });
    });
  })();
</script>

<?php include('./includes/footer.php'); ?>