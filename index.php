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
    <title>Book Catalog</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<section class="yellow-banner">
    <div class="banner-content">
        <div class="text">
            <h1>Summer Reading</h1>
            <p>Escape into a world of stories this season.<br>Browse top-rated books and discover your next great read.</p>
            <a href="browse.php" class="read-more-btn">Browse More</a>
        </div>
        <div class="banner-image">
            <img src="images/logo/reading-book.png" alt="Reading Illustration">
        </div>
    </div>
</section>

<div class="container">
    <form method="GET" class="search-filter">
        <input type="text" name="search" placeholder="Search for books..." value="<?= htmlspecialchars($search) ?>">
        <select name="genre">
            <option value="">All Genres</option>
            <?php while ($g = $genreResult->fetch_assoc()): ?>
                <option value="<?= $g['id'] ?>" <?= $genre == $g['id'] ? 'selected' : '' ?>><?= htmlspecialchars($g['name']) ?></option>
            <?php endwhile; ?>
        </select>
        <button type="submit">Search</button>
    </form>
</div>

<?php if (!$isSearching): ?>
<div class="suggested-books-wrapper">
    <h2 class="suggested-books-title">Suggested Books (Top Rated)</h2>
    <div class="suggested-books-grid">
        <?php
        $topBooks = $conn->query("SELECT books.*, genres.name AS genre_name,
            (SELECT AVG(rating) FROM reviews WHERE reviews.book_id = books.id) AS avg_rating 
            FROM books 
            LEFT JOIN genres ON books.genre_id = genres.id
            ORDER BY avg_rating DESC 
            LIMIT 5");
        while ($book = $topBooks->fetch_assoc()):
            $cover = !empty($book['cover_image']) && file_exists("uploads/" . $book['cover_image'])
                ? "uploads/" . $book['cover_image']
                : "images/placeholder.png";
        ?>
        <div class="suggested-card">
            <img src="<?= $cover ?>" alt="<?= htmlspecialchars($book['title']) ?>" class="suggested-cover">
            <div class="suggested-info">
                <div class="scroll-book-title">
                    <strong><?= htmlspecialchars($book['title']) ?></strong><br>
                    <small>By <?= htmlspecialchars($book['author']) ?></small><br>
                    <small>Genre: <?= htmlspecialchars($book['genre_name'] ?? 'N/A') ?></small>
                </div>
                <p class="rating"><?= displayStars($book['avg_rating']) ?> (<?= $book['avg_rating'] ? round($book['avg_rating'], 1) : 'No ratings' ?>)</p>
                <a href="book.php?id=<?= $book['id'] ?>" class="view-btn">View</a>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>
<?php endif; ?>

<?php if (!$isSearching): ?>
<section class="book-section">
    <h2 class="section-title">Trending Books</h2>
    <div class="horizontal-scroll">
        <?php
        $trendingBooks = $conn->query("SELECT books.*, genres.name AS genre_name FROM books LEFT JOIN genres ON books.genre_id = genres.id ORDER BY books.id DESC LIMIT 10");
        while ($book = $trendingBooks->fetch_assoc()):
            $cover = !empty($book['cover_image']) && file_exists("uploads/" . $book['cover_image'])
                ? "uploads/" . $book['cover_image']
                : "images/placeholder.png";
        ?>
        <div class="scroll-book-card">
            <img src="<?= $cover ?>" alt="<?= htmlspecialchars($book['title']) ?>">
            <div class="scroll-book-title">
                <strong><?= htmlspecialchars($book['title']) ?></strong><br>
                <small>By <?= htmlspecialchars($book['author']) ?></small><br>
                <small>Genre: <?= htmlspecialchars($book['genre_name'] ?? 'N/A') ?></small>
            </div>
            <a href="book.php?id=<?= $book['id'] ?>" class="scroll-btn">Read</a>
        </div>
        <?php endwhile; ?>
    </div>
</section>
<?php endif; ?>

<?php if (!$isSearching): ?>
<section class="book-section">
    <h2 class="section-title">Classic Books</h2>
    <div class="horizontal-scroll">
        <?php
        $classicGenre = $conn->query("SELECT id FROM genres WHERE name = 'Classics'")->fetch_assoc();
        $classicGenreId = $classicGenre['id'] ?? 0;

        $classicBooks = $conn->query("SELECT books.*, genres.name AS genre_name 
                                      FROM books 
                                      LEFT JOIN genres ON books.genre_id = genres.id 
                                      WHERE books.genre_id = $classicGenreId 
                                      LIMIT 10");
        while ($book = $classicBooks->fetch_assoc()):
            $cover = !empty($book['cover_image']) && file_exists("uploads/" . $book['cover_image'])
                ? "uploads/" . $book['cover_image']
                : "images/placeholder.png";
        ?>
        <div class="scroll-book-card">
            <img src="<?= $cover ?>" alt="<?= htmlspecialchars($book['title']) ?>">
            <div class="scroll-book-title">
                <strong><?= htmlspecialchars($book['title']) ?></strong><br>
                <small>By <?= htmlspecialchars($book['author']) ?></small><br>
                <small>Genre: <?= htmlspecialchars($book['genre_name'] ?? 'N/A') ?></small>
            </div>
            <a href="book.php?id=<?= $book['id'] ?>" class="scroll-btn">Read</a>
        </div>
        <?php endwhile; ?>
    </div>
</section>
<?php endif; ?>

<?php if ($isSearching): ?>
<section class="book-section">
    <h2 class="section-title">Search Results</h2>
    <div class="book-grid compact">
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
                    <p><?= displayStars($book['avg_rating']) ?> (<?= $book['avg_rating'] ? round($book['avg_rating'], 1) : 'No ratings' ?>)</p>
                    <a href="book.php?id=<?= $book['id'] ?>" class="scroll-btn">View</a>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="padding: 1rem;">No books found for your search.</p>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

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
});

// Lucide Icons load
lucide.createIcons();
</script>
<script src="https://unpkg.com/lucide@latest"></script>

<?php include 'includes/footer.php'; ?>
</body>
</html>
