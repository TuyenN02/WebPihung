<?php
include("../../../admin/config/connection.php");
session_start();

$id_cus = $_SESSION['ID_ThanhVien'];
$id_cart = $_SESSION['ID_GioHang'];

if (isset($_POST['update_cart'])) {
    $errors = [];

    
        $update_quantity = $_POST['soluong'];

        foreach ($update_quantity as $id_product => $quantity) {
            // Validate quantity
            if (!is_numeric($quantity) || $quantity <= 0) {
                $errors[$id_product] = "Số lượng không hợp lệ. Vui lòng nhập số dương.";
                continue;
            }

            $quantity = (int)$quantity;

            // Check stock availability
            $sql_check_stock = "SELECT SoLuong FROM sanpham WHERE ID_SanPham = $id_product";
            $result_stock = mysqli_query($mysqli, $sql_check_stock);

            if ($result_stock) {
                $row_stock = mysqli_fetch_array($result_stock);
                $stock_available = (int)$row_stock['SoLuong'];

                if ($quantity > $stock_available) {
                    $errors[$id_product] = "Số lượng yêu cầu vượt quá số lượng hiện có trong kho.";
                    continue;
                }

                // Update cart
                $sql_update_quantity = "UPDATE chitietgiohang SET SoLuong = $quantity WHERE ID_GioHang = $id_cart AND ID_SanPham = $id_product";
                if (!mysqli_query($mysqli, $sql_update_quantity)) {
                    $errors['update'] = "Lỗi khi cập nhật giỏ hàng: " . mysqli_error($mysqli);
                }
            } else {
                $errors['stock'] = "Lỗi khi kiểm tra kho: " . mysqli_error($mysqli);
            }
        }

        // Handle errors or success for updating cart
        if (!empty($errors)) {
            $_SESSION['update_cart_errors'] = $errors;
            header('Location: ../../../index.php?navigate=cart');
            exit();
        } else {
            unset($_SESSION['update_cart_errors']);
        }
    

        // Place order logic here
        // For example: validate order details, create order, etc.
        header('Location: ../../../index.php?navigate=customer_info');
        exit();
    

    // Redirect to the cart page if no specific action was taken
    header('Location: ../../../index.php?navigate=cart');
    exit();
}
?>
