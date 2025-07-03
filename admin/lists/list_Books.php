<?php
require_once __DIR__ . '/../db.php';

$stmt = $pdo->query("
  SELECT b.*, g.name AS genre_name
  FROM books b
  LEFT JOIN genres g ON b.genre_id = g.id
  ORDER BY b.id ASC
");

$books = $stmt->fetchAll();
?>

<?php if (count($books) === 0): ?>
  <p>No books found.</p>
<?php else: ?>
  <table>
    <thead>
      <tr>
        <th>Title</th><th>Author</th><th>Genre</th><th>Description</th><th>Image</th><th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($books as $book): ?>
        <tr>
          <td><?= htmlspecialchars($book['title']) ?></td>
          <td><?= htmlspecialchars($book['author']) ?></td>
          <td><?= htmlspecialchars($book['genre_name'] ?? 'N/A') ?></td>
          <td><?= htmlspecialchars($book['description']) ?></td>
          <td>
            <?php if ($book['cover_image']): ?>
              <img src="../uploads/<?= htmlspecialchars($book['cover_image']) ?>" width="60" />
            <?php else: ?> N/A <?php endif; ?>
          </td>
          <td>
            <a href="dashboard.php?section=Books&action=edit&id=<?= $book['id'] ?>">Edit</a> |
           <a href="actions/book_action.php?action=delete&id=<?= $book['id'] ?>" class="button-delete" onclick="return confirm('Delete this book?')">Delete</a>


          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>
