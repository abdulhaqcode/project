<?php
require_once '../auth/session-check.php';
require_once '../config/database.php';
if (!isset($_GET['id'])) { header('Location: index.php'); exit; }
$purchase_id = $_GET['id'];

try {
    $pdo->beginTransaction();
    $items = $pdo->prepare("SELECT * FROM purchase_items WHERE purchase_id = ?");
    $items->execute([$purchase_id]);
    $items = $items->fetchAll(PDO::FETCH_ASSOC);

    foreach ($items as $item) {
        // Decrease stock back
        $pdo->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ?")->execute([$item['quantity'], $item['product_id']]);
        $previous = $pdo->prepare("SELECT quantity FROM products WHERE id = ?");
        $previous->execute([$item['product_id']]);
        $new_stock = $previous->fetchColumn();
        $previous_stock = $new_stock + $item['quantity'];
        $pdo->prepare("INSERT INTO stock_movements (product_id, type, quantity, previous_stock, new_stock, note, created_by) VALUES (?, 'OUT', ?, ?, ?, ?, ?)")->execute([
            $item['product_id'], $item['quantity'], $previous_stock, $new_stock,
            "Purchase #$purchase_id reversed", $_SESSION['user_id']
        ]);
    }
    $pdo->prepare("DELETE FROM purchase_items WHERE purchase_id = ?")->execute([$purchase_id]);
    $pdo->prepare("DELETE FROM purchases WHERE id = ?")->execute([$purchase_id]);
    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    die("Error deleting purchase: " . $e->getMessage());
}
header('Location: index.php');
exit;