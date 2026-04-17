<?php
session_start();
require_once '../../config/config.php';

// Fetch categories for dropdown
$categories = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>New Survey - QuickQuery</title>
  <!-- Add your existing CSS links here -->
</head>
<body>

<!-- Your existing sidebar/navbar here -->

<div class="container">
  <h2>Create New Survey</h2>

  <form method="POST" action="../../controllers/create_survey.php">

    <div>
      <label>Survey Title</label>
      <input type="text" name="title" required placeholder="Enter survey title">
    </div>

    <div>
      <label>Description</label>
      <textarea name="description" placeholder="Enter description"></textarea>
    </div>

    <div>
      <label>Category</label>
      <select name="category_id" required>
        <option value="">-- Select Category --</option>
        <?php foreach ($categories as $cat): ?>
          <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div>
      <label>Status</label>
      <select name="status">
        <option value="pending">Pending</option>
        <option value="published">Published</option>
      </select>
    </div>

    <button type="submit">Create Survey</button>
    <a href="surveymanagement.php">Cancel</a>

  </form>
</div>

</body>
</html>