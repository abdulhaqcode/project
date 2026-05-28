<?php
require_once '../auth/session-check.php';
require_once '../config/database.php';

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id=?");
    $stmt->execute([$_GET['id']]);
}
header('Location: index.php');
exit;