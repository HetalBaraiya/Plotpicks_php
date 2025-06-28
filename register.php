<?php
session_start();
require_once 'includes/db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];

    // Basic validation
    if (empty($username) || empty($email) || empty($password) || empty($confirm)) {
        $message = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
    } elseif (strlen($password) < 8) {
        $message = "Password must be at least 8 characters.";
    } elseif ($password !== $confirm) {
        $message = "Passwords do not match.";
    } else {
        // Check for duplicate username
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $message = "Username already exists.";
        } else {
            // Check for duplicate email
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $message = "Email already exists.";
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $username, $email, $hashedPassword);

                if ($stmt->execute()) {
                    $_SESSION['login_success'] = "Registered successfully. Please log in.";
                    header("Location: login.php");
                    exit();
                } else {
                    $message = "Registration failed. Try again.";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="css/style.css">

</head>
<body>
<?php include 'includes/header.php'; ?>
<div class="form-container">
    <h2>Register</h2>
    <?php if ($message): ?>
        <div class="alert"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <form method="POST">
        <center>
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email" required pattern="[^@]+@[^\.]+\..+">
            <input type="password" name="password" placeholder="Password (min 8 characters)" required minlength="8">
            <input type="password" name="confirm" placeholder="Confirm Password" required minlength="8"><br>
            <button type="submit">Register</button>
        </center>
        <p>Already have an account? <a href="login.php">Login here</a>.</p>
    </form>
</div>
<?php include 'includes/footer.php'; ?>
</body>
</html>
