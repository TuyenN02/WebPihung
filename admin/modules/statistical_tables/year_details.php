<?php
include('../../config/connection.php');

// Truy vấn dữ liệu cho năm hiện tại
$query = "
    SELECT DATE(ThoiGianLap) AS dayOfYear, SUM(GiaTien) AS dailySales
    FROM donhang
    WHERE ThoiGianLap >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)
    GROUP BY DATE(ThoiGianLap)
    ORDER BY DATE(ThoiGianLap)
";

$result = $mysqli->query($query);

$daysWithOrders = [];
$totalSales = 0; // Biến để lưu tổng doanh thu năm
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $daysWithOrders[] = $row;
        $totalSales += $row['dailySales']; // Cộng doanh thu hàng ngày vào tổng doanh thu
    }
}

// Lấy chi tiết đơn hàng cho mỗi ngày
$orderDetails = [];
foreach ($daysWithOrders as $day) {
    $dayQuery = "
        SELECT * 
        FROM donhang 
        WHERE DATE(ThoiGianLap) = '{$day['dayOfYear']}'
    ";
    $dayResult = $mysqli->query($dayQuery);
    if ($dayResult->num_rows > 0) {
        $orderDetails[$day['dayOfYear']] = $dayResult->fetch_all(MYSQLI_ASSOC);
    }
}

$mysqli->close();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Chi Tiết Doanh Thu Năm</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        .container {
            width: 80%;
            margin: auto;
        }
        .card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        canvas {
            width: 100% !important;
        }
        .order-details {
            margin-top: 20px;
        }
        .order-details table {
            width: 100%;
            border-collapse: collapse;
        }
        .order-details th, .order-details td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .order-details th {
            background-color: #f2f2f2;
        }
        .total-sales {
            font-size: 20px;
            font-weight: bold;
            color: #28a745;
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h2 class="text-center">Chi Tiết Doanh Thu Năm</h2>
            
            <!-- Hiển thị tổng doanh thu năm -->
            <div class="total-sales">
                Tổng Doanh Thu Năm: <?php echo number_format($totalSales); ?> VNĐ
            </div>
            
            <canvas id="year-chart" style="height: 400px;"></canvas>
            <div class="order-details">
                <?php foreach ($daysWithOrders as $day): ?>
                    <h3>Ngày: <?php echo $day['dayOfYear']; ?></h3>
                    <table>
                        <thead>
                            <tr>
                                <th>ID Đơn Hàng</th>
                                <th>Thời Gian</th>
                                <th>Địa Chỉ</th>
                                <th>Giá Tiền</th>
                                <th>Ghi Chú</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orderDetails[$day['dayOfYear']] as $order): ?>
                                <tr>
                                    <td><?php echo $order['ID_DonHang']; ?></td>
                                    <td><?php echo $order['ThoiGianLap']; ?></td>
                                    <td><?php echo $order['DiaChi']; ?></td>
                                    <td><?php echo number_format($order['GiaTien']); ?> VNĐ</td>
                                    <td><?php echo $order['GhiChu']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('year-chart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode(array_column($daysWithOrders, 'dayOfYear')); ?>,
                    datasets: [{
                        label: 'Doanh Thu Hàng Ngày',
                        data: <?php echo json_encode(array_column($daysWithOrders, 'dailySales')); ?>,
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
    </script>
</body>
</html>
