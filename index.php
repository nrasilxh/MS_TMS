<?php
session_start();
if (isset($_SESSION['username'])) {
    header("Location: home.php"); // Redirect to home page if already logged in
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
 body {
            background-color: #839169; /* Main background color */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: #cfdaba; /* Container background color */
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
            text-align: center;
        }
        h1 {
            color: #B99470; /* Heading color */
            margin-bottom: 20px;
        }
        .btn-primary {
            background-color: #B99470; /* Button background color */
            border-color: #B99470; /* Button border color */
            color: #FEFAE0; /* Button text color */
            padding: 10px 20px;
            font-size: 18px;
        }
        .btn-primary:hover {
            background-color: #45712f; /* Button hover background color */
            border-color: #A6B37D; /* Button hover border color */
            color: #FEFAE0; /* Button hover text color */
        }
        h1 {
        color: #744700;
        margin-bottom: 20px;
        font-size: 36px;
        font-weight: bold;
        font-family: 'Poppins', sans-serif;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        letter-spacing: 1px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Logo Image -->
    <img src="images\Logo-momsnature-color.png" alt="Logo" style="width: 150px; margin-bottom: 20px;">
        <h1>Welcome to Mom's Nature TMS</h1>
        <p>Please log in to continue.</p>
        <a href="login.php" class="btn btn-primary">Login</a>
    </div>
</body>
</html>
