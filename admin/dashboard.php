<?php

session_start();

$section = $_GET['section'] ?? 'Books';
$action = $_GET['action'] ?? '';
?>






<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="../css/admin.css">
    
  <style>
    .admin-layout {
      display: flex;
      min-height: 100vh;
      max-width: 100%;
    }

    #admin-sidebar {
      width: 200px;
      background: #f1f1f1;
      padding: 1rem;
    }

    #admin-sidebar ul {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .admin-nav-item {
      padding: 10px;
      margin: 8px 0;
      cursor: pointer;
      background: #ddd;
      border-radius: 4px;
      text-align: center;
      transition: background 0.3s;
    }

    .admin-nav-item a {
      text-decoration: none;
      color: #333;
      display: block;
    }

    .admin-nav-item:hover {
      background: #ccc;
    }

    .admin-nav-item.active {
      background: #0073e6;
    }

    .admin-nav-item.active a {
      color: white;
      font-weight: bold;
    }

    main {
      flex: 1;
      padding: 1.5rem;
    }

    .top-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1.5rem;
    }

    .button {
      background: #0073e6;
      color: white;
      padding: 10px 16px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    .button:hover {
      background: #005bb5;
    }
  </style>
</head>
<body>
<?php include '../includes/header.php'; ?>
<div class="admin-layout">
  <aside id="admin-sidebar">
    <ul>
      <li class="admin-nav-item <?= $section === 'Books' ? 'active' : '' ?>">
        <a href="?section=Books">📚 Books</a>
      </li>
      <li class="admin-nav-item <?= $section === 'Categories' ? 'active' : '' ?>">
        <a href="?section=Categories">📁 Categories</a>
      </li>
      <li class="admin-nav-item <?= $section === 'Users' ? 'active' : '' ?>">
        <a href="?section=Users">👤 Users</a>
      </li>
    </ul>
  </aside>

  <main>
    <div class="top-bar">
      <h2 id="section-title"><?= htmlspecialchars($section) ?></h2>
      <a href="?section=<?= urlencode($section) ?>&action=add">
        <button class="button">+ Add</button>
      </a>
    </div>

    <div id="admin-content">
      <?php
      if ($action === 'edit' && $section === 'Books') {
  include "forms/form_edit_Books.php";
}

        // Display appropriate form or list based on action
        if ($action === 'add') {
          $formPath = __DIR__ . "/forms/form_add_{$section}.php";
          if (file_exists($formPath)) {
            include $formPath;
          } else {
            echo "<p>Form for $section not found.</p>";
          }
        } else {
          $listPath = __DIR__ . "/lists/list_{$section}.php";
          if (file_exists($listPath)) {
            include $listPath;
          } else {
            echo "<p>List for $section not found.</p>";
          }
        }
      ?>
    </div>
  </main>
</div>
<?php include '../includes/footer.php'; ?>

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
</script>


</body>
</html>
