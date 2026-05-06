<?php
include(__DIR__ . '/../middleware/user.php');
include(__DIR__ . '/../config/config.php');
include(__DIR__ . '/../../public/user/includes/header.php');
include(__DIR__ . '/../../public/user/includes/topbar.php');
include(__DIR__ . '/../../public/user/includes/sidebar.php');
include(__DIR__ . '/notificationController.php');
include(__DIR__ . '/userNotificationController.php');

$survey_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user_id   = $_SESSION['authUser']['user_id'] ?? 0;
// ── Fetch the survey (published only) ───────────────────────────────────────
$stmt = $conn->prepare(
    "SELECT * FROM surveys WHERE id = ? AND status = 'published' LIMIT 1"
);
$stmt->bind_param('i', $survey_id);
$stmt->execute();
$survey = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$survey) {
    $error_page = true;
}

// ── Check for duplicate submission ──────────────────────────────────────────
$already_submitted = false;
if (!empty($survey) && $user_id) {
    $stmt = $conn->prepare(
        "SELECT id FROM responses WHERE survey_id = ? AND user_id = ? LIMIT 1"
    );
    $stmt->bind_param('ii', $survey_id, $user_id);
    $stmt->execute();
    $already_submitted = (bool)$stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// ── Fetch questions ──────────────────────────────────────────────────────────
$questions = [];
if (!empty($survey) && !$already_submitted) {
    $stmt = $conn->prepare(
        "SELECT * FROM questions WHERE survey_id = ? ORDER BY id ASC"
    );
    $stmt->bind_param('i', $survey_id);
    $stmt->execute();
    $questions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// ── Handle form submission ───────────────────────────────────────────────────
$success      = false;
$form_errors  = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($survey) && !$already_submitted) {

    foreach ($questions as $q) {
    $qid      = $q['id'];
    $type     = $q['type'] ?? $q['question_type'];
    $required = (bool)($q['is_required'] ?? 1);

    if (!$required) continue;  // ← skip optional questions

    if (in_array($type, ['checkbox'])) {
        if (empty($_POST['answer'][$qid])) {
            $form_errors[$qid] = 'Please select at least one option.';
        }
    } elseif (in_array($type, ['radio', 'scale'])) {
        if (!isset($_POST['answer'][$qid]) || $_POST['answer'][$qid] === '') {
            $form_errors[$qid] = 'This field is required.';
        }
    } else {
        if (empty(trim($_POST['answer'][$qid] ?? ''))) {
            $form_errors[$qid] = 'This field is required.';
        }
    }
}

    if (empty($form_errors)) {
        $conn->begin_transaction();
        try {
            $stmt = $conn->prepare(
                "INSERT INTO responses (survey_id, user_id) VALUES (?, ?)"
            );
            $stmt->bind_param('ii', $survey_id, $user_id);
            $stmt->execute();
            $response_id = $stmt->insert_id;
            $stmt->close();

            $stmt = $conn->prepare(
                "INSERT INTO answers (response_id, question_id, answer_text) VALUES (?, ?, ?)"
            );

            foreach ($questions as $q) {
                $qid  = $q['id'];
                $type = $q['type'] ?? $q['question_type'];

                if ($type === 'checkbox') {
                    $answer_text = implode(', ', $_POST['answer'][$qid]);
                } elseif ($type === 'radio' || $type === 'scale') {
                    $answer_text = $_POST['answer'][$qid];
                } else {
                    $answer_text = trim($_POST['answer'][$qid] ?? '');
                }

                $stmt->bind_param('iis', $response_id, $qid, $answer_text);
                $stmt->execute();
            }
            $stmt->close();

            $conn->commit();
            $success           = true;
            $already_submitted = true;
  notif_insert($conn, 'answered', $survey['title'], $survey_id);

            // ── Notify user that their response was recorded ───────────────
            user_notif_response_recorded($conn, $user_id, $survey_id, $survey['title']);
        } catch (Exception $e) {
            $conn->rollback();
            $form_errors['_global'] = 'Something went wrong. Please try again.';
        }
    }
}

// ── SVG icon helpers ─────────────────────────────────────────────────────────
function icon_check($color) {
    return '<svg width="34" height="34" viewBox="0 0 24 24" fill="none"
             stroke="' . $color . '" stroke-width="2.8"
             stroke-linecap="round" stroke-linejoin="round">
               <polyline points="20 6 9 17 4 12"/>
             </svg>';
}
function icon_warn($color) {
    return '<svg width="32" height="32" viewBox="0 0 24 24" fill="none"
             stroke="' . $color . '" stroke-width="2.5"
             stroke-linecap="round" stroke-linejoin="round">
               <circle cx="12" cy="12" r="10"/>
               <line x1="12" y1="8" x2="12" y2="12"/>
               <line x1="12" y1="16" x2="12.01" y2="16"/>
             </svg>';
}
?>

<style>
  /* ── Palette ────────────────────────────────────────────────────────────── */
  :root {
    --ink:        #0f0e0d;
    --ink-2:      #3a3835;
    --ink-3:      #7a776f;
    --paper:      #f7f5f0;
    --paper-2:    #eceae3;
    --paper-3:    #e0ddd4;
    --gold:       #c9972b;
    --gold-light: #f5e9cc;
    --gold-dark:  #8a6318;
    --teal:       #1b6b6b;
    --teal-lt:    #d0eaea;
    --rose:       #a02c2c;
    --rose-lt:    #f5dede;
    --radius:     10px;
    --shadow:     0 2px 16px rgba(15, 14, 13, 0.07);
  }

  /* ── Wrapper ────────────────────────────────────────────────────────────── */
  .sv-wrap {
    max-width: 780px;
    margin: 2.5rem auto 4rem;
    padding: 0 1.25rem;
    font-family: var(--font-body, system-ui, sans-serif);
    color: var(--ink);
  }

  /* ── Hero banner ────────────────────────────────────────────────────────── */
  .sv-hero {
    background: var(--gold);
    color: #fff;
    border-radius: var(--radius) var(--radius) 0 0;
    padding: 2.5rem 2.5rem 2rem;
    position: relative;
    overflow: hidden;
  }
  .sv-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background: repeating-linear-gradient(
      -45deg,
      transparent, transparent 28px,
      rgba(5,0,0,.03) 28px, rgba(8,0,0,.03) 30px
    );
  }
  .sv-hero-tag {
    display: inline-block;
    font-size: .7rem;
    font-weight: 700;
    letter-spacing: .14em;
    text-transform: uppercase;
    color: var(--ink);
    margin-bottom: .55rem;
    position: relative;
  }
  .sv-hero h1 {
    font-family: var(--font-head, inherit);
    font-size: clamp(1.5rem, 3.8vw, 2.2rem);
    font-weight: 600;
    margin: 0 0 .55rem;
    line-height: 1.22;
    position: relative;
  }
  .sv-hero p {
    font-size: .92rem;
    color: rgba(255,255,255,.62);
    margin: 0;
    max-width: 560px;
    position: relative;
  }
  .sv-hero-meta {
    margin-top: 1.2rem;
    display: flex;
    gap: 1.25rem;
    flex-wrap: wrap;
    position: relative;
  }
  .sv-hero-meta span {
    font-size: .75rem;
    color: var(--ink);
    display: flex;
    align-items: center;
    gap: .35rem;
  }

  /* ── Progress bar ───────────────────────────────────────────────────────── */
  .sv-progress-bar {
    height: 4px;
    background: var(--paper-3);
  }
  .sv-progress-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--teal), var(--gold));
    width: 0%;
    transition: width .4s ease;
  }

  /* ── Form card ──────────────────────────────────────────────────────────── */
  .sv-form-card {
    background: #fff;
    border: 1px solid var(--paper-3);
    border-top: none;
    border-radius: 0 0 var(--radius) var(--radius);
    padding: 2.5rem;
    box-shadow: var(--shadow);
  }

  /* ── Question block ─────────────────────────────────────────────────────── */
  .sv-question {
    border: 1.5px solid var(--paper-3);
    border-radius: var(--radius);
    padding: 1.6rem 1.8rem;
    margin-bottom: 1.4rem;
    background: var(--paper);
    transition: border-color .2s, box-shadow .2s;
    position: relative;
  }
  .sv-question:focus-within {
    border-color: var(--teal);
    box-shadow: 0 0 0 3px rgba(27,107,107,.1);
  }
  .sv-question.has-error {
    border-color: var(--rose);
    box-shadow: 0 0 0 3px rgba(160,44,44,.1);
  }

  .sv-q-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: .35rem;
  }
  .sv-q-num {
    font-size: .7rem;
    font-weight: 700;
    letter-spacing: .12em;
    text-transform: uppercase;
    color: var(--teal);
  }
  .sv-badge {
    font-size: .67rem;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
    padding: .22rem .58rem;
    border-radius: 5px;
  }
  .sv-badge.required { background: var(--paper-2); color: var(--rose); }
  .sv-badge.optional { background: var(--paper-2); color: var(--gold); }

  .sv-q-label {
    font-family: var(--font-head, inherit);
    font-size: 1.05rem;
    font-weight: 600;
    color: var(--ink);
    margin: 0 0 1rem;
    line-height: 1.42;
  }

  /* ── Text / textarea ────────────────────────────────────────────────────── */
  .sv-input,
  .sv-textarea {
    width: 100%;
    box-sizing: border-box;
    border: 1.5px solid var(--paper-3);
    border-radius: 8px;
    padding: .7rem 1rem;
    font-family: var(--font-body, inherit);
    font-size: .93rem;
    color: var(--ink);
    background: #fff;
    outline: none;
    transition: border-color .2s, box-shadow .2s;
  }
  .sv-input:focus,
  .sv-textarea:focus {
    border-color: var(--teal);
    box-shadow: 0 0 0 3px rgba(27,107,107,.1);
  }
  .sv-textarea { resize: vertical; min-height: 110px; }

  /* ── Radio / checkbox options ───────────────────────────────────────────── */
  .sv-options { display: flex; flex-direction: column; gap: .5rem; }
  .sv-option {
    display: flex;
    align-items: center;
    gap: .75rem;
    padding: .65rem 1rem;
    border: 1.5px solid var(--paper-3);
    border-radius: 8px;
    background: #fff;
    cursor: pointer;
    transition: border-color .2s, background .2s;
    font-size: .93rem;
    color: var(--ink-2);
  }
  .sv-option:hover {
    border-color: var(--teal);
    background: var(--teal-lt);
  }
  .sv-option input[type="radio"],
  .sv-option input[type="checkbox"] {
    accent-color: var(--teal);
    width: 16px;
    height: 16px;
    flex-shrink: 0;
    cursor: pointer;
  }
  .sv-option input[type="radio"]:checked ~ span,
  .sv-option input[type="checkbox"]:checked ~ span {
    font-weight: 500;
    color: var(--ink);
  }

  /* ── Scale ──────────────────────────────────────────────────────────────── */
  .sv-scale {
    display: flex;
    gap: .5rem;
    flex-wrap: wrap;
    margin-top: .25rem;
  }
  .sv-scale-label {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: .4rem;
    font-size: .78rem;
    color: var(--ink-3);
    cursor: pointer;
    min-width: 30px;
  }
  .sv-scale-label input[type="radio"] {
    accent-color: var(--gold);
    width: 18px;
    height: 18px;
  }
  .sv-scale-ends {
    display: flex;
    justify-content: space-between;
    font-size: .73rem;
    color: var(--ink-3);
    margin-top: .55rem;
  }

  /* ── Error hint ─────────────────────────────────────────────────────────── */
  .sv-error-hint {
    font-size: .8rem;
    color: var(--rose);
    margin-top: .5rem;
    display: flex;
    align-items: center;
    gap: .3rem;
  }

  /* ── Divider ────────────────────────────────────────────────────────────── */
  .sv-divider {
    height: 1px;
    background: var(--paper-2);
    margin: 1.75rem 0 1.5rem;
  }

  /* ── Submit row ─────────────────────────────────────────────────────────── */
  .sv-submit-row {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: .75rem;
    margin-top: .25rem;
  }

  .sv-btn-submit {
    font-family: var(--font-body, inherit);
    font-size: .95rem;
    font-weight: 700;
    color: #fff;
    background: var(--teal);
    border: none;
    border-radius: 8px;
    padding: .82rem 2.2rem;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: .55rem;
    letter-spacing: .01em;
    transition: background .2s, transform .15s, box-shadow .2s;
    box-shadow: 0 4px 14px rgba(27,107,107,.25);
  }
  .sv-btn-submit:hover {
    background: var(--gold-dark);
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(138,99,24,.3);
  }
  .sv-btn-submit:active { transform: translateY(0); }

  .sv-btn-outline {
    font-family: var(--font-body, inherit);
    font-size: .9rem;
    font-weight: 600;
    color: var(--gold);
    background: transparent;
    border: 1.5px solid var(--paper-3);
    border-radius: 8px;
    padding: .82rem 1.5rem;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    text-decoration: none;
    transition: border-color .2s, background .2s;
  }
  .sv-btn-outline:hover {
    border-color: var(--teal);
    background: var(--teal-lt);
  }

  /* ── Global error alert ─────────────────────────────────────────────────── */
  .sv-alert {
    background: var(--rose-lt);
    border: 1px solid #e8b8b8;
    border-radius: 8px;
    padding: .9rem 1.2rem;
    font-size: .88rem;
    color: var(--rose);
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: .5rem;
  }

  /* ── State screens ──────────────────────────────────────────────────────── */
  .sv-state {
    text-align: center;
    padding: 3.5rem 2rem;
  }
  .sv-state-icon {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.25rem;
  }
  .sv-state-icon.success { background: var(--paper); }
  .sv-state-icon.error   { background: var(--rose-lt); }
  .sv-state-icon.done    { background: var(--paper-3); }

  .sv-state h2 {
    font-family: var(--font-head, inherit);
    font-size: 1.7rem;
    font-weight: 600;
    margin: 0 0 .5rem;
    color: var(--ink);
  }
  .sv-state p {
    color: var(--ink-3);
    font-size: .93rem;
    margin: 0 0 1.5rem;
  }

  /* ── Error page ─────────────────────────────────────────────────────────── */
  .sv-hero-solo { border-radius: var(--radius); }
