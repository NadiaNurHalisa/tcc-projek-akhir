<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Manajemen Kos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">Manajemen Kos</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="profile.php">Profil Saya</a>
                <span class="navbar-text me-3">Selamat Datang, <?php echo htmlspecialchars($user['username']); ?></span>
                <a class="nav-link" href="logout.php">Keluar</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <h1>Dashboard</h1>
                <div class="alert alert-info">
                    <strong>Fitur Terbaru:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Semua pengguna dapat memesan kamar</li>
                        <?php if (isAdmin()): ?>
                            <li>Admin dapat meng-Update status dan catatan pemesanan</li>
                        <?php endif; ?>
                        <li>Manajemen profil telah di-Update</li>
                    </ul>
                </div>
                <div class="row mt-4">
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Kamar</h5>
                                <p class="card-text">Lihat dan kelola kamar kos. Semua pengguna sekarang dapat mengedit detail kamar!</p>
                                <a href="rooms.php" class="btn btn-primary">Lihat Kamar</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Pemesanan</h5>
                                <p class="card-text">Kelola pemesanan Anda<?php echo isAdmin() ? ' dan Update status pemesanan' : ''; ?></p>
                                <a href="bookings.php" class="btn btn-primary">Lihat Pemesanan</a>
                            </div>
                        </div>
                    </div>
                    <?php if (isAdmin()): ?>
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Pengguna</h5>
                                <p class="card-text">Kelola pengguna sistem</p>
                                <a href="users.php" class="btn btn-primary">Lihat Pengguna</a>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
