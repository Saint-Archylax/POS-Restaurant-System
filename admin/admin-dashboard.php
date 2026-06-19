<?php include('includes/header.php') ?>

<!-- CSS -->
<link href="assets/css/material-dashboard.min.css" rel="stylesheet">
<link href="assets/css/style.css" rel="stylesheet">

<!-- JS -->
<script src="assets/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/chartjs.min.js"></script>
<script src="assets/js/perfect-scrollbar.min.js"></script>
<script src="assets/js/smooth-scrollbar.min.js"></script>
<script src="assets/js/script.js"></script>

<div class="container-fluid py-2">
    <div class="row">
        <div class="ms-3">
            <h3 class="mb-0 h4 font-weight-bolder">Dashboard</h3>
            <p class="mb-4">Check your sales performance</p>
        </div>
    </div>

    <!-- First Row: Day and Week -->
    <div class="row">
        <!-- Day Chart -->
        <div class="col-lg-6 col-md-12 mb-4">
            <div class="card h-100">
                <div class="card-header p-2">
                    <h6 class="mb-0">Today's Sales</h6>
                </div>
                <div class="card-body p-2">
                    <canvas id="daySalesChart" height="220"></canvas>
                    <div class="d-flex justify-content-between mt-2">
                        <div class="small-box bg-light p-2 text-center rounded">
                            <small class="d-block">Total Sales</small>
                            <span class="font-weight-bold" id="todaySales">₱0.00</span>
                        </div>
                        <div class="small-box bg-light p-2 text-center rounded">
                            <small class="d-block">Transactions</small>
                            <span class="font-weight-bold" id="todayCount">0</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Week Chart -->
        <div class="col-lg-6 col-md-12 mb-4">
            <div class="card h-100">
                <div class="card-header p-2">
                    <h6 class="mb-0">Weekly Sales</h6>
                </div>
                <div class="card-body p-2">
                    <canvas id="weekSalesChart" height="220"></canvas>
                    <div class="d-flex justify-content-between mt-2">
                        <div class="small-box bg-light p-2 text-center rounded">
                            <small class="d-block">Total Sales</small>
                            <span class="font-weight-bold" id="weekSales">₱0.00</span>
                        </div>
                        <div class="small-box bg-light p-2 text-center rounded">
                            <small class="d-block">Transactions</small>
                            <span class="font-weight-bold" id="weekCount">0</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Second Row: Month and Year -->
    <div class="row">
        <!-- Month Chart -->
        <div class="col-lg-6 col-md-12 mb-4">
            <div class="card h-100">
                <div class="card-header p-2">
                    <h6 class="mb-0">Monthly Sales</h6>
                </div>
                <div class="card-body p-2">
                    <canvas id="monthSalesChart" height="220"></canvas>
                    <div class="d-flex justify-content-between mt-2">
                        <div class="small-box bg-light p-2 text-center rounded">
                            <small class="d-block">Total Sales</small>
                            <span class="font-weight-bold" id="monthSales">₱0.00</span>
                        </div>
                        <div class="small-box bg-light p-2 text-center rounded">
                            <small class="d-block">Transactions</small>
                            <span class="font-weight-bold" id="monthCount">0</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Year Chart -->
        <div class="col-lg-6 col-md-12 mb-4">
            <div class="card h-100">
                <div class="card-header p-2">
                    <h6 class="mb-0">Yearly Sales</h6>
                </div>
                <div class="card-body p-2">
                    <canvas id="yearSalesChart" height="220"></canvas>
                    <div class="d-flex justify-content-between mt-2">
                        <div class="small-box bg-light p-2 text-center rounded">
                            <small class="d-block">Total Sales</small>
                            <span class="font-weight-bold" id="yearSales">₱0.00</span>
                        </div>
                        <div class="small-box bg-light p-2 text-center rounded">
                            <small class="d-block">Transactions</small>
                            <span class="font-weight-bold" id="yearCount">0</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php') ?>