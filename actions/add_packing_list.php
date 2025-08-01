<?php
require_once '../includes/database.php';
session_start();

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $trip_id = $_POST['trip_id'];
    $list_name = trim($_POST['list_name']);

    if (empty($trip_id) || empty($list_name)) {
        header("Location: ../trip.php?id=$trip_id&error=missing_fields");
        exit;
    }

    $pdo = get_db_connection();
    $stmt = $pdo->prepare(
        'INSERT INTO packing_lists (trip_id, list_name) VALUES (?, ?)'
    );

    if ($stmt->execute([$trip_id, $list_name])) {
        header("Location: ../trip.php?id=$trip_id&status=list_created");
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
