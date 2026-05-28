<?php
require_once '../auth/session-check.php';
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php'); exit;
}

$invoice_no = trim($_POST['invoice_no']);
$supplier_name = trim($_POST['supplier_name']);
$purchase_date = $_POST['purchase_date'];
$created_by = $_SESSION['user_id'];

$product_ids = $_POST['product_id'] ?? [];
$quantities = $_POST['quantity'] ?? [];

if (empty($product_ids) || count(array_filter($quantities, fn($q) => (int)$q > 0)) === 0) {
    die("Please add at least one product with quantity.");
}

try {
    $pdo->beginTransaction();

    $total_amount = 0;
    $items = [];
    foreach ($product_ids as $i => $pid) {
        $pid = (int)$pid;
        $qty = (int)($quantities[$i] ?? 0);
        if ($pid <= 0 || $qty <= 0) continue;

        // Get product (use purchase price)
        $stmt = $pdo->prepare("SELECT id, purchase_price, quantity FROM products WHERE id = ? FOR UPDATE");
        $stmt->execute([$pid]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$product) throw new Exception("Product ID $pid not found.");
        $price = $product['purchase_price'];
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

    // Insert purchase header
    $stmt = $pdo->prepare("INSERT INTO purchases (invoice_no, supplier_name, total_amount, purchase_date, created_by) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$invoice_no, $supplier_name, $total_amount, $purchase_date, $created_by]);
    $purchase_id = $pdo->lastInsertId();

    // Insert items and increase stock
    $insertItem = $pdo->prepare("INSERT INTO purchase_items (purchase_id, product_id, quantity, price, subtotal) VALUES (?, ?, ?, ?, ?)");
    $updateStock = $pdo->prepare("UPDATE products SET quantity = quantity + ? WHERE id = ?");
    $insertMovement = $pdo->prepare("INSERT INTO stock_movements (product_id, type, quantity, previous_stock, new_stock, note, created_by) VALUES (?, 'IN', ?, ?, ?, ?, ?)");

    foreach ($items as $item) {
        $pid = $item['product_id'];
        $qty = $item['quantity'];
        $previous = $item['previous_stock'];
        $new_stock = $previous + $qty;

        $insertItem->execute([$purchase_id, $pid, $qty, $item['price'], $item['subtotal']]);
        $updateStock->execute([$qty, $pid]);
        $note = "Purchase #$purchase_id ($invoice_no)";
        $insertMovement->execute([$pid, $qty, $previous, $new_stock, $note, $created_by]);
    }

    $pdo->commit();
    header('Location: view.php?id=' . $purchase_id);
    exit;
} catch (Exception $e) {
    $pdo->rollBack();
    die("Error: " . $e->getMessage());
}