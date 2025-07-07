<?php
session_start();
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'staff'])) {
    header("Location: index.html");
    exit();
}

require 'db_connect.php';

$success = '';
$error = '';

$customerResult = $conn->query("SELECT id, name FROM customers");
$customers = [];
while ($row = $customerResult->fetch_assoc()) {
    $customers[] = $row;
}

$productResult = $conn->query("SELECT id, name, price, quantity FROM products WHERE quantity > 0 AND is_deleted = 0");
$products = [];
while ($row = $productResult->fetch_assoc()) {
    $products[] = $row;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $customer_id = $_POST['customer_id'];
    $product_ids = $_POST['product_id'];
    $quantities = $_POST['quantity'];

    $total = 0;
    $sale_items = [];

    for ($i = 0; $i < count($product_ids); $i++) {
        $product_id = $product_ids[$i];
        $quantity = (int)$quantities[$i];

        if (!$product_id || $quantity <= 0) continue;

        $stmt = $conn->prepare("SELECT price, quantity FROM products WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $stmt->bind_result($price, $stock_quantity);
        $stmt->fetch();
        $stmt->close();

        if ($stock_quantity === null || $stock_quantity < $quantity) {
            $error .= "Product ID $product_id has insufficient stock.<br>";
            continue;
        }

        $subtotal = $price * $quantity;
        $total += $subtotal;

        $sale_items[] = [
            'product_id' => $product_id,
            'quantity' => $quantity,
            'price' => $price
        ];
    }

    if (empty($sale_items)) {
        $error .= "No valid products selected.";
    } else {
        $stmt = $conn->prepare("INSERT INTO sales (customer_id, total, sale_date) VALUES (?, ?, NOW())");
        $stmt->bind_param("id", $customer_id, $total);
        $stmt->execute();
        $sale_id = $stmt->insert_id;
        $stmt->close();

        foreach ($sale_items as $item) {
            $stmt = $conn->prepare("INSERT INTO sale_items (sale_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiid", $sale_id, $item['product_id'], $item['quantity'], $item['price']);
            $stmt->execute();
            $stmt->close();

            $stmt = $conn->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ?");
            $stmt->bind_param("ii", $item['quantity'], $item['product_id']);
            $stmt->execute();
            $stmt->close();
        }

        $success = "Sale recorded successfully with multiple products.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Sale</title>
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

        select,
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

        .message {
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 5px;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>

<?php if (!empty($success)): ?>
    <div class="message success"><?= htmlspecialchars($success) ?></div>
<?php elseif (!empty($error)): ?>
    <div class="message error"><?= $error ?></div>
<?php endif; ?>

<div class="form-container">
    <h2><center>Create Sale</h2>
    <form method="post">
    <p>
        <label for="customer_id">Customer:</label>
        <select name="customer_id" id="customer_id" required style="width: 600px;">
            <option value="">Select Customer</option>
            <?php foreach ($customers as $customer): ?>
                <option value="<?= $customer['id'] ?>"><?= htmlspecialchars($customer['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </p>

    <?php for ($i = 0; $i < 3; $i++): ?>
        <p>
            <label>Product <?= $i + 1 ?>:</label>
            <select name="product_id[]" style="width: 600px;">
                <option value="">Select Product</option>
                <?php foreach ($products as $product): ?>
                    <option value="<?= $product['id'] ?>">
                        <?= htmlspecialchars($product['name']) ?> (৳<?= $product['price'] ?> | Stock: <?= $product['quantity'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </p>

        <p>
            <label>Quantity:</label>
            <input type="number" name="quantity[]" min="1" value="" style="width: 580px;">
        </p>
    <?php endfor; ?>

    <p>
        <button type="submit">Submit Sale</button>
        <button type="button" onclick="window.location.href='dashboard.php';">Back to Dashboard</button>
    </p>
</form>
</div>
</body>
</html>
