<?php
session_start();
require_once __DIR__ . '/../includes/db_connect.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $list_id = $_POST['list_id'];
    $user_id = $_SESSION['user_id'];  // Get the ID of the user making the change
    $list_name = htmlspecialchars($_POST['list_name']);

    // Update the list name
    $stmt = $conn->prepare("UPDATE affiliate_lists SET list_name = ? WHERE list_id = ?");
    $stmt->bind_param('si', $list_name, $list_id);
    $stmt->execute();

    // Handle item deletion
    if (isset($_POST['delete_item'])) {
        $delete_item_ids = $_POST['delete_item'];

        // Log the deletion before deleting the item from the affiliate_items table
        foreach ($delete_item_ids as $delete_item_id) {
            // Log the deletion (optional, for tracking)
            log_change($list_id, $delete_item_id, $user_id, 'item_delete', '', '', 'item_delete', $conn);

            // Now delete the marked items from the affiliate_items table
            $stmt = $conn->prepare("DELETE FROM affiliate_items WHERE item_id = ?");
            $stmt->bind_param('i', $delete_item_id);
            $stmt->execute();
        }
    }

    // Fetch current values before updating, to log changes if necessary
    $stmt = $conn->prepare("SELECT * FROM affiliate_items WHERE list_id = ?");
    $stmt->bind_param('i', $list_id);
    $stmt->execute();
    $current_items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Map current items by item_id for easy comparison
    $current_items_map = [];
    foreach ($current_items as $item) {
        $current_items_map[$item['item_id']] = $item;
    }

    // Update existing items and log changes
    if (isset($_POST['item_id'])) {
        $item_ids = $_POST['item_id'];
        $positions = $_POST['position'];
        $names = $_POST['name'];
        $links = $_POST['link'];
        $descriptions = $_POST['description'];

        for ($i = 0; $i < count($item_ids); $i++) {
            $item_id = $item_ids[$i];
            $position = $positions[$i];
            $name = htmlspecialchars($names[$i]);
            $link = htmlspecialchars($links[$i]);
            $description = htmlspecialchars($descriptions[$i]);

            // Check if the current item exists in the map
            if (isset($current_items_map[$item_id])) {
                $current_item = $current_items_map[$item_id];

                // Compare current values with updated values and log changes
                if ($current_item['position'] != $position) {
                    log_change($list_id, $item_id, $user_id, 'position', $current_item['position'], $position, 'item_update', $conn);
                }
                if ($current_item['name'] != $name) {
                    log_change($list_id, $item_id, $user_id, 'name', $current_item['name'], $name, 'item_update', $conn);
                }
                if ($current_item['link'] != $link) {
                    log_change($list_id, $item_id, $user_id, 'link', $current_item['link'], $link, 'item_update', $conn);
                }
                if ($current_item['description'] != $description) {
                    log_change($list_id, $item_id, $user_id, 'description', $current_item['description'], $description, 'item_update', $conn);
                }

                // Update the existing item in the database
                $stmt = $conn->prepare("UPDATE affiliate_items SET position = ?, name = ?, link = ?, description = ? WHERE item_id = ?");
                $stmt->bind_param('isssi', $position, $name, $link, $description, $item_id);
                $stmt->execute();
            }
        }
    }

    // Insert new items
    if (isset($_POST['new_position'])) {
        $new_positions = $_POST['new_position'];
        $new_names = $_POST['new_name'];
        $new_links = $_POST['new_link'];
        $new_descriptions = $_POST['new_description'];

        for ($i = 0; $i < count($new_names); $i++) {
            $new_position = $new_positions[$i];
            $new_name = htmlspecialchars($new_names[$i]);
            $new_link = htmlspecialchars($new_links[$i]);
            $new_description = htmlspecialchars($new_descriptions[$i]);

            // Insert each new item
            $stmt = $conn->prepare("INSERT INTO affiliate_items (list_id, position, name, link, description) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param('iisss', $list_id, $new_position, $new_name, $new_link, $new_description);
            $stmt->execute();

            // Log the newly added item
            $new_item_id = $stmt->insert_id;
            log_change($list_id, $new_item_id, $user_id, 'position', '', $new_position, 'item_add', $conn);
            log_change($list_id, $new_item_id, $user_id, 'name', '', $new_name, 'item_add', $conn);
            log_change($list_id, $new_item_id, $user_id, 'link', '', $new_link, 'item_add', $conn);
            log_change($list_id, $new_item_id, $user_id, 'description', '', $new_description, 'item_add', $conn);
        }
    }

    // Redirect after success
    header("Location: /views/dashboard.php");
    exit();
}

// Close the statement and connection
$stmt->close();
$conn->close();