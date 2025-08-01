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
    $date = $_POST['date'];
    $activity_description = trim($_POST['activity_description']);
    $location = trim($_POST['location']);

    // Basic validation
    if (empty($trip_id) || empty($date) || empty($activity_description)) {
        // Handle error - maybe redirect with an error message
        header("Location: ../trip.php?id=$trip_id&error=missing_fields");
        exit;
    }

    $pdo = get_db_connection();
    $stmt = $pdo->prepare(
        'INSERT INTO itinerary (trip_id, date, activity_description, location) VALUES (?, ?, ?, ?)'
    );

    if ($stmt->execute([$trip_id, $date, $activity_description, $location])) {
        // Redirect back to the trip page with a success message
        header("Location: ../trip.php?id=$trip_id&status=itinerary_added");
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
