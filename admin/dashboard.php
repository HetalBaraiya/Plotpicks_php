<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: ../index.php");
    exit();
}

// Handle book deletion
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM books WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: dashboard.php");
    exit();
}

// Handle book update
if (isset($_POST['edit_book'])) {
    $book_id = $_POST['book_id'];
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $description = trim($_POST['description']);
    $genre_id = $_POST['genre_id'];
    $cover_image = '';

    if (!empty($_FILES['cover_image']['name'])) {
        $target_dir = "../uploads/";
        $cover_image = basename($_FILES["cover_image"]["name"]);
        $target_file = $target_dir . $cover_image;
        move_uploaded_file($_FILES["cover_image"]["tmp_name"], $target_file);
    }

    if ($cover_image) {
        $stmt = $conn->prepare("UPDATE books SET title=?, author=?, description=?, genre_id=?, cover_image=? WHERE id=?");
        $stmt->bind_param("sssisi", $title, $author, $description, $genre_id, $cover_image, $book_id);
    } else {
        $stmt = $conn->prepare("UPDATE books SET title=?, author=?, description=?, genre_id=? WHERE id=?");
        $stmt->bind_param("sssii", $title, $author, $description, $genre_id, $book_id);
    }
    $stmt->execute();
    header("Location: dashboard.php");
    exit();
}

// Handle new book addition
if (isset($_POST['add_book'])) {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $description = trim($_POST['description']);
    $genre_id = $_POST['genre_id'];
    $cover_image = '';

    if (!empty($_FILES['cover_image']['name'])) {
        $target_dir = "../uploads/";
        $cover_image = basename($_FILES["cover_image"]["name"]);
        $target_file = $target_dir . $cover_image;
        move_uploaded_file($_FILES["cover_image"]["tmp_name"], $target_file);
    }

    if ($title && $author && $description && $genre_id && $cover_image) {
        $stmt = $conn->prepare("INSERT INTO books (title, author, description, genre_id, cover_image) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssis", $title, $author, $description, $genre_id, $cover_image);
        $stmt->execute();
    }
    header("Location: dashboard.php");
    exit();
}

// Fetch books and genres
$books = $conn->query("SELECT books.*, genres.name AS genre_name FROM books JOIN genres ON books.genre_id = genres.id ORDER BY books.id DESC");
$genres = $conn->query("SELECT * FROM genres");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include '../includes/header.php'; ?>
<div class="admin-container">
    <h2>Admin Dashboard</h2>

    <section>
        <h3>Add New Book</h3>
        <form method="POST" enctype="multipart/form-data" class="admin-form">
            <input type="hidden" name="add_book" value="1">
            <input type="text" name="title" placeholder="Book Title" required>
            <input type="text" name="author" placeholder="Author" required>
            <textarea name="description" placeholder="Description" required></textarea>
            <select name="genre_id" required>
                <option value="">Select Genre</option>
                <?php $genres->data_seek(0); while ($genre = $genres->fetch_assoc()): ?>
                    <option value="<?= $genre['id'] ?>"><?= htmlspecialchars($genre['name']) ?></option>
                <?php endwhile; ?>
            </select>
            <input type="file" name="cover_image" accept="image/*" required>
            <button type="submit">Add Book</button>
        </form>
    </section>

    <section>
        <h3>All Books</h3>
        <table>
            <thead>
                <tr>
                    <th>Cover</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Genre</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($book = $books->fetch_assoc()): ?>
                    <tr>
                        <td><img src="../uploads/<?= htmlspecialchars($book['cover_image']) ?>" alt="" width="60"></td>
                        <td><?= htmlspecialchars($book['title']) ?></td>
                        <td><?= htmlspecialchars($book['author']) ?></td>
                        <td><?= htmlspecialchars($book['genre_name']) ?></td>
                        <td><?= htmlspecialchars(substr($book['description'], 0, 100)) ?>...</td>
                        <td>
                            <form method="POST" enctype="multipart/form-data" class="edit-form">
                                <input type="hidden" name="edit_book" value="1">
                                <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                                <input type="text" name="title" value="<?= htmlspecialchars($book['title']) ?>" required>
                                <input type="text" name="author" value="<?= htmlspecialchars($book['author']) ?>" required>
                                <textarea name="description" required><?= htmlspecialchars($book['description']) ?></textarea>
                                <select name="genre_id" required>
                                    <?php $genres->data_seek(0); while ($genre = $genres->fetch_assoc()): ?>
                                        <option value="<?= $genre['id'] ?>" <?= $genre['id'] == $book['genre_id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($genre['name']) ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                                <input type="file" name="cover_image" accept="image/*">
                                <button type="submit">Save</button>
                                <a href="dashboard.php?delete=<?= $book['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>
</div>
<?php include '../includes/footer.php'; ?>
</body>
</html>
