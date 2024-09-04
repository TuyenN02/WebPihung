<?php
session_start();
include("../../config/connection.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id'])) {
        $ID_DanhMuc = intval($_POST['id']); // Bảo mật: ép kiểu ID_DanhMuc thành số nguyên
        $TenDanhMuc = trim(mysqli_real_escape_string($mysqli, $_POST['TenDanhMuc']));
        $MoTa = trim(mysqli_real_escape_string($mysqli, $_POST['MoTa']));

        // Lưu dữ liệu vào session
        $_SESSION['data'] = [
            'TenDanhMuc' => $TenDanhMuc,
            'MoTa' => $MoTa,
        ];

        // Kiểm tra tên danh mục có trùng không
        $check_category_query = "SELECT ID_DanhMuc FROM danhmuc WHERE TenDanhMuc='$TenDanhMuc' AND ID_DanhMuc != $ID_DanhMuc";
        $check_category_result = mysqli_query($mysqli, $check_category_query);

        if (mysqli_num_rows($check_category_result) > 0) {
            echo json_encode(['status' => 'error', 'message' => "Tên danh mục đã tồn tại!"]);
            exit();
        }

        // Cập nhật thông tin danh mục trong cơ sở dữ liệu
        $sql_update = "UPDATE danhmuc SET 
            TenDanhMuc='$TenDanhMuc', 
            MoTa='$MoTa'
            WHERE ID_DanhMuc=$ID_DanhMuc";

        if (mysqli_query($mysqli, $sql_update)) {
            unset($_SESSION['data']); // Xóa dữ liệu lưu trữ sau khi lưu thành công
            $_SESSION['success'] = "Cập nhật danh mục thành công!"; // Lưu thông báo thành công vào session
            echo json_encode(['status' => 'success', 'redirect' => 'index.php?cat=list-cat']);
        } else {
            echo json_encode(['status' => 'error', 'message' => "Cập nhật thất bại. Vui lòng thử lại."]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => "ID danh mục không hợp lệ."]);
    }
}
?>
