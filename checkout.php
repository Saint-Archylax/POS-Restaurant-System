<?php
session_start();
require_once 'db_connection.php';
require_once 'cart.php';

$cart = new Cart($conn);

if ($cart->getCartCount() == 0) {
    header("Location: home.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate cash given
    $cash_given = (float)$_POST['cash_given'];
    $total_amount = $cart->getCartTotal();
    
    if ($cash_given < $total_amount) {
        $_SESSION['error'] = "Cash given must be at least the total amount";
        header("Location: checkout.php");
        exit();
    }

    // Create order (simplified without table number)
    $order_sql = "INSERT INTO Orders (Total_Amount) VALUES (?)";
    $order_stmt = $conn->prepare($order_sql);
    $order_stmt->bind_param("d", $total_amount);
    $order_stmt->execute();
    $order_id = $conn->insert_id;
    
    // Add order details
    $cart_items = $cart->getCartItems();
    $order_detail_sql = "INSERT INTO Order_Details (Order_ID, Item_ID, Quantity) VALUES (?, ?, ?)";
    $order_detail_stmt = $conn->prepare($order_detail_sql);
    
    while ($item = $cart_items->fetch_assoc()) {
    // Insert order details
    $order_detail_stmt->bind_param("iii", $order_id, $item['Item_ID'], $item['Quantity']);
    $order_detail_stmt->execute();

    // Deduct stock quantity
    $deduct_stock_sql = "
        UPDATE menu_items 
        SET stock_quantity = stock_quantity - ?, 
            is_available = CASE WHEN stock_quantity - ? > 0 THEN 1 ELSE 0 END
        WHERE Item_ID = ?
    ";
    $deduct_stock_stmt = $conn->prepare($deduct_stock_sql);
    $deduct_stock_stmt->bind_param("iii", $item['Quantity'], $item['Quantity'], $item['Item_ID']);
    $deduct_stock_stmt->execute();
    $deduct_stock_stmt->close();
}

    
    // Record payment with cash_given
    $payment_sql = "INSERT INTO Payments (Order_ID, Amount, Cash_Given) 
                   VALUES (?, ?, ?)";
    $payment_stmt = $conn->prepare($payment_sql);
    $payment_stmt->bind_param("idd", $order_id, $total_amount, $cash_given);
    $payment_stmt->execute();
    $payment_id = $conn->insert_id;
    
    $cart->clearCart();

    // Add transaction history record
    $history_sql = "INSERT INTO Transaction_History 
                (Order_ID, Payment_ID, Action, Details) 
                VALUES (?, ?, 'PAYMENT_RECORDED', 'New order created')";
    $history_stmt = $conn->prepare($history_sql);
    $history_stmt->bind_param("ii", $order_id, $payment_id);
    $history_stmt->execute();
    
    header("Location: order_success.php?order_id=$order_id");
    exit();   
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        
        .receipt-container {
            width: 100%;
            max-width: 300px;
            background: white;
            padding: 25px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 5px;
            text-align: center;
        }
        
        .receipt-header {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #ccc;
        }
        
        .receipt-body {
            margin: 20px 0;
            text-align: left;
        }
        
        .receipt-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        
        .receipt-total {
            font-weight: bold;
            border-top: 1px dashed #ccc;
            padding-top: 10px;
            margin-top: 10px;
            display: flex;
            justify-content: space-between;
        }
        
        .receipt-divider {
            border-top: 1px dashed #ccc;
            margin: 15px 0;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .form-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }
        
        .checkout-btn {
            width: 100%;
            padding: 10px;
            background-color: #333;
            color: white;
            border: none;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .checkout-btn:hover {
            background-color: #555;
        }
        
        .currency {
            font-family: 'Courier New', monospace;
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="receipt-header">CHECKOUT</div>
        
        <form method="POST" action="checkout.php">
            <div class="form-group">
                <label for="cash_given">CASH GIVEN:</label>
                <input type="number" name="cash_given" id="cash_given" step="0.01" min="0" required>
            </div>
            
            <div class="receipt-divider"></div>
            
            <div class="receipt-body">
                <h3>ORDER SUMMARY</h3>
                <?php
                $cart_items = $cart->getCartItems();
                $total = 0;
                
                if ($cart_items->num_rows > 0) {
                    while ($item = $cart_items->fetch_assoc()) {
                        $item_total = $item['Price'] * $item['Quantity'];
                        $total += $item_total;
                        echo "<div class='receipt-item'>
                                <span>{$item['Item_Name']} x {$item['Quantity']}</span>
                                <span class='currency'>₱" . number_format($item_total, 2) . "</span>
                              </div>";
                    }
                    
                    echo "<div class='receipt-total'>
                            <span>TOTAL:</span>
                            <span class='currency'>₱" . number_format($total, 2) . "</span>
                          </div>";
                }
                ?>
            </div>
            
            <div class="receipt-divider"></div>
            
            <button type="submit" class="checkout-btn">COMPLETE ORDER</button>
        </form>
    </div>
</body>
</html>
