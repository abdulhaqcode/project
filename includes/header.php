<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Determine base path for correct linking
$basePath = '../'; // adjust if files are deeper, but we'll use relative path from the file location. Actually easier: define a global $basePath in each page before include. We'll just set a variable $basePath = '../'; in pages that are inside subfolders. For simplicity, we'll pass a variable or use relative paths. I'll keep it as is and just use `../` in header for assets and links. Since header is in includes/ folder, pages in root can include with `../includes/header.php` and all assets will be `../assets/...`, etc. So I'll set all links relative to the site root by making sure header links start with `/inventory/` if possible. Better: Use a constant. We'll define `ROOT` in a config later. For now, I'll just use absolute paths from root: `/inventory/...`. So all internal links in header will be absolute.

// Retrieve user data
$userName = $_SESSION['user_name'] ?? 'User';
$userRole = $_SESSION['user_role'] ?? 'staff';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/project/assets/css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="/project/index.php">Inventory</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="/project/index.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="/project/categories/index.php">Categories</a></li>
        <li class="nav-item"><a class="nav-link" href="/project/products/index.php">Products</a></li>
        <li class="nav-item"><a class="nav-link" href="/project/stock/index.php">Stock</a></li>
        <li class="nav-item"><a class="nav-link" href="/project/sales/index.php">Sales</a></li>
        <li class="nav-item"><a class="nav-link" href="/project/purchases/index.php">Purchases</a></li>
        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
        <li class="nav-item"><a class="nav-link" href="/project/users/index.php">Users</a></li>
        <?php endif; ?>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
            <?= htmlspecialchars($userName) ?> (<?= $userRole ?>)
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="/project/auth/logout.php">Logout</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>
<div class="container mt-4">