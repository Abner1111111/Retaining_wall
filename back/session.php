<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: index.php"); // Redirect to the login page
    exit();
}

// User is logged in; you can access session data

?>
