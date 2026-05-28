<?php
require_once '../auth/session-check.php';
// Simple upload handler if you want to use AJAX later, but store.php/update.php already handle it.
// For now, it's just a placeholder.
echo json_encode(['status' => 'error', 'message' => 'Direct upload not implemented']);