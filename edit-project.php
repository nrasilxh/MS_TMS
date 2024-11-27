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

    // Fetch the project details
    $sql = "SELECT * FROM projects WHERE project_id = $project_id";
    $result = $mysqli->query($sql);

    if ($result->num_rows > 0) {
        $project = $result->fetch_assoc();
    } else {
        // Redirect if project doesn't exist
        header("Location: projects.php?error=Project not found");
        exit();
    }
} else {
    // Redirect if no project ID is provided
    header("Location: projects.php?error=No project ID provided");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $project_name = $mysqli->real_escape_string($_POST['project_name']);
    $project_description = $mysqli->real_escape_string($_POST['project_description']);
    $project_start_date = $mysqli->real_escape_string($_POST['project_start_date']);
    $project_due_date = $mysqli->real_escape_string($_POST['project_due_date']);
    $project_status = $mysqli->real_escape_string($_POST['project_status']);

    // Update the project in the database
    $sql = "UPDATE projects SET 
                project_name = '$project_name', 
                project_description = '$project_description',
                project_start_date = '$project_start_date',
                project_due_date = '$project_due_date',
                project_status = '$project_status'
            WHERE project_id = $project_id";

    if ($mysqli->query($sql) === TRUE) {
        // Redirect to projects.php with a success message
        header("Location: projects.php?success=1");
        exit();
    } else {
        echo "Error updating project: " . $mysqli->error;
    }
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Project</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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

    <!-- Header -->
    <div class="header" id="header">
    <h1>Project</h1>
    <div class="user-role" >
    <?php echo htmlspecialchars($_SESSION['username']); ?>
</div>
</div>

    <div class="sidebar" id="sidebar">

    <ul class="list-unstyled">
        <li><a href="home.php">Home</a></li>
        <li><a href="dashboard.php">Dashboard</a></li>
        
        <?php if ($_SESSION['role_name'] == 'Admin' || $_SESSION['role_name'] == 'Manager' || $_SESSION['role_name'] == 'Staff'): ?>
            <li><a href="projects.php">Projects</a></li>
            <li><a href="tasks.php">Tasks</a></li>
        <?php endif; ?>
        <li><a href="report.php">Report</a></li>
        <?php if ($_SESSION['role_name'] == 'Admin' || $_SESSION['role_name'] == 'Manager'): ?>
            
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

    <!-- Main Content -->
    <div class="container" id="content"><br>
        <h2 class="card-deck">Update project details :</h2>

    <div class="container">
        <form action="edit-project.php?id=<?php echo $project_id; ?>" method="POST">
            <div class="form-group"><br>
                <label for="project_name">Project Name</label>
                <input type="text" class="form-control" id="project_name" name="project_name" value="<?php echo htmlspecialchars($project['project_name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="project_description">Project Description</label>
                <textarea class="form-control" id="project_description" name="project_description" rows="4"><?php echo htmlspecialchars($project['project_description']); ?></textarea>
            </div>
            <div class="form-group">
                <label for="project_start_date">Start Date</label>
                <input type="date" class="form-control" id="project_start_date" name="project_start_date" value="<?php echo htmlspecialchars($project['project_start_date']); ?>" required>
            </div>
            <div class="form-group">
                <label for="project_due_date">Due Date</label>
                <input type="date" class="form-control" id="project_due_date" name="project_due_date" value="<?php echo htmlspecialchars($project['project_due_date']); ?>" required>
            </div>
            <div class="form-group">
                <label for="project_status">Project Status</label>
                <select class="form-control" id="project_status" name="project_status" required>
                    <option value="Pending" <?php if($project['project_status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                    <option value="In Progress" <?php if($project['project_status'] == 'In Progress') echo 'selected'; ?>>In Progress</option>
                    <option value="Completed" <?php if($project['project_status'] == 'Completed') echo 'selected'; ?>>Completed</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Update Project</button>
            <a href="projects.php" class="btn btn-secondary btn ml-2">Cancel</a>
        </form>
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

