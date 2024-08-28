<?php
session_start();
include("../../config/connection.php");

// Khởi tạo biến để lưu thông báo lỗi và dữ liệu
$errors = [];
$data = [];

// Xử lý dữ liệu từ biểu mẫu
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tenbaiviet = trim($_POST['Tenbaiviet']);
    $noidung = trim($_POST['Noidung']);
    $img = $_FILES['Img'];

    // Lưu dữ liệu vào biến $data để giữ lại thông tin
    $data['Tenbaiviet'] = $tenbaiviet;
    $data['Noidung'] = $noidung;

    // Kiểm tra tên bài viết
    if (empty($tenbaiviet)) {
        $errors['Tenbaiviet'] = 'Tên bài viết không được để trống.';
    } elseif (strlen($tenbaiviet) < 3) {
        $errors['Tenbaiviet'] = 'Tên bài viết phải có ít nhất 3 ký tự.';
    } elseif (strlen($tenbaiviet) > 255) {
        $errors['Tenbaiviet'] = 'Tên bài viết không được vượt quá 255 ký tự.';
    }

    // Kiểm tra nội dung
    if (empty($noidung)) {
        $errors['Noidung'] = 'Nội dung không được để trống.';
    }

    // Kiểm tra hình ảnh
    if ($img['error'] === UPLOAD_ERR_NO_FILE) {
        $errors['Img'] = 'Hãy chọn hình ảnh để tải lên.';
    } else {
        $allowedExtensions = ['jpg', 'jpeg', 'png'];
        $fileExtension = strtolower(pathinfo($img['name'], PATHINFO_EXTENSION));

        if (!in_array($fileExtension, $allowedExtensions)) {
            $errors['Img'] = 'Định dạng tệp không hợp lệ! Vui lòng chỉ tải lên tệp có đuôi .jpg hoặc .png.';
        }
    }

    // Nếu không có lỗi, thực hiện thêm dữ liệu vào cơ sở dữ liệu
    if (empty($errors)) {
        // Tạo đường dẫn lưu hình ảnh
        $uploadDir = '../../../assets/image/supplier/';
        $imgPath = $uploadDir . basename($img['name']);

        // Di chuyển tệp hình ảnh từ thư mục tạm thời đến thư mục đích
        if (move_uploaded_file($img['tmp_name'], $imgPath)) {
            // Lấy tên tệp hình ảnh để lưu vào cơ sở dữ liệu
            $imgRelativePath = basename($img['name']);

            // Thực hiện thêm bài viết vào cơ sở dữ liệu
            $stmt = $mysqli->prepare("INSERT INTO posts (Tenbaiviet, Noidung, Img) VALUES (?, ?, ?)");
            $stmt->bind_param('sss', $tenbaiviet, $noidung, $imgRelativePath);

            if ($stmt->execute()) {
                // Xóa dữ liệu và thông báo lỗi trong session
                unset($_SESSION['data']);
                unset($_SESSION['errors']);

                // Đặt thông báo thành công vào session và chuyển hướng về trang danh sách bài viết
                $_SESSION['success'] = 'Thêm bài viết thành công!';
            
            
            } else {
                $errors['general'] = 'Có lỗi xảy ra khi thêm bài viết vào cơ sở dữ liệu.';
            }
        } else {
            $errors['Img'] = 'Có lỗi xảy ra khi tải lên hình ảnh.';
        }
    }

    // Lưu dữ liệu và lỗi vào session để hiển thị lại trên biểu mẫu
    $_SESSION['errors'] = $errors;
    $_SESSION['data'] = $data;
    
    // Chuyển hướng lại trang thêm bài viết với thông báo lỗi
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}
?>
<style>
#wp-content {
    margin-left: 250px;
    flex: 1;
    padding: 10px;
    margin-top: 100px;
}
</style>