<?php
// Kết nối đến cơ sở dữ liệu
include("../../config/connection.php");
session_start();

// Kiểm tra xem form đã được gửi chưa
if (isset($_POST['add'])) {
    $name = trim($_POST['name']); // Xóa dấu cách đầu và cuối
    $description = trim($_POST['Mota']);
    
    // Xóa dữ liệu lỗi trước khi kiểm tra mới
    unset($_SESSION['errors']);
    
    // Kiểm tra dữ liệu nhập vào
    if (empty($name)) {
        $_SESSION['errors']['name'] = "Tên danh mục không được để trống.";
    } elseif (strlen($name) < 3) {
        $_SESSION['errors']['name'] = "Tên danh mục phải có ít nhất 3 ký tự.";
    } elseif (strlen($name) > 25) {
        $_SESSION['errors']['name'] = "Tên danh mục không được quá 25 ký tự.";
    } else {
        // Bảo vệ chống SQL Injection
        $name = mysqli_real_escape_string($mysqli, $name);
        $description = mysqli_real_escape_string($mysqli, $description);
        
        // Kiểm tra trùng tên danh mục
        $checkSql = "SELECT COUNT(*) AS count FROM danhmuc WHERE TenDanhMuc = '$name'";
        $result = mysqli_query($mysqli, $checkSql);
        $row = mysqli_fetch_assoc($result);

        if ($row['count'] > 0) {
            $_SESSION['errors']['name'] = "Danh mục này đã tồn tại.";
        } else {
            // Thực hiện truy vấn thêm danh mục vào cơ sở dữ liệu
            $sql = "INSERT INTO danhmuc (TenDanhMuc, Mota) VALUES ('$name', '$description')";
            if (mysqli_query($mysqli, $sql)) {
                // Lưu thông báo thành công vào session và chuyển hướng
                $_SESSION['success'] = "Thêm danh mục thành công!";
                header("Location: ../../index.php?cat=list-cat");
                exit();
            } else {
                // Lưu lỗi vào session và chuyển hướng trở lại trang thêm danh mục
                $_SESSION['errors']['database'] = "Lỗi cơ sở dữ liệu: " . mysqli_error($mysqli);
            }
        }
    }
    
    // Chuyển hướng trở lại trang thêm danh mục nếu có lỗi
    header("Location: ../../index.php?cat=add-cat");
    exit();
}
?>
