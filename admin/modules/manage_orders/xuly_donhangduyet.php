<?php 
include("../../config/connection.php"); 

// Kiểm tra xem có ID được gửi qua không
if (isset($_GET['id'])) {
    $ID_DonHang = $_GET['id'];
    $XuLy = 3;

    // Sử dụng prepared statement để cập nhật trạng thái đơn hàng
    $stmt = $mysqli->prepare("UPDATE donhang SET XuLy = ? WHERE ID_DonHang = ?");
    $stmt->bind_param("is", $XuLy, $ID_DonHang);

    if ($stmt->execute()) {
        // Chuyển hướng đến trang danh sách đơn hàng với thông báo thành công và thêm thông báo xác nhận
        header('Location: ../../index.php?order=success-order-list&status=success&message=Đơn hàng đang được lấy');
    } else {
        // Chuyển hướng đến trang danh sách đơn hàng với thông báo lỗi
        header('Location: ../../index.php?order=success-order-list&status=error&message=Lỗi khi cập nhật đơn hàng');
    }
    $stmt->close();
    exit(); // Đảm bảo rằng không có thêm mã PHP được thực thi sau khi chuyển hướng
} else {
    // Nếu không có ID, chuyển hướng đến trang danh sách đơn hàng với thông báo lỗi
    header('Location: ../../index.php?order=success-order-list&status=error&message=ID đơn hàng không hợp lệ');
    exit();
}
?>