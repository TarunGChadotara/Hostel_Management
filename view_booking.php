<?php
require 'config.php';

if (!isset($_SESSION['user']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Get booking details (updated query without email)
$stmt = $pdo->prepare("
    SELECT b.*, u.username, h.name as hostel_name, h.location, h.price, h.image_url
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    JOIN hostels h ON b.hostel_id = h.id
    WHERE b.id = ?
");
$stmt->execute([$_GET['id']]);
$booking = $stmt->fetch();

if (!$booking) {
    header("Location: bookings.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Booking Details</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="container header-content">
            <div class="logo">Hostel Management</div>
            <nav>
                <ul>
                    <li><a href="admin_panel.php">Dashboard</a></li>
                    <li><a href="users.php">Manage Users</a></li>
                    <li><a href="hostels.php">Manage Hostels</a></li>
                    <li><a href="bookings.php">View Bookings</a></li>
                    <li><a href="reports.php">View Reports</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <h2>Booking Details #<?= $booking['id'] ?></h2>
        
        <div class="booking-details">
            <div class="detail-card">
                <h3>User Information</h3>
                <p><strong>Username:</strong> <?= htmlspecialchars($booking['username']) ?></p>
            </div>
            
            <div class="detail-card">
                <h3>Hostel Information</h3>
                <div class="hostel-img" style="background-image: url('<?= htmlspecialchars($booking['image_url']) ?>')"></div>
                <p><strong>Hostel:</strong> <?= htmlspecialchars($booking['hostel_name']) ?></p>
                <p><strong>Location:</strong> <?= htmlspecialchars($booking['location']) ?></p>
                <p><strong>Price per night:</strong> $<?= number_format($booking['price'], 2) ?></p>
            </div>
            
            <div class="detail-card">
                <h3>Booking Dates</h3>
                <p><strong>Check-in:</strong> <?= date('F j, Y', strtotime($booking['check_in'])) ?></p>
                <p><strong>Check-out:</strong> <?= date('F j, Y', strtotime($booking['check_out'])) ?></p>
                <p><strong>Nights:</strong> <?= 
                    round((strtotime($booking['check_out']) - strtotime($booking['check_in'])) / (60 * 60 * 24)) 
                ?></p>
            </div>
            
            <div class="detail-card">
                <h3>Status & Payment</h3>
                <p><strong>Status:</strong> <span class="status-<?= $booking['status'] ?>">
                    <?= ucfirst($booking['status']) ?>
                </span></p>
                <p><strong>Total Price:</strong> $<?= 
                    number_format($booking['price'] * 
                    round((strtotime($booking['check_out']) - strtotime($booking['check_in'])) / (60 * 60 * 24)), 2) 
                ?></p>
                <p><strong>Booked on:</strong> <?= date('F j, Y g:i a', strtotime($booking['created_at'])) ?></p>
            </div>
        </div>
        
        <div class="actions">
            <a href="bookings.php" class="btn">Back to Bookings</a>
            <?php if ($booking['status'] != 'cancelled'): ?>
                <a href="cancel_booking.php?id=<?= $booking['id'] ?>" class="btn btn-danger">Cancel Booking</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>