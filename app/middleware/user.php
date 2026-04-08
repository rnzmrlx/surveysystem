<?php
session_start();
$root = dirname(__DIR__);
include_once($root . '/config/config.php');

// ✅ Handle logout BEFORE the auth check
if (isset($_POST['logoutButton'])) {
    unset($_SESSION['user_id']);
    unset($_SESSION['userRole']);
    unset($_SESSION['authUser']);
    session_destroy();
    header("Location: /surveysystem/public/login");
    exit(0);
}

if (!isset($_SESSION['authUser'])) {
    $_SESSION['message'] = "You must be logged in to access this page.";
    $_SESSION['code'] = "error";
    header("Location: /surveysystem/public/login");
    exit();
} else {
    if ($_SESSION['userRole'] !== 'user') {
        $_SESSION['message'] = "You do not have permission to access this page.";
        $_SESSION['code'] = "error";
        header("Location: /surveysystem/public/admin/index");
        exit();
    }
}
