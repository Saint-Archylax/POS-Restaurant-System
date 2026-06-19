<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"/>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <link rel="stylesheet" href="home.css">
    
</head>
<body>
<div class="header">
    <div class="header-menu">
        <label for="menu-lists">
            <i class="ri-menu-line" id="menu"></i>
        </label>
        <div class="search-box">
            <i class="ri-search-2-line"></i>
            <input type="text" name="search" placeholder="Search">
        </div>
    </div>
</div>

<!-- Cover Design -->
<div class="cover">
    <div class="cover-overlay">
        <h1 id="cssText">Transaction History.</h1>
        <span id="cssText">Recently Purchased Products.</span>
    </div>
</div>

<br><br>

<!-- Menu Bar -->
<input type="checkbox" id="menu-lists">
<div class="container-menu">
    <div class="btnclose">
        <label for="menu-lists">
            <i class="fa-solid fa-arrow-left"></i>
        </label>
    </div>
    <ul class="ps-0">
        <li class="hover-menu">
            <a href="home.php" class="Home">
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

<div class="container">
    <table class="table">
        <thead>
            <tr class="table-primary">
                <th scope="col">Order ID</th>
                <th scope="col">Items</th>
                <th scope="col">Total</th>
                <th scope="col">Cash Given</th>
                <th scope="col">Change</th>
                <th scope="col">Date/Time</th>
                <th scope="col">Status</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            require_once 'db_connection.php';
            
            $sql = "SELECT 
        o.Order_ID, 
        o.Total_Amount, 
        o.Order_Status, 
        p.Cash_Given, 
        (p.Cash_Given - o.Total_Amount) AS Change_Given, 
        o.Order_Date,
        MAX(th.Timestamp) AS Transaction_Time,
        GROUP_CONCAT(th.Action SEPARATOR ', ') AS Action,
        GROUP_CONCAT(th.Details SEPARATOR '; ') AS Details
    FROM Orders o
    JOIN Payments p ON o.Order_ID = p.Order_ID
    LEFT JOIN Transaction_History th ON o.Order_ID = th.Order_ID
    GROUP BY o.Order_ID, o.Total_Amount, o.Order_Status, p.Cash_Given, o.Order_Date
    ORDER BY o.Order_Date DESC";
            
            $result = $conn->query($sql);
            
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <th scope='row'>{$row['Order_ID']}</th>
                            <td>
                                <button class='btn btn-sm btn-primary view-items' 
                                        data-order='{$row['Order_ID']}'>
                                    View Items
                                </button>
                            </td>
                            <td>₱" . number_format($row['Total_Amount'], 2) . "</td>
                            <td>₱" . number_format($row['Cash_Given'], 2) . "</td>
                            <td>₱" . number_format($row['Change_Given'], 2) . "</td>
                            <td>{$row['Order_Date']}</td>
                            <td><span class='badge bg-" . ($row['Order_Status'] == 'Paid' ? 'success' : 'danger') . "'>
                                {$row['Order_Status']}
                            </span></td>
                            <td>";
                    
                    if ($row['Order_Status'] == 'Paid') {
                        echo "<button class='btn btn-sm btn-warning refund-btn' 
                              data-order='{$row['Order_ID']}'>
                              Refund
                            </button>";
                    }
                    
                    echo "</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='8'>No orders found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<!-- Modal for displaying order items -->
<div class="modal fade" id="itemsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Order Items</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="itemsModalBody">
                <!-- Items will be loaded here via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Refund Modal -->
<div class="modal fade" id="refundModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Process Refund</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to refund this order?</p>
                <form id="refundForm">
                    <input type="hidden" name="order_id" id="refundOrderId">
                    <div class="mb-3">
                        <label for="refundReason" class="form-label">Reason for refund</label>
                        <textarea class="form-control" id="refundReason" name="reason" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmRefund">Process Refund</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // View items functionality
    $('.view-items').click(function() {
        var orderId = $(this).data('order');
        
        $.ajax({
            url: 'get_order_items.php',
            type: 'GET',
            data: { order_id: orderId },
            success: function(response) {
                $('#itemsModalBody').html(response);
                $('#itemsModal').modal('show');
            }
        });
    });

    // Refund functionality
    $('.refund-btn').click(function() {
        var orderId = $(this).data('order');
        $('#refundOrderId').val(orderId);
        $('#refundModal').modal('show');
    });

    $('#confirmRefund').click(function() {
        var formData = $('#refundForm').serialize();
        
        $.ajax({
            url: 'process_refund.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                if(response.success) {
                    location.reload(); // Refresh to show updated status
                } else {
                    alert('Error: ' + response.error);
                }
            },
            error: function() {
                alert('Error processing refund');
            }
        });
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
</body>
</html>