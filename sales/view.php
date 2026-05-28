<?php
require_once '../auth/session-check.php';
require_once '../config/database.php';
if (!isset($_GET['id'])) { header('Location: index.php'); exit; }
$stmt = $pdo->prepare("SELECT * FROM sales WHERE id = ?");
$stmt->execute([$_GET['id']]);
$sale = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$sale) die("Sale not found.");

$items = $pdo->prepare("SELECT si.*, p.name as product_name FROM sale_items si JOIN products p ON si.product_id = p.id WHERE si.sale_id = ?");
$items->execute([$sale['id']]);
$items = $items->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>
<h1>Sale Invoice</h1>
<div class="card">
    <div class="card-body">
        <h5>Invoice No: <?= htmlspecialchars($sale['invoice_no']) ?></h5>
        <p>Customer: <?= htmlspecialchars($sale['customer_name']) ?></p>
        <p>Date: <?= $sale['sale_date'] ?></p>
        <p>Total: $<?= number_format($sale['total_amount'], 2) ?></p>
    </div>
</div>

<h3 class="mt-4">Items</h3>
<table class="table table-bordered">
    <thead>
        <tr><th>Product</th><th>Quantity</th><th>Price</th><th>Subtotal</th></tr>
    </thead>
    <tbody>
    <?php foreach ($items as $item): ?>
        <tr>
            <td><?= htmlspecialchars($item['product_name']) ?></td>
            <td><?= $item['quantity'] ?></td>
            <td><?= number_format($item['price'], 2) ?></td>
            <td><?= number_format($item['subtotal'], 2) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<a href="index.php" class="btn btn-secondary">Back to Sales</a>
<?php include '../includes/footer.php'; ?>