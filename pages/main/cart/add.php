<?php
include("../../../admin/config/connection.php");
session_start();

$id_cart = $_SESSION['ID_GioHang'];

if (isset($_GET['id']) && isset($_POST['soluong'])) {
    $id_product = (int)$_GET['id']; // Chuyển đổi id_product thành kiểu integer
    $soluong = $_POST['soluong'];

    // Kiểm tra và xử lý đầu vào để đảm bảo chỉ số lượng hợp lệ (số dương và không có ký tự đặc biệt)
    if (!is_numeric($soluong) || $soluong <= 0) {
        $_SESSION['error'] = "Số lượng không hợp lệ. Vui lòng nhập số dương.";
        header('Location: ../../../index.php?navigate=productInfo&id_product=' . $id_product);
        exit();
    }

    $soluong = (float)$soluong; // Chuyển đổi soluong thành kiểu float

    // Kiểm tra số lượng sản phẩm hiện có trong kho
    $sql_check_stock = "SELECT SoLuong FROM sanpham WHERE ID_SanPham = $id_product";
    $result_stock = mysqli_query($mysqli, $sql_check_stock);

    if ($result_stock) {
        $row_stock = mysqli_fetch_array($result_stock);
        $stock_available = $row_stock['SoLuong'];

        if ($soluong > $stock_available) {
            // Lưu thông báo lỗi vào session và chuyển hướng về trang chi tiết sản phẩm
            $_SESSION['error'] = "Số lượng yêu cầu vượt quá số lượng hiện có trong kho.";
            header('Location: ../../../index.php?navigate=productInfo&id_product=' . $id_product);
            exit();
        }

        // Kiểm tra xem sản phẩm đã có trong giỏ hàng chưa
        $sql_check_product = "SELECT SoLuong FROM chitietgiohang WHERE ID_GioHang = $id_cart AND ID_SanPham = $id_product";
        $result = mysqli_query($mysqli, $sql_check_product);

        if ($result) {
            if (mysqli_num_rows($result) > 0) {
                // Sản phẩm đã có trong giỏ hàng, lấy số lượng hiện tại và cộng số lượng mới
                $row = mysqli_fetch_array($result);
                $current_quantity = (float)$row['SoLuong'];
                $new_quantity = $current_quantity + $soluong;

                // Cập nhật số lượng sản phẩm trong giỏ hàng
                $sql_update_quantity = "UPDATE chitietgiohang SET SoLuong = $new_quantity WHERE ID_GioHang = $id_cart AND ID_SanPham = $id_product";
                if (!mysqli_query($mysqli, $sql_update_quantity)) {
                    echo "Error updating record: " . mysqli_error($mysqli);
                }
            } else {
                // Sản phẩm chưa có trong giỏ hàng, thêm mới
                $sql_addtocart = "INSERT INTO chitietgiohang (ID_GioHang, ID_SanPham, SoLuong) VALUES ($id_cart, $id_product, $soluong)";
                if (!mysqli_query($mysqli, $sql_addtocart)) {
                    echo "Error inserting record: " . mysqli_error($mysqli);
                }
            }
        } else {
            echo "Error checking product: " . mysqli_error($mysqli);
        }
    } else {
        echo "Error checking stock: " . mysqli_error($mysqli);
    }
} else {
    echo "Invalid input.";
}

// Chuyển hướng về trang giỏ hàng sau khi cập nhật
header('Location: ../../../index.php?navigate=cart');
exit();
?>
