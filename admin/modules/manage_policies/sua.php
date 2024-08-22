<?php
session_start();
include("../../config/connection.php");

if (isset($_POST['updatePolicy'])) {
    $id = $_POST['ID_ChinhSach'];
    $tieude = mysqli_real_escape_string($mysqli, trim($_POST['TieuDe']));
    $noidung = mysqli_real_escape_string($mysqli, trim($_POST['NoiDung']));
    
    // Khởi tạo mảng chứa lỗi
    $errors = array();

    // Kiểm tra lỗi Tiêu đề
    if (empty($tieude)) {
        $errors['TieuDe'] = "Tiêu đề không được để trống.";
    } elseif (strlen($tieude) < 3) {
        $errors['TieuDe'] = "Tiêu đề phải có ít nhất 3 ký tự.";
    }

    // Kiểm tra lỗi Nội dung
    if (empty($noidung)) {
        $errors['NoiDung'] = "Nội dung không được để trống.";
    } elseif (strlen($noidung) < 10) {
        $errors['NoiDung'] = "Nội dung phải có ít nhất 10 ký tự.";
    }

    // Nếu có lỗi, lưu lỗi vào session và chuyển hướng về trang chỉnh sửa
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header("Location: ../../index.php?policy=edit-policy&id=$id");
        exit();
    } else {
        // Nếu không có lỗi, tiến hành cập nhật cơ sở dữ liệu
        $sql_update = "UPDATE chinhsach SET TieuDe='$tieude', NoiDung='$noidung' WHERE ID_ChinhSach='$id'";
        if (mysqli_query($mysqli, $sql_update)) {
            $_SESSION['message'] = "Cập nhật chính sách thành công!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Cập nhật chính sách thất bại!";
            $_SESSION['message_type'] = "danger";
        }

        // Xóa session lỗi sau khi xử lý
        unset($_SESSION['errors']);
        header("Location: ../../index.php?policy=list-policy");
        exit();
    }
}
