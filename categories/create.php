<?php require_once '../auth/session-check.php'; include '../includes/header.php'; ?>
<h1>Add Category</h1>
<form method="post" action="store.php">
    <div class="mb-3">
        <label>Name</label>
        <input type="text" name="name" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Description</label>
        <textarea name="description" class="form-control"></textarea>
    </div>
    <div class="mb-3">
        <label>Status</label>
        <select name="status" class="form-control">
            <option value="1">Active</option>
            <option value="0">Inactive</option>
        </select>
    </div>
    <button type="submit" class="btn btn-success">Save Category</button>
    <a href="index.php" class="btn btn-secondary">Cancel</a>
</form>
<?php include '../includes/footer.php'; ?>