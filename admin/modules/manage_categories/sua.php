<?php
include("../../config/connection.php");
session_start();

if (isset($_POST['submit']) && isset($_GET['id'])) {
    $ID_DanhMuc = intval($_GET['id']); // Bảo mật: ép kiểu ID_DanhMuc thành số nguyên
    $TenDanhMuc = trim($_POST['TenDanhMuc']);
    $Mota = trim($_POST['Mota']);
    
    // Xóa lỗi trước khi kiểm tra mới
    unset($_SESSION['errors']);
    
    // Kiểm tra dữ liệu nhập vào
    if (empty($TenDanhMuc)) {
        $_SESSION['errors']['TenDanhMuc'] = "Tên danh mục không được để trống.";
    } elseif (strlen($TenDanhMuc) < 3) {
        $_SESSION['errors']['TenDanhMuc'] = "Tên danh mục phải có ít nhất 3 ký tự.";
    } elseif (strlen($TenDanhMuc) > 25) {
        $_SESSION['errors']['TenDanhMuc'] = "Tên danh mục không được quá 25 ký tự.";
    }
    
    // Nếu không có lỗi, thực hiện cập nhật dữ liệu
    if (!isset($_SESSION['errors'])) {
        $TenDanhMuc = mysqli_real_escape_string($mysqli, $TenDanhMuc);
        $Mota = mysqli_real_escape_string($mysqli, $Mota);

        $sql_fix = "UPDATE danhmuc SET TenDanhMuc = '$TenDanhMuc', Mota = '$Mota' WHERE ID_DanhMuc = $ID_DanhMuc";
        if (mysqli_query($mysqli, $sql_fix)) {
            $_SESSION['success'] = "Cập nhật danh mục thành công!";
            header('Location: ../../index.php?cat=list-cat');
            exit();
        } else {
            $_SESSION['errors']['database'] = "Lỗi cơ sở dữ liệu: " . mysqli_error($mysqli);
        }
    }

    // Chuyển hướng trở lại trang chỉnh sửa danh mục nếu có lỗi
    header('Location: ../../index.php?cat=edit-cat&id=' . $ID_DanhMuc);
    exit();
}
?>
