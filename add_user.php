<?php 
require 'db_connect.php';

$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $role = $_POST['role'];

    if (!in_array($role, ['admin', 'staff'])) {
        $error = "Invalid role selected.";
    } elseif (empty($username) || empty($password)) {
        $error = "Username and password are required.";
    } else {
        $check = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $check->bind_param("s", $username);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "Username already exists.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $hashedPassword, $role);
            if ($stmt->execute()) {
                $success = "User added successfully.";
            } else {
                $error = "Error: " . $conn->error;
            }
            $stmt->close();
        }

        $check->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add New User</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .form-container {
            text-align: center;
            padding: 30px;
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .styled-btn {
            padding: 10px 20px;
            margin-top: 15px;
            color: black;
            border: 2px solid #007bff;
            border-radius: 5px;
            background-color: white;
            text-decoration: none;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s, color 0.3s;
        }

        .styled-btn:hover {
            background-color: #007bff;
            color: white;
        }

        input[type="text"],
        input[type="password"],
        select {
            padding: 10px;
            width: 250px;
            margin-top: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        p {
            margin: 10px 0;
        }

        .success-msg {
            color: green;
        }

        .error-msg {
            color: red;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Add New User</h2>
        <?php if (!empty($success)) echo "<p class='success-msg'>$success</p>"; ?>
        <?php if (!empty($error)) echo "<p class='error-msg'>$error</p>"; ?>

        <form method="post">
            <p>Username:</p>
            <input type="text" name="username" required><br>

            <p>Password:</p>
            <input type="text" name="password" required><br>

            <p>Role:</p>
            <select name="role" required>
                <option value="">Select Role</option>
                <option value="admin">Admin</option>
                <option value="staff">Staff</option>
            </select><br>

            <input type="submit" value="Add User" class="styled-btn">
        </form>
    </div>
</body>
</html>