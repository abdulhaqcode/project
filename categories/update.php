<?php
require_once '../auth/session-check.php';
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $status = (int)$_POST['status'];

    $stmt = $pdo->prepare("UPDATE categories SET name=?, description=?, status=? WHERE id=?");
    $stmt->execute([$name, $description, $status, $id]);
}
header('Location: index.php');
exit;