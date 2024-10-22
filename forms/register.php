<?php
// Include the database connection
require_once __DIR__ . '/../includes/db_connect.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['username']) && !empty($_POST['password']) && !empty($_POST['retype_password'])) {
        if ($_POST['password'] === $_POST['retype_password']) {
            $username = htmlspecialchars($_POST['username']);
            $password = $_POST['password'];

            // Check if the email or nickname already exists
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? OR username = ?");
            $stmt->bind_param('ss', $email, $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                handleError('/register', 'Email or nickname already exists!');
            } else {
                // Insert into users and user_credentials
                $stmt = $conn->prepare("INSERT INTO users (username) VALUES (?)");
                $stmt->bind_param('s',$username);
                $stmt->execute();
                $user_id = $conn->insert_id;
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $conn->prepare("INSERT INTO user_credentials (user_id, password) VALUES (?, ?)");
                $stmt->bind_param('is', $user_id, $hashed_password);
                $stmt->execute();
                redirect('index.php', 'User registered successfully!');
            }
            $stmt->close();
        } else {
            handleError('/register', 'Passwords do not match!');
        }
    } else {
        handleError('index.php', 'Required parameters missing.');
    }
}
$conn->close();
?>