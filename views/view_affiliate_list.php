<?php
session_start();
include '../includes/db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /index.php");
    exit();
}

// Get the username and role from the session to greet the user in the header
$username = htmlspecialchars($_SESSION['username']);
$role = htmlspecialchars($_SESSION['role']); // Get user role

// Include the header after fetching the username
include '../views/header.php';

// Get the list ID from the URL
if (isset($_GET['list_id'])) {
    $list_id = $_GET['list_id'];

    // Prepare the SQL query differently based on the role
    if ($role === 'admin') {
        // Admin can view any list
        $stmt = $conn->prepare("SELECT affiliate_lists.*, users.username AS creator FROM affiliate_lists JOIN users ON affiliate_lists.user_id = users.id WHERE list_id = ?");
        $stmt->bind_param('i', $list_id);
    } else {
        // Slot users can only view their own lists
        $stmt = $conn->prepare("SELECT * FROM affiliate_lists WHERE list_id = ? AND user_id = ?");
        $stmt->bind_param('ii', $list_id, $_SESSION['user_id']);
    }

    $stmt->execute();
    $list_result = $stmt->get_result();

    if ($list_result->num_rows > 0) {
        // List found, display its items
        $list = $list_result->fetch_assoc();
        $list_name = htmlspecialchars($list['list_name']);
        $creator = isset($list['creator']) ? htmlspecialchars($list['creator']) : ''; // Creator name if admin

        echo "<a href='/views/dashboard.php' class='button'>Back to Dashboard</a>";
        echo "<h2>$list_name</h2>";

        // If admin, show who created the list
        if ($role === 'admin') {
            echo "<p>Created by: $creator</p>";
        }

        // Fetch the list items
        $stmt = $conn->prepare("SELECT * FROM affiliate_items WHERE list_id = ? ORDER BY position ASC");
        $stmt->bind_param('i', $list_id);
        $stmt->execute();
        $items_result = $stmt->get_result();

        if ($items_result->num_rows > 0) {
            echo "<table border='1'>
                    <tr>
                        <th>Position</th>
                        <th>Name</th>
                        <th>Link</th>
                        <th>Description</th>
                    </tr>";
            while ($item = $items_result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($item['position']) . "</td>";
                echo "<td>" . htmlspecialchars($item['name']) . "</td>";
                echo "<td><a href='" . htmlspecialchars($item['link']) . "'>Visit</a></td>";
                echo "<td>" . htmlspecialchars($item['description']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No items found in this list.</p>";
        }

    } else {
        echo "<p>List not found or access denied.</p>";
    }

} else {
    echo "<p>No list ID provided.</p>";
}

$stmt->close();
$conn->close();

// Include the footer
include '../views/footer.php';
?>
