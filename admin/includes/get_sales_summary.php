<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../db_connection.php';

$period = $_GET['period'] ?? 'today';

try {
    switch($period) {
        case 'today':
            $stmt = $conn->prepare("SELECT 
                                    COALESCE(SUM(Total_Amount), 0) as total,
                                    COUNT(*) as transaction_count
                                  FROM Orders 
                                  WHERE DATE(Order_Date) = CURDATE() 
                                  AND Order_Status = 'Paid'");
            break;
            
        case 'week':
            $stmt = $conn->prepare("SELECT 
                                    COALESCE(SUM(Total_Amount), 0) as total,
                                    COUNT(*) as transaction_count
                                  FROM Orders 
                                  WHERE YEARWEEK(Order_Date, 1) = YEARWEEK(CURDATE(), 1) 
                                  AND Order_Status = 'Paid'");
            break;
            
        case 'month':
            $stmt = $conn->prepare("SELECT 
                                    COALESCE(SUM(Total_Amount), 0) as total,
                                    COUNT(*) as transaction_count
                                  FROM Orders 
                                  WHERE MONTH(Order_Date) = MONTH(CURDATE()) 
                                  AND YEAR(Order_Date) = YEAR(CURDATE()) 
                                  AND Order_Status = 'Paid'");
            break;
            
        case 'year':
            $stmt = $conn->prepare("SELECT 
                                    COALESCE(SUM(Total_Amount), 0) as total,
                                    COUNT(*) as transaction_count
                                  FROM Orders 
                                  WHERE YEAR(Order_Date) = YEAR(CURDATE()) 
                                  AND Order_Status = 'Paid'");
            break;
            
        default:
            throw new Exception("Invalid period specified");
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    
    echo json_encode($data);
    
} catch(Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
} finally {
    $conn->close();
}
?>
