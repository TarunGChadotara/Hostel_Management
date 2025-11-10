<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Get username from session
$username = htmlspecialchars($_SESSION['user']);

// Database connection
$conn = new mysqli("localhost", "root", "", "hostel_management");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get real statistics from database
$stats = [];
$result = $conn->query("SELECT COUNT(*) as total_users FROM users");
$row = $result->fetch_assoc();
$stats['users'] = $row['total_users'];

$result = $conn->query("SELECT COUNT(*) as total_hostels FROM hostels");
$row = $result->fetch_assoc();
$stats['hostels'] = $row['total_hostels'];

$result = $conn->query("SELECT COUNT(*) as total_bookings FROM bookings");
$row = $result->fetch_assoc();
$stats['bookings'] = $row['total_bookings'];

// Get additional stats
$result = $conn->query("SELECT COUNT(*) as pending_requests FROM bookings WHERE status = 'pending'");
$row = $result->fetch_assoc();
$stats['pending'] = $row['pending_requests'];

$result = $conn->query("SELECT SUM(h.price) as revenue FROM bookings b JOIN hostels h ON b.hostel_id = h.id WHERE b.status = 'confirmed'");
$row = $result->fetch_assoc();
$stats['revenue'] = $row['revenue'] ? $row['revenue'] : 0;

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .dashboard {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        .card {
            background: #1e293b;
            padding: 20px;
            border-radius: 10px;
            border-left: 4px solid #4fd1c5;
            transition: transform 0.3s;
            color: #ffffff; /* Changed to white */
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        .card h3 {
            color: #4fd1c5;
            margin-bottom: 15px;
        }
        .stats {
            font-size: 28px;
            font-weight: bold;
            margin: 10px 0;
        }
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid #334155;
        }
        .action-btn {
            display: inline-block;
            padding: 8px 15px;
            background: #4fd1c5;
            color: #1a2a3a;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            margin-top: 10px;
        }
        header {
            background: #1a2a3a;
            padding: 15px 0;
            margin-bottom: 30px;
        }
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            color: #4fd1c5;
            font-size: 24px;
            font-weight: bold;
        }
        nav ul {
            display: flex;
            list-style: none;
            gap: 20px;
        }
        nav a {
            color: #fff;
            text-decoration: none;
        }
        nav a.active {
            color: #4fd1c5;
        }
    </style>
</head>
<body>
    <header>
        <div class="container header-content">
            <div class="logo">Hostel Management</div>
            <nav>
                <ul>
                    <li><a href="admin_panel.php" class="active">Dashboard</a></li>
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
        <div class="admin-header">
            <h2>👑 Hostel Admin Dashboard</h2>
            <div>
                <span>Welcome, <?php echo $username; ?></span>
                <a class="logout-btn" href="logout.php">Logout</a>
            </div>
        </div>
        
        <div class="dashboard">
            <!-- Real Statistics from Database -->
            <div class="card">
                <h3>👥 Total Users</h3>
                <div class="stats"><?php echo $stats['users']; ?></div>
                <p>Registered users</p>
                <a href="users.php" class="action-btn">Manage Users</a>
            </div>
            
            <div class="card">
                <h3>🏠 Total Hostels</h3>
                <div class="stats"><?php echo $stats['hostels']; ?></div>
                <p>Available hostels</p>
                <a href="hostels.php" class="action-btn">Manage Hostels</a>
            </div>
            
            <div class="card">
                <h3>📋 Total Bookings</h3>
                <div class="stats"><?php echo $stats['bookings']; ?></div>
                <p>All bookings</p>
                <a href="bookings.php" class="action-btn">View Bookings</a>
            </div>
            
            <div class="card">
                <h3>⏳ Pending Requests</h3>
                <div class="stats"><?php echo $stats['pending']; ?></div>
                <p>Awaiting approval</p>
                <a href="bookings.php?status=pending" class="action-btn">Review</a>
            </div>
            
            <div class="card">
                <h3>💰 Total Revenue</h3>
                <div class="stats">$<?php echo number_format($stats['revenue'], 2); ?></div>
                <p>Confirmed bookings</p>
                <a href="reports.php" class="action-btn">View Reports</a>
            </div>
            
            <!-- Quick Actions -->
            <div class="card">
                <h3>⚡ Quick Actions</h3>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <a href="add_hostel.php" class="action-btn">+ Add New Hostel</a>
                    <a href="add_user.php" class="action-btn">+ Add New User</a>
                    <a href="announcement.php" class="action-btn">+ Send Announcement</a>
                </div>
            </div>
            
            <!-- System Info -->
            <div class="card">
                <h3>📊 System Status</h3>
                <p><strong>Server Time:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
                <p><strong>PHP Version:</strong> <?php echo phpversion(); ?></p>
                <p><strong>Users Online:</strong> Active</p>
                <a href="system_status.php" class="action-btn">View Details</a>
            </div>
        </div>
    </div>
</body>
</html>