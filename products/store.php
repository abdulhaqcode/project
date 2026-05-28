<?php
require_once '../auth/session-check.php';
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $sku = trim($_POST['sku']);
    $barcode = trim($_POST['barcode']);
    $category_id = $_POST['category_id'] ?: null;
    $purchase_price = $_POST['purchase_price'];
    $selling_price = $_POST['selling_price'];
    $quantity = $_POST['quantity'];
    $min_stock = $_POST['min_stock'];
    $status = $_POST['status'];
    $description = trim($_POST['description']);

    // Image upload
    $imageName = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $imageName = time() . '_' . uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $imageName);
    }

    $sql = "INSERT INTO products (category_id, name, sku, barcode, purchase_price, selling_price, quantity, min_stock, image, description, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$category_id, $name, $sku, $barcode, $purchase_price, $selling_price, $quantity, $min_stock, $imageName, $description, $status]);

    header('Location: index.php');
    exit;
}
header('Location: index.php');