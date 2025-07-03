<?php
require_once '../db.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'add') {
  $name = trim($_POST['name']);
  if ($name !== '') {
    $stmt = $pdo->prepare("INSERT INTO genres (name) VALUES (?)");
    $stmt->execute([$name]);
  }

} elseif ($action === 'update') {
  $id = $_POST['id'];
  $name = trim($_POST['name']);
  if ($id && $name !== '') {
    $stmt = $pdo->prepare("UPDATE genres SET name = ? WHERE id = ?");
    $stmt->execute([$name, $id]);
  }

} elseif ($action === 'delete') {
  $id = $_GET['id'];
  $stmt = $pdo->prepare("DELETE FROM genres WHERE id = ?");
  $stmt->execute([$id]);
}

header("Location: ../dashboard.php?section=Categories");
exit;
