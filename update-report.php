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

// Fetch report details based on report_id
$query = "SELECT * FROM reports WHERE report_id = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $report_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if report exists
if ($result->num_rows == 0) {
    die("Report not found.");
}

$row = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $report_title = $_POST['report_title'];
    $report_description = $_POST['report_description'];
    $report_status = $_POST['report_status'];

    // Update the report in the database
    $update_query = "UPDATE reports SET report_title = ?, report_description = ?, report_status = ?, updated_at = NOW() WHERE report_id = ?";
    $stmt = $db->prepare($update_query);
    $stmt->bind_param("sssi", $report_title, $report_description, $report_status, $report_id);

    if ($stmt->execute()) {
        // Redirect to the report page after update
        header("Location: report.php");
        exit();
    } else {
        $error_message = "Error updating report: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Report</title>
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
        .btn-primary {
            background-color: #B99470;
            border-color: #B99470;
            color: #FEFAE0;
            border-radius: 25px;
            transition: background-color 0.3s, border-color 0.3s;
        }
        .btn-primary:hover {
            background-color: #A6B37D;
            border-color: #A6B37D;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h3>Update Report</h3>
        </div>
        <div class="card-body">
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="update-report.php?report_id=<?php echo htmlspecialchars($report_id); ?>">
                <div class="form-group">
                    <label for="report_title">Title</label>
                    <input type="text" class="form-control" id="report_title" name="report_title" value="<?php echo htmlspecialchars($row['report_title']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="report_description">Description</label>
                    <textarea class="form-control" id="report_description" name="report_description" rows="4" required><?php echo htmlspecialchars($row['report_description']); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="report_status">Status</label>
                    <select class="form-control" id="report_status" name="report_status" required>
                        <option value="Pending" <?php echo ($row['report_status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                        <option value="In Progress" <?php echo ($row['report_status'] == 'In Progress') ? 'selected' : ''; ?>>In Progress</option>
                        <option value="Completed" <?php echo ($row['report_status'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Update Report</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>
