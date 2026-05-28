<?php
require_once '../auth/session-check.php';
require_once '../config/database.php';
if (!isset($_GET['id'])) { header('Location: index.php'); exit; }
$sale_id = $_GET['id'];

try {
    $pdo->beginTransaction();
    // Get all items for this sale
    $items = $pdo->prepare("SELECT * FROM sale_items WHERE sale_id = ?");
    $items->execute([$sale_id]);
    $items = $items->fetchAll(PDO::FETCH_ASSOC);

    foreach ($items as $item) {
        // Increase stock back
        $pdo->prepare("UPDATE products SET quantity = quantity + ? WHERE id = ?")->execute([$item['quantity'], $item['product_id']]);
        // Log reverse movement (IN) as correction
        $previous = $pdo->prepare("SELECT quantity FROM products WHERE id = ?");
        $previous->execute([$item['product_id']]);
        $new_stock = $previous->fetchColumn();
        $previous_stock = $new_stock - $item['quantity'];
        $pdo->prepare("INSERT INTO stock_movements (product_id, type, quantity, previous_stock, new_stock, note, created_by) VALUES (?, 'IN', ?, ?, ?, ?, ?)")->execute([
            $item['product_id'], $item['quantity'], $previous_stock, $new_stock,
            "Sale #$sale_id reversed", $_SESSION['user_id']
        ]);
    }
    // Delete items (cascade should handle, but to be safe)
    $pdo->prepare("DELETE FROM sale_items WHERE sale_id = ?")->execute([$sale_id]);
    $pdo->prepare("DELETE FROM sales WHERE id = ?")->execute([$sale_id]);
    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    die("Error deleting sale: " . $e->getMessage());
}
header('Location: index.php');
exit;