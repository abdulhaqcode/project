<?php
require_once '../auth/session-check.php';
require_once '../config/database.php';
if (isset($_GET['id'])) {
    // Fetch image to delete file
    $stmt = $pdo->prepare("SELECT image FROM products WHERE id=?");
    $stmt->execute([$_GET['id']]);
    $product = $stmt->fetch();
    if ($product && $product['image']) {
        $filePath = __DIR__ . '/../uploads/' . $product['image'];
        if (file_exists($filePath)) unlink($filePath);
    }
    $stmt = $pdo->prepare("DELETE FROM products WHERE id=?");
    $stmt->execute([$_GET['id']]);
}
header('Location: index.php');
exit;