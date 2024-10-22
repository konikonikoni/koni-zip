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
