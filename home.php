<?php
session_start();
require_once 'db_connection.php';
require_once 'cart.php';

$cart = new Cart($conn);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    // Debugging: Log the POST data
    // error_log("Add to cart POST data: " . print_r($_POST, true));
    
    if (isset($_POST['item_id'])) {
        $item_id = (int)$_POST['item_id'];
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
        
        // Verify item exists in database
        $check_sql = "SELECT Item_ID FROM Menu_Items WHERE Item_ID = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("i", $item_id);
        $check_stmt->execute();
        
        if ($check_stmt->get_result()->num_rows > 0) {
            $cart->addItem($item_id, $quantity);
            $_SESSION['cart_message'] = "Item added to cart!";
        } else {
            $_SESSION['cart_message'] = "Invalid item!";
        }
    }
    
    // Redirect to prevent form resubmission
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

$cart_count = $cart->getCartCount();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>

    <link rel="stylesheet" href="./home.css">
     <!--remixions link-->
     <link
    href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css"
    rel="stylesheet"/>

    <!--awesomeicons link-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"/>

    <!--google fonts link-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">


</head>
<body>
    <div class="header">
        <nav>
            <h2 class="title">DineTrack: Smart Restaurant Order & Sales Tracker</h2>
            <ul>
                <li><a href="navbars/products.html">Products</a></li>
                <li><a href="navbars/about.html">About</a></li>
                <li><a href="navbars/contact.html">Contact</a></li>
                <li><a href="navbars/account.php">Account</a></li>
            </ul>
        </nav>
        <div class="header-menu">

            <label for="menu-lists">
                <i class="ri-menu-line" id="menu"></i>
            </label>

            <div class="search-box">
                <i class="ri-search-2-line"></i>
                <input type="text" name="search" placeholder="Search menu items..." autocomplete="off">
            </div>

            <div class="cart-icon">
                <label for="sidebar-active">
                    <i class="ri-shopping-cart-line" id="mainCart"></i>
                    </label>
                <span><?php echo $cart_count; ?></span>
            </div>

        </div>
    </div>

    <!--Cover Design-->
    <div class="cover">
        <div class="cover-overlay">
            <h1>Serve Faster.</h1>
            <span>Taste Better.</span>
        </div>
    </div>

    <?php
$items = $conn->query(
    "SELECT Item_ID, Item_Name, Price, Image_Path
       FROM Menu_Items
      WHERE is_available = 1"
);
?>
<h2 class="menuLabel">Your Menu Items</h2>

<div class="container">
  <div class="card-lists">
<?php while ($row = $items->fetch_assoc()): ?>
    <div class="card">
      <img src="<?php echo htmlspecialchars($row['Image_Path']); ?>"
           alt="<?php echo htmlspecialchars($row['Item_Name']); ?>">
      <h4 class="food-name"><?php echo htmlspecialchars($row['Item_Name']); ?></h4>
      <br>
      <div class="card-price">
        <div class="price">&#8369; <?php echo number_format($row['Price'], 2); ?></div>
        <form class="add-to-cart-form" method="POST" action="">
          <input type="hidden" name="item_id"
                 value="<?php echo $row['Item_ID']; ?>">
          <input type="hidden" name="add_to_cart" value="1">
          <button type="submit" class="add-to-cart">
            <i class="ri-add-circle-line fa-2x"></i>
          </button>
        </form>
      </div>
    </div>
<?php endwhile; ?>
  </div>
</div>

    <!--Cart-Section-->
    <nav>
        <input type="checkbox" id="sidebar-active"> 

        <div class="links-container">
            <label for="sidebar-active">
                <i class="ri-close-large-fill"></i>
            </label>
            
            <div class="cart-items">
    <h3 id="CS-label">Customer's Cart</h3>
    <?php
    $cart_items = $cart->getCartItems();
    $cart_total = 0;
    
    if ($cart_items->num_rows > 0) {
        while ($item = $cart_items->fetch_assoc()) {
            $item_total = $item['Price'] * $item['Quantity'];
            $cart_total += $item_total;
            ?>
            <div class="item">
                <span><?php echo $item['Item_Name']; ?> (x<?php echo $item['Quantity']; ?>)</span>
                <span>&#8369 <?php echo number_format($item_total, 2); ?></span>
                <form method="POST" action="cart_actions.php" style="display:inline;">
                    <input type="hidden" name="action" value="remove">
                    <input type="hidden" name="item_id" value="<?php echo $item['Item_ID']; ?>">
                    <button type="submit" style="background:none; border:none; color:red; cursor:pointer;">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                </form>
            </div>
            <?php
        }
    } else {
        echo '<div class="item">Your cart is empty</div>';
    }
    ?>
</div>

<div class="sidebar-details">
    <div class="total-amount">
        <h5>Total</h5>
        <div class="cart-total">&#8369 <?php echo number_format($cart_total, 2); ?></div>
    </div>
    <div>
        <form method="POST" action="checkout.php">
            <button type="submit" class="checkout-btn">Checkout</button>
        </form>
    </div>
</div>

<!--Menu Bar-->
    <input type="checkbox" id="menu-lists">

        <div class="container-menu">
            <div class="btnclose">
                <label for="menu-lists">
                    <i class="fa-solid fa-arrow-left"></i>
                </label>
            </div>

            <ul>
                <li class="hover-menu">
                    <a href="#" class="Home">
                        <span class="icon"><i class="fa-solid fa-house"></i></span>
                        <span class="text"> Home</span>
                    </a>
                </li>

                <li class="hover-menu">
                    <a href="history.php" class="History">
                        <span class="icon"><i class="fa-solid fa-timeline"></i></span>
                        <span class="text"> History</span>
                    </a>
                </li>

                <li class="hover-menu" id="logout-ex">
                    <a href="index.html" class="Logout">
                        <span class="icon"><i class="fa-solid fa-arrow-right-from-bracket"></i></span>
                        <span class="text"> Logout</span>
                    </a>
                </li>
            </ul>

        </div>

<script src="javascript.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
</body>
</html>
