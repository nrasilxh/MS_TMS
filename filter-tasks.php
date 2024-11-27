<?php
session_start(); // Start session at the beginning of each page
if (!isset($_SESSION['username'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit;
}
require 'db_connection.php'; // Include database connection

$status = $_POST['status'] ?? 'All';

$query = "SELECT * FROM tasks";
if ($status !== 'All') {
    $query .= " WHERE status = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$status]);
} else {
    $stmt = $pdo->query($query);
}

$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($tasks as $task) {
    echo "<tr>
            <td>{$task['task_name']}</td>
            <td>{$task['assignee']}</td>
            <td>{$task['priority']}</td>
            <td>{$task['status']}</td>
            <td>{$task['due_date']}</td>
            <td>
                <a href='view-task.php?id={$task['id']}' class='btn btn-secondary btn-sm'>View</a>
                <a href='update-task.php?id={$task['id']}' class='btn btn-primary btn-sm'>Edit</a>
                <a href='delete-task.php?id={$task['id']}' class='btn btn-danger btn-sm'>Delete</a>
            </td>
        </tr>";
}
