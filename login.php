<?php
session_start();
include('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password']; // Encrypt password

    $sql = "SELECT users.id, users.email, users.role_id, roles.role_name 
            FROM users 
            JOIN roles ON users.role_id = roles.role_id 
            WHERE users.username = ? AND users.password = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        // Store username, role_id, and role_name in session
        $_SESSION['username'] = $username;
        $_SESSION['role_id'] = $user['role_id'];
        $_SESSION['role_name'] = $user['role_name']; // Store role_name in session
        header("Location: home.php"); // Redirect to dashboard on successful login
        exit();
    } else {
        $error = "Invalid username,email or password."; // Set error message
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #FEFAE0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: #C0C78C;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
        }
        h2 {
            color: #B99470;
            margin-bottom: 20px;
            text-align: center;
        }
        .form-control {
            background-color: #A6B37D;
            border: 1px solid #B99470;
            color: #FEFAE0;
        }
        .btn-primary {
            background-color: #B99470;
            border-color: #B99470;
            color: #FEFAE0;
            width: 100%;
        }
        .btn-primary:hover {
            background-color: #45712f;
            border-color: #A6B37D;
            color: #FEFAE0;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Login</h2>

    <form action="login.php" method="POST">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="pwd">Password:</label>
            <input type="password" class="form-control" id="pwd" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
    </form><br>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>
    
</div>
  
</body>
</html>
