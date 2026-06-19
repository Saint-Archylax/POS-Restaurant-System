<?php
require_once 'db_connection.php';

$response = ['success' => false];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_id = intval($_POST['item_id']);
    $adjustment_type = $_POST['adjustment_type'];
    $quantity = intval($_POST['quantity']);
    $reason = trim($_POST['reason'] ?? '');

    if ($item_id <= 0 || $quantity <= 0) {
        $response['error'] = 'Invalid data provided.';
        echo json_encode($response);
        exit;
    }

    // Fetch current stock
    $stmt = $conn->prepare("SELECT stock_quantity, Price FROM menu_items WHERE Item_ID = ?");
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $stmt->bind_result($current_stock, $price);
    $stmt->fetch();
    $stmt->close();

    if ($adjustment_type === 'add') {
        $new_stock = $current_stock + $quantity;
    } elseif ($adjustment_type === 'remove') {
        if ($quantity > $current_stock) {
            $response['error'] = 'Cannot waste more than current stock.';
            echo json_encode($response);
            exit;
        }
        $new_stock = $current_stock - $quantity;

        // Log the waste
        $total_loss = $quantity * $price;
        $log_stmt = $conn->prepare("INSERT INTO waste_log (item_id, quantity_lost, total_loss, reason, date) VALUES (?, ?, ?, ?, NOW())");
        $log_stmt->bind_param("iids", $item_id, $quantity, $total_loss, $reason);
        $log_stmt->execute();
        $log_stmt->close();
    } else {
        $response['error'] = 'Invalid adjustment type.';
        echo json_encode($response);
        exit;
    }

    // Update stock and availability
    $is_available = $new_stock > 0 ? 1 : 0;
    $update_stmt = $conn->prepare("UPDATE menu_items SET stock_quantity = ?, is_available = ? WHERE Item_ID = ?");
    $update_stmt->bind_param("iii", $new_stock, $is_available, $item_id);
    $update_stmt->execute();
    $update_stmt->close();

    $response['success'] = true;


    $adjustment_type = $_POST['adjustment_type'] ?? '';
$quantity = intval($_POST['quantity'] ?? 0);
$item_id = intval($_POST['item_id'] ?? 0);
$reason = $_POST['reason'] ?? null;

if (!$item_id || !$adjustment_type || $quantity <= 0) {
    echo json_encode(['error' => 'Invalid input']);
    exit;
}

if ($adjustment_type === 'remove' && empty($reason)) {
    echo json_encode(['error' => 'Reason required for waste']);
    exit;
}

}

echo json_encode($response);
?>
