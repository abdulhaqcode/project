<?php
require_once '../auth/session-check.php';
require_once '../config/database.php';
if (!isset($_GET['id'])) { header('Location: index.php'); exit; }
$stmt = $pdo->prepare("SELECT * FROM products WHERE id=?");
$stmt->execute([$_GET['id']]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$product) { echo "Product not found."; exit; }
$categories = $pdo->query("SELECT id, name FROM categories WHERE status=1")->fetchAll();
include '../includes/header.php';
?>
<h1>Edit Product</h1>
<form method="post" action="update.php" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= $product['id'] ?>">
    <div class="row">
        <div class="col-md-6 mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" required>
        </div>
        <div class="col-md-3 mb-3">
            <label>SKU</label>
            <input type="text" name="sku" class="form-control" value="<?= htmlspecialchars($product['sku']) ?>">
        </div>
        <div class="col-md-3 mb-3">
            <label>Barcode</label>
            <input type="text" name="barcode" class="form-control" value="<?= htmlspecialchars($product['barcode']) ?>">
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 mb-3">
            <label>Category</label>
            <select name="category_id" class="form-select">
                <option value="">Select Category</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= $product['category_id'] == $cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4 mb-3">
            <label>Purchase Price</label>
            <input type="number" step="0.01" name="purchase_price" class="form-control" value="<?= $product['purchase_price'] ?>" required>
        </div>
        <div class="col-md-4 mb-3">
            <label>Selling Price</label>
            <input type="number" step="0.01" name="selling_price" class="form-control" value="<?= $product['selling_price'] ?>" required>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3 mb-3">
            <label>Quantity</label>
            <input type="number" name="quantity" class="form-control" value="<?= $product['quantity'] ?>" required>
        </div>
        <div class="col-md-3 mb-3">
            <label>Min Stock</label>
            <input type="number" name="min_stock" class="form-control" value="<?= $product['min_stock'] ?>">
        </div>
        <div class="col-md-3 mb-3">
            <label>Status</label>
            <select name="status" class="form-select">
                <option value="1" <?= $product['status'] ? 'selected' : '' ?>>Active</option>
                <option value="0" <?= !$product['status'] ? 'selected' : '' ?>>Inactive</option>
            </select>
        </div>
        <div class="col-md-3 mb-3">
            <label>Product Image</label>
            <input type="file" name="image" class="form-control" accept="image/*">
            <?php if ($product['image']): ?>
                <img src="/project/uploads/<?= $product['image'] ?>" height="50" class="mt-2">
            <?php endif; ?>
        </div>
    </div>
    <div class="mb-3">
        <label>Description</label>
        <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($product['description']) ?></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Update Product</button>
    <a href="index.php" class="btn btn-secondary">Cancel</a>
</form>
<?php include '../includes/footer.php'; ?>