</style>

<!-- ── Markup ──────────────────────────────────────────────────────────────── -->
<div class="main-wrapper">
  <div class="sv-wrap">

    <?php if (!empty($error_page)): ?>
    <!-- Survey not found / not published -->
    <div class="sv-hero sv-hero-solo">
      <div class="sv-state">
        <div class="sv-state-icon error">
          <?= icon_warn('#a02c2c') ?>
        </div>
        <h2>Survey Not Found</h2>
        <p>This survey doesn't exist or isn't open for responses.</p>
        <a href="mysurvey.php" class="sv-btn-outline">← Browse Surveys</a>
      </div>
    </div>

    <?php else: ?>

    <!-- Hero banner -->
    <div class="sv-hero">
      <div class="sv-hero-tag">Survey #<?= $survey_id ?></div>
      <h1><?= htmlspecialchars($survey['title']) ?></h1>
      <?php if (!empty($survey['description'])): ?>
        <p><?= htmlspecialchars($survey['description']) ?></p>
      <?php endif; ?>
      <div class="sv-hero-meta">
        <span>📅 Ends: <?= htmlspecialchars($survey['end_date']) ?></span>
        <span>❓ <?= count($questions) ?> question<?= count($questions) !== 1 ? 's' : '' ?></span>
      </div>
    </div>

    <!-- Progress bar (JS-driven) -->
    <div class="sv-progress-bar">
      <div class="sv-progress-fill" id="sv-progress"></div>
    </div>

    <!-- Form card -->
    <div class="sv-form-card">

      <?php if ($success): ?>
      <!-- Success state -->
      <div class="sv-state">
        <div class="sv-state-icon success">
          <?= icon_check('#1b6b6b') ?>
        </div>
        <h2>Thank You!</h2>
        <p>Your response has been recorded successfully.</p>
        <a href="mysurvey.php" class="sv-btn-outline">← Back to Surveys</a>
      </div>

      <?php elseif ($already_submitted): ?>
      <!-- Already submitted -->
      <div class="sv-state">
        <div class="sv-state-icon done">
          <?= icon_check('#1b6b6b') ?>
        </div>
        <h2>Already Submitted</h2>
        <p>You've already completed this survey. Thank you for your participation!</p>
        <a href="mysurvey.php" class="sv-btn-outline">← Browse Surveys</a>
      </div>

      <?php else: ?>
      <!-- Survey form -->

      <?php if (!empty($form_errors['_global'])): ?>
        <div class="sv-alert">⚠ <?= htmlspecialchars($form_errors['_global']) ?></div>
      <?php endif; ?>

      <form method="POST" id="sv-form" novalidate>
        <?php foreach ($questions as $idx => $q):
          $qid      = $q['id'];
          $type     = $q['type'] ?? $q['question_type'];
          $opts     = [];
          if (!empty($q['options'])) {
              $decoded = json_decode($q['options'], true);
              $opts    = is_array($decoded)
                ? $decoded
                : array_filter(array_map('trim', explode("\n", $q['options'])));
          }
          $has_err = isset($form_errors[$qid]);
          $prev    = $_POST['answer'][$qid] ?? null;
        ?>
        <div class="sv-question <?= $has_err ? 'has-error' : '' ?>" id="q-<?= $qid ?>">

          <div class="sv-q-header">
            <div class="sv-q-num">Question <?= $idx + 1 ?></div>
