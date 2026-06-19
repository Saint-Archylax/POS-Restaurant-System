<?php
require_once 'db_connection.php';

if (isset($_GET['order_id'])) {
    $order_id = (int)$_GET['order_id'];
    
    $sql = "SELECT m.Item_Name, od.Quantity, m.Price, 
           (od.Quantity * m.Price) AS Item_Total
           FROM Order_Details od
           JOIN Menu_Items m ON od.Item_ID = m.Item_ID
           WHERE od.Order_ID = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo '<table class="table table-sm">';
        echo '<thead><tr><th>Item</th><th>Qty</th><th>Price</th><th>Total</th></tr></thead>';
        echo '<tbody>';
        
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['Item_Name']}</td>
                    <td>{$row['Quantity']}</td>
                    <td>₱" . number_format($row['Price'], 2) . "</td>
                    <td>₱" . number_format($row['Item_Total'], 2) . "</td>
                  </tr>";
        }
        
        echo '</tbody></table>';
    } else {
        echo '<p>No items found for this order</p>';
    }
}
?>