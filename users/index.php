<?php
require_once '../auth/session-check.php';
require_once '../config/database.php';

// Only admins can access
if ($_SESSION['user_role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

// Handle role update
if (isset($_POST['update_role']) && isset($_POST['user_id']) && isset($_POST['new_role'])) {
    $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->execute([$_POST['new_role'], $_POST['user_id']]);
    header('Location: index.php');
    exit;
}

// Handle user deletion (except yourself)
if (isset($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];
    if ($deleteId !== $_SESSION['user_id']) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$deleteId]);
    }
    header('Location: index.php');
    exit;
}

$users = $pdo->query("SELECT id, name, username, role, created_at FROM users ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>

<h1>User Management</h1>
<a href="../auth/signup.php" class="btn btn-primary mb-3">Add New User</a>

<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Username</th>
            <th>Role</th>
            <th>Created</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($users as $user): ?>
        <tr>
            <td><?= $user['id'] ?></td>
            <td><?= htmlspecialchars($user['name']) ?></td>
            <td><?= htmlspecialchars($user['username']) ?></td>
            <td>
                <span class="badge bg-<?= $user['role'] === 'admin' ? 'danger' : 'info' ?>">
                    <?= $user['role'] ?>
                </span>
            </td>
            <td><?= date('Y-m-d', strtotime($user['created_at'])) ?></td>
            <td>
                <?php if ($user['id'] !== $_SESSION['user_id']): ?>
                    <!-- Change Role Form -->
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                        <select name="new_role" onchange="this.form.submit()" class="form-select form-select-sm d-inline w-auto">
                            <option value="staff" <?= $user['role'] === 'staff' ? 'selected' : '' ?>>Staff</option>
                            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                        </select>
                        <input type="hidden" name="update_role" value="1">
                    </form>
                    <a href="index.php?delete=<?= $user['id'] ?>" class="btn btn-sm btn-danger ms-2" onclick="return confirm('Delete this user?')">Delete</a>
                <?php else: ?>
                    <span class="text-muted">Current user</span>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php include '../includes/footer.php'; ?>