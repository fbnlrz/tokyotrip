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
    $booking_type = $_POST['booking_type'];
    $details = $_POST['details'];

    // Basic validation
    if (empty($trip_id) || empty($booking_type) || empty($details)) {
        header("Location: ../trip.php?id=$trip_id&error=missing_fields");
        exit;
    }

    // Encode the details array into a JSON string
    $details_json = json_encode($details);

    $pdo = get_db_connection();
    $stmt = $pdo->prepare(
        'INSERT INTO bookings (trip_id, type, details) VALUES (?, ?, ?)'
    );

    if ($stmt->execute([$trip_id, $booking_type, $details_json])) {
        // Redirect back to the trip page with a success message
        header("Location: ../trip.php?id=$trip_id&status=booking_added");
        exit;
    } else {
        // Handle database error
        header("Location: ../trip.php?id=$trip_id&error=db_error");
        exit;
    }
} else {
    // If not a POST request, redirect to the dashboard
    header('Location: ../index.php');
    exit;
}
?>
