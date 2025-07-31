<?php
session_start();
session_destroy(); // Destroy the session
//session_start(); // Start a new session to set the message
$_SESSION['logout_message'] = "You have been logged out successfully."; // Set the logout message
header("Location: login.php"); // Redirect to login page after logout
exit;
?>
