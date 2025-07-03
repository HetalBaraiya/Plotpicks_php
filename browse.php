<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';

$search = $_GET['search'] ?? '';
$genre = $_GET['genre'] ?? '';
$isSearching = !empty($search) || !empty($genre);

// Fetch genres
$genreResult = $conn->query("SELECT * FROM genres");

// Fetch books
$query = "SELECT books.*, genres.name AS genre_name,
          (SELECT AVG(rating) FROM reviews WHERE reviews.book_id = books.id) AS avg_rating
          FROM books
          LEFT JOIN genres ON books.genre_id = genres.id
          WHERE books.title LIKE ?";

$params = ["%$search%"];
if (!empty($genre)) {
    $query .= " AND genres.id = ?";
    $params[] = $genre;
}

$stmt = $conn->prepare($query);
if (count($params) == 2) {
    $stmt->bind_param("si", $params[0], $params[1]);
} else {
    $stmt->bind_param("s", $params[0]);
}
$stmt->execute();
$books = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Browse Books</title>
  <link rel="stylesheet" href="css/style.css">
  <style>
    .book-grid.compact {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 24px;
      margin-top: 2rem;
    }

    .book-card.small {
      padding: 12px;
    }
  </style>
</head>
<body>

<?php include 'includes/header.php'; ?>



<div class="container">
  <form method="GET" class="search-filter">
    <input type="text" name="search" placeholder="Search for books..." value="<?= htmlspecialchars($search) ?>">
    <select name="genre">
      <option value="">All Genres</option>
      <?php while ($g = $genreResult->fetch_assoc()): ?>
        <option value="<?= $g['id'] ?>" <?= $genre == $g['id'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($g['name']) ?>
        </option>
      <?php endwhile; ?>
    </select>
    <button type="submit">Search</button>
  </form>
</div>

<section class="book-section">
  <h2 class="section-title">Browse Results</h2>
  <div class="book-grid compact" style="max-width: 1200px; margin: auto; padding: 0 20px;">
    <?php if ($books->num_rows > 0): ?>
      <?php while ($book = $books->fetch_assoc()):
        $cover = !empty($book['cover_image']) && file_exists("uploads/" . $book['cover_image'])
            ? "uploads/" . $book['cover_image']
            : "images/placeholder.png";
      ?>
      <div class="book-card small">
        <img src="<?= $cover ?>" alt="<?= htmlspecialchars($book['title']) ?>" class="book-cover-small">
        <div class="book-info">
          <h4><?= htmlspecialchars($book['title']) ?></h4>
          <p><strong>Author:</strong> <?= htmlspecialchars($book['author']) ?></p>
          <p><strong>Genre:</strong> <?= htmlspecialchars($book['genre_name']) ?></p>
          <p><?= displayStars($book['avg_rating']) ?>
            (<?= $book['avg_rating'] ? round($book['avg_rating'], 1) : 'No ratings' ?>)
          </p>
          <a href="book.php?id=<?= $book['id'] ?>" class="scroll-btn">View</a>
        </div>
      </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p style="padding: 1rem;">No books found.</p>
    <?php endif; ?>
  </div>
</section>

<script src="https://unpkg.com/lucide@latest"></script>
<script>
  document.getElementById('theme-toggle').addEventListener('click', () => {
    document.body.classList.toggle('dark');
    const icon = document.getElementById('icon');
    icon.textContent = document.body.classList.contains('dark') ? '☀️' : '🌙';
    localStorage.setItem('theme', document.body.classList.contains('dark') ? 'dark' : 'light');
  });

  window.addEventListener('DOMContentLoaded', () => {
    if (localStorage.getItem('theme') === 'dark') {
      document.body.classList.add('dark');
      document.getElementById('icon').textContent = '☀️';
    }
    lucide.createIcons();
  });
</script>

<?php include 'includes/footer.php'; ?>
</body>
</html>
