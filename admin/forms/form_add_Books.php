<?php
// Get genres for dropdown
require_once 'db.php';
$stmt = $pdo->query("SELECT * FROM genres ORDER BY name");
$genres = $stmt->fetchAll();
?>

<form class="admin-form" action="actions/book_action.php" method="POST" enctype="multipart/form-data">
  <input type="hidden" name="action" value="add">
  <input type="text" name="title" placeholder="Book Title" required>
  <input type="text" name="author" placeholder="Author" required>
  <select name="genre_id" required>
    <option value="">Select Genre</option>
    <?php foreach ($genres as $g): ?>
      <option value="<?= $g['id'] ?>"><?= htmlspecialchars($g['name']) ?></option>
    <?php endforeach; ?>
  </select>
  <textarea name="description" placeholder="Description" required></textarea>
  <input type="file" name="cover_image">
  <button type="submit">Submit Book</button>
</form>
