<?php
include('../../config/connection.php');

// Lấy các tham số start_date và end_date từ URL nếu có
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : null;
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : null;

// Tạo câu truy vấn SQL cơ bản
$query = "SELECT COUNT(*) as processingOrders FROM donhang WHERE XuLy IN (1, 3, 4, 6)"; // XuLy = 1, 3, 4, 6 là đơn hàng đang xử lý

// Nếu có cả start_date và end_date, thêm điều kiện vào câu truy vấn
if ($startDate && $endDate) {
    $query .= " AND ThoiGianLap BETWEEN ? AND ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('ss', $startDate, $endDate);
} else {
    $stmt = $mysqli->prepare($query);
}

// Thực thi câu truy vấn
$stmt->execute();
$result = $stmt->get_result();

// Lấy số lượng đơn hàng đang xử lý
$data = $result->fetch_assoc();

// Đóng kết nối
$stmt->close();
$mysqli->close();

// Trả dữ liệu dưới dạng JSON
header('Content-Type: application/json');
echo json_encode($data);
?>
