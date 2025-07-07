<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit("Access denied.");
}

$success = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name     = trim($_POST['productName']);
    $category = trim($_POST['category']);
    $price    = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);

    if ($name === '' || $category === '' || $price <= 0 || $quantity <= 0) {
        $error = "Please fill in all fields correctly.";
    } else {
		
        $stmt = $conn->prepare("SELECT id, quantity FROM products WHERE name = ? AND category = ? AND is_deleted = 0");
        $stmt->bind_param("ss", $name, $category);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $existing_id = $row['id'];
            $new_qty = $row['quantity'] + $quantity;

            $update = $conn->prepare("UPDATE products SET quantity = ?, price = ? WHERE id = ?");
            $update->bind_param("idi", $new_qty, $price, $existing_id);
            $update->execute();

            $success = "Product already exists. Quantity updated.";
        } else {
            $insert = $conn->prepare("INSERT INTO products (name, category, price, quantity) VALUES (?, ?, ?, ?)");
            $insert->bind_param("ssdi", $name, $category, $price, $quantity);
            $insert->execute();

            $success = "New product added successfully.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Product</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
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

        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
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

        .message {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
		
		select {
			width: 104%;
			padding: 9px;
			border: 1px solid #ccc;
			border-radius: 4px;
			font-family: inherit;
			font-size: 15px;
		}

    </style>
</head>
<body>
    <div class="form-container">
        <h2>Add New Product</h2>

        <?php if ($success): ?>
            <div class="message success"><?= htmlspecialchars($success) ?></div>
        <?php elseif ($error): ?>
            <div class="message error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label for="productName">Product Name</label>
                <input type="text" id="productName" name="productName" required>
            </div>

			<div class="form-group">
				<label for="category">Category</label>
				<select id="category" name="category" required>
					<option value="" disabled selected>Select Category</option>
					<option value="Grocery">Grocery</option>
					<option value="Vegetable">Vegetable</option>
					<option value="Beverages">Beverages</option>
					<option value="Personal Care">Personal Care</option>
					<option value="Household">Household</option>
					<option value="Snacks">Snacks</option>
					<option value="Dairy">Dairy</option>
					<option value="Bakery">Bakery</option>
					<option value="Frozen">Frozen</option>
					<option value="Baby Products">Baby Products</option>
					<option value="Health">Health</option>
				</select>
			</div>


            <div class="form-group">
                <label for="price">Price (৳)</label>
                <input type="number" id="price" name="price" step="0.5" required>
            </div>

            <div class="form-group">
                <label for="quantity">Quantity</label>
                <input type="number" id="quantity" name="quantity" required>
            </div>

            <div class="button-group">
                <button type="submit">Add Product</button>
                <button type="button" onclick="window.location.href='dashboard.php';">Back to Dashboard</button>
            </div>
        </form>
    </div>
</body>
</html>