<?php
require_once '../auth/session-check.php';
require_once '../config/database.php';

$search = $_GET['search'] ?? '';
$category_id = $_GET['category_id'] ?? '';

$query = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE 1=1";
$params = [];

if ($search !== '') {
    $query .= " AND (p.name LIKE ? OR p.sku LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($category_id !== '') {
    $query .= " AND p.category_id = ?";
    $params[] = $category_id;
}
$query .= " ORDER BY p.id DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$categories = $pdo->query("SELECT id, name FROM categories WHERE status=1")->fetchAll();

include '../includes/header.php';
?>

<h1>Products</h1>
<form method="get" class="row g-3 mb-3">
    <div class="col-md-6">
        <input type="text" name="search" class="form-control" placeholder="Search name or SKU" value="<?= htmlspecialchars($search) ?>">
    </div>
    <div class="col-md-4">
        <select name="category_id" class="form-select">
            <option value="">All Categories</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= $category_id == $cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2">
        <button type="submit" class="btn btn-primary w-100">Filter</button>
    </div>
</form>
<a href="create.php" class="btn btn-success mb-3">Add New Product</a>

<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>ID</th><th>Image</th><th>Name</th><th>SKU</th><th>Category</th>
            <th>Price (Sell)</th><th>Qty</th><th>Min Stock</th><th>Status</th><th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($products as $prod): ?>
        <tr>
            <td><?= $prod['id'] ?></td>
            <td>
                <?php if ($prod['image']): ?>
                    <img src="/project/uploads/<?= $prod['image'] ?>" height="40" alt="">
                <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($prod['name']) ?></td>
            <td><?= htmlspecialchars($prod['sku']) ?></td>
            <td><?= htmlspecialchars($prod['category_name']) ?></td>
            <td><?= number_format($prod['selling_price'], 2) ?></td>
            <td><?= $prod['quantity'] ?></td>
            <td><?= $prod['min_stock'] ?></td>
            <td><?= $prod['status'] ? 'Active' : 'Inactive' ?></td>
            <td>
                <a href="view.php?id=<?= $prod['id'] ?>" class="btn btn-sm btn-info">View</a>
                <a href="edit.php?id=<?= $prod['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                <a href="delete.php?id=<?= $prod['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this product?')">Delete</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php include '../includes/footer.php'; ?>