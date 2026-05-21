<?php
class AdminController
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getDashboardStats()
    {
        $stats = [];

        $surveyRes = $this->conn->query("SELECT COUNT(*) as total FROM surveys");
        $stats['total_surveys'] = $surveyRes->fetch_assoc()['total'] ?? 0;

        $userRes = $this->conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'student'");
        $stats['total_students'] = $userRes->fetch_assoc()['total'] ?? 0;

        $respRes = $this->conn->query("SELECT COUNT(*) as total FROM responses");
        $stats['total_responses'] = $respRes->fetch_assoc()['total'] ?? 0;

        return $stats;
    }

    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        unset($_SESSION['user_id']);
        unset($_SESSION['userRole']);
        unset($_SESSION['authUser']);

        session_destroy();

        header("Location: /surveysystem/public/login.php");
        exit(0);
    }
}

if (isset($_POST['logoutButton'])) {
    require_once dirname(__DIR__) . '/config/config.php';

    if (isset($conn)) {
        $adminCtrl = new AdminController($conn);
        $adminCtrl->logout();
    }
}