<span class="sv-badge <?= $q['is_required'] ? 'required' : 'optional' ?>">
  <?= $q['is_required'] ? 'Required' : 'Optional' ?>
</span>          </div>

          <p class="sv-q-label"><?= htmlspecialchars($q['question_text']) ?></p>

          <?php if ($type === 'text'): ?>
            <input
              type="text"
              class="sv-input"
              name="answer[<?= $qid ?>]"
              value="<?= htmlspecialchars($prev ?? '') ?>"
              placeholder="Your answer…"
            >

          <?php elseif ($type === 'textarea'): ?>
            <textarea
              class="sv-textarea"
              name="answer[<?= $qid ?>]"
              placeholder="Your answer…"
            ><?= htmlspecialchars($prev ?? '') ?></textarea>

          <?php elseif ($type === 'radio'): ?>
            <div class="sv-options">
              <?php foreach ($opts as $opt): ?>
              <label class="sv-option">
                <input
                  type="radio"
                  name="answer[<?= $qid ?>]"
                  value="<?= htmlspecialchars($opt) ?>"
                  <?= $prev === $opt ? 'checked' : '' ?>
                >
                <span><?= htmlspecialchars($opt) ?></span>
              </label>
              <?php endforeach; ?>
            </div>

          <?php elseif ($type === 'checkbox'): ?>
            <div class="sv-options">
              <?php
              $prev_arr = is_array($prev) ? $prev : [];
              foreach ($opts as $opt):
              ?>
              <label class="sv-option">
                <input
                  type="checkbox"
                  name="answer[<?= $qid ?>][]"
                  value="<?= htmlspecialchars($opt) ?>"
                  <?= in_array($opt, $prev_arr) ? 'checked' : '' ?>
                >
                <span><?= htmlspecialchars($opt) ?></span>
              </label>
              <?php endforeach; ?>
            </div>

          <?php elseif ($type === 'scale'): ?>
            <div class="sv-scale">
              <?php for ($i = 1; $i <= 5; $i++): ?>
              <label class="sv-scale-label">
                <input
                  type="radio"
                  name="answer[<?= $qid ?>]"
                  value="<?= $i ?>"
                  <?= (string)$prev === (string)$i ? 'checked' : '' ?>
                >
                <?= $i ?>
              </label>
              <?php endfor; ?>
            </div>
            <div class="sv-scale-ends">
              <span>1 — Not at all</span>
              <span>5 — Extremely</span>
            </div>

          <?php endif; ?>

          <?php if ($has_err): ?>
            <div class="sv-error-hint">⚠ <?= htmlspecialchars($form_errors[$qid]) ?></div>
          <?php endif; ?>

        </div>
        <?php endforeach; ?>

        <div class="sv-divider"></div>

        <div class="sv-submit-row">
          <button type="submit" class="sv-btn-submit">
            Submit Survey →
          </button>
        </div>
      </form>

      <?php endif; ?>
    </div><!-- /.sv-form-card -->

    <?php endif; ?>

  </div><!-- /.sv-wrap -->
