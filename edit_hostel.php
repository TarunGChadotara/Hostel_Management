<?php
require 'config.php';

if (!isset($_SESSION['user']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Get hostel details
$stmt = $pdo->prepare("SELECT * FROM hostels WHERE id = ?");
$stmt->execute([$_GET['id']]);
$hostel = $stmt->fetch();

if (!$hostel) {
    header("Location: hostels.php");
    exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $location = trim($_POST['location']);
    $price = trim($_POST['price']);
    $description = trim($_POST['description']);
    $image_url = trim($_POST['image_url']);
    
    $stmt = $pdo->prepare("UPDATE hostels SET name = ?, location = ?, price = ?, description = ?, image_url = ? WHERE id = ?");
    $stmt->execute([$name, $location, $price, $description, $image_url, $hostel['id']]);
    
    $_SESSION['success'] = "Hostel updated successfully!";
    header("Location: hostels.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Hostel</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>Edit Hostel</h2>
    
    <form method="POST" action="">
        <div class="form-group">
            <label>Hostel Name</label>
            <input type="text" name="name" value="<?= htmlspecialchars($hostel['name']) ?>" required>
        </div>
        <div class="form-group">
            <label>Location</label>
            <input type="text" name="location" value="<?= htmlspecialchars($hostel['location']) ?>" required>
        </div>
        <div class="form-group">
            <label>Price per Night ($)</label>
            <input type="number" name="price" step="0.01" value="<?= htmlspecialchars($hostel['price']) ?>" required>
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="3"><?= htmlspecialchars($hostel['description']) ?></textarea>
        </div>
        <div class="form-group">
            <label>Image URL</label>
            <input type="text" name="image_url" value="<?= htmlspecialchars($hostel['image_url']) ?>" placeholder="https://example.com/image.jpg">
        </div>
        <button type="submit" class="btn">Update Hostel</button>
        <a href="hostels.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>