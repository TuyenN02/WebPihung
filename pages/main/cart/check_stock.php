<?php
session_start();
include("../../../admin/config/connection.php");

// Kiểm tra người dùng đã đăng nhập
if (!isset($_SESSION['ID_ThanhVien'])) {
    header('Location: ../../index.php'); // Quay lại trang chính nếu chưa đăng nhập
    exit();
}

// Lấy ID người dùng từ session
$id_cus = $_SESSION['ID_ThanhVien'];
$errors = [];

// Lấy dữ liệu từ form giỏ hàng
foreach ($_POST['soluong'] as $id_sanpham => $soluong) {
    $soluong = intval($soluong);

    // Kiểm tra số lượng sản phẩm
    $sql = "SELECT SoLuong FROM sanpham WHERE ID_SanPham = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $id_sanpham);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if ($product) {
        if ($soluong > $product['SoLuong']) {
            $errors[] = "Số lượng sản phẩm (ID: $id_sanpham) không hợp lệ. Còn lại: " . $product['SoLuong'];
        }
    } else {
        $errors[] = "Sản phẩm (ID: $id_sanpham) không tồn tại.";
    }
}

// Kiểm tra và xử lý thông báo lỗi
if (!empty($errors)) {
    $_SESSION['update_cart_errors'] = $errors;
    header('Location: ../../index.php?navigate=cart'); // Quay lại trang giỏ hàng với lỗi
    exit();
}

// Nếu không có lỗi, chuyển hướng đến trang đặt hàng
header('Location: ../../index.php?navigate=customer_info');
exit();
?>
