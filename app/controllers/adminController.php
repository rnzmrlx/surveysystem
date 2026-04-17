<?php
class AdminController {
    private $conn;

    // The constructor takes the database connection from your config file
    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Dashboard Statistics
     */
    public function getDashboardStats() {
        $stats = [];
        
        // Total Surveys
        $surveyRes = $this->conn->query("SELECT COUNT(*) as total FROM surveys");
        $stats['total_surveys'] = $surveyRes->fetch_assoc()['total'] ?? 0;

        // Total Students
        $userRes = $this->conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'student'");
        $stats['total_students'] = $userRes->fetch_assoc()['total'] ?? 0;

        // Total Responses
        $respRes = $this->conn->query("SELECT COUNT(*) as total FROM responses");
        $stats['total_responses'] = $respRes->fetch_assoc()['total'] ?? 0;

        return $stats;
    }

    /**
     * Logout Logic
     */
    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Clear session
        unset($_SESSION['user_id']);
        unset($_SESSION['userRole']);
        unset($_SESSION['authUser']);
        
        session_destroy();
        
        // Redirecting back to the login page
        header("Location: /surveysystem/public/login.php");
        exit(0);
    }
}

/**
 * HANDLE ACTIONS
 * This part listens for the logout button click
 */
if (isset($_POST['logoutButton'])) {
    // Correct Path: Go up 1 level from 'controllers' to 'app', then into 'config'
    require_once dirname(__DIR__) . '/config/config.php'; 
    
    // Ensure $conn exists from your config.php
    if (isset($conn)) {
        $adminCtrl = new AdminController($conn);
        $adminCtrl->logout();
    }
}