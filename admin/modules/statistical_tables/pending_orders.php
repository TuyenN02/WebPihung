<?php
include('../../config/connection.php');

// Lấy các tham số start_date và end_date từ URL nếu có
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : null;
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : null;
$singleDay = isset($_GET['date']) ? $_GET['date'] : null;

// Khởi tạo câu truy vấn SQL cơ bản
$query = "SELECT COUNT(*) as pendingOrders FROM donhang WHERE XuLy = 0";

// Nếu có cả start_date và end_date, thêm điều kiện vào câu truy vấn
if ($startDate && $endDate) {
    $query .= " AND ThoiGianLap BETWEEN ? AND ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('ss', $startDate, $endDate);
} elseif ($singleDay) {
    $query .= " AND DATE(ThoiGianLap) = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('s', $singleDay);
} else {
    $stmt = $mysqli->prepare($query);
}

// Thực thi câu truy vấn
$stmt->execute();
$result = $stmt->get_result();

// Lấy kết quả từ câu truy vấn
$row = $result->fetch_assoc();

// Trả về kết quả dưới dạng JSON
echo json_encode(['pendingOrders' => $row['pendingOrders']]);

// Đóng kết nối
$stmt->close();
$mysqli->close();
?>
