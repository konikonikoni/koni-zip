<?php
session_start();
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $list_name = htmlspecialchars($_POST['list_name']);

    // Insert the list
    $stmt = $conn->prepare("INSERT INTO affiliate_lists (user_id, list_name) VALUES (?, ?)");
    $stmt->bind_param('is', $user_id, $list_name);
    $stmt->execute();
    $list_id = $conn->insert_id;  // Get the newly created list ID

    // Insert each item in the list
    $positions = $_POST['position'];
    $names = $_POST['name'];
    $links = $_POST['link'];
    $descriptions = $_POST['description'];

    for ($i = 0; $i < count($names); $i++) {
        $position = $positions[$i];
        $name = htmlspecialchars($names[$i]);
        $link = htmlspecialchars($links[$i]);
        $description = htmlspecialchars($descriptions[$i]);

        $stmt = $conn->prepare("INSERT INTO affiliate_items (list_id, position, name, link, description) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('iisss', $list_id, $position, $name, $link, $description);
        $stmt->execute();
    }

    redirect('../views/dashboard.php', "Affiliate list created successfully!");
}

$stmt->close();
$conn->close();
