<?php
require_once '../auth/session-check.php';
require_once '../config/database.php';
$products = $pdo->query("SELECT id, name, selling_price, quantity FROM products WHERE status=1 AND quantity > 0 ORDER BY name")->fetchAll();
include '../includes/header.php';
?>
<h1>New Sale</h1>
<form id="saleForm" method="post" action="store.php">
    <div class="row mb-3">
        <div class="col-md-4">
            <label>Invoice No</label>
            <input type="text" name="invoice_no" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label>Customer Name</label>
            <input type="text" name="customer_name" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label>Sale Date</label>
            <input type="date" name="sale_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
        </div>
    </div>

    <h4>Products</h4>
    <div id="saleItems">
        <div class="row mb-2 item-row">
            <div class="col-md-5">
                <select name="product_id[]" class="form-select product-select" required>
                    <option value="">Select Product</option>
                    <?php foreach ($products as $p): ?>
                        <option value="<?= $p['id'] ?>" data-price="<?= $p['selling_price'] ?>">
                            <?= htmlspecialchars($p['name']) ?> (Stock: <?= $p['quantity'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <input type="number" name="quantity[]" class="form-control quantity" placeholder="Qty" min="1" required>
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control price" readonly placeholder="Price">
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control subtotal" readonly placeholder="Subtotal">
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-danger remove-row">&times;</button>
            </div>
        </div>
    </div>
    <button type="button" class="btn btn-sm btn-secondary mb-3" id="addRow">+ Add Product</button>
    <div class="mb-3">
        <strong>Grand Total: $<span id="grandTotal">0.00</span></strong>
    </div>
    <button type="submit" class="btn btn-success">Complete Sale</button>
    <a href="index.php" class="btn btn-secondary">Cancel</a>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('saleItems');
    const addBtn = document.getElementById('addRow');
    const grandTotalSpan = document.getElementById('grandTotal');

    function calculateRow(row) {
        const select = row.querySelector('.product-select');
        const qty = row.querySelector('.quantity');
        const priceInput = row.querySelector('.price');
        const subtotalInput = row.querySelector('.subtotal');
        const selectedOption = select.options[select.selectedIndex];
        let price = 0;
        if (selectedOption && selectedOption.dataset.price) {
            price = parseFloat(selectedOption.dataset.price);
        }
        priceInput.value = price.toFixed(2);
        let quantity = parseInt(qty.value) || 0;
        let subtotal = price * quantity;
        subtotalInput.value = subtotal.toFixed(2);
        updateGrandTotal();
    }

    function updateGrandTotal() {
        let total = 0;
        document.querySelectorAll('.subtotal').forEach(function(input) {
            total += parseFloat(input.value) || 0;
        });
        grandTotalSpan.textContent = total.toFixed(2);
    }

    container.addEventListener('change', function(e) {
        if (e.target.matches('.product-select') || e.target.matches('.quantity')) {
            calculateRow(e.target.closest('.item-row'));
        }
    });

    container.addEventListener('click', function(e) {
        if (e.target.matches('.remove-row')) {
            const rows = document.querySelectorAll('.item-row');
            if (rows.length > 1) {
                e.target.closest('.item-row').remove();
                updateGrandTotal();
            }
        }
    });

    addBtn.addEventListener('click', function() {
        const firstRow = document.querySelector('.item-row');
        const newRow = firstRow.cloneNode(true);
        // Clear values
        newRow.querySelector('select').selectedIndex = 0;
        newRow.querySelector('.quantity').value = '';
        newRow.querySelector('.price').value = '';
        newRow.querySelector('.subtotal').value = '';
        container.appendChild(newRow);
    });
});
</script>
<?php include '../includes/footer.php'; ?>