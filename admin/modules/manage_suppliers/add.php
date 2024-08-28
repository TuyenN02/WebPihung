<?php
session_start();
include("../../config/connection.php");

// Khởi tạo biến để lưu thông báo lỗi và dữ liệu
$errors = [];
$data = [];

// Xử lý dữ liệu từ biểu mẫu
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Nhận dữ liệu từ biểu mẫu
    $tenNCC = isset($_POST['TenNCC']) ? trim($_POST['TenNCC']) : '';
    $moTa = isset($_POST['MoTa']) ? trim($_POST['MoTa']) : '';
    $email = isset($_POST['Email']) ? trim($_POST['Email']) : '';
    $diaChi = isset($_POST['DiaChi']) ? trim($_POST['DiaChi']) : '';
    $soDienThoai = isset($_POST['SoDienThoai']) ? trim($_POST['SoDienThoai']) : '';
    $img = isset($_FILES['Img']) ? $_FILES['Img'] : null;

    // Lưu dữ liệu vào biến $data để giữ lại thông tin
    $data['TenNCC'] = $tenNCC;
    $data['MoTa'] = $moTa;
    $data['Email'] = $email;
    $data['DiaChi'] = $diaChi;
    $data['SoDienThoai'] = $soDienThoai;

    // Nếu không có lỗi, thực hiện thêm dữ liệu vào cơ sở dữ liệu
    if (empty($errors)) {
        // Tạo đường dẫn lưu hình ảnh
        $uploadDir = '../../../assets/image/supplier/';
        $imgPath = $uploadDir . basename($img['name']);

        // Di chuyển tệp hình ảnh từ thư mục tạm thời đến thư mục đích
        if (move_uploaded_file($img['tmp_name'], $imgPath)) {
            // Lấy tên tệp hình ảnh để lưu vào cơ sở dữ liệu
            $imgRelativePath = basename($img['name']);

            // Thực hiện thêm nhà cung cấp vào cơ sở dữ liệu
            $stmt = $mysqli->prepare("INSERT INTO nhacungcap (TenNCC, MoTa, Img, SoDienThoai, Email, DiaChi) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param('ssssss', $tenNCC, $moTa, $imgRelativePath, $soDienThoai, $email, $diaChi);

            if ($stmt->execute()) {
                // Xóa dữ liệu và thông báo lỗi trong session
                unset($_SESSION['data']);
                unset($_SESSION['errors']);

                // Đặt thông báo thành công vào session và chuyển hướng về trang danh sách nhà cung cấp
                $_SESSION['success_message'] = 'Thêm nhà cung cấp thành công!';
               
            } else {
                $errors['general'] = 'Có lỗi xảy ra khi thêm nhà cung cấp vào cơ sở dữ liệu: ' . $stmt->error;
            }
        } else {
            $errors['Img'] = 'Có lỗi xảy ra khi tải lên hình ảnh.';
        }
    }

    // Lưu dữ liệu và lỗi vào session để hiển thị lại trên biểu mẫu
    $_SESSION['errors'] = $errors;
    $_SESSION['data'] = $data;

    // Chuyển hướng lại trang thêm nhà cung cấp với thông báo lỗi
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}
?>
