<?php
include 'db_connect.php';
session_start();

// Step 1: Admin check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit("Access denied.");
}

// Step 2: Validate product ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    exit("Invalid product ID.");
}
$id = (int)$_GET['id'];

// Step 3: Soft delete the product
$stmt = $conn->prepare("UPDATE products SET is_deleted = 1 WHERE id = ?");
if (!$stmt) {
    exit("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $stmt->close();
    header("Location: view_products.php?msg=soft_deleted");
    exit();
} else {
    $stmt->close();
    exit("Error soft-deleting product: " . $stmt->error);
}
?>
