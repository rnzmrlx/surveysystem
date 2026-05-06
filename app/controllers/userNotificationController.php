<?php
// ── userNotificationController.php (USER) ────────────────────────────────────
// Location: surveysystem/app/controllers/userNotificationController.php
// Handles user-only notifications.
// ─────────────────────────────────────────────────────────────────────────────

// ── INSERT a user notification ────────────────────────────────────────────
function user_notif_insert($conn, $userId, $type, $message, $surveyId = null, $surveyTitle = null) {
    // Prevent duplicate notifications of the same type for same survey/user
    if ($surveyId) {
        $check = $conn->prepare("
            SELECT id FROM user_notifications
            WHERE user_id = ? AND type = ? AND survey_id = ?
            LIMIT 1
        ");
        $check->bind_param('isi', $userId, $type, $surveyId);
        $check->execute();
        $exists = $check->get_result()->fetch_assoc();
        $check->close();
        if ($exists) return; // already notified
    }

$stmt = $conn->prepare("
    INSERT INTO user_notifications (user_id, type, message, survey_id, survey_title)
    VALUES (?, ?, ?, ?, ?)
");
$stmt->bind_param('issis', $userId, $type, $message, $surveyId, $surveyTitle);
$stmt->execute();
$stmt->close();
}

// ── NOTIFY all users about a new published survey ─────────────────────────
function user_notif_survey_published($conn, $surveyId, $surveyTitle) {
    $result = $conn->query("SELECT id FROM users WHERE role = 'user'");
    while ($row = $result->fetch_assoc()) {
        $msg = "A new survey is available: \"{$surveyTitle}\". Take it now!";
        user_notif_insert($conn, $row['id'], 'survey_published', $msg, $surveyId, $surveyTitle);
    }
}

// ── NOTIFY all users who responded that a survey is now closed ────────────
function user_notif_survey_closed($conn, $surveyId, $surveyTitle) {
    $stmt = $conn->prepare("
        SELECT DISTINCT user_id FROM responses WHERE survey_id = ?
    ");
    $stmt->bind_param('i', $surveyId);
    $stmt->execute();
    $users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    foreach ($users as $u) {
        $msg = "The survey \"{$surveyTitle}\" that you participated in has been closed.";
        user_notif_insert($conn, $u['user_id'], 'survey_closed', $msg, $surveyId, $surveyTitle);
    }
}

// ── NOTIFY a user their response was recorded ─────────────────────────────
function user_notif_response_recorded($conn, $userId, $surveyId, $surveyTitle) {
    $msg = "Your response to \"{$surveyTitle}\" has been successfully recorded. Thank you!";
    user_notif_insert($conn, $userId, 'response_recorded', $msg, $surveyId, $surveyTitle);
}

// ── FETCH notifications for the logged-in user ────────────────────────────
function user_notif_fetch($conn, $userId, $limit = 20) {
    $stmt = $conn->prepare("
        SELECT id, type, message, survey_title, is_read, created_at
        FROM   user_notifications
        WHERE  user_id = ?
        ORDER  BY created_at DESC
        LIMIT  ?
    ");
    $stmt->bind_param('ii', $userId, $limit);
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    $countStmt = $conn->prepare(
        "SELECT COUNT(*) AS cnt FROM user_notifications WHERE user_id = ? AND is_read = 0"
    );
    $countStmt->bind_param('i', $userId);
    $countStmt->execute();
    $unread = (int) $countStmt->get_result()->fetch_assoc()['cnt'];
    $countStmt->close();

    return ['unread' => $unread, 'notifications' => $rows];
}

// ── MARK all as read for the logged-in user ───────────────────────────────
function user_notif_mark_read($conn, $userId) {
    $stmt = $conn->prepare(
        "UPDATE user_notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0"
    );
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $stmt->close();
}

// ── HTTP action handler ───────────────────────────────────────────────────
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {

    ob_start();
    session_start();

    if (empty($_SESSION['authUser'])) {
        ob_end_clean();
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }

    include __DIR__ . '/../config/config.php';
    header('Content-Type: application/json');

$userId = (int) ($_SESSION['authUser']['user_id'] ?? 0);
    $action = $_GET['action'] ?? $_POST['action'] ?? '';

  if ($action === 'fetch') {
        ob_end_clean();
        echo json_encode(user_notif_fetch($conn, $userId));
        exit;
    }

    // ── ADD THIS ──────────────────────────────────────────────────────────
    if ($action === 'fetch_all') {
        $stmt = $conn->prepare("
            SELECT id, type, message, survey_id, survey_title, is_read, created_at
            FROM   user_notifications
            WHERE  user_id = ?
            ORDER  BY created_at DESC
            LIMIT  100
        ");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        ob_end_clean();
        echo json_encode(['notifications' => $rows]);
        exit;
    }
    // ─────────────────────────────────────────────────────────────────────


    if ($action === 'mark_read') {
        user_notif_mark_read($conn, $userId);
        ob_end_clean();
        echo json_encode(['success' => true]);
        exit;
    }

    ob_end_clean();
    echo json_encode(['error' => 'Unknown action']);
    exit;
}