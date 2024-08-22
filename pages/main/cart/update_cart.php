<?php
include("../../../admin/config/connection.php");
session_start();

$id_cus = $_SESSION['ID_ThanhVien'];
$id_cart = $_SESSION['ID_GioHang'];

if (isset($_POST['update_cart'])) {
    $errors = [];
    $update_quantity = $_POST['soluong'];

    foreach ($update_quantity as $id_product => $quantity) {
        // Kiểm tra và xử lý đầu vào để đảm bảo chỉ số lượng hợp lệ
        if (!is_numeric($quantity) || $quantity <= 0) {
            $errors[$id_product] = "Số lượng không hợp lệ. Vui lòng nhập số dương.";
            continue;
        }

        $quantity = (int)$quantity;

        // Kiểm tra số lượng sản phẩm hiện có trong kho
        $sql_check_stock = "SELECT SoLuong FROM sanpham WHERE ID_SanPham = $id_product";
        $result_stock = mysqli_query($mysqli, $sql_check_stock);

        if ($result_stock) {
            $row_stock = mysqli_fetch_array($result_stock);
            $stock_available = (int)$row_stock['SoLuong'];

            if ($quantity > $stock_available) {
                $errors[$id_product] = "Số lượng yêu cầu vượt quá số lượng hiện có trong kho.";
                continue;
            }

            // Cập nhật số lượng sản phẩm trong giỏ hàng
            $sql_update_quantity = "UPDATE chitietgiohang SET SoLuong = $quantity WHERE ID_GioHang = $id_cart AND ID_SanPham = $id_product";
            if (!mysqli_query($mysqli, $sql_update_quantity)) {
                echo "Error updating record: " . mysqli_error($mysqli);
            }
        } else {
            echo "Error checking stock: " . mysqli_error($mysqli);
        }
    }

    // Lưu lỗi vào session và chuyển hướng lại nếu có lỗi
    if (!empty($errors)) {
        $_SESSION['update_cart_errors'] = $errors;
        header('Location: ../../../index.php?navigate=cart');
        exit();
    } else {
        // Xóa lỗi nếu không có lỗi
        unset($_SESSION['update_cart_errors']);
        header('Location: ../../../index.php?navigate=cart');
        exit();
    }
}

if (isset($_POST['place_order'])) {
    // Xử lý đặt hàng
    // Ví dụ: kiểm tra số lượng, tạo đơn hàng, v.v.
    header('Location: ../../../index.php?navigate=customer_info');
    exit();
}
?>
