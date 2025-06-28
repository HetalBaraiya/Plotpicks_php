<?php

session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Handle search and filter
$search = $_GET['search'] ?? '';
$genre = $_GET['genre'] ?? '';

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
    <title>Book Catalog</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<section class="yellow-banner">
    <div class="banner-content">
        <div class="text">
            <h1>📚 Summer Reading</h1>
            <p>Escape into a world of stories this season.<br>Browse top-rated books and discover your next great read.</p>
            <a href="#book-catalog" class="read-more-btn">Read More</a>
        </div>
        <div class="banner-image">
            <img src="images/logo/reading-book.png" alt="Reading Illustration">
        </div>
    </div>
</section>

<div class="suggested-books-wrapper">
  <h2 class="suggested-books-title">Suggested Books (Top Rated)</h2>
  <div class="book-grid suggested-books">
      <?php
      $topBooks = $conn->query("SELECT books.*, 
          (SELECT AVG(rating) FROM reviews WHERE reviews.book_id = books.id) AS avg_rating 
          FROM books 
          ORDER BY avg_rating DESC 
          LIMIT 5");
      while ($book = $topBooks->fetch_assoc()): ?>
          <div class="book-card small">
              <h4><?= htmlspecialchars($book['title']) ?></h4>
              <p><?= displayStars($book['avg_rating']) ?></p>
          </div>
      <?php endwhile; ?>
  </div>
</div>



<div class="container gallery-container">
    <h1>Book Catalog</h1>

    <form method="GET" class="search-filter">
        <input type="text" name="search" placeholder="Search books..." value="<?= htmlspecialchars($search) ?>">
        <select name="genre">
            <option value="">All Genres</option>
            <?php while ($row = $genreResult->fetch_assoc()): ?>
                <option value="<?= $row['id'] ?>" <?= $genre == $row['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($row['name']) ?>
                </option>
            <?php endwhile; ?>
        </select>
        <button type="submit">Search</button>
    </form>

    <div class="book-grid">
        <?php while ($book = $books->fetch_assoc()): ?>
            <div class="book-card">
                    <?php if ($book['cover_image']): ?>
    <img src="uploads/<?= htmlspecialchars($book['cover_image']) ?>" alt="<?= htmlspecialchars($book['title']) ?>" class="book-cover">
<?php else: ?>
    <img src="images/placeholder.png" alt="No Cover" class="book-cover">
<?php endif; ?>

                <div class="book-info">
                    <h2><?= htmlspecialchars($book['title']) ?></h2>
                    <p>By <?= htmlspecialchars($book['author']) ?></p>
                    <p>Genre: <?= htmlspecialchars($book['genre_name']) ?></p>
                    <p>Rating:
                        <?= displayStars($book['avg_rating']) ?>
                        (<?= $book['avg_rating'] ? round($book['avg_rating'], 1) : 'No ratings' ?>)
                    </p>
                    <a href="book.php?id=<?= $book['id'] ?>" class="details-btn">View Details</a>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
<!--
    <section class="py-6 border-bottom">
  <h2 class="section-title">LATEST RELEASES</h2>
  <div class="horizontal-scroll">
    <?php
    $latestBooks = $conn->query("SELECT * FROM books ORDER BY id DESC LIMIT 10");
    while ($book = $latestBooks->fetch_assoc()): ?>
      <div class="book-item">
        <img src="uploads/<?= htmlspecialchars($book['cover_image']) ?>" alt="<?= htmlspecialchars($book['title']) ?>" />
        <div class="book-title"><?= htmlspecialchars($book['title']) ?></div>
        <div class="book-author"><?= htmlspecialchars($book['author']) ?></div>
        <div class="book-badge badge-rave">NEW</div>
      </div>
    <?php endwhile; ?>
  </div>
</section>
-->

<!--
//best reviewd
<section class="py-6 border-top">
  <h2 class="section-title">BEST REVIEWED</h2>
  <div class="horizontal-scroll">
    <?php
    $topRated = $conn->query("SELECT books.*, (SELECT AVG(rating) FROM reviews WHERE book_id = books.id) AS avg_rating FROM books ORDER BY avg_rating DESC LIMIT 10");
    while ($book = $topRated->fetch_assoc()): ?>
      <div class="book-item">
        <img src="uploads/<?= htmlspecialchars($book['cover_image']) ?>" alt="<?= htmlspecialchars($book['title']) ?>" />
        <div class="book-title"><?= htmlspecialchars($book['title']) ?></div>
        <div class="book-author"><?= htmlspecialchars($book['author']) ?></div>
        <div class="book-badge badge-rave">
          <?= $book['avg_rating'] >= 4.5 ? 'RAVE' : ($book['avg_rating'] >= 3.5 ? 'POSITIVE' : 'MIXED') ?>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
</section>
-->

<!--
//style
<style>
  .section-title {
    text-align: center;
    font-weight: 800;
    font-size: 14px;
    margin-bottom: 1em;
    font-family: sans-serif;
    letter-spacing: 1px;
    text-transform: uppercase;
  }

  .horizontal-scroll {
    overflow-x: auto;
    padding: 0 1em;
    display: flex;
    gap: 20px;
    min-width: 100%;
    scrollbar-width: none; /* Firefox */
  }
  .horizontal-scroll::-webkit-scrollbar {
    display: none; /* Chrome, Safari */
  }

  .book-item {
    flex: 0 0 auto;
    width: 80px;
    text-align: center;
    font-family: serif;
  }

  .book-item img {
    width: 80px;
    height: 120px;
    object-fit: cover;
    margin-bottom: 6px;
    border-radius: 3px;
  }

  .book-title {
    font-size: 11px;
    line-height: 1.1;
  }

  .book-author {
    font-size: 8px;
    text-transform: uppercase;
    color: #888;
    margin-top: 2px;
    font-family: monospace;
    letter-spacing: 0.5px;
  }

  .book-badge {
    font-size: 9px;
    margin-top: 3px;
    font-weight: bold;
    text-transform: uppercase;
  }

  .badge-rave {
    color: #b91c1c;
  }
  .border-top {
    border-top: 1px solid #ddd;
  }
  .border-bottom {
    border-bottom: 1px solid #ddd;
  }
  .py-6 {
    padding: 2em 0;
  }
</style>
    -->
</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>