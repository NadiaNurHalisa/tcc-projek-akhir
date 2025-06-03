<?php
require_once 'config.php';

if (isLoggedIn()) {
    redirect('index.php');
}

$error = '';
$success = '';

if ($_POST) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $email = $_POST['email'] ?? '';
    $no_hp = $_POST['no_hp'] ?? '';
    $registerType = $_POST['register_type'] ?? 'user';

    if ($username && $password && $email) {
        $data = [
            'username' => $username,
            'password' => $password,
            'email' => $email
        ];

        if ($registerType === 'admin' && $no_hp) {
            $data['no_hp'] = $no_hp;
        }

        $endpoint = $registerType === 'admin' ? '/auth/register-admin' : '/auth/register';
        $response = makeApiCall($endpoint, 'POST', $data);

        if ($response['code'] === 200 || $response['code'] === 201) {
            $success = 'Pendaftaran berhasil! Anda sekarang dapat login.';
        } else {
            $error = $response['data']['message'] ?? 'Pendaftaran gagal';
        }
    } else {
        $error = 'Silakan lengkapi semua kolom yang diperlukan';
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Sistem Manajemen Kos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Daftar</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                        <?php endif; ?>

                        <form method="POST">

                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="mb-3" id="phone-field" style="display: none;">
                                <label for="no_hp" class="form-label">Nomor Telepon</label>
                                <input type="text" class="form-control" id="no_hp" name="no_hp">
                            </div>
                            <div class="mb-3">
                                <label for="register_type" class="form-label">Daftar sebagai</label>
                                <select class="form-select" id="register_type" name="register_type"
                                    onchange="togglePhoneField()">
                                    <option value="user">Pengguna</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Daftar</button>
                        </form>

                        <div class="text-center mt-3">
                            <p>Sudah punya akun? <a href="login.php">Masuk di sini</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePhoneField() {
            const registerType = document.getElementById('register_type').value;
            const phoneField = document.getElementById('phone-field');
            if (registerType === 'admin') {
                phoneField.style.display = 'block';
                document.getElementById('no_hp').required = true;
            } else {
                phoneField.style.display = 'none';
                document.getElementById('no_hp').required = false;
            }
        }
    </script>
</body>

</html>