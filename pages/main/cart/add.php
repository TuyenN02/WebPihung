<?php
include("../../../admin/config/connection.php");
session_start();

// Lấy ID giỏ hàng từ session
$id_cart = $_SESSION['ID_GioHang'] ?? null;

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['TenDangNhap'])) {
    echo "<script>
             alert('Bạn cần đăng nhập để thêm sản phẩm vào giỏ hàng.');
             window.location.href = 'http://localhost/WebPihung/index.php?navigate=login';
          </script>";
    exit();
}

// Kiểm tra xem có thông tin sản phẩm và số lượng được gửi từ form hay không
if (isset($_GET['id']) && isset($_POST['soluong'])) {
    $id_product = (int)$_GET['id']; // Chuyển đổi id_product thành kiểu integer
    $soluong = (float)$_POST['soluong']; // Chuyển đổi soluong thành kiểu float

    // Kiểm tra và xử lý đầu vào để đảm bảo số lượng hợp lệ (số dương và không có ký tự đặc biệt)
    if (!is_numeric($soluong) || $soluong <= 0) {
        $_SESSION['error'] = "Số lượng không hợp lệ. Vui lòng nhập số dương.";
        header('Location: ../../../index.php?navigate=productInfo&id_product=' . $id_product);
        exit();
    }

    // Xử lý hành động "Thêm vào giỏ hàng"
    if ($_POST['action_type'] == 'add_to_cart') {
        // Kiểm tra số lượng sản phẩm hiện có trong kho
        $sql_check_stock = "SELECT SoLuong FROM sanpham WHERE ID_SanPham = ?";
        $stmt_stock = mysqli_prepare($mysqli, $sql_check_stock);
        mysqli_stmt_bind_param($stmt_stock, "i", $id_product);
        mysqli_stmt_execute($stmt_stock);
        mysqli_stmt_bind_result($stmt_stock, $stock_available);
        mysqli_stmt_fetch($stmt_stock);
        mysqli_stmt_close($stmt_stock);

        // Nếu số lượng yêu cầu vượt quá số lượng hiện có trong kho
        if ($soluong > $stock_available) {
            $_SESSION['error'] = "Số lượng yêu cầu vượt quá số lượng hiện có trong kho.";
            header('Location: ../../../index.php?navigate=productInfo&id_product=' . $id_product);
            exit();
        }

        // Kiểm tra xem sản phẩm đã có trong giỏ hàng chưa
        $sql_check_product = "SELECT SoLuong FROM chitietgiohang WHERE ID_GioHang = ? AND ID_SanPham = ?";
        $stmt_product = mysqli_prepare($mysqli, $sql_check_product);
        mysqli_stmt_bind_param($stmt_product, "ii", $id_cart, $id_product);
        mysqli_stmt_execute($stmt_product);
        mysqli_stmt_bind_result($stmt_product, $current_quantity);
        $product_exists = mysqli_stmt_fetch($stmt_product);
        mysqli_stmt_close($stmt_product);

        // Nếu sản phẩm đã có trong giỏ hàng, cập nhật số lượng
        if ($product_exists) {
            $new_quantity = $current_quantity + $soluong;
            $sql_update_quantity = "UPDATE chitietgiohang SET SoLuong = ? WHERE ID_GioHang = ? AND ID_SanPham = ?";
            $stmt_update = mysqli_prepare($mysqli, $sql_update_quantity);
            mysqli_stmt_bind_param($stmt_update, "dii", $new_quantity, $id_cart, $id_product);
            if (!mysqli_stmt_execute($stmt_update)) {
                echo "Error updating record: " . mysqli_stmt_error($stmt_update);
            }
            mysqli_stmt_close($stmt_update);
        } else {
            // Nếu sản phẩm chưa có trong giỏ hàng, thêm mới
            $sql_addtocart = "INSERT INTO chitietgiohang (ID_GioHang, ID_SanPham, SoLuong) VALUES (?, ?, ?)";
            $stmt_add = mysqli_prepare($mysqli, $sql_addtocart);
            mysqli_stmt_bind_param($stmt_add, "iid", $id_cart, $id_product, $soluong);
            if (!mysqli_stmt_execute($stmt_add)) {
                echo "Error inserting record: " . mysqli_stmt_error($stmt_add);
            }
            mysqli_stmt_close($stmt_add);
        }

        // Chuyển hướng về trang giỏ hàng sau khi cập nhật
        header('Location: ../../../index.php?navigate=cart');
        exit();
    } elseif ($_POST['action_type'] == 'buy_now') {
        // Kiểm tra xem thông tin đơn hàng từ session có đầy đủ và hợp lệ không
        if (!isset($_SESSION['ID_ThanhVien']) || !isset($_SESSION['allMoney'])) {
            $_SESSION['order_error'] = "Thông tin không hợp lệ.";
            header('Location: ../../../index.php?navigate=customer_info');
            exit();
        }

        $id_cus = $_SESSION['ID_ThanhVien'];
        $GiaTien = $_SESSION['allMoney'];
        $NguoiNhan = $_POST['NguoiNhan'] ?? '';
        $DiaChi = $_POST['DiaChi'] ?? '';
        $SoDienThoai = $_POST['SoDienThoai'] ?? '';
        $GhiChu = $_POST['GhiChu'] ?? '';

        // Kiểm tra thông tin người nhận
        if (empty($NguoiNhan) || empty($DiaChi) || empty($SoDienThoai) || !is_numeric($GiaTien)) {
            $_SESSION['order_error'] = "Vui lòng nhập đầy đủ thông tin.";
            header('Location: ../../../index.php?navigate=customer_info');
            exit();
        }

        // Thêm đơn hàng vào cơ sở dữ liệu
        $sql_insert_order = "INSERT INTO donhang (ID_ThanhVien, ThoiGianLap, DiaChi, GhiChu, GiaTien, SoDienThoai, XuLy) 
                             VALUES (?, NOW(), ?, ?, ?, ?, 1)";
        $stmt_order = mysqli_prepare($mysqli, $sql_insert_order);
        mysqli_stmt_bind_param($stmt_order, 'isssss', $id_cus, $DiaChi, $GhiChu, $GiaTien, $SoDienThoai);
        if (mysqli_stmt_execute($stmt_order)) {
            $order_id = mysqli_insert_id($mysqli);

            // Thêm chi tiết đơn hàng vào cơ sở dữ liệu
            $sql_cart_details = "SELECT chitietgiohang.ID_SanPham, chitietgiohang.SoLuong, sanpham.GiaBan 
                                 FROM chitietgiohang 
                                 INNER JOIN sanpham ON chitietgiohang.ID_SanPham = sanpham.ID_SanPham
                                 WHERE chitietgiohang.ID_GioHang = ?";
            $stmt_cart_details = mysqli_prepare($mysqli, $sql_cart_details);
            mysqli_stmt_bind_param($stmt_cart_details, 'i', $id_cart);
            mysqli_stmt_execute($stmt_cart_details);
            $result_cart_details = mysqli_stmt_get_result($stmt_cart_details);

            // Duyệt qua các chi tiết giỏ hàng và thêm vào đơn hàng
            while ($row = mysqli_fetch_assoc($result_cart_details)) {
                $product_id = $row['ID_SanPham'];
                $quantity = $row['SoLuong'];
                $price = $row['GiaBan'];

                $sql_insert_order_detail = "INSERT INTO chitietdonhang (ID_DonHang, ID_SanPham, SoLuong, GiaBan)
                                            VALUES (?, ?, ?, ?)";
                $stmt_order_detail = mysqli_prepare($mysqli, $sql_insert_order_detail);
                mysqli_stmt_bind_param($stmt_order_detail, 'iiid', $order_id, $product_id, $quantity, $price);
                mysqli_stmt_execute($stmt_order_detail);
            }

            mysqli_stmt_close($stmt_cart_details);

            // Xóa giỏ hàng sau khi đặt hàng thành công
            $sql_delete_cart = "DELETE FROM chitietgiohang WHERE ID_GioHang = ?";
            $stmt_delete_cart = mysqli_prepare($mysqli, $sql_delete_cart);
            mysqli_stmt_bind_param($stmt_delete_cart, 'i', $id_cart);
            mysqli_stmt_execute($stmt_delete_cart);
            mysqli_stmt_close($stmt_delete_cart);

            $_SESSION['order_success'] = "Đơn hàng của bạn đã được đặt thành công!";
            header('Location: ../../../index.php?navigate=order_success');
            exit();
        } else {
            $_SESSION['order_error'] = "Có lỗi xảy ra trong quá trình đặt hàng.";
            header('Location: ../../../index.php?navigate=customer_info');
            exit();
        }
    } 
} else {
    // Xử lý nếu thiếu thông tin cần thiết
    $_SESSION['error'] = "Thiếu thông tin cần thiết.";
    header('Location: ../../../index.php');
    exit();
}
?>
