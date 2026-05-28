<?php
require_once '../auth/session-check.php';
require_once '../config/database.php';

$stmt = $pdo->query("SELECT s.*, u.name as user_name FROM sales s LEFT JOIN users u ON s.created_by = u.id ORDER BY s.id DESC");
$sales = $stmt->fetchAll(PDO::FETCH_ASSOC);
include '../includes/header.php';
?>
<h1>Sales</h1>
<a href="create.php" class="btn btn-primary mb-3">New Sale</a>
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Invoice No</th>
            <th>Customer</th>
            <th>Total</th>
            <th>Date</th>
            <th>Created By</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($sales as $sale): ?>
        <tr>
            <td><?= $sale['id'] ?></td>
            <td><?= htmlspecialchars($sale['invoice_no']) ?></td>
            <td><?= htmlspecialchars($sale['customer_name']) ?></td>
            <td><?= number_format($sale['total_amount'], 2) ?></td>
            <td><?= $sale['sale_date'] ?></td>
            <td><?= htmlspecialchars($sale['user_name'] ?? '') ?></td>
            <td>
                <a href="view.php?id=<?= $sale['id'] ?>" class="btn btn-sm btn-info">View</a>
                <a href="delete.php?id=<?= $sale['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this sale and reverse stock?')">Delete</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php include '../includes/footer.php'; ?>