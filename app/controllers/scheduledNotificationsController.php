<?php
include __DIR__ . '/../config/config.php';
include __DIR__ . '/userNotificationController.php';
include __DIR__ . '/notificationController.php';

$today    = date('Y-m-d');
$tomorrow = date('Y-m-d', strtotime('+1 day'));

$closingSoon = $conn->query("
    SELECT id, title FROM surveys
    WHERE status = 'published'
      AND end_date = '{$tomorrow}'
");

while ($survey = $closingSoon->fetch_assoc()) {
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

$expired = $conn->query("
    SELECT id, title FROM surveys
    WHERE status = 'published'
      AND end_date IS NOT NULL
      AND end_date < '{$today}'
");

while ($survey = $expired->fetch_assoc()) {
    $conn->query("UPDATE surveys SET status = 'closed' WHERE id = {$survey['id']}");
    notif_insert($conn, 'auto_closed', $survey['title'], $survey['id']);
    user_notif_survey_closed($conn, $survey['id'], $survey['title']);
}
