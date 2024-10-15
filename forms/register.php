<?php
// Include the database connection
include '../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if the email or nickname already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? OR username = ?");
    $stmt->bind_param('ss', $email, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "Email or nickname already exists!";
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
        echo "User registered successfully!";
        echo "<meta http-equiv='refresh' content='3; url=index.php'>";
    }
    $stmt->close();
}
$conn->close();
?>