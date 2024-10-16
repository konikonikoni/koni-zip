<?php
// Include the database connection
require_once __DIR__ . '/../includes/db_connect.php';
require_once __DIR__ . '/../includes/functions.php';

// Start the session to track user login
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare a SQL statement to check if the user exists
    $stmt = $conn->prepare("SELECT users.id, users.role, user_credentials.password FROM users
                            JOIN user_credentials ON users.id = user_credentials.user_id
                            WHERE users.username = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the user was found
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $hashed_password = $row['password'];

        // Verify the password
        if (password_verify($password, $hashed_password)) {
            // Password is correct, start the session
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['username'] = $username;
            // Redirect to a secure page (e.g., dashboard.php.php)
            redirect('/views/dashboard.php', 'Login successful!');
        } else {
            handleError('index.php', 'Invalid password. Please try again.');
        }
    } else {
        handleError('index.php', 'Username not found.');
    }

    $stmt->close();
}

$conn->close();