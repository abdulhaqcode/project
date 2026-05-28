<?php
require_once '../auth/session-check.php';
require_once '../config/database.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}
$stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->execute([$_GET['id']]);
$category = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$category) {
    echo "Category not found.";
    exit;
}
include '../includes/header.php';
?>
<h1>Edit Category</h1>
<form method="post" action="update.php">
    <input type="hidden" name="id" value="<?= $category['id'] ?>">
    <div class="mb-3">
        <label>Name</label>
        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($category['name']) ?>" required>
    </div>
    <div class="mb-3">
        <label>Description</label>
        <textarea name="description" class="form-control"><?= htmlspecialchars($category['description']) ?></textarea>
    </div>
    <div class="mb-3">
        <label>Status</label>
        <select name="status" class="form-control">
            <option value="1" <?= $category['status'] ? 'selected' : '' ?>>Active</option>
            <option value="0" <?= !$category['status'] ? 'selected' : '' ?>>Inactive</option>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Update Category</button>
    <a href="index.php" class="btn btn-secondary">Cancel</a>
</form>
<?php include '../includes/footer.php'; ?>