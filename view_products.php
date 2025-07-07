<?php
include 'db_connect.php';
$result = $conn->query("SELECT * FROM products WHERE is_deleted = 0");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Product List</title>
    <style>
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

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }

        a {
            text-decoration: none;
            color: #007bff;
        }
    </style>
</head>
<body>

    <h2>Product List</h2>


    <table>
        <tr><th>ID</th><th>Name</th><th>Category</th><th>Price</th><th>Quantity</th><th>Actions</th></tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['category']) ?></td>
                <td><?= $row['price'] ?></td>
                <td><?= $row['quantity'] ?></td>
                <td>
                    <a href='edit_product.php?id=<?= $row['id'] ?>'>Edit</a> | 
                    <a href='delete_product.php?id=<?= $row['id'] ?>'>Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
	
	    <div class="button-group">
        <button type="button" onclick = "window.location.href='add_product.php';">Add a New Product</button>
		<button type="button" onclick = "window.location.href='dashboard.php';">Back to Dashboard</button>
		</div>
</body>
</html>
