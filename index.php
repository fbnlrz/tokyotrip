<?php
require_once 'includes/database.php';

// This will be the main dashboard page, so we need to check for a valid session.
session_start();

// If the user is not logged in, redirect to the login page.
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$pdo = get_db_connection();
$error_message = '';
$success_message = '';

// Handle new trip creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_trip'])) {
    $trip_name = trim($_POST['trip_name']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    if (empty($trip_name) || empty($start_date) || empty($end_date)) {
        $error_message = 'Please fill in all fields for the new trip.';
    } elseif (strtotime($start_date) > strtotime($end_date)) {
        $error_message = 'Start date cannot be after the end date.';
    } else {
        $stmt = $pdo->prepare('INSERT INTO trips (trip_name, start_date, end_date) VALUES (?, ?, ?)');
        if ($stmt->execute([$trip_name, $start_date, $end_date])) {
            $success_message = 'Trip created successfully!';
        } else {
            $error_message = 'Failed to create the trip. Please try again.';
        }
    }
}

// Fetch all trips to display on the dashboard
$trips_stmt = $pdo->query('SELECT id, trip_name, start_date, end_date FROM trips ORDER BY start_date ASC');
$trips = $trips_stmt->fetchAll();

include 'includes/header.php';
?>

<h1 class="mb-4">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>

<div class="row">
    <!-- Left column for creating trips -->
    <div class="col-md-4">
        <div class="card bg-secondary text-light">
            <div class="card-body">
                <h3 class="card-title">Create a New Trip</h3>

                <?php if ($error_message): ?>
                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                <?php endif; ?>

                <?php if ($success_message): ?>
                    <div class="alert alert-success"><?php echo $success_message; ?></div>
                <?php endif; ?>

                <form action="index.php" method="post">
                    <div class="mb-3">
                        <label for="trip_name" class="form-label">Trip Name</label>
                        <input type="text" class="form-control" id="trip_name" name="trip_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" required>
                    </div>
                    <button type="submit" name="create_trip" class="btn btn-primary w-100">Create Trip</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Right column for listing trips -->
    <div class="col-md-8">
        <h2>All Planned Trips</h2>
        <div class="list-group">
            <?php if (empty($trips)): ?>
                <p>No trips have been planned yet. Create one to get started!</p>
            <?php else: ?>
                <?php foreach ($trips as $trip): ?>
                    <a href="trip.php?id=<?php echo $trip['id']; ?>" class="list-group-item list-group-item-action bg-secondary text-light border-dark">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1"><?php echo htmlspecialchars($trip['trip_name']); ?></h5>
                            <small>Trip ID: <?php echo $trip['id']; ?></small>
                        </div>
                        <p class="mb-1">
                            From: <?php echo date('M j, Y', strtotime($trip['start_date'])); ?>
                            To: <?php echo date('M j, Y', strtotime($trip['end_date'])); ?>
                        </p>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
