<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$mysqli = new mysqli("localhost", "root", "", "login_db");

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Fetch the current user's data
$user = $_SESSION['username']; // Assuming user_id is stored in session
$sql = "SELECT email FROM users WHERE id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($email);
$stmt->fetch();

// Handle the update of email and password
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get new email and password values from the form
    $new_email = $_POST['email'];
    $new_password = $_POST['password'];

    // Validate and sanitize inputs
    if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['message'] = "Invalid email format";
        $_SESSION['message_type'] = "danger";
    } else {
        // Prepare SQL to update email
        $update_sql = "UPDATE users SET email = ? WHERE id = ?";
        $stmt = $mysqli->prepare($update_sql);
        $stmt->bind_param("si", $new_email, $user_id);
        $stmt->execute();

        // If password is provided, hash and update it
        if (!empty($new_password)) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_password_sql = "UPDATE users SET password = ? WHERE id = ?";
            $stmt = $mysqli->prepare($update_password_sql);
            $stmt->bind_param("si", $hashed_password, $user_id);
            $stmt->execute();
        }

        $_SESSION['message'] = "Settings updated successfully!";
        $_SESSION['message_type'] = "success";
    }
}

// Close database connection
$stmt->close();
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
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
        <h1>Settings</h1>
        <div class="user-role">
            <?php echo htmlspecialchars($_SESSION['username']); ?>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
    <h2>Navigation</h2>
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
    
    <div class="content" id="content">
        <br>
        <!-- Main Content -->
        <div class="container">
            <h2 class="card-deck">Account Settings</h2>
            <br>

            <!-- Display success or error messages -->
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-<?php echo $_SESSION['message_type']; ?>">
                    <?php echo $_SESSION['message']; ?>
                </div>
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>

            <form action="settings.php" method="POST">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" name="email" class="form-control" id="email" value="<?php echo htmlspecialchars($email); ?>" required>
                </div>
                <div class="form-group">
                    <label for="password">New Password:</label>
                                <input type="password" name="password" class="form-control" id="password" required minlength="8" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters">
                    <small class="form-text text-muted">Leave blank if you don't want to change the password.</small>
                </div>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </form>
        </div>
    </div>

    <script>
        // Toggle sidebar
        document.getElementById('toggle-btn').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('show');
            document.getElementById('content').classList.toggle('shift');
        });
    </script>
</body>
</html>
