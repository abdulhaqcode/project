<?php
require_once '../auth/session-check.php';
require_once '../config/database.php';

$stmt = $pdo->query("SELECT p.*, u.name as user_name FROM purchases p LEFT JOIN users u ON p.created_by = u.id ORDER BY p.id DESC");
$purchases = $stmt->fetchAll(PDO::FETCH_ASSOC);
include '../includes/header.php';
?>
<h1>Purchases</h1>
<a href="create.php" class="btn btn-primary mb-3">New Purchase</a>
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Invoice No</th>
            <th>Supplier</th>
            <th>Total</th>
            <th>Date</th>
            <th>Created By</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($purchases as $purchase): ?>
        <tr>
            <td><?= $purchase['id'] ?></td>
            <td><?= htmlspecialchars($purchase['invoice_no']) ?></td>
            <td><?= htmlspecialchars($purchase['supplier_name']) ?></td>
            <td><?= number_format($purchase['total_amount'], 2) ?></td>
            <td><?= $purchase['purchase_date'] ?></td>
            <td><?= htmlspecialchars($purchase['user_name'] ?? '') ?></td>
            <td>
                <a href="view.php?id=<?= $purchase['id'] ?>" class="btn btn-sm btn-info">View</a>
                <a href="delete.php?id=<?= $purchase['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this purchase and reverse stock?')">Delete</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php include '../includes/footer.php'; ?>