<?php
class Cart {
    private $conn;
    private $session_id;

    public function __construct($conn) {
        $this->conn = $conn;
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->session_id = session_id();
        
        // Verify table exists with more detailed error reporting
        if (!$this->tableExists('Cart')) {
            error_log("Cart table check failed. Available tables: " . print_r($this->getAllTables(), true));
            throw new Exception("Cart table does not exist in database");
        }
    }

    private function getAllTables() {
        $result = $this->conn->query("SHOW TABLES");
        $tables = [];
        while ($row = $result->fetch_array()) {
            $tables[] = $row[0];
        }
        return $tables;
    }

    // Clear cart with better error handling
    public function clearCart() {
        try {
            $sql = "DELETE FROM Cart WHERE Session_ID = ?";
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            $stmt->bind_param("s", $this->session_id);
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }
            return true;
        } catch (Exception $e) {
            error_log("clearCart Error: " . $e->getMessage());
            return false;
        }
    }

    // Get cart count with NULL handling
    public function getCartCount() {
        try {
            $sql = "SELECT COALESCE(SUM(Quantity), 0) as count FROM Cart WHERE Session_ID = ?";
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            $stmt->bind_param("s", $this->session_id);
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            return (int)$row['count'];
        } catch (Exception $e) {
            error_log("getCartCount Error: " . $e->getMessage());
            return 0;
        }
    }

    // Add item with transaction safety
    public function addItem($item_id, $quantity = 1) {
        $this->conn->begin_transaction();
        try {
            // First verify item exists
            if (!$this->itemExists($item_id)) {
                throw new Exception("Item $item_id does not exist");
            }

            // Check if item exists in cart
            $check_sql = "SELECT Quantity FROM Cart WHERE Session_ID = ? AND Item_ID = ? FOR UPDATE";
            $check_stmt = $this->conn->prepare($check_sql);
            if (!$check_stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            $check_stmt->bind_param("si", $this->session_id, $item_id);
            if (!$check_stmt->execute()) {
                throw new Exception("Execute failed: " . $check_stmt->error);
            }
            
            $result = $check_stmt->get_result();
            
            if ($result->num_rows > 0) {
                // Update quantity if item exists
                $row = $result->fetch_assoc();
                $new_quantity = $row['Quantity'] + $quantity;
                
                $update_sql = "UPDATE Cart SET Quantity = ? WHERE Session_ID = ? AND Item_ID = ?";
                $update_stmt = $this->conn->prepare($update_sql);
                if (!$update_stmt) {
                    throw new Exception("Prepare failed: " . $this->conn->error);
                }
                $update_stmt->bind_param("isi", $new_quantity, $this->session_id, $item_id);
                if (!$update_stmt->execute()) {
                    throw new Exception("Execute failed: " . $update_stmt->error);
                }
            } else {
                // Add new item
                $insert_sql = "INSERT INTO Cart (Session_ID, Item_ID, Quantity) VALUES (?, ?, ?)";
                $insert_stmt = $this->conn->prepare($insert_sql);
                if (!$insert_stmt) {
                    throw new Exception("Prepare failed: " . $this->conn->error);
                }
                $insert_stmt->bind_param("sii", $this->session_id, $item_id, $quantity);
                if (!$insert_stmt->execute()) {
                    throw new Exception("Execute failed: " . $insert_stmt->error);
                }
            }
            
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("addItem Error: " . $e->getMessage());
            return false;
        }
    }

    // More robust itemExists check
    private function itemExists($item_id) {
        try {
            $stmt = $this->conn->prepare("SELECT 1 FROM Menu_Items WHERE Item_ID = ? AND is_available = TRUE");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            $stmt->bind_param("i", $item_id);
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }
            return $stmt->get_result()->num_rows > 0;
        } catch (Exception $e) {
            error_log("itemExists Error: " . $e->getMessage());
            return false;
        }
    }

    // Get cart items with price validation
    public function getCartItems() {
        try {
            $sql = "SELECT c.Cart_ID, c.Item_ID, c.Quantity, 
                           m.Item_Name, m.Price, m.Image_Path
                    FROM Cart c 
                    JOIN Menu_Items m ON c.Item_ID = m.Item_ID 
                    WHERE c.Session_ID = ? AND m.is_available = TRUE";
                    
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            
            $stmt->bind_param("s", $this->session_id);
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }
            
            return $stmt->get_result();
        } catch (Exception $e) {
            error_log("getCartItems Error: " . $e->getMessage());
            return false;
        }
    }

    // Get cart total with price validation
    public function getCartTotal() {
        try {
            $sql = "SELECT COALESCE(SUM(c.Quantity * m.Price), 0) as total 
                    FROM Cart c 
                    JOIN Menu_Items m ON c.Item_ID = m.Item_ID 
                    WHERE c.Session_ID = ? AND m.is_available = TRUE";
                    
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            
            $stmt->bind_param("s", $this->session_id);
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }
            
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            
            return (float)$row['total'];
        } catch (Exception $e) {
            error_log("getCartTotal Error: " . $e->getMessage());
            return 0.0;
        }
    }

    // Remove item with validation
    public function removeItem($item_id) {
        try {
            $sql = "DELETE FROM Cart WHERE Session_ID = ? AND Item_ID = ?";
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            $stmt->bind_param("si", $this->session_id, $item_id);
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }
            return $stmt->affected_rows > 0;
        } catch (Exception $e) {
            error_log("removeItem Error: " . $e->getMessage());
            return false;
        }
    }

    // Update quantity with validation
    public function updateQuantity($item_id, $quantity) {
        try {
            if ($quantity < 0) {
                throw new Exception("Quantity cannot be negative");
            }
            
            if ($quantity == 0) {
                return $this->removeItem($item_id);
            }
            
            $sql = "UPDATE Cart SET Quantity = ? WHERE Session_ID = ? AND Item_ID = ?";
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            $stmt->bind_param("isi", $quantity, $this->session_id, $item_id);
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }
            return $stmt->affected_rows > 0;
        } catch (Exception $e) {
            error_log("updateQuantity Error: " . $e->getMessage());
            return false;
        }
    }

    // Table exists check with error reporting
    private function tableExists($table) {
        try {
            $result = $this->conn->query("SHOW TABLES LIKE '$table'");
            if (!$result) {
                throw new Exception("Query failed: " . $this->conn->error);
            }
            return $result->num_rows > 0;
        } catch (Exception $e) {
            error_log("tableExists Error: " . $e->getMessage());
            return false;
        }
    }

    // Debug methods remain the same
    public function debugCart() {
        // ... keep existing debugCart method ...
    }

    public function debugInfo() {
        // ... keep existing debugInfo method ...
    }
}
?>