<?php
require_once 'auth/session-check.php';
require_once 'config/database.php';

// Quick stats
$productCount = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$categoryCount = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
$lowStockCount = $pdo->query("SELECT COUNT(*) FROM products WHERE quantity <= min_stock AND status=1")->fetchColumn();
$todayMovements = $pdo->query("SELECT COUNT(*) FROM stock_movements WHERE DATE(created_at) = CURDATE()")->fetchColumn();

include 'includes/header.php';
?>
<h1>Dashboard</h1>
<div class="row g-4 mt-2">
    <div class="col-md-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h5 class="card-title">Total Products</h5>
                <p class="card-text fs-3"><?= $productCount ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h5 class="card-title">Categories</h5>
                <p class="card-text fs-3"><?= $categoryCount ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <h5 class="card-title">Low Stock</h5>
                <p class="card-text fs-3"><?= $lowStockCount ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-info">
            <div class="card-body">
                <h5 class="card-title">Today's Movements</h5>
                <p class="card-text fs-3"><?= $todayMovements ?></p>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>