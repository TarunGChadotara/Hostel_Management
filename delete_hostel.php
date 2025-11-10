<?php
require 'config.php';

if (!isset($_SESSION['user']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("DELETE FROM hostels WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    
    $_SESSION['success'] = "Hostel deleted successfully!";
}

header("Location: hostels.php");
exit;
?>