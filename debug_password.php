<?php
require_once 'config/database.php';

// Fetch the admin user
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute(['admin@example.com']);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    echo "User found: " . $user['name'] . "<br>";
    echo "Stored hash: " . $user['password'] . "<br>";
    echo "Does 'admin123' verify? " . (password_verify('admin123', $user['password']) ? 'YES' : 'NO') . "<br>";
    echo "<br>Now generate a fresh hash for 'admin123':<br>";
    echo password_hash('admin123', PASSWORD_DEFAULT);
} else {
    echo "User not found.";
}
?>