<?php 
    include("../../config/connection.php"); 

    if (isset($_GET['id'])) {
        $ID_DonHang = intval($_GET['id']); // Bảo vệ chống SQL Injection
        $sql_Order = "UPDATE donhang SET XuLy='2' WHERE ID_DonHang=$ID_DonHang";
        
        if (mysqli_query($mysqli, $sql_Order)) {
            // Cập nhật số lượng sản phẩm
            $sql_order = "SELECT * FROM chitietdonhang WHERE ID_DonHang = $ID_DonHang";
            $query_order = mysqli_query($mysqli, $sql_order);
            
            while ($row = mysqli_fetch_assoc($query_order)) {
                $id_sanpham = $row['ID_SanPham'];
                $soluong = $row['SoLuong'];
                $sql_update = "UPDATE sanpham SET SoLuong = SoLuong + $soluong WHERE ID_SanPham = $id_sanpham";
                mysqli_query($mysqli, $sql_update);
            }
            // Thành công
            header('Location: ../../index.php?order=success-order-list&status=cancel_success');
        } else {
            // Lỗi
            header('Location: ../../index.php?success-order-list&status=error');
        }
    } else {
        // Không có ID
        header('Location: ../../index.php?order=success-order-list&status=error');
    }
    exit();
?>
