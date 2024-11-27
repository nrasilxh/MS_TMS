<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Check if report_id is provided
if (!isset($_GET['report_id'])) {
    die("Report ID is missing.");
}

$report_id = $_GET['report_id'];

// Connect to the database
$db = new mysqli('localhost', 'root', '', 'login_db'); // Update with your database credentials
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Check if the report exists before deleting
$query = "SELECT * FROM reports WHERE report_id = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $report_id);
$stmt->execute();
$result = $stmt->get_result();

// If report doesn't exist, redirect back to reports page
if ($result->num_rows == 0) {
    die("Report not found.");
}

$row = $result->fetch_assoc();

// Handle deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $delete_query = "DELETE FROM reports WHERE report_id = ?";
    $stmt = $db->prepare($delete_query);
    $stmt->bind_param("i", $report_id);

    if ($stmt->execute()) {
        // Redirect to the report overview page after deletion
        header("Location: report.php");
        exit();
    } else {
        $error_message = "Error deleting report: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Report</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #FEFAE0;
            color: #333;
            font-family: 'Poppins', sans-serif;
        }
        .container {
            max-width: 800px;
            margin-top: 50px;
        }
        .card {
            background-color: #C0C78C;
            color: #333;
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #B99470;
            color: #FEFAE0;
            font-weight: 600;
            border-radius: 15px 15px 0 0;
        }
        .btn-danger {
            background-color: #DC3545;
            border-color: #DC3545;
            color: #FEFAE0;
            border-radius: 25px;
            transition: background-color 0.3s, border-color 0.3s;
        }
        .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }
        .btn-secondary {
            background-color: #6C757D;
            border-color: #6C757D;
            color: #FEFAE0;
            border-radius: 25px;
            transition: background-color 0.3s, border-color 0.3s;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h3>Delete Report</h3>
        </div>
        <div class="card-body">
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>
            
            <p>Are you sure you want to delete the following report?</p>
            <table class="table table-bordered">
                <tr>
                    <th>Title</th>
                    <td><?php echo htmlspecialchars($row['report_title']); ?></td>
                </tr>
                <tr>
                    <th>Description</th>
                    <td><?php echo htmlspecialchars($row['report_description']); ?></td>
                </tr>
                <tr>
                    <th>Submitted By</th>
                    <td><?php echo htmlspecialchars($row['submitted_by']); ?></td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td><?php echo htmlspecialchars($row['report_status']); ?></td>
                </tr>
                <tr>
                    <th>Created At</th>
                    <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                </tr>
            </table>

            <form method="POST" action="delete-report.php?report_id=<?php echo htmlspecialchars($report_id); ?>">
                <button type="submit" class="btn btn-danger">Delete Report</button>
                <a href="report.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>

</body>
</html>
