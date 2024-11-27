<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$mysqli = new mysqli("localhost", "root", "", "login_db");

// Check for connection errors
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Query for Pending Tasks count
$pending_query = "SELECT COUNT(*) AS task_pending_count FROM tasks WHERE task_status = 'Pending'";
$pending_result = $mysqli->query($pending_query);
$task_pending_count = $pending_result ? $pending_result->fetch_assoc()['task_pending_count'] : 0;

// Query for In Progress Tasks count
$inprogress_query = "SELECT COUNT(*) AS task_inprogress_count FROM tasks WHERE task_status = 'In Progress'";
$inprogress_result = $mysqli->query($inprogress_query);
$task_inprogress_count = $inprogress_result ? $inprogress_result->fetch_assoc()['task_inprogress_count'] : 0;

// Query for Completed Tasks count
$completed_query = "SELECT COUNT(*) AS task_completed_count FROM tasks WHERE task_status = 'Completed'";
$completed_result = $mysqli->query($completed_query);
$task_completed_count = $completed_result ? $completed_result->fetch_assoc()['task_completed_count'] : 0;


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #FEFAE0;
            color: #333;
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            height: 100vh;
            flex-direction: column;
            overflow: hidden; /* Prevents scrollbars */
        }
        .header {
            display: flex;
            background-color: #B99470;
            padding: 20px;
            text-align: center;
            color: #FEFAE0;
            font-weight: 600;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 1;
            justify-content: space-between; /* Space between elements */
            position: relative;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            text-align: right;
            padding-left: 100px;
            transition: margin-left 0.3s ease, width 0.3s ease; /* Smooth content shift */
        }
        .user-role {
            font-size: 20px;
            font-weight:bold;
            color: #FEFAE0;
            text-align: right;
            padding-right: 70px;
        }
        .sidebar {
            width: 250px;
            background-color: #7ea96b;
            color: #FEFAE0;
            padding: 20px;
            height: calc(100vh - 70px); /* Adjust height based on header */
            position: fixed;
            top: 70px; /* Adjust based on header height */
            left: -250px; /* Start hidden */
            transition: left 0.3s ease; /* Slide effect */
            box-shadow: 2px 0 4px rgba(0, 0, 0, 0.1);
            z-index: 2;
        }
        .sidebar.show {
            left: 0; /* Slide in */
        }
        .sidebar a {
            color: #FEFAE0;
            display: block;
            padding: 10px 15px; /* Add horizontal padding to create space from the edges */
            text-decoration: none;
            font-weight: 600;
            transition: background-color 0.3s;
            border-radius: 10px; /* Rounded button */
        }
        .sidebar a:hover {
            background-color: #B99470;
        }
        .content {
            margin-left: 0; /* Default margin when sidebar is hidden */
            padding: 20px;
            flex: 1;
            height: calc(100vh - 70px); /* Adjust height based on header */
            overflow-y: auto;
            transition: margin-left 0.3s ease, width 0.3s ease; /* Smooth content shift */
            z-index: 1;
        }
        .content.shift {
            margin-left: 250px; /* Shift content to the right when sidebar is shown */
            width: calc(100% - 250px); /* Reduce width to make space for the sidebar */
        }
        .card {
            background-color: #C0C78C;
            color: #333;
            border: none;
            margin-bottom: 20px;
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
        }
        .btn-secondary {
            background-color: #6C757D;
            border-color: #6C757D;
            color: #FEFAE0;
            border-radius: 25px; /* Rounded button */
            transition: background-color 0.3s, border-color 0.3s;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
            color: #FEFAE0;
        }
        .toggle-btn {
            position: fixed;
            top: 15px;
            left: 20px;
            background-color: #A6B37D;
            color: #FEFAE0;
            border: none;
            padding: 10px 20px;
            border-radius: 25px; /* Rounded button */
            cursor: pointer;
            z-index: 1000;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            font-weight: 600;
            transition: background-color 0.3s;
        }
        .toggle-btn:focus {
            outline: none; /* Removes default outline */
            border: 1px solid black; /* Adds a black border on focus */
        }
        .toggle-btn:hover {
            background-color: #7ea96b;
        }
        
    </style>
</head>
<body>
<div class="header" id="header">
    <h1>Dashboard</h1>
    <div class="user-role" >
    <?php echo htmlspecialchars($_SESSION['username']); ?>
