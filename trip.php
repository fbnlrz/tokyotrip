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

<?php
// Display success or error messages
if (isset($_GET['status'])) {
    $status = $_GET['status'];
    $message = '';
    switch ($status) {
        case 'itinerary_added':
            $message = 'Itinerary item added successfully!';
            break;
        case 'booking_added':
            $message = 'Booking added successfully!';
            break;
        case 'expense_added':
            $message = 'Expense added successfully!';
            break;
        case 'list_created':
            $message = 'Packing list created successfully!';
            break;
        case 'item_added':
            $message = 'Packing list item added successfully!';
            break;
    }
    if ($message) {
        echo '<div class="alert alert-success">' . htmlspecialchars($message) . '</div>';
    }
}

if (isset($_GET['error'])) {
    $error = $_GET['error'];
    $message = 'An unknown error occurred.';
    switch ($error) {
        case 'missing_fields':
            $message = 'Please fill in all required fields.';
            break;
        case 'db_error':
            $message = 'A database error occurred. Please try again.';
            break;
    }
    echo '<div class="alert alert-danger">' . htmlspecialchars($message) . '</div>';
}
?>

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

        <?php
        // Fetch itinerary items
        $itinerary_stmt = $pdo->prepare('SELECT date, activity_description, location FROM itinerary WHERE trip_id = ? ORDER BY date ASC');
        $itinerary_stmt->execute([$trip_id]);
        $itinerary_items = $itinerary_stmt->fetchAll();

        // Group itinerary by date
        $grouped_itinerary = [];
        foreach ($itinerary_items as $item) {
            $grouped_itinerary[$item['date']][] = $item;
        }
        ?>

        <?php if (empty($grouped_itinerary)): ?>
            <p>No itinerary items have been added yet.</p>
        <?php else: ?>
            <?php foreach ($grouped_itinerary as $date => $items): ?>
                <h4 class="mt-4"><?php echo date('F j, Y', strtotime($date)); ?></h4>
                <ul class="list-group">
                    <?php foreach ($items as $item): ?>
                        <li class="list-group-item bg-secondary text-light">
                            <strong><?php echo htmlspecialchars($item['activity_description']); ?></strong>
                            <?php if (!empty($item['location'])): ?>
                                <br><small class="text-muted">Location: <?php echo htmlspecialchars($item['location']); ?></small>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- Add Itinerary Item Form -->
        <div class="card bg-secondary text-light mt-4">
            <div class="card-body">
                <h5 class="card-title">Add New Itinerary Item</h5>
                <form action="actions/add_itinerary.php" method="post">
                    <input type="hidden" name="trip_id" value="<?php echo $trip_id; ?>">
                    <div class="mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" class="form-control" id="date" name="date" required>
                    </div>
                    <div class="mb-3">
                        <label for="activity_description" class="form-label">Activity Description</label>
                        <textarea class="form-control" id="activity_description" name="activity_description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="location" class="form-label">Location (Optional)</label>
                        <input type="text" class="form-control" id="location" name="location">
                    </div>
                    <button type="submit" class="btn btn-primary">Add to Itinerary</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Bookings Tab -->
    <div class="tab-pane fade" id="bookings" role="tabpanel" aria-labelledby="bookings-tab">
        <h3>Booking Overview</h3>

        <?php
        // Fetch bookings
        $bookings_stmt = $pdo->prepare('SELECT type, details FROM bookings WHERE trip_id = ? ORDER BY id DESC');
        $bookings_stmt->execute([$trip_id]);
        $bookings = $bookings_stmt->fetchAll();

        // Group bookings by type
        $grouped_bookings = ['flight' => [], 'hotel' => [], 'transport' => []];
        foreach ($bookings as $booking) {
            $grouped_bookings[$booking['type']][] = json_decode($booking['details'], true);
        }
        ?>

        <?php foreach ($grouped_bookings as $type => $bookings_list): ?>
            <h4 class="mt-4 text-capitalize"><?php echo htmlspecialchars($type); ?>s</h4>
            <?php if (empty($bookings_list)): ?>
                <p>No <?php echo htmlspecialchars($type); ?> bookings yet.</p>
            <?php else: ?>
                <div class="list-group">
                    <?php foreach ($bookings_list as $details): ?>
                        <div class="list-group-item bg-secondary text-light">
                            <?php if ($type === 'flight'): ?>
                                <strong><?php echo htmlspecialchars($details['airline']); ?> - Flight <?php echo htmlspecialchars($details['flight_number']); ?></strong><br>
                                Departs: <?php echo htmlspecialchars($details['departure_airport']); ?> at <?php echo htmlspecialchars($details['departure_time']); ?><br>
                                Arrives: <?php echo htmlspecialchars($details['arrival_airport']); ?> at <?php echo htmlspecialchars($details['arrival_time']); ?><br>
                                Reference: <?php echo htmlspecialchars($details['booking_reference']); ?>
                            <?php elseif ($type === 'hotel'): ?>
                                <strong><?php echo htmlspecialchars($details['hotel_name']); ?></strong><br>
                                Address: <?php echo htmlspecialchars($details['address']); ?><br>
                                Check-in: <?php echo htmlspecialchars($details['check_in_date']); ?>, Check-out: <?php echo htmlspecialchars($details['check_out_date']); ?><br>
                                Confirmation: <?php echo htmlspecialchars($details['booking_confirmation']); ?>
                            <?php elseif ($type === 'transport'): ?>
                                <strong><?php echo htmlspecialchars($details['transport_type']); ?></strong><br>
                                Details: <?php echo htmlspecialchars($details['transport_details']); ?>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>

        <!-- Add Booking Form -->
        <div class="card bg-secondary text-light mt-4">
            <div class="card-body">
                <h5 class="card-title">Add New Booking</h5>
                <form action="actions/add_booking.php" method="post">
                    <input type="hidden" name="trip_id" value="<?php echo $trip_id; ?>">
                    <div class="mb-3">
                        <label for="booking_type" class="form-label">Booking Type</label>
                        <select class="form-control" id="booking_type" name="booking_type" required>
                            <option value="">Select a type...</option>
                            <option value="flight">Flight</option>
                            <option value="hotel">Hotel</option>
                            <option value="transport">Transport</option>
                        </select>
                    </div>

                    <!-- Dynamic fields will be injected here by JavaScript -->
                    <div id="booking_details_fields"></div>

                    <button type="submit" class="btn btn-primary mt-3">Add Booking</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Financials Tab -->
    <div class="tab-pane fade" id="financials" role="tabpanel" aria-labelledby="financials-tab">
        <h3>Financials</h3>

        <?php
        // Fetch trip members
        $members_stmt = $pdo->prepare('
            SELECT u.id, u.username
            FROM users u
            JOIN trip_members tm ON u.id = tm.user_id
            WHERE tm.trip_id = ?
        ');
        $members_stmt->execute([$trip_id]);
        $trip_members = $members_stmt->fetchAll();

        // Fetch expenses
        $expenses_stmt = $pdo->prepare('
            SELECT e.amount, e.currency, e.description, e.is_shared, u.username as payer_name
            FROM expenses e
            JOIN users u ON e.payer_user_id = u.id
            WHERE e.trip_id = ?
            ORDER BY e.id DESC
        ');
        $expenses_stmt->execute([$trip_id]);
        $expenses = $expenses_stmt->fetchAll();

        $total_expenses = 0;
        foreach ($expenses as $expense) {
            $total_expenses += $expense['amount'];
        }
        ?>

        <div class="row">
            <div class="col-md-6">
                <h4>Total Expenses: <?php echo number_format($total_expenses, 2); ?> JPY</h4>

                <h5 class="mt-4">Expense List</h5>
                <?php if (empty($expenses)): ?>
                    <p>No expenses recorded yet.</p>
                <?php else: ?>
                    <ul class="list-group">
                        <?php foreach ($expenses as $expense): ?>
                            <li class="list-group-item bg-secondary text-light">
                                <?php echo htmlspecialchars($expense['description']); ?> -
                                <strong><?php echo number_format($expense['amount'], 2); ?> <?php echo htmlspecialchars($expense['currency']); ?></strong>
                                <br>
                                <small>Paid by: <?php echo htmlspecialchars($expense['payer_name']); ?>
                                <?php if ($expense['is_shared']): ?>
                                    (Shared)
                                <?php endif; ?>
                                </small>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
            <div class="col-md-6">
                <!-- Add Expense Form -->
                <div class="card bg-secondary text-light">
                    <div class="card-body">
                        <h5 class="card-title">Add New Expense</h5>
                        <form action="actions/add_expense.php" method="post">
                            <input type="hidden" name="trip_id" value="<?php echo $trip_id; ?>">
                            <div class="mb-3">
                                <label for="amount" class="form-label">Amount</label>
                                <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
                            </div>
                            <div class="mb-3">
                                <label for="currency" class="form-label">Currency</label>
                                <input type="text" class="form-control" id="currency" name="currency" value="JPY" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <input type="text" class="form-control" id="description" name="description" required>
                            </div>
                            <div class="mb-3">
                                <label for="payer_user_id" class="form-label">Paid By</label>
                                <select class="form-control" id="payer_user_id" name="payer_user_id" required>
                                    <option value="">Select a member...</option>
                                    <?php foreach ($trip_members as $member): ?>
                                        <option value="<?php echo $member['id']; ?>"><?php echo htmlspecialchars($member['username']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="is_shared" name="is_shared" value="1">
                                <label class="form-check-label" for="is_shared">
                                    This is a shared expense
                                </label>
                            </div>
                            <button type="submit" class="btn btn-primary">Add Expense</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Packing Lists Tab -->
    <div class="tab-pane fade" id="packing-lists" role="tabpanel" aria-labelledby="packing-lists-tab">
        <h3>Packing Lists</h3>

        <div class="row">
            <div class="col-md-6">
                <h4>Existing Lists</h4>
                <?php
                // Fetch packing lists for the trip
                $lists_stmt = $pdo->prepare('SELECT id, list_name FROM packing_lists WHERE trip_id = ?');
                $lists_stmt->execute([$trip_id]);
                $packing_lists = $lists_stmt->fetchAll();
                ?>

                <?php if (empty($packing_lists)): ?>
                    <p>No packing lists created yet.</p>
                <?php else: ?>
                    <?php foreach ($packing_lists as $list): ?>
                        <div class="card bg-secondary text-light mb-3">
                            <div class="card-header">
                                <?php echo htmlspecialchars($list['list_name']); ?>
                            </div>
                            <div class="card-body">
                                <?php
                                // Fetch items for each list
                                $items_stmt = $pdo->prepare('SELECT id, item_name, is_checked FROM packing_list_items WHERE list_id = ?');
                                $items_stmt->execute([$list['id']]);
                                $items = $items_stmt->fetchAll();
                                ?>
                                <ul class="list-group">
                                    <?php foreach ($items as $item): ?>
                                        <li class="list-group-item bg-dark text-light">
                                            <input class="form-check-input me-2" type="checkbox"
                                                   value="<?php echo $item['id']; ?>"
                                                   id="item_<?php echo $item['id']; ?>"
                                                   <?php echo $item['is_checked'] ? 'checked' : ''; ?>
                                                   onchange="togglePackedStatus(this)">
                                            <label class="form-check-label <?php echo $item['is_checked'] ? 'text-decoration-line-through' : ''; ?>"
                                                   for="item_<?php echo $item['id']; ?>">
                                                <?php echo htmlspecialchars($item['item_name']); ?>
                                            </label>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                                <!-- Add item to list form -->
                                <form action="actions/add_packing_list_item.php" method="post" class="mt-3">
                                    <input type="hidden" name="list_id" value="<?php echo $list['id']; ?>">
                                    <input type="hidden" name="trip_id" value="<?php echo $trip_id; ?>">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="item_name" placeholder="New item..." required>
                                        <button class="btn btn-primary" type="submit">Add</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="col-md-6">
                <!-- Create New Packing List Form -->
                <div class="card bg-secondary text-light">
                    <div class="card-body">
                        <h5 class="card-title">Create New Packing List</h5>
                        <form action="actions/add_packing_list.php" method="post">
                            <input type="hidden" name="trip_id" value="<?php echo $trip_id; ?>">
                            <div class="mb-3">
                                <label for="list_name" class="form-label">List Name</label>
                                <input type="text" class="form-control" id="list_name" name="list_name" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Create List</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePackedStatus(checkbox) {
    const itemId = checkbox.value;
    const isChecked = checkbox.checked;

    fetch('actions/toggle_packing_item.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `item_id=${itemId}&is_checked=${isChecked ? 1 : 0}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const label = document.querySelector(`label[for="item_${itemId}"]`);
            if (isChecked) {
                label.classList.add('text-decoration-line-through');
            } else {
                label.classList.remove('text-decoration-line-through');
            }
        } else {
            // Optionally handle error
            alert('Failed to update status.');
            checkbox.checked = !isChecked; // Revert change
        }
    });
}
</script>

<?php include 'includes/footer.php'; ?>
