<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'staff'])) {
    header("Location: index.html");
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);

    $check = $conn->prepare("SELECT id FROM customers WHERE phone = ? OR email = ?");
    $check->bind_param("ss", $phone, $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $message = "Phone or Email already exists!";
    } else {
        $stmt = $conn->prepare("INSERT INTO customers (name, phone, email) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $phone, $email);
        if ($stmt->execute()) {
            $message = "Customer added successfully!";
        } else {
            $message = "Error!!!: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add New Customer</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 30px;
        }

        .form-container {
            max-width: 600px;
            margin: auto;
            background: #f8f9fa;
            padding: 20px 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        h2 {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="email"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .button-group {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        button {
            padding: 10px 20px;
            border: 2px solid #007bff;
            background-color: white;
            color: black;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s, color 0.3s;
        }

        button:hover {
            background-color: #007bff;
            color: white;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Add New Customer</h2>

    <form method="post" action="add_customer.php">
        <label for="name">Customer Name</label>
        <input type="text" name="name" id="name" required>

        <label for="phone">Phone</label>
        <input type="text" name="phone" id="phone" required>

        <label for="email">Email</label>
        <input type="email" name="email" id="email" required>

            <div class="button-group">
                <button type="submit">Add Customer</button>
				<button type="button" onclick="window.location.href='dashboard.php';">Back to Dashboard</button>
            </div>
</div>
	</form>
</body>
</html>