</div>
</div>


    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
    <h2><?php echo htmlspecialchars($_SESSION['role_name']); ?></h2>
    <ul class="list-unstyled">
        <li><a href="home.php">Home</a></li>
        <li><a href="dashboard.php">Dashboard</a></li>
        
        <?php if ($_SESSION['role_name'] == 'Admin' || $_SESSION['role_name'] == 'Manager' || $_SESSION['role_name'] == 'Staff'): ?>
            <li><a href="projects.php">Projects</a></li>
            <li><a href="tasks.php">Tasks</a></li>
        <?php endif; ?>
        <?php if ($_SESSION['role_name'] == 'Admin' || $_SESSION['role_name'] == 'Manager'): ?>
            <li><a href="report.php">Report</a></li>
            <li><a href="users.php">Users</a></li>
            <li><a href="settings.php">Settings</a></li>
        <?php endif; ?>
        
        <?php if ($_SESSION['role_name'] == 'Admin' || $_SESSION['role_name'] == 'Manager' || $_SESSION['role_name'] == 'Production Crew'): ?>
            <li><a href="product-inventory.php">Product Inventory</a></li>
        <?php endif; ?>
        <li><a href="faq.php">FAQs</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</div>


    <button class="toggle-btn" id="toggle-btn">☰</button>

    <div class="content" id="content">
    <br>
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
<br>
        <!-- Quick Stats Section -->
        <div class="card-deck">
            <div class="card"><div class="card-header">Pending Tasks</div><div class="card-body"><h5>Pending Tasks</h5><h1><?php echo $task_pending_count; ?></h1></div></div>
            <div class="card"><div class="card-header">In Progress Tasks</div><div class="card-body"><h5>In Progress Tasks</h5><h1><?php echo $task_inprogress_count; ?></h1></div></div>
            <div class="card"><div class="card-header">Completed Tasks</div><div class="card-body"><h5>Completed Tasks</h5><h1><?php echo $task_completed_count; ?></h1></div></div>
        
            <div class="data-section">

            <!-- Schedule -->
            <div class="schedule">
                <h4>Today</h4>
                <div class="event">
                    <p><strong>Daily Standup Call</strong></p>
                    <p>9:00 AM</p>
                </div>
                <div class="event">
                    <p><strong>Brand Identity Meeting</strong></p>
                    <p>11:00 AM</p>
                </div>
            </div>
            </div>        
        </div>



        <!-- Filter Options -->
        <form method="GET" action="tasks.php" class="mt-3">
            <label for="status">Filter by Status:</label>
            <select name="status" id="status">
                <option>All</option>
                <option>Pending</option>
                <option>In Progress</option>
                <option>Completed</option>
            </select>
            <button type="submit" class="btn btn-primary btn-sm" id="filterBtn">Filter</button>
        </form>

        <!-- Task Table -->
        <div class="card mt-3">
            <div class="card-header">Issues :</div>
            <div class="card-body">
            <table class="table table-bordered" style="background-color: #cfdaba;">
            <thead style="background-color: #839169;">
                        <tr>
                            <th>Task Name</th>
                            <th>Assignee</th>
                            <th>Status</th>
                            <th>Due Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Design Logo</td>
                            <td>John</td>
                            <td>Pending</td>
                            <td>2024-11-01</td>
                            <td>
                                <a href="view-task.php" class="btn btn-secondary btn-sm">View</a>
                                        <!-- Buttons -->
                            <?php if (in_array($_SESSION['role_name'], ['Admin', 'Manager'])): ?>
                                <a href="edit-task.php" class="btn btn-primary btn-sm">Edit</a>
                            <?php endif; ?>
                                
                            </td>
                        </tr>
                        <!-- More rows as needed -->
                    </tbody>
                </table>
                        <!-- Add Task Button -->
                    <?php if (in_array($_SESSION['role_name'], ['Admin', 'Manager'])): ?>
                        <a href="add-task.php" class="btn btn-primary">Add New Task</a>
                    <?php endif; ?>
            </div>

            
        </div>

        
    </div>



    <script>
        document.getElementById('toggle-btn').addEventListener('click', function() {
            var sidebar = document.getElementById('sidebar');
            var content = document.getElementById('content');
            if (sidebar.classList.contains('show')) {
                sidebar.classList.remove('show');
                content.classList.remove('shift');
            } else {
                sidebar.classList.add('show');
                content.classList.add('shift');
            }
        });
    </script>

    
</body>
</html>
