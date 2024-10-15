<?php
// Get the current request URI
$request = trim($_SERVER['REQUEST_URI'], '/');
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
                <form method="POST">
                    <input type="text" placeholder="username" id="username" name="username">
                    <input type="password" placeholder="password" id="password" name="password">
                    <input type="password" placeholder="retype password" id="retype_password" name="retype_password">
                    <div id="submit">
                        <input type="submit" value="Sign up">
                        <input type="button" value="Already signed up" onclick="location.href='/'">
                    </div>
                </form>
            </div>
        <?php else: ?>
            <div id="login">
                <form method="POST">
                    <input type="text" placeholder="username" id="username" name="username">
                    <input type="password" placeholder="password" id="password" name="password">
                    <div id="submit">
                        <input type="submit" value="Log In">
                        <input type="button" value="Sign up" onclick="location.href='/register'">
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>
