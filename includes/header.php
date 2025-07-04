<?php
 

  $basePath = (basename(dirname($_SERVER['SCRIPT_NAME'])) === 'admin') ? '../' : '';
?>
<header class="main-header">
  <div class="header-container">
    <a href="<?= $basePath ?>index.php" class="logo">📚 PlotPicks</a>
    
    <nav>
      <a href="<?= $basePath ?>index.php" class="nav-link">Home</a>
      <a href="<?= $basePath ?>browse.php" class="nav-link">Browse</a>
      <a href="<?= $basePath ?>categories.php" class="nav-link">Categories</a>
      <a href="<?= $basePath ?>suggest.php" class="nav-link">Suggest</a>

      <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
        <a href="<?= $basePath ?>admin/dashboard.php" class="nav-link">Admin Panel</a>
      <?php endif; ?>
    </nav>

    <div class="user-controls">
      <?php if (isset($_SESSION['username'])): ?>
        <span class="welcome-msg">
          Welcome <?= $_SESSION['is_admin'] ? 'Admin' : htmlspecialchars($_SESSION['username']) ?>
        </span>
        <a href="<?= $basePath ?>logout.php" class="nav-link">Logout</a>
      <?php else: ?>
        <a href="<?= $basePath ?>login.php" class="nav-link">Login</a>
      <?php endif; ?>

      <button id="theme-toggle" aria-label="Toggle Theme">
        <span id="icon">🌙</span>
      </button>
    </div>
  </div>
</header>
