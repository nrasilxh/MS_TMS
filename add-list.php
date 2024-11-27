<?php
session_start(); // Start session at the beginning of each page
if (!isset($_SESSION['username'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit;
}
include('config/constants.php');

if (isset($_POST['submit'])) {
    $list_name = mysqli_real_escape_string($conn, $_POST['list_name']);
    $list_desc = mysqli_real_escape_string($conn, $_POST['list_description']);
    
    $sql = "INSERT INTO tbl_lists (list_name, list_desc) VALUES ('$list_name', '$list_desc')";
    $res = mysqli_query($conn, $sql);
    
    if ($res == true) {
        echo "List added successfully!";
    } else {
        echo "Failed to add list.";
    }
}
?>
<html>
<head>
    <title>Add List</title>
</head>
<body>
    <h1>Add List</h1>
    <form method="POST" action="">
        <table>
            <tr>
                <td>List Name:</td>
                <td><input type="text" name="list_name" required /></td>
            </tr>
            <tr>
                <td>List Description:</td>
                <td><textarea name="list_description"></textarea></td>
            </tr>
            <tr>
                <td colspan="2"><input type="submit" name="submit" value="Save" /></td>
            </tr>
        </table>
    </form>
</body>
</html>
<footer>
    <div class="footer-container">
        <p>&copy; <?php echo date("Y"); ?> Mom's Nature Task Management System. All rights reserved.</p>
    </div>
</footer>
