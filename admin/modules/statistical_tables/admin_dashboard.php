<!DOCTYPE html>
<html lang="vi">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link
            rel="stylesheet"
            href="../assets/bootstrap/css/bootstrap.min.css"
        />
        <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.css" />
        <link
            rel="stylesheet"
            href="../assets/bootstrap/js/bootstrap.bundle.js"
        />
        <link
            rel="stylesheet"
            href="../assets/bootstrap/js/bootstrap.bundle.min.js"
        />
        <link
            rel="stylesheet"
            href="https://use.fontawesome.com/releases/v5.7.2/css/all.css"
            integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr"
            crossorigin="anonymous"
        />
        <link rel="stylesheet" href="css/style.css" />
    <title>PiHung_Admin</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Styling for the entire page */
        body { 

    background-color: #75dd80; 
    margin: 0; 
    padding: 0; 
    color: #333; /* Default text color */
    }

        
        .container-fluid { 
            width: 90%; 
            margin: 20px auto; 
            background-color: #fff; /* White background for the main container */
            padding: 20px; /* Padding around the container */
            border-radius: 8px; /* Rounded corners */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Subtle shadow */
        }
        h1 { 
            margin-bottom: 20px; 
            color: #333; /* Darker text color for headings */
        }
        
        .card-container { 
            display: flex; 
            justify-content: space-between; 
            flex-wrap: nowrap; /* Ensure cards stay in a single row */
            gap: 20px; /* Add spacing between cards */
        }
        
        .card { 
            background: #fff; /* White background for cards */
            border-radius: 8px; 
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); 
            padding: 20px; 
            flex: 1; 
            min-width: 150px; /* Adjust as needed */
            color: #333; /* Dark text color for card content */
            font-weight: bold; 
            text-align: center; 
            display: flex;
            flex-direction: column;
            align-items: center; /* Center content horizontally */
            justify-content: center; /* Center content vertically */
        }
        
        .card p { 
            margin-bottom: 10px; 
        }
        
        .card h3 { 
            margin: 0; 
            font-size: 24px; 
        }
        
        .card:nth-child(1) { background-color: #7ae2ad; } /* Green */
        .card:nth-child(2) { background-color: #ffffff; } /* White */
        .card:nth-child(3) { background-color: #ffca28; } /* Yellow */
        .card:nth-child(4) { background-color: #42a5f5; } /* Blue */
        .card:nth-child(5) { background-color: #ab47bc; } /* Purple */
        
        .btn-primary { 
            background-color: #4fcc5c; 
            color: #fff; 
            border: none; 
            padding: 10px 20px; 
            border-radius: 5px; 
            cursor: pointer; 
            font-size: 16px; 
        }
        
        .btn-primary:hover { 
            background-color: #0056b3; 
        }
        
        .text-center { 
            text-align: center; 
        }
        
        .modal { 
            display: none; 
            position: fixed; 
            z-index: 1000; 
            left: 0; 
            top: 0; 
            width: 100%; 
            height: 100%; 
            overflow: auto; 
            background-color: rgba(0,0,0,0.4); 
        }
        
        .modal-content { 
            background-color: #fff; 
            margin: 15% auto; 
            padding: 20px; 
            border: 1px solid #888; 
            width: 80%; 
            border-radius: 8px; 
        }
        
        .close { 
            color: #aaa; 
            float: right; 
            font-size: 28px; 
            font-weight: bold; 
        }
        
        .close:hover, .close:focus { 
            color: black; 
            text-decoration: none; 
            cursor: pointer; 
        }
        
        .radio-group { 
            margin: 10px 0; 
        }
        
        .radio-group label { 
            margin-right: 10px; 
        }

        /* Styling for specific order date selection */
        .order-date-controls, .date-range-controls { 
            margin-top: 10px; 
            background-color: #fff; /* White background for controls */
            padding: 15px; 
            border-radius: 8px; 
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Subtle shadow */
        }
        
        .date-range-controls label,
        .order-date-controls label {
            color: #333; /* Dark text color */
        }

        /* New container for charts */
        .chart-container {
            margin-top: 20px;
        }
        
        .chart-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .text-dark-blue {
            color: #095409; /* Dark greenish-blue */
            font-size: 23px; /* Slightly smaller font size */
        }
        .container-fluid {
    width: 90%;
    margin: 20px auto;
    background-color: #fff;
    padding: 10px;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    margin-left: 1px;
    margin-right: 1px;
        }
    </style>
</head>
<body>
    <div id="content" class="container-fluid">
        <h1 class="text-center">THỐNG KÊ DOANH THU</h1>

        <div class="form-container">
            <div class="card">
            <h2 class="text-center" style="color: white;">Chọn thời gian thống kê</h2>

                <div class="radio-group text-center">
                    <label>
                        <input type="radio" name="stat-type" value="date-range"> Khoảng thời gian
                    </label>
                    <label>
                        <input type="radio" name="stat-type" value="specific-date"> Ngày cụ thể
                    </label>
                    <label>
                        <input type="radio" name="stat-type" value="total"> Tổng thống kê
                    </label>
                </div>
            </div>

            <div class="card">
                <form id="filter-form" class="text-center">
                    <div id="date-range-controls" class="date-range-controls">
                        <label for="start-date">Từ ngày:</label>
                        <input type="date" id="start-date" name="start-date">

                        <label for="end-date">Đến ngày:</label>
                        <input type="date" id="end-date" name="end-date">
                    </div>

                    <div id="order-date-controls" class="order-date-controls">
                        <label for="order-date">Ngày:</label>
                        <input type="date" id="order-date" name="order-date">
                    </div>

                    <button type="submit" class="btn-primary">Thống kê</button>
                    <button id="export-report" class="btn-primary" style="background-color: #007bff;">Xuất Báo Cáo</button>
                </form>
            </div>
        </div>

        <div class="card-container">
            <div class="card"style="background-color: #4caf50; color: #fff;">
                <p>Đơn hàng đã duyệt</p>
                <h3 id="donhang_Duyet">0</h3>
            </div>
            <div class="card"style="background-color: #f43d3d; color: #fff;">
                <p>Số đơn hàng đã bán</p>
                <h3 id="completed-orders">0</h3>
            </div>
            <div class="card">
                <p>Sản phẩm hiện có trong kho</p>
                <h3 id="current-products">0</h3>
            </div>
            <div class="card">
                <p>Doanh thu tổng cộng</p>
                <h3 id="total-revenue">0</h3>
            </div>
            
        </div>
        <div class="chart-container">
 
    <div class="chart-card">
         <p class="text-center">Tuần vừa qua</p>
        <canvas id="chart3" width="400" height="200"></canvas>
          </div>
            <div class="chart-card">
            <p class="text-center">Tháng vừa qua</p>
           <canvas id="chart2" width="400" height="200"></canvas>
           </div>
            <div class="chart-card">
         <p class="text-center">Năm vừa qua</p>
        <canvas id="chart1" width="400" height="200"></canvas>
    </div>
</div>
        
    </div>
    <script>
       document.addEventListener('DOMContentLoaded', () => {
    const radioButtons = document.querySelectorAll('input[name="stat-type"]');
    const dateRangeControls = document.getElementById('date-range-controls');
    const orderDateControls = document.getElementById('order-date-controls');

    // Set default state for date controls
    dateRangeControls.style.display = 'none';
    orderDateControls.style.display = 'none';
    
    // Show appropriate controls based on selected radio button
    radioButtons.forEach(radio => {
        radio.addEventListener('change', () => {
            dateRangeControls.style.display = 'none';
            orderDateControls.style.display = 'none';
            document.getElementById('start-date').required = false;
            document.getElementById('end-date').required = false;
            document.getElementById('order-date').required = false;

            if (radio.value === 'date-range') {
                dateRangeControls.style.display = 'block';
                document.getElementById('start-date').required = true;
                document.getElementById('end-date').required = true;
            } else if (radio.value === 'specific-date') {
                orderDateControls.style.display = 'block';
                document.getElementById('order-date').required = true;
            }
        });
    });

    // Trigger the change event on load to set the initial state correctly
    document.querySelector('input[name="stat-type"]:checked')?.dispatchEvent(new Event('change'));
            document.getElementById('filter-form').addEventListener('submit', function(e) {
                e.preventDefault(); // Ngăn chặn form submit mặc định

                const statType = document.querySelector('input[name="stat-type"]:checked').value;
                const startDate = document.getElementById('start-date').value;
                const endDate = document.getElementById('end-date').value;
                const orderDate = document.getElementById('order-date').value;

                // Kiểm tra ngày bắt đầu và ngày kết thúc
                if (statType === 'date-range') {
                    if (startDate && endDate) {
                        const start = new Date(startDate);
                        const end = new Date(endDate);

                        if (start > end) {
                            alert('Nhập ngày không hợp lệ.');
                            return;
                        } 
                    }
                }

                let fetchUrl;

                if (statType === 'date-range') {
                    fetchUrl = `modules/statistical_tables/completed_orders.php?start_date=${startDate}&end_date=${endDate}`;
                } else if (statType === 'specific-date') {
                    fetchUrl = `modules/statistical_tables/completed_orders.php?order_date=${orderDate}`;
                } else {
                    fetchUrl = 'modules/statistical_tables/completed_orders.php';
                }

                fetch(fetchUrl)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('completed-orders').innerText = data.completedOrders || 0;
                    })
                    .catch(error => console.error('Error fetching completed orders:', error));

                fetch('modules/statistical_tables/current_products.php')
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('current-products').innerText = data.currentProducts || 0;
                    })
                    .catch(error => console.error('Error fetching current products:', error));

                fetch(fetchUrl.replace('completed_orders', 'total_revenue'))
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('total-revenue').innerText = data.totalRevenue || 0;
                    })
                    .catch(error => console.error('Error fetching total revenue:', error));

                fetch(fetchUrl.replace('completed_orders', 'donhang_Duyet'))
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('donhang_Duyet').innerText = data.donhang_Duyet || 0;
                    })
                    .catch(error => console.error('Error fetching donhang_Duyet:', error));

            });

            document.getElementById('export-report').addEventListener('click', () => {
                const statType = document.querySelector('input[name="stat-type"]:checked').value;
                const startDate = document.getElementById('start-date').value;
                const endDate = document.getElementById('end-date').value;
                const orderDate = document.getElementById('order-date').value;

                if ((statType === 'date-range' && (!startDate || !endDate)) ||
                    (statType === 'specific-date' && !orderDate)) {
                   
                    return;
                }

                if (statType === 'date-range') {
                    if (new Date(startDate) > new Date(endDate)) {
                        alert('Nhập ngày không hợp lệ.');
                        return;
                    }
                    
                }

                const url = `modules/statistical_tables/export_report.php?stat_type=${statType}&start_date=${startDate}&end_date=${endDate}&date=${orderDate}`;
                window.location.href = url;
            });

               // Fetch and render weekly data
    fetch('modules/statistical_tables/thongketuan.php')
        .then(response => response.json())
        .then(data => {
            const ctx3 = document.getElementById('chart3').getContext('2d');
            new Chart(ctx3, {
                type: 'bar',
                data: {
                    labels: data.map(item => item.date3),
                    datasets: [{
                        label: 'Doanh Thu Tuần',
                        data: data.map(item => item.sales3),
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(tooltipItem) {
                                    return 'Doanh Thu: ' + tooltipItem.raw.toLocaleString() + ' VNĐ';
                                }
                            }
                        }
                    }
                }
            });
        });

 
            // Fetch and render monthly data
            fetch('modules/statistical_tables/thongkethang.php')
                .then(response => response.json())
                .then(data => {
                    const ctx2 = document.getElementById('chart2').getContext('2d');
                    new Chart(ctx2, {
                        type: 'bar',
                        data: {
                            labels: data.map(item => item.date2),
                            datasets: [{
                                label: 'Doanh Thu 30 Ngày Qua',
                                data: data.map(item => item.sales2),
                                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(tooltipItem) {
                                            return 'Doanh Thu: ' + tooltipItem.raw.toLocaleString() + ' VNĐ';
                                        }
                                    }
                                }
                            }
                        }
                    });
                });

    // Fetch and render yearly data
    fetch('modules/statistical_tables/thongkenam.php')
        .then(response => response.json())
        .then(data => {
            const ctx1 = document.getElementById('chart1').getContext('2d');
            new Chart(ctx1, {
                type: 'bar',
                data: {
                    labels: data.map(item => item.date1),
                    datasets: [{
                        label: 'Doanh Thu 365 Ngày Qua',
                        data: data.map(item => item.sales1),
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(tooltipItem) {
                                    return 'Doanh Thu: ' + tooltipItem.raw.toLocaleString() + ' VNĐ';
                                }
                            }
                        }
                    }
                }
            });
        });
          
  

        });
    </script>
    <script>
    document.getElementById('filter-form').addEventListener('submit', function(event) {
        const selectedOption = document.querySelector('input[name="stat-type"]:checked');
        if (!selectedOption) {
            event.preventDefault();
            alert('Vui lòng chọn thời gian thống kê.');
        }
    });

    document.getElementById('export-report').addEventListener('click', function(event) {
        const selectedOption = document.querySelector('input[name="stat-type"]:checked');
        if (!selectedOption) {
            event.preventDefault();
            alert('Vui lòng chọn thời gian thống kê.');
        }
    });
</script>
</body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
        crossorigin="anonymous"
    ></script>
    <script
        src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
        crossorigin="anonymous"
    ></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
    <script src="js/app.js"></script>

</html>
