<?php
require_once '../auth/session-check.php';
require_once '../config/database.php';
$categories = $pdo->query("SELECT id, name FROM categories WHERE status=1")->fetchAll();
include '../includes/header.php';
?>
<h1>Add Product</h1>
<form method="post" action="store.php" enctype="multipart/form-data">
    <div class="row">
        <div class="col-md-6 mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="col-md-3 mb-3">
            <label>SKU</label>
            <input type="text" name="sku" class="form-control">
        </div>
        <div class="col-md-3 mb-3">
            <label>Barcode</label>
            <input type="text" name="barcode" class="form-control">
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 mb-3">
            <label>Category</label>
            <select name="category_id" class="form-select">
                <option value="">Select Category</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4 mb-3">
            <label>Purchase Price</label>
            <input type="number" step="0.01" name="purchase_price" class="form-control" required>
        </div>
        <div class="col-md-4 mb-3">
            <label>Selling Price</label>
            <input type="number" step="0.01" name="selling_price" class="form-control" required>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3 mb-3">
            <label>Quantity</label>
            <input type="number" name="quantity" class="form-control" value="0" required>
        </div>
        <div class="col-md-3 mb-3">
            <label>Min Stock</label>
            <input type="number" name="min_stock" class="form-control" value="5">
        </div>
        <div class="col-md-3 mb-3">
            <label>Status</label>
            <select name="status" class="form-select">
                <option value="1">Active</option>
                <option value="0">Inactive</option>
            </select>
        </div>
        <div class="col-md-3 mb-3">
            <label>Product Image</label>
            <input type="file" name="image" class="form-control" accept="image/*">
        </div>
    </div>
    <div class="mb-3">
        <label>Description</label>
        <textarea name="description" class="form-control" rows="3"></textarea>
    </div>
    <button type="submit" class="btn btn-success">Save Product</button>
    <a href="index.php" class="btn btn-secondary">Cancel</a>
</form>
<?php include '../includes/footer.php'; ?>