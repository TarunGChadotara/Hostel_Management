<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php"); // Send to login instead of Google
    exit;
}

$username = htmlspecialchars($_SESSION['user']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Welcome</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
<div class="container">
    <div class="welcome-box">
        <h2>👋 Welcome, <?php echo $username; ?>!</h2>
        <p>You have successfully logged in to the system.</p>
        <a class="logout-btn" href="logout.php">Logout</a>
    </div>
</div>
</body>
</html>