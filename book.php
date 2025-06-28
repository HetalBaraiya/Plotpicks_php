<?php
session_start();
require_once 'includes/db.php';

$book_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$book_id) {
    header("Location: index.php");
    exit();
}

// Fetch book details
$stmt = $conn->prepare("SELECT books.*, genres.name as genre_name FROM books JOIN genres ON books.genre_id = genres.id WHERE books.id = ?");
$stmt->bind_param("i", $book_id);
$stmt->execute();
$book = $stmt->get_result()->fetch_assoc();

if (!$book) {
    echo "<p>Book not found.</p>";
    exit();
}

// Handle review submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $rating = (int)$_POST['rating'];
    $review_text = trim($_POST['review_text']);
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO reviews (user_id, book_id, rating, review_text) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $user_id, $book_id, $rating, $review_text);
    $stmt->execute();
    header("Location: book.php?id=" . $book_id);
    exit();
}

// Fetch reviews
$stmt = $conn->prepare("SELECT r.*, u.username FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.book_id = ? ORDER BY r.created_at DESC");
$stmt->bind_param("i", $book_id);
$stmt->execute();
$reviews = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($book['title']) ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'includes/header.php'; ?>
<div class="book-container">
    <h2><?= htmlspecialchars($book['title']) ?></h2>
    <p><strong>Author:</strong> <?= htmlspecialchars($book['author']) ?></p>
    <p><strong>Genre:</strong> <?= htmlspecialchars($book['genre_name']) ?></p>
    <p><?= nl2br(htmlspecialchars($book['description'])) ?></p>

    <h3>Reviews</h3>
    <?php if ($reviews->num_rows > 0): ?>
        <?php while ($review = $reviews->fetch_assoc()): ?>
            <div class="review">
                <strong><?= htmlspecialchars($review['username']) ?>:</strong>
                <span class="stars">
                    <?= str_repeat('★', $review['rating']) . str_repeat('☆', 5 - $review['rating']) ?>
                </span>
                <p><?= nl2br(htmlspecialchars($review['review_text'])) ?></p>
                <small><?= $review['created_at'] ?></small>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No reviews yet.</p>
    <?php endif; ?>

    <?php if (isset($_SESSION['user_id'])): ?>
        <h4>Leave a Review</h4>
        <form method="POST" class="review-form">
            <label for="rating">Rating:</label>
            <select name="rating" required>
                <option value="">Choose...</option>
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <option value="<?= $i ?>"><?= $i ?> Star<?= $i > 1 ? 's' : '' ?></option>
                <?php endfor; ?>
            </select>
            <textarea name="review_text" rows="4" placeholder="Write your review..." required></textarea>
            <button type="submit">Submit Review</button>
        </form>
    <?php else: ?>
        <p><a href="login.php">Login</a> to leave a review.</p>
    <?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?>
</body>
</html>
