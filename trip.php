<?php
require_once 'includes/database.php';
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get trip ID from URL
$trip_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($trip_id === 0) {
    // A simple way to handle missing ID, maybe redirect to index
    header('Location: index.php');
    exit;
}

$pdo = get_db_connection();

// Fetch trip details
$stmt = $pdo->prepare('SELECT * FROM trips WHERE id = ?');
$stmt->execute([$trip_id]);
$trip = $stmt->fetch();

// If trip not found, redirect or show error
if (!$trip) {
    // For now, just redirect back to the dashboard
    header('Location: index.php');
    exit;
}

include 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1><?php echo htmlspecialchars($trip['trip_name']); ?></h1>
        <p class="text-muted">
            <?php echo date('F j, Y', strtotime($trip['start_date'])); ?> - <?php echo date('F j, Y', strtotime($trip['end_date'])); ?>
        </p>
    </div>
    <a href="index.php" class="btn btn-secondary">Back to Dashboard</a>
</div>

<!-- Tab navigation -->
<ul class="nav nav-tabs" id="tripTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="itinerary-tab" data-bs-toggle="tab" data-bs-target="#itinerary" type="button" role="tab" aria-controls="itinerary" aria-selected="true">Itinerary</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="bookings-tab" data-bs-toggle="tab" data-bs-target="#bookings" type="button" role="tab" aria-controls="bookings" aria-selected="false">Bookings</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="financials-tab" data-bs-toggle="tab" data-bs-target="#financials" type="button" role="tab" aria-controls="financials" aria-selected="false">Financials</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="packing-lists-tab" data-bs-toggle="tab" data-bs-target="#packing-lists" type="button" role="tab" aria-controls="packing-lists" aria-selected="false">Packing Lists</button>
    </li>
</ul>

<!-- Tab content -->
<div class="tab-content pt-3" id="tripTabContent">
    <!-- Itinerary Tab -->
    <div class="tab-pane fade show active" id="itinerary" role="tabpanel" aria-labelledby="itinerary-tab">
        <h3>Day-by-Day Itinerary</h3>
        <p>Itinerary management will be implemented here.</p>
        <!-- Placeholder for itinerary content -->
    </div>

    <!-- Bookings Tab -->
    <div class="tab-pane fade" id="bookings" role="tabpanel" aria-labelledby="bookings-tab">
        <h3>Booking Overview</h3>
        <p>Booking management will be implemented here.</p>
        <!-- Placeholder for bookings content -->
    </div>

    <!-- Financials Tab -->
    <div class="tab-pane fade" id="financials" role="tabpanel" aria-labelledby="financials-tab">
        <h3>Financials</h3>
        <p>Budgeting and expense tracking will be implemented here.</p>
        <!-- Placeholder for financials content -->
    </div>

    <!-- Packing Lists Tab -->
    <div class="tab-pane fade" id="packing-lists" role="tabpanel" aria-labelledby="packing-lists-tab">
        <h3>Packing Lists</h3>
        <p>Packing list management will be implemented here.</p>
        <!-- Placeholder for packing lists content -->
    </div>
</div>

<?php include 'includes/footer.php'; ?>
