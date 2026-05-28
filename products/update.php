<?php
require_once '../auth/session-check.php';
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
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

    // Handle image update: if a new file is uploaded, replace the old one.
    $imageName = $_POST['old_image'] ?? '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../uploads/';
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $imageName = time() . '_' . uniqid() . '.' . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $imageName);
        // Optionally delete old file if exists
        if (!empty($_POST['old_image']) && file_exists($uploadDir . $_POST['old_image'])) {
            unlink($uploadDir . $_POST['old_image']);
        }
    }

    $sql = "UPDATE products SET category_id=?, name=?, sku=?, barcode=?, purchase_price=?, selling_price=?, quantity=?, min_stock=?, image=?, description=?, status=? WHERE id=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$category_id, $name, $sku, $barcode, $purchase_price, $selling_price, $quantity, $min_stock, $imageName, $description, $status, $id]);

    header('Location: index.php');
    exit;
}
header('Location: index.php');