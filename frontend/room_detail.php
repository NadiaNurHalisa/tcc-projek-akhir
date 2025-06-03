<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$error = '';
$success = '';
$room = null;
$room_id = $_GET['id'] ?? null;

if (!$room_id) {
    redirect('rooms.php');
}

// Get room details
$response = makeApiCall('/rooms/' . $room_id, 'GET', null, $_SESSION['token']);
if ($response['code'] === 200) {
    $room = $response['data'];
} else {
    $error = 'Kamar tidak ditemukan';
}

if (!$room) {
    redirect('rooms.php');
}

// Parse facilities from JSON string if needed
$facilities = [];
if (isset($room['facilities'])) {
    $facilities_decoded = json_decode($room['facilities'], true);
    if (is_array($facilities_decoded)) {
        $facilities = $facilities_decoded;
    }
}

// Handle room update
if ($_POST && isset($_POST['action']) && $_POST['action'] === 'update') {
    $data = [
        'name' => $_POST['name'],
        'description' => $_POST['description'],
        'price' => intval($_POST['price']),
        'facilities' => $_POST['facilities'],
        'status' => $_POST['status'],
        'image_url' => $_POST['image_url']
    ];
    
    $response = makeApiCall('/rooms/' . $room_id, 'PUT', $data, $_SESSION['token']);
    if ($response['code'] === 200) {
        $success = 'Kamar berhasil di-Update!';
        // Refresh room data
        $room_response = makeApiCall('/rooms/' . $room_id, 'GET', null, $_SESSION['token']);
        if ($room_response['code'] === 200) {
            $room = $room_response['data'];
        }
    } else {
        $error = $response['data']['message'] ?? 'Gagal meng-Update kamar';
    }
}

// Get status badge color
function getStatusBadgeClass($status) {
    switch (strtolower($status)) {
        case 'available':
            return 'bg-success';
        case 'occupied':
            return 'bg-danger';
        case 'maintenance':
            return 'bg-warning';
        default:
            return 'bg-secondary';
    }
}

