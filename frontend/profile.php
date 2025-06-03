<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$error = '';
$success = '';
$user = $_SESSION['user'];

// Handle profile update
if ($_POST && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_profile') {
        $data = [
            'username' => $_POST['username'],
            'email' => $_POST['email']
        ];
        
        $response = makeApiCall('/users/' . $user['id'], 'PUT', $data, $_SESSION['token']);
        if ($response['code'] === 200) {
            $success = 'Profile updated successfully!';
            // Update session data
            $_SESSION['user']['username'] = $_POST['username'];
            $_SESSION['user']['email'] = $_POST['email'];
            $user = $_SESSION['user'];
        } else {
            $error = $response['data']['message'] ?? 'Failed to update profile';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Hotel Booking System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">Hotel Booking</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="rooms.php">Rooms</a>
                <a class="nav-link" href="bookings.php">Bookings</a>
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
                        <h4 class="mb-0">My Profile</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <input type="hidden" name="action" value="update_profile">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Role</label>
                                <input type="text" class="form-control" value="<?php echo ucfirst($user['role']); ?>" readonly>
                            </div>
                            <?php if (isset($user['no_hp'])): ?>
                            <div class="mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['no_hp'] ?? 'Not provided'); ?>" readonly>
                            </div>
                            <?php endif; ?>
                            <div class="d-flex justify-content-between">
                                <a href="index.php" class="btn btn-secondary">Back to Dashboard</a>
                                <button type="submit" class="btn btn-primary">Update Profile</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
