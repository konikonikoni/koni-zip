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
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="100" height="100">
                <!-- Folder Shape -->
                <rect x="10" y="30" width="80" height="50" fill="none" stroke="black" stroke-width="2"/>

                <!-- Folder Tab -->
                <rect x="10" y="20" width="30" height="10" fill="none" stroke="black" stroke-width="2"/>

                <!-- Zipper -->
                <rect x="50.5" y="30" width="2" height="50" fill="black"/>
                <rect x="55" y="32.5" width="5" height="2.5" fill="black"/>
                <rect x="46" y="35" width="5" height="2.5" fill="black"/>
                <rect x="55" y="37.5" width="5" height="2.5" fill="black"/>
                <rect x="46" y="40" width="5" height="2.5" fill="black"/>
                <rect x="55" y="42.5" width="5" height="2.5" fill="black"/>
                <rect x="46" y="45" width="5" height="2.5" fill="black"/>
                <rect x="55" y="47.5" width="5" height="2.5" fill="black"/>
                <rect x="46" y="50" width="5" height="2.5" fill="black"/>
                <rect x="55" y="52.5" width="5" height="2.5" fill="black"/>
                <rect x="46" y="55" width="5" height="2.5" fill="black"/>
                <rect x="55" y="57.5" width="5" height="2.5" fill="black"/>
                <rect x="46" y="60" width="5" height="2.5" fill="black"/>
                <rect x="55" y="62.5" width="5" height="2.5" fill="black"/>
                <rect x="46" y="65" width="5" height="2.5" fill="black"/>
                <rect x="55" y="67.5" width="5" height="2.5" fill="black"/>
                <rect x="46" y="70" width="5" height="2.5" fill="black"/>
                <rect x="55" y="72.5" width="5" height="2.5" fill="black"/>
                <rect x="46" y="75" width="5" height="2.5" fill="black"/>
            </svg>
        </div>
        <?php if ($request === 'register'): ?>
            <div id="register">
                <form>
                    <input type="text" placeholder="username" id="username" name="username">
                    <input type="password" placeholder="password" id="password" name="password">
                    <input type="password" placeholder="password" id="password" name="password">
                    <div id="submit">
                        <input type="submit" value="Sign Up">
                    </div>
                </form>
            </div>
        <?php else: ?>
            <div id="login">
                <form>
                    <input type="text" placeholder="username" id="username" name="username">
                    <input type="password" placeholder="password" id="password" name="password">
                    <div id="submit">
                        <input type="submit" value="Log In">
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>
