<?php
include('../../config/connection.php');

// Lấy các tham số start_date, end_date và order_date từ URL nếu có
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : null;
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : null;
$orderDate = isset($_GET['order_date']) ? $_GET['order_date'] : null;

// Tạo câu truy vấn SQL cơ bản
$query = "SELECT COUNT(*) as completedOrders FROM donhang WHERE XuLy = 5";

// Kiểm tra xem người dùng muốn thống kê theo ngày cụ thể hay theo khoảng thời gian
if ($orderDate) {
    // Thống kê theo ngày cụ thể
    $query .= " AND DATE(ThoiGianLap) = '$orderDate'";
} elseif ($startDate && $endDate) {
    // Thống kê theo khoảng thời gian
    $query .= " AND ThoiGianLap BETWEEN '$startDate' AND '$endDate'";
}

$result = $mysqli->query($query);

// Lấy kết quả từ câu truy vấn
$row = $result->fetch_assoc();

// Trả về kết quả dưới dạng JSON
echo json_encode(['completedOrders' => $row['completedOrders']]);
?>
