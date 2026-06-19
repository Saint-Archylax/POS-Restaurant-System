<?php
require_once 'db_connection.php';

header('Content-Type: application/json');

try {
    $order_id = $_POST['order_id'] ?? null;
    $reason = $_POST['reason'] ?? '';

    if (!$order_id) {
        throw new Exception("Order ID is required");
    }

    // Start transaction
    $conn->begin_transaction();

    // 1. Get payment details
    $payment_sql = "SELECT Payment_ID, Amount FROM Payments WHERE Order_ID = ?";
    $payment_stmt = $conn->prepare($payment_sql);
    $payment_stmt->bind_param("i", $order_id);
    $payment_stmt->execute();
    $payment = $payment_stmt->get_result()->fetch_assoc();

    if (!$payment) {
        throw new Exception("Payment record not found");
    }

    // 2. Update order status to Refunded
    $update_order = $conn->prepare("UPDATE Orders SET Order_Status = 'Refunded' WHERE Order_ID = ?");
    $update_order->bind_param("i", $order_id);
    $update_order->execute();

    // 3. Create refund record
    $refund_sql = "INSERT INTO Refunds (Order_ID, Payment_ID, Amount, Reason) 
                   VALUES (?, ?, ?, ?)";
    $refund_stmt = $conn->prepare($refund_sql);
    $refund_stmt->bind_param("iids", $order_id, $payment['Payment_ID'], $payment['Amount'], $reason);
    $refund_stmt->execute();

    // 4. Record in transaction history
    $history_sql = "INSERT INTO Transaction_History 
                    (Order_ID, Payment_ID, Action, Details) 
                    VALUES (?, ?, 'REFUND_PROCESSED', ?)";
    $history_stmt = $conn->prepare($history_sql);
    $history_details = "Refund processed. Reason: " . substr($reason, 0, 100);
    $history_stmt->bind_param("iis", $order_id, $payment['Payment_ID'], $history_details);
    $history_stmt->execute();

    // Commit transaction
    $conn->commit();

    echo json_encode(['success' => true]);
    
} catch(Exception $e) {
    $conn->rollback();
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
} finally {
    $conn->close();
}