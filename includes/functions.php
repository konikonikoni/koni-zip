<?php
// Redirect helper function
function redirect($url, $message, $delay = 3) {
    echo htmlspecialchars($message);
    echo "<meta http-equiv='refresh' content='{$delay}; url=" . htmlspecialchars($url) . "'>";
    exit();  // Stop further script execution after redirection
}

// Error handling helper function
function handleError($url, $message) {
    redirect($url, $message);
}

// Function to log changes
function log_change($list_id, $item_id, $user_id, $field_changed, $old_value, $new_value, $change_type, $conn) {
    // Store results of htmlspecialchars in variables
    $field_changed_safe = htmlspecialchars($field_changed);
    $old_value_safe = htmlspecialchars($old_value);
    $new_value_safe = htmlspecialchars($new_value);
    $change_type_safe = htmlspecialchars($change_type);

    // Proceed with logging the change
    $stmt = $conn->prepare("
        INSERT INTO affiliate_list_changes (list_id, item_id, user_id, field_changed, old_value, new_value, change_type)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param('iiissss', $list_id, $item_id, $user_id, $field_changed_safe, $old_value_safe, $new_value_safe, $change_type_safe);
    $stmt->execute();
    $stmt->close();
}

// Fetch the most recent changes for the item
function get_last_change($list_id, $item_id, $field, $conn) {
    $stmt = $conn->prepare("SELECT new_value FROM affiliate_list_changes WHERE list_id = ? AND item_id = ? AND field_changed = ? ORDER BY change_date DESC LIMIT 1");
    $stmt->bind_param('iis', $list_id, $item_id, $field);
    if ($stmt->execute()) {
        $stmt->bind_result($new_value);
        $stmt->fetch();
        $stmt->close();
        return $new_value;
    } else {
        return null;  // Return null in case of an error
    }
}
?>