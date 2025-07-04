<?php
require_once '../db.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

if ($action === 'add') {
  $title = $_POST['title'];
  $author = $_POST['author'];
  $genre_id = $_POST['genre_id'];
  $description = $_POST['description'];
  $imageName = '';

 $existingImage = $_POST['existing_image'] ?? '';
$imageName = $existingImage;

if (!empty($_FILES['cover_image']['name'])) {
  $uploadedName = basename($_FILES['cover_image']['name']);
  $uploadPath = "../uploads/$uploadedName";

  if (move_uploaded_file($_FILES['cover_image']['tmp_name'], $uploadPath)) {
    $imageName = $uploadedName;
  }
}

$stmt = $pdo->prepare("UPDATE books SET title=?, author=?, genre_id=?, description=?, cover_image=? WHERE id=?");
$stmt->execute([$title, $author, $genre_id, $description, $imageName, $id]);


  $stmt = $pdo->prepare("INSERT INTO books (title, author, genre_id, description, cover_image) VALUES (?, ?, ?, ?, ?)");
  $stmt->execute([$title, $author, $genre_id, $description, $imageName]);

} elseif ($action === 'edit') {
  $id = $_POST['id'];
  $title = $_POST['title'];
  $author = $_POST['author'];
  $genre_id = $_POST['genre_id'];
  $description = $_POST['description'];

  if (!empty($_FILES['cover_image']['name'])) {
    $imageName = basename($_FILES['cover_image']['name']);
    move_uploaded_file($_FILES['cover_image']['tmp_name'], "../uploads/$imageName");
    $stmt = $pdo->prepare("UPDATE books SET title=?, author=?, genre_id=?, description=?, cover_image=? WHERE id=?");
    $stmt->execute([$title, $author, $genre_id, $description, $imageName, $id]);
  } else {
    $stmt = $pdo->prepare("UPDATE books SET title=?, author=?, genre_id=?, description=? WHERE id=?");
    $stmt->execute([$title, $author, $genre_id, $description, $id]);
  }

} elseif ($action === 'delete') {
  $id = $_GET['id'];
  $stmt = $pdo->prepare("DELETE FROM books WHERE id = ?");
  $stmt->execute([$id]);
}

header("Location: ../dashboard.php?section=Books");
exit;