// Get status text in Indonesian
function getStatusText($status) {
    switch (strtolower($status)) {
        case 'available':
            return 'Tersedia';
        case 'occupied':
            return 'Terisi';
        case 'maintenance':
            return 'Dalam Perawatan';
        default:
            return ucfirst($status);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Kamar - <?php echo htmlspecialchars($room['name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">Manajemen Kos</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="rooms.php">Kamar</a>
                <a class="nav-link" href="bookings.php">Pemesanan</a>
                <a class="nav-link" href="index.php">Dashboard</a>
                <a class="nav-link" href="logout.php">Keluar</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="rooms.php">Kamar</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($room['name']); ?></li>
                    </ol>
                </nav>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h2 class="card-title mb-0"><?php echo htmlspecialchars($room['name']); ?></h2>
                                    <span class="badge <?php echo getStatusBadgeClass($room['status']); ?> fs-6">
                                        <?php echo getStatusText($room['status']); ?>
                                    </span>
                                </div>

                                <?php if ($room['image_url']): ?>
                                    <div class="mb-4">
                                        <img src="<?php echo htmlspecialchars($room['image_url']); ?>" 
                                             class="img-fluid rounded" 
                                             alt="Gambar Kamar"
                                             style="width: 100%; height: 400px; object-fit: cover;">
                                    </div>
                                <?php endif; ?>

                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <h5><i class="bi bi-info-circle"></i> Deskripsi</h5>
                                        <p class="text-muted"><?php echo nl2br(htmlspecialchars($room['description'])); ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <h5><i class="bi bi-currency-dollar"></i> Harga</h5>
                                        <p class="h4 text-primary">Rp <?php echo number_format($room['price']); ?></p>
                                        <small class="text-muted">per malam</small>
                                    </div>
                                </div>

                                <?php if (!empty($facilities)): ?>
                                    <div class="mb-4">
                                        <h5><i class="bi bi-gear"></i> Fasilitas</h5>
                                        <div class="row">
                                            <?php foreach ($facilities as $facility): ?>
                                                <div class="col-md-4 mb-2">
                                                    <span class="badge bg-light text-dark border">
                                                        <i class="bi bi-check-circle text-success"></i>
                                                        <?php echo htmlspecialchars($facility); ?>
                                                    </span>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <div class="mb-3">
                                    <h6><i class="bi bi-calendar"></i> Informasi Kamar</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <small class="text-muted">Dibuat:</small><br>
                                            <span><?php echo date('d/m/Y H:i', strtotime($room['created_at'])); ?></span>
                                        </div>
                                        <div class="col-md-6">
                                            <small class="text-muted">Terakhir Update:</small><br>
                                            <span><?php echo date('d/m/Y H:i', strtotime($room['updated_at'])); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Aksi</h5>
                            </div>
                            <div class="card-body">
                                <?php if ($room['status'] === 'available'): ?>
                                    <a href="booking_form.php?room_id=<?php echo $room['id']; ?>" 
                                       class="btn btn-primary btn-lg w-100 mb-3">
                                        <i class="bi bi-calendar-plus"></i> Pesan Kamar Ini
                                    </a>
                                <?php elseif ($room['status'] === 'occupied'): ?>
                                    <button class="btn btn-danger btn-lg w-100 mb-3" disabled>
                                        <i class="bi bi-x-circle"></i> Kamar Sedang Terisi
                                    </button>
                                <?php elseif ($room['status'] === 'maintenance'): ?>
                                    <button class="btn btn-warning btn-lg w-100 mb-3" disabled>
                                        <i class="bi bi-tools"></i> Kamar Dalam Perawatan
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-secondary btn-lg w-100 mb-3" disabled>
                                        <i class="bi bi-x-circle"></i> Kamar Tidak Tersedia
                                    </button>
                                <?php endif; ?>

                                <?php if (isAdmin()): ?>
                                    <button class="btn btn-warning w-100 mb-2" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editRoomModal">
                                        <i class="bi bi-pencil"></i> Update Kamar
                                    </button>
                                    
                                    <a href="rooms.php" class="btn btn-outline-secondary w-100">
                                        <i class="bi bi-arrow-left"></i> Kembali ke Daftar Kamar
                                    </a>
                                <?php else: ?>
                                    <a href="rooms.php" class="btn btn-outline-primary w-100">
                                        <i class="bi bi-arrow-left"></i> Kembali ke Daftar Kamar
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="card mt-3">
                            <div class="card-header">
                                <h6 class="card-title mb-0">Ringkasan Harga</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <span>Harga per malam:</span>
                                    <strong>Rp <?php echo number_format($room['price']); ?></strong>
                                </div>
                                <hr>
                                <?php if ($room['status'] === 'available'): ?>
                                    <small class="text-muted">
                                        * Harga final akan dihitung berdasarkan jumlah malam menginap
                                    </small>
                                <?php else: ?>
                                    <small class="text-danger">
                                        * Kamar saat ini tidak tersedia untuk pemesanan
                                    </small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if (isAdmin()): ?>
    <!-- Edit Room Modal -->
    <div class="modal fade" id="editRoomModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Kamar: <?php echo htmlspecialchars($room['name']); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nama Kamar</label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="<?php echo htmlspecialchars($room['name']); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="price" class="form-label">Harga per Malam</label>
                                    <input type="number" class="form-control" id="price" name="price" 
                                           value="<?php echo $room['price']; ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="description" name="description" 
                                      rows="4" required><?php echo htmlspecialchars($room['description']); ?></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="available" <?php echo $room['status'] === 'available' ? 'selected' : ''; ?>>Tersedia</option>
                                        <option value="occupied" <?php echo $room['status'] === 'occupied' ? 'selected' : ''; ?>>Terisi</option>
                                        <option value="maintenance" <?php echo $room['status'] === 'maintenance' ? 'selected' : ''; ?>>Dalam Perawatan</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="facilities" class="form-label">Fasilitas (format JSON)</label>
                                    <input type="text" class="form-control" id="facilities" name="facilities" 
                                           value="<?php echo htmlspecialchars($room['facilities']); ?>" 
                                           placeholder='["AC", "WiFi", "TV"]' required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="image_url" class="form-label">URL Gambar</label>
                            <input type="url" class="form-control" id="image_url" name="image_url" 
                                   value="<?php echo htmlspecialchars($room['image_url'] ?? ''); ?>">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Update Kamar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
