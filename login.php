<?php
session_start();
require_once 'includes/db.php';

$message = '';
if (isset($_SESSION['login_success'])) {
    $message = $_SESSION['login_success'];
    unset($_SESSION['login_success']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $message = "Please fill in all fields.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        // var_dump($user);


        if ($user && password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['is_admin'] = $user['is_admin'];
            $_SESSION['login_success'] = "Welcome, $username! You have logged in successfully.";

            // Redirect based on admin status
            if ($user['is_admin']) {
                header("Location: /plotpicks_php/admin/dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit();
        } else {
            $message = "Invalid username or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="/plotpicks_php/css/style.css">


</head>
<body>
<?php include 'includes/header.php'; ?>
<div class="form-container">
    <h2>Login</h2>
    <?php if ($message): ?>
        <p class="error"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    <form method="POST">
        <center>
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required><br>
            <button type="submit">Login</button>
        </center>
        <p>Don't have an account? <a href="register.php">Register here</a>.</p>
    </form>
</div>
<?php include 'includes/footer.php'; ?>
</body>
</html>
