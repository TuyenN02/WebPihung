<?php
// Kết nối cơ sở dữ liệu
include('../../config/connection.php');

// Khởi tạo mảng dữ liệu để trả về
$statistics = array();

// Lấy tham số ngày bắt đầu và ngày kết thúc từ yêu cầu POST
$startDate = isset($_POST['start_date']) ? $_POST['start_date'] : null;
$endDate = isset($_POST['end_date']) ? $_POST['end_date'] : null;

// Chuyển đổi định dạng ngày nếu cần (tùy thuộc vào cơ sở dữ liệu và định dạng bạn sử dụng)
if ($startDate && $endDate) {
    // Truy vấn số đơn hàng đang chờ xác nhận (XuLy = 1) trong khoảng thời gian
    $query_pending_orders = "SELECT COUNT(*) AS count FROM donhang WHERE XuLy = 1 AND DATE(ThoiGianLap) BETWEEN ? AND ?";
    $stmt = $mysqli->prepare($query_pending_orders);
    $stmt->bind_param("ss", $startDate, $endDate);
    $stmt->execute();
    $result_pending_orders = $stmt->get_result();
    if ($result_pending_orders) {
        $row = $result_pending_orders->fetch_assoc();
        $statistics['donhang_Duyet'] = $row['count'];
    }

    // Truy vấn số đơn hàng đang giao (XuLy = 4) trong khoảng thời gian
    $query_in_progress_orders = "SELECT COUNT(*) AS count FROM donhang WHERE XuLy = 4 AND DATE(ThoiGianLap) BETWEEN ? AND ?";
    $stmt = $mysqli->prepare($query_in_progress_orders);
    $stmt->bind_param("ss", $startDate, $endDate);
    $stmt->execute();
    $result_in_progress_orders = $stmt->get_result();
    if ($result_in_progress_orders) {
        $row = $result_in_progress_orders->fetch_assoc();
        $statistics['inProgressOrders'] = $row['count'];
    }

    // Truy vấn số đơn hàng đã bán (XuLy = 5) trong khoảng thời gian
    $query_completed_orders = "SELECT COUNT(*) AS count FROM donhang WHERE XuLy = 5 AND DATE(ThoiGianLap) BETWEEN ? AND ?";
    $stmt = $mysqli->prepare($query_completed_orders);
    $stmt->bind_param("ss", $startDate, $endDate);
    $stmt->execute();
    $result_completed_orders = $stmt->get_result();
    if ($result_completed_orders) {
        $row = $result_completed_orders->fetch_assoc();
        $statistics['completedOrders'] = $row['count'];
    }

    // Truy vấn số sản phẩm hiện có
    $query_total_products = "SELECT SUM(SoLuong) AS total FROM sanpham";
    $result_total_products = $mysqli->query($query_total_products);
    if ($result_total_products) {
        $row = $result_total_products->fetch_assoc();
        $statistics['totalProducts'] = $row['total'];
    }

    // Truy vấn doanh thu từ các đơn hàng đã bán trong khoảng thời gian
    $query_total_revenue = "SELECT SUM(GiaTien) AS revenue FROM donhang WHERE XuLy = 5 AND DATE(ThoiGianLap) BETWEEN ? AND ?";
    $stmt = $mysqli->prepare($query_total_revenue);
    $stmt->bind_param("ss", $startDate, $endDate);
    $stmt->execute();
    $result_total_revenue = $stmt->get_result();
    if ($result_total_revenue) {
        $row = $result_total_revenue->fetch_assoc();
        $statistics['totalRevenue'] = $row['revenue'];
    }
} else {
    // Nếu không có ngày, thống kê cho toàn bộ dữ liệu
    // Truy vấn số đơn hàng đang chờ xác nhận
    $query_pending_orders = "SELECT COUNT(*) AS count FROM donhang WHERE XuLy = 9";
    $result_pending_orders = $mysqli->query($query_pending_orders);
    if ($result_pending_orders) {
        $row = $result_pending_orders->fetch_assoc();
        $statistics['pendingOrders'] = $row['count'];
    }

    // Truy vấn số đơn hàng đang giao
    $query_in_progress_orders = "SELECT COUNT(*) AS count FROM donhang WHERE XuLy = 4";
    $result_in_progress_orders = $mysqli->query($query_in_progress_orders);
    if ($result_in_progress_orders) {
        $row = $result_in_progress_orders->fetch_assoc();
        $statistics['inProgressOrders'] = $row['count'];
    }

    // Truy vấn số đơn hàng đã bán
    $query_completed_orders = "SELECT COUNT(*) AS count FROM donhang WHERE XuLy = 5";
    $result_completed_orders = $mysqli->query($query_completed_orders);
    if ($result_completed_orders) {
        $row = $result_completed_orders->fetch_assoc();
        $statistics['completedOrders'] = $row['count'];
    }

    // Truy vấn số sản phẩm hiện có
    $query_total_products = "SELECT SUM(SoLuong) AS total FROM sanpham";
    $result_total_products = $mysqli->query($query_total_products);
    if ($result_total_products) {
        $row = $result_total_products->fetch_assoc();
        $statistics['totalProducts'] = $row['total'];
    }

    // Truy vấn doanh thu từ các đơn hàng đã bán
    $query_total_revenue = "SELECT SUM(GiaTien) AS revenue FROM donhang WHERE XuLy = 5";
    $result_total_revenue = $mysqli->query($query_total_revenue);
    if ($result_total_revenue) {
        $row = $result_total_revenue->fetch_assoc();
        $statistics['totalRevenue'] = $row['revenue'];
    }
}

// Đóng kết nối
$mysqli->close();

// Trả về dữ liệu dưới dạng JSON
header('Content-Type: application/json');
echo json_encode($statistics);
?>
