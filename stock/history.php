<?php
require_once '../auth/session-check.php';
require_once '../config/database.php';

$stmt = $pdo->query("SELECT sm.*, p.name as product_name, u.name as user_name
                     FROM stock_movements sm
                     JOIN products p ON sm.product_id = p.id
                     LEFT JOIN users u ON sm.created_by = u.id
                     ORDER BY sm.created_at DESC");
$movements = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>
<h1>Stock Movement History</h1>
<a href="index.php" class="btn btn-primary mb-3">Back to Stock Menu</a>
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Product</th>
            <th>Type</th>
            <th>Qty</th>
            <th>Previous</th>
            <th>New</th>
            <th>Note</th>
            <th>Done By</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($movements as $mov): ?>
        <tr>
            <td><?= $mov['id'] ?></td>
            <td><?= htmlspecialchars($mov['product_name']) ?></td>
            <td>
                <span class="badge bg-<?= $mov['type'] === 'IN' ? 'success' : 'danger' ?>"><?= $mov['type'] ?></span>
            </td>
            <td><?= $mov['quantity'] ?></td>
            <td><?= $mov['previous_stock'] ?></td>
            <td><?= $mov['new_stock'] ?></td>
            <td><?= htmlspecialchars($mov['note']) ?></td>
            <td><?= htmlspecialchars($mov['user_name'] ?? 'Unknown') ?></td>
            <td><?= date('Y-m-d H:i', strtotime($mov['created_at'])) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php include '../includes/footer.php'; ?>