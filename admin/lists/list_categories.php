<?php
require_once __DIR__ . '/../db.php';

$genres = $pdo->query("SELECT * FROM genres ORDER BY id ASC")->fetchAll();
$edit_id = $_GET['edit_id'] ?? null;
?>

<table>
  <thead>
    <tr><th>ID</th><th>Name</th><th>Actions</th></tr>
  </thead>
  <tbody>
    <?php foreach ($genres as $genre): ?>
      <tr>
        <td><?= $genre['id'] ?></td>
        <td>
          <?php if ($edit_id == $genre['id']): ?>
            <form action="actions/category_action.php" method="POST" style="display:flex; gap:6px;">
              <input type="hidden" name="action" value="update">
              <input type="hidden" name="id" value="<?= $genre['id'] ?>">
              <input type="text" name="name" value="<?= htmlspecialchars($genre['name']) ?>" required>
              <button type="submit" class="button">Save</button>
              <a href="dashboard.php?section=Categories" class="button-delete">Cancel</a>
            </form>
          <?php else: ?>
            <?= htmlspecialchars($genre['name']) ?>
          <?php endif; ?>
        </td>
        <td>
          <?php if ($edit_id != $genre['id']): ?>
            <a href="dashboard.php?section=Categories&edit_id=<?= $genre['id'] ?>" class="button-edit">Edit</a>
            <a href="actions/category_action.php?action=delete&id=<?= $genre['id'] ?>" class="button-delete" onclick="return confirm('Delete this category?')">Delete</a>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
