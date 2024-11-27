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

// Handle user deletion
if (isset($_GET['delete'])) {
    $project_id = (int) $_GET['delete'];
    $delete_sql = "DELETE FROM projects WHERE id = $project_id";
    if ($mysqli->query($delete_sql) === TRUE) {
        header("Location: projects.php?success=1");
        exit();
    } else {
        echo "Error deleting project: " . $mysqli->error;
    }
}
// Fetch projects data
$status = $_GET['status'] ?? 'All';

// Adjust SQL query
$sql = "SELECT * FROM projects";
if ($status !== 'All') {
    $sql .= " WHERE project_status = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $status);
} else {
    $stmt = $mysqli->prepare($sql);
}

$stmt->execute();
$result = $stmt->get_result();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projects</title>
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

    <!-- Header -->
    <div class="header" id="header">
    <h1>Project</h1>
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

    <!-- Toggle Button -->
    <button class="toggle-btn" id="toggle-btn">☰</button>

    <!-- Main Content -->
    <div class="content" id="content">
        <h2>Project Overview</h2>

        <!-- Filter Options -->
        <form method="GET" action="projects.php" class="mt-3">
            <label for="status">Filter by Status:</label>
            <select name="status" id="status">
                <option>All</option>
                <option>Not Started</option>
                <option>In Progress</option>
                <option>Completed</option>
            </select>
            <button type="submit" class="btn btn-primary btn-sm" id="filterBtn">Filter</button>
        </form>

        <!-- Projects Section -->
        <div class="card">
            <div class="card-header" >Active Projects</div>
            <div class="card-body" >
                <table class="table table-bordered" style="background-color: #cfdaba;">
                    <thead style="background-color: #839169;">
                        <tr>
                            <th>Project Name</th>
                            <th>Description</th>
                            <th>Start Date</th>
                            <th>Due Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['project_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['project_description']); ?></td>
                                    <td><?php echo htmlspecialchars($row['project_start_date']); ?></td>
                                    <td><?php echo htmlspecialchars($row['project_due_date']); ?></td>
                                    <td><?php echo htmlspecialchars($row['project_status']); ?></td>
                                    <td>
                                         <!-- View Button (Visible to all roles) -->
                <a href="projects.php?status=All" class="btn btn-secondary btn-sm">View</a>

                            <!-- Edit Button (Accessible to Admin, Manager, and Staff roles) -->
                            <?php if (in_array($_SESSION['role_name'], ['Admin', 'Manager', 'Staff'])): ?>
                                <a href="edit-project.php?id=<?php echo $row['project_id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                            <?php endif; ?>

                            <!-- Delete Button (Accessible only to Admin and Manager roles) -->
                            <?php if (in_array($_SESSION['role_name'], ['Admin', 'Manager'])): ?>
                                <a href="delete-project.php?id=<?php echo $row['project_id']; ?>" 
                                class="btn btn-danger btn-sm" 
                                onclick="return confirm('Are you sure you want to delete this project?');">
                                Delete
                                </a>
                            <?php endif; ?>
                                </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4">No projects found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Project Button -->
        <?php if (in_array($_SESSION['role_name'], ['Admin', 'Manager'])): ?>
            <a href="add-project.php" class="btn btn-primary">Add New Project</a>
        <?php endif; ?>
    </div>

    <script>
        // Toggle sidebar
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


