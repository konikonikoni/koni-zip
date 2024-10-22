<?php
session_start();
include '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['list_id'])) {
    $list_id = $_POST['list_id'];

    // Check if the list belongs to the logged-in user
    $stmt = $conn->prepare("SELECT * FROM affiliate_lists WHERE list_id = ? AND user_id = ?");
    $stmt->bind_param('ii', $list_id, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        // Delete the list and all associated items
        $stmt = $conn->prepare("DELETE FROM affiliate_lists WHERE list_id = ?");
        $stmt->bind_param('i', $list_id);
        $stmt->execute();

        // Optionally, you can delete associated affiliate items in a single step if there's a foreign key constraint
        // DELETE FROM affiliate_items WHERE list_id = ?;

        echo "List deleted successfully.";
    } else {
        echo "You are not authorized to delete this list.";
    }

    $stmt->close();
}
$conn->close();

// Redirect back to the dashboard
header("Location: /views/dashboard.php");
exit();