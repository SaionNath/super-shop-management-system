<?php
require 'db_connect.php';
$success = $error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        header("Location: reset_password.php?username=" . urlencode($username));
        exit();
    } else {
        $error = "Username not found.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh; /* Full height */
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

        input[type="text"] {
            padding: 10px;
            width: 250px;
            margin-top: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        p {
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Forgot Password</h2>
        <?php if (!empty($success)) echo "<p style='color:green;'>$success</p>"; ?>
        <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>

        <form method="post">
            <p>Enter your username:</p>
            <input type="text" name="username" required><br>
            <input type="submit" value="Continue" class="styled-btn">
        </form>
    </div>
</body>
</html>
