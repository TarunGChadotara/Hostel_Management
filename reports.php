<?php
require 'config.php';

if (!isset($_SESSION['user']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Get report data
$revenue = $pdo->query("
    SELECT 
        SUM(h.price) as total_revenue,
        COUNT(*) as total_bookings,
        AVG(h.price) as avg_booking_value
    FROM bookings b
    JOIN hostels h ON b.hostel_id = h.id
    WHERE b.status = 'confirmed'
")->fetch();

$popular_hostels = $pdo->query("
    SELECT h.name, COUNT(b.id) as bookings_count
    FROM hostels h
    LEFT JOIN bookings b ON h.id = b.hostel_id
    GROUP BY h.id
    ORDER BY bookings_count DESC
    LIMIT 5
")->fetchAll();

$monthly_revenue = $pdo->query("
    SELECT 
        DATE_FORMAT(b.created_at, '%Y-%m') as month,
        SUM(h.price) as revenue
    FROM bookings b
    JOIN hostels h ON b.hostel_id = h.id
    WHERE b.status = 'confirmed'
    GROUP BY month
    ORDER BY month DESC
    LIMIT 6
")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Reports</title>
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
        <h2>System Reports</h2>
        
        <div class="grid">
            <!-- Revenue Summary -->
            <div class="card">
                <h3>Revenue Summary</h3>
                <div class="stats">
                    <p>Total Revenue: <strong>$<?= number_format($revenue['total_revenue'] ?? 0, 2) ?></strong></p>
                    <p>Total Bookings: <strong><?= $revenue['total_bookings'] ?? 0 ?></strong></p>
                    <p>Avg. Booking Value: <strong>$<?= number_format($revenue['avg_booking_value'] ?? 0, 2) ?></strong></p>
                </div>
            </div>
            
            <!-- Popular Hostels -->
            <div class="card">
                <h3>Popular Hostels</h3>
                <ul>
                    <?php foreach ($popular_hostels as $hostel): ?>
                    <li>
                        <?= htmlspecialchars($hostel['name']) ?> 
                        <span class="badge"><?= $hostel['bookings_count'] ?> bookings</span>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <!-- Monthly Revenue -->
            <div class="card">
                <h3>Monthly Revenue</h3>
                <table>
                    <tr>
                        <th>Month</th>
                        <th>Revenue</th>
                    </tr>
                    <?php foreach ($monthly_revenue as $month): ?>
                    <tr>
                        <td><?= date('F Y', strtotime($month['month'] . '-01')) ?></td>
                        <td>$<?= number_format($month['revenue'], 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>
</body>
</html>