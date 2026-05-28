<?php
require_once '../auth/session-check.php';
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['movement_type'] ?? '';
    $product_id = $_POST['product_id'];
    $quantity = (int)$_POST['quantity'];
    $note = trim($_POST['note'] ?? '');
    $created_by = $_SESSION['user_id'];

    if ($quantity <= 0) {
        die("Quantity must be positive.");
    }

    // Get current product
    $stmt = $pdo->prepare("SELECT id, quantity FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$product) {
        die("Product not found.");
    }

    $previous_stock = $product['quantity'];
    $new_stock = ($type === 'IN') ? $previous_stock + $quantity : $previous_stock - $quantity;

    // Validate no negative stock for OUT
    if ($new_stock < 0) {
        die("Insufficient stock. Current stock: $previous_stock, attempted to remove: $quantity.");
    }

    // Update product quantity
    $updateStmt = $pdo->prepare("UPDATE products SET quantity = ? WHERE id = ?");
    $updateStmt->execute([$new_stock, $product_id]);

    // Insert stock movement log
    $insertStmt = $pdo->prepare("INSERT INTO stock_movements (product_id, type, quantity, previous_stock, new_stock, note, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $insertStmt->execute([$product_id, $type, $quantity, $previous_stock, $new_stock, $note, $created_by]);

    header('Location: history.php');
    exit;
}
header('Location: index.php');