<?php
require_once '../includes/database.php';
session_start();

header('Content-Type: application/json');

// Basic security checks
if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$item_id = filter_input(INPUT_POST, 'item_id', FILTER_VALIDATE_INT);
$is_checked = filter_input(INPUT_POST, 'is_checked', FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

if ($item_id === false || $is_checked === null) {
    echo json_encode(['success' => false, 'error' => 'Invalid input']);
    exit;
}

$pdo = get_db_connection();

// Here you might want to add an extra check to ensure the user has permission for this trip/item
// For now, we'll assume if they are logged in, it's okay.

$stmt = $pdo->prepare(
    'UPDATE packing_list_items SET is_checked = ? WHERE id = ?'
);

if ($stmt->execute([$is_checked ? 1 : 0, $item_id])) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Database update failed']);
}
?>
