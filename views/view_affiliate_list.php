<?php
session_start();
include __DIR__ . '/../includes/db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /index.php");
    exit();
}

// Get the username and role from the session
$username = htmlspecialchars($_SESSION['username']);
$role = htmlspecialchars($_SESSION['role']); // Get user role

// Include the header file (which likely contains the site header and greeting)
include __DIR__ . '/../views/header.php';

// Check if a list ID is provided in the URL
if (isset($_GET['list_id'])) {
    $list_id = $_GET['list_id'];

    // Prepare SQL query based on user role
    if ($role === 'admin') {
        // Admins can view all lists, including who created the list
        $stmt = $conn->prepare("
            SELECT affiliate_lists.*, users.username AS creator 
            FROM affiliate_lists 
            JOIN users ON affiliate_lists.user_id = users.id 
            WHERE list_id = ?
        ");
        $stmt->bind_param('i', $list_id);
    } else {
        // Non-admin users can only view their own lists
        $stmt = $conn->prepare("SELECT * FROM affiliate_lists WHERE list_id = ? AND user_id = ?");
        $stmt->bind_param('ii', $list_id, $_SESSION['user_id']);
    }

    // Execute the statement and fetch the list result
    $stmt->execute();
    $list_result = $stmt->get_result();

    // If the list exists
    if ($list_result->num_rows > 0) {
        $list = $list_result->fetch_assoc();
        $list_name = htmlspecialchars($list['list_name']);
        $creator = isset($list['creator']) ? htmlspecialchars($list['creator']) : ''; // Only for admin

        // Display the list title and creator (if admin)
        echo "<a href='/views/dashboard.php' class='button'>Back to Dashboard</a>";
        echo "<h2>$list_name</h2>";

        if ($role === 'admin') {
            echo "<p>Created by: $creator</p>";
        }

        // Fetch the affiliate list items ordered by position
        $stmt = $conn->prepare("SELECT * FROM affiliate_items WHERE list_id = ? ORDER BY position ASC");
        $stmt->bind_param('i', $list_id);
        $stmt->execute();
        $items_result = $stmt->get_result();

        // Fetch all changes made within 12 hours of the most recent change, including the most recent change
        $stmt_changes = $conn->prepare("
            SELECT alc.item_id, alc.field_changed
            FROM affiliate_list_changes alc
            WHERE alc.list_id = ?
            AND alc.change_date BETWEEN (
                SELECT MAX(change_date) 
                FROM affiliate_list_changes 
                WHERE list_id = ?
            ) - INTERVAL 12 HOUR
            AND (
                SELECT MAX(change_date) 
                FROM affiliate_list_changes 
                WHERE list_id = ?
            )
        ");
        $stmt_changes->bind_param('iii', $list_id, $list_id, $list_id);  // Bind the list_id parameter three times
        $stmt_changes->execute();
        $changes_result = $stmt_changes->get_result();

        // Create an array to track which fields were changed for each item
        $changed_items = [];
        while ($change = $changes_result->fetch_assoc()) {
            $changed_items[$change['item_id']][$change['field_changed']] = true;
        }

        // If items exist in the list
        if ($items_result->num_rows > 0) {
            echo "<table border='1'>
                    <tr>
                        <th>Position</th>
                        <th>Name</th>
                        <th>Link</th>
                        <th>Description</th>
                    </tr>";

            // Loop through the items and display them
            while ($item = $items_result->fetch_assoc()) {
                $item_id = $item['item_id'];

                // Set the background color to light green if a field was changed
                $position_style = isset($changed_items[$item_id]['position']) ? "background-color: lightgreen;" : "";
                $name_style = isset($changed_items[$item_id]['name']) ? "background-color: lightgreen;" : "";
                $link_style = isset($changed_items[$item_id]['link']) ? "background-color: lightgreen;" : "";
                $description_style = isset($changed_items[$item_id]['description']) ? "background-color: lightgreen;" : "";

                // Display each item row with the appropriate styles for changes
                echo "<tr>";
                echo "<td style='$position_style'>" . htmlspecialchars($item['position']) . "</td>";
                echo "<td style='$name_style'>" . htmlspecialchars($item['name']) . "</td>";
                echo "<td style='$link_style'><a href='" . htmlspecialchars($item['link']) . "'>Visit</a></td>";
                echo "<td style='$description_style'>" . htmlspecialchars($item['description']) . "</td>";
                echo "</tr>";
            }

            echo "</table>";
        } else {
            // If no items are found
            echo "<p>No items found in this list.</p>";
        }
    } else {
        // If the list is not found or access is denied
        echo "<p>List not found or access denied.</p>";
    }
} else {
    // If no list ID is provided in the URL
    echo "<p>No list ID provided.</p>";
}

// Close the prepared statement and database connection
$stmt->close();
$conn->close();

// Include the footer (presumably contains site footer and closing tags)
include __DIR__ . '/../views/footer.php';
?>