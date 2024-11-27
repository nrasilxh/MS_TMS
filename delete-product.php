<?php
session_start();
include('config.php');

session_start(); // Start session at the beginning of each page
if (!isset($_SESSION['username'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit;
}
// Check if the product ID is provided in the URL
if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    // Prepare the SQL query to delete the product
    $sql = "DELETE FROM products WHERE id = ?";

    // Prepare and bind the statement
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $product_id); // Bind the ID parameter

        // Execute the query
        if ($stmt->execute()) {
            // Redirect to the product inventory page after successful deletion
            header("Location: product-inventory.php?message=Product deleted successfully");
            exit();
        } else {
            // Display error if the query fails
            echo "Error: " . $conn->error;
        }

        $stmt->close();
    }
} else {
    // If no product ID is provided, redirect to the product inventory page
    header("Location: product-inventory.php?error=Product ID not specified");
    exit();
}

$conn->close();
?>
