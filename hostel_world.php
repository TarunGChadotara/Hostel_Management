<?php
require 'config.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Get all hostels with sample data if empty
$stmt = $pdo->query("SELECT * FROM hostels");
$hostels = $stmt->fetchAll();

// If no hostels, insert sample data
if (empty($hostels)) {
    $sampleHostels = [
        [
            'name' => 'Mountain View Hostel',
            'location' => '123 Alpine Road, Mountain Town',
            'description' => 'Beautiful hostel with stunning mountain views',
            'price' => 25.00,
            'image_url' => 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80'
        ],
        [
            'name' => 'Forest Retreat',
            'location' => '456 Woodland Path, Green Valley',
            'description' => 'Eco-friendly hostel surrounded by nature',
            'price' => 30.00,
            'image_url' => 'https://images.unsplash.com/photo-1564501049412-61c2a3083791?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80'
        ],
        [
            'name' => 'Lakeside Lodge',
            'location' => '789 Waterside Drive, Lake District',
            'description' => 'Peaceful hostel by the lake with canoe rentals',
            'price' => 28.00,
            'image_url' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80'
        ]
    ];

    foreach ($sampleHostels as $hostel) {
        $stmt = $pdo->prepare("INSERT INTO hostels (name, location, description, price, image_url) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$hostel['name'], $hostel['location'], $hostel['description'], $hostel['price'], $hostel['image_url']]);
    }

    // Refresh hostels list
    $stmt = $pdo->query("SELECT * FROM hostels");
    $hostels = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Hostel World</title>
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
        <h1>Available Hostels</h1>
        
        <div class="hostel-grid">
            <?php foreach ($hostels as $hostel): ?>
            <div class="hostel-card">
                <div class="hostel-img" style="background-image: url('<?= htmlspecialchars($hostel['image_url']) ?>')"></div>
                <div class="hostel-info">
                    <h3><?= htmlspecialchars($hostel['name']) ?></h3>
                    <p><strong>Location:</strong> <?= htmlspecialchars($hostel['location']) ?></p>
                    <p><?= htmlspecialchars($hostel['description']) ?></p>
                    <p class="hostel-price">$<?= number_format($hostel['price'], 2) ?> / night</p>
                    <a href="book.php?id=<?= $hostel['id'] ?>" class="btn">Book Now</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>