</div><!-- /.main-wrapper -->

<script>
(function () {
  const questions = document.querySelectorAll('.sv-question');
  const bar       = document.getElementById('sv-progress');
  if (!bar || !questions.length) return;

  // ── Progress bar ──────────────────────────────────────────────────────────
  function updateProgress() {
    let answered = 0;
    questions.forEach(q => {
      const inputs = q.querySelectorAll('input, textarea');
      let filled = false;
      inputs.forEach(inp => {
        if (inp.type === 'radio' || inp.type === 'checkbox') {
          if (inp.checked) filled = true;
        } else if (inp.value.trim()) {
          filled = true;
        }
      });
      if (filled) answered++;
    });
    bar.style.width = Math.round((answered / questions.length) * 100) + '%';
  }

  document.getElementById('sv-form')?.addEventListener('input', updateProgress);
  updateProgress();

  // ── Highlight selected option row ─────────────────────────────────────────
  document.querySelectorAll('.sv-option input').forEach(inp => {
    inp.addEventListener('change', function () {
      if (this.type === 'radio') {
        const siblings = this.closest('.sv-options')?.querySelectorAll('.sv-option');
        siblings?.forEach(s => s.style.removeProperty('border-color'));
      }
      if (this.checked) {
        this.closest('.sv-option').style.borderColor = 'var(--teal)';
      } else {
        this.closest('.sv-option').style.removeProperty('border-color');
      }
    });
  });
})();
</script>

<?php include(__DIR__ . '/../../public/user/includes/footer.php'); ?>