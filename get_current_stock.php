<?php
require_once 'db_connection.php';

if (isset($_GET['item_id'])) {
    $item_id = intval($_GET['item_id']);

    $stmt = $conn->prepare("SELECT stock_quantity FROM menu_items WHERE Item_ID = ?");
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $stmt->bind_result($stock);
    
    if ($stmt->fetch()) {
        echo json_encode(['stock' => $stock]);
    } else {
        echo json_encode(['error' => 'Item not found']);
    }
    
    $stmt->close();
} else {
    echo json_encode(['error' => 'Item ID is missing']);
}
?>
