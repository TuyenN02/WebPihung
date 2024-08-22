<?php
header('Content-Type: application/json');
include('../../config/connection.php');

// Lấy giá trị khoảng thời gian từ URL
$period = isset($_GET['period']) ? $_GET['period'] : 'weekly';

switch ($period) {
    case 'weekly':
        // Lấy doanh thu hàng tuần trong năm hiện tại
        $query = "
            SELECT 
                WEEK(date) AS period, 
                SUM(revenue) AS total_revenue 
            FROM orders 
            WHERE YEAR(date) = YEAR(CURDATE()) 
            GROUP BY period
            ORDER BY period;
        ";
        break;
    case 'monthly':
        // Lấy doanh thu hàng tháng trong năm hiện tại
        $query = "
            SELECT 
                MONTH(date) AS period, 
                SUM(revenue) AS total_revenue 
            FROM orders 
            WHERE YEAR(date) = YEAR(CURDATE()) 
            GROUP BY period
            ORDER BY period;
        ";
        break;
    case 'yearly':
        // Lấy doanh thu hàng năm
        $query = "
            SELECT 
                YEAR(date) AS period, 
                SUM(revenue) AS total_revenue 
            FROM orders 
            GROUP BY period
            ORDER BY period;
        ";
        break;
    default:
        // Nếu không xác định được khoảng thời gian, trả về lỗi
        echo json_encode(['error' => 'Invalid period']);
        exit();
}

$result = $mysqli->query($query);

$data = [];
$labels = [];
$values = [];

while ($row = $result->fetch_assoc()) {
    $labels[] = $period === 'weekly' ? "Tuần {$row['period']}" :
                ($period === 'monthly' ? "Tháng {$row['period']}" :
                ($period === 'yearly' ? "Năm {$row['period']}" : ''));
    $values[] = (float) $row['total_revenue'];
}

$data['labels'] = $labels;
$data['values'] = $values;

echo json_encode($data);
?>
