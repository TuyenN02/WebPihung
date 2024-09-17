<?php
$id_order = isset($_GET['id']) ? $_GET['id'] : '';
$sql_order = "SELECT NguoiNhan, SoDienThoai, DiaChi, ThoiGianLap,
    GhiChu, CodeOrder, HinhThucThanhToan, GiaTien, XuLy 
    FROM donhang WHERE ID_DonHang = $id_order";
$query_order = mysqli_query($mysqli, $sql_order);
$row = mysqli_fetch_assoc($query_order);
$sql_order_detail = "SELECT TenSanPham, chitietdonhang.SoLuong,
    chitietdonhang.GiaMua FROM sanpham 
    INNER JOIN chitietdonhang ON chitietdonhang.ID_SanPham = sanpham.ID_SanPham
    WHERE chitietdonhang.ID_DonHang = $id_order";
$query_order_detail = mysqli_query($mysqli, $sql_order_detail);
?>

<div id="content" class="container-fluid">
    <div class="card">
        <!-- Button to go back -->
        <div class="card-header">
            <a href="index.php?order=success-order-list" class="btn btn-secondary">Quay lại</a>
        </div>

        <table class="table table-bordered table-checkall">
            <tr>
                <th colspan="4"><h1 class="text-center">Chi tiết đơn hàng</h1></th>
            </tr>
            <tr>
                <td colspan="2">Người nhận: <?= htmlspecialchars($row['NguoiNhan']) ?></td>
                <td colspan="2">Số điện thoại: <?= htmlspecialchars($row['SoDienThoai']) ?></td>
            </tr>
            <tr>
                <td colspan="2">Địa chỉ: <?= htmlspecialchars($row['DiaChi']) ?></td>
                <td colspan="2">Thời gian: <?= htmlspecialchars($row['ThoiGianLap']) ?></td>
            </tr>
            <tr>
                <td colspan="4">Ghi chú: <?= htmlspecialchars($row['GhiChu']) ?></td>
            </tr>
            <tr>
                <td colspan="2">Mã đơn hàng: <?= htmlspecialchars($row['CodeOrder']) ?></td>
                <td colspan="2">Hình thức thanh toán: <?= htmlspecialchars($row['HinhThucThanhToan']) ?></td>
            </tr>
            <tr>
                <th>STT</th>
                <th>Tên sản phẩm</th>
                <th>Số lượng</th>
                <th>Giá mua</th>
            </tr>
            <?php 
            $i = 0;
            while ($row_detail = mysqli_fetch_assoc($query_order_detail)) {
                $i++;    
            ?>
            <tr>
                <td><?= $i ?></td>
                <td><?= htmlspecialchars($row_detail['TenSanPham']) ?></td>
                <td><?= $row_detail['SoLuong'] ?> Cây</td>
                <td><?= number_format($row_detail['GiaMua'], 0, ',', '.') ?> VND/Cây</td>
            </tr>
            <?php } ?>
            <tr>
                <th colspan="4">Tổng tiền: <?= number_format($row['GiaTien'], 0, ',', '.') ?> VND</th>
            </tr>
        </table>

        <!-- Print Order Button at the Bottom-Right -->
<div class="d-flex justify-content-end">
    <a href="modules/manage_orders/indonhang.php?id=<?= $id_order ?>" class="btn btn-primary" target="_blank">
        <i class="fas fa-print"></i> In đơn hàng
    </a>
</div>

    </div>
</div>

</div><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
   
    .btn {
        position: relative;
        bottom: 5px;
        right: 5px;
    }
#wp-content {
    margin-left: 250px;
    flex: 1;
    padding: 10px;
    margin-top: 60px;
}
</style>