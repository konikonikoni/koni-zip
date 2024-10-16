<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If not, redirect to the login page
    header("Location: /index.php");
    exit();
}

// Get role from $_SESSION
$role = htmlspecialchars($_SESSION['role']);
$username = htmlspecialchars($_SESSION['username']);

echo "Welcome to your dashboard, " . $username;
echo "<a href='../actions/logout.php'>Logout</a>";