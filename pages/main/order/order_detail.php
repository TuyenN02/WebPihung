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

<div class="container mt-60 min-height-100">  
    <table class="table-bordered w-100 bg-white" cellpadding="5px">
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
            <td><?= htmlspecialchars($row_detail['SoLuong']) ?> Cây</td>
            <td><?= number_format($row_detail['GiaMua'], 0, ',', '.') ?> VND/Cây</td>
        </tr>
        <?php }?>
        <tr>
            <th colspan="4">Tổng tiền: <?= number_format($row['GiaTien'], 0, ',', '.') ?> VND</th>
        </tr>
    </table>

    <?php
    // Xử lý trạng thái đơn hàng
    if ($row['XuLy'] == 0 || $row['XuLy'] == 1 || $row['XuLy'] == 3) {
    ?>
     <div class="text-center mt-60">
         <a class="btn btn-danger" href="pages/main/order/cancel.php?id_cancel=<?= $id_order ?>">Hủy đơn hàng</a>
     </div>
     <?php
    } elseif ($row['XuLy'] == 4) {
    ?>
     <div class="text-center mt-60">
         <a class="btn btn-warning" href="pages/main/order/return_request.php?id_request=<?= $id_order ?>">Yêu cầu hoàn trả</a>
         <a class="btn btn-success" href="pages/main/order/confirm_received.php?id_confirm=<?= $id_order ?>">Đã nhận được hàng</a>
     </div>
     <?php
    }
    ?>

    <?php
    // Xử lý yêu cầu hoàn trả
    if (isset($_GET['return']) && $_GET['return'] == 'true') {
        ?>
        <div class="mt-3">
            <h3 class="text-center">Yêu cầu hoàn trả</h3>
            <form action="pages/main/order/process_return_request.php" method="POST">
                <input type="hidden" name="id_order" value="<?= $id_order ?>">
                <div class="form-group">
                    <label for="note">Ghi chú:</label>
                    <textarea id="note" name="note" class="form-control" rows="4" required></textarea>
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Xác nhận yêu cầu</button>
                </div>
            </form>
        </div>
        <?php
    }
    ?>
</div>
