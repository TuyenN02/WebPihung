<?php 
include("../../config/connection.php");
session_start();

if (isset($_GET['id'])) {
    $ID_DanhMuc = $_GET['id'];

    // Bảo vệ chống SQL Injection
    $ID_DanhMuc = mysqli_real_escape_string($mysqli, $ID_DanhMuc);

    // Thực hiện xóa danh mục
    $sql_xoa = "DELETE FROM danhmuc WHERE ID_DanhMuc = '$ID_DanhMuc'";
    if (mysqli_query($mysqli, $sql_xoa)) {
        // Lưu thông báo thành công vào session
        $_SESSION['success'] = "Xóa danh mục thành công!";
    } else {
        // Lưu thông báo lỗi vào session nếu có lỗi
        $_SESSION['errors']['database'] = "Lỗi cơ sở dữ liệu: " . mysqli_error($mysqli);
    }

    // Chuyển hướng trở lại trang danh sách danh mục
    header('Location: ../../index.php?cat=list-cat');
    exit();
}
?>
