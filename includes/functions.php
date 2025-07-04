<?php
function displayStars($rating) {
    $full = floor($rating);
    $html = '';
    for ($i = 0; $i < 5; $i++) {
        $html .= $i < $full ? '★' : '☆';
    }
    return $html;
}
// Escape output for HTML safety
function esc($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check if user is admin
function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
}

// Redirect helper
function redirect($url) {
    header("Location: $url");
    exit();
}

function avgRating($bookId) {
    global $conn;
    $stmt = $conn->prepare("SELECT AVG(rating) AS avg FROM reviews WHERE book_id = ?");
    $stmt->bind_param("i", $bookId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return round($result['avg'], 1);
}

function getUsername($userId) {
    global $conn;
    $stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result['username'] ?? 'Anonymous';
}




