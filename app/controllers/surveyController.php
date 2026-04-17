<?php
// controllers/save_survey.php
include('../app/config/config.php');
session_start();

header('Content-Type: application/json');

$title        = trim($_POST['title'] ?? '');
$description  = trim($_POST['description'] ?? '');
$status       = in_array($_POST['status'], ['published', 'pending', 'closed'])
                ? $_POST['status'] : 'pending';
$questionsRaw = $_POST['questions'] ?? '[]';
$questions    = json_decode($questionsRaw, true);

// Get logged-in user ID from session (adjust key if yours differs)
$userId = $_SESSION['user_id'] ?? 1;

if (!$title) {
    echo json_encode(['success' => false, 'message' => 'Title is required']);
    exit;
}

// ── Insert into surveys ──────────────────────────────────────────────────────
$stmt = $conn->prepare(
    "INSERT INTO surveys (user_id, title, description, status, created_at)
     VALUES (?, ?, ?, ?, NOW())"
);
$stmt->bind_param("isss", $userId, $title, $description, $status);
$stmt->execute();
$surveyId = $stmt->insert_id;
$stmt->close();

if (!$surveyId) {
    echo json_encode(['success' => false, 'message' => 'Failed to insert survey']);
    exit;
}

// ── Insert questions ─────────────────────────────────────────────────────────
if (is_array($questions)) {
    // Match your column names: questions_text, type
    $qStmt = $conn->prepare(
        "INSERT INTO questions (survey_id, questions_text, type)
         VALUES (?, ?, ?)"
    );

    foreach ($questions as $q) {
        $text = trim($q['text'] ?? '');
        $type = $q['type'] ?? 'text';

        if (!$text) continue;

        $qStmt->bind_param("iss", $surveyId, $text, $type);
        $qStmt->execute();
        $questionId = $qStmt->insert_id;

        // ── Insert options (radio / checkbox only) ───────────────────────────
        // Match your column name: questions_id (not question_id)
        if (in_array($type, ['radio', 'checkbox']) && !empty($q['opts'])) {
            $oStmt = $conn->prepare(
                "INSERT INTO options (questions_id, option_text)
                 VALUES (?, ?)"
            );
            foreach ($q['opts'] as $optText) {
                $optText = trim($optText);
                if (!$optText) continue;
                $oStmt->bind_param("is", $questionId, $optText);
                $oStmt->execute();
            }
            $oStmt->close();
        }
    }
    $qStmt->close();
}

echo json_encode(['success' => true, 'id' => $surveyId]);
exit;