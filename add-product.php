<?php
session_start(); // Start session at the beginning of each page
if (!isset($_SESSION['username'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit;
}

include('config.php');

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get input values from the form
    $product_name = $_POST['product_name'];
    $product_description = $_POST['product_description'];
    $price = $_POST['price'];
    $stock_quantity = $_POST['stock_quantity'];
    
    // Get current date and time for created_at
    $created_at = date('Y-m-d H:i:s');
    
    // Insert product into the database
    $sql = "INSERT INTO products (product_name, product_description, price, stock_quantity, created_at) 
            VALUES ('$product_name', '$product_description', '$price', '$stock_quantity', '$created_at')";
    
    if ($conn->query($sql) === TRUE) {
        // Redirect to the product inventory page after successful insertion
        header("Location: product-inventory.php");
        exit;
    } else {
        // Display error if the query failed
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Product</title>
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

<div class="header">
    <h1>Add New Product</h1>
    <div class="user-role"><?php echo htmlspecialchars($_SESSION['username']); ?></div>
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

<div class="content" id="content">
    <div class="card">
        <div class="card-header">Add Product Form</div>
        <div class="card-body">
            <form action="add-product.php" method="POST">
                <div class="form-group">
                    <label for="product_name">Product Name</label>
                    <input type="text" class="form-control" id="product_name" name="product_name" required>
                </div>
                <div class="form-group">
                    <label for="product_description">Product Description</label>
                    <textarea class="form-control" id="product_description" name="product_description" required></textarea>
                </div>
                <div class="form-group">
                    <label for="price">Price (RM)</label>
                    <input type="number" class="form-control" id="price" name="price" required step="0.01" min="0">
                </div>
                <div class="form-group">
                    <label for="stock_quantity">Stock Quantity</label>
                    <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" required min="1">
                </div>
                <button type="submit" class="btn btn-primary">Add Product</button>
            </form>
        </div>
    </div>
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

</body>
</html>
<footer>
    <div class="footer-container">
        <p>&copy; <?php echo date("Y"); ?> Mom's Nature Task Management System. All rights reserved.</p>
    </div>
</footer>
