<?php
include('../../config/connection.php');

// Lấy các tham số start_date, end_date và order_date từ URL nếu có
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : null;
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : null;
$orderDate = isset($_GET['order_date']) ? $_GET['order_date'] : null;

// Tạo câu truy vấn SQL cơ bản
$query = "SELECT SUM(GiaTien) as totalRevenue FROM donhang WHERE XuLy = 5"; // XuLy = 5 có nghĩa là đơn hàng đã hoàn tất

// Kiểm tra xem người dùng muốn thống kê theo ngày cụ thể hay theo khoảng thời gian
if ($orderDate) {
    // Chuyển đổi định dạng ngày tháng để tránh lỗi SQL injection và định dạng không hợp lệ
    $orderDate = $mysqli->real_escape_string($orderDate);
    // Thống kê theo ngày cụ thể
    $query .= " AND DATE(ThoiGianLap) = '$orderDate'";
} elseif ($startDate && $endDate) {
    // Chuyển đổi định dạng ngày tháng để tránh lỗi SQL injection và định dạng không hợp lệ
    $startDate = $mysqli->real_escape_string($startDate);
    $endDate = $mysqli->real_escape_string($endDate);
    // Thống kê theo khoảng thời gian
    $query .= " AND DATE(ThoiGianLap) BETWEEN '$startDate' AND '$endDate'";
}

// Thực hiện câu truy vấn
$result = $mysqli->query($query);

// Kiểm tra lỗi truy vấn
if (!$result) {
    echo json_encode(['error' => 'Lỗi truy vấn: ' . $mysqli->error]);
    exit;
}

// Lấy kết quả từ câu truy vấn
$row = $result->fetch_assoc();

// Kiểm tra nếu tổng doanh thu là null, đặt thành 0
$totalRevenue = $row['totalRevenue'] ? $row['totalRevenue'] : 0;

// Định dạng số liệu thành chuỗi có phân cách hàng ngàn và thêm đơn vị VNĐ
$formattedTotal = number_format($totalRevenue, 0, ',', '.') . ' VNĐ';

// Trả về kết quả dưới dạng JSON
echo json_encode(['totalRevenue' => $formattedTotal]);
?>
