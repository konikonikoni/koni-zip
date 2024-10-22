<?php
// Start the session
session_start();

// Get the current request URI
$request = trim($_SERVER['REQUEST_URI'], '/');

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    // If the user is logged in, redirect to the dashboard
    header("Location: /views/dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="/css/style.css">
    <title>koni.zip</title>
</head>
<body>
<main>
    <div id="svg">
        <img src="/images/folder.svg" alt="zip-Folder">
    </div>

    <?php if ($request === 'register'): ?>
        <div id="register">
            <form method="POST" action="/forms/register.php">
                <input type="text" placeholder="username" id="username" name="username" required>
                <input type="password" placeholder="password" id="password" name="password" required>
                <input type="password" placeholder="retype password"
                       id="retype_password" name="retype_password" required>
                <div id="submit">
                    <input type="submit" value="Sign Up">
                    <input type="button" value="Log In" onclick="location.href='/'">
                </div>
            </form>
        </div>
    <?php else: ?>
        <div id="login">
            <form method="POST" action="/forms/login.php">
                <input type="text" placeholder="username" id="username" name="username" required>
                <input type="password" placeholder="password" id="password" name="password" required>
                <div id="submit">
                    <input type="submit" value="Log In">
                    <input type="button" value="Sign Up" onclick="location.href='/register'">
                </div>
            </form>
        </div>
    <?php endif; ?>
</main>
</body>
</html>