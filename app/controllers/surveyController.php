<?php
ob_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
include(__DIR__ . '/../config/config.php');
include(__DIR__ . '/notificationController.php');
include(__DIR__ . '/userNotificationController.php');
header('Content-Type: application/json');
$action = $_POST['action'] ?? '';

// ── CREATE ────────────────────────────────────────────────────────────────────
if ($action === 'create') {

    $title       = trim($_POST['title']       ?? '');
    $description = trim($_POST['description'] ?? '');
    $status      = trim($_POST['status']      ?? 'pending');
    $start_date  = trim($_POST['start_date']  ?? '');
    $end_date    = trim($_POST['end_date']    ?? '');
    $questions   = json_decode($_POST['questions'] ?? '[]', true);

    if (empty($title)) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Title is required']);
        exit;
    }

    $user_id = $_SESSION['authUser']['user_id'] ?? 1;

    $stmt = $conn->prepare(
        "INSERT INTO surveys (user_id, title, description, status, start_date, end_date)
         VALUES (?, ?, ?, ?, ?, ?)"
    );
    $stmt->bind_param('isssss', $user_id, $title, $description, $status, $start_date, $end_date);

    if (!$stmt->execute()) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'DB error: ' . $conn->error]);
        exit;
    }

    $surveyId = $conn->insert_id;
    $stmt->close();

    foreach ($questions as $q) {
        $text        = trim($q['text']     ?? '');
        $type        = trim($q['type']     ?? 'text');
        $options     = json_encode($q['opts'] ?? []);
        $is_required = isset($q['required']) ? (int)$q['required'] : 1;

        if (empty($text)) continue;

        $qStmt = $conn->prepare(
            "INSERT INTO questions (survey_id, question_text, question_type, options, is_required)
             VALUES (?, ?, ?, ?, ?)"
        );
        $qStmt->bind_param('isssi', $surveyId, $text, $type, $options, $is_required);
        $qStmt->execute();
        $qStmt->close();
    }

    ob_end_clean();
    echo json_encode(['success' => true, 'survey_id' => $surveyId]);
    exit;
}

// ── UPDATE ────────────────────────────────────────────────────────────────────
if ($action === 'update') {

    $id          = (int)($_POST['id']          ?? 0);
    $title       = trim($_POST['title']        ?? '');
    $description = trim($_POST['description']  ?? '');
    $status      = trim($_POST['status']       ?? 'pending');
    $start_date  = trim($_POST['start_date']   ?? '');
    $end_date    = trim($_POST['end_date']     ?? '');
    $questions   = json_decode($_POST['questions'] ?? '[]', true);

    if ($id <= 0 || empty($title)) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'ID and title are required']);
        exit;
    }

    $stmt = $conn->prepare(
        "UPDATE surveys
         SET title = ?, description = ?, status = ?, start_date = ?, end_date = ?
         WHERE id = ?"
    );
    $stmt->bind_param('sssssi', $title, $description, $status, $start_date, $end_date, $id);

    if (!$stmt->execute()) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'DB error: ' . $conn->error]);
        exit;
    }
    $stmt->close();

    $conn->query("DELETE FROM questions WHERE survey_id = $id");

    foreach ($questions as $q) {
        $text        = trim($q['text']     ?? '');
        $type        = trim($q['type']     ?? 'text');
        $options     = json_encode($q['opts'] ?? []);
        $is_required = isset($q['required']) ? (int)$q['required'] : 1;

        if (empty($text)) continue;

        $qStmt = $conn->prepare(
            "INSERT INTO questions (survey_id, question_text, question_type, options, is_required)
             VALUES (?, ?, ?, ?, ?)"
        );
        $qStmt->bind_param('isssi', $id, $text, $type, $options, $is_required);
        $qStmt->execute();
        $qStmt->close();
    }

    ob_end_clean();
    echo json_encode(['success' => true]);
    exit;
}

// ── DELETE ────────────────────────────────────────────────────────────────────
if ($action === 'delete') {

    $id = (int)($_POST['id'] ?? 0);

    if ($id <= 0) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Invalid ID']);
        exit;
    }

    $conn->query("DELETE FROM questions WHERE survey_id = $id");
    $conn->query("DELETE FROM surveys   WHERE id = $id");

    ob_end_clean();
    echo json_encode(['success' => true]);
    exit;
}

// ── PUBLISH / CLOSE ───────────────────────────────────────────────────────────
if ($action === 'publish' || $action === 'close') {

    $id        = (int)($_POST['id'] ?? 0);
    $newStatus = $action === 'publish' ? 'published' : 'closed';

    if ($id <= 0) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Invalid ID']);
        exit;
    }

    // Fetch survey title for notifications
    $surveyRow = $conn->query("SELECT title FROM surveys WHERE id = $id")->fetch_assoc();
    $surveyTitle = $surveyRow['title'] ?? 'Untitled';

    $stmt = $conn->prepare("UPDATE surveys SET status = ? WHERE id = ?");
    $stmt->bind_param('si', $newStatus, $id);
    $stmt->execute();
    $stmt->close();

    // ── Notify when published ──────────────────────────────────────────────
if ($action === 'publish') {
        $testInsert = $conn->query("INSERT INTO user_notifications (user_id, type, message, survey_id, survey_title) VALUES (14, 'survey_published', 'direct debug', $id, '$surveyTitle')");
        error_log('Direct insert: ' . ($testInsert ? 'OK' : $conn->error));
        
        $users = $conn->query("SELECT id FROM users WHERE role = 'user'");
        error_log('Users found: ' . $users->num_rows);
        
        user_notif_survey_published($conn, $id, $surveyTitle);
    }

    // ── Notify when manually closed ───────────────────────────────────────
    if ($action === 'close') {
        // Notify admin
        notif_insert($conn, 'auto_closed', $surveyTitle, $id);
        // Notify users who responded
        user_notif_survey_closed($conn, $id, $surveyTitle);
    }

    ob_end_clean();
    echo json_encode(['success' => true]);
    exit;
}

// ── AUTO-CLOSE EXPIRED SURVEYS ────────────────────────────────────────────────
if ($action === 'auto_close') {

    $today = date('Y-m-d');
    $expired = $conn->query("
        SELECT id, title FROM surveys
        WHERE status = 'published'
          AND end_date IS NOT NULL
          AND end_date < '{$today}'
    ");

    $count = 0;
    while ($survey = $expired->fetch_assoc()) {
        $conn->query("UPDATE surveys SET status = 'closed' WHERE id = {$survey['id']}");

        // Notify admin
        notif_insert($conn, 'auto_closed', $survey['title'], $survey['id']);

        // Notify users who responded
        user_notif_survey_closed($conn, $survey['id'], $survey['title']);

        $count++;
    }

    ob_end_clean();
    echo json_encode(['success' => true, 'closed' => $count]);
    exit;
}

// ── Unknown action ────────────────────────────────────────────────────────────
ob_end_clean();
echo json_encode(['success' => false, 'message' => 'Unknown action']);
exit;