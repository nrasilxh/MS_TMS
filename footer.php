<?
session_start(); // Start session at the beginning of each page
if (!isset($_SESSION['username'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit;
}
?><!DOCTYPE html>
<footer>
    <div class="footer-container">
        <p>&copy; <?php echo date("Y"); ?> Mom's Nature Task Management System. All rights reserved.</p>
    </div>
</footer>

<style>
footer {
    color: #000; /* Change text color to black */
    padding: 20px 0;
    background-color: transparent; /* Remove background color */
    position: fixed; /* Make footer fixed at the bottom */
    bottom: 0;
    width: 100%; /* Make sure the footer spans the whole width */
    left: 0; /* Ensure footer aligns with the left edge */
    transition: all 0.3s ease; /* Smooth transition when sidebar is toggled */
}

.footer-container {
    max-width: 1300px;
    margin: 0 auto;
    padding: 0; /* Remove horizontal padding to eliminate gap */
}

.footer-container p {
    margin: 0;
    font-size: 16px;
    text-align: left; /* Align text to the left */
}


.footer-links li {
    display: inline;
}

.footer-links a {
    color: #000; /* Change link color to black */
    text-decoration: none;
    font-weight: 600;
    transition: color 0.3s;
}

.footer-links a:hover {
    color: #B99470; /* Hover effect (can keep the original color or adjust) */
}

/* Adjust content and sidebar for smooth transition when sidebar is shown */
.content.shift + footer {
    margin-left: 250px; /* Moves footer when sidebar is toggled */
}
</style>
