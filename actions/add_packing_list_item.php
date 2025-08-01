<?php
require_once '../includes/database.php';
session_start();

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $list_id = $_POST['list_id'];
    $trip_id = $_POST['trip_id']; // For redirection
    $item_name = trim($_POST['item_name']);

    if (empty($list_id) || empty($item_name) || empty($trip_id)) {
        header("Location: ../trip.php?id=$trip_id&error=missing_fields");
        exit;
    }

    $pdo = get_db_connection();
    $stmt = $pdo->prepare(
        'INSERT INTO packing_list_items (list_id, item_name) VALUES (?, ?)'
    );

    if ($stmt->execute([$list_id, $item_name])) {
        header("Location: ../trip.php?id=$trip_id&status=item_added");
        exit;
    } else {
        header("Location: ../trip.php?id=$trip_id&error=db_error");
        exit;
    }
} else {
    header('Location: ../index.php');
    exit;
}
?>
