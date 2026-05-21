<?php
// ── scheduledNotifications.php ────────────────────────────────────────────
// Location: surveysystem/app/controllers/scheduledNotifications.php
//
// Run this file on a schedule — either:
//   (A) Cron job:  0 8 * * * php /path/to/surveysystem/app/controllers/scheduledNotifications.php
//   (B) Auto-run:  include it in user/index.php so it fires on every login
//
// It is safe to run multiple times — all checks are idempotent.
// ─────────────────────────────────────────────────────────────────────────────

include __DIR__ . '/../config/config.php';
include __DIR__ . '/userNotificationController.php';
include __DIR__ . '/notificationController.php';

$today    = date('Y-m-d');
$tomorrow = date('Y-m-d', strtotime('+1 day'));

// ── 1. CLOSING SOON — surveys ending tomorrow ─────────────────────────────
// Notify users who haven't answered yet about surveys closing in 1 day
$closingSoon = $conn->query("
    SELECT id, title FROM surveys
    WHERE status = 'published'
      AND end_date = '{$tomorrow}'
");

while ($survey = $closingSoon->fetch_assoc()) {
    // Get users who have NOT answered this survey
    $users = $conn->query("
        SELECT id FROM users
        WHERE role = 'user'
          AND id NOT IN (
              SELECT DISTINCT user_id FROM responses WHERE survey_id = {$survey['id']}
          )
    ");

    while ($user = $users->fetch_assoc()) {
        $already = $conn->query("
            SELECT id FROM user_notifications
            WHERE user_id  = {$user['id']}
              AND type      = 'closing_soon'
              AND survey_id = {$survey['id']}
              AND DATE(created_at) = '{$today}'
            LIMIT 1
        ")->fetch_assoc();

        if (!$already) {
            $msg = "Reminder: The survey \"{$survey['title']}\" closes tomorrow. Don't miss it!";
            user_notif_insert(
                $conn,
                $user['id'],
                'closing_soon',
                $msg,
                $survey['id'],
                $survey['title']
            );
        }
    }
}

// ── 2. INACTIVITY — users with no response in 7 days ─────────────────────
$cutoff = date('Y-m-d H:i:s', strtotime('-7 days'));

$inactiveUsers = $conn->query("
    SELECT u.id FROM users u
    WHERE u.role = 'user'
      AND (
          NOT EXISTS (SELECT 1 FROM responses r WHERE r.user_id = u.id)
          OR
          (SELECT MAX(submitted_at) FROM responses r WHERE r.user_id = u.id) < '{$cutoff}'
      )
      AND u.created_at < '{$cutoff}'
");

while ($user = $inactiveUsers->fetch_assoc()) {
    $alreadyNotified = $conn->query("
        SELECT id FROM user_notifications
        WHERE user_id = {$user['id']}
          AND type    = 'inactivity'
          AND created_at >= '{$cutoff}'
        LIMIT 1
    ")->fetch_assoc();

    if (!$alreadyNotified) {
        $hasSurveys = $conn->query("
            SELECT COUNT(*) AS cnt FROM surveys
            WHERE status = 'published'
              AND id NOT IN (
                  SELECT DISTINCT survey_id FROM responses WHERE user_id = {$user['id']}
              )
        ")->fetch_assoc()['cnt'];

        if ($hasSurveys > 0) {
            $msg = "You haven't taken any surveys in a while. There are {$hasSurveys} survey(s) waiting for you!";
            user_notif_insert(
                $conn,
                $user['id'],
                'inactivity',
                $msg,
                null,
                null
            );
        }
    }
}

// ── 3. AUTO-CLOSE expired surveys + notify affected users & admin ─────────
$expired = $conn->query("
    SELECT id, title FROM surveys
    WHERE status = 'published'
      AND end_date IS NOT NULL
      AND end_date < '{$today}'
");

while ($survey = $expired->fetch_assoc()) {
    $conn->query("UPDATE surveys SET status = 'closed' WHERE id = {$survey['id']}");

    // Notify admin
    notif_insert($conn, 'auto_closed', $survey['title'], $survey['id']);

    // Notify users who responded
    user_notif_survey_closed($conn, $survey['id'], $survey['title']);
}

// Uncomment below if running via browser directly (for testing):
// echo json_encode(['success' => true, 'message' => 'Scheduled notifications processed.']);