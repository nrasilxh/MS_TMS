<?php
session_start(); // Start session at the beginning of each page
if (!isset($_SESSION['username'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit;
}
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    header("Location: update-settings.php");
    exit();
}

// Check if the user has admin privileges
if ($_SESSION['role'] !== 'admin') {
    echo "Access Denied: Admins only.";
    exit();
}

// Store necessary session information for authenticated admin
$user_id = $_SESSION['admin_id'];
$user_name = $_SESSION['name'];
$security_key = $_SESSION['security_key'];
?>
