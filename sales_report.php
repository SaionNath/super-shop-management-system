<?php
require 'db_connect.php';
session_start();

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'staff'])) {
    header("Location: dashboard.php");
    exit("Access denied.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Sales Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }

        h2 {
            margin-bottom: 20px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 10px;
            margin-bottom: 30px;
        }

        table, th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        a {
            text-decoration: none;
            color: #007bff;
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
    <h2>Sales Report</h2>

    <?php
    $result = $conn->query("
        SELECT 
            sales.id AS sale_id,
            customers.name AS customer_name,
            products.name AS product_name,
            sale_items.quantity,
            sale_items.price,
            (sale_items.quantity * sale_items.price) AS item_total,
            sales.sale_date
        FROM sales
        JOIN customers ON sales.customer_id = customers.id
        JOIN sale_items ON sales.id = sale_items.sale_id
        JOIN products ON sale_items.product_id = products.id
        ORDER BY sales.sale_date DESC, sales.id
    ");

    if ($result->num_rows > 0) {
        $current_sale_id = null;
        $total_for_sale = 0;
        $rows_buffer = [];

        while ($row = $result->fetch_assoc()) {
            if ($current_sale_id !== $row['sale_id']) {
                if ($current_sale_id !== null && count($rows_buffer) > 0) {
                    // Output buffered table
                    foreach ($rows_buffer as $index => $r) {
                        echo "<tr>
                                <td>{$r['product_name']}</td>
                                <td>{$r['quantity']}</td>
                                <td>{$r['price']}</td>
                                <td>{$r['item_total']}</td>";

                        if ($index === 0) {
                            echo "<td rowspan='" . count($rows_buffer) . "' style='vertical-align: middle; font-weight: bold;'>৳" . number_format($total_for_sale, 2) . "</td>";
                        }

                        echo "</tr>";
                    }
                    echo "</table><br>";
                }

                // Start new sale
                $current_sale_id = $row['sale_id'];
                $total_for_sale = 0;
                $rows_buffer = [];

                echo "<h3>Sale ID: {$row['sale_id']} | Customer: {$row['customer_name']} | Date: {$row['sale_date']}</h3>";
                echo "<table>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Unit Price (৳)</th>
                            <th>Item Total (৳)</th>
                            <th>Total (৳)</th>
                        </tr>";
            }

            $rows_buffer[] = $row;
            $total_for_sale += $row['item_total'];
        }

        // Output final sale
        if (!empty($rows_buffer)) {
            foreach ($rows_buffer as $index => $r) {
                echo "<tr>
                        <td>{$r['product_name']}</td>
                        <td>{$r['quantity']}</td>
                        <td>{$r['price']}</td>
                        <td>{$r['item_total']}</td>";

                if ($index === 0) {
                    echo "<td rowspan='" . count($rows_buffer) . "' style='vertical-align: middle; font-weight: bold;'>৳" . number_format($total_for_sale, 2) . "</td>";
                }

                echo "</tr>";
            }
            echo "</table>";
        }

    } else {
        echo "<p>No sales found.</p>";
    }
    ?>

    <br>
    <div class="button">
        <button type="button" onclick="window.location.href='dashboard.php';">Back to Dashboard</button>
    </div>
</body>
</html>
