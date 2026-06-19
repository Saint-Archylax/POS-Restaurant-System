document.addEventListener('DOMContentLoaded', function() {
    // Initialize all charts
    initDayChart();
    initWeekChart();
    initMonthChart();
    initYearChart();
    
    // Load all data
    loadChartData();
});

// Chart Initialization Functions
function initDayChart() {
    const ctx = document.getElementById('daySalesChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [],
                datasets: [{
                    label: 'Sales (₱)',
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1,
                    data: []
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: { 
                        display: true, 
                        text: 'Today\'s Sales',
                        font: { size: 14 }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return '₱' + context.raw.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₱' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }
}

function initWeekChart() {
    const ctx = document.getElementById('weekSalesChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Sales (₱)',
                    backgroundColor: 'rgba(75, 192, 192, 0.5)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1,
                    data: []
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: { 
                        display: true, 
                        text: 'Weekly Sales',
                        font: { size: 14 }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return '₱' + context.raw.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₱' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }
}

function initMonthChart() {
    const ctx = document.getElementById('monthSalesChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Sales (₱)',
                    borderColor: 'rgba(153, 102, 255, 1)',
                    borderWidth: 2,
                    fill: false,
                    data: []
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: { 
                        display: true, 
                        text: 'Monthly Sales',
                        font: { size: 14 }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return '₱' + context.raw.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₱' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }
}

function initYearChart() {
    const ctx = document.getElementById('yearSalesChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Sales (₱)',
                    borderColor: 'rgba(255, 159, 64, 1)',
                    borderWidth: 2,
                    fill: false,
                    data: []
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: { 
                        display: true, 
                        text: 'Yearly Sales',
                        font: { size: 14 }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return '₱' + context.raw.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₱' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }
}

// Data Loading Functions
function loadChartData() {
    fetchDayData();
    fetchWeekData();
    fetchMonthData();
    fetchYearData();
}

function fetchDayData() {
    fetch('includes/get_sales_data.php?period=day')
        .then(response => response.json())
        .then(data => {
            const chart = Chart.getChart('daySalesChart');
            if (!chart) return;

            if (data.empty || !data.length) {
                chart.data.labels = [];
                chart.data.datasets[0].data = [];
                chart.update();
                document.getElementById('todaySales').textContent = '₱0.00';
                document.getElementById('todayCount').textContent = '0';
                return;
            }

            chart.data.labels = data.map(item => item.hour + ':00');
            chart.data.datasets[0].data = data.map(item => parseFloat(item.total) || 0);
            chart.update();

            // Update summary boxes
            const total = data.reduce((sum, item) => sum + (parseFloat(item.total) || 0), 0);
            document.getElementById('todaySales').textContent = `₱${total.toFixed(2)}`;
            document.getElementById('todayCount').textContent = data.length;
        })
        .catch(error => console.error('Day data error:', error));
}

function fetchWeekData() {
    fetch('includes/get_sales_data.php?period=week')
        .then(response => response.json())
        .then(data => {
            const chart = Chart.getChart('weekSalesChart');
            if (!chart) return;

            if (data.empty || !data.length) {
                chart.data.datasets[0].data = Array(7).fill(0);
                chart.update();
                document.getElementById('weekSales').textContent = '₱0.00';
                document.getElementById('weekCount').textContent = '0';
                return;
            }

            const days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
            const salesData = Array(7).fill(0);
            
            data.forEach(item => {
                const dayIndex = days.findIndex(d => d.toLowerCase() === item.day.toLowerCase());
                if (dayIndex !== -1) salesData[dayIndex] = parseFloat(item.total) || 0;
            });
            
            chart.data.datasets[0].data = salesData;
            chart.update();

            // Update summary boxes
            const total = data.reduce((sum, item) => sum + (parseFloat(item.total) || 0), 0);
            document.getElementById('weekSales').textContent = `₱${total.toFixed(2)}`;
            document.getElementById('weekCount').textContent = data.length;
        })
        .catch(error => console.error('Week data error:', error));
}

function fetchMonthData() {
    fetch('includes/get_sales_data.php?period=month')
        .then(response => response.json())
        .then(data => {
            const chart = Chart.getChart('monthSalesChart');
            if (!chart) return;

            if (data.empty || !data.length) {
                chart.data.labels = [];
                chart.data.datasets[0].data = [];
                chart.update();
                document.getElementById('monthSales').textContent = '₱0.00';
                document.getElementById('monthCount').textContent = '0';
                return;
            }

            chart.data.labels = data.map(item => new Date(item.date).getDate());
            chart.data.datasets[0].data = data.map(item => parseFloat(item.total) || 0);
            chart.update();

            // Update summary boxes
            const total = data.reduce((sum, item) => sum + (parseFloat(item.total) || 0), 0);
            document.getElementById('monthSales').textContent = `₱${total.toFixed(2)}`;
            document.getElementById('monthCount').textContent = data.length;
        })
        .catch(error => console.error('Month data error:', error));
}

function fetchYearData() {
    fetch('includes/get_sales_data.php?period=year')
        .then(response => response.json())
        .then(data => {
            const chart = Chart.getChart('yearSalesChart');
            if (!chart) return;

            if (data.empty || !data.length) {
                chart.data.datasets[0].data = Array(12).fill(0);
                chart.update();
                document.getElementById('yearSales').textContent = '₱0.00';
                document.getElementById('yearCount').textContent = '0';
                return;
            }

            const salesData = Array(12).fill(0);
            data.forEach(item => {
                const monthIndex = parseInt(item.month_num) - 1;
                if (monthIndex >= 0 && monthIndex < 12) {
                    salesData[monthIndex] = parseFloat(item.total) || 0;
                }
            });
            
            chart.data.datasets[0].data = salesData;
            chart.update();

            // Update summary boxes
            const total = data.reduce((sum, item) => sum + (parseFloat(item.total) || 0), 0);
            document.getElementById('yearSales').textContent = `₱${total.toFixed(2)}`;
            document.getElementById('yearCount').textContent = data.length;
        })
        .catch(error => console.error('Year data error:', error));
}
