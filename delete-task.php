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

// Check if task ID is provided in the URL
if (isset($_GET['id'])) {
    $task_id = (int) $_GET['id'];

    // Delete the task from the database
    $sql = "DELETE FROM tasks WHERE task_id = $task_id";

    if ($mysqli->query($sql) === TRUE) {
        // Redirect to tasks.php with a success message
        header("Location: tasks.php?success=Task deleted successfully");
        exit();
    } else {
        echo "Error deleting task: " . $mysqli->error;
    }
} else {
    // Redirect if no task ID is provided
    header("Location: tasks.php?error=No task ID provided");
    exit();
}

$mysqli->close();

