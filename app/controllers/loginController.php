<?php session_start();
$root = dirname(__DIR__);
include_once($root . "/config/config.php");

function generate_uuid()
{
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff)
    );
}

// ═══════════════════════════════════════════════════════════
// LOGIN
// ═══════════════════════════════════════════════════════════
if (isset($_POST['loginButton'])) {
    $username = $_POST['username'];
    $inputPassword = $_POST['password'];

    // Fetch user by username only — never compare password in SQL
    $loginQuery = "SELECT `id`, `firstName`, `lastName`, `emailAddress`, `username`, `password`, `role`, `avatar` FROM `users` WHERE username = ? LIMIT 1";
    $stmt = $conn->prepare($loginQuery);

    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();
            $storedHash = $data['password'];

            // Support plain text (old) and bcrypt (new)
            $verified = false;
            if (password_verify($inputPassword, $storedHash)) {
                // Already bcrypt
                $verified = true;
            } elseif ($storedHash === $inputPassword) {
                // Plain text match — upgrade to bcrypt now
                $verified = true;
                $newHash = password_hash($inputPassword, PASSWORD_DEFAULT);
                $upg = $conn->prepare("UPDATE users SET password=? WHERE id=?");
                $upg->bind_param('si', $newHash, $data['id']);
                $upg->execute();
                $upg->close();
            }

            if ($verified) {
                $user_id  = $data['id'];
                $fullName = $data['firstName'] . ' ' . $data['lastName'];
                $userRole = $data['role'];

                $_SESSION['user_id']  = $user_id;
                $_SESSION['userRole'] = $userRole;
                $_SESSION['authUser'] = [
                    'user_id'   => $user_id,
                    'fullName'  => $fullName,
                    'firstName' => $data['firstName'],
                    'lastName'  => $data['lastName'],
                    'username'  => $data['username'],
                    'avatar'    => $data['avatar'] ?? '',  // ← needed for topbar photo
                ];

                $_SESSION['message'] = "Welcome $fullName";
                $_SESSION['code']    = "success";

                if ($userRole === 'admin') {
                    header("Location: /surveysystem/public/admin/index");
                } else {
                    header("Location: /surveysystem/public/user/index");
                }
                exit();
            } else {
                $_SESSION['message'] = "Invalid username or password";
                $_SESSION['code']    = "error";
                header("Location: /surveysystem/public/login");
                exit();
            }
        } else {
            $_SESSION['message'] = "Invalid username or password";
            $_SESSION['code']    = "error";
            header("Location: /surveysystem/public/login");
            exit();
        }
    } else {
        $_SESSION['message'] = "Something went wrong. Please try again.";
        $_SESSION['code']    = "error";
        header("Location: /surveysystem/public/login");
        exit();
    }
}

// ═══════════════════════════════════════════════════════════
// REGISTER
// ═══════════════════════════════════════════════════════════
if (isset($_POST['registerButton'])) {
    $firstName       = $_POST['firstName'];
    $middleName      = $_POST['middleName'];
    $lastName        = $_POST['lastName'];
    $emailAddress    = $_POST['emailAddress'];
    $username        = $_POST['username'];
    $password        = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
    $street          = $_POST['street'];
    $barangay        = $_POST['barangay'];
    $city            = $_POST['city'];
    $role            = 'user';
    $uuid            = generate_uuid();

    // Validate email format
    if (!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['message'] = "Invalid email format";
        $_SESSION['code']    = "error";
        header("Location: /surveysystem/public/registration");
        exit();
    }

    // Check if email already exists
    $chkEmail = $conn->prepare("SELECT id FROM users WHERE emailAddress = ? LIMIT 1");
    $chkEmail->bind_param('s', $emailAddress);
    $chkEmail->execute();
    $chkEmail->store_result();
    if ($chkEmail->num_rows > 0) {
        $_SESSION['message'] = "Email already exists";
        $_SESSION['code']    = "error";
        header("Location: /surveysystem/public/registration");
        exit();
    }
    $chkEmail->close();

    // Check if username already exists
    $chkUser = $conn->prepare("SELECT id FROM users WHERE username = ? LIMIT 1");
    $chkUser->bind_param('s', $username);
    $chkUser->execute();
    $chkUser->store_result();
    if ($chkUser->num_rows > 0) {
        $_SESSION['message'] = "Username already exists";
        $_SESSION['code']    = "error";
        header("Location: /surveysystem/public/registration");
        exit();
    }
    $chkUser->close();

    // Check passwords match
    if ($password !== $confirmPassword) {
        $_SESSION['message'] = "Passwords do not match";
        $_SESSION['code']    = "error";
        header("Location: /surveysystem/public/registration");
        exit();
    }

    // Hash the password before saving
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert user with hashed password
    $ins = $conn->prepare("INSERT INTO `users`(`uuid`,`firstName`,`middleName`,`lastName`,`emailAddress`,`username`,`password`,`street`,`barangay`,`city`,`role`) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
    $ins->bind_param('sssssssssss', $uuid, $firstName, $middleName, $lastName, $emailAddress, $username, $hashedPassword, $street, $barangay, $city, $role);

    if ($ins->execute()) {
        $_SESSION['message'] = "Registration successful. Please login.";
        $_SESSION['code']    = "success";
        header("Location: /surveysystem/public/login");
        exit();
    } else {
        $_SESSION['message'] = "Something went wrong. Please try again.";
        $_SESSION['code']    = "error";
        header("Location: /surveysystem/public/registration");
        exit();
    }
    $ins->close();
}

// ═══════════════════════════════════════════════════════════
// LOGOUT
// ═══════════════════════════════════════════════════════════
if (isset($_POST['logoutButton'])) {
    session_destroy();
    header("Location: /surveysystem/public/login");
    exit();
}
