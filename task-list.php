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

// Query to get projects and tasks
$query = "SELECT 
            projects.project_name, 
            tasks.task_name, 
            projects.project_start_date, 
            projects.project_due_date, 
            projects.project_status, 
            tasks.task_status 
          FROM tasks 
          JOIN projects ON tasks.project_id = projects.project_id";

$result = $mysqli->query($query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task List</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #FEFAE0;
            color: #333;
        }
        .header {
            background-color: #B99470;
            padding: 20px;
            text-align: center;
            color: #FEFAE0;
            font-weight: 600;
        }
        .container {
            margin-top: 20px;
        }
        .table {
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .btn {
            border-radius: 25px;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="header">
        <h1>Task List</h1>
    </div>

    <!-- Main Content -->
    <div class="container">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Project</th>
                    <th>Task</th>
                    <th>Project Start Date</th>
                    <th>Project Due Date</th>
                    <th>Project Status</th>
                    <th>Task Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['project_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['task_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['project_start_date']); ?></td>
                            <td><?php echo htmlspecialchars($row['project_due_date']); ?></td>
                            <td><?php echo htmlspecialchars($row['project_status']); ?></td>
                            <td><?php echo htmlspecialchars($row['task_status']); ?></td>
                            <td>
                                <a href="view-task.php?task_id=<?php echo $row['task_id']; ?>" class="btn btn-primary btn-sm">View</a>
                                <a href="update-task.php?task_id=<?php echo $row['task_id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                                <a href="delete-task.php?task_id=<?php echo $row['task_id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">No tasks found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>
</html>

<?php
$mysqli->close();
?>
