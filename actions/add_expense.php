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
    $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);
    $currency = trim($_POST['currency']);
    $description = trim($_POST['description']);
    $payer_user_id = filter_input(INPUT_POST, 'payer_user_id', FILTER_VALIDATE_INT);
    $is_shared = isset($_POST['is_shared']) ? 1 : 0;

    // Basic validation
    if (empty($trip_id) || $amount === false || empty($description) || $payer_user_id === false) {
        header("Location: ../trip.php?id=$trip_id&error=missing_fields");
        exit;
    }

    $pdo = get_db_connection();
    $stmt = $pdo->prepare(
        'INSERT INTO expenses (trip_id, payer_user_id, amount, currency, description, is_shared) VALUES (?, ?, ?, ?, ?, ?)'
    );

    if ($stmt->execute([$trip_id, $payer_user_id, $amount, $currency, $description, $is_shared])) {
        // Redirect back to the trip page
        header("Location: ../trip.php?id=$trip_id&status=expense_added");
        exit;
    } else {
        // Handle database error
        header("Location: ../trip.php?id=$trip_id&error=db_error");
        exit;
    }
} else {
    // If not a POST request, redirect
    header('Location: ../index.php');
    exit;
}
?>
