<?php
session_start();
require 'authentication.php'; // Ensure the user is authenticated
require 'db_connection.php'; // Database connection

// Redirect if not authenticated
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['admin_id'];
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate the email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // Update the email
        $stmt = $conn->prepare("UPDATE users SET email = ? WHERE id = ?");
        $stmt->bind_param("si", $email, $user_id);
        if (!$stmt->execute()) {
            $error = "Error updating email.";
        }

        // If a new password is provided, update the password
        if (!empty($password)) {
            // Validate password strength (basic example)
            if (strlen($password) < 8) {
                $error = "Password must be at least 8 characters.";
            } elseif (!preg_match("/[A-Za-z]/", $password) || !preg_match("/[0-9]/", $password)) {
                $error = "Password must contain both letters and numbers.";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->bind_param("si", $hashed_password, $user_id);
                if (!$stmt->execute()) {
                    $error = "Error updating password.";
                }
            }
        }

        // If no errors, set a success message and redirect
        if (empty($error)) {
            $_SESSION['success'] = "Settings updated successfully.";
            header("Location: settings.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Settings</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2>Settings Update</h2>

        <!-- Display error message if any -->
        <?php if (!empty($error)): ?>
            <p class="text-danger"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <!-- Display success message -->
        <?php if (isset($_SESSION['success'])): ?>
            <p class="text-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></p>
        <?php endif; ?>

        <form action="update-settings.php" method="POST">
            <div class="form-group">
                <label for="email">New Email:</label>
                <input type="email" name="email" class="form-control" id="email" required value="<?php echo htmlspecialchars($email ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="password">New Password (optional):</label>
                <input type="password" name="password" class="form-control" id="password">
            </div>
            <button type="submit" class="btn btn-primary">Update Settings</button>
        </form>
    </div>
</body>
</html>
<footer>
    <div class="footer-container">
        <p>&copy; <?php echo date("Y"); ?> Mom's Nature Task Management System. All rights reserved.</p>
    </div>
</footer>