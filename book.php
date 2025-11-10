<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $hostel_id = $_POST['hostel_id'];
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    
    $stmt = $pdo->prepare("INSERT INTO bookings (user_id, hostel_id, check_in, check_out) VALUES (?, ?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $hostel_id, $check_in, $check_out]);
    
    header("Location: my_bookings.php");
    exit;
}

// Get hostel details
$stmt = $pdo->prepare("SELECT * FROM hostels WHERE id = ?");
$stmt->execute([$_GET['id']]);
$hostel = $stmt->fetch();

if (!$hostel) {
    header("Location: hostel_world.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Book Hostel</title>
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <header>
        <div class="container header-content">
            <div class="logo">Hostel Management</div>
            <nav>
                <ul>
                    <li><a href="hostel_world.php">Hostels</a></li>
                    <li><a href="my_bookings.php">My Bookings</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="form-container">
            <h2>Book <?= htmlspecialchars($hostel['name']) ?></h2>
            
            <div class="hostel-card" style="margin-bottom: 2rem;">
                <div class="hostel-img" style="background-image: url('<?= htmlspecialchars($hostel['image_url']) ?>')"></div>
                <div class="hostel-info">
                    <h3><?= htmlspecialchars($hostel['name']) ?></h3>
                    <p><strong>Location:</strong> <?= htmlspecialchars($hostel['location']) ?></p>
                    <p class="hostel-price">$<?= number_format($hostel['price'], 2) ?> / night</p>
                </div>
            </div>
            
            <form method="POST" action="">
                <input type="hidden" name="hostel_id" value="<?= $hostel['id'] ?>">
                
                <div class="form-group">
                    <label>Check-in Date</label>
                    <input type="date" name="check_in" class="form-control" required min="<?= date('Y-m-d') ?>">
                </div>
                
                <div class="form-group">
                    <label>Check-out Date</label>
                    <input type="date" name="check_out" class="form-control" required min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
                </div>
                
                <button type="submit" class="btn">Confirm Booking</button>
                <a href="hostel_world.php" class="btn btn-outline">Cancel</a>
            </form>
        </div>
    </div>
</body>
</html>