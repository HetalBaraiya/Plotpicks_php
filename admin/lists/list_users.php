<?php
require_once __DIR__ . '/../db.php';
$users = $pdo->query("SELECT * FROM users ORDER BY id")->fetchAll();
?>

<table>
  <thead>
    <tr>
      <th>ID</th>
      <th>Username</th>
      <th>Email</th>
      <th>Role</th>
      <th>Created At</th>
      <th>Updated At</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($users as $user): ?>
      <tr>
        <td><?= $user['id'] ?></td>
        <td><?= htmlspecialchars($user['username']) ?></td>
        <td><?= htmlspecialchars($user['email']) ?></td>
        <td><?= $user['is_admin'] ? 'admin' : 'user' ?></td>
        <td><?= $user['created_at'] ?? '-' ?></td>
        <td><?= $user['updated_at'] ?? '-' ?></td>
        <td>
          <a href="actions/user_action.php?action=delete&id=<?= $user['id'] ?>" class="button-delete" onclick="return confirm('Delete this user?')">Delete</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
