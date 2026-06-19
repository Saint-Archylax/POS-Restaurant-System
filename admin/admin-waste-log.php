<?php 
// Use correct path to db_connection.php
require_once __DIR__ . '/../db_connection.php';

// Include header with correct path
require_once __DIR__ . '/includes/header.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Waste Log</title>
    
    <!-- CSS Links -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"/>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../home.css">
    
    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>

<div class="ms-3">
    <h3 class="mb-0 h4 font-weight-bolder">Waste/Stock Adjustment Log</h3>
    <p class="mb-4">Adjust stock or Product waste</p>
</div>

<div class="mb-3 ms-3">
    <button class="btn btn-primary" id="viewWasteHistory">View Waste History</button>
</div>

<div class="update-container" id="update-container">
    <div class="card-lists">
        <?php
        if ($conn && $conn->connect_error === null) {
            $sql = "SELECT * FROM menu_items ORDER BY is_available DESC, Item_Name ASC";
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $availabilityClass = $row['is_available'] ? '' : 'unavailable-item';
                    echo '<div class="card '.$availabilityClass.'" data-item-id="'.$row['Item_ID'].'">
                            <img src="../img/'.basename($row['Image_Path']).'"/>
                            <h5 class="food-name">'.$row['Item_Name'].'</h5>
                            <div class="stock-info">
                                Stock: <span class="stock-count">'.($row['stock_quantity'] ?? 0).'</span>
                                '.($row['is_available'] ? '' : '<span class="badge bg-danger">Out of Stock</span>').'
                            </div>
                            <div class="card-price">
                                <div class="price">₱'.number_format($row['Price'], 2).'</div>
                                <button class="btn btn-sm btn-warning adjust-stock" 
                                       data-item-id="'.$row['Item_ID'].'"
                                       data-item-name="'.$row['Item_Name'].'">
                                    <i class="fa-regular fa-pen-to-square"></i> Adjust
                                </button>
                                <!-- Quick Add button removed -->
                            </div>
                          </div>';
                }
            } else {
                echo '<p>No products found</p>';
            }
        } else {
            echo '<div class="alert alert-danger">Database connection error</div>';
        }
        ?>
    </div>
</div>

<!-- Stock Adjustment Modal -->
<div class="modal fade" id="stockModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Adjust Stock</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="stockForm">
                    <input type="hidden" name="item_id" id="adjustItemId">
                    <div class="mb-3">
                        <label class="form-label">Product: <strong id="itemNameDisplay"></strong></label>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Current Stock: <strong id="currentStockDisplay"></strong></label>
                    </div>
                    <div class="mb-3">
                        <label for="adjustmentType" class="form-label">Action</label>
                        <select class="form-select" name="adjustment_type" id="adjustmentType" required>
                            <option value="add">Add Stock</option>
                            <option value="remove">Record Waste</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control" name="quantity" id="quantity" min="1" required>
                    </div>
                    <div class="mb-3" id="reasonField">
                        <label for="reason" class="form-label">Waste Reason</label>
                        <textarea class="form-control" name="reason" id="reason" rows="3" placeholder="Spoilage, breakage, etc."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveStockAdjustment">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Waste History Modal -->
<div class="modal fade" id="historyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Waste History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Date</th>
                                <th>Item</th>
                                <th>Qty Lost</th>
                                <th>Total Loss</th>
                                <th>Reason</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="wasteHistoryBody">
                            <!-- AJAX loaded content -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
    .unavailable-item {
        opacity: 0.7;
        background-color: #f8f9fa;
    }
    .unavailable-item .food-name {
        text-decoration: line-through;
    }
    .stock-info {
        font-size: 0.9rem;
        margin-bottom: 8px;
    }
</style>

<script>
// Verify libraries are loaded
console.log('jQuery version:', $.fn.jquery);
console.log('Bootstrap version:', bootstrap.Tooltip.VERSION);

$(document).ready(function() {

    $(document).on('click', '.quick-add-stock', function() {
    const itemId = $(this).data('item-id');

    $.post('../process_stock_adjustment.php', {
        item_id: itemId,
        adjustment_type: 'add',
        quantity: 5
    }, function(response) {
        if (response.success) {
            location.reload();
        } else {
            alert('Failed to add stock: ' + (response.error || 'Unknown error'));
        }
    }, 'json').fail(function() {
        alert('Server error while adding stock.');
    });
});

    // Set base path for AJAX calls
    const basePath = '../';
    
    // Initially hide reason field
    $('#reasonField').hide();

    // Toggle reason field based on adjustment type
    $('#adjustmentType').change(function() {
        $('#reasonField').toggle($(this).val() === 'remove');
    });

    // Handle adjust stock button click
    $(document).on('click', '.adjust-stock', function() {
        var itemId = $(this).data('item-id');
        var itemName = $(this).data('item-name');
        
        $('#adjustItemId').val(itemId);
        $('#itemNameDisplay').text(itemName);
        
        // Get current stock via AJAX
        $.get(basePath + 'get_current_stock.php', {item_id: itemId}, function(response) {
            if(response.error) {
                alert(response.error);
                return;
            }
            $('#currentStockDisplay').text(response.stock);
            var stockModal = new bootstrap.Modal(document.getElementById('stockModal'));
            stockModal.show();
        }, 'json').fail(function() {
            alert('Error fetching current stock');
        });
    });

    // Save stock adjustment
    $('#saveStockAdjustment').click(function() {
        var formData = $('#stockForm').serialize();
        
        $.post(basePath + 'process_stock_adjustment.php', formData, function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('Error: ' + (response.error || 'Unknown error occurred'));
            }
        }, 'json').fail(function() {
            alert('Error processing adjustment');
        });
    });

    // View waste history
    $('#viewWasteHistory').click(function() {
        $.get(basePath + 'get_waste_history.php', function(response) {
            $('#wasteHistoryBody').html(response);
            var historyModal = new bootstrap.Modal(document.getElementById('historyModal'));
            historyModal.show();
        }).fail(function() {
            alert('Error loading waste history');
        });
    });

    // Add new stock button
    $('#addStockBtn').click(function() {
        alert('Add new stock feature would open here');
    });
});
</script>

<?php 
// Include footer with correct path
require_once __DIR__ . '/includes/footer.php'; 
?>