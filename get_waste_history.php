<?php
require_once 'db_connection.php';

$sql = "SELECT 
            wl.date, 
            mi.Item_Name, 
            wl.quantity_lost, 
            wl.total_loss, 
            wl.reason
        FROM waste_log wl
        JOIN menu_items mi ON wl.item_id = mi.Item_ID
        ORDER BY wl.date DESC";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<tr>
                <td>' . date("Y-m-d H:i", strtotime($row['date'])) . '</td>
                <td>' . htmlspecialchars($row['Item_Name']) . '</td>
                <td>' . intval($row['quantity_lost']) . '</td>
                <td>₱' . number_format($row['total_loss'], 2) . '</td>
                <td>' . htmlspecialchars($row['reason']) . '</td>
                <td><i class="fa-solid fa-trash-can text-muted"></i></td>
              </tr>';
    }
} else {
    echo '<tr><td colspan="6" class="text-center">No waste records found</td></tr>';
}
?>
