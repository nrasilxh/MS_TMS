<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Database connection
$mysqli = new mysqli("localhost", "root", "", "login_db");

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Check if project ID is provided in the URL
if (isset($_GET['id'])) {
    $project_id = (int) $_GET['id'];

    // Delete the project from the database
    $sql = "DELETE FROM projects WHERE project_id = $project_id";

    if ($mysqli->query($sql) === TRUE) {
        // Redirect to projects.php with a success message
        header("Location: projects.php?success=Project deleted successfully");
        exit();
    } else {
        echo "Error deleting project: " . $mysqli->error;
    }
} else {
    // Redirect if no project ID is provided
    header("Location: projects.php?error=No project ID provided");
    exit();
}

$mysqli->close();
?>
