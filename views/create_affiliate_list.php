<?php
session_start();
require_once '../includes/db_connect.php';

// Get the username from the session to greet the user in the header
$username = htmlspecialchars($_SESSION['username']); // Get the username

// Include the header after fetching the username
include '../views/header.php';
?>

    <h2>Create a New Affiliate List</h2>

    <form action="../forms/create_affiliate_list.php" method="POST">
        <label for="list_name">List Name:</label>
        <input type="text" id="list_name" name="list_name" required><br>

        <!-- Add multiple items dynamically via JavaScript -->
        <div id="new-item-container">
            <!-- New items will be added here dynamically -->
        </div>

        <button type="button" onclick="addNewItem()">Add Item</button><br>
        <input type="submit" class="button button-green" value="Create List">
    </form>

    <script type="text/javascript" src="../js/add_affiliate_item.js"></script>

<?php
// Include the footer
include '../views/footer.php';
?>