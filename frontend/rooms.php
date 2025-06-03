<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$error = '';
$success = '';
$rooms = [];

// Handle form submissions
if ($_POST) {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                if (isAdmin()) {
                    $data = [
                        'name' => $_POST['name'],
                        'description' => $_POST['description'],
                        'price' => intval($_POST['price']),
                        'facilities' => $_POST['facilities'],
                        'status' => $_POST['status'],
                        'image_url' => $_POST['image_url']
                    ];
                    
                    $response = makeApiCall('/rooms', 'POST', $data, $_SESSION['token']);
                    if ($response['code'] === 200 || $response['code'] === 201) {
                        $success = 'Kamar berhasil dibuat!';
                    } else {
                        $error = $response['data']['message'] ?? 'Gagal membuat kamar';
                    }
                }
                break;
                
            case 'update':
                // Allow all logged in users to update rooms
                if (isset($_POST['room_id'])) {
                    $data = [
                        'name' => $_POST['name'],
                        'description' => $_POST['description'],
                        'price' => intval($_POST['price']),
                        'facilities' => $_POST['facilities'],
                        'status' => $_POST['status'],
                        'image_url' => $_POST['image_url']
                    ];
                    
                    $response = makeApiCall('/rooms/' . $_POST['room_id'], 'PUT', $data, $_SESSION['token']);
                    if ($response['code'] === 200) {
                        $success = 'Kamar berhasil di-Update!';
                    } else {
                        $error = $response['data']['message'] ?? 'Gagal meng-Update kamar';
                    }
                }
                break;
                
            case 'delete':
                if (isAdmin() && isset($_POST['room_id'])) {
                    $response = makeApiCall('/rooms/' . $_POST['room_id'], 'DELETE', null, $_SESSION['token']);
                    if ($response['code'] === 200) {
                        $success = 'Kamar berhasil dihapus!';
                    } else {
                        $error = $response['data']['message'] ?? 'Gagal menghapus kamar';
                    }
                }
                break;
        }
    }
}

// Get all rooms
$response = makeApiCall('/rooms', 'GET', null, $_SESSION['token']);
if ($response['code'] === 200) {
    $rooms = $response['data']['result'] ?? $response['data'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kamar - Sistem Manajemen Kos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">Manajemen Kos</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="index.php">Dashboard</a>
                <a class="nav-link" href="logout.php">Keluar</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h1>Kamar</h1>
                    <?php if (isAdmin()): ?>
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createRoomModal">Tambah Kamar Baru</button>
                    <?php endif; ?>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>

                <div class="row">
                    <?php foreach ($rooms as $room): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card">
                                <?php if ($room['image_url']): ?>
                                    <img src="<?php echo htmlspecialchars($room['image_url']); ?>" class="card-img-top" alt="Gambar Kamar" style="height: 200px; object-fit: cover;">
                                <?php endif; ?>
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($room['name']); ?></h5>
                                    <p class="card-text"><?php echo htmlspecialchars(substr($room['description'], 0, 100)); ?><?php echo strlen($room['description']) > 100 ? '...' : ''; ?></p>
                                    <p class="card-text">
                                        <small class="text-muted">
                                            Harga: Rp <?php echo number_format($room['price']); ?>/malam<br>
                                            Status: <span class="badge bg-<?php echo $room['status'] === 'available' ? 'success' : 'warning'; ?>">
                                                <?php echo ucfirst($room['status']); ?>
                                            </span>
                                        </small>
                                    </p>
                                    <div class="d-flex justify-content-between flex-wrap gap-1">
                                        <a href="room_detail.php?id=<?php echo $room['id']; ?>" class="btn btn-info btn-sm">Detail</a>
                                        <?php if ($room['status'] === 'available'): ?>
                                            <a href="booking_form.php?room_id=<?php echo $room['id']; ?>" class="btn btn-primary btn-sm">Pesan Sekarang</a>
                                        <?php else: ?>
                                            <button class="btn btn-secondary btn-sm" disabled>
                                                <?php if ($room['status'] === 'occupied'): ?>
                                                    Terisi
                                                <?php elseif ($room['status'] === 'maintenance'): ?>
                                                    Perawatan
                                                <?php else: ?>
                                                    Tidak Tersedia
                                                <?php endif; ?>
                                            </button>
                                        <?php endif; ?>
                                        <?php if (isAdmin()): ?>
                                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editRoomModal<?php echo $room['id']; ?>">Update</button>
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Anda yakin ingin menghapus kamar ini?')">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="room_id" value="<?php echo $room['id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Edit Room Modal for each room -->
                        <div class="modal fade" id="editRoomModal<?php echo $room['id']; ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Kamar: <?php echo htmlspecialchars($room['name']); ?></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST">
                                        <div class="modal-body">
                                            <input type="hidden" name="action" value="update">
                                            <input type="hidden" name="room_id" value="<?php echo $room['id']; ?>">
                                            <div class="mb-3">
                                                <label for="name<?php echo $room['id']; ?>" class="form-label">Nama Kamar</label>
                                                <input type="text" class="form-control" id="name<?php echo $room['id']; ?>" name="name" value="<?php echo htmlspecialchars($room['name']); ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="description<?php echo $room['id']; ?>" class="form-label">Deskripsi</label>
                                                <textarea class="form-control" id="description<?php echo $room['id']; ?>" name="description" rows="3" required><?php echo htmlspecialchars($room['description']); ?></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label for="price<?php echo $room['id']; ?>" class="form-label">Harga per Malam</label>
                                                <input type="number" class="form-control" id="price<?php echo $room['id']; ?>" name="price" value="<?php echo $room['price']; ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="facilities<?php echo $room['id']; ?>" class="form-label">Fasilitas (format JSON)</label>
                                                <input type="text" class="form-control" id="facilities<?php echo $room['id']; ?>" name="facilities" value="<?php echo htmlspecialchars($room['facilities']); ?>" placeholder='["AC", "WiFi", "TV"]' required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="status<?php echo $room['id']; ?>" class="form-label">Status</label>
                                                <select class="form-select" id="status<?php echo $room['id']; ?>" name="status" required>
                                                    <option value="available" <?php echo $room['status'] === 'available' ? 'selected' : ''; ?>>Tersedia</option>
                                                    <option value="occupied" <?php echo $room['status'] === 'occupied' ? 'selected' : ''; ?>>Terisi</option>
                                                    <option value="maintenance" <?php echo $room['status'] === 'maintenance' ? 'selected' : ''; ?>>Dalam Perawatan</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="image_url<?php echo $room['id']; ?>" class="form-label">URL Gambar</label>
                                                <input type="url" class="form-control" id="image_url<?php echo $room['id']; ?>" name="image_url" value="<?php echo htmlspecialchars($room['image_url'] ?? ''); ?>">
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
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <?php if (isAdmin()): ?>
    <!-- Create Room Modal -->
    <div class="modal fade" id="createRoomModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Kamar Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="create">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Kamar</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="price" class="form-label">Harga per Malam</label>
                            <input type="number" class="form-control" id="price" name="price" required>
                        </div>
                        <div class="mb-3">
                            <label for="facilities" class="form-label">Fasilitas (format JSON)</label>
                            <input type="text" class="form-control" id="facilities" name="facilities" placeholder='["AC", "WiFi", "TV"]' required>
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="available">Tersedia</option>
                                <option value="occupied">Terisi</option>
                                <option value="maintenance">Dalam Perawatan</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="image_url" class="form-label">URL Gambar</label>
                            <input type="url" class="form-control" id="image_url" name="image_url">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Tambah Kamar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
