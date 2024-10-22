<?php
// Redirect helper function
function redirect($url, $message, $delay = 3) {
    echo $message;
    echo "<meta http-equiv='refresh' content='{$delay}; url={$url}'>";
    exit();  // Stop further script execution after redirection
}

// Error handling helper function
function handleError($url, $message) {
    redirect($url, $message);
}

// Function to log changes
function log_change($list_id, $item_id, $user_id, $field, $old_value, $new_value, $change_type, $conn) {
    $stmt = $conn->prepare("INSERT INTO affiliate_list_changes (list_id, item_id, user_id, field_changed, old_value, new_value, change_type) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('iiissss', $list_id, $item_id, $user_id, $field, $old_value, $new_value, $change_type);
    $stmt->execute();
}

// Fetch the most recent changes for the item
function get_last_change($list_id, $item_id, $field, $conn) {
    $stmt = $conn->prepare("SELECT new_value FROM affiliate_list_changes WHERE list_id = ? AND item_id = ? AND field_changed = ? ORDER BY change_date DESC LIMIT 1");
    $stmt->bind_param('iis', $list_id, $item_id, $field);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc()['new_value'] ?? null;
}