<?php
include('../../app/middleware/user.php');
include('./includes/header.php');
include('./includes/topbar.php');
include('./includes/sidebar.php');
include('../../app/config/config.php');

$user_id = $_SESSION['auth_user']['user_id'];

// Get published surveys
$sql = "SELECT s.id, s.title, s.description, s.created_at,
        (SELECT COUNT(*) FROM responses r 
         WHERE r.survey_id = s.id AND r.user_id = $user_id) as answered
        FROM surveys s
        WHERE s.status = 'published'
        ORDER BY s.created_at DESC";

$result = mysqli_query($conn, $sql);

$surveys = [];
while ($row = mysqli_fetch_assoc($result)) {
    $surveys[] = $row;
}
?>

<style>
  body { background: #f7f5f0; font-family: 'DM Sans', sans-serif; }

  .main-wrapper {
    padding: 2.5rem;
    max-width: 1200px;
    margin: auto;
  }

  h1 {
    font-family: 'DM Serif Display', serif;
    font-size: 2.5rem;
    margin-bottom: 1.5rem;
  }

  .survey-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 18px;
  }

  .survey-card {
    background: #fff;
    border: 1.5px solid #e0ddd4;
    border-radius: 10px;
    padding: 1.3rem;
    transition: 0.2s;
  }

  .survey-card:hover {
    border-color: #c9972b;
    transform: translateY(-2px);
  }

  .survey-title {
    font-weight: 600;
    font-size: 16px;
    margin-bottom: 6px;
  }

  .survey-desc {
    font-size: 13px;
    color: #7a776f;
    margin-bottom: 12px;
  }

  .survey-meta {
    font-size: 12px;
    color: #999;
    margin-bottom: 12px;
  }

  .badge {
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
  }

  .answered {
    background: #d0eaea;
    color: #1b6b6b;
  }

  .not-answered {
    background: #f5e9cc;
    color: #8a6318;
  }

  .btn {
    display: inline-block;
    padding: 8px 14px;
    font-size: 12px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 600;
    transition: 0.15s;
  }

  .btn-answer {
    background: #c9972b;
    color: #fff;
  }

  .btn-answer:hover {
    background: #8a6318;
  }

  .btn-disabled {
    background: #ccc;
    color: #666;
    pointer-events: none;
  }

</style>

<div class="main-wrapper">

  <h1>My Surveys</h1>

  <?php if (empty($surveys)): ?>
    <p style="color:#777;">No available surveys right now.</p>
  <?php else: ?>

    <div class="survey-grid">
      <?php foreach ($surveys as $s): ?>
        <div class="survey-card">

          <div class="survey-title"><?= htmlspecialchars($s['title']) ?></div>

          <?php if (!empty($s['description'])): ?>
            <div class="survey-desc"><?= htmlspecialchars($s['description']) ?></div>
          <?php endif; ?>

          <div class="survey-meta">
            Created: <?= date('M d, Y', strtotime($s['created_at'])) ?>
          </div>

          <?php if ($s['answered'] > 0): ?>
            <span class="badge answered">Answered</span>
            <br><br>
            <a href="#" class="btn btn-disabled">Already Submitted</a>
          <?php else: ?>
            <span class="badge not-answered">Not Answered</span>
            <br><br>
            <a href="take_survey.php?id=<?= $s['id'] ?>" class="btn btn-answer">Answer Survey</a>
          <?php endif; ?>

        </div>
      <?php endforeach; ?>
    </div>

  <?php endif; ?>

</div>

<?php include('./includes/footer.php'); ?>