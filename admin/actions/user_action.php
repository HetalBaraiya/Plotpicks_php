<?php
require_once '../db.php';

$action = $_GET['action'] ?? '';

if ($action === 'delete') {
  $id = $_GET['id'];
  $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
  $stmt->execute([$id]);
}

header("Location: ../dashboard.php?section=Users");
exit;
