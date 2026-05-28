<?php
require_once '../auth/session-check.php';
require_once '../config/database.php';
$products = $pdo->query("SELECT id, name, quantity FROM products WHERE status=1 ORDER BY name")->fetchAll();
include '../includes/header.php';
?>
<h1>Stock OUT</h1>
<form method="post" action="store.php">
    <input type="hidden" name="movement_type" value="OUT">
    <div class="mb-3">
        <label>Product</label>
        <select name="product_id" class="form-select" required>
            <option value="">Select Product</option>
            <?php foreach ($products as $prod): ?>
                <option value="<?= $prod['id'] ?>"><?= htmlspecialchars($prod['name']) ?> (In stock: <?= $prod['quantity'] ?>)</option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="mb-3">
        <label>Quantity to Remove</label>
        <input type="number" name="quantity" class="form-control" min="1" required>
    </div>
    <div class="mb-3">
        <label>Note</label>
        <input type="text" name="note" class="form-control" placeholder="e.g., Sold to customer">
    </div>
    <button type="submit" class="btn btn-danger">Remove Stock</button>
    <a href="index.php" class="btn btn-secondary">Cancel</a>
</form>
<?php include '../includes/footer.php'; ?>