<?php
include 'db_connect.php';
session_start();

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit("Access denied.");
}

// Validate product ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    exit("Invalid product ID.");
}
$id = (int)$_GET['id'];

// Fetch product details
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();

if (!$product) {
    exit("Product not found.");
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'];
    $cat = $_POST['category'];
    $price = (float)$_POST['price'];
    $qty = (int)$_POST['quantity'];

    // Update product
    $stmt = $conn->prepare("UPDATE products SET name = ?, category = ?, price = ?, quantity = ? WHERE id = ?");
    $stmt->bind_param("ssdii", $name, $cat, $price, $qty, $id);
    if ($stmt->execute()) {
        echo "Product updated successfully. <a href='view_products.php'>Back</a>";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>
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
            text-align: center;
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
    <h2>Edit Product</h2>

    <form method="post">
        <label for="name">Name</label>
        <input type="text" name="name" id="name" value="<?= htmlspecialchars($product['name']) ?>" required>

        <label for="category">Category</label>
        <input type="text" name="category" id="category" value="<?= htmlspecialchars($product['category']) ?>" required>

        <label for="price">Price</label>
        <input type="number" step="0.01" name="price" id="price" value="<?= $product['price'] ?>" required>

        <label for="quantity">Quantity</label>
        <input type="number" name="quantity" id="quantity" value="<?= $product['quantity'] ?>" required>

        <div class="button-group">
            <button type="submit">Update</button>
            <button type="button" onclick="window.location.href='view_products.php';">Back to Products</button>
        </div>
    </form>
</div>

</body>
</html>

