<?php
require_once '../auth/session-check.php';
require_once '../config/database.php';

// Only admins can access
if ($_SESSION['user_role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Validation
    if (empty($name) || empty($username) || empty($password)) {
        $error = 'All fields are required.';
    } else {
        // Check if username already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error = 'Username already taken.';
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, username, password, role) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $username, $hashedPassword, $role]);
            $success = 'User created successfully.';
        }
    }
}

include '../includes/header.php';
?>

<h1>Create New User</h1>

<?php if ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="alert alert-success"><?= $success ?></div>
<?php endif; ?>

<form method="post">
    <div class="mb-3">
        <label>Full Name</label>
        <input type="text" name="name" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Username</label>
        <input type="text" name="username" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Password</label>
        <input type="password" name="password" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Role</label>
        <select name="role" class="form-select" required>
            <option value="staff">Staff</option>
            <option value="admin">Admin</option>
        </select>
    </div>
    <button type="submit" class="btn btn-success">Create User</button>
    <a href="../users/index.php" class="btn btn-secondary">Back to Users</a>
</form>

<?php include '../includes/footer.php'; ?>