<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$error = '';
$success = '';
$room = null;
$room_id = $_GET['room_id'] ?? null;

if ($room_id) {
    $response = makeApiCall('/rooms/' . $room_id, 'GET', null, $_SESSION['token']);
    if ($response['code'] === 200) {
        $room = $response['data'];
        
        // Check if room is available for booking
        if ($room['status'] !== 'available') {
            $status_text = '';
            switch ($room['status']) {
                case 'occupied':
                    $status_text = 'terisi';
                    break;
                case 'maintenance':
                    $status_text = 'dalam perawatan';
                    break;
                default:
                    $status_text = 'tidak tersedia';
            }
            $error = "Maaf, kamar ini sedang $status_text dan tidak dapat dipesan saat ini.";
        }
    }
}

if (!$room) {
    redirect('rooms.php');
}

if ($_POST) {
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';
    $notes = $_POST['notes'] ?? '';
    
    // Validate dates
    if ($start_date && $end_date) {
        $start_timestamp = strtotime($start_date);
        $end_timestamp = strtotime($end_date);
        $current_timestamp = time();
        
        // Check if dates are valid
        if ($start_timestamp >= $end_timestamp) {
            $error = 'Check-out date must be after check-in date';
        } elseif ($start_timestamp < $current_timestamp) {
            $error = 'Check-in date cannot be in the past';
        } else {
            // Calculate total price
            $days = ($end_timestamp - $start_timestamp) / (60 * 60 * 24);
            $total_price = $days * floatval($room['price']);
            
            $data = [
                'user_id' => intval($_SESSION['user']['id']),
                'room_id' => intval($room_id),
                'start_date' => $start_date,
                'end_date' => $end_date,
                'status' => 'pending',
                'total_price' => $total_price,
                'notes' => $notes
            ];
            
            $response = makeApiCall('/bookings', 'POST', $data, $_SESSION['token']);
            
            // Handle different response codes
            if ($response['code'] === 200 || $response['code'] === 201) {
                $success = 'Booking created successfully! Your booking is pending confirmation.';
            } elseif ($response['code'] === 400) {
                $error = $response['data']['message'] ?? 'Invalid booking data. Please check your input.';
            } elseif ($response['code'] === 401) {
                $error = 'Authentication failed. Please login again.';
                session_destroy();
            } elseif ($response['code'] === 403) {
                $error = 'You do not have permission to create bookings.';
            } else {
                $error = $response['data']['message'] ?? 'Failed to create booking. Please try again.';
            }
        }
    } else {
        $error = 'Please fill in all required fields';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan Kamar - Sistem Manajemen Kos</title>
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
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Book Room: <?php echo htmlspecialchars($room['name']); ?></h4>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>
                        <?php if ($success): ?>
                            <div class="alert alert-success">
                                <?php echo htmlspecialchars($success); ?>
                                <br><a href="bookings.php">View your bookings</a>
                            </div>
                        <?php endif; ?>

                        <?php if ($room['status'] !== 'available'): ?>
                            <div class="alert alert-warning">
                                <strong>Peringatan:</strong> Kamar ini sedang 
                                <?php 
                                switch ($room['status']) {
                                    case 'occupied': echo 'terisi'; break;
                                    case 'maintenance': echo 'dalam perawatan'; break;
                                    default: echo 'tidak tersedia';
                                }
                                ?> 
                                dan tidak dapat dipesan saat ini.
                                <br>
                                <a href="rooms.php" class="btn btn-primary btn-sm mt-2">Lihat Kamar Lain</a>
                            </div>
                        <?php endif; ?>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <?php if ($room['image_url']): ?>
                                    <img src="<?php echo htmlspecialchars($room['image_url']); ?>" class="img-fluid rounded" alt="Gambar Kamar">
                                <?php endif; ?>
                            </div>
                            <div class="col-md-8">
                                <h5><?php echo htmlspecialchars($room['name']); ?></h5>
                                <p><?php echo htmlspecialchars($room['description']); ?></p>
                                <p><strong>Harga:</strong> Rp <?php echo number_format($room['price']); ?> per malam</p>
                                <p><strong>Status:</strong> 
                                    <span class="badge bg-<?php echo $room['status'] === 'available' ? 'success' : ($room['status'] === 'occupied' ? 'danger' : 'warning'); ?>">
                                        <?php 
                                        switch ($room['status']) {
                                            case 'available': echo 'Tersedia'; break;
                                            case 'occupied': echo 'Terisi'; break;
                                            case 'maintenance': echo 'Dalam Perawatan'; break;
                                            default: echo ucfirst($room['status']);
                                        }
                                        ?>
                                    </span>
                                </p>
                            </div>
                        </div>

                        <?php if ($room['status'] === 'available'): ?>
                        <form method="POST" id="bookingForm">
                            <div class="mb-3">
                                <label for="start_date" class="form-label">Check-in Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" 
                                       min="<?php echo date('Y-m-d'); ?>" 
                                       value="<?php echo $_POST['start_date'] ?? ''; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="end_date" class="form-label">Check-out Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" 
                                       min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>"
                                       value="<?php echo $_POST['end_date'] ?? ''; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="notes" class="form-label">Special Requests (Optional)</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3" 
                                          placeholder="Any special requests or notes..."><?php echo htmlspecialchars($_POST['notes'] ?? ''); ?></textarea>
                            </div>
                            <div class="mb-3" id="priceCalculation" style="display: none;">
                                <div class="alert alert-info">
                                    <strong>Price Calculation:</strong>
                                    <div id="calculationDetails"></div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <a href="room_detail.php?id=<?php echo $room_id; ?>" class="btn btn-secondary">Kembali ke Detail Kamar</a>
                                <button type="submit" class="btn btn-primary" id="submitBtn">Buat Pemesanan</button>
                            </div>
                        </form>
                        <?php else: ?>
                        <div class="text-center">
                            <div class="alert alert-info">
                                <h5>Tidak Dapat Memesan Kamar Ini</h5>
                                <p>Kamar ini sedang tidak tersedia untuk pemesanan.</p>
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="room_detail.php?id=<?php echo $room_id; ?>" class="btn btn-secondary">Kembali ke Detail Kamar</a>
                                    <a href="rooms.php" class="btn btn-primary">Lihat Kamar Lain</a>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Calculate price when dates change
        function calculatePrice() {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            const pricePerNight = <?php echo $room['price']; ?>;
            
            if (startDate && endDate) {
                const start = new Date(startDate);
                const end = new Date(endDate);
                const diffTime = Math.abs(end - start);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                
                if (diffDays > 0) {
                    const totalPrice = diffDays * pricePerNight;
                    document.getElementById('calculationDetails').innerHTML = 
                        `${diffDays} night(s) × Rp ${pricePerNight.toLocaleString()} = Rp ${totalPrice.toLocaleString()}`;
                    document.getElementById('priceCalculation').style.display = 'block';
                } else {
                    document.getElementById('priceCalculation').style.display = 'none';
                }
            } else {
                document.getElementById('priceCalculation').style.display = 'none';
            }
        }
        
        // Update end date minimum when start date changes
        document.getElementById('start_date').addEventListener('change', function() {
            const startDate = this.value;
            if (startDate) {
                const nextDay = new Date(startDate);
                nextDay.setDate(nextDay.getDate() + 1);
                document.getElementById('end_date').min = nextDay.toISOString().split('T')[0];
            }
            calculatePrice();
        });
        
        document.getElementById('end_date').addEventListener('change', calculatePrice);
        
        // Form validation
        document.getElementById('bookingForm').addEventListener('submit', function(e) {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            
            if (startDate && endDate && new Date(startDate) >= new Date(endDate)) {
                e.preventDefault();
                alert('Check-out date must be after check-in date');
                return;
            }
        });
    </script>
</body>
</html>
