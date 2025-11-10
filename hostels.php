<?php
require 'config.php';

if (!isset($_SESSION['user']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $location = trim($_POST['location']);
    $price = trim($_POST['price']);
    $description = trim($_POST['description']);
    $image_url = trim($_POST['image_url']);
    
    $stmt = $pdo->prepare("INSERT INTO hostels (name, location, price, description, image_url) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$name, $location, $price, $description, $image_url]);
    
    $_SESSION['success'] = "Hostel added successfully!";
    header("Location: hostels.php");
    exit;
}

// Get all hostels
$hostels = $pdo->query("SELECT * FROM hostels ORDER BY created_at DESC")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Hostels</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>Manage Hostels</h2>
    
    <div class="admin-actions">
        <button class="btn" onclick="document.getElementById('add-hostel').style.display='block'">Add New Hostel</button>
    </div>
    
    <div id="add-hostel" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="close" onclick="document.getElementById('add-hostel').style.display='none'">&times;</span>
            <h3>Add New Hostel</h3>
            <form method="POST" action="">
                <div class="form-group">
                    <label>Hostel Name</label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label>Location</label>
                    <input type="text" name="location" required>
                </div>
                <div class="form-group">
                    <label>Price per Night ($)</label>
                    <input type="number" name="price" step="0.01" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label>Image URL</label>
                    <input type="text" name="image_url" placeholder="https://example.com/image.jpg">
                </div>
                <button type="submit" class="btn">Add Hostel</button>
            </form>
        </div>
    </div>
    
    <div class="hostels-list">
        <?php foreach ($hostels as $hostel): ?>
            <div class="hostel-card">
                <div class="hostel-img" style="background-image: url('<?= $hostel['image_url'] ?: 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80' ?>')"></div>
                <div class="hostel-info">
                    <h3><?= htmlspecialchars($hostel['name']) ?></h3>
                    <p><?= htmlspecialchars($hostel['location']) ?></p>
                    <p>$<?= number_format($hostel['price'], 2) ?>/night</p>
                    <p><?= htmlspecialchars($hostel['description']) ?></p>
                    <div class="hostel-actions">
                        <a href="edit_hostel.php?id=<?= $hostel['id'] ?>" class="btn">Edit</a>
                        <a href="delete_hostel.php?id=<?= $hostel['id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this hostel?')">Delete</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
// Close modal when clicking outside
window.onclick = function(event) {
    if (event.target.className === 'modal') {
        event.target.style.display = 'none';
    }
}
</script>
</body>
</html>