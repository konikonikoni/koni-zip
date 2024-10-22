<?php
session_start();
require_once __DIR__ . '/../includes/db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /index.php");
    exit();
}

// Get role and username from the session
$role = htmlspecialchars($_SESSION['role']);
$username = htmlspecialchars($_SESSION['username']);

// Include the header
include '../views/header.php';

// Include the affiliate list module
include '../modules/affiliate_list.php';

// Include the footer
include '../views/footer.php';

$conn->close();
?>