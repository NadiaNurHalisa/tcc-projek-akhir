<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$error = '';
$success = '';
$bookings = [];

// Handle booking actions
if ($_POST && isset($_POST['action'])) {
    if ($_POST['action'] === 'delete' && isset($_POST['booking_id'])) {
        $response = makeApiCall('/bookings/' . $_POST['booking_id'], 'DELETE', null, $_SESSION['token']);
        if ($response['code'] === 200) {
            $success = 'Booking deleted successfully!';
        } else {
            $error = $response['data']['message'] ?? 'Failed to delete booking';
        }
    }
    // Add update booking functionality
    elseif ($_POST['action'] === 'update' && isset($_POST['booking_id']) && isAdmin()) {
        $data = [
            'status' => $_POST['status']
        ];
        
        if (!empty($_POST['notes'])) {
            $data['notes'] = $_POST['notes'];
        }
        
        $response = makeApiCall('/bookings/' . $_POST['booking_id'], 'PUT', $data, $_SESSION['token']);
        if ($response['code'] === 200) {
            $success = 'Booking updated successfully!';
        } else {
            $error = $response['data']['message'] ?? 'Failed to update booking';
        }
    }
}

// Get bookings based on user role
if (isAdmin()) {
    $response = makeApiCall('/bookings', 'GET', null, $_SESSION['token']);
} else {
    $response = makeApiCall('/bookings/user/' . $_SESSION['user']['id'], 'GET', null, $_SESSION['token']);
}

if ($response['code'] === 200) {
    $bookings = $response['data'];
    if (!is_array($bookings)) {
        $bookings = [];
    }
}

// Function to get badge color for booking status
function getStatusBadgeColor($status) {
    switch (strtolower($status)) {
        case 'confirmed':
            return 'success';
        case 'pending':
            return 'warning';
        case 'completed':
            return 'info';
        case 'cancelled':
            return 'danger';
        default:
            return 'secondary';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookings - Hotel Booking System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">Hotel Booking</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="rooms.php">Rooms</a>
                <a class="nav-link" href="index.php">Dashboard</a>
                <a class="nav-link" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <h1><?php echo isAdmin() ? 'All Bookings' : 'My Bookings'; ?></h1>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>

                <?php if (empty($bookings)): ?>
                    <div class="alert alert-info">
                        No bookings found. <a href="rooms.php">Browse rooms to make a booking</a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <?php if (isAdmin()): ?>
                                        <th>User</th>
                                    <?php endif; ?>
                                    <th>Room</th>
                                    <th>Check-in</th>
                                    <th>Check-out</th>
                                    <th>Status</th>
                                    <th>Total Price</th>
                                    <th>Notes</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($bookings as $booking): ?>
                                    <tr>
                                        <td><?php echo $booking['id']; ?></td>
                                        <?php if (isAdmin()): ?>
                                            <td>
                                                <?php 
                                                echo isset($booking['user']) 
                                                    ? htmlspecialchars($booking['user']['username']) 
                                                    : 'User ID: ' . $booking['user_id']; 
                                                ?>
                                            </td>
                                        <?php endif; ?>
                                        <td>
                                            <?php 
                                            echo isset($booking['room']) 
                                                ? htmlspecialchars($booking['room']['name']) 
                                                : 'Room ID: ' . $booking['room_id']; 
                                            ?>
                                        </td>
                                        <td><?php echo $booking['start_date']; ?></td>
                                        <td><?php echo $booking['end_date']; ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo getStatusBadgeColor($booking['status']); ?>">
                                                <?php echo ucfirst($booking['status']); ?>
                                            </span>
                                        </td>
                                        <td>Rp <?php echo number_format($booking['total_price']); ?></td>
                                        <td><?php echo htmlspecialchars($booking['notes'] ?? ''); ?></td>
                                        <td>
                                            <?php if (isAdmin()): ?>
                                                <button class="btn btn-primary btn-sm me-1" data-bs-toggle="modal" data-bs-target="#editBookingModal<?php echo $booking['id']; ?>">
                                                    Edit
                                                </button>
                                                <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this booking?')">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                                </form>
                                            <?php else: ?>
                                                <?php if (strtolower($booking['status']) === 'pending'): ?>
                                                    <small class="text-muted">Awaiting confirmation</small>
                                                <?php else: ?>
                                                    <small class="text-muted">Status: <?php echo ucfirst($booking['status']); ?></small>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </td>
                                    </tr>

                                    <?php if (isAdmin()): ?>
                                    <!-- Edit Booking Modal -->
                                    <div class="modal fade" id="editBookingModal<?php echo $booking['id']; ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Booking #<?php echo $booking['id']; ?></h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form method="POST">
                                                    <div class="modal-body">
                                                        <input type="hidden" name="action" value="update">
                                                        <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                                        <div class="mb-3">
                                                            <label for="status<?php echo $booking['id']; ?>" class="form-label">Status</label>
                                                            <select class="form-select" id="status<?php echo $booking['id']; ?>" name="status" required>
                                                                <option value="pending" <?php echo $booking['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                                <option value="confirmed" <?php echo $booking['status'] === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                                                <option value="cancelled" <?php echo $booking['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                                                <option value="completed" <?php echo $booking['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="notes<?php echo $booking['id']; ?>" class="form-label">Admin Notes</label>
                                                            <textarea class="form-control" id="notes<?php echo $booking['id']; ?>" name="notes" rows="3" placeholder="Add notes about this booking..."><?php echo htmlspecialchars($booking['notes'] ?? ''); ?></textarea>
                                                        </div>
                                                        <div class="mb-3">
                                                            <div class="card bg-light">
                                                                <div class="card-body">
                                                                    <h6 class="card-title">Booking Details</h6>
                                                                    <p class="mb-1"><strong>Room:</strong> <?php echo isset($booking['room']) ? htmlspecialchars($booking['room']['name']) : 'Room ID: ' . $booking['room_id']; ?></p>
                                                                    <p class="mb-1"><strong>Guest:</strong> <?php echo isset($booking['user']) ? htmlspecialchars($booking['user']['username']) : 'User ID: ' . $booking['user_id']; ?></p>
                                                                    <p class="mb-1"><strong>Dates:</strong> <?php echo $booking['start_date']; ?> to <?php echo $booking['end_date']; ?></p>
                                                                    <p class="mb-1"><strong>Total Price:</strong> Rp <?php echo number_format($booking['total_price']); ?></p>
                                                                    <p class="mb-0"><strong>Current Status:</strong> 
                                                                        <span class="badge bg-<?php echo getStatusBadgeColor($booking['status']); ?>">
                                                                            <?php echo ucfirst($booking['status']); ?>
                                                                        </span>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary">Update Booking</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
