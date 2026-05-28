<?php
require_once '../auth/session-check.php';
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $status = isset($_POST['status']) ? (int)$_POST['status'] : 1;

    $stmt = $pdo->prepare("INSERT INTO categories (name, description, status) VALUES (?, ?, ?)");
    $stmt->execute([$name, $description, $status]);

    header('Location: index.php');
    exit;
}
header('Location: index.php');