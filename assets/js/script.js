// inventory/assets/js/script.js
document.addEventListener('DOMContentLoaded', function() {

    // Auto-focus first input
    const firstInput = document.querySelector('input[autofocus], input:not([type=hidden])');
    if (firstInput) firstInput.focus();

    // ========== Bootstrap Delete Confirmation ==========
    // Instead of using onclick="return confirm(...)" 
    // we use a modal. Attach data-delete attributes to delete links.
    // Example: <a href="delete.php?id=5" class="btn btn-sm btn-danger delete-btn" data-item="Product X">Delete</a>

    // Create modal if not exists
    if (!document.getElementById('deleteModal')) {
        const modalHTML = `
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <p>Are you sure you want to delete <strong id="deleteItemName"></strong>?</p>
                <p class="text-muted small">This action cannot be undone.</p>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="#" id="confirmDeleteBtn" class="btn btn-danger">Delete</a>
              </div>
            </div>
          </div>
        </div>`;
        document.body.insertAdjacentHTML('beforeend', modalHTML);
    }

    // Handle delete button clicks
    document.addEventListener('click', function(e) {
        const deleteBtn = e.target.closest('.delete-btn');
        if (deleteBtn) {
            e.preventDefault();
            const href = deleteBtn.getAttribute('href');
            const itemName = deleteBtn.getAttribute('data-item') || 'this item';
            document.getElementById('deleteItemName').textContent = itemName;
            const confirmBtn = document.getElementById('confirmDeleteBtn');
            confirmBtn.href = href;
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        }
    });

    // Auto-dismiss alerts after 4 seconds (if any)
    const alerts = document.querySelectorAll('.alert:not(.alert-danger)');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 4000);
    });
});