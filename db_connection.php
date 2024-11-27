<?php
$host = 'localhost'; // Database host
$db = 'login_db'; // Database name
$user = 'root'; // Database username
$pass = ''; // Database password (if any)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>