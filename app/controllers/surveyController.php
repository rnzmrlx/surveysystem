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

// ── VIEW SURVEY (GET request) ─────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {

    $surveyId = (int)$_GET['id'];

    if ($surveyId <= 0) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Invalid ID']);
        exit;
    }

    $surveyRes = $conn->query("SELECT * FROM surveys WHERE id = $surveyId LIMIT 1");
    if (!$surveyRes || $surveyRes->num_rows === 0) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Survey not found']);
        exit;
    }
    $survey = $surveyRes->fetch_assoc();

    $questionsRes = $conn->query("SELECT * FROM questions WHERE survey_id = $surveyId ORDER BY id ASC");
    $questions = [];
    while ($q = $questionsRes->fetch_assoc()) {
        $questions[] = [
            'id'            => (int)$q['id'],
            'question_text' => $q['question_text'],
            'question_type' => $q['question_type'],
            'is_required'   => (int)($q['is_required'] ?? 1),
            'options'       => json_decode($q['options'] ?? '[]', true),
        ];
    }

    $usersRes      = $conn->query("SELECT COUNT(*) as cnt FROM users WHERE role = 'user'");
    $totalStudents = (int)$usersRes->fetch_assoc()['cnt'];

    $rRes = $conn->query("
        SELECT r.id          AS response_id,
               r.submitted_at,
               u.firstName,
               u.middleName,
               u.lastName,
               u.emailAddress
        FROM   responses r
        JOIN   users     u ON u.id = r.user_id
        WHERE  r.survey_id = $surveyId
        ORDER  BY r.submitted_at DESC
    ");

    $responses = [];
    while ($resp = $rRes->fetch_assoc()) {
        $rid = (int)$resp['response_id'];

        $nameParts = array_filter([
            trim($resp['firstName']  ?? ''),
            trim($resp['middleName'] ?? ''),
            trim($resp['lastName']   ?? ''),
        ]);
        $fullName = implode(' ', $nameParts);

        $aRes    = $conn->query("SELECT question_id, answer_text FROM answers WHERE response_id = $rid");
        $answers = [];
        while ($a = $aRes->fetch_assoc()) {
            $answers[(string)$a['question_id']] = $a['answer_text'];
        }

        $responses[] = [
            'response_id'  => $rid,
            'student_name' => $fullName,
            'email'        => $resp['emailAddress'] ?? '',
            'submitted_at' => $resp['submitted_at'],
            'answers'      => (object)$answers,
        ];
    }

    $responseCount = count($responses);
    $pct           = $totalStudents > 0 ? round(($responseCount / $totalStudents) * 100) : 0;

    ob_end_clean();
    echo json_encode([
        'success'       => true,
        'survey'        => $survey,
        'questions'     => $questions,
        'responses'     => $responses,
        'responseCount' => $responseCount,
        'totalStudents' => $totalStudents,
        'pct'           => $pct,
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

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

    $surveyRow   = $conn->query("SELECT title FROM surveys WHERE id = $id")->fetch_assoc();
    $surveyTitle = $surveyRow['title'] ?? 'Untitled';

    $stmt = $conn->prepare("UPDATE surveys SET status = ? WHERE id = ?");
    $stmt->bind_param('si', $newStatus, $id);
    $stmt->execute();
    $stmt->close();

   if ($action === 'publish') {
    notif_insert($conn, 'published', $surveyTitle, $id);
 user_notif_survey_published($conn, $id, $surveyTitle);
}

    if ($action === 'close') {
        // Notify admin panel
        notif_insert($conn, 'auto_closed', $surveyTitle, $id);
        // Notify users who responded via topbar
        user_notif_survey_closed($conn, $id, $surveyTitle);
    }

    ob_end_clean();
    echo json_encode(['success' => true]);
    exit;
}

// ── AUTO-CLOSE EXPIRED SURVEYS ────────────────────────────────────────────────
if ($action === 'auto_close') {

    $today   = date('Y-m-d');
    $expired = $conn->query("
        SELECT id, title FROM surveys
        WHERE status = 'published'
          AND end_date IS NOT NULL
          AND end_date < '{$today}'
    ");

    $count = 0;
    while ($survey = $expired->fetch_assoc()) {
        $conn->query("UPDATE surveys SET status = 'closed' WHERE id = {$survey['id']}");

        notif_insert($conn, 'auto_closed', $survey['title'], $survey['id']);
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