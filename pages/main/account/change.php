<?php
session_start();
include("../../../admin/config/connection.php");

if (isset($_POST['sua']) && isset($_GET['id'])) {
    $ID_ThanhVien = $_GET['id'];
    $HoVaTen = mysqli_real_escape_string($mysqli, trim($_POST['HoVaTen']));
    $Email = mysqli_real_escape_string($mysqli, trim($_POST['Email']));
    $SoDienThoai = mysqli_real_escape_string($mysqli, trim($_POST['SoDienThoai']));
    $DiaChi = mysqli_real_escape_string($mysqli, trim($_POST['DiaChi']));
    
    // Khởi tạo mảng chứa lỗi
    $errors = array();
    
    // Kiểm tra lỗi Tên
    if (empty($HoVaTen)) {
        $errors['HoVaTen'] = "Tên không được bỏ trống!";
    } elseif (strlen($HoVaTen) < 2 || strlen($HoVaTen) > 50) {
        $errors['HoVaTen'] = "Tên phải từ 2 đến 50 ký tự!";
    }
      // Kiểm tra lỗi Tên
      if (empty($DiaChi)) {
        $errors['HoVaTen'] = "Địa chỉ không được bỏ trống!";
    } elseif (strlen($DiaChi) < 10 || strlen($DiaChi) > 100) {
        $errors['DiaChi'] = "Địa chỉ phải từ 10 đến 100 ký tự!";
    }
    
    // Kiểm tra lỗi Email
    $email_pattern = "/^[A-Za-z0-9_.]{4,}@([a-zA-Z0-9]{2,12})(\.[a-zA-Z]{2,12})+$/";
    if (empty($Email)) {
        $errors['Email'] = "Email không được để trống!";
    } elseif (!preg_match($email_pattern, $Email)) {
        $errors['Email'] = "Email không hợp lệ!";
    }
    
    // Kiểm tra lỗi Số điện thoại
    if (empty($SoDienThoai)) {
        $errors['SoDienThoai'] = "Số điện thoại không được để trống!";
    } elseif (!preg_match("/^0[0-9]{9,11}$/", $SoDienThoai)) {
        $errors['SoDienThoai'] = "Số điện thoại phải bắt đầu bằng số 0 và có từ 10 đến 12 số!";
    }
    
    // Nếu có lỗi, lưu lỗi vào session và chuyển hướng về trang chỉnh sửa
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header("Location: ../../../index.php?navigate=changeProfile");
        exit();
    } else {
        // Nếu không có lỗi, tiến hành cập nhật cơ sở dữ liệu
        $sql_update = "UPDATE thanhvien SET HoVaTen='$HoVaTen', Email='$Email', DiaChi='$DiaChi', SoDienThoai='$SoDienThoai' WHERE ID_ThanhVien='$ID_ThanhVien'";
        if (mysqli_query($mysqli, $sql_update)) {
            $_SESSION['message'] = "Cập nhật thông tin cá nhân thành công!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Cập nhật thông tin cá nhân thất bại!";
            $_SESSION['message_type'] = "danger";
        }

        // Xóa session lỗi sau khi xử lý
        unset($_SESSION['errors']);
        header("Location: ../../../index.php?navigate=profile");
        exit();
    }
}
?>
