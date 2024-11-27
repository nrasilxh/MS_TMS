<?php
session_start(); // Start session at the beginning of each page
if (!isset($_SESSION['username'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit;
}
include('config/constants.php');
?>

<html>
<head>
    <title>Manage Tasks</title>
</head>
<body>
    <h1>Manage Tasks</h1>

    <a href="add-task.php">Add New Task</a>
    <br/><br/>

    <table border="1">
        <tr>
            <th>ID</th>
            <th>Task Name</th>
            <th>Description</th>
            <th>Due Date</th>
            <th>Status</th>
            <th>List</th>
            <th>Actions</th>
        </tr>
        
        <?php
        // SQL query to fetch all tasks
        $sql = "SELECT tbl_tasks.*, tbl_lists.list_name FROM tbl_tasks INNER JOIN tbl_lists ON tbl_tasks.list_id = tbl_lists.id";
        $res = mysqli_query($conn, $sql);

        if ($res == true) {
            while ($row = mysqli_fetch_assoc($res)) {
                ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['task_name']; ?></td>
                    <td><?php echo $row['task_desc']; ?></td>
                    <td><?php echo $row['due_date']; ?></td>
                    <td><?php echo $row['status']; ?></td>
                    <td><?php echo $row['list_name']; ?></td>
                    <td>
                        <a href="update-task.php?id=<?php echo $row['id']; ?>">Edit</a> | 
                        <a href="delete-task.php?id=<?php echo $row['id']; ?>">Delete</a>
                    </td>
                </tr>
                <?php
            }
        } else {
            echo "<tr><td colspan='7'>No tasks found.</td></tr>";
        }
        ?>
    </table>
</body>
</html>
