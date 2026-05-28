<?php
require_once '../auth/session-check.php';
require_once '../config/database.php';
$products = $pdo->query("SELECT id, name, quantity FROM products WHERE status=1 ORDER BY name")->fetchAll();
include '../includes/header.php';
?>
<h1>Stock IN</h1>
<form method="post" action="store.php">
    <input type="hidden" name="movement_type" value="IN">
    <div class="mb-3">
        <label>Product</label>
        <select name="product_id" class="form-select" required>
            <option value="">Select Product</option>
            <?php foreach ($products as $prod): ?>
                <option value="<?= $prod['id'] ?>"><?= htmlspecialchars($prod['name']) ?> (Current: <?= $prod['quantity'] ?>)</option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="mb-3">
        <label>Quantity to Add</label>
        <input type="number" name="quantity" class="form-control" min="1" required>
    </div>
    <div class="mb-3">
        <label>Note</label>
        <input type="text" name="note" class="form-control" placeholder="e.g., Purchase from supplier">
    </div>
    <button type="submit" class="btn btn-primary">Add Stock</button>
    <a href="index.php" class="btn btn-secondary">Cancel</a>
</form>
<?php include '../includes/footer.php'; ?>