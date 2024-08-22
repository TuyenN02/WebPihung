<?php
include('../../config/connection.php');

// Lấy các tham số start_date và end_date từ URL nếu có
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : null;
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : null;

// Tạo câu truy vấn SQL cơ bản
$query = "SELECT SUM(SoLuong) as currentProducts FROM sanpham";

// Nếu có cả start_date và end_date, cập nhật câu truy vấn để lấy số lượng sản phẩm trong khoảng thời gian cụ thể
if ($startDate && $endDate) {
    $query = "SELECT SUM(c.soluong) as currentProducts 
              FROM chitietdonhang c
              JOIN donhang d ON c.ID_DonHang = d.ID_DonHang
              WHERE d.ThoiGianLap BETWEEN ? AND ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('ss', $startDate, $endDate);
} else {
    $stmt = $mysqli->prepare($query);
}

// Thực thi câu truy vấn
$stmt->execute();
$result = $stmt->get_result();

// Lấy kết quả từ câu truy vấn
$row = $result->fetch_assoc();

// Đảm bảo số lượng sản phẩm không bị null
$currentProducts = $row['currentProducts'] ? $row['currentProducts'] : 0;

// Trả về kết quả dưới dạng JSON
header('Content-Type: application/json');
echo json_encode(['currentProducts' => $currentProducts]);

// Đóng kết nối
$stmt->close();
$mysqli->close();
?>
