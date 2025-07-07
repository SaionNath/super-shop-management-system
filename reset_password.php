<?php
require 'db_connect.php';
$success = $error = "";

$username = $_GET['username'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_password = trim($_POST['new_password']);
    $username = $_POST['username'];

    if (empty($new_password)) {
        $error = "Password cannot be empty.";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
        $stmt->bind_param("ss", $hashed_password, $username);
        if ($stmt->execute()) {
            $success = "Password reset successful. You can now <a href='index.html'>login</a>.";
        } else {
            $error = "Error resetting password.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .form-container {
            text-align: center;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        input[type="text"] {
            padding: 10px;
            width: 250px;
            margin-top: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .styled-btn {
            padding: 10px 20px;
            margin-top: 15px;
            color: black;
            border: 2px solid #007bff;
            border-radius: 5px;
            background-color: white;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s, color 0.3s;
        }

        .styled-btn:hover {
            background-color: #007bff;
            color: white;
        }

        p {
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Reset Password</h2>
        <?php if (!empty($success)) echo "<p style='color:green;'>$success</p>"; ?>
        <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>

        <form method="post">
            <input type="hidden" name="username" value="<?= htmlspecialchars($username) ?>">
            <p>New Password:</p>
            <input type="text" name="new_password" required><br>
            <input type="submit" value="Reset Password" class="styled-btn">
        </form>
    </div>
</body>
</html>

