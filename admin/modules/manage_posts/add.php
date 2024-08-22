<?php
session_start();
include("../../config/connection.php");

if (isset($_POST['submit'])) {
    // Lấy dữ liệu từ form và xử lý trim()
    $Tenbaiviet = trim(mysqli_real_escape_string($mysqli, $_POST['Tenbaiviet']));
    
    $Noidung = trim(mysqli_real_escape_string($mysqli, $_POST['Noidung']));
    $Img = $_FILES['Img']['name']; // Xóa dấu cách thừa trong tên hình ảnh
    $Img_tmp = $_FILES['Img']['tmp_name'];

    // Lưu dữ liệu vào session
    $_SESSION['data'] = [
        'Tenbaiviet' => $Tenbaiviet,
        'Noidung' => $Noidung,
        'Img' => $Img // Lưu tên ảnh vào session
    ];

    // Kiểm tra lỗi
    $errors = [];

    // Kiểm tra các trường thông tin trống
    if (empty($Tenbaiviet)) {
        $errors['Tenbaiviet'] = "Tên bài viết không được để trống.";
    }elseif (strlen($Tenbaiviet) < 3) {
        $errors['Tenbaiviet'] = "Tên bài viết phải có ít nhất 3 ký tự.";
    }

    
    if (empty($Noidung)) {
        $errors['Noidung'] = "Nội dung không được để trống.";
    }
    if (empty($Img)) {
        $errors['Img'] = "Hình ảnh không được để trống.";
    }

    // Kiểm tra độ dài tên bài viết
    if (strlen($Tenbaiviet) > 100) {
        $errors['Tenbaiviet'] = "Tên bài viết không được vượt quá 100 ký tự.";
    }

    // Kiểm tra trùng tên bài viết trong cơ sở dữ liệu
    $check_name_query = "SELECT * FROM posts WHERE Tenbaiviet = '$Tenbaiviet'";
    $check_name_result = mysqli_query($mysqli, $check_name_query);
    if (mysqli_num_rows($check_name_result) > 0) {
        $errors['Tenbaiviet'] = "Tên bài viết đã tồn tại.";
    }

    // Kiểm tra nếu có lỗi
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header("Location: ../../index.php?posts=add-post");
        exit();
    }

    // Nếu không có lỗi, tiếp tục lưu vào cơ sở dữ liệu
    if (move_uploaded_file($Img_tmp, "../../../assets/image/supplier/".$Img)) {
        $sql = "INSERT INTO posts(Tenbaiviet, Noidung, Img) 
                VALUES('$Tenbaiviet','$Noidung', '$Img')";
        if (mysqli_query($mysqli, $sql)) {
            unset($_SESSION['data']); // Xóa dữ liệu lưu trữ sau khi lưu thành công
            $_SESSION['success'] = "Thêm bài viết thành công!";
            header("Location: ../../index.php?posts=list-posts");
            exit();
        } else {
            $_SESSION['errors']['database'] = "Lỗi cơ sở dữ liệu: " . mysqli_error($mysqli);
            header("Location: ../../index.php?posts=add-post");
            exit();
        }
    } else {
        $_SESSION['errors']['upload'] = "Không thể tải lên hình ảnh. Vui lòng thử lại.";
        header("Location: ../../index.php?posts=add-post");
        exit();
    }
}
?>
