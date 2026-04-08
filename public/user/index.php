<?php 
include('../../app/middleware/user.php'); 

$logged_in_name = isset($_SESSION['name']) ? $_SESSION['name'] : "Student";
$logged_in_id   = isset($_SESSION['student_id']) ? $_SESSION['student_id'] : "STU-2026";
$page = basename($_SERVER['PHP_SELF']); 

$name_parts = explode(' ', $logged_in_name);
$initials = strtoupper(substr($name_parts[0], 0, 1) . (isset($name_parts[1]) ? substr($name_parts[1], 0, 1) : ''));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuickQuery | Student Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@300;400;500;600&display=swap');

        :root {
            --ink:       #0f0e0d;
            --ink-2:     #3a3835;
            --ink-3:     #7a776f;
            --paper:     #f7f5f0;
            --paper-2:   #eceae3;
            --paper-3:   #e0ddd4;
            --gold:      #c9972b;
            --pass:      #2a6b4a;
            --fail:      #a02c2c;
            --radius:    14px;
            --sidebar-w: 260px;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--paper);
            color: var(--ink);
            display: flex;
        }

        #sidebar {
            width: var(--sidebar-w);
            height: 100vh;
            background: #fff;
            border-right: 1.5px solid var(--paper-3);
            position: fixed;
            padding: 1.5rem 1.5rem;
            display: flex;
            flex-direction: column;
            z-index: 1001;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            margin-bottom: 2.5rem;
            text-decoration: none;
        }

        .sidebar-brand img {
            height: 48px;
            width: auto;
        }

        .nav-heading {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--ink-3);
            margin: 1.5rem 0 0.75rem 0.5rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            text-decoration: none;
            color: var(--ink-2);
            font-size: 14px;
            font-weight: 500;
            border-radius: 8px;
            transition: 0.2s;
        }

        .nav-link.active { background: var(--paper-2); color: var(--ink); }
        .nav-link:hover:not(.active) { background: var(--paper); }

        #main-wrapper {
            margin-left: var(--sidebar-w);
            width: calc(100% - var(--sidebar-w));
            min-height: 100vh;
        }

        nav.topbar {
            height: 70px;
            padding: 0 2.5rem;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            background: #fff;
            border-bottom: 1.5px solid var(--paper-3);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .user-dropdown { position: relative; cursor: pointer; }

        .user-trigger {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 6px 12px;
            border-radius: 10px;
            transition: 0.2s;
        }

        .user-trigger:hover { background: var(--paper); }

        .avatar {
            width: 38px;
            height: 38px;
            background: var(--paper-2);
            border: 1.5px solid var(--paper-3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: var(--gold);
            font-size: 12px;
        }

        .dropdown-menu {
            position: absolute;
            top: 110%;
            right: 0;
            width: 200px;
            background: #fff;
            border: 1.5px solid var(--paper-3);
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            display: none;
            flex-direction: column;
            overflow: hidden;
        }

        .dropdown-menu.show { display: flex; }

        .dropdown-item {
            padding: 12px 16px;
            font-size: 14px;
            text-decoration: none;
            color: var(--ink);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .dropdown-item:hover { background: var(--paper); }

        .logout-form {
            margin: 0;
            padding: 0;
            border-top: 1px solid var(--paper-3);
        }

        .logout-form button {
            width: 100%;
            background: none;
            border: none;
            cursor: pointer;
            padding: 12px 16px;
            font-size: 14px;
            font-family: 'DM Sans', sans-serif;
            color: var(--fail);
            display: flex;
            align-items: center;
            gap: 10px;
            text-align: left;
        }

        .logout-form button:hover { background: var(--paper); }

        .container { max-width: 1000px; margin: 3rem auto; padding: 0 2rem; }

        header h1 {
            font-family: 'DM Serif Display', serif;
            font-size: 2.8rem;
            font-weight: 400;
            margin-bottom: 0.5rem;
        }

        header h1 em { color: var(--gold); font-style: italic; }
        header p { color: var(--ink-3); margin-bottom: 3rem; }

        .section-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--ink-3);
            margin-bottom: 1.5rem;
            border-bottom: 1px solid var(--paper-3);
            padding-bottom: 8px;
        }

        .survey-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 4rem;
        }

        .card {
            background: #fff;
            border: 1.5px solid var(--paper-3);
            border-radius: var(--radius);
            padding: 1.5rem;
            transition: 0.2s;
            display: flex;
            flex-direction: column;
        }

        .card:hover {
            transform: translateY(-3px);
            border-color: var(--gold);
            box-shadow: 0 5px 15px rgba(0,0,0,0.03);
        }

        .btn-primary {
            background: var(--ink);
            color: var(--paper);
            text-decoration: none;
            padding: 10px;
            border-radius: 10px;
            text-align: center;
            font-size: 14px;
            font-weight: 600;
            margin-top: 1rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border: 1.5px solid var(--paper-3);
            border-radius: var(--radius);
            overflow: hidden;
        }

        th {
            text-align: left;
            padding: 12px 20px;
            background: var(--paper-2);
            font-size: 11px;
            color: var(--ink-3);
            text-transform: uppercase;
        }

        td {
            padding: 15px 20px;
            border-bottom: 1px solid var(--paper-2);
            font-size: 14px;
        }
    </style>
