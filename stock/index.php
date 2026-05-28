<?php
require_once '../auth/session-check.php';
require_once '../config/database.php';
// Redirect to history or show quick links. We'll show stock movement links.
include '../includes/header.php';
?>
<h1>Stock Management</h1>
<div class="list-group mt-4">
    <a href="stock-in.php" class="list-group-item list-group-item-action">
        <h5>Stock IN (Add Inventory)</h5>
        <p>Record new stock received (purchases, returns).</p>
    </a>
    <a href="stock-out.php" class="list-group-item list-group-item-action">
        <h5>Stock OUT (Reduce Inventory)</h5>
        <p>Record stock sold or used.</p>
    </a>
    <a href="history.php" class="list-group-item list-group-item-action">
        <h5>Stock Movement History</h5>
        <p>View all stock in/out logs.</p>
    </a>
</div>
<?php include '../includes/footer.php'; ?>