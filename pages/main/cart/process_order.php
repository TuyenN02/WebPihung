<?php
session_start();
include("../../../admin/config/connection.php");

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['ID_ThanhVien'])) {
    header('Location: index.php?navigate=login');
    exit();
}

$id_cus = $_SESSION['ID_ThanhVien'];

// Lấy thông tin từ form
$NguoiNhan = $_POST['NguoiNhan'];
$DiaChi = $_POST['DiaChi'];
$SoDienThoai = $_POST['SoDienThoai'];
$GhiChu = $_POST['GhiChu'];
$GiaTien = $_SESSION['allMoney'];

// Kiểm tra thông tin người dùng
if (empty($NguoiNhan) || empty($DiaChi) || empty($SoDienThoai) || !is_numeric($GiaTien)) {
    $_SESSION['order_error'] = "Vui lòng nhập đầy đủ thông tin.";
    header('Location: index.php?navigate=customer_info');
    exit();
}

// Thêm đơn hàng vào cơ sở dữ liệu
$sql_insert_order = "INSERT INTO donhang (ID_ThanhVien, ThoiGianLap, DiaChi, GhiChu, GiaTien, SoDienThoai, XuLy) 
                     VALUES ($id_cus, NOW(), '$DiaChi', '$GhiChu', $GiaTien, '$SoDienThoai', 0)";
if (mysqli_query($mysqli, $sql_insert_order)) {
    $order_id = mysqli_insert_id($mysqli);

    // Thêm chi tiết đơn hàng vào cơ sở dữ liệu
    $sql_cart_details = "SELECT chitietgiohang.ID_SanPham, chitietgiohang.SoLuong, sanpham.GiaBan 
                         FROM chitietgiohang 
                         INNER JOIN sanpham ON chitietgiohang.ID_SanPham = sanpham.ID_SanPham
                         WHERE chitietgiohang.id_GioHang = (SELECT id_GioHang FROM giohang WHERE ID_ThanhVien = $id_cus)";
    $query_cart_details = mysqli_query($mysqli, $sql_cart_details);

    while ($row = mysqli_fetch_assoc($query_cart_details)) {
        $product_id = $row['ID_SanPham'];
        $quantity = $row['SoLuong'];
        $price = $row['GiaBan'];

        $sql_insert_order_detail = "INSERT INTO chitietdonhang (ID_DonHang, ID_SanPham, SoLuong, GiaBan)
                                    VALUES ($order_id, $product_id, $quantity, $price)";
        mysqli_query($mysqli, $sql_insert_order_detail);
    }

    // Xóa giỏ hàng sau khi đặt hàng thành công
    $sql_delete_cart = "DELETE FROM chitietgiohang WHERE id_GioHang = (SELECT id_GioHang FROM giohang WHERE ID_ThanhVien = $id_cus)";
    mysqli_query($mysqli, $sql_delete_cart);

    $_SESSION['order_success'] = "Đơn hàng của bạn đã được đặt thành công!";
    header('Location: index.php?navigate=order_success');
    exit();
} else {
    $_SESSION['order_error'] = "Có lỗi xảy ra trong quá trình đặt hàng.";
    header('Location: index.php?navigate=customer_info');
    exit();
}
?>