</head>
<body>

    <aside id="sidebar">
        <a href="index.php" class="sidebar-brand">
            <img src="../admin/assets/img/logo.png" alt="QuickQuery">
        </a>

        <div class="nav-heading">Overview</div>
        <a href="index.php" class="nav-link active"><i class="bi bi-grid"></i> Dashboard</a>

        <div class="nav-heading">Activities</div>
        <a href="surveys.php" class="nav-link"><i class="bi bi-journal-text"></i> My Surveys</a>
        <a href="results.php" class="nav-link"><i class="bi bi-bar-chart"></i> Results</a>
    </aside>

    <div id="main-wrapper">
        <nav class="topbar">
            <div class="user-dropdown" id="userDropdown">
                <div class="user-trigger">
                    <div style="text-align: right">
                        <div style="font-size: 13px; font-weight: 600;"><?= htmlspecialchars($logged_in_name) ?></div>
                        <div style="font-size: 11px; color: var(--ink-3);"><?= htmlspecialchars($logged_in_id) ?></div>
                    </div>
                    <div class="avatar"><?= $initials ?></div>
                </div>
                <div class="dropdown-menu" id="dropdownMenu">
                    <a href="profile.php" class="dropdown-item"><i class="bi bi-person"></i> Profile</a>
                    <form method="POST" action="" class="logout-form">
                        <button type="submit" name="logoutButton">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </nav>

        <div class="container">
            <header>
                <h1>Welcome back, <em><?= htmlspecialchars(explode(' ', $logged_in_name)[0]) ?></em>.</h1>
                <p>You have 2 surveys waiting for your response.</p>
            </header>

            <div class="section-label">Available Surveys</div>
            <div class="survey-grid">
                <div class="card">
                    <span style="color:var(--gold); font-size:10px; font-weight:700; text-transform:uppercase;">Academic</span>
                    <h3 style="font-family:'DM Serif Display'; margin: 10px 0;">Mid-Term Instructor Evaluation</h3>
                    <p style="font-size:13px; color:var(--ink-3); flex-grow:1;">Feedback for the current semester curriculum.</p>
                    <a href="#" class="btn-primary">Start Survey</a>
                </div>
                <div class="card">
                    <span style="color:var(--gold); font-size:10px; font-weight:700; text-transform:uppercase;">Facilities</span>
                    <h3 style="font-family:'DM Serif Display'; margin: 10px 0;">Campus Tech &amp; Wi-Fi Audit</h3>
                    <p style="font-size:13px; color:var(--ink-3); flex-grow:1;">Help us improve student common area connectivity.</p>
                    <a href="#" class="btn-primary">Start Survey</a>
                </div>
            </div>

            <div class="section-label">Recent Submissions</div>
            <table>
                <thead>
                    <tr>
                        <th>Survey Name</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>School Admin Performance Audit</strong></td>
                        <td>Mar 25, 2026</td>
                        <td style="color:var(--pass); font-weight:600;">Completed</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        const trigger = document.getElementById('userDropdown');
        const menu = document.getElementById('dropdownMenu');

        trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            menu.classList.toggle('show');
        });

        window.addEventListener('click', () => {
            menu.classList.remove('show');
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php if (isset($_SESSION['message']) && isset($_SESSION['code']) && $_SESSION['code'] != ''): ?>
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
    endif; ?>

</body>
</html>
