<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}

$role = $_SESSION['role'];
$username = $_SESSION['username'] ?? $role;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        h2 {
            margin-bottom: 5px;
        }
        p {
            margin-top: 0;
        }
        .btn {
            padding: 10px 20px;
            margin: 5px;
            color: black;
            border: 2px solid #007bff;
            border-radius: 5px;
            background-color: white;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s, color 0.3s;
        }

        .btn:hover:not(.red) {
            background-color: #007bff;
            color: white;
        }

        .red {
            background-color: #dc3545;
            color: white;
            border: none;
        }

        .red:hover {
            background-color: #c82333;
        }
		
		body {
			font-family: Arial, sans-serif;
			background-image: url('images/dashboard_banner.jpg');
			background-size: cover;
			background-position: center;
			background-repeat: no-repeat;
			color: white; /* optional if your image is dark */
			min-height: 100vh;
		}

    </style>
</head>
<body>

<h2>Welcome!!!, <?= htmlspecialchars($username) ?>!</h2>
<p>Role: <strong><?= htmlspecialchars($role) ?></strong></p>

<?php if ($role === 'admin'): ?>
    <a href="add_product.php" class="btn">Add Product</a>
    <a href="view_products.php" class="btn">Manage Products</a>
    <a href="add_customer.php" class="btn">Add Customer</a>
    <a href="create_sale.php" class="btn">Create Sale</a>
    <a href="sales_report.php" class="btn">Sales Report</a>
    <a href="logout.php" class="btn red">Logout</a>
<?php elseif ($role === 'staff'): ?>
    <a href="add_customer.php" class="btn">Add Customer</a>
    <a href="create_sale.php" class="btn">Create Sale</a>
	<a href="sales_report.php" class="btn">Sales Report</a>
    <a href="logout.php" class="btn red">Logout</a>
<?php endif; ?>

</body>
</html>
