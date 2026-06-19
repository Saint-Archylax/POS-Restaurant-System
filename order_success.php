<?php
session_start();
require_once 'db_connection.php';

if (!isset($_GET['order_id'])) {
    header("Location: home.php");
    exit();
}

$order_id = (int)$_GET['order_id'];

// Get order and payment details
$sql = "SELECT o.Order_ID, o.Total_Amount, o.Order_Date, 
               p.Cash_Given, (p.Cash_Given - o.Total_Amount) AS Change_Given
        FROM Orders o
        JOIN Payments p ON o.Order_ID = p.Order_ID
        WHERE o.Order_ID = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Order not found");
}

$order = $result->fetch_assoc();

// Get order items
$items_sql = "SELECT m.Item_Name, od.Quantity, m.Price, 
              (od.Quantity * m.Price) AS Item_Total
              FROM Order_Details od
              JOIN Menu_Items m ON od.Item_ID = m.Item_ID
              WHERE od.Order_ID = ?";
$items_stmt = $conn->prepare($items_sql);
$items_stmt->bind_param("i", $order_id);
$items_stmt->execute();
$items = $items_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Success</title>
    <link rel="stylesheet" href="./home.css">
</head>
<body>
    <div class="order-success-container">
        <h1>Order Successful!</h1>
        <h3>Order #<?php echo $order['Order_ID']; ?></h3>
        <p>Date: <?php echo $order['Order_Date']; ?></p>
        
        <div class="order-summary">
            <h3>Order Summary</h3>
            <?php while ($item = $items->fetch_assoc()): ?>
                <div class="order-item">
                    <span><?php echo $item['Item_Name']; ?> x <?php echo $item['Quantity']; ?></span>
                    <span>₱<?php echo number_format($item['Item_Total'], 2); ?></span>
                </div>
            <?php endwhile; ?>
            
            <div class="order-totals">
                <div class="total-row">
                    <span>Subtotal:</span>
                    <span>₱<?php echo number_format($order['Total_Amount'], 2); ?></span>
                </div>
                <div class="total-row">
                    <span>Cash Given:</span>
                    <span>₱<?php echo number_format($order['Cash_Given'], 2); ?></span>
                </div>
                <div class="total-row">
                    <span>Change:</span>
                    <span>₱<?php echo number_format($order['Change_Given'], 2); ?></span>
                </div>
            </div>
        </div>
        
        <a href="home.php" class="back-btn">Return to Home</a>
    </div>
</body>
</html>