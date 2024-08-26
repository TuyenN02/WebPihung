<?php
// Kết nối cơ sở dữ liệu
include 'C:/xampp/htdocs/pihung/vendor/autoload.php'; 
$mysqli = new mysqli("localhost", "root", "", "pihung");

// Kiểm tra kết nối
if ($mysqli->connect_errno) {
    echo "Kết nối thất bại: " . $mysqli->connect_error;
    exit();
}

// Lấy dữ liệu từ form
$report_type = $_POST['report_type'];
$start_date = isset($_POST['start_date']) ? $_POST['start_date'] : null;
$end_date = isset($_POST['end_date']) ? $_POST['end_date'] : null;

// Khởi tạo các biến thống kê
$pendingOrders = 0;
$inProgressOrders = 0;
$completedOrders = 0;
$totalProducts = 0;
$totalRevenue = 0;

// Thiết lập truy vấn SQL dựa trên loại báo cáo
if ($report_type == 'date-range' && $start_date && $end_date) {
    $dateCondition = "WHERE ThoiGianLap BETWEEN '$start_date' AND '$end_date'";
} else {
    $dateCondition = ""; // Toàn bộ thời gian
}

// Thống kê đơn hàng đang chờ xác nhận
$sql = "SELECT COUNT(*) AS pendingOrders FROM donhang $dateCondition AND XuLy = 9";
$result = $mysqli->query($sql);
if ($result) {
    $row = $result->fetch_assoc();
    $pendingOrders = $row['pendingOrders'];
}

// Thống kê đơn hàng đang giao
$sql = "SELECT COUNT(*) AS inProgressOrders FROM donhang $dateCondition AND XuLy = 4";
$result = $mysqli->query($sql);
if ($result) {
    $row = $result->fetch_assoc();
    $inProgressOrders = $row['inProgressOrders'];
}

// Thống kê đơn hàng đã bán
$sql = "SELECT COUNT(*) AS completedOrders FROM donhang $dateCondition AND XuLy = 5";
$result = $mysqli->query($sql);
if ($result) {
    $row = $result->fetch_assoc();
    $completedOrders = $row['completedOrders'];
}

// Thống kê số sản phẩm hiện có
$sql = "SELECT SUM(SoLuong) AS totalProducts FROM sanpham";
$result = $mysqli->query($sql);
if ($result) {
    $row = $result->fetch_assoc();
    $totalProducts = $row['totalProducts'];
}

// Thống kê doanh thu
$sql = "SELECT SUM(GiaTien) AS totalRevenue FROM donhang $dateCondition AND XuLy = 5";
$result = $mysqli->query($sql);
if ($result) {
    $row = $result->fetch_assoc();
    $totalRevenue = $row['totalRevenue'];
}

// Hiển thị kết quả thống kê
echo "<h3>Thông tin thống kê</h3>";
echo "<p><strong>Đơn hàng đang chờ xác nhận:</strong> $pendingOrders</p>";
echo "<p><strong>Đơn hàng đang giao:</strong> $inProgressOrders</p>";
echo "<p><strong>Đơn hàng đã bán:</strong> $completedOrders</p>";
echo "<p><strong>Số sản phẩm hiện có:</strong> $totalProducts</p>";
echo "<p><strong>Doanh thu:</strong> $totalRevenue VND</p>";

// Đóng kết nối cơ sở dữ liệu
$mysqli->close();
?>
