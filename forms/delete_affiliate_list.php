<?php
session_start();
require_once '../includes/db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /index.php");
    exit();
}

$role = $_SESSION['role']; // Get the user's role
$list_id = $_POST['list_id']; // Get the list ID to delete

// If the user is an admin, they can delete any list
if ($role === 'admin') {
    // Admin can delete any list
    $stmt = $conn->prepare("DELETE FROM affiliate_lists WHERE list_id = ?");
    $stmt->bind_param('i', $list_id);
} else {
    // Non-admin users can only delete their own lists
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("DELETE FROM affiliate_lists WHERE list_id = ? AND user_id = ?");
    $stmt->bind_param('ii', $list_id, $user_id);
}

// Execute the query
if ($stmt->execute()) {
    // Optionally, delete associated items in affiliate_items
    $stmt_items = $conn->prepare("DELETE FROM affiliate_items WHERE list_id = ?");
    $stmt_items->bind_param('i', $list_id);
    $stmt_items->execute();

    // Redirect to the dashboard after successful deletion
    header("Location: /views/dashboard.php?message=List deleted successfully");
} else {
    // Handle failure to delete
    echo "Failed to delete the list.";
}

$stmt->close();
$conn->close();
?>