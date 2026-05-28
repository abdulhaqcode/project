<?php
require_once '../auth/session-check.php';
require_once '../config/database.php';
if (!isset($_GET['id'])) { header('Location: index.php'); exit; }
$stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
$stmt->execute([$_GET['id']]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$product) { echo "Product not found."; exit; }
include '../includes/header.php';
?>
<h1>Product Details</h1>
<div class="card">
    <div class="row g-0">
        <div class="col-md-4">
            <?php if ($product['image']): ?>
                <img src="/project/uploads/<?= $product['image'] ?>" class="img-fluid rounded-start" alt="">
            <?php endif; ?>
        </div>
        <div class="col-md-8">
            <div class="card-body">
                <h3 class="card-title"><?= htmlspecialchars($product['name']) ?></h3>
                <p class="card-text">SKU: <?= htmlspecialchars($product['sku']) ?> | Barcode: <?= htmlspecialchars($product['barcode']) ?></p>
                <p><strong>Category:</strong> <?= htmlspecialchars($product['category_name']) ?></p>
                <p><strong>Purchase Price:</strong> <?= number_format($product['purchase_price'], 2) ?></p>
                <p><strong>Selling Price:</strong> <?= number_format($product['selling_price'], 2) ?></p>
                <p><strong>Quantity:</strong> <?= $product['quantity'] ?> (Min: <?= $product['min_stock'] ?>)</p>
                <p><strong>Status:</strong> <?= $product['status'] ? 'Active' : 'Inactive' ?></p>
                <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($product['description'])) ?></p>
                <a href="edit.php?id=<?= $product['id'] ?>" class="btn btn-warning">Edit</a>
                <a href="index.php" class="btn btn-secondary">Back</a>
            </div>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>