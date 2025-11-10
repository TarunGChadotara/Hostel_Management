<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get user bookings
$stmt = $pdo->prepare("SELECT b.*, h.name, h.location, h.price, h.image_url
                      FROM bookings b 
                      JOIN hostels h ON b.hostel_id = h.id
                      WHERE b.user_id = ? ORDER BY b.created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$bookings = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Bookings</title>
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .bookings-container {
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .page-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #334155;
        }
        
        .bookings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
            margin-top: 20px;
        }
        
        .booking-card {
            background: #1e293b;
            border-radius: 12px;
            padding: 20px;
            border-left: 4px solid #4fd1c5;
            transition: transform 0.3s ease;
            color: #ffffff;
        }
        
        .booking-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(79, 209, 197, 0.2);
        }
        
        .booking-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .booking-title {
            color: #4fd1c5;
            font-size: 18px;
            font-weight: bold;
            margin: 0;
        }
        
        .booking-status {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .status-pending {
            background: #fbbf24;
            color: #1a2a3a;
        }
        
        .status-confirmed {
            background: #10b981;
            color: #ffffff;
        }
        
        .status-cancelled {
            background: #ef4444;
            color: #ffffff;
        }
        
        .booking-details {
            margin: 15px 0;
        }
        
        .booking-detail {
            display: flex;
            justify-content: space-between;
            margin: 8px 0;
            padding: 5px 0;
            border-bottom: 1px solid #334155;
        }
        
        .detail-label {
            color: #94a3b8;
            font-weight: 500;
        }
        
        .detail-value {
            color: #ffffff;
            font-weight: bold;
        }
        
        .booking-price {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            color: #4fd1c5;
            margin: 15px 0;
            padding: 10px;
            background: rgba(79, 209, 197, 0.1);
            border-radius: 8px;
        }
        
        .booking-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        
        .action-btn {
            flex: 1;
            padding: 8px 15px;
            text-align: center;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        
        .view-btn {
            background: #4fd1c5;
            color: #1a2a3a;
        }
        
        .view-btn:hover {
            background: #38b2ac;
        }
        
        .cancel-btn {
            background: #ef4444;
            color: #ffffff;
        }
        
        .cancel-btn:hover {
            background: #dc2626;
        }
        
        .no-bookings {
            text-align: center;
            padding: 40px;
            color: #94a3b8;
        }
        
        .no-bookings-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
        
        .back-link {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #4fd1c5;
            color: #1a2a3a;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        
        .back-link:hover {
            background: #38b2ac;
        }
        
        @media (max-width: 768px) {
            .bookings-grid {
                grid-template-columns: 1fr;
            }
            
            .booking-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="bookings-container">
            <div class="page-header">
                <h2>📋 My Bookings</h2>
                <p>View and manage your hostel reservations</p>
                <a href="hostel_world.php" class="back-link">← Back to Hostels</a>
            </div>
            
            <?php if (count($bookings) > 0): ?>
                <div class="bookings-grid">
                    <?php foreach ($bookings as $booking): ?>
                    <div class="booking-card">
                        <div class="booking-header">
                            <h3 class="booking-title"><?= htmlspecialchars($booking['name']) ?></h3>
                            <span class="booking-status status-<?= $booking['status'] ?>">
                                <?= ucfirst($booking['status']) ?>
                            </span>
                        </div>
                        
                        <div class="booking-details">
                            <div class="booking-detail">
                                <span class="detail-label">Location:</span>
                                <span class="detail-value"><?= htmlspecialchars($booking['location']) ?></span>
                            </div>
                            
                            <div class="booking-detail">
                                <span class="detail-label">Check-in:</span>
                                <span class="detail-value"><?= date('M d, Y', strtotime($booking['check_in'])) ?></span>
                            </div>
                            
                            <div class="booking-detail">
                                <span class="detail-label">Check-out:</span>
                                <span class="detail-value"><?= date('M d, Y', strtotime($booking['check_out'])) ?></span>
                            </div>
                            
                            <div class="booking-detail">
                                <span class="detail-label">Nights:</span>
                                <span class="detail-value">
                                    <?= $nights = (strtotime($booking['check_out']) - strtotime($booking['check_in'])) / (60 * 60 * 24) ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="booking-price">
                            $<?= number_format($booking['price'] * $nights, 2) ?>
                        </div>
                        
                        <div class="booking-actions">
                            <a href="view_booking.php?id=<?= $booking['id'] ?>" class="action-btn view-btn">View Details</a>
                            <?php if ($booking['status'] == 'pending'): ?>
                                <a href="cancel_booking.php?id=<?= $booking['id'] ?>" class="action-btn cancel-btn">Cancel</a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-bookings">
                    <div class="no-bookings-icon">📭</div>
                    <h3>No Bookings Yet</h3>
                    <p>You haven't made any bookings yet. Start exploring our hostels!</p>
                    <a href="hostel_world.php" class="back-link">Browse Hostels</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>