<?php
require_once '../auth/session-check.php';
require_once '../config/database.php';

// Strict admin-only check
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

// Handle role update (POST with confirmation)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_role') {
    $user_id = (int)$_POST['user_id'];
    $new_role = $_POST['new_role'];
    if ($user_id !== $_SESSION['user_id'] && in_array($new_role, ['admin','staff'])) {
        $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->execute([$new_role, $user_id]);
    }
    header('Location: index.php');
    exit;
}

// Handle user deletion (POST with confirmation)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_user') {
    $user_id = (int)$_POST['user_id'];
    // Cannot delete yourself
    if ($user_id !== $_SESSION['user_id']) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
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
                    <!-- Role change form (requires explicit submit) -->
                    <form method="post" class="d-inline-block me-2">
                        <input type="hidden" name="action" value="update_role">
                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                        <select name="new_role" class="form-select form-select-sm d-inline w-auto" onchange="if(confirm('Change role of <?= htmlspecialchars($user['name']) ?> to ' + this.options[this.selectedIndex].text + '?')) this.form.submit()">
                            <option value="staff" <?= $user['role'] === 'staff' ? 'selected' : '' ?>>Staff</option>
                            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                        </select>
                    </form>
                    <!-- Delete button triggers modal -->
                    <a href="index.php?delete=<?= $user['id'] ?>" 
                       class="btn btn-sm btn-danger delete-btn" 
                       data-item="User: <?= htmlspecialchars($user['name']) ?>"
                       data-id="<?= $user['id'] ?>">Delete</a>
                <?php else: ?>
                    <span class="text-muted">Current user</span>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<!-- Hidden form for deletion, submitted by modal -->
<form id="deleteUserForm" method="post" style="display:none;">
    <input type="hidden" name="action" value="delete_user">
    <input type="hidden" name="user_id" id="deleteUserId" value="">
</form>

<script>
// Override the generic delete modal for user deletion to use POST form
document.addEventListener('DOMContentLoaded', function() {
    // Wait for delete modal to be ready
    const observer = new MutationObserver(function() {
        const confirmBtn = document.getElementById('confirmDeleteBtn');
        if (confirmBtn) {
            confirmBtn.addEventListener('click', function(e) {
                // Check if the delete button had data-id (for users)
                const deleteBtn = document.querySelector('.delete-btn[data-id]');
                if (deleteBtn) {
                    e.preventDefault();
                    document.getElementById('deleteUserId').value = deleteBtn.getAttribute('data-id');
                    document.getElementById('deleteUserForm').submit();
                }
                // Otherwise, the original link behavior works (for other modules)
            });
            observer.disconnect();
        }
    });
    observer.observe(document.body, { childList: true, subtree: true });
});
</script>

<?php include '../includes/footer.php'; ?>