<?php
session_start();
include("../../config/connection.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $TenDanhMuc = trim(mysqli_real_escape_string($mysqli, $_POST['TenDanhMuc']));
    $MoTa = trim(mysqli_real_escape_string($mysqli, $_POST['MoTa']));

    // Lưu dữ liệu vào session
    $_SESSION['data'] = [
        'TenDanhMuc' => $TenDanhMuc,
        'MoTa' => $MoTa
    ];

    // Kiểm tra tên danh mục có trùng không
    $check_category_query = "SELECT ID_DanhMuc FROM danhmuc WHERE TenDanhMuc='$TenDanhMuc'";
    $check_category_result = mysqli_query($mysqli, $check_category_query);

    if (mysqli_num_rows($check_category_result) > 0) {
        echo json_encode(['status' => 'error', 'message' => "Tên danh mục đã tồn tại!"]);
        exit();
    }

    // Thêm danh mục vào cơ sở dữ liệu
    $sql_insert = "INSERT INTO danhmuc (TenDanhMuc, Mota) 
                   VALUES ('$TenDanhMuc', '$MoTa')";

    if (mysqli_query($mysqli, $sql_insert)) {
        unset($_SESSION['data']); // Xóa dữ liệu lưu trữ sau khi thêm thành công
        $_SESSION['success'] = "Thêm danh mục thành công!"; // Lưu thông báo thành công vào session
        echo json_encode(['status' => 'success', 'redirect' => 'index.php?cat=list-cat']);
    } else {
        echo json_encode(['status' => 'error', 'message' => "Thêm thất bại. Vui lòng thử lại."]);
    }
}
?>
