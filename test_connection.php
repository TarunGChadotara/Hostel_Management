<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=hostel_management', 'root', '');
    echo "<h2>Connection Successful!</h2>";
    echo "<p>Now try <a href='register.php'>register.php</a></p>";
} catch (PDOException $e) {
    echo "<h2>Connection Failed</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "<h3>Troubleshooting:</h3>";
    echo "<ol>
        <li>Is MySQL running? (Check XAMPP/WAMP)</li>
        <li>Does database 'hostel_management' exist?</li>
        <li>Try using '127.0.0.1' instead of 'localhost'</li>
        <li>Check if port 3306 is blocked by firewall</li>
    </ol>";
}
?>