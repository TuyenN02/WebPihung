<?php
include("../../config/connection.php");
session_start();

if (isset($_GET['id'])) {
    $id_thanhvien = $_GET['id'];
    
    // Xóa chi tiết đơn hàng
    $query_delete_chitietdonhang = "DELETE FROM chitietdonhang WHERE ID_DonHang IN
    (SELECT ID_DonHang FROM donhang WHERE ID_DonHang IN
    (SELECT ID_DonHang FROM donhang WHERE ID_ThanhVien = '$id_thanhvien'))";
    $result_chitietdonhang = mysqli_query($mysqli, $query_delete_chitietdonhang);
    
    // Xóa chi tiết giỏ hàng
    $query_delete_chitietgiohang = "DELETE FROM chitietgiohang WHERE ID_GioHang IN
    (SELECT ID_GioHang FROM giohang WHERE ID_ThanhVien = '$id_thanhvien')";
    $result_chitietgiohang = mysqli_query($mysqli, $query_delete_chitietgiohang);
    
    // Xóa đơn hàng
    $query_delete_donhang = "DELETE FROM donhang WHERE ID_DonHang IN
    (SELECT ID_DonHang FROM donhang WHERE ID_ThanhVien = '$id_thanhvien')";
    $result_donhang = mysqli_query($mysqli, $query_delete_donhang);
    
    // Xóa bình luận
    $query_delete_binhluan = "DELETE FROM binhluan WHERE ID_ThanhVien = '$id_thanhvien'";
    $result_binhluan = mysqli_query($mysqli, $query_delete_binhluan);
    
    // Xóa giỏ hàng
    $query_delete_giohang = "DELETE FROM giohang WHERE ID_ThanhVien = '$id_thanhvien'";
    $result_giohang = mysqli_query($mysqli, $query_delete_giohang);
    
    // Xóa thành viên
    $query_delete_thanhvien = "DELETE FROM thanhvien WHERE ID_ThanhVien = '$id_thanhvien'";
    $result_thanhvien = mysqli_query($mysqli, $query_delete_thanhvien);
    
    // Kiểm tra kết quả và thiết lập thông báo
    if ($result_chitietdonhang && $result_chitietgiohang && $result_donhang && $result_binhluan && $result_giohang && $result_thanhvien) {
        $_SESSION['success_message'] = "Xóa tài khoản thành công!";
    } else {
        $_SESSION['error_message'] = "Xóa tài khoản thất bại!";
    }
    
    // Chuyển hướng về trang danh sách tài khoản
    header('Location: ../../index.php?user=list-user');
    exit();
}
