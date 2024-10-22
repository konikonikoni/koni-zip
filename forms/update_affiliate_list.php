<?php
session_start();
require_once '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $list_id = $_POST['list_id'];
    $list_name = htmlspecialchars($_POST['list_name']);

    // Update the list name
    $stmt = $conn->prepare("UPDATE affiliate_lists SET list_name = ? WHERE list_id = ?");
    $stmt->bind_param('si', $list_name, $list_id);
    $stmt->execute();

    // Update existing items
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

            // Update each existing item
            $stmt = $conn->prepare("UPDATE affiliate_items SET position = ?, name = ?, link = ?, description = ? WHERE item_id = ?");
            $stmt->bind_param('isssi', $position, $name, $link, $description, $item_id);
            $stmt->execute();
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
        }
    }

    echo "Affiliate list updated successfully!";
    header("Location: /views/dashboard.php");
    exit();
}

$stmt->close();
$conn->close();