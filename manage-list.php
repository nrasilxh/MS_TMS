<?php
session_start(); // Start session at the beginning of each page
if (!isset($_SESSION['username'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit;
}
include('config/constants.php');

$sql = "SELECT * FROM tbl_lists";
$res = mysqli_query($conn, $sql);
?>
<html>
<head>
    <title>Manage Lists</title>
</head>
<body>
    <h1>Manage Lists</h1>
    <a href="add-list.php">Add New List</a>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>List Name</th>
            <th>Description</th>
            <th>Actions</th>
        </tr>
        <?php
        if ($res == true) {
            while ($row = mysqli_fetch_assoc($res)) {
                ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['list_name']; ?></td>
                    <td><?php echo $row['list_desc']; ?></td>
                    <td>
                        <a href="update-list.php?id=<?php echo $row['id']; ?>">Edit</a> | 
                        <a href="delete-list.php?id=<?php echo $row['id']; ?>">Delete</a>
                    </td>
                </tr>
                <?php
            }
        }
        ?>
    </table>
</body>
</html>
