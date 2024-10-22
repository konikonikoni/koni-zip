<?php
session_start();
require_once '../includes/db_connect.php';

// Get the list ID from the URL
$list_id = $_GET['list_id'];
$role = $_SESSION['role'];  // Get the user's role
$username = htmlspecialchars($_SESSION['username']); // Get the username for greeting

// Include the header after fetching the username
include '../views/header.php';

// Prepare the SQL query differently based on the role
if ($role === 'admin') {
    // Admin can edit any list
    $stmt = $conn->prepare("SELECT affiliate_lists.*, users.username AS creator FROM affiliate_lists JOIN users ON affiliate_lists.user_id = users.id WHERE list_id = ?");
    $stmt->bind_param('i', $list_id);
} else {
    // Slot users can only edit their own lists
    $stmt = $conn->prepare("SELECT * FROM affiliate_lists WHERE list_id = ? AND user_id = ?");
    $stmt->bind_param('ii', $list_id, $_SESSION['user_id']);
}

$stmt->execute();
$list_result = $stmt->get_result();

// Check if the list exists and belongs to the user (for slot role) or is accessible to the admin
if ($list_result->num_rows === 1) {
    $list = $list_result->fetch_assoc();
    $list_name = htmlspecialchars($list['list_name']);

    // Fetch the existing items in the list
    $stmt = $conn->prepare("SELECT * FROM affiliate_items WHERE list_id = ? ORDER BY position ASC");
    $stmt->bind_param('i', $list_id);
    $stmt->execute();
    $items_result = $stmt->get_result();
} else {
    echo "List not found or access denied.";
    exit();
}
?>
    <a href='/views/dashboard.php' class="button">Back to Dashboard</a>
    <h2>Edit Affiliate List: <?php echo $list_name; ?></h2>

    <!-- Form for editing the list -->
    <form action="/forms/update_affiliate_list.php" method="POST">
        <input type="hidden" name="list_id" value="<?php echo $list_id; ?>">

        <label for="list_name">List Name:</label>
        <input type="text" id="list_name" name="list_name" value="<?php echo $list_name; ?>" required><br>

        <h3>Edit Existing Items</h3>
        <div id="item-container">
            <?php while ($item = $items_result->fetch_assoc()): ?>
                <div class="item">
                    <input type="hidden" name="item_id[]" value="<?php echo $item['item_id']; ?>">

                    <label for="position">Position:</label>
                    <input type="number" name="position[]" value="<?php echo $item['position']; ?>" required><br>

                    <label for="name">Name:</label>
                    <input type="text" name="name[]" value="<?php echo htmlspecialchars($item['name']); ?>" required><br>

                    <label for="link">Link:</label>
                    <input type="url" name="link[]" value="<?php echo htmlspecialchars($item['link']); ?>" required><br>

                    <label for="description">Description:</label>
                    <textarea name="description[]"><?php echo htmlspecialchars($item['description']); ?></textarea><br>
                </div>
            <?php endwhile; ?>
        </div>

        <div id="new-item-container">
            <!-- New items will be added here dynamically -->
        </div>

        <button type="button" onclick="addNewItem()">Add Another Item</button><br><br>
        <input type="submit" class="button button-green" value="Save Changes">
    </form>

    <script type="text/javascript" src="../js/add_affiliate_item.js"></script>

<?php
$stmt->close();
$conn->close();

// Include the footer
include '../views/footer.php';
?>