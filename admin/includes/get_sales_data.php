<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../db_connection.php';

$period = $_GET['period'] ?? 'week';

try {
    switch($period) {
        case 'day':
            $stmt = $conn->prepare("SELECT 
                            HOUR(Order_Date) as hour,
                            SUM(Total_Amount) as total,
                            COUNT(*) as transaction_count
                          FROM Orders 
                          WHERE DATE(Order_Date) = CURDATE()
                          AND Order_Status = 'Paid'
                          GROUP BY HOUR(Order_Date)
                          ORDER BY HOUR(Order_Date)");
            break;

        case 'week':
            $stmt = $conn->prepare("SELECT 
                                    DAYNAME(Order_Date) as day, 
                                    SUM(Total_Amount) as total,
                                    COUNT(*) as transaction_count
                                  FROM Orders 
                                  WHERE YEARWEEK(Order_Date, 1) = YEARWEEK(CURDATE(), 1) 
                                  AND Order_Status = 'Paid'
                                  GROUP BY DAYOFWEEK(Order_Date), DAYNAME(Order_Date)
                                  ORDER BY DAYOFWEEK(Order_Date)");
            break;
            
        case 'month':
            $stmt = $conn->prepare("SELECT 
                                    DATE(Order_Date) as date,
                                    SUM(Total_Amount) as total,
                                    COUNT(*) as transaction_count
                                  FROM Orders 
                                  WHERE MONTH(Order_Date) = MONTH(CURDATE()) 
                                  AND YEAR(Order_Date) = YEAR(CURDATE()) 
                                  AND Order_Status = 'Paid'
                                  GROUP BY DATE(Order_Date)
                                  ORDER BY DATE(Order_Date)");
            break;
            
        case 'year':
            $stmt = $conn->prepare("SELECT 
                                    MONTH(Order_Date) as month_num,
                                    MONTHNAME(Order_Date) as month, 
                                    SUM(Total_Amount) as total,
                                    COUNT(*) as transaction_count
                                  FROM Orders 
                                  WHERE YEAR(Order_Date) = YEAR(CURDATE()) 
                                  AND Order_Status = 'Paid'
                                  GROUP BY MONTH(Order_Date), MONTHNAME(Order_Date)
                                  ORDER BY MONTH(Order_Date)");
            break;
            
        default:
            throw new Exception("Invalid period specified");
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_all(MYSQLI_ASSOC);
    
    if (empty($data)) {
        echo json_encode(['empty' => true]);
    } else {
        echo json_encode($data);
    }
    
} catch(Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
} finally {
    $conn->close();
}
?>
