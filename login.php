<?php
session_start();
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Fetch hashed password from DB
    $stmt = $conn->prepare("SELECT id, role, password FROM users WHERE username = ?");
    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($id, $role, $hashedPassword);
        
        if ($stmt->fetch()) {
            if (password_verify($password, $hashedPassword)) {
                $_SESSION['user_id'] = $id;
                $_SESSION['role'] = $role;
                header("Location: dashboard.php");
                exit();
            } else {
                echo "Invalid loginooooo.";
            }
        } else {
            echo "Invalid login.";
        }

        $stmt->close();
    } else {
        echo "Database error: " . $conn->error;
    }

    $conn->close();
}
?>
