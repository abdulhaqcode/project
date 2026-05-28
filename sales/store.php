<?php
require_once '../auth/session-check.php';
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php'); exit;
}

$invoice_no = trim($_POST['invoice_no']);
$customer_name = trim($_POST['customer_name']);
$sale_date = $_POST['sale_date'];
$created_by = $_SESSION['user_id'];

$product_ids = $_POST['product_id'] ?? [];
$quantities = $_POST['quantity'] ?? [];

if (empty($product_ids) || count(array_filter($quantities, fn($q) => (int)$q > 0)) === 0) {
    die("Please add at least one product with quantity.");
}

try {
    $pdo->beginTransaction();

    // Calculate total
    $total_amount = 0;
    $items = [];
    foreach ($product_ids as $i => $pid) {
        $pid = (int)$pid;
        $qty = (int)($quantities[$i] ?? 0);
        if ($pid <= 0 || $qty <= 0) continue;

        // Get product current stock and price
        $stmt = $pdo->prepare("SELECT id, selling_price, quantity FROM products WHERE id = ? FOR UPDATE");
        $stmt->execute([$pid]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$product) throw new Exception("Product ID $pid not found.");
        if ($product['quantity'] < $qty) {
            throw new Exception("Insufficient stock for product '{$product['id']}'. Available: {$product['quantity']}");
        }
        $price = $product['selling_price'];
        $subtotal = $price * $qty;
        $total_amount += $subtotal;

        $items[] = [
            'product_id' => $pid,
            'quantity' => $qty,
            'price' => $price,
            'subtotal' => $subtotal,
            'previous_stock' => $product['quantity']
        ];
    }

    // Insert sale header
    $stmt = $pdo->prepare("INSERT INTO sales (invoice_no, customer_name, total_amount, sale_date, created_by) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$invoice_no, $customer_name, $total_amount, $sale_date, $created_by]);
    $sale_id = $pdo->lastInsertId();

    // Insert sale items and update stock
    $insertItem = $pdo->prepare("INSERT INTO sale_items (sale_id, product_id, quantity, price, subtotal) VALUES (?, ?, ?, ?, ?)");
    $updateStock = $pdo->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ?");
    $insertMovement = $pdo->prepare("INSERT INTO stock_movements (product_id, type, quantity, previous_stock, new_stock, note, created_by) VALUES (?, 'OUT', ?, ?, ?, ?, ?)");

    foreach ($items as $item) {
        $pid = $item['product_id'];
        $qty = $item['quantity'];
        $previous = $item['previous_stock'];
        $new_stock = $previous - $qty;

        $insertItem->execute([$sale_id, $pid, $qty, $item['price'], $item['subtotal']]);
        $updateStock->execute([$qty, $pid]);
        $note = "Sale #$sale_id ($invoice_no)";
        $insertMovement->execute([$pid, $qty, $previous, $new_stock, $note, $created_by]);
    }

    $pdo->commit();
    header('Location: view.php?id=' . $sale_id);
    exit;
} catch (Exception $e) {
    $pdo->rollBack();
    die("Error: " . $e->getMessage());
}
?>