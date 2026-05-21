<?php
// ── notificationController.php (ADMIN) ───────────────────────────────────────
// Location: surveysystem/app/controllers/notificationController.php
// Handles admin-only notifications.
// ─────────────────────────────────────────────────────────────────────────────

// ── INSERT a notification (called internally by other controllers) ─────────
function notif_insert($conn, $type, $surveyTitle, $surveyId = null, $userId = null)
{
    $message = match ($type) {
        'accessed'        => "Someone accessed the survey: \"{$surveyTitle}\"",
        'answered'        => "Someone submitted a response to: \"{$surveyTitle}\"",
        'published'       => "Survey \"{$surveyTitle}\" has been published and users have been notified.",
        'auto_closed'     => "Survey \"{$surveyTitle}\" has expired and was automatically closed.",
        'user_registered' => $surveyTitle, // reused field carries the message for registration
        default           => "Survey activity on: \"{$surveyTitle}\"",
    };

    $stmt = $conn->prepare("
        INSERT INTO notifications (type, message, survey_id, survey_title, user_id)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->bind_param('ssisi', $type, $message, $surveyId, $surveyTitle, $userId);
    $stmt->execute();
    $stmt->close();
}

// ── FETCH recent notifications (with optional limit) ─────────────────────
function notif_fetch($conn, $limit = 20)
{
    $stmt = $conn->prepare("
        SELECT id, type, message, survey_title, is_read, created_at
        FROM   notifications
        ORDER  BY created_at DESC
        LIMIT  ?
    ");
    $stmt->bind_param('i', $limit);
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    $unread = (int) $conn->query(
        "SELECT COUNT(*) AS cnt FROM notifications WHERE is_read = 0"
    )->fetch_assoc()['cnt'];

    return ['unread' => $unread, 'notifications' => $rows];
}

// ── FETCH ALL notifications (no limit — for history page) ─────────────────
function notif_fetch_all($conn)
{
    $stmt = $conn->prepare("
        SELECT id, type, message, survey_title, is_read, created_at
        FROM   notifications
        ORDER  BY created_at DESC
    ");
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return ['notifications' => $rows];
}

// ── MARK all as read ──────────────────────────────────────────────────────
function notif_mark_read($conn)
{
    $conn->query("UPDATE notifications SET is_read = 1 WHERE is_read = 0");
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

    $action = $_GET['action'] ?? $_POST['action'] ?? '';

    if ($action === 'fetch') {
        $limit = max(1, min(200, (int) ($_GET['limit'] ?? 20)));
        ob_end_clean();
        echo json_encode(notif_fetch($conn, $limit));
        exit;
    }

    if ($action === 'fetch_all') {
        ob_end_clean();
        echo json_encode(notif_fetch_all($conn));
        exit;
    }

    if ($action === 'mark_read') {
        notif_mark_read($conn);
        ob_end_clean();
        echo json_encode(['success' => true]);
        exit;
    }

    if ($action === 'mark_one') {
        $id = (int) ($_GET['id'] ?? 0);
        if ($id > 0) {
            $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE id = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->close();
        }
        ob_end_clean();
        echo json_encode(['success' => true]);
        exit;
    }

    ob_end_clean();
    echo json_encode(['error' => 'Unknown action']);
    exit;
}