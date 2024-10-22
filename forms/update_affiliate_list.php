<?php
session_start();
require_once __DIR__ . '/../includes/db_connect.php';  // Database connection
require_once __DIR__ . '/../includes/functions.php';  // Custom functions (for logging)

// Ensure the request is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the necessary POST data
    $list_id = $_POST['list_id'];
    $user_id = $_SESSION['user_id'];  // Get the ID of the user making the change
    $list_name = htmlspecialchars($_POST['list_name']);  // Sanitize input

    // Update the list name
    $stmt = $conn->prepare("UPDATE affiliate_lists SET list_name = ? WHERE list_id = ?");
    $stmt->bind_param('si', $list_name, $list_id);
    $stmt->execute();

    // Fetch current values of the affiliate items before updating for comparison
    $stmt = $conn->prepare("SELECT * FROM affiliate_items WHERE list_id = ?");
    $stmt->bind_param('i', $list_id);
    $stmt->execute();
    $current_items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Create a map of current items by item_id for easy comparison later
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

            // Retrieve the current values for the item
            $current_item = $current_items_map[$item_id];

            // Compare current values with updated values and log only if changed
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

    // Insert new items (if any)
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
            $new_item_id = $stmt->insert_id;  // Get the ID of the new item
            log_change($list_id, $new_item_id, $user_id, 'position', '', $new_position, 'item_add', $conn);
            log_change($list_id, $new_item_id, $user_id, 'name', '', $new_name, 'item_add', $conn);
            log_change($list_id, $new_item_id, $user_id, 'link', '', $new_link, 'item_add', $conn);
            log_change($list_id, $new_item_id, $user_id, 'description', '', $new_description, 'item_add', $conn);
        }
    }

    // Provide a success message and redirect the user to the dashboard
    echo "Affiliate list updated successfully!";
    header("Location: /views/dashboard.php");
    exit();
}

// Close the prepared statement and database connection
$stmt->close();
$conn->close();
?>