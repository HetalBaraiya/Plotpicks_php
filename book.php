<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';

$bookId = $_GET['id'] ?? 0;
if (!$bookId) {
    header("Location: index.php");
    exit();
}

// Handle review submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $rating = (int)$_POST['rating'];
    $review_text = trim($_POST['review_text']);
    $user_id = $_SESSION['user_id'];

    if ($rating >= 1 && $rating <= 5 && strlen($review_text) >= 10) {
        $stmt = $conn->prepare("INSERT INTO reviews (user_id, book_id, rating, review_text) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $user_id, $bookId, $rating, $review_text);
        $stmt->execute();
        header("Location: book.php?id=" . $bookId);
        exit();
    }
}

// Fetch book details
$stmt = $conn->prepare("SELECT books.*, genres.name AS genre_name FROM books 
                        LEFT JOIN genres ON books.genre_id = genres.id 
                        WHERE books.id = ?");
$stmt->bind_param("i", $bookId);
$stmt->execute();
$book = $stmt->get_result()->fetch_assoc();

if (!$book) {
    echo "<p>Book not found.</p>";
    exit();
}

// Fetch reviews
$reviews = $conn->query("SELECT r.*, u.username FROM reviews r 
                         JOIN users u ON r.user_id = u.id 
                         WHERE r.book_id = $bookId 
                         ORDER BY r.created_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($book['title']) ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<div class="container book-detail-container">
  <div class="book-detail-layout">
    <div class="book-detail-left">
      <img src="uploads/<?= htmlspecialchars($book['cover_image']) ?>" alt="<?= htmlspecialchars($book['title']) ?>" class="book-detail-cover">
      <div class="book-meta">
        <span class="genre-tag"><?= htmlspecialchars($book['genre_name']) ?></span>
        <p class="pages">Pages: <?= $book['pages'] ?? 'N/A' ?></p>
        <p class="avg-rating"><?= displayStars(avgRating($bookId)) ?></p>
      </div>
    </div>

    <div class="book-detail-right">
      <h1><?= htmlspecialchars($book['title']) ?></h1>
      <p class="book-author">by <?= htmlspecialchars($book['author']) ?></p>

      <div class="book-description-box">
        <strong>Description</strong>
        <p><?= nl2br(htmlspecialchars($book['description'])) ?></p>
      </div>

      <div class="review-section">
        <h3>🗨 Reviews (<?= $reviews->num_rows ?>)</h3>

        <?php if (isset($_SESSION['user_id'])): ?>
          <form method="POST" action="book.php?id=<?= $bookId ?>" class="review-form">
            <input type="hidden" name="book_id" value="<?= $bookId ?>">

            <label>Your Rating</label>
            <div class="rating-stars">
  <?php for ($i = 5; $i >= 1; $i--): ?>
    <input type="radio" name="rating" value="<?= $i ?>" id="star<?= $i ?>">
    <label for="star<?= $i ?>">★</label>
  <?php endfor; ?>
</div>


            <label>Your Review</label>
            <textarea name="review_text" required minlength="10" maxlength="1000" placeholder="What did you think of the book?"></textarea>

            <button type="submit" class="submit-btn">Submit Review</button>
          </form>
        <?php else: ?>
          <p><a href="login.php">Login</a> to write a review.</p>
        <?php endif; ?>

        <div class="existing-reviews">
          <?php if ($reviews->num_rows > 0): ?>
            <?php while ($review = $reviews->fetch_assoc()): ?>
              <div class="review-item">
                <p><strong><?= htmlspecialchars($review['username']) ?></strong> <?= displayStars($review['rating']) ?></p>
                <p><?= nl2br(htmlspecialchars($review['review_text'])) ?></p>
                <small><?= date("F j, Y", strtotime($review['created_at'])) ?></small>
              </div>
            <?php endwhile; ?>
          <?php else: ?>
            <p>No reviews yet. Be the first to write one!</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<<script>
  window.addEventListener('DOMContentLoaded', () => {
    const toggleBtn = document.getElementById('theme-toggle');
    const icon = document.getElementById('icon');
    const savedTheme = localStorage.getItem('theme');

    if (savedTheme === 'dark') {
      document.body.classList.add('dark');
      icon.textContent = '☀️';
    }

    if (toggleBtn) {
      toggleBtn.addEventListener('click', () => {
        document.body.classList.toggle('dark');
        const isDark = document.body.classList.contains('dark');
        icon.textContent = isDark ? '☀️' : '🌙';
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
      });
    }
  });
</script>



<?php include 'includes/footer.php'; ?>
</body>
</html>
