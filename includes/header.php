<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<header>
    <div class="navbar">
        <div class="logo"><a href="index.php">📚 PlotPicks</a></div>
        <nav>
            <a href="/plotpicks_php/index.php">Home</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="logout.php">Logout (<?= htmlspecialchars($_SESSION['username']) ?>)</a>
                <?php if (!empty($_SESSION['is_admin'])): ?>
                    <a href="/plotpicks_php/admin/dashboard.php">Admin</a>
                <?php endif; ?>
            <?php else: ?>
                <a href="/plotpicks_php/login.php">Login</a>
                <a href="/plotpicks_php/register.php">Register</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<link rel="stylesheet" href="/css/style.css">