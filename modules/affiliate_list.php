<?php
// Fetch the user's affiliate lists based on role
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];  // Get role from session

if ($role === 'admin') {
    // Admins can see all affiliate lists and see who created them
    $stmt = $conn->prepare("
        SELECT affiliate_lists.*, users.username AS creator 
        FROM affiliate_lists 
        JOIN users ON affiliate_lists.user_id = users.id
    ");
} elseif ($role === 'slot') {
    // Slot users can only see their own lists
    $stmt = $conn->prepare("SELECT * FROM affiliate_lists WHERE user_id = ?");
    $stmt->bind_param('i', $user_id);
} else {
    // Other roles have no access to lists
    echo "<p>You do not have permission to view or generate affiliate lists.</p>";
    return; // Stop further execution of this file
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($list = $result->fetch_assoc()) {
        $list_id = $list['list_id'];
        $list_name = htmlspecialchars($list['list_name']);
        $creator = isset($list['creator']) ? htmlspecialchars($list['creator']) : '';

        echo "<div class='affiliate-list'>";
        echo "<h3>$list_name</h3>";

        // For admin, show who created the list
        if ($role === 'admin') {
            echo "<p>Created by: $creator</p>";
        }

        // View button to see all items in the list
        echo "<a class='button' href='/views/view_affiliate_list.php?list_id=$list_id'>View</a> ";

        // Edit button (only for "slot" users on their own lists or for "admin" for all lists)
        if ($role === 'slot' || $role === 'admin') {
            echo "<a class='button' href='/views/edit_affiliate_list.php?list_id=$list_id'>Edit</a> ";
        }

        // Delete button (only for "slot" users on their own lists or for "admin" for all lists)
        if ($role === 'slot' || $role === 'admin') {
            echo "<form action='/forms/delete_affiliate_list.php' method='POST' style='display:inline;'>
                    <input type='hidden' name='list_id' value='$list_id'>
                    <button type='submit' class='button-red' onclick='return confirm(\"Are you sure you want to delete this list?\")'>Delete</button>
                  </form>";
        }

        echo "</div>";
    }

    // Only slot and admin can create new lists
    if ($role === 'slot' || $role === 'admin') {
        echo "<a class='button button-green' href='/views/create_affiliate_list.php'>Create a new list</a>";
    }
} else {
    // If no lists found
    if ($role === 'slot' || $role === 'admin') {
        echo "<p>No affiliate lists found. <a class='button button-green' href='/views/create_affiliate_list.php'>Create a new list</a></p>";
    } else {
        echo "<p>No affiliate lists available.</p>";
    }
}

$stmt->close();
?>