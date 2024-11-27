<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

require 'db_connection.php'; // Make sure to include your database connection file

if (isset($_GET['id'])) {
    $taskId = intval($_GET['id']); // Sanitize the input

    // Prepare a statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM tasks WHERE id = ?");
    $stmt->bind_param("i", $taskId);

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $task = $result->fetch_assoc();
    } else {
        header("Location: tasks.php?status=notfound"); // Redirect if task not found
        exit();
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: tasks.php?status=All"); // Redirect if no ID is provided
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Task</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #FEFAE0;
            color: #333;
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
        }
        .header {
            background-color: #B99470;
            padding: 20px;
            text-align: center;
            color: #FEFAE0;
            font-weight: 600;
        }
        .card {
            background-color: #C0C78C;
            color: #333;
            border: none;
            margin: 20px;
            border-radius: 15px; /* Rounded corners */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #B99470;
            color: #FEFAE0;
            font-weight: 600;
            border-radius: 15px 15px 0 0; /* Rounded top corners */
        }
        .btn-primary {
            background-color: #B99470;
            border-color: #B99470;
            color: #FEFAE0;
            border-radius: 25px; /* Rounded button */
            transition: background-color 0.3s, border-color 0.3s;
        }
        .btn-primary:hover {
            background-color: #A6B37D;
            border-color: #A6B37D;
            color: #FEFAE0;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="header">
        <h1>View Task</h1>
    </div>

    <!-- Main Content -->
    <div class="container">
        <div class="card">
            <div class="card-header">Task Details</div>
            <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($task['task_name']); ?></h5>
                <p class="card-text"><strong>Description:</strong> <?php echo htmlspecialchars($task['description']); ?></p>
                <p class="card-text"><strong>Status:</strong> <?php echo htmlspecialchars($task['status']); ?></p>
                <p class="card-text"><strong>Due Date:</strong> <?php echo htmlspecialchars($task['due_date']); ?></p>
                <a href="update-task.php?id=<?php echo $taskId; ?>" class="btn btn-primary">Edit Task</a>
                <a href="tasks.php" class="btn btn-secondary">Back to Tasks</a>
            </div>
        </div>
    </div>


</body>
</html>
