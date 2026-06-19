<?php
// Ensure session is started properly
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'db_connection.php';
require_once 'cart.php';

// Set JSON header for all responses
header('Content-Type: application/json');

// Enhanced error response function
function sendError($message, $code = 400, $debug = []) {
    http_response_code($code);
    $response = ['success' => false, 'message' => $message];
    if (!empty($debug)) {
        $response['debug'] = $debug;
    }
    echo json_encode($response);
    exit();
}

// Enhanced success response function
function sendSuccess($data = [], $debug = []) {
    $response = array_merge(['success' => true], $data);
    if (!empty($debug)) {
        $response['debug'] = $debug;
    }
    echo json_encode($response);
    exit();
}

try {
    // Initialize cart with error handling
    try {
        $cart = new Cart($conn);
    } catch (Exception $e) {
        sendError('Cart initialization failed', 500, [
            'error' => $e->getMessage(),
            'session_id' => session_id(),
            'session_status' => session_status()
        ]);
    }

    // Handle GET requests
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['get_cart'])) {
        $cart_items = $cart->getCartItems();
        if ($cart_items === false) {
            sendError('Failed to retrieve cart items', 500);
        }
        
        $items = [];
        while ($item = $cart_items->fetch_assoc()) {
            $items[] = $item;
        }

        sendSuccess([
            'count' => $cart->getCartCount(),
            'items' => $items,
            'total' => $cart->getCartTotal()
        ]);
    }
    // Handle POST requests
    elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['action'])) {
            sendError('No action specified');
        }

        $action = $_POST['action'];
        $item_id = isset($_POST['item_id']) ? (int)$_POST['item_id'] : null;
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

        // Validate item_id for relevant actions
        if (in_array($action, ['add', 'remove', 'update']) && empty($item_id)) {
            sendError('Item ID is required');
        }

        // Additional validation for specific actions
        switch ($action) {
            case 'add':
                if ($quantity < 1) {
                    sendError('Quantity must be at least 1');
                }
                if (!$cart->addItem($item_id, $quantity)) {
                    sendError('Failed to add item to cart', 500);
                }
                break;
                
            case 'remove':
                if (!$cart->removeItem($item_id)) {
                    sendError('Failed to remove item from cart', 500);
                }
                break;
                
            case 'update':
                if ($quantity < 0) {
                    sendError('Quantity cannot be negative');
                }
                if (!$cart->updateQuantity($item_id, $quantity)) {
                    sendError('Failed to update item quantity', 500);
                }
                break;
                
            case 'clear':
                if (!$cart->clearCart()) {
                    sendError('Failed to clear cart', 500);
                }
                break;
                
            default:
                sendError('Invalid action');
        }

        // Return updated cart data
        $cart_items = $cart->getCartItems();
        if ($cart_items === false) {
            sendError('Failed to retrieve updated cart', 500);
        }
        
        $items = [];
        while ($item = $cart_items->fetch_assoc()) {
            $items[] = $item;
        }

        sendSuccess([
            'count' => $cart->getCartCount(),
            'items' => $items,
            'total' => $cart->getCartTotal()
        ]);
    }
    else {
        sendError('Invalid request method', 405);
    }
} catch (Exception $e) {
    // Log full error with stack trace
    error_log("Unhandled exception: " . $e->getMessage() . "\n" . $e->getTraceAsString());
    sendError('An unexpected error occurred', 500, [
        'error' => $e->getMessage(),
        'trace' => $e->getTrace()
    ]);
}
?>