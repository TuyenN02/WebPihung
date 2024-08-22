<?php
session_start();
include("../../config/connection.php");

if (isset($_POST['submit'])) {
    $ID_baiviet = intval($_GET['id_baiviet']); // Bảo mật: ép kiểu ID_NCC thành số nguyên

    // Lấy dữ liệu từ form và loại bỏ dấu cách
    $Tenbaiviet = trim(mysqli_real_escape_string($mysqli, $_POST['name']));
    $Noidung = trim(mysqli_real_escape_string($mysqli, $_POST['Noidung']));
    $Img = $_FILES['image']['name'];
    $Img_tmp = $_FILES['image']['tmp_name'];

    // Lưu dữ liệu vào session
    $_SESSION['data'] = [
        'Tenbaiviet' => $Tenbaiviet,
        'Noidung' => $Noidung,
    ];

    // Kiểm tra lỗi bỏ trống
    if (empty($Tenbaiviet)) {
        $_SESSION['errors']['Tenbaiviet'] = "Tên bài viết không được để trống.";
    }elseif (strlen($Tenbaiviet) < 3) {
        $_SESSION['errors']['Tenbaiviet'] = "Tên bài viết phải có ít nhất 3 ký tự.";
    }

   
    
    if (empty($Noidung)) {
        $_SESSION['errors']['Noidung'] = "Địa chỉ không được để trống.";
    }

    // Kiểm tra độ dài tên nhà cung cấp
    if (strlen($Tenbaiviet) > 49) {
        $_SESSION['errors']['Tenbaiviet'] = "Tên bài viết không được vượt quá 50 ký tự.";
    }
    

   

   
    // Kiểm tra nếu có lỗi
    if (!empty($_SESSION['errors'])) {
        header("Location: ../../index.php?posts=edit-posts&id_baiviet=$ID_baiviet");
        exit();
    }
    
    // Cập nhật thông tin nhà cung cấp trong cơ sở dữ liệu
    $sql_update = "UPDATE posts SET 
        Tenbaiviet='$Tenbaiviet', 
      
        Noidung='$Noidung'";
    
    // Thêm phần cập nhật hình ảnh nếu có
    if (!empty($Img)) {
        // Kiểm tra định dạng hình ảnh
        $allowedExtensions = ['jpg', 'jpeg', 'png'];
        $imgExtension = strtolower(pathinfo($Img, PATHINFO_EXTENSION));
        if (in_array($imgExtension, $allowedExtensions)) {
            // Xóa hình ảnh cũ nếu cần
            $result = mysqli_query($mysqli, "SELECT Img FROM posts WHERE ID_baiviet=$ID_baiviet");
            $oldImg = mysqli_fetch_assoc($result)['Img'];
            if ($oldImg && file_exists("../../../assets/image/supplier/$oldImg")) {
                unlink("../../../assets/image/supplier/$oldImg");
            }

            // Di chuyển hình ảnh mới
            move_uploaded_file($Img_tmp, "../../../assets/image/supplier/".$Img);
            $sql_update .= ", Img='$Img'";
        } else {
            $_SESSION['errors']['image'] = "Định dạng hình ảnh không hợp lệ! Vui lòng tải lên tệp .jpg, .jpeg hoặc .png.";
            header("Location: ../../index.php?posts=edit-posts&id_baiviet=$ID_baiviet");
            exit();
        }
    }

    $sql_update .= " WHERE ID_baiviet=$ID_baiviet";

    if (mysqli_query($mysqli, $sql_update)) {
        unset($_SESSION['data']); // Xóa dữ liệu lưu trữ sau khi lưu thành công
        
        // Thêm thông báo thành công
        $_SESSION['success'] = "Cập nhật bài viết thành công!";
        header("Location: ../../index.php?posts=list-posts");
    } else {
        $_SESSION['errors']['update'] = "Cập nhật thất bại. Vui lòng thử lại.";
        header("Location: ../../index.php?posts=edit-posts&id_baiviet=$ID_baiviet");
    }
}
?>
