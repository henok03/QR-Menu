<?php
$host = '127.0.0.1'; // Replace 3307 with the number you see next to MySQL in XAMPP
$db   = 'qr-menu';
$port = '3306'; // Matches your phpMyAdmin screenshot
$user = 'root';
$pass = ''; 

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected successfully!"; // Uncomment this to test
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

?>
