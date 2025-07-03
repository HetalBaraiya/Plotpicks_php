<?php
require_once 'db.php';

$id = $_GET['id'];
$book = $pdo->query("SELECT * FROM books WHERE id = $id")->fetch();
$genres = $pdo->query("SELECT * FROM genres ORDER BY name")->fetchAll();
?>

<form class="admin-form" action="actions/book_action.php" method="POST" enctype="multipart/form-data">
  <input type="hidden" name="action" value="edit">
  <input type="hidden" name="id" value="<?= $book['id'] ?>">
  <input type="text" name="title" value="<?= htmlspecialchars($book['title']) ?>" required>
  <input type="text" name="author" value="<?= htmlspecialchars($book['author']) ?>" required>
  <select name="genre_id" required>
    <?php foreach ($genres as $g): ?>
      <option value="<?= $g['id'] ?>" <?= $book['genre_id'] == $g['id'] ? 'selected' : '' ?>>
        <?= htmlspecialchars($g['name']) ?>
      </option>
    <?php endforeach; ?>
  </select>
  <textarea name="description"><?= htmlspecialchars($book['description']) ?></textarea>
  <input type="file" name="cover_image">
  <button type="submit">Update Book</button>
</form>
