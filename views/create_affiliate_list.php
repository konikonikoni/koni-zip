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
        <div id="item-container">
            <div class="item">
                <label for="position">Position:</label>
                <input type="number" name="position[]" required><br>

                <label for="name">Name:</label>
                <input type="text" name="name[]" required><br>

                <label for="link">Link:</label>
                <input type="url" name="link[]" required><br>

                <label for="description">Description:</label>
                <textarea name="description[]"></textarea><br>
            </div>
        </div>

        <button type="button" onclick="addNewItem()">Add Another Item</button><br>
        <input type="submit" class="button button-green" value="Create List">
    </form>

    <script type="text/javascript" src="../js/add_affiliate_item.js"></script>

<?php
// Include the footer
include '../views/footer.php';
